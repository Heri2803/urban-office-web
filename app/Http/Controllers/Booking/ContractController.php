<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Traits\DocumentHelperTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ContractController extends Controller
{
    use DocumentHelperTrait;
    /**
     * List contract milik customer
     */
        public function index(Request $request)
    {
        $user = auth()->user();

        // Base query dengan filter user
        $query = Contract::with(['transaction', 'addendums'])
            ->whereHas('transaction', function ($q) use ($user) {
                $q->where(function ($query) use ($user) {
                    $query->whereNotNull('user_id')
                        ->where('user_id', $user->id);
                })->orWhere(function ($query) use ($user) {
                    $query->whereNull('user_id')
                        ->where('email', $user->email);
                });
            })
            ->whereIn('status', ['active', 'expired', 'terminated', 'renewed'])
            ->latest();

        // ✅ Filter server-side by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ✅ Stats dihitung dari SEMUA data user (sebelum filter & paginate)
        $allContracts = (clone $query)->get();
        $stats = [
            'total'    => $allContracts->count(),
            'active'   => $allContracts->where('status', 'active')->count(),
            'expired'  => $allContracts->where('status', 'expired')->count(),
            'renewed'  => $allContracts->where('status', 'renewed')->count(),
        ];

        $contracts = $query->paginate(10)->withQueryString();

        $contractsData = $contracts->map(function ($c) {
            return [
                'id'                   => $c->id,
                'contract_number'      => $c->contract_number,
                'type'                 => $c->type,
                'status'               => $c->status,
                'start_date'           => $c->formatted_start_date ?? '-',
                'end_date'             => $c->formatted_end_date ?? '-',
                'remaining_days'       => $c->remaining_days,
                'display_status'       => $c->status === 'renewed' && $c->remaining_days > 0 ? 'active_renewed' : $c->status,
                'has_pdf'              => $c->hasPdf(),
                'termination_reason'   => $c->termination_reason ?? null, // ✅ tambah ini
                'created_at'           => $c->created_at?->toDateString(),
                'download_url'         => route('customer.contracts.download', $c->id), // ✅ URL dari route
                'renew_url'            => route('customer.contracts.renew.form', $c->id),    // ✅ URL dari route
                'addendums'            => $c->addendums->map(function ($a) {
                    $isLatest = \App\Models\Addendum::where('contract_id', $a->contract_id)->max('addendum_order') == $a->addendum_order;
                    return [
                        'id'              => $a->id,
                        'number'          => $a->addendum_number,
                        'date'            => $a->formatted_addendum_date,
                        'status'          => $a->status,
                        'display_status'  => $a->status === 'renewed' && $a->remaining_days > 0 ? 'active_renewed' : $a->status,
                        'remaining_days'  => $a->remaining_days,
                        'status_badge'    => $a->status_badge,
                        'download_url'    => route('customer.addendums.download', $a->id),
                        'renew_url'       => route('customer.addendums.renew.form', $a->id),
                        'has_pdf'         => $a->hasPdf() || in_array($a->status, ['active']),
                        'is_latest'       => $isLatest,
                    ];
                }),
            ];
        });

        return view('layouts.dashboard.contract', compact('contracts', 'contractsData', 'stats'));
    }

    /**
     * Download PDF kontrak
     */
    public function download(Contract $contract)
    {
        $transaction = $contract->transaction;
        $user = auth()->user();

        $isOwner = ($transaction->user_id && $transaction->user_id == $user->id)
                || (!$transaction->user_id && $transaction->email == $user->email);

        abort_if(!$isOwner, 403, 'Unauthorized access');

        if (!$contract->file_path || !Storage::disk('public')->exists($contract->file_path)) {
            // Attempt to regenerate PDF if missing from disk
            $contract->load(['transaction.location.city', 'transaction.user', 'invoice']);
            $transaction = $contract->transaction;
            $invoice     = $contract->invoice;

            if (!$transaction || !$invoice) {
                return back()->with('error', 'Data kontrak tidak lengkap, tidak bisa regenerate PDF.');
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

                // Kalkulasi nominal sewa
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
                    'sewa_amount'       => $sewaAmount,
                    'terbilang_sewa'    => $this->amountToWords($sewaAmount),
                    'qrCodeBase64'      => $qrCodeBase64,
                ])->setPaper('A4', 'portrait');

                $filename = basename($contract->file_path ?: 'Kontrak-' . $contract->id . '.pdf');
                if (!$contract->file_path) {
                    $contract->file_path = 'contracts/' . $filename;
                    $contract->save();
                }
                
                Storage::disk('public')->put($contract->file_path, $pdf->output());

                Log::info('Customer Contract PDF regenerated successfully.', [
                    'contract_id' => $contract->id,
                    'file_path'   => $contract->file_path,
                ]);

            } catch (\Exception $e) {
                Log::error('Failed to regenerate contract PDF for customer.', [
                    'contract_id' => $contract->id,
                    'error'       => $e->getMessage(),
                ]);
                return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
            }
        }

        $filename = basename($contract->file_path);
        $fullPath = Storage::disk('public')->path($contract->file_path);

        return response()->file($fullPath, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Download PDF Addendum
     */
    public function downloadAddendum(\App\Models\Addendum $addendum)
    {
        $transaction = $addendum->transaction;
        if (!$transaction) {
            $transaction = $addendum->contract->transaction;
        }
        
        $user = auth()->user();

        $isOwner = ($transaction->user_id && $transaction->user_id == $user->id)
                || (!$transaction->user_id && $transaction->email == $user->email);

        abort_if(!$isOwner, 403, 'Unauthorized access');

        if (!$addendum->file_path || !Storage::disk('public')->exists($addendum->file_path)) {
            // Regenerate PDF dari data yang tersimpan
            $addendum->load([
                'contract.transaction.location.city',
                'transaction.location.city',
                'parentAddendum',
                'invoice',
            ]);

            $txn              = $addendum->contract?->transaction ?? $addendum->transaction;
            $originalContract = $addendum->contract;
            $parentAddendum   = $addendum->parentAddendum;

            if (!$txn) {
                return back()->with('error', 'Data transaksi tidak ditemukan untuk regenerate PDF.');
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
                    'transaction'        => $txn,
                    'originalContract'   => $originalContract,
                    'parentAddendum'     => $parentAddendum,
                    'terbilang_baru'     => $terbilang_baru,
                    'terbilang_original' => $terbilang_original,
                    'terbilang_sebelum'  => $terbilang_sebelum,
                    'qrCodeBase64'       => $qrCodeBase64,
                ])->setPaper('A4', 'portrait');

                if (!$addendum->file_path) {
                    $addendumDate        = $addendum->addendum_date ?? now();
                    $fname               = 'Addendum-' . $addendum->roman_order
                                         . '-' . preg_replace('/[^A-Za-z0-9\-]/', '-', $txn->order_id ?? $addendum->id)
                                         . '-' . Carbon::parse($addendumDate)->format('Ymd') . '.pdf';
                    $addendum->file_path = 'addendums/' . $fname;
                    $addendum->save();
                }

                Storage::disk('public')->put($addendum->file_path, $pdf->output());

                Log::info('Customer Addendum PDF regenerated.', [
                    'addendum_id' => $addendum->id,
                    'file_path'   => $addendum->file_path,
                ]);

                return $pdf->stream(basename($addendum->file_path));

            } catch (\Exception $e) {
                Log::error('Failed to regenerate customer Addendum PDF.', [
                    'addendum_id' => $addendum->id,
                    'error'       => $e->getMessage(),
                ]);
                return back()->with('error', 'Gagal generate PDF addendum: ' . $e->getMessage());
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
     * Halaman form perpanjangan kontrak
     */
    public function renewForm(Contract $contract)
    {
        $user = auth()->user();
        $transaction = $contract->transaction;
        
        $isOwner = ($transaction->user_id && $transaction->user_id === $user->id)
                || (!$transaction->user_id && $transaction->email === $user->email);
        
        abort_if(!$isOwner, 403, 'Unauthorized access');
        
        if (!in_array($contract->status, ['active', 'expired'])) {
            return redirect()->route('customer.contracts.index')
                ->with('error', 'Kontrak ini tidak dapat diperpanjang.');
        }
        
        $contract->load(['transaction', 'transaction.location']);
        
        // ✅ TIDAK PERLU CARI HARGA - VIEW AKAN FETCH VIA API
        return view('layouts.dashboard.contract-renew', compact('contract'));
    }

    /**
     * Proses perpanjangan - buat transaksi & dapatkan Midtrans Snap Token
     */
    public function renewProcess(Request $request, $contractId)
    {
        $user = auth()->user();

        Log::info('renewProcess called with:', ['contractId' => $contractId]);

        $oldContract = Contract::find($contractId);

        if (!$oldContract) {
            return $this->jsonOrRedirect($request, false, 'Kontrak tidak ditemukan.', 404);
        }

        $oldTransaction = Transaction::find($oldContract->transaction_id);

        if (!$oldTransaction) {
            return $this->jsonOrRedirect($request, false, 'Data transaksi tidak ditemukan.', 404);
        }

        // Validasi kepemilikan
        $isOwner = ($oldTransaction->user_id && $oldTransaction->user_id == $user->id)
                || (!$oldTransaction->user_id && $oldTransaction->email == $user->email);

        if (!$isOwner) {
            return $this->jsonOrRedirect($request, false, 'Unauthorized access', 403);
        }

        // Hanya active atau expired yang bisa diperpanjang
        if (!in_array($oldContract->status, ['active', 'expired'])) {
            return $this->jsonOrRedirect($request, false, 'Kontrak ini tidak dapat diperpanjang.', 422);
        }

        $request->validate([
            'duration_type'  => 'required|in:month,year',
            'duration_value' => 'required|integer|min:1|max:60',
            'addendum_date'  => 'required|date', // ← tambahan baru
        ]);

        try {
            DB::beginTransaction();

            // 1. HITUNG TOTAL
            $pricePerMonth = $this->getPricePerMonthFromServicePrice($oldTransaction);

            if ($pricePerMonth <= 0) {
                throw new \Exception('Harga per bulan tidak ditemukan.');
            }

            if ($request->duration_type === 'month') {
                $totalAmount = $pricePerMonth * $request->duration_value;
                $bulan = $request->duration_value;
                $tahun = null;
            } else {
                $totalAmount = $pricePerMonth * 12 * $request->duration_value;
                $bulan = null;
                $tahun = $request->duration_value;
            }

            // 2. BUAT TRANSAKSI BARU
            $newTransaction = $oldTransaction->replicate();
            $newTransaction->order_id    = $this->generateRenewalOrderId();
            $newTransaction->bulan       = $bulan;
            $newTransaction->tahun       = $tahun;
            $newTransaction->gross_amount = $totalAmount;
            $newTransaction->status      = 'pending';
            $newTransaction->created_at  = now();
            $newTransaction->updated_at  = now();
            $newTransaction->save();

            // 3. BUAT INVOICE BARU
            $invoiceNumber = \App\Models\Invoice::generateNumber($newTransaction);

            $newInvoice = \App\Models\Invoice::create([
                'transaction_id' => $newTransaction->id,
                'invoice_number' => $invoiceNumber,
                'status'         => 'pending',
                'created_by'     => $user->id,
            ]);

            // 4. TENTUKAN APAKAH BUAT CONTRACT RENEWAL ATAU ADDENDUM
            $existingAddendums = \App\Models\Addendum::where('contract_id', $oldContract->id)->count();

            // Cari original contract (selalu yang pertama)
            $originalContract = $oldContract;

            // Hitung addendum_order → mulai dari 2 karena kontrak = I
            $addendumOrder    = $existingAddendums + 2;

            // Hitung sequence global
            $sequenceNumber   = \App\Models\Addendum::count() + 1;

            // Hitung end_date dari addendum_date + duration
            $addendumDate = \Carbon\Carbon::parse($request->addendum_date);
            $endDate      = $request->duration_type === 'year'
                ? $addendumDate->copy()->addYears($request->duration_value)
                : $addendumDate->copy()->addMonths($request->duration_value);

            // Cari parent_addendum_id
            $parentAddendum = \App\Models\Addendum::where('contract_id', $originalContract->id)
                                                ->orderBy('addendum_order', 'desc')
                                                ->first();

            // 5. SIMPAN DRAFT ADDENDUM (sebelum payment)
            $newAddendum = \App\Models\Addendum::create([
                'contract_id'        => $originalContract->id,
                'transaction_id'     => $newTransaction->id,
                'invoice_id'         => $newInvoice->id,
                'parent_addendum_id' => $parentAddendum?->id ?? null,
                'addendum_order'     => $addendumOrder,
                'sequence_number'    => $sequenceNumber,
                'addendum_date'      => $request->addendum_date,
                'start_date'         => $request->addendum_date, // sama dengan addendum_date
                'end_date'           => $endDate->toDateString(),
                'duration'           => $request->duration_value,
                'duration_type'      => $request->duration_type,
                'gross_amount'       => $totalAmount,
                'status'             => 'draft', // ← draft dulu, active setelah settlement
                'created_by'         => $user->id,
            ]);

            DB::commit();

            // 6. DAPATKAN MIDTRANS SNAP TOKEN
            $snapToken = $this->getMidtransSnapToken($newTransaction, $user, $oldContract->type);

            // Simpan snap token ke transaksi agar bisa dilanjutkan di mails.blade.php
            $newTransaction->update(['snap_token' => $snapToken]);

            Log::info('Contract renewal (addendum) initiated', [
                'old_contract_id'    => $oldContract->id,
                'new_addendum_id'    => $newAddendum->id,
                'addendum_order'     => $addendumOrder,
                'sequence_number'    => $sequenceNumber,
                'new_transaction_id' => $newTransaction->id,
                'order_id'           => $newTransaction->order_id,
                'user_id'            => $user->id,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success'        => true,
                    'message'        => 'Transaksi perpanjangan berhasil dibuat.',
                    'order_id'       => $newTransaction->order_id,
                    'total_amount'   => $totalAmount,
                    'snap_token'     => $snapToken,
                    'addendum_id'    => $newAddendum->id,
                    'invoice_number' => $invoiceNumber,
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Contract renewal failed', [
                'contract_id' => $contractId,
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
            ]);

            return $this->jsonOrRedirect($request, false, 'Gagal memproses perpanjangan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Halaman form perpanjangan Addendum
     */
    public function renewAddendumForm(\App\Models\Addendum $addendum)
    {
        $user = auth()->user();
        
        $transaction = $addendum->transaction;
        if (!$transaction) {
            $transaction = $addendum->contract->transaction;
        }
        
        $isOwner = ($transaction->user_id && $transaction->user_id == $user->id)
                || (!$transaction->user_id && $transaction->email == $user->email);
        
        abort_if(!$isOwner, 403, 'Unauthorized access');
        
        if (!in_array($addendum->status, ['active', 'expired'])) {
            return redirect()->route('customer.contracts.index')
                ->with('error', 'Addendum ini tidak dapat diperpanjang.');
        }
        
        $addendum->load(['contract.transaction.location']);
        
        return view('layouts.dashboard.addendum-renew', compact('addendum'));
    }

    /**
     * Proses perpanjangan Addendum (Membuat transaksi baru & Addendum draft)
     */
    public function renewAddendumProcess(Request $request, $addendumId)
    {
        $user = auth()->user();

        Log::info('renewAddendumProcess called with:', ['addendumId' => $addendumId]);

        $oldAddendum = \App\Models\Addendum::find($addendumId);

        if (!$oldAddendum) {
            return $this->jsonOrRedirect($request, false, 'Addendum tidak ditemukan.', 404);
        }

        $oldTransaction = Transaction::find($oldAddendum->transaction_id ?? $oldAddendum->contract->transaction_id);

        if (!$oldTransaction) {
            return $this->jsonOrRedirect($request, false, 'Data transaksi asal tidak ditemukan.', 404);
        }

        $isOwner = ($oldTransaction->user_id && $oldTransaction->user_id == $user->id)
                || (!$oldTransaction->user_id && $oldTransaction->email == $user->email);

        if (!$isOwner) {
            return $this->jsonOrRedirect($request, false, 'Unauthorized access', 403);
        }

        if (!in_array($oldAddendum->status, ['active', 'expired'])) {
            return $this->jsonOrRedirect($request, false, 'Addendum ini tidak dapat diperpanjang.', 422);
        }

        $request->validate([
            'duration_type'  => 'required|in:month,year',
            'duration_value' => 'required|integer|min:1|max:60',
            'addendum_date'  => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $pricePerMonth = $this->getPricePerMonthFromServicePrice($oldTransaction);

            if ($pricePerMonth <= 0) {
                throw new \Exception('Harga per bulan tidak ditemukan.');
            }

            if ($request->duration_type === 'month') {
                $totalAmount = $pricePerMonth * $request->duration_value;
                $bulan = $request->duration_value;
                $tahun = null;
            } else {
                $totalAmount = $pricePerMonth * 12 * $request->duration_value;
                $bulan = null;
                $tahun = $request->duration_value;
            }

            // BUAT TRANSAKSI BARU
            $newTransaction = $oldTransaction->replicate();
            $newTransaction->order_id    = $this->generateRenewalOrderId();
            $newTransaction->bulan       = $bulan;
            $newTransaction->tahun       = $tahun;
            $newTransaction->gross_amount = $totalAmount;
            $newTransaction->status      = 'pending';
            $newTransaction->created_at  = now();
            $newTransaction->updated_at  = now();
            $newTransaction->save();

            // BUAT INVOICE BARU
            $invoiceNumber = \App\Models\Invoice::generateNumber($newTransaction);
            $newInvoice = \App\Models\Invoice::create([
                'transaction_id' => $newTransaction->id,
                'invoice_number' => $invoiceNumber,
                'status'         => 'pending',
                'created_by'     => $user->id,
            ]);

            $maxOrder = \App\Models\Addendum::where('contract_id', $oldAddendum->contract_id)->max('addendum_order') ?? 1;
            $addendumOrder = $maxOrder + 1;

            $sequenceNumber = \App\Models\Addendum::count() + 1;

            $addendumDate = \Carbon\Carbon::parse($request->addendum_date);
            $endDate      = $request->duration_type === 'year'
                ? $addendumDate->copy()->addYears($request->duration_value)
                : $addendumDate->copy()->addMonths($request->duration_value);

            // SIMPAN DRAFT ADDENDUM BARU
            $newAddendum = \App\Models\Addendum::create([
                'contract_id'        => $oldAddendum->contract_id,
                'transaction_id'     => $newTransaction->id,
                'invoice_id'         => $newInvoice->id,
                'parent_addendum_id' => $oldAddendum->id,
                'addendum_order'     => $addendumOrder,
                'sequence_number'    => $sequenceNumber,
                'addendum_date'      => $request->addendum_date,
                'start_date'         => $request->addendum_date,
                'end_date'           => $endDate->toDateString(),
                'duration'           => $request->duration_value,
                'duration_type'      => $request->duration_type,
                'gross_amount'       => $totalAmount,
                'status'             => 'draft',
                'created_by'         => $user->id,
            ]);

            DB::commit();

            $snapToken = $this->getMidtransSnapToken($newTransaction, $user, $oldAddendum->contract->type);
            $newTransaction->update(['snap_token' => $snapToken]);

            Log::info('Addendum renewal initiated', [
                'parent_addendum_id' => $oldAddendum->id,
                'new_addendum_id'    => $newAddendum->id,
                'new_transaction_id' => $newTransaction->id,
                'user_id'            => $user->id,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success'        => true,
                    'message'        => 'Transaksi perpanjangan Addendum berhasil dibuat.',
                    'order_id'       => $newTransaction->order_id,
                    'total_amount'   => $totalAmount,
                    'snap_token'     => $snapToken,
                    'addendum_id'    => $newAddendum->id,
                    'invoice_number' => $invoiceNumber,
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Addendum renewal failed', [
                'parent_addendum_id' => $addendumId,
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
            ]);

            return $this->jsonOrRedirect($request, false, 'Gagal memproses perpanjangan Addendum: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Helper: JSON atau redirect response
     */
    private function jsonOrRedirect(Request $request, bool $success, string $message, int $status = 200)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => $success,
                'message' => $message,
            ], $status);
        }

        return $success
            ? redirect()->route('customer.contracts.index')->with('success', $message)
            : back()->with('error', $message);
    }

    /**
     * Mendapatkan harga per bulan dari tabel service_prices
     */
    private function getPricePerMonthFromServicePrice($transaction): float
    {
        // Tentukan room_type_id berdasarkan room_type
        $roomTypeId = match ($transaction->room_type) {
            'Virtual Office' => 5,
            'Private Office' => 1,
            'Coworking Space' => 3,
            'Meeting Room' => 2,
            default => null,
        };
        
        if (!$roomTypeId) {
            return 0;
        }
        
        // Cari harga bulanan
        $servicePrice = \App\Models\ServicePrice::where('room_type_id', $roomTypeId)
            ->where('duration_type', 'month')
            ->where('duration', 1)
            ->first();
        
        if ($servicePrice) {
            return $servicePrice->base_price;
        }
        
        return 0;
    }

    /**
     * Generate order ID untuk renewal dengan format Midtrans
     */
    private function generateRenewalOrderId(): string
    {
        do {
            $random    = strtoupper(\Illuminate\Support\Str::random(10));
            $timestamp = time();
            $orderId   = "ORDER-{$random}-{$timestamp}";
        } while (Transaction::where('order_id', $orderId)->exists());

        return $orderId;
    }

    /**
     * Dapatkan Midtrans Snap Token
     */
    private function getMidtransSnapToken(Transaction $transaction, $user, ?string $contractType = null): string
    {
        // Set Midtrans configuration
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
        
        $params = [
            'transaction_details' => [
                'order_id' => $transaction->order_id,
                'gross_amount' => (int) $transaction->gross_amount,
            ],
            'customer_details' => [
                'first_name' => $user->name ?? 'Customer',
                'email' => $user->email ?? $transaction->email,
                'phone' => $user->phone ?? $transaction->phone ?? '',
            ],
            'item_details' => [
                [
                    'id' => 'RENEWAL-' . $transaction->id,
                    'price' => (int) $transaction->gross_amount,
                    'quantity' => 1,
                    'name' => 'Perpanjangan Kontrak ' . ($contractType ?? $transaction->room_type ?? 'Layanan'),
                ]
            ],
            'callbacks' => [
                'finish' => route('customer.contracts.index'),
                'error' => route('customer.contracts.index'),
                'pending' => route('customer.contracts.index'),
            ]
        ];
        
        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token failed', [
                'order_id' => $transaction->order_id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Format teks durasi untuk display
     */
    private function formatDurationText(int $value, string $type): string
    {
        if ($type === 'month') {
            return $value . ' Bulan';
        }
        return $value . ' Tahun';
    }
}