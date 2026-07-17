<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Transaction;
use App\Traits\DocumentHelperTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class ContractController extends Controller
{
    use DocumentHelperTrait;

    /**
     * Display the contract management page.
     * Section 1: Draft contracts (belum di-publish/generate PDF)
     * Section 2: Active/Expired/Terminated contracts
     */
    public function index(Request $request)
    {
        // =============================================
        // SECTION 1: Draft Contracts (Menunggu Konfirmasi Admin)
        // Ambil Transaction yang belum punya Contract berstatus active/expired/terminated
        // =============================================
        $draftQuery = Transaction::with(['user', 'location', 'invoice'])
            ->where('room_type', 'Virtual Office')
            ->whereHas('invoice')
            ->whereDoesntHave('contracts', function($q) {
                $q->whereIn('status', ['active', 'expired', 'terminated', 'renewed']);
            })
            ->orderBy('created_at', 'desc');

        if ($request->filled('search_draft')) {
            $search = $request->search_draft;
            $draftQuery->where(function ($q) use ($search) {
                $q->where('order_id',      'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhereHas('invoice', function ($qi) use ($search) {
                      $qi->where('invoice_number', 'like', "%{$search}%");
                  });
            });
        }

        $draftContracts = $draftQuery
            ->paginate(10, ['*'], 'draft_page')
            ->withQueryString();

        // =============================================
        // SECTION 2: Active Contracts (Kontrak Aktif/Arsip)
        // =============================================
        $activeQuery = Contract::with(['transaction', 'invoice', 'updatedBy', 'addendums'])
            ->virtualOffice()
            ->whereIn('status', ['active', 'expired', 'terminated', 'renewed'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search_active')) {
            $search = $request->search_active;
            $activeQuery->where(function ($q) use ($search) {
                // Cari di kolom contract itu sendiri
                $q->where('contract_number', 'like', "%{$search}%")
                  // Cari di transaksi terkait
                  ->orWhereHas('transaction', function ($qt) use ($search) {
                      $qt->where('order_id',      'like', "%{$search}%")
                         ->orWhere('nama_lengkap', 'like', "%{$search}%")
                         ->orWhere('company_name', 'like', "%{$search}%");
                  })
                  // Cari di invoice terkait
                  ->orWhereHas('invoice', function ($qi) use ($search) {
                      $qi->where('invoice_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status_active')) {
            $activeQuery->where('status', $request->status_active);
        }

        $activeContracts = $activeQuery
            ->paginate(10, ['*'], 'active_page')
            ->withQueryString();

        $activeContracts->getCollection()->transform(function ($contract) {
            $phone = preg_replace('/[^0-9]/', '', $contract->transaction->phone ?? '');
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }
            $namaPelanggan = $contract->transaction->nama_lengkap ?? 'Pelanggan';
            $nomorKontrak = $contract->contract_number ?? '-';
            
            $waMessage = "Halo {$namaPelanggan},\n\nKami menginformasikan bahwa masa sewa Virtual Office Anda dengan Nomor Kontrak: {$nomorKontrak} perlu diperpanjang. Silakan melakukan proses perpanjangan melalui portal kami, Anda dapat mengunjunginya di link berikut: https://my.urbanoffice.id/beforelogin\n\nTerima kasih,\nUrban Office";
            
            $contract->wa_link = $phone ? "https://wa.me/{$phone}?text=" . rawurlencode($waMessage) : '#';
            
            return $contract;
        });

        $activeTab = $request->get('tab', 'vo');

        return view('layouts.admin.management-contract', compact(
            'draftContracts',
            'activeContracts',
            'activeTab'
        ));
    }

    /**
     * Generate PDF untuk kontrak VO.
     * - Dipanggil saat admin klik "Generate PDF" dari draft contract
     * - Menyimpan file PDF ke storage
     * - Update status contract dari draft → active
     * - Set contract_date, contract_number, file_path
     */
    public function generateVirtualOfficeContract(Transaction $transaction, Request $request)
    {
        // Authorization check
        abort_if(!auth()->user()->isAdmin(), 403, 'Unauthorized');

        // Validasi hanya untuk VO
        if ($transaction->room_type !== 'Virtual Office') {
            return back()->with('error', 'Aksi ini hanya untuk transaksi Virtual Office.');
        }

        $request->validate([
            'contract_date' => 'required|date',
        ]);

        // Load relasi yang dibutuhkan
        $transaction->load([
            'location.city',
            'user',
            'invoice',
        ]);

        $invoice = $transaction->invoice;

        if (!$invoice) {
            return back()->with('error', 'Invoice tidak ditemukan. Harap generate invoice terlebih dahulu.');
        }

        $contractDate = Carbon::parse($request->contract_date);

        // Buat atau cari draft kontrak
        $contract = Contract::firstOrCreate(
            ['transaction_id' => $transaction->id, 'status' => 'draft'],
            [
                'invoice_id'    => $invoice->id,
                'created_by'    => auth()->id(),
                'type'          => 'Virtual Office',
                'contract_date' => $contractDate->toDateString(),
            ]
        );

        try {
            // =============================================
            // PERSIAPAN DATA UNTUK PDF
            // =============================================
            $contractDate = Carbon::parse($request->contract_date);

            $startDate  = $contract->start_date ?? $contractDate->copy();
            $bulan      = $transaction->bulan ?? 12;
            $tahun      = $transaction->tahun ?? null;

            $endDate    = $tahun
                ? $startDate->copy()->addYears($tahun)
                : $startDate->copy()->addMonths($bulan);

            $durasiTeks = $tahun
                ? $tahun . ' (' . $this->numberToWords($tahun) . ') tahun'
                : $bulan . ' (' . $this->numberToWords($bulan) . ') bulan';

            // =============================================
            // ✅ KALKULASI NOMINAL SEWA (gross - deposit)
            // =============================================
            $deposit     = (int) ($transaction->deposit ?? 0);
            $grossAmount = (int) $transaction->gross_amount;
            $sewaAmount  = $grossAmount - $deposit;

            // Pastikan contract tersimpan sebelum generate contract number
            $contract->save();
            $sequence       = str_pad($contract->id, 3, '0', STR_PAD_LEFT);
            $contractNumber = $transaction->order_id . '/VO/' . $sequence . '/Urban Office/' . $this->romanize($contractDate->month) . '/' . $contractDate->year;

            // =============================================
            // GENERATE PUBLIC TOKEN & QR CODE
            // =============================================
            if (!$contract->hasPublicToken()) {
                $this->generatePublicToken($contract);
                $contract->refresh();
            }

            $qrCodeBase64 = $this->generateQrCodeBase64($contract->public_token);

            // =============================================
            // GENERATE PDF
            // =============================================
            $pdf = Pdf::loadView('layouts.admin.virtual-office-pdf', [
                'contract'          => $contract,
                'transaction'       => $transaction,
                'invoice'           => $invoice,
                'contractDate'      => $contractDate,
                'startDate'         => $startDate,
                'endDate'           => $endDate,
                'durasiTeks'        => $durasiTeks,
                'contractNumber'    => $contractNumber,
                'terbilang_amount'  => $this->amountToWords($grossAmount),
                'terbilang_deposit' => $this->amountToWords($deposit),
                // ✅ TAMBAHAN BARU
                'sewa_amount'       => $sewaAmount,
                'terbilang_sewa'    => $this->amountToWords($sewaAmount),
                'qrCodeBase64'      => $qrCodeBase64,
            ])->setPaper('A4', 'portrait');

            // =============================================
            // SIMPAN FILE PDF KE STORAGE
            // =============================================
            $filename = 'Kontrak-VO-' . preg_replace('/[^A-Za-z0-9\-]/', '-', $transaction->order_id) . '-' . $contractDate->format('Ymd') . '.pdf';
            $filePath = 'contracts/' . $filename;

            Storage::disk('public')->put($filePath, $pdf->output());

            // =============================================
            // UPDATE RECORD CONTRACT
            // =============================================
            $contract->update([
                'status'          => 'active',
                'contract_number' => $contractNumber,
                'contract_date'   => $contractDate->toDateString(),
                'start_date'      => $startDate->toDateString(),
                'end_date'        => $endDate->toDateString(),
                'file_path'       => $filePath,
                'created_by'      => auth()->id(),
            ]);

            Log::info('Contract PDF generated successfully.', [
                'contract_id'     => $contract->id,
                'contract_number' => $contractNumber,
                'file_path'       => $filePath,
                'public_token'    => $contract->public_token,
            ]);

            return $pdf->stream($filename);

        } catch (\Exception $e) {
            Log::error('Failed to generate contract PDF.', [
                'contract_id' => $contract->id,
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Download ulang PDF kontrak yang sudah pernah digenerate.
     * Mengambil file dari storage, tidak generate ulang dari scratch.
     * Data kontrak frozen sesuai saat pertama digenerate.
     */
        public function downloadContract(Contract $contract)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Unauthorized');

        if (!$contract->hasPdf()) {
            return redirect()->route('admin.contracts.index')
                ->with('error', 'File PDF belum tersedia. Silakan generate ulang kontrak ini.');
        }

        // Jika file tidak ada di storage, regenerate PDF dari data yang sudah tersimpan
        if (!Storage::disk('public')->exists($contract->file_path) || !$contract->hasPublicToken()) {

            $contract->load(['transaction.location.city', 'transaction.user', 'invoice']);
            $transaction = $contract->transaction;
            $invoice     = $contract->invoice;

            if (!$transaction || !$invoice) {
                return redirect()->route('admin.contracts.index')
                    ->with('error', 'Data kontrak tidak lengkap, tidak bisa regenerate PDF.');
            }

            try {
                $contractDate = $contract->contract_date ?? now();
                $bulan        = $transaction->bulan ?? 12;
                $tahun        = $transaction->tahun ?? null;
                $startDate    = $contract->start_date ?? $contractDate;
                $endDate      = $tahun
                    ? Carbon::parse($startDate)->addYears($tahun)
                    : Carbon::parse($startDate)->addMonths($bulan);
                $durasiTeks   = $tahun
                    ? $tahun . ' (' . $this->numberToWords($tahun) . ') tahun'
                    : $bulan . ' (' . $this->numberToWords($bulan) . ') bulan';

                // ✅ TAMBAHAN — kalkulasi nominal sewa
                $deposit     = (int) ($transaction->deposit ?? 0);
                $grossAmount = (int) $transaction->gross_amount;
                $sewaAmount  = $grossAmount - $deposit;

                if (!$contract->hasPublicToken()) {
                    $this->generatePublicToken($contract);
                    $contract->refresh();
                }

                $qrCodeBase64 = $this->generateQrCodeBase64($contract->public_token);

                $pdf = Pdf::loadView('layouts.admin.virtual-office-pdf', [
                    'contract'          => $contract,
                    'transaction'       => $transaction,
                    'invoice'           => $invoice,
                    'contractDate'      => Carbon::parse($contractDate),
                    'startDate'         => Carbon::parse($startDate),
                    'endDate'           => $endDate,
                    'durasiTeks'        => $durasiTeks,
                    'contractNumber'    => $contract->contract_number,
                    'terbilang_amount'  => $this->amountToWords($grossAmount),
                    'terbilang_deposit' => $this->amountToWords($deposit),
                    // ✅ TAMBAHAN BARU
                    'sewa_amount'       => $sewaAmount,
                    'terbilang_sewa'    => $this->amountToWords($sewaAmount),
                    'qrCodeBase64'      => $qrCodeBase64,
                ])->setPaper('A4', 'portrait');

                Storage::disk('public')->put($contract->file_path, $pdf->output());

                Log::info('Contract PDF regenerated successfully.', [
                    'contract_id'  => $contract->id,
                    'file_path'    => $contract->file_path,
                    'public_token' => $contract->public_token,
                ]);

                $filename = basename($contract->file_path);
                return $pdf->stream($filename);

            } catch (\Exception $e) {
                Log::error('Failed to regenerate contract PDF.', [
                    'contract_id' => $contract->id,
                    'error'       => $e->getMessage(),
                    'trace'       => $e->getTraceAsString(),
                ]);

                return redirect()->route('admin.contracts.index')
                    ->with('error', 'Gagal regenerate PDF: ' . $e->getMessage());
            }
        }

        // File ada di storage, stream langsung ke browser
        $filename = basename($contract->file_path);
        $fullPath = Storage::disk('public')->path($contract->file_path);

        return response()->file($fullPath, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    // =============================================
    // PRIVATE HELPERS
    // =============================================

    public function updateStatus(Request $request, Contract $contract)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Unauthorized');
        
        $request->validate([
            'status' => 'required|in:active,expired,terminated,renewed',
            'reason' => 'required_if:status,terminated|nullable|string|max:255',
        ]);
        
        $oldStatus = $contract->status;
        $newStatus = $request->status;
        
        // Validasi transisi status
        $allowedTransitions = [
            'active'     => ['expired', 'terminated'],
            'expired'    => ['renewed'],
            'terminated' => [], // tidak bisa diubah lagi
            'renewed'    => ['active', 'expired'],
        ];
        
        if (!in_array($newStatus, $allowedTransitions[$oldStatus] ?? [])) {
            return response()->json([
                'success' => false,
                'message' => "Tidak dapat mengubah status dari {$oldStatus} ke {$newStatus}."
            ], 422);
        }
        
        $contract->status     = $newStatus;
        $contract->updated_by = auth()->id(); // ← TAMBAHAN: simpan siapa yang ubah status

        // Tambahan data untuk status tertentu
        if ($newStatus === 'terminated') {
            $contract->terminated_at        = now();
            $contract->termination_reason   = $request->reason;
        }
        
        if ($newStatus === 'expired' && $oldStatus === 'active') {
            if ($contract->end_date && $contract->end_date->isFuture()) {
                $contract->end_date = now();
            }
        }
        
        $contract->save();
        
        Log::info('Contract status updated.', [
            'contract_id' => $contract->id,
            'old_status'  => $oldStatus,
            'new_status'  => $newStatus,
            'updated_by'  => auth()->id(), // sudah konsisten dengan DB
        ]);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'message'      => "Status kontrak berhasil diubah menjadi " . ucfirst($this->statusLabel($newStatus)),
                'new_status'   => $newStatus,
                'status_badge' => $contract->status_badge,
            ]);
        }
        
        return redirect()->back()->with('success', 'Status kontrak berhasil diperbarui.');
    }

    /**
     * Helper: Label status untuk response message
     */
    private function statusLabel(string $status): string
    {
        return [
            'active' => 'Aktif',
            'expired' => 'Expired',
            'terminated' => 'Terminated',
            'renewed' => 'Diperpanjang',
            'draft' => 'Draft',
        ][$status] ?? $status;
    }

    /**
     * Halaman Addendum Management (terpisah dari Kontrak)
     */
    public function addendumIndex(Request $request)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Unauthorized');

        $query = \App\Models\Addendum::with([
                'contract.transaction.user',
                'transaction.user',
                'invoice',
            ])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by search (addendum_number or customer name / order_id)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('addendum_number', 'like', "%{$search}%")
                  ->orWhereHas('contract.transaction', function ($q2) use ($search) {
                      $q2->where('order_id', 'like', "%{$search}%")
                         ->orWhere('nama_lengkap', 'like', "%{$search}%")
                         ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        $addendums  = $query->paginate(15)->withQueryString();

        $addendums->getCollection()->transform(function ($addendum) {
            $transaction = $addendum->contract?->transaction ?? $addendum->transaction;
            $phone = preg_replace('/[^0-9]/', '', $transaction->phone ?? '');
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }
            $namaPelanggan = $transaction->nama_lengkap ?? 'Pelanggan';
            $nomorAddendum = $addendum->addendum_number ?? 'Draft Addendum';
            
            $waMessage = "Halo {$namaPelanggan},\n\nKami menginformasikan bahwa masa sewa Addendum Virtual Office Anda dengan Nomor Addendum: {$nomorAddendum} perlu diperpanjang. Silakan melakukan proses perpanjangan melalui portal kami, Anda dapat mengunjunginya di link berikut: https://my.urbanoffice.id/beforelogin\n\nTerima kasih,\nUrban Office";
            
            $addendum->wa_link = $phone ? "https://wa.me/{$phone}?text=" . rawurlencode($waMessage) : '#';
            
            return $addendum;
        });

        // Stats
        $stats = [
            'total'      => \App\Models\Addendum::count(),
            'active'     => \App\Models\Addendum::where('status', 'active')->count(),
            'draft'      => \App\Models\Addendum::where('status', 'draft')->count(),
            'terminated' => \App\Models\Addendum::where('status', 'terminated')->count(),
        ];

        return view('layouts.admin.management-addendum', compact('addendums', 'stats'));
    }

    /**
     * Download PDF Addendum.
     * Jika file fisik tidak ada di storage → regenerate otomatis dari data addendum.
     */
    public function downloadAddendum(\App\Models\Addendum $addendum)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Unauthorized');

        // Cek apakah file masih ada di disk
        $fileExists = $addendum->file_path && Storage::disk('public')->exists($addendum->file_path);

        if (!$fileExists) {
            // Load semua relasi yang dibutuhkan untuk generate PDF
            $addendum->load([
                'contract.transaction.location.city',
                'transaction.location.city',
                'parentAddendum',
                'invoice',
            ]);

            $transaction      = $addendum->contract?->transaction ?? $addendum->transaction;
            $originalContract = $addendum->contract;
            $parentAddendum   = $addendum->parentAddendum;

            if (!$transaction) {
                return redirect()->back()
                    ->with('error', 'Data transaksi tidak ditemukan, tidak bisa regenerate PDF Addendum.');
            }

            try {
                if (!$addendum->public_token) {
                    $this->generatePublicToken($addendum);
                    $addendum->refresh();
                }

                $qrCodeBase64 = $this->generateQrCodeBase64($addendum->public_token, 'addendum.public.verify');

                $terbilang_baru     = $this->amountToWords((int) $addendum->gross_amount);
                $terbilang_original = $this->amountToWords((int) ($originalContract?->transaction?->gross_amount ?? 0));
                $terbilang_sebelum  = $parentAddendum
                    ? $this->amountToWords((int) $parentAddendum->gross_amount)
                    : '';

                $pdf = Pdf::loadView('layouts.admin.virtual-office-addendum-pdf', [
                    'addendum'           => $addendum,
                    'transaction'        => $transaction,
                    'originalContract'   => $originalContract,
                    'parentAddendum'     => $parentAddendum,
                    'terbilang_baru'     => $terbilang_baru,
                    'terbilang_original' => $terbilang_original,
                    'terbilang_sebelum'  => $terbilang_sebelum,
                    'qrCodeBase64'       => $qrCodeBase64,
                ])->setPaper('A4', 'portrait');

                // Buat path baru kalau belum ada
                if (!$addendum->file_path) {
                    $addendumDate = $addendum->addendum_date ?? now();
                    $filename     = 'Addendum-' . $addendum->roman_order
                                  . '-' . preg_replace('/[^A-Za-z0-9\-]/', '-', $transaction->order_id ?? $addendum->id)
                                  . '-' . \Carbon\Carbon::parse($addendumDate)->format('Ymd')
                                  . '.pdf';
                    $addendum->file_path = 'addendums/' . $filename;
                    $addendum->save();
                }

                Storage::disk('public')->put($addendum->file_path, $pdf->output());

                Log::info('Addendum PDF regenerated by admin.', [
                    'addendum_id' => $addendum->id,
                    'file_path'   => $addendum->file_path,
                    'admin_id'    => auth()->id(),
                ]);

                $filename = basename($addendum->file_path);
                return $pdf->stream($filename);

            } catch (\Exception $e) {
                Log::error('Failed to regenerate Addendum PDF.', [
                    'addendum_id' => $addendum->id,
                    'error'       => $e->getMessage(),
                ]);
                return redirect()->back()
                    ->with('error', 'Gagal regenerate PDF Addendum: ' . $e->getMessage());
            }
        }

        $filename = basename($addendum->file_path);
        $fullPath = Storage::disk('public')->path($addendum->file_path);

        return response()->file($fullPath, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);

    }

    /**
     * Terminate Addendum khusus
     */
    public function terminateAddendum(Request $request, \App\Models\Addendum $addendum)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Unauthorized');
        
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);
        
        if ($addendum->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Addendum berstatus aktif yang dapat dibatalkan.'
            ], 422);
        }
        
        $addendum->status = 'terminated';
        $addendum->save();
        
        Log::info('Addendum terminated by admin.', [
            'addendum_id' => $addendum->id,
            'contract_id' => $addendum->contract_id,
            'reason'      => $request->reason,
            'admin_id'    => auth()->id(),
        ]);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'message'      => "Addendum berhasil dibatalkan.",
                'new_status'   => 'terminated',
                'status_badge' => $addendum->status_badge,
            ]);
        }
        
        return redirect()->back()->with('success', 'Addendum berhasil dibatalkan.');
    }
}