<?php

namespace App\Http\Controllers\Backend\MitraPanel;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use App\Models\PromoMetric;
use App\Models\PromoUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HistoryPromoController extends Controller
{
    /**
     * Display promo history page for partner
     */
    public function index()
    {
        return view('layouts.mitrapanel.history-promo');
    }

    /**
     * API untuk get promo history dengan analytics REAL dari database
     */
    public function getPromoHistory(Request $request)
    {
        try {
            $partnerId = auth()->id();
            $location = $request->get('location', 'all');
            $status = $request->get('status', 'all');
            $service = $request->get('service', 'all');
            $search = $request->get('search', '');

            Log::info("🔍 Partner Promo History Request", [
                'partner_id' => $partnerId,
                'location' => $location,
                'status' => $status,
                'service' => $service,
                'search' => $search
            ]);

            // DEBUG: Check what promos exist in database - RELAX FILTERS DULU
            $allPromos = Promo::with(['type', 'category'])->get();
            Log::info("📊 Total promos in database: " . $allPromos->count());
            
            foreach ($allPromos as $promo) {
                Log::info("Promo #{$promo->id}: {$promo->name}", [
                    'status' => $promo->status,
                    'is_approved' => $promo->is_approved,
                    'locations' => $promo->locations,
                    'service_types' => $promo->service_types,
                    'start_date' => $promo->start_date,
                    'end_date' => $promo->end_date
                ]);
            }

            // Build query dengan FILTERS YANG LEBIH RELAXED
            $query = Promo::with(['type', 'category']);

            Log::info("🔄 Building query with RELAXED filters...");

            // Status filter - lebih relaxed
            if ($status !== 'all') {
                $query->where('status', $status);
                Log::info("✅ Applied status filter: {$status}");
            } else {
                // Include semua status kecuali draft untuk testing
                $query->where('status', '!=', 'draft');
                Log::info("✅ Excluding draft status only");
            }

            // APPROVAL FILTER - COMMENT DULU UNTUK TESTING
            // $query->where('is_approved', true);
            Log::info("🟡 SKIPPING approval filter for testing");

            // Location filter - lebih relaxed
            if ($location !== 'all') {
                $query->whereJsonContains('locations', $location);
                Log::info("✅ Applied location filter: {$location}");
            } else {
                Log::info("✅ No location filter (all locations)");
            }

            // Service filter - lebih relaxed  
            if ($service !== 'all') {
                if ($service === 'all-services') {
                    $query->where(function($q) {
                        $q->whereJsonContains('service_types', 'all-services')
                          ->orWhereNull('service_types')
                          ->orWhere('service_types', '[]');
                    });
                    Log::info("✅ Applied service filter: all-services (relaxed)");
                } else {
                    $query->whereJsonContains('service_types', $service);
                    Log::info("✅ Applied service filter: {$service}");
                }
            } else {
                Log::info("✅ No service filter (all services)");
            }

            // Search filter
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
                Log::info("✅ Applied search filter: {$search}");
            }

            // Ordering
            $query->orderBy('start_date', 'desc');

            // Get results
            $promos = $query->get();
            Log::info("📈 Query found {$promos->count()} promos");

            // DEBUG: Log each found promo
            foreach ($promos as $promo) {
                Log::info("🎯 Found promo: {$promo->name} (ID: {$promo->id})", [
                    'status' => $promo->status,
                    'is_approved' => $promo->is_approved,
                    'locations' => $promo->locations,
                    'service_types' => $promo->service_types
                ]);
            }

            // Jika masih tidak ada data, gunakan fallback
            if ($promos->count() === 0) {
                Log::warning("⚠️ No promos found with current filters, using fallback data");
                $fallbackData = $this->getFallbackData();
                return response()->json([
                    'success' => true,
                    'data' => $fallbackData['promos'],
                    'summary' => $fallbackData['summary'],
                    'filters' => [
                        'location' => $location,
                        'status' => $status,
                        'service' => $service,
                        'search' => $search
                    ],
                    'debug' => [
                        'message' => 'Using fallback data - no promos in database',
                        'total_promos_in_db' => $allPromos->count(),
                        'query_found' => 0
                    ]
                ]);
            }

            // Enhance dengan metrics data
            $enhancedPromos = $promos->map(function($promo) use ($location) {
                return $this->enhancePromoWithMetrics($promo, $location);
            });

            $summary = $this->getSummary($enhancedPromos);

            Log::info("✅ Final result: {$enhancedPromos->count()} enhanced promos");

            return response()->json([
                'success' => true,
                'data' => $enhancedPromos,
                'summary' => $summary,
                'filters' => [
                    'location' => $location,
                    'status' => $status,
                    'service' => $service,
                    'search' => $search
                ],
                'debug' => [
                    'total_promos_in_db' => $allPromos->count(),
                    'query_found' => $promos->count(),
                    'enhanced_count' => $enhancedPromos->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Partner Promo History Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load promo history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enhance promo data dengan REAL metrics dari database
     */
    private function enhancePromoWithMetrics($promo, $location)
    {
        Log::info("📈 Enhancing promo: {$promo->name}");

        // Get REAL metrics data
        $metricsQuery = PromoMetric::where('promo_id', $promo->id);
        
        if ($location !== 'all') {
            $metricsQuery->where('location', $location);
        }
        
        $metrics = $metricsQuery->get();
        Log::info("   📊 Found {$metrics->count()} metrics for promo {$promo->id}");

        // Get REAL usage data
        $usageQuery = PromoUsage::where('promo_id', $promo->id);
        
        if ($location !== 'all') {
            $usageQuery->where('location', $location);
        }
        
        $usages = $usageQuery->get();
        Log::info("   👥 Found {$usages->count()} usages for promo {$promo->id}");

        // Calculate impact metrics
        $impact = $this->calculatePromoImpact($promo, $metrics, $usages);

        return [
            'id' => $promo->id,
            'name' => $promo->name,
            'code' => $promo->code,
            'service' => $this->getPrimaryService($promo->service_types),
            'location' => $location === 'all' ? 'Multiple Locations' : $this->formatLocationName($location),
            'startDate' => $promo->start_date->format('d M Y'),
            'endDate' => $promo->end_date->format('d M Y'),
            'status' => $promo->status,
            'impact' => $impact,
            'type' => $promo->type->name,
            'category' => $promo->category->name,
            'discount_info' => $this->getDiscountInfo($promo),
            'usage_info' => $this->getUsageInfo($promo),
            'total_usage' => $usages->count(),
            'total_revenue' => $usages->sum('transaction_amount'),
            'total_discount' => $usages->sum('discount_amount')
        ];
    }

    /**
     * Calculate REAL promo impact metrics dari data database
     */
    private function calculatePromoImpact($promo, $metrics, $usages)
    {
        // Jika tidak ada metrics data, return null
        if ($metrics->isEmpty()) {
            return null;
        }

        $totalBefore = $this->getBeforePeriodMetrics($promo, $metrics);
        $totalDuring = $this->getDuringPeriodMetrics($promo, $metrics);
        $totalAfter = $this->getAfterPeriodMetrics($promo, $metrics);

        // Calculate percentages berdasarkan data REAL
        $transactionIncrease = $totalBefore['transactions'] > 0 
            ? (($totalDuring['transactions'] - $totalBefore['transactions']) / $totalBefore['transactions']) * 100 
            : ($totalDuring['transactions'] > 0 ? 100 : 0); // Jika tidak ada data before, anggap 100% increase

        $revenueIncrease = $totalBefore['revenue'] > 0 
            ? (($totalDuring['revenue'] - $totalBefore['revenue']) / $totalBefore['revenue']) * 100 
            : ($totalDuring['revenue'] > 0 ? 100 : 0);

        $afterEffect = $totalBefore['transactions'] > 0 && $totalAfter['transactions'] > 0
            ? (($totalAfter['transactions'] - $totalBefore['transactions']) / $totalBefore['transactions']) * 100
            : null;

        return [
            'transactionIncrease' => round($transactionIncrease, 1),
            'revenueIncrease' => round($revenueIncrease, 1),
            'afterEffect' => $afterEffect ? round($afterEffect, 1) : null,
            'before' => $totalBefore,
            'during' => array_merge($totalDuring, [
                'promoUsage' => $usages->count(),
                'totalDiscount' => $usages->sum('discount_amount')
            ]),
            'after' => $totalAfter['transactions'] > 0 ? $totalAfter : null
        ];
    }

    /**
     * Get metrics for before promo period (30 days sebelum promo)
     */
    private function getBeforePeriodMetrics($promo, $metrics)
    {
        $beforeStart = $promo->start_date->copy()->subDays(30);
        $beforeEnd = $promo->start_date->copy()->subDay();

        $beforeMetrics = $metrics->whereBetween('metric_date', [$beforeStart->format('Y-m-d'), $beforeEnd->format('Y-m-d')]);

        return [
            'transactions' => $beforeMetrics->sum('transactions'),
            'revenue' => $beforeMetrics->sum('revenue')
        ];
    }

    /**
     * Get metrics for during promo period
     */
    private function getDuringPeriodMetrics($promo, $metrics)
    {
        $duringMetrics = $metrics->whereBetween('metric_date', [
            $promo->start_date->format('Y-m-d'), 
            $promo->end_date->format('Y-m-d')
        ]);

        return [
            'transactions' => $duringMetrics->sum('transactions'),
            'revenue' => $duringMetrics->sum('revenue')
        ];
    }

    /**
     * Get metrics for after promo period (30 days setelah promo)
     */
    private function getAfterPeriodMetrics($promo, $metrics)
    {
        $afterStart = $promo->end_date->copy()->addDay();
        $afterEnd = $promo->end_date->copy()->addDays(30);

        // Cek jika after period sudah lewat dari sekarang
        if ($afterStart->gt(now())) {
            return ['transactions' => 0, 'revenue' => 0];
        }

        $afterMetrics = $metrics->whereBetween('metric_date', [
            $afterStart->format('Y-m-d'), 
            min($afterEnd->format('Y-m-d'), now()->format('Y-m-d'))
        ]);

        return [
            'transactions' => $afterMetrics->sum('transactions'),
            'revenue' => $afterMetrics->sum('revenue')
        ];
    }

    /**
     * Get summary statistics
     */
    private function getSummary($promos)
    {
        $activePromos = $promos->where('status', 'active')->count();
        $promosWithImpact = $promos->filter(fn($p) => $p['impact'] !== null);
        
        $avgImpact = $promosWithImpact->isNotEmpty()
            ? round($promosWithImpact->avg(fn($p) => $p['impact']['transactionIncrease']), 1)
            : 0;

        $bestPromo = $promosWithImpact->sortByDesc(fn($p) => $p['impact']['transactionIncrease'])->first();

        return [
            'totalPromos' => $promos->count(),
            'activePromos' => $activePromos,
            'avgImpact' => $avgImpact,
            'bestPromo' => $bestPromo ? [
                'name' => $bestPromo['name'],
                'impact' => $bestPromo['impact']['transactionIncrease']
            ] : ['name' => 'No data', 'impact' => 0]
        ];
    }

    /**
     * Helper methods
     */
    private function getPrimaryService($serviceTypes)
    {
        if (empty($serviceTypes) || in_array('all-services', $serviceTypes)) {
            return 'All Services';
        }
        
        $serviceMap = [
            'private-office' => 'Private Office',
            'virtual-office' => 'Virtual Office', 
            'coworking-space' => 'Coworking Space',
            'meeting-room' => 'Meeting Room',
            'event-space' => 'Event Space',
            'sharing-room' => 'Sharing Room'
        ];
        
        return $serviceMap[$serviceTypes[0]] ?? 'All Services';
    }

    private function getDiscountInfo($promo)
    {
        if (!$promo->discount_type) {
            return 'No Discount';
        }
        
        $amount = number_format($promo->discount_amount);
        return $amount . ($promo->discount_type === 'percentage' ? '%' : ' IDR');
    }

    private function getUsageInfo($promo)
    {
        if (!$promo->usage_limit) {
            return 'Unlimited';
        }
        
        $used = $promo->usage_count;
        $remaining = $promo->usage_limit - $used;
        return "{$used}/{$promo->usage_limit} used ({$remaining} left)";
    }

    private function formatLocationName($location)
    {
        return ucwords(str_replace('-', ' ', $location));
    }

    /**
     * Export functionality untuk partner
     */
    public function exportHistory(Request $request)
    {
        try {
            $type = $request->get('type', 'pdf'); // pdf or excel
            $filters = $request->all();

            // Reuse the same logic from getPromoHistory
            $response = $this->getPromoHistory(new Request($filters));
            $data = json_decode($response->getContent(), true);

            if (!$data['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate export data'
                ], 500);
            }

            // TODO: Implement actual PDF/Excel export
            // For now return success message
            return response()->json([
                'success' => true,
                'message' => 'Export functionality will be implemented soon',
                'filters' => $filters,
                'data_count' => count($data['data'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Export Promo History Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }
}