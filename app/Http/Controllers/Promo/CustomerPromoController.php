<?php

namespace App\Http\Controllers\Promo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promo;
use Illuminate\Support\Facades\Auth;

class CustomerPromoController extends Controller
{
    /**
     * Tampilkan halaman Deals & Promos (Publik)
     * Menampilkan tipe Banner (1), Discount (2), dan Voucher (3)
     */
    public function deals()
    {
        $userId = Auth::id();

        // Promo yang di-target admin ke customer tertentu (is_targeted) TIDAK PERNAH
        // tampil di halaman /deals, baik sebelum maupun sesudah diklaim — tempatnya
        // langsung di /my-vouchers. Halaman /deals hanya untuk promo publik.
        $promos = Promo::with(['category', 'type'])
            ->whereIn('promo_type_id', [1, 2, 3]) // Banner, Discount & Voucher
            ->where('status', 'active')
            ->where('is_targeted', false)
            ->when($userId, function ($query) use ($userId) {
                // Discount/Voucher publik yang sudah diklaim user ini pindah ke My Vouchers,
                // jadi tidak perlu tampil lagi di /deals. Banner tidak pernah diklaim jadi tidak kena filter ini.
                $query->whereNotIn('id', function ($sub) use ($userId) {
                    $sub->select('promo_id')
                        ->from('promo_user')
                        ->where('user_id', $userId)
                        ->where('is_claimed', true);
                });
            })
            ->orderBy('priority', 'asc')
            ->get();

        // Status di DB baru sinkron via cron per jam, jadi hitung ulang di sini juga
        // supaya promo yang sudah lewat end_date / kuota habis langsung tampil pudar
        // walau kolom status belum sempat ke-update oleh scheduler.
        $now = now();
        $promos->each(function ($promo) use ($now) {
            // Promo berlaku sepanjang hari end_date, jadi bandingkan ke akhir hari (endOfDay),
            // bukan jam 00:00 mentah — kalau tidak, promo yang end_date-nya "hari ini" akan
            // langsung dianggap expired sejak lewat tengah malam.
            $promo->is_expired = (bool) ($promo->end_date && $now->gt($promo->end_date->copy()->endOfDay()));
            $promo->is_quota_full = $promo->usage_limit !== null && $promo->usage_limit > 0 && $promo->usage_count >= $promo->usage_limit;
            $promo->can_claim = $promo->canBeUsed();
        });

        return view('layouts.dashboard.deals', compact('promos'));
    }

    /**
     * Tampilkan halaman My Vouchers (Privat - perlu login)
     * Menampilkan promo_user milik user (Voucher Air-Drop dan Claimed Deals)
     */
    public function myVouchers()
    {
        $user = Auth::user();

        // Tampilkan: (a) promo yang sudah benar-benar diklaim user, ATAU
        // (b) promo yang di-target/air-drop admin ke user ini meski belum diklaim
        //     (supaya user bisa klaim langsung dari halaman ini).
        $vouchers = $user->promos()->with('type')
                         ->where(function ($query) {
                             $query->where('promo_user.is_claimed', true)
                                   ->orWhere('promos.is_targeted', true);
                         })
                         ->orderByPivot('created_at', 'desc')
                         ->get();

        return view('layouts.dashboard.my-vouchers', compact('vouchers'));
    }

    /**
     * Klaim promo publik (Discount & Voucher) ke akun pengguna (dompet promo_user)
     */
    public function claim(Request $request)
    {
        $request->validate([
            'promo_id' => 'required|exists:promos,id'
        ]);

        $user = Auth::user();
        $promo = Promo::findOrFail($request->promo_id);

        // Discount (2) dan Voucher (3) bisa diklaim mandiri oleh customer.
        // Banner (1) hanya informasi, tidak diklaim.
        if (!in_array($promo->promo_type_id, [2, 3])) {
            return response()->json([
                'success' => false,
                'message' => 'Promo ini tidak dapat diklaim.'
            ], 403);
        }

        if ($promo->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Promo sudah tidak aktif.'
            ], 400);
        }

        $now = now();

        // Bandingkan ke awal/akhir hari (bukan jam mentah) supaya promo yang start_date atau
        // end_date-nya "hari ini" tetap valid sepanjang hari itu, konsisten dengan
        // BannerController::apiIndex() yang jadi acuan status promo di admin.
        if ($promo->start_date && $now->lt($promo->start_date->copy()->startOfDay())) {
            return response()->json([
                'success' => false,
                'message' => 'Promo belum bisa diklaim, belum mulai berlaku.'
            ], 400);
        }

        if ($promo->end_date && $now->gt($promo->end_date->copy()->endOfDay())) {
            return response()->json([
                'success' => false,
                'message' => 'Promo sudah kedaluwarsa dan tidak dapat diklaim lagi.'
            ], 400);
        }

        if ($promo->usage_limit !== null && $promo->usage_limit > 0 && $promo->usage_count >= $promo->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Kuota promo sudah habis.'
            ], 400);
        }

        if ($promo->usage_per_user !== null) {
            $userUsageCount = $promo->usages()->where('user_id', $user->id)->count();
            if ($userUsageCount >= $promo->usage_per_user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah mencapai batas penggunaan promo ini.'
                ], 400);
            }
        }

        $existingPivot = $user->promos()->where('promo_id', $promo->id)->first();

        // Cek apakah sudah pernah klaim
        if ($existingPivot && $existingPivot->pivot->is_claimed) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mengklaim promo ini.'
            ], 400);
        }

        if ($existingPivot) {
            // Baris sudah ada karena user memang di-target/air-drop admin, tinggal tandai claimed.
            $user->promos()->updateExistingPivot($promo->id, [
                'is_claimed' => true,
                'claimed_at' => now(),
            ]);
        } else {
            // Belum ada baris sama sekali. Voucher yang di-target admin (is_targeted)
            // tidak boleh diklaim mandiri oleh user yang bukan bagian dari target list.
            if ($promo->is_targeted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Promo ini tidak tersedia untuk Anda.'
                ], 403);
            }

            $user->promos()->attach($promo->id, [
                'is_claimed' => true,
                'claimed_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Promo berhasil diklaim dan masuk ke My Vouchers Anda!'
        ]);
    }
}
