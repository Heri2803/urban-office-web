<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\GenerateMonthlyTaxReport::class,
        \App\Console\Commands\UpdateExpiredPromosCommand::class,
    ];
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Warm cache setiap jam
        $schedule->command('tax:warm-cache')->hourly();

        // Generate reports baru setiap bulan
        $schedule->command('tax:generate-monthly')->monthlyOn(1, '02:00');

        // Update status rooms setiap menit
        $schedule->command('rooms:update-status')->everyMinute();

        // ── PROMO STATUS AUTO-UPDATE ──────────────────────────────────────────
        // Jalankan setiap jam untuk update promo expired/upcoming/reactivate
        // Pastikan cron server: * * * * * php artisan schedule:run >> /dev/null 2>&1
        $schedule->command('promos:update-expired')
                 ->hourly()
                 ->appendOutputTo(storage_path('logs/promo-scheduler.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
