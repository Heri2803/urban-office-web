<?php
// app/Http/Controllers/Admin/VirtualOfficeController.php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\VirtualOfficeImportTemplate;
use App\Imports\VirtualOfficeImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class VirtualOfficeController extends Controller
{
    /**
     * Display a listing of virtual office transactions.
     */
    public function index(Request $request)
    {
        // Base query dengan relasi yang diperlukan
        $query = Transaction::with(['user', 'documents'])
            ->where('room_type', 'Virtual Office')
            ->whereIn('status', ['settlement', 'pending', 'expire']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('order_id', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('booking_date', [$request->date_from, $request->date_to]);
        }

        // Sorting
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $transactions = $query->paginate(12)->withQueryString();

        // Statistik
        $stats = [
            'total' => Transaction::where('room_type', 'Virtual Office')->count(),
            'settlement' => Transaction::where('room_type', 'Virtual Office')->where('status', 'settlement')->count(),
            'pending' => Transaction::where('room_type', 'Virtual Office')->where('status', 'pending')->count(),
            'expire' => Transaction::where('room_type', 'Virtual Office')->where('status', 'expire')->count(),
            'total_deposit' => Transaction::where('room_type', 'Virtual Office')->where('status', 'settlement')->sum('deposit'),
            'total_revenue' => Transaction::where('room_type', 'Virtual Office')->where('status', 'settlement')->sum('gross_amount'),
        ];

        // Untuk API request (Postman)
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $transactions,
                'stats' => $stats
            ]);
        }

        // Untuk web view
        return view('layouts.admin.virtual-office-management', compact('transactions', 'stats'));
    }

    /**
     * Display the specified transaction details.
     */
    public function show(Request $request, $id)
    {
        $transaction = Transaction::with(['user', 'documents'])
            ->where('room_type', 'Virtual Office')
            ->findOrFail($id);

        // Untuk API request (Postman)
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $transaction
            ]);
        }

        // Untuk web view
        return redirect()->route('admin.virtual-office.index');
    }

    /**
     * Get documents for a transaction (AJAX).
     */
    public function getDocuments($transactionId)
    {
        $transaction = Transaction::where('room_type', 'Virtual Office')->findOrFail($transactionId);
        
        $documents = $transaction->documents()
            ->with('verifier') // Perbaikan: dari 'verifiedBy' menjadi 'verifier'
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($doc) {
                return [
                    'id' => $doc->id,
                    'type' => $doc->document_type,
                    'type_label' => $doc->type_label, // Bisa pakai accessor dari model
                    'filename' => $doc->filename,
                    'original_filename' => $doc->original_filename,
                    'file_url' => $doc->file_url, // Pakai accessor dari model
                    'file_size' => $doc->formatted_size, // Pakai accessor dari model
                    'mime_type' => $doc->mime_type,
                    'status' => $doc->status,
                    'status_badge' => $this->getStatusBadge($doc->status),
                    'notes' => $doc->notes,
                    'uploaded_at' => $doc->created_at->format('d M Y H:i'),
                    'verified_by' => $doc->verifier?->name, // Perbaikan: dari verifiedBy menjadi verifier
                    'verified_at' => $doc->verified_at?->format('d M Y H:i'),
                    'can_download' => $doc->status === 'verified' || auth()->user()->role === 'admin',
                ];
            });

        return response()->json([
            'success' => true,
            'documents' => $documents,
            'transaction' => [
                'id' => $transaction->id,
                'nama_lengkap' => $transaction->nama_lengkap,
                'order_id' => $transaction->order_id,
            ]
        ]);
    }

    /**
     * Verify a document.
     */
    public function verifyDocument(Request $request, $documentId)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'notes' => 'nullable|string|max:500',
        ]);

        $document = Document::findOrFail($documentId);
        
        $document->status = $request->status;
        $document->notes = $request->notes;
        $document->verified_by = auth()->id();
        $document->verified_at = now();
        $document->save();

        // Cek kelengkapan dokumen setelah verifikasi
        $transaction = $document->transaction;
        $isComplete = $transaction->hasCompleteDocuments();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil diverifikasi',
            'is_complete' => $isComplete,
            'document' => $document
        ]);
    }

    /**
     * Download document.
     */
    public function downloadDocument($documentId)
    {
        $document = Document::findOrFail($documentId);
        
        // Cek otorisasi
        if ($document->status !== 'verified' && auth()->user()->role !== 'admin') {
            abort(403, 'Dokumen belum diverifikasi');
        }

        // Gunakan disk 'public' dan path langsung dari database
        // Karena file_path di database sudah relatif ke storage/app/public/
        $filePath = $document->file_path; // "documents/transaction_96/1772249629_96_npwp.jpeg"
        
        // Cek apakah file ada di storage public
        if (!Storage::disk('public')->exists($filePath)) {
            // Log error untuk debugging
            \Log::error('File not found:', [
                'document_id' => $documentId,
                'file_path' => $filePath,
                'full_path' => storage_path('app/public/' . $filePath)
            ]);
            
            abort(404, 'File tidak ditemukan');
        }

        // Download file
        return Storage::disk('public')->download($filePath, $document->original_filename);
    }

    /**
     * Export data to PDF.
     */
    public function export(Request $request)
    {
        $query = Transaction::with(['user', 'documents'])
            ->where('room_type', 'Virtual Office');

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['settlement', 'pending', 'expire']);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('order_id', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Date range filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('booking_date', [$request->date_from, $request->date_to]);
        }

        $query->orderBy('created_at', 'desc');

        // Limit filter
        $limit = $request->input('limit');
        if ($limit && in_array((int)$limit, [50, 100, 200, 500])) {
            $query->limit((int)$limit);
        }

        $transactions = $query->get();

        // Get logo
        $logoPath = 'D:\\laragon\\www\\webappurban\\web-app-urbanoffice\\public\\assets\\urban office new logo.jpeg';
        if (!\Illuminate\Support\Facades\File::exists($logoPath)) {
            $logoPath = public_path('assets/urban office new logo.jpeg');
        }
        $logoBase64 = null;
        if (\Illuminate\Support\Facades\File::exists($logoPath)) {
            try {
                $logoData = \Illuminate\Support\Facades\File::get($logoPath);
                $logoBase64 = 'data:image/jpeg;base64,' . base64_encode($logoData);
            } catch (\Exception $e) {
                \Log::error('Failed to load logo in VirtualOffice export PDF: ' . $e->getMessage());
            }
        }

        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('layouts.admin.exports.virtual-office-pdf', [
            'transactions' => $transactions,
            'logoBase64' => $logoBase64,
            'filters' => [
                'status' => $request->status,
                'search' => $request->search,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'limit' => $request->limit,
            ]
        ])->setPaper('A4', 'landscape');

        $filename = 'virtual-office-export-' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    // ========== HELPER METHODS ==========

    private function getDocumentTypeLabel($type)
    {
        $labels = [
            'ktp' => 'KTP',
            'npwp' => 'NPWP',
            'akta_perusahaan' => 'Akta Perusahaan',
            'siup_nib' => 'SIUP/NIB',
            'other' => 'Dokumen Lain'
        ];

        return $labels[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    private function getStatusBadge($status)
    {
        $badges = [
            'pending' => 'warning',
            'verified' => 'success',
            'rejected' => 'danger'
        ];

        return $badges[$status] ?? 'secondary';
    }

    private function formatDuration($transaction)
    {
        if ($transaction->jam) return $transaction->jam . ' Jam';
        if ($transaction->hari) return $transaction->hari . ' Hari';
        if ($transaction->minggu) return $transaction->minggu . ' Minggu';
        if ($transaction->bulan) return $transaction->bulan . ' Bulan';
        if ($transaction->tahun) return $transaction->tahun . ' Tahun';
        return '-';
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Download dynamic Excel template for importing VO tenants.
     */
    public function downloadTemplate()
    {
        return Excel::download(new VirtualOfficeImportTemplate(), 'template_import_virtual_office.xlsx');
    }

    /**
     * Import VO tenants from Excel file with database transaction.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120', // Max 5MB
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes' => 'Format file harus berupa .xlsx atau .xls.',
            'file.max' => 'Ukuran file tidak boleh lebih dari 5MB.',
        ]);

        DB::beginTransaction();

        try {
            $import = new VirtualOfficeImport(auth()->id());
            Excel::import($import, $request->file('file'));

            $errors = $import->getErrors();

            if (count($errors) > 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'errors' => $errors,
                ], 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengimpor {$import->getImportedCount()} data penyewa Virtual Office.",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Virtual Office Import Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'errors' => ['Gagal memproses file Excel: ' . $e->getMessage()],
            ], 500);
        }
    }
}