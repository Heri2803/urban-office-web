<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use App\Models\PromoUsage;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;

class PromoUsageController extends Controller
{
    /**
     * Display the promo usage report page.
     */
    public function index()
    {
        // For the dropdown filters on frontend
        $promos = Promo::select('id', 'name', 'code')->orderBy('name', 'asc')->get();
        // Remove `where('is_active', true)` because locations table doesn't have it
        $locations = Location::select('id', 'name')->get();

        return view('layouts.admin.promo-usage', compact('promos', 'locations'));
    }

    /**
     * Bangun query gabungan antara event "claimed" (tabel promo_user, saat promo
     * masuk ke wallet customer) dan event "used" (tabel promo_usages, saat promo
     * benar-benar dipakai pada transaksi yang settlement).
     *
     * Kedua sumber di-UNION ALL supaya satu tabel laporan bisa menampilkan seluruh
     * perjalanan promo — bukan cuma pemakaian akhir — untuk ketiga tipe promo
     * (Banner, Discount, Voucher) sekaligus.
     */
    private function buildCombinedQuery(Request $request): Builder
    {
        $search   = $request->get('search');
        $promoId  = $request->get('promo_id');
        $location = $request->get('location');
        $eventType = $request->get('event_type'); // 'claimed' | 'used' | null (semua)

        $claims = DB::table('promo_user as pu')
            ->join('promos as p', 'p.id', '=', 'pu.promo_id')
            ->join('users as u', 'u.id', '=', 'pu.user_id')
            ->leftJoin('promo_types as pt', 'pt.id', '=', 'p.promo_type_id')
            ->leftJoin('promo_categories as pc', 'pc.id', '=', 'p.promo_category_id')
            ->where('pu.is_claimed', true)
            ->when($promoId, fn ($q) => $q->where('pu.promo_id', $promoId))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('u.name', 'like', "%{$search}%")
                       ->orWhere('u.email', 'like', "%{$search}%")
                       ->orWhere('p.code', 'like', "%{$search}%")
                       ->orWhere('p.name', 'like', "%{$search}%");
                });
            })
            ->select([
                DB::raw("CONCAT('claim-', pu.id) as row_id"),
                'pu.promo_id',
                'p.code as promo_code',
                'p.name as promo_name',
                'pt.name as promo_type_name',
                'pc.name as promo_category_name',
                'u.name as user_name',
                'u.email as user_email',
                'u.telephone as user_phone',
                DB::raw("'claimed' as event_type"),
                'pu.claimed_at as event_date',
                DB::raw('NULL as location'),
                DB::raw('NULL as discount_amount'),
                DB::raw('NULL as transaction_amount'),
                DB::raw('NULL as transaction_id'),
            ]);

        // Baris klaim tidak punya data lokasi (belum ada transaksi) — kalau admin
        // filter by lokasi, baris klaim otomatis tidak relevan untuk ditampilkan.
        if ($location) {
            $claims->whereRaw('1 = 0');
        }

        $usages = DB::table('promo_usages as pu2')
            ->join('promos as p', 'p.id', '=', 'pu2.promo_id')
            ->join('users as u', 'u.id', '=', 'pu2.user_id')
            ->leftJoin('promo_types as pt', 'pt.id', '=', 'p.promo_type_id')
            ->leftJoin('promo_categories as pc', 'pc.id', '=', 'p.promo_category_id')
            ->leftJoin('transactions as t', 't.id', '=', 'pu2.transaction_id')
            ->when($promoId, fn ($q) => $q->where('pu2.promo_id', $promoId))
            ->when($location, fn ($q) => $q->where('pu2.location', $location))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('u.name', 'like', "%{$search}%")
                       ->orWhere('u.email', 'like', "%{$search}%")
                       ->orWhere('p.code', 'like', "%{$search}%")
                       ->orWhere('p.name', 'like', "%{$search}%");
                });
            })
            ->select([
                DB::raw("CONCAT('usage-', pu2.id) as row_id"),
                'pu2.promo_id',
                'p.code as promo_code',
                'p.name as promo_name',
                'pt.name as promo_type_name',
                'pc.name as promo_category_name',
                'u.name as user_name',
                'u.email as user_email',
                'u.telephone as user_phone',
                DB::raw("'used' as event_type"),
                'pu2.created_at as event_date',
                'pu2.location',
                'pu2.discount_amount',
                'pu2.transaction_amount',
                't.order_id as transaction_id',
            ]);

        if ($eventType === 'claimed') {
            $combined = $claims;
        } elseif ($eventType === 'used') {
            $combined = $usages;
        } else {
            $combined = $claims->unionAll($usages);
        }

        $wrapped = DB::table(DB::raw('(' . $combined->toSql() . ') as combined'))
                     ->mergeBindings($combined);

        if ($request->filled('date_from')) {
            $wrapped->whereDate('event_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $wrapped->whereDate('event_date', '<=', $request->date_to);
        }

        return $wrapped->orderBy('event_date', 'desc');
    }

    /**
     * API Endpoint to fetch usages data for DataTable
     */
    public function apiIndex(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 25);
            $rows = $this->buildCombinedQuery($request)->paginate($perPage);

            $transformedData = collect($rows->items())->map(function ($row) {
                return $this->formatRow($row);
            });

            return response()->json([
                'success' => true,
                'data' => $transformedData,
                'meta' => [
                    'current_page' => $rows->currentPage(),
                    'last_page' => $rows->lastPage(),
                    'total' => $rows->total(),
                    'per_page' => $rows->perPage()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get summary stats for the cards
     */
    public function getSummaryStats()
    {
        try {
            $totalClaimed = DB::table('promo_user')->where('is_claimed', true)->count();
            $totalUsed = PromoUsage::count();
            $totalDiscount = PromoUsage::sum('discount_amount');

            // Top promo (berdasarkan pemakaian nyata di transaksi, bukan klaim)
            $topPromoId = PromoUsage::select('promo_id', DB::raw('count(*) as total'))
                                   ->groupBy('promo_id')
                                   ->orderByDesc('total')
                                   ->first();

            $topPromo = null;
            if ($topPromoId && $topPromoId->promo_id) {
                $promo = Promo::find($topPromoId->promo_id);
                if ($promo) {
                    $topPromo = [
                        'name' => $promo->name,
                        'count' => $topPromoId->total
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_claimed' => number_format($totalClaimed, 0, ',', '.'),
                    'total_used' => number_format($totalUsed, 0, ',', '.'),
                    'total_discount' => 'Rp ' . number_format((float) $totalDiscount, 0, ',', '.'),
                    'top_promo' => $topPromo
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Export usage+claim data to CSV (Native PHP)
     */
    public function exportCsv(Request $request)
    {
        $rows = $this->buildCombinedQuery($request)->get();
        $date = date('Y-m-d');
        $fileName = "promo-usage-{$date}.csv";

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('No', 'Kode Promo', 'Nama Promo', 'Tipe Promo', 'Nama User', 'Email', 'Status', 'Lokasi', 'Diskon', 'Nilai Transaksi', 'Tanggal');

        $callback = function () use ($rows, $columns) {
            $file = fopen('php://output', 'w');
            // Menambahkan BOM untuk kompatibilitas UTF-8 di Excel
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

            fputcsv($file, $columns);

            $no = 1;
            foreach ($rows as $row) {
                $data = $this->formatRow($row);

                fputcsv($file, array(
                    $no++,
                    $data['promo_code'],
                    $data['promo_name'],
                    $data['promo_type'],
                    $data['user_name'],
                    $data['user_email'] ?? '-',
                    $data['event_type'] === 'claimed' ? 'Diklaim' : 'Digunakan',
                    $data['location'] ?? '-',
                    $data['discount_amount'] !== null ? $data['discount_amount'] : '-',
                    $data['transaction_amount'] !== null ? $data['transaction_amount'] : '-',
                    $data['created_at'],
                ));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Normalisasi 1 baris hasil query gabungan (claim/usage) ke bentuk yang
     * konsisten untuk dikonsumsi frontend maupun export CSV.
     */
    private function formatRow(object $row): array
    {
        $discount = $row->discount_amount !== null ? (float) $row->discount_amount : null;
        $transactionAmount = $row->transaction_amount !== null ? (float) $row->transaction_amount : null;

        return [
            'id' => $row->row_id,
            'promo_id' => $row->promo_id,
            'promo_code' => $row->promo_code ?? '-',
            'promo_name' => $row->promo_name ?? 'Unknown Promo',
            'promo_type' => $row->promo_type_name ?? '-',
            'promo_category' => $row->promo_category_name ?? '-',
            'user_name' => $row->user_name ?? 'Unknown User',
            'user_email' => $row->user_email,
            'user_phone' => $row->user_phone,
            'event_type' => $row->event_type,
            'location' => $row->location,
            'discount_amount' => $discount,
            'formatted_discount' => $discount !== null ? 'Rp ' . number_format($discount, 0, ',', '.') : '-',
            'transaction_amount' => $transactionAmount,
            'formatted_transaction_amount' => $transactionAmount !== null ? 'Rp ' . number_format($transactionAmount, 0, ',', '.') : '-',
            'transaction_id' => $row->transaction_id,
            'created_at' => $row->event_date ? Carbon::parse($row->event_date)->format('d/m/Y H:i') : '-',
        ];
    }
}
