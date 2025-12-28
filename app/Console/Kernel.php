<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\GenerateMonthlyTaxReports::class,
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

    $schedule->command('rooms:update-status')->everyMinute();
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
