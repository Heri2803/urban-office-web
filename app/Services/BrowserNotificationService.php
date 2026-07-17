<?php

namespace App\Services;

use App\Models\Surat;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class BrowserNotificationService
{
    /**
     * Kirim notifikasi browser ke user
     */
    public function sendToUser(User $user, Surat $surat)
    {
        // Data untuk notification payload
        $notificationData = [
            'title' => '📬 Surat Masuk Baru',
            'body' => "{$surat->perihal}\nDari: {$surat->pengirim}\nNo: {$surat->nomor_surat}",
            'icon' => asset('/assets/LOGO_URBAN_OFFICE.png'),
            'badge' => asset('/assets/LOGO_URBAN_OFFICE.png'),
            'url'       => route('dashboard.surats.show', $surat->id),
            'tag'       => "surat-{$surat->id}",
            'surat_id'  => $surat->id,
            'timestamp' => now()->timestamp,
            'actions'   => [
                [
                    'action' => 'view',
                    'title'  => 'Lihat Surat'
                ],
                [
                    'action' => 'close', 
                    'title'  => 'Tutup'
                ]
            ]
        ];

        // Simpan ke database Laravel notification
        $databaseNotification = $user->notifications()->create([
            'id'   => \Illuminate\Support\Str::uuid(),
            'type' => \App\Notifications\SuratMasukNotification::class,
            'data' => array_merge($notificationData, [
                'message'    => "Anda menerima surat baru: {$surat->perihal}",
                'action_url' => route('dashboard.surats.show', $surat->id),
            ]),
            'created_at' => now(),
        ]);

        // Cache notification data untuk service worker
        $this->cacheNotificationForBrowser($user->id, $notificationData);

        Log::info("Browser notification queued for user {$user->id}", [
            'surat_id' => $surat->id,
            'notification_id' => $databaseNotification->id
        ]);

        return $databaseNotification;
    }

    /**
     * Kirim notifikasi status pengambilan surat
     */
    public function sendPickupNotification(User $user, Surat $surat)
    {
        $methodText = $surat->metode_pengambilan === 'offline' ? 'diambil secara offline di Urban Office' : 'dikirim via ' . $surat->kurir_pengiriman;
        $body = "Surat: {$surat->perihal} telah {$methodText} pada " . ($surat->tanggal_diambil ? $surat->tanggal_diambil->format('d/m/Y') : now()->format('d/m/Y'));
        
        $notificationData = [
            'title' => '📦 Surat Telah Diambil/Dikirim',
            'body' => $body,
            'icon' => asset('/assets/LOGO_URBAN_OFFICE.png'),
            'badge' => asset('/assets/LOGO_URBAN_OFFICE.png'),
            'url'       => route('dashboard.surats.show', $surat->id),
            'tag'       => "surat-pickup-{$surat->id}",
            'surat_id'  => $surat->id,
            'timestamp' => now()->timestamp,
            'actions'   => [
                [
                    'action' => 'view',
                    'title'  => 'Lihat Detail'
                ],
                [
                    'action' => 'close', 
                    'title'  => 'Tutup'
                ]
            ]
        ];

        // Simpan ke database Laravel notification
        $databaseNotification = $user->notifications()->create([
            'id'   => \Illuminate\Support\Str::uuid(),
            'type' => \App\Notifications\SuratMasukNotification::class,
            'data' => array_merge($notificationData, [
                'message'    => $body,
                'action_url' => route('dashboard.surats.show', $surat->id),
            ]),
            'created_at' => now(),
        ]);

        // Cache untuk polling browser
        $this->cacheNotificationForBrowser($user->id, $notificationData);

        return $databaseNotification;
    }

    /**
     * Kirim notifikasi status pengambilan ke multiple users
     */
    public function sendPickupNotificationToMultipleUsers($userIds, Surat $surat)
    {
        $users = User::whereIn('id', $userIds)->get();
        $results = [];

        foreach ($users as $user) {
            try {
                $results[$user->id] = $this->sendPickupNotification($user, $surat);
            } catch (\Exception $e) {
                Log::error("Failed to send pickup notification to user {$user->id}", [
                    'error' => $e->getMessage()
                ]);
                $results[$user->id] = false;
            }
        }

        return $results;
    }

    /**
     * Kirim notifikasi ke multiple users
     */
    public function sendToMultipleUsers($userIds, Surat $surat)
    {
        $users = User::whereIn('id', $userIds)->get();
        $results = [];

        foreach ($users as $user) {
            try {
                $results[$user->id] = $this->sendToUser($user, $surat);
            } catch (\Exception $e) {
                Log::error("Failed to send notification to user {$user->id}", [
                    'error' => $e->getMessage()
                ]);
                $results[$user->id] = false;
            }
        }

        return $results;
    }

    /**
     * Cache notifikasi untuk di-polling oleh browser
     */
    private function cacheNotificationForBrowser($userId, $data)
    {
        $cacheKey = "pending_notifications:{$userId}";
        
        // Ambil existing pending notifications
        $pending = cache()->get($cacheKey, []);
        
        // Tambah notifikasi baru (max 10 pending)
        $pending[] = $data;
        if (count($pending) > 10) {
            array_shift($pending);
        }
        
        // Cache selama 1 jam
        cache()->put($cacheKey, $pending, now()->addHour());
    }

    /**
     * Ambil pending notifications untuk user
     */
    public function getPendingNotifications($userId)
    {
        $cacheKey = "pending_notifications:{$userId}";
        $notifications = cache()->pull($cacheKey, []); // pull = ambil + hapus
        
        return $notifications;
    }
}