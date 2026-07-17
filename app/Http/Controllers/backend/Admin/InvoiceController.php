<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Location;
use App\Models\Room;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\City;
use App\Models\LunchOption;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    private function getViewData(Request $request = null): array
    {
        // --- $transactions untuk invoice-generate.blade.php ---
        // NOTE: Setelah Observer aktif + backfill dijalankan, list ini akan
        // kosong karena semua transaksi sudah punya invoice otomatis.
        // Tab ini tetap dipertahankan untuk edge case / transaksi gagal observer.
        $transactionQuery = Transaction::with(['user', 'location'])
            ->whereDoesntHave('invoice')
            ->latest();

        if ($request && $request->filled('search_transaction')) {
            $search = $request->search_transaction;
            $transactionQuery->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhere('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $transactions = $transactionQuery
            ->paginate(10)
            ->withPath(route('admin.invoices.index'))
            ->withQueryString();

        // --- $invoices untuk invoice-list.blade.php ---
        $invoiceQuery = Invoice::with(['transaction', 'creator'])->latest();

        if ($request && $request->filled('status')) {
            $invoiceQuery->ofStatus($request->status);
        }
        if ($request && $request->filled('date_from') && $request->filled('date_to')) {
            $invoiceQuery->dateRange($request->date_from, $request->date_to);
        }
        if ($request && $request->filled('search')) {
            $invoiceQuery->search($request->search);
        }

        $invoices = $invoiceQuery->paginate(15)->withQueryString();

        // --- $stats untuk summary box di invoice-list.blade.php ---
        $stats = [
            'total'      => Invoice::count(),
            'pending'    => Invoice::pending()->count(),
            'settlement' => Invoice::settlement()->count(),
            'expired'    => Invoice::expired()->count(),
            'pending_approvals' => Invoice::where('settlement_request_status', 'pending')->count(),
        ];

        // --- Data untuk modal-invoice-manual.blade.php ---
        $locations = Location::all();
        $users     = User::where('role', 'customer')->get();

        $rooms = Room::with(['roomType:id,name'])
            ->select('id', 'location_id', 'room_type_id', 'room_number', 'floor', 'capacity', 'size_m2', 'status')
            ->get()
            ->flatMap(function ($room) {
                $base = [
                    'id'          => $room->id,
                    'location_id' => $room->location_id,
                    'name'        => "Ruang {$room->room_number} - Lt.{$room->floor} ({$room->capacity} pax)",
                    'status'      => $room->status,
                    'capacity'    => $room->capacity,
                ];

                // Room multi-purpose (room_type_id = NULL)
                // munculkan untuk Private Office DAN Meeting Room sekaligus
                if ($room->room_type_id === null) {
                    return [
                        array_merge($base, ['type' => 'Private Office']),
                        array_merge($base, ['type' => 'Meeting Room']),
                    ];
                }

                // Room dengan tipe spesifik — 1 entry saja
                return [
                    array_merge($base, ['type' => $room->roomType->name ?? null])
                ];
            })
            ->filter(fn($room) => $room['type'] !== null)
            ->values();

        $serviceCategories = ServiceCategory::all();
        $cities            = City::all();
        $lunchOptions      = LunchOption::all();

        $settlementApprovals = null;
        if (auth()->check() && auth()->user()->role === 'finance') {
            $settlementApprovals = Invoice::whereNotNull('settlement_request_status')
                ->with(['transaction', 'requestedBy', 'processedBy'])
                ->orderByRaw("FIELD(settlement_request_status, 'pending', 'approved', 'rejected')")
                ->latest('updated_at')
                ->paginate(15, ['*'], 'approvals_page')
                ->withQueryString();
        }

        // --- $settlementHistory untuk invoice-history.blade.php ---
        $settlementHistory = null;
        if (auth()->check() && auth()->user()->role === 'admin') {
            $settlementHistory = Invoice::whereNotNull('settlement_request_status')
                ->with(['transaction', 'requestedBy', 'processedBy'])
                ->latest('updated_at')
                ->paginate(15, ['*'], 'history_page')
                ->withQueryString();
        }

        return compact(
            'transactions',
            'invoices',
            'stats',
            'locations',
            'users',
            'rooms',
            'serviceCategories',
            'cities',
            'lunchOptions',
            'settlementApprovals',
            'settlementHistory'
        );
    }

    /**
     * Generate manual order ID.
     * Format: MANUAL-ORDER-YYYYMMDD-XXX
     */
    private function generateManualOrderId(): string
    {
        $date   = date('Ymd');
        $prefix = 'MANUAL-ORDER';

        $lastOrder = Transaction::withoutGlobalScopes()
            ->where('order_id', 'like', $prefix . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastOrder) {
            $parts = explode('-', $lastOrder->order_id);
            $lastSeq = (int)end($parts);
            $nextNumber = $lastSeq + 1;
        }

        return $prefix . '-' . $date . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Format nomor telepon untuk WhatsApp.
     */
    private function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    // =========================================================
    // MAIN VIEW METHODS
    // =========================================================

    public function index(Request $request)
    {
        $viewData = $this->getViewData($request);
        return view('layouts.admin.management-invoice', $viewData);
    }

    public function create(Request $request)
    {
        $viewData = $this->getViewData($request);
        return view('layouts.admin.management-invoice', $viewData);
    }

    public function show(Invoice $invoice, Request $request)
    {
        $invoice->load([
            'transaction.user',
            'transaction.location',
            'transaction.lunches',
            'creator',
            'requestedBy',
            'processedBy',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data'    => $invoice,
            ]);
        }

        $viewData = $this->getViewData($request);

        return view('layouts.admin.management-invoice', array_merge(
            $viewData,
            compact('invoice')
        ));
    }

    // =========================================================
    // STORE METHODS
    // =========================================================

    /**
     * Generate invoice dari transaksi yang dipilih di TAB 2 (manual fallback).
     * Digunakan untuk transaksi lama yang belum punya invoice
     * (sebelum Observer aktif atau backfill dijalankan).
     */
    public function store(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
        ]);

        $transaction = Transaction::findOrFail($request->transaction_id);

        if ($transaction->hasInvoice()) {
            return back()->with('error', 'Transaksi ini sudah memiliki invoice.');
        }

        try {
            DB::beginTransaction();

            $invoiceNumber = Invoice::generateNumber($transaction);

            Invoice::create([
                'invoice_number' => $invoiceNumber,
                'transaction_id' => $transaction->id,
                'status'         => $transaction->status,
                'created_by'     => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('admin.invoices.index')
                ->with('success', 'Invoice berhasil dibuat. Nomor: ' . $invoiceNumber);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat invoice: ' . $e->getMessage());
        }
    }

    /**
     * Simpan invoice manual (buat transaksi baru + invoice sekaligus).
     *
     * PENTING: Menggunakan $transaction->saveQuietly() untuk membuat transaksi
     * TANPA men-trigger TransactionObserver::created(). Ini mencegah double invoice
     * karena invoice dibuat manual di bawah setelah transaksi tersimpan.
     */
    public function storeManual(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id'             => 'nullable|exists:users,id',
                'nama_lengkap'        => 'required|string|max:255',
                'company_name'        => 'nullable|string|max:255',
                'phone'               => 'required|string|max:20',
                'email'               => 'required|email|max:255',
                'nik'                 => 'nullable|string|max:50',
                'city_id'             => 'required|exists:citys,id',
                'location_id'         => 'required|exists:locations,id',
                'room_type'           => 'required|string',
                'room_id'             => 'nullable|exists:rooms,id',
                'booking_date'        => 'required|date',
                'start_time'          => 'nullable',
                'jumlah_orang'        => 'nullable|integer|min:1',
                'paket'               => 'nullable|string',
                'jam'                 => 'nullable|integer|min:1',
                'hari'                => 'nullable|integer|min:1',
                'minggu'              => 'nullable|integer|min:1',
                'bulan'               => 'nullable|integer|min:1',
                'tahun'               => 'nullable|integer|min:1',
                'service_category_id' => 'nullable|exists:service_categories,id',
                'coffee_break'        => 'nullable|string',
                'status_pkp'          => 'nullable|in:PKP,Non PKP',
                'lunch_option_id'     => 'nullable|exists:lunch_options,id',
                'lunch_quantity'      => 'nullable|integer|min:1',
                'gross_amount'        => 'required|numeric|min:0',
                'deposit'             => 'nullable|numeric|min:0',
                'status'              => 'required|in:pending,settlement,expired',
                'notes'               => 'nullable|string',
            ]);

            DB::beginTransaction();

            $orderId = $this->generateManualOrderId();

            // =========================================================
            // FIX: Gunakan new + fill + saveQuietly() untuk BYPASS Observer
            // Transaction::create() akan trigger TransactionObserver::created()
            // yang akan auto-buat invoice — lalu kita buat invoice lagi di bawah
            // = DUPLIKAT. saveQuietly() mencegah semua event model dijalankan.
            // =========================================================
            $transaction = new Transaction();
            $transaction->fill([
                'user_id'             => $request->user_id,
                'city_id'             => $request->city_id,
                'location_id'         => $request->location_id,
                'room_id'             => $request->room_id,
                'room_type'           => $request->room_type,
                'booking_date'        => $request->booking_date,
                'start_time'          => $request->start_time,
                'jumlah_orang'        => $request->jumlah_orang ?? 1,
                'paket'               => $request->paket,
                'jam'                 => $request->jam,
                'hari'                => $request->hari,
                'minggu'              => $request->minggu,
                'bulan'               => $request->bulan,
                'tahun'               => $request->tahun,
                'service_category_id' => $request->service_category_id,
                'coffee_break'        => $request->coffee_break,
                'status_pkp'          => $request->status_pkp,
                'nama_lengkap'        => $request->nama_lengkap,
                'company_name'        => $request->company_name,
                'phone'               => $request->phone,
                'email'               => $request->email,
                'nik'                 => $request->nik,
                'order_id'            => $orderId,
                'gross_amount'        => $request->gross_amount,
                'deposit'             => $request->deposit ?? 0,
                'lunch_total'         => 0,
                'status'              => $request->status,
                'payment_type'        => 'manual',
                'notes'               => $request->notes,
                'created_by'          => auth()->id(),
            ]);

            // saveQuietly() = simpan tanpa trigger Observer (tidak ada auto-invoice)
            $transaction->saveQuietly();

            // Handle lunch option
            if ($request->filled('lunch_option_id') && $request->filled('lunch_quantity')) {
                $lunchOption = LunchOption::find($request->lunch_option_id);
                if ($lunchOption) {
                    $transaction->lunches()->create([
                        'lunch_option_id' => $lunchOption->id,
                        'quantity'        => $request->lunch_quantity,
                    ]);

                    $transaction->updateQuietly([
                        'lunch_total' => $lunchOption->price * $request->lunch_quantity,
                    ]);
                }
            }

            // Buat invoice manual (satu-satunya invoice untuk transaksi ini)
            $invoiceNumber = Invoice::generateNumber($transaction);

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'transaction_id' => $transaction->id,
                'status'         => $transaction->status,
                'created_by'     => auth()->id(),
            ]);

            DB::commit();

            $invoice->load('transaction');

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice manual berhasil dibuat. Nomor: ' . $invoiceNumber,
                    'data'    => $invoice,
                ], 200);
            }

            return redirect()
                ->route('admin.invoices.index')
                ->with('success', 'Invoice manual berhasil dibuat. Nomor: ' . $invoiceNumber);

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors'  => $e->errors(),
                ], 422);
            }
            throw $e;

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Gagal membuat invoice manual: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat invoice: Terjadi kesalahan pada server',
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal membuat invoice. Silakan coba lagi.');
        }
    }

    // =========================================================
    // PDF & WHATSAPP
    // =========================================================

    /**
     * Download invoice sebagai PDF.
     * $invoice di-pass ke view agar pdf.blade.php bisa menampilkan
     * invoice_number langsung tanpa fallback ke order_id.
     */
    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load([
            'transaction.location',
            'transaction.city',
            'transaction.serviceCategory',
            'transaction.room',
            'transaction.lunches.lunchOption',
        ]);

        $transaction = $invoice->transaction;

        // ========== BACKGROUND INVOICE ==========
        $backgroundPath = base_path('public/assets/Invoice Virtual Office Mentahan.png');

        if (!File::exists($backgroundPath)) {
            $backgroundPath = 'D:\laragon\www\webappurban\web-app-urbanoffice\public\assets\Invoice Virtual Office Mentahan.png';
        }

        if (!File::exists($backgroundPath)) {
            $backgroundPath = '/home/K7308095/webappurban/web-app-urbanoffice/public/assets/Invoice Virtual Office Mentahan.png';
        }

        $backgroundImage = null;
        if (File::exists($backgroundPath)) {
            try {
                $imageData       = File::get($backgroundPath);
                $backgroundImage = 'data:image/png;base64,' . base64_encode($imageData);
            } catch (\Exception $e) {
                \Log::error('Failed to load invoice background: ' . $e->getMessage());
            }
        }

        // ========== TTD SIGNATURE ==========
        $ttdPath = base_path('public/assets/TTD PAK MEGA.jpeg');

        if (!File::exists($ttdPath)) {
            $ttdPath = base_path('public/assets/ttd_pak_mega.jpeg');
        }

        if (!File::exists($ttdPath)) {
            $ttdPath = 'D:\laragon\www\webappurban\web-app-urbanoffice\public\assets\TTD PAK MEGA.jpeg';
        }

        if (!File::exists($ttdPath)) {
            $ttdPath = '/home/K7308095/webappurban/web-app-urbanoffice/public/assets/ttd_pak_mega.jpeg';
        }

        $ttdImage = null;
        if (File::exists($ttdPath)) {
            try {
                $imageData = File::get($ttdPath);
                $ttdImage  = 'data:image/jpeg;base64,' . base64_encode($imageData);
            } catch (\Exception $e) {
                \Log::error('Failed to load TTD image: ' . $e->getMessage());
            }
        }

        // ========== GENERATE PDF ==========
        $pdf = Pdf::loadView('layouts.invoices.pdf', [
            'invoice'         => $invoice,        // ← invoice_number tersedia di sini
            'transaction'     => $transaction,
            'backgroundImage' => $backgroundImage,
            'hasBackground'   => !empty($backgroundImage),
            'ttdImage'        => $ttdImage,
            'hasTTD'          => !empty($ttdImage),
        ])->setPaper('A4', 'portrait');

        $filename = 'invoice-' . str_replace(['/', '\\', ' '], '-', $invoice->invoice_number) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Kirim invoice via WhatsApp.
     */
    public function sendWhatsApp(Invoice $invoice)
    {
        $invoice->load('transaction');

        $phone = $invoice->transaction->phone ?? null;

        if (!$phone) {
            return back()->with('error', 'Nomor telepon tidak ditemukan.');
        }

        $phone  = $this->formatPhoneNumber($phone);
        $pdfUrl = route('admin.invoices.pdf', $invoice);

        $message  = "Halo {$invoice->transaction->nama_lengkap},\n\n";
        $message .= "Berikut adalah invoice untuk transaksi Anda:\n";
        $message .= "Nomor Invoice: {$invoice->invoice_number}\n";
        $message .= "Total: Rp " . number_format($invoice->transaction->gross_amount, 0, ',', '.') . "\n";
        $message .= "Status: " . strtoupper($invoice->status) . "\n\n";
        $message .= "Download invoice: {$pdfUrl}\n\n";
        $message .= "Terima kasih.";

        return redirect()->away("https://wa.me/{$phone}?text=" . urlencode($message));
    }

    // =========================================================
    // STATUS MANAGEMENT
    // =========================================================

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate([
            'status' => 'required|in:pending,settlement,expired',
        ]);

        // Kunci invoice non-MANUAL agar tidak bisa diubah manual
        if (!$invoice->isManual()) {
            return back()->with('error', 'Status invoice otomatis non-manual tidak dapat diubah secara manual.');
        }

        // Untuk invoice MANUAL, batasi perubahan status ke settlement untuk non-finance
        if ($request->status === 'settlement') {
            if (auth()->user()->role !== 'finance') {
                return back()->with('error', 'Perubahan status ke Settlement untuk invoice MANUAL harus melalui pengajuan approval ke finance.');
            }
        }

        DB::beginTransaction();
        try {
            $invoice->update(['status' => $request->status]);

            if ($invoice->transaction) {
                $invoice->transaction->update(['status' => $request->status]);
            }

            DB::commit();
            return back()->with('success', 'Status invoice berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal memperbarui status invoice: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui status.');
        }
    }

    /**
     * Mengajukan perubahan status dari pending ke settlement (oleh Admin)
     */
    public function requestSettlement(Request $request, Invoice $invoice)
    {
        if ($invoice->status !== 'pending' || !$invoice->isManual()) {
            return back()->with('error', 'Hanya invoice MANUAL dengan status pending yang dapat diajukan settlement.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
            'payment_proof' => 'nullable|file|image|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
        }

        $invoice->update([
            'settlement_request_status' => 'pending',
            'settlement_requested_by' => auth()->id(),
            'settlement_request_notes' => $request->notes,
            'settlement_payment_proof' => $proofPath,
            'settlement_rejection_reason' => null, // reset penolakan sebelumnya jika ada
        ]);

        return back()->with('success', 'Pengajuan settlement berhasil dikirim ke finance.');
    }

    /**
     * Index halaman approval (diarahkan ke tab approval pada index utama)
     */
    public function approvalIndex(Request $request)
    {
        if (auth()->user()->role !== 'finance') {
            abort(403, 'Forbidden. Finance role required.');
        }

        return redirect()->route('admin.invoices.index', ['tab' => 'approvals']);
    }

    /**
     * Menyetujui pengajuan settlement (oleh Finance)
     */
    public function approveSettlement(Request $request, Invoice $invoice)
    {
        if (auth()->user()->role !== 'finance') {
            return back()->with('error', 'Hanya role finance yang dapat menyetujui pengajuan settlement.');
        }

        if ($invoice->settlement_request_status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        DB::beginTransaction();
        try {
            $invoice->update([
                'status' => 'settlement',
                'settlement_request_status' => 'approved',
                'settlement_processed_by' => auth()->id(),
            ]);

            if ($invoice->transaction) {
                $invoice->transaction->update(['status' => 'settlement']);
            }

            DB::commit();
            return back()->with('success', 'Pengajuan settlement berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to approve settlement: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyetujui pengajuan.');
        }
    }

    /**
     * Menolak pengajuan settlement (oleh Finance)
     */
    public function rejectSettlement(Request $request, Invoice $invoice)
    {
        if (auth()->user()->role !== 'finance') {
            return back()->with('error', 'Hanya role finance yang dapat menolak pengajuan settlement.');
        }

        if ($invoice->settlement_request_status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $invoice->update([
                'settlement_request_status' => 'rejected',
                'settlement_processed_by' => auth()->id(),
                'settlement_rejection_reason' => $request->rejection_reason,
            ]);

            DB::commit();
            return back()->with('success', 'Pengajuan settlement berhasil ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to reject settlement: ' . $e->getMessage());
            return back()->with('error', 'Gagal menolak pengajuan.');
        }
    }

    public function syncStatus(Invoice $invoice)
    {
        $invoice->load('transaction');

        if (!$invoice->transaction) {
            return back()->with('error', 'Transaksi tidak ditemukan.');
        }

        $transactionStatus = $invoice->transaction->status;

        if ($invoice->status === $transactionStatus) {
            return back()->with('info', 'Status invoice sudah sesuai dengan transaksi.');
        }

        $invoice->update(['status' => $transactionStatus]);

        return back()->with('success', 'Status invoice berhasil disinkronkan dengan transaksi.');
    }
}