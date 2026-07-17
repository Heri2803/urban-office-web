<?php

namespace App\Console\Commands;

use App\Models\Promo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateExpiredPromosCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'promos:update-expired
                            {--dry-run : Tampilkan perubahan tanpa mengeksekusi ke database}';

    /**
     * The console command description.
     */
    protected $description = 'Update status promo: expired (end_date), upcoming, reaktivasi, dan quota habis (usage_limit)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $now      = Carbon::now();

        $this->info('=== Promo Status Updater ===');
        $this->info('Waktu eksekusi: ' . $now->toDateTimeString());

        if ($isDryRun) {
            $this->warn('[DRY RUN MODE] Tidak ada perubahan yang akan disimpan ke database.');
        }

        // ── 1. Update promo yang sudah EXPIRED ─────────────────────────────────────
        // Cari semua promo dengan end_date < sekarang yang statusnya masih 'active'
        // atau 'upcoming' (bukan draft/inactive yang memang sengaja dimatikan admin)
        $expiredQuery = Promo::where('end_date', '<', $now)
                             ->whereNotIn('status', ['draft', 'inactive', 'ended']);

        $expiredCount = $expiredQuery->count();
        $expiredIds   = $expiredQuery->pluck('id')->toArray();

        $this->info("Promo expired ditemukan: {$expiredCount}");

        if ($expiredCount > 0) {
            if (!$isDryRun) {
                DB::beginTransaction();
                try {
                    Promo::whereIn('id', $expiredIds)->update([
                        'status'     => 'ended',
                        'updated_at' => $now,
                    ]);
                    DB::commit();

                    Log::info('[UpdateExpiredPromos] Status → ended', [
                        'count'    => $expiredCount,
                        'ids'      => $expiredIds,
                        'executed' => $now->toDateTimeString(),
                    ]);

                    $this->info("✅ {$expiredCount} promo diupdate ke status 'ended'");
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('[UpdateExpiredPromos] Gagal update ended', [
                        'error' => $e->getMessage(),
                    ]);
                    $this->error('❌ Gagal update promo expired: ' . $e->getMessage());
                    return self::FAILURE;
                }
            } else {
                $this->table(
                    ['ID', 'Nama', 'Status Lama', 'End Date', 'Status Baru'],
                    Promo::whereIn('id', $expiredIds)
                         ->get(['id', 'name', 'status', 'end_date'])
                         ->map(fn($p) => [
                             $p->id,
                             $p->name,
                             $p->status,
                             $p->end_date?->toDateString(),
                             'ended',
                         ])
                );
            }
        }

        // ── 2. Update promo yang belum dimulai (UPCOMING) ───────────────────────────
        // Cari promo dengan start_date > sekarang yang statusnya masih 'active'
        // (mungkin admin sudah set active tapi start_date belum tercapai)
        $upcomingQuery = Promo::where('start_date', '>', $now)
                              ->where('status', 'active');

        $upcomingCount = $upcomingQuery->count();
        $upcomingIds   = $upcomingQuery->pluck('id')->toArray();

        $this->info("Promo upcoming ditemukan: {$upcomingCount}");

        if ($upcomingCount > 0) {
            if (!$isDryRun) {
                DB::beginTransaction();
                try {
                    Promo::whereIn('id', $upcomingIds)->update([
                        'status'     => 'upcoming',
                        'updated_at' => $now,
                    ]);
                    DB::commit();

                    Log::info('[UpdateExpiredPromos] Status → upcoming', [
                        'count'    => $upcomingCount,
                        'ids'      => $upcomingIds,
                        'executed' => $now->toDateTimeString(),
                    ]);

                    $this->info("✅ {$upcomingCount} promo diupdate ke status 'upcoming'");
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('[UpdateExpiredPromos] Gagal update upcoming', [
                        'error' => $e->getMessage(),
                    ]);
                    $this->error('❌ Gagal update promo upcoming: ' . $e->getMessage());
                    return self::FAILURE;
                }
            } else {
                $this->table(
                    ['ID', 'Nama', 'Status Lama', 'Start Date', 'Status Baru'],
                    Promo::whereIn('id', $upcomingIds)
                         ->get(['id', 'name', 'status', 'start_date'])
                         ->map(fn($p) => [
                             $p->id,
                             $p->name,
                             $p->status,
                             $p->start_date?->toDateString(),
                             'upcoming',
                         ])
                );
            }
        }

        // ── 3. Reaktivasi promo upcoming yang sudah saatnya aktif ───────────────────
        // Jika ada promo dengan status 'upcoming' tapi start_date sudah lewat
        $reactivateQuery = Promo::where('status', 'upcoming')
                                ->where('start_date', '<=', $now)
                                ->where('end_date', '>=', $now);

        $reactivateCount = $reactivateQuery->count();
        $reactivateIds   = $reactivateQuery->pluck('id')->toArray();

        $this->info("Promo siap diaktifkan kembali: {$reactivateCount}");

        if ($reactivateCount > 0) {
            if (!$isDryRun) {
                DB::beginTransaction();
                try {
                    Promo::whereIn('id', $reactivateIds)->update([
                        'status'     => 'active',
                        'updated_at' => $now,
                    ]);
                    DB::commit();

                    Log::info('[UpdateExpiredPromos] Status → active (reaktivasi)', [
                        'count'    => $reactivateCount,
                        'ids'      => $reactivateIds,
                        'executed' => $now->toDateTimeString(),
                    ]);

                    $this->info("✅ {$reactivateCount} promo diaktifkan kembali ke status 'active'");
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('[UpdateExpiredPromos] Gagal reaktivasi', [
                        'error' => $e->getMessage(),
                    ]);
                    $this->error('❌ Gagal reaktivasi promo: ' . $e->getMessage());
                    return self::FAILURE;
                }
            }
        }

        // ── 4. Nonaktifkan promo yang QUOTA-NYA HABIS ──────────────────────────────────
        // Cari promo dengan usage_limit terisi DAN usage_count >= usage_limit
        // yang statusnya masih 'active' atau 'upcoming'
        // Catatan: promo tanpa usage_limit (NULL) tidak dibatasi → skip
        $quotaFullQuery = Promo::whereNotNull('usage_limit')
                               ->whereColumn('usage_count', '>=', 'usage_limit')
                               ->whereIn('status', ['active', 'upcoming']);

        $quotaFullCount = $quotaFullQuery->count();
        $quotaFullIds   = $quotaFullQuery->pluck('id')->toArray();

        $this->info("Promo quota habis (usage_limit tercapai): {$quotaFullCount}");

        if ($quotaFullCount > 0) {
            if (!$isDryRun) {
                DB::beginTransaction();
                try {
                    Promo::whereIn('id', $quotaFullIds)->update([
                        'status'     => 'inactive',
                        'updated_at' => $now,
                    ]);
                    DB::commit();

                    Log::info('[UpdateExpiredPromos] Status → inactive (quota habis)', [
                        'count'    => $quotaFullCount,
                        'ids'      => $quotaFullIds,
                        'executed' => $now->toDateTimeString(),
                    ]);

                    $this->info("✅ {$quotaFullCount} promo dinonaktifkan (quota habis)");
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('[UpdateExpiredPromos] Gagal update quota habis', [
                        'error' => $e->getMessage(),
                    ]);
                    $this->error('❌ Gagal nonaktifkan promo quota habis: ' . $e->getMessage());
                    return self::FAILURE;
                }
            } else {
                $this->table(
                    ['ID', 'Nama', 'Status Lama', 'Usage', 'Limit', 'Status Baru'],
                    Promo::whereIn('id', $quotaFullIds)
                         ->get(['id', 'name', 'status', 'usage_count', 'usage_limit'])
                         ->map(fn($p) => [
                             $p->id,
                             $p->name,
                             $p->status,
                             $p->usage_count,
                             $p->usage_limit,
                             'inactive',
                         ])
                );
            }
        }

        $totalChanged = $expiredCount + $upcomingCount + $reactivateCount + $quotaFullCount;
        $this->newLine();
        $this->info("=== Selesai. Total perubahan: {$totalChanged} promo ===");

        Log::info('[UpdateExpiredPromos] Selesai', [
            'expired_updated'    => $expiredCount,
            'upcoming_updated'   => $upcomingCount,
            'reactivated'        => $reactivateCount,
            'quota_deactivated'  => $quotaFullCount,
            'is_dry_run'         => $isDryRun,
        ]);

        return self::SUCCESS;
    }
}
