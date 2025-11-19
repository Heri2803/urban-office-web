<?php

namespace App\Http\Controllers\Backend\MitraPanel;

use App\Http\Controllers\Controller;
use App\Models\MonthlyTaxReport;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str; 


class FakturPajakController extends Controller
{
    public function index(Request $request)
    {
        Log::info('=== FAKTUR PAJAK CONTROLLER START ===');

        try {
            $user = auth()->user();
            $mitraId = $user->mitra_id;
            
            // ✅ VALIDASI: Pastikan user memiliki mitra_id
            if (!$mitraId) {
                Log::warning('User tidak memiliki mitra_id', ['user_id' => $user->id]);
                return redirect()->route('mitrapanel.dashboard')
                    ->with('error', 'User tidak terasosiasi dengan mitra');
            }

            Log::info('User mitra_id', ['mitra_id' => $mitraId]);

            // ✅ MODIFIED: Load reports HANYA dari locations milik mitra
            $reports = MonthlyTaxReport::with(['location' => function($query) use ($mitraId) {
                    $query->where('mitra_id', $mitraId); // ✅ FILTER MITRA
                }])
                ->whereHas('location', function($query) use ($mitraId) {
                    $query->where('mitra_id', $mitraId); // ✅ FILTER MITRA
                })
                ->whereNotNull('period')
                ->orderBy('period', 'desc')
                ->get();
            
            Log::info('Reports loaded with mitra filter', [
                'mitra_id' => $mitraId,
                'total' => $reports->count(),
                'with_revenue' => $reports->where('total_revenue', '>', 0)->count()
            ]);

            // ✅ PERBAIKAN: Extract years dengan cara yang lebih safe
            $availableYears = $reports
                ->pluck('period')
                ->filter() // Hapus null values
                ->map(function($period) {
                    // Handle jika period adalah string atau Carbon object
                    if (is_string($period)) {
                        return \Carbon\Carbon::parse($period)->year;
                    }
                    return $period->year;
                })
                ->unique()
                ->sort()
                ->values()
                ->toArray();

            // Fallback jika tidak ada years
            if (empty($availableYears)) {
                $availableYears = [date('Y')];
            }

            Log::info('Years extracted', ['years' => $availableYears]);

            // ✅ MODIFIED: Get locations HANYA milik mitra
            $availableLocations = Location::where('mitra_id', $mitraId) // ✅ FILTER MITRA
                ->orderBy('name')
                ->get();

            Log::info('Locations loaded with mitra filter', [
                'mitra_id' => $mitraId,
                'locations_count' => $availableLocations->count()
            ]);

            $filters = [
                'location' => $request->get('location', 'all'),
                'year' => $request->get('year', 'all'),
                'status' => $request->get('status', 'all'),
            ];

            Log::info('Data prepared successfully with mitra filter');

        } catch (\Exception $e) {
            Log::error('CONTROLLER ERROR: ' . $e->getMessage());
            Log::error('Stack: ' . $e->getTraceAsString());
            
            // Fallback data dengan filter mitra
            $user = auth()->user();
            $mitraId = $user->mitra_id ?? 0;
            
            $reports = collect();
            $availableYears = [2024, 2025];
            $availableLocations = $mitraId 
                ? Location::where('mitra_id', $mitraId)->get()
                : collect();
            $filters = ['location' => 'all', 'year' => 'all', 'status' => 'all'];
        }

        Log::info('=== FAKTUR PAJAK CONTROLLER END ===');
        Log::info('Final data with mitra filter:', [
            'mitra_id' => $mitraId ?? 'unknown',
            'reports_count' => $reports->count(),
            'years_count' => count($availableYears),
            'locations_count' => $availableLocations->count()
        ]);

        return view('layouts.mitrapanel.faktur-pajak', compact(
            'reports', 
            'filters', 
            'availableYears',
            'availableLocations'
        ));
    }

        public function downloadPDF(MonthlyTaxReport $faktur)
    {
        try {
            // Load data dengan relasi
            $faktur->load('location');
            
            // AMBIL DATA TRANSACTIONS BERDASARKAN PERIODE & LOKASI
            $transactions = \App\Models\Transaction::where('location_id', $faktur->location_id)
                ->whereIn('status', ['settlement', 'capture', 'paid'])
                ->whereYear('booking_date', $faktur->period->year)
                ->whereMonth('booking_date', $faktur->period->month)
                ->get();

            // GROUP BY ROOM_TYPE UNTUK DETAIL LAYANAN
            $serviceDetails = $transactions->groupBy('room_type')->map(function($transactions, $roomType) {
                return [
                    'room_type' => $roomType,
                    'total_amount' => $transactions->sum('gross_amount'),
                    'transaction_count' => $transactions->count()
                ];
            })->values();

            $data = [
                'faktur' => $faktur,
                'formatted_period' => $faktur->period->translatedFormat('F Y'),
                'service_details' => $serviceDetails,
                'total_transactions' => $transactions->count()
            ];

            $pdf = Pdf::loadView('layouts.mitrapanel.exports.faktur-pajak-pdf', $data);
            
            $pdf->setPaper('A4', 'portrait');
            $filename = "Faktur-Pajak-{$faktur->invoice_number}.pdf";
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Download semua faktur untuk satu lokasi (PDF ZIP)
     */
    public function downloadLocationFaktur($locationId)
    {
        try {
            $user = auth()->user();
            $mitraId = $user->mitra_id;

            // ✅ VALIDASI: Pastikan user memiliki mitra_id
            if (!$mitraId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terasosiasi dengan mitra'
                ], 403);
            }

            // ✅ VALIDASI: Pastikan location milik mitra
            $location = Location::where('id', $locationId)
                ->where('mitra_id', $mitraId)
                ->firstOrFail();

            // ✅ Ambil semua faktur untuk lokasi ini
            $reports = MonthlyTaxReport::where('location_id', $locationId)
                ->whereHas('location', function($query) use ($mitraId) {
                    $query->where('mitra_id', $mitraId);
                })
                ->where('total_revenue', '>', 0) // Hanya yang punya data
                ->orderBy('period', 'desc')
                ->get();

            if ($reports->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada faktur untuk lokasi ini'
                ], 404);
            }

            Log::info('Download location faktur', [
                'location_id' => $locationId,
                'location_name' => $location->name,
                'faktur_count' => $reports->count(),
                'mitra_id' => $mitraId
            ]);

            // ✅ Generate ZIP file dengan semua faktur
            return $this->generateFakturZip($reports, $location->name);

        } catch (\Exception $e) {
            Log::error('Error downloading location faktur: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal download faktur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download semua faktur untuk semua lokasi milik mitra (PDF ZIP)
     */
    public function downloadAllFaktur()
    {
        try {
            $user = auth()->user();
            $mitraId = $user->mitra_id;

            // ✅ VALIDASI: Pastikan user memiliki mitra_id
            if (!$mitraId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terasosiasi dengan mitra'
                ], 403);
            }

            // ✅ Ambil semua faktur milik mitra
            $reports = MonthlyTaxReport::whereHas('location', function($query) use ($mitraId) {
                    $query->where('mitra_id', $mitraId);
                })
                ->where('total_revenue', '>', 0) // Hanya yang punya data
                ->orderBy('location_id')
                ->orderBy('period', 'desc')
                ->get();

            if ($reports->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada faktur untuk di-download'
                ], 404);
            }

            Log::info('Download all faktur', [
                'mitra_id' => $mitraId,
                'faktur_count' => $reports->count(),
                'locations_count' => $reports->pluck('location_id')->unique()->count()
            ]);

            // ✅ Generate ZIP file dengan semua faktur
            return $this->generateFakturZip($reports, 'Semua-Lokasi');

        } catch (\Exception $e) {
            Log::error('Error downloading all faktur: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal download semua faktur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method untuk generate ZIP file
     */
    private function generateFakturZip($reports, $prefix)
    {
        $zipFileName = 'faktur-pajak-' . Str::slug($prefix) . '-' . now()->format('Y-m-d') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Ensure temp directory exists
        if (!File::exists(dirname($zipPath))) {
            File::makeDirectory(dirname($zipPath), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($reports as $report) {
                // Generate PDF untuk setiap faktur
                $pdf = $this->generateFakturPdf($report);
                $pdfFileName = $this->getFakturFileName($report);
                
                // Add PDF to ZIP
                $zip->addFromString($pdfFileName, $pdf->output());
            }
            $zip->close();
        }

        // Return download response
        return response()->download($zipPath, $zipFileName)
            ->deleteFileAfterSend(true);
    }

    /**
     * Helper method untuk generate nama file PDF
     */
    private function getFakturFileName($report)
    {
        $locationName = Str::slug($report->location->name ?? 'unknown');
        $period = $report->period->format('Y-m');
        $invoiceNumber = $report->invoice_number;
        
        return "Faktur-{$locationName}-{$period}-{$invoiceNumber}.pdf";
    }

    /**
     * Helper method untuk generate PDF (gunakan logic yang sudah ada)
     */
        private function generateFakturPdf($report)
    {
        // Format period untuk display
        $formattedPeriod = $report->period->format('F Y');
        
        // Data untuk template - GUNAKAN VARIABLE NAME YANG DIEXPECT
        $data = [
            'faktur' => $report, // ✅ GUNAKAN 'faktur' BUKAN 'report'
            'location' => $report->location,
            'formatted_period' => $formattedPeriod,
            'service_details' => collect(), // Kosongkan dulu
            'total_transactions' => 0
        ];
        
        // Gunakan template existing
        $pdf = \PDF::loadView('layouts.mitrapanel.exports.faktur-pajak-pdf', $data);
        
        return $pdf->setPaper('a4', 'portrait');
    }
}