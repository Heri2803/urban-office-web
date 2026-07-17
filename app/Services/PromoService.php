<?php

namespace App\Services;

use App\Models\Promo;
use App\Models\PromoUsage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PromoService
{
    /**
     * DTO-style result untuk validasi promo.
     * Menggunakan array sederhana (no DTO class) agar kompatibel dengan PHP 7+.
     *
     * Struktur return:
     *  [
     *    'valid'           => bool,
     *    'message'         => string,
     *    'discount_amount' => float,   // 0 jika tidak valid
     *    'promo'           => ?Promo,
     *  ]
     */

    /**
     * Validasi promo sebelum transaksi dibuat.
     * Termasuk pengecekan usage_per_user (harus dikirim user_id jika user sudah login).
     *
     * @param  string      $promoCode
     * @param  int|null    $userId        null jika guest (pengecekkan usage_per_user di-skip)
     * @param  float       $subtotal
     * @param  string      $locationId    ID localsi (sebagai string, sesuai kolom locations JSON)
     * @param  string      $roomType      Nama room type (misal: "Meeting Room")
     * @return array
     */
    public function validate(
        string $promoCode,
        ?int   $userId,
        float  $subtotal,
        string $locationId,
        string $roomType
    ): array {
        $promo = Promo::where('code', $promoCode)->first();

        if (!$promo) {
            return $this->fail('Kode promo tidak ditemukan.');
        }

        // Cek status & approval
        if (!$promo->is_approved) {
            return $this->fail('Kode promo belum disetujui.');
        }

        if (in_array($promo->status, ['draft', 'inactive', 'ended'])) {
            return $this->fail('Kode promo tidak aktif.');
        }

        $now = Carbon::now();

        // Cek tanggal — bandingkan ke awal/akhir hari, bukan jam mentah, supaya promo yang
        // start_date/end_date-nya "hari ini" tetap valid sepanjang hari itu.
        if ($now->lt($promo->start_date->copy()->startOfDay())) {
            $startStr = $promo->start_date->format('d M Y');
            return $this->fail("Kode promo belum bisa digunakan. Mulai berlaku: {$startStr}.");
        }

        if ($now->gt($promo->end_date->copy()->endOfDay())) {
            return $this->fail('Kode promo sudah expired.');
        }

        // Cek apakah promo butuh claim (Discount & Voucher)
        if (in_array($promo->promo_type_id, [2, 3])) {
            if ($userId === null) {
                return $this->fail('Anda harus login untuk menggunakan promo ini.');
            }
            
            // Check in promo_user - harus benar-benar sudah diklaim user (bukan sekadar
            // ditarget/air-drop admin), dan belum pernah dipakai.
            $isClaimed = DB::table('promo_user')
                ->where('promo_id', $promo->id)
                ->where('user_id', $userId)
                ->where('is_claimed', true)
                ->where('is_used', false)
                ->exists();
                
            if (!$isClaimed) {
                return $this->fail('Anda harus klaim promo ini terlebih dahulu sebelum menggunakannya.');
            }
        }

        // Cek usage_limit global
        if ($promo->usage_limit !== null && $promo->usage_limit > 0 && $promo->usage_count >= $promo->usage_limit) {
            return $this->fail('Kuota penggunaan promo sudah habis.');
        }

        // Cek usage_per_user (hanya jika user login)
        if ($userId !== null && $promo->usage_per_user !== null) {
            $userUsageCount = PromoUsage::where('promo_id', $promo->id)
                                        ->where('user_id', $userId)
                                        ->count();

            if ($userUsageCount >= $promo->usage_per_user) {
                return $this->fail('Anda sudah menggunakan promo ini sebanyak ' . $promo->usage_per_user . 'x (limit per user).');
            }
        }

        // Cek lokasi
        $locations = $promo->locations ?? [];
        if (!empty($locations) && !in_array($locationId, $locations) && !in_array('all', $locations)) {
            return $this->fail('Promo tidak berlaku untuk lokasi ini.');
        }

        // Cek tipe layanan
        $serviceTypes = $promo->service_types ?? [];
        $roomTypeSlug = Str::slug($roomType);
        if (!empty($serviceTypes) && !in_array($roomTypeSlug, $serviceTypes) && !in_array('all-services', $serviceTypes)) {
            return $this->fail('Promo tidak berlaku untuk jenis layanan ini.');
        }

        // Cek minimum transaksi
        if ($subtotal < (float) $promo->min_transaction) {
            $minFormatted = 'Rp ' . number_format((float) $promo->min_transaction, 0, ',', '.');
            return $this->fail("Minimal transaksi untuk promo ini adalah {$minFormatted}.");
        }

        // Hitung diskon
        $discountAmount = $this->calculateDiscount($promo, $subtotal);

        return [
            'valid'           => true,
            'message'         => 'Promo berhasil digunakan!',
            'discount_amount' => $discountAmount,
            'promo'           => $promo,
        ];
    }

    /**
     * Hitung jumlah diskon berdasarkan promo dan subtotal.
     * Bisa dipakai standalone (tidak wajib lewat validate() dulu).
     *
     * @param  Promo $promo
     * @param  float $subtotal
     * @return float
     */
    public function calculateDiscount(Promo $promo, float $subtotal): float
    {
        if ($promo->discount_type === 'percentage') {
            $discount = ($promo->discount_amount / 100) * $subtotal;
        } else {
            // fixed amount
            $discount = (float) $promo->discount_amount;
        }

        // Diskon tidak boleh melebihi subtotal
        return min($discount, $subtotal);
    }

    /**
     * Catat pemakaian promo setelah payment settlement.
     * Menggunakan DB::transaction() + lockForUpdate() untuk mencegah race condition.
     *
     * @param  Promo  $promo
     * @param  int    $userId
     * @param  int    $transactionId
     * @param  string $location       Kode/ID lokasi tempat transaksi
     * @param  float  $discountAmount Nominal diskon yang diberikan
     * @param  float  $transactionAmount Total transaksi sebelum diskon (gross_amount)
     * @param  array  $metadata       Data tambahan (opsional)
     * @return void
     * @throws \Exception jika usage_per_user sudah terlampaui (race condition)
     */
    public function recordUsage(
        Promo  $promo,
        int    $userId,
        int    $transactionId,
        string $location,
        float  $discountAmount,
        float  $transactionAmount,
        array  $metadata = []
    ): void {
        DB::transaction(function () use (
            $promo, $userId, $transactionId, $location,
            $discountAmount, $transactionAmount, $metadata
        ) {
            // Lock baris promo agar tidak ada concurrent update
            $lockedPromo = Promo::lockForUpdate()->find($promo->id);

            if (!$lockedPromo) {
                Log::error('[PromoService::recordUsage] Promo not found', ['promo_id' => $promo->id]);
                throw new \Exception('Promo tidak ditemukan saat akan dicatat.');
            }

            // Double-check usage_limit dengan data terbaru (setelah lock)
            if ($lockedPromo->usage_limit !== null && $lockedPromo->usage_limit > 0 && $lockedPromo->usage_count >= $lockedPromo->usage_limit) {
                Log::warning('[PromoService::recordUsage] Usage limit reached (race condition prevented)', [
                    'promo_id'    => $lockedPromo->id,
                    'usage_count' => $lockedPromo->usage_count,
                    'usage_limit' => $lockedPromo->usage_limit,
                ]);
                throw new \Exception('Kuota penggunaan promo sudah habis (race condition ditangani).');
            }

            // Double-check usage_per_user dengan data terbaru
            if ($lockedPromo->usage_per_user !== null) {
                $userUsageCount = PromoUsage::where('promo_id', $lockedPromo->id)
                                            ->where('user_id', $userId)
                                            ->count();

                if ($userUsageCount >= $lockedPromo->usage_per_user) {
                    Log::warning('[PromoService::recordUsage] usage_per_user exceeded (race condition prevented)', [
                        'promo_id'       => $lockedPromo->id,
                        'user_id'        => $userId,
                        'user_count'     => $userUsageCount,
                        'usage_per_user' => $lockedPromo->usage_per_user,
                    ]);
                    throw new \Exception('Batas penggunaan promo per user sudah tercapai.');
                }
            }

            // Cek apakah usage untuk transaksi ini sudah ada (idempotency)
            $exists = PromoUsage::where('promo_id', $lockedPromo->id)
                                ->where('transaction_id', $transactionId)
                                ->exists();

            if ($exists) {
                Log::info('[PromoService::recordUsage] Usage already recorded, skipping', [
                    'promo_id'       => $lockedPromo->id,
                    'transaction_id' => $transactionId,
                ]);
                return; // idempotent — tidak error, cukup skip
            }

            // Insert ke promo_usages
            PromoUsage::create([
                'promo_id'           => $lockedPromo->id,
                'user_id'            => $userId,
                'transaction_id'     => $transactionId,
                'location'           => $location,
                'discount_amount'    => $discountAmount,
                'transaction_amount' => $transactionAmount,
                'metadata'           => !empty($metadata) ? $metadata : null,
            ]);

            // Tandai sudah digunakan di promo_user jika ini promo yang butuh claim
            if (in_array($lockedPromo->promo_type_id, [2, 3])) {
                DB::table('promo_user')
                    ->where('promo_id', $lockedPromo->id)
                    ->where('user_id', $userId)
                    ->where('is_used', false)
                    ->update([
                        'is_used' => true,
                        'used_at' => Carbon::now(),
                    ]);
            }

            // Increment usage_count di tabel promos
            $lockedPromo->increment('usage_count');

            // Setelah increment, refresh untuk mendapat nilai terbaru
            $lockedPromo->refresh();

            // Jika usage_count sudah mencapai usage_limit, set status ke 'inactive'
            if (
                $lockedPromo->usage_limit !== null &&
                $lockedPromo->usage_limit > 0 &&
                $lockedPromo->usage_count >= $lockedPromo->usage_limit
            ) {
                $lockedPromo->update(['status' => 'inactive']);

                Log::info('[PromoService::recordUsage] Promo deactivated — usage_limit reached', [
                    'promo_id'   => $lockedPromo->id,
                    'promo_code' => $lockedPromo->code,
                    'usage'      => $lockedPromo->usage_count . '/' . $lockedPromo->usage_limit,
                ]);
            }

            Log::info('[PromoService::recordUsage] Usage recorded successfully', [
                'promo_id'       => $lockedPromo->id,
                'promo_code'     => $lockedPromo->code,
                'user_id'        => $userId,
                'transaction_id' => $transactionId,
                'discount_amount'=> $discountAmount,
                'usage_count'    => $lockedPromo->usage_count,
            ]);
        });
    }

    /**
     * Helper: bangun array response gagal validasi.
     */
    private function fail(string $message): array
    {
        return [
            'valid'           => false,
            'message'         => $message,
            'discount_amount' => 0.0,
            'promo'           => null,
        ];
    }
}
