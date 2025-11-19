<?php
// app/Console/Commands/WarmTaxCache.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MonthlyTaxReport;
use Illuminate\Support\Facades\Log;

class WarmTaxCache extends Command
{
    protected $signature = 'tax:warm-cache';
    protected $description = 'Warm up tax report cache for better performance';

    public function handle()
    {
        Log::info('=== TAX CACHE WARMING START ===');
        
        $reports = MonthlyTaxReport::with('location')->get();
        $updatedCount = 0;

        foreach ($reports as $report) {
            // Access attributes untuk trigger cache update
            $revenue = $report->total_revenue;
            $tax = $report->tax_amount;
            
            $updatedCount++;
        }

        Log::info("Tax cache warmed: {$updatedCount} reports updated");
        $this->info("✅ Tax cache warmed: {$updatedCount} reports");
        
        Log::info('=== TAX CACHE WARMING END ===');
        return Command::SUCCESS;
    }
}