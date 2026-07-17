<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Services\BrowserNotificationService;
use Illuminate\Http\Request;

class BrowserNotificationController extends Controller
{
    protected $browserNotificationService;

    public function __construct(BrowserNotificationService $browserNotificationService)
    {
        $this->middleware('auth');
        $this->browserNotificationService = $browserNotificationService;
    }

    /**
     * Get pending browser notifications
     * Dipanggil oleh JavaScript secara periodik atau saat halaman load
     */
    public function getPendingNotifications(Request $request)
    {
        $userId = auth()->id();
        
        $notifications = cache()->get("pending_notifications:{$userId}", []);
        
        // Jika ada notifikasi, ambil dan hapus dari cache
        if (!empty($notifications)) {
            cache()->forget("pending_notifications:{$userId}");
        }

        return response()->json([
            'success'               => true,
            'notifications'         => $notifications,
            'count'                 => count($notifications),
            'unread_database_count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Get unread count dari database notifications
     */
    public function unreadCount(Request $request)
    {
        return response()->json([
            'success' => true,
            'count'   => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark satu notifikasi sebagai read
     */
    public function markAsRead(Request $request, $notificationId)
    {
        $notification = auth()->user()->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true,
            'unread_count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark semua notifikasi sebagai read
     */
    public function markAllAsRead(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi telah ditandai sebagai dibaca.',
        ]);
    }
}