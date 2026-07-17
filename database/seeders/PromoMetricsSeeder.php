<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promo;
use App\Models\PromoMetric;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PromoMetricsSeeder extends Seeder
{
    public function run()
    {
        try {
            // Clear existing metrics data untuk avoid duplicates.
            // Catatan: sengaja TIDAK menyentuh tabel promo_usages di sini —
            // itu tabel transaksi real (dicatat oleh PromoService::recordUsage),
            // bukan tempat data dummy. Truncate di sini pernah menghapus data
            // usage asli dan membuatnya permanen desync dari promos.usage_count.
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            PromoMetric::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $promos = Promo::whereIn('status', ['ended', 'active'])->get();
            
            if ($promos->isEmpty()) {
                echo "No promos found with status ended or active. Please run PromoSeeder first.\n";
                return;
            }

            echo "Generating metrics for " . $promos->count() . " promos...\n";

            foreach ($promos as $index => $promo) {
                echo "Processing promo " . ($index + 1) . ": " . $promo->name . "\n";
                
                // PERBAIKAN: Handle locations yang sudah array
                $locations = $this->getLocations($promo);
                $startDate = Carbon::parse($promo->start_date);
                $endDate = Carbon::parse($promo->end_date);
                
                foreach ($locations as $location) {
                    $this->generateBeforePeriodData($promo, $location, $startDate);
                    $this->generateDuringPeriodData($promo, $location, $startDate, $endDate);
                    
                    if ($promo->status === 'ended') {
                        $this->generateAfterPeriodData($promo, $location, $endDate);
                    }
                }
            }

            echo "Promo metrics generated successfully!\n";
            echo "Total metrics: " . PromoMetric::count() . "\n";

        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
        }
    }

    /**
     * Handle locations field yang bisa berupa array atau JSON string
     */
    private function getLocations($promo)
    {
        $locations = $promo->locations;
        
        // Jika locations adalah string JSON, decode
        if (is_string($locations)) {
            $decoded = json_decode($locations, true);
            return is_array($decoded) ? $decoded : ['jakarta-pusat'];
        }
        
        // Jika locations sudah array, langsung return
        if (is_array($locations)) {
            return !empty($locations) ? $locations : ['jakarta-pusat'];
        }
        
        // Default fallback
        return ['jakarta-pusat'];
    }

    private function generateBeforePeriodData($promo, $location, $startDate)
    {
        $beforeStart = $startDate->copy()->subDays(30);
        $currentDate = $beforeStart;
        
        while ($currentDate->lt($startDate)) {
            $this->createMetricData($promo, $location, $currentDate, 'before');
            $currentDate->addDay();
        }
    }

    private function generateDuringPeriodData($promo, $location, $startDate, $endDate)
    {
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $this->createMetricData($promo, $location, $currentDate, 'during');
            $currentDate->addDay();
        }
    }

    private function generateAfterPeriodData($promo, $location, $endDate)
    {
        $afterEnd = $endDate->copy()->addDays(30);
        $currentDate = $endDate->copy()->addDay();
        
        while ($currentDate->lte($afterEnd)) {
            $this->createMetricData($promo, $location, $currentDate, 'after');
            $currentDate->addDay();
        }
    }

    private function createMetricData($promo, $location, $date, $period)
    {
        // Base values berdasarkan period
        $baseValues = $this->getBaseValuesByPeriod($period);
        
        // Random variation
        $variation = rand(-20, 20) / 100; // -20% to +20% variation
        
        $views = $baseValues['views'] * (1 + $variation);
        $clicks = $baseValues['clicks'] * (1 + $variation);
        $transactions = $baseValues['transactions'] * (1 + $variation);
        $revenue = $baseValues['revenue'] * (1 + $variation);
        
        // Untuk during period, tambahkan boost untuk promo
        if ($period === 'during') {
            $promoBoost = rand(10, 60) / 100; // 10-60% boost karena promo
            $transactions = $transactions * (1 + $promoBoost);
            $revenue = $revenue * (1 + $promoBoost * 0.8); // Revenue boost sedikit lebih rendah
        }

        try {
            PromoMetric::create([
                'promo_id' => $promo->id,
                'location' => $location,
                'metric_date' => $date->format('Y-m-d'),
                
                // Engagement Metrics
                'views' => max(10, round($views)),
                'clicks' => max(5, round($clicks)),
                'unique_visitors' => max(5, round($views * 0.3)),
                
                // Conversion Metrics
                'transactions' => max(1, round($transactions)),
                'revenue' => max(100000, round($revenue)),
                'promo_usage' => $period === 'during' ? max(1, round($transactions * 0.6)) : 0,
                'discount_amount_used' => $period === 'during' ? round($revenue * 0.1) : 0,
                
                // Customer Metrics
                'new_customers' => max(0, round($transactions * 0.3)),
                'returning_customers' => max(0, round($transactions * 0.7)),
                
                // Performance Calculations
                'conversion_rate' => min(50, round(($clicks > 0 ? ($transactions / $clicks) * 100 : 0), 2)),
                'revenue_per_click' => $clicks > 0 ? round($revenue / $clicks) : 0,
                'avg_order_value' => $transactions > 0 ? round($revenue / $transactions) : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            echo "Error creating metric data: " . $e->getMessage() . "\n";
        }
    }

    private function getBaseValuesByPeriod($period)
    {
        // Base values untuk setiap period
        $bases = [
            'before' => [
                'views' => 80,
                'clicks' => 35,
                'transactions' => 8,
                'revenue' => 6000000
            ],
            'during' => [
                'views' => 120, // Increased views karena promo
                'clicks' => 55, // Increased clicks
                'transactions' => 15, // Base transactions sebelum boost
                'revenue' => 9000000 // Base revenue sebelum boost
            ],
            'after' => [
                'views' => 90, // Slight increase dari before period
                'clicks' => 40,
                'transactions' => 10,
                'revenue' => 7000000
            ]
        ];

        return $bases[$period] ?? $bases['before'];
    }
}