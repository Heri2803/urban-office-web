<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\User;
use App\Services\BrowserNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuratController extends Controller
{
    protected $browserNotificationService;

    public function __construct(BrowserNotificationService $browserNotificationService)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized. Hanya admin yang dapat mengakses halaman ini.');
            }
            return $next($request);
        });
        $this->browserNotificationService = $browserNotificationService;
    }

    /**
     * Display a listing of all surats (Admin view)
     */
    public function index(Request $request)
    {
        $query = Surat::query()->with(['creator', 'recipients'])->withCount('recipients');

        // Search by nomor surat, perihal, or pengirim
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by status_pengambilan
        if ($request->filled('status_pengambilan')) {
            $query->where('status_pengambilan', $request->status_pengambilan);
        }

        // Filter by date range
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('tanggal_surat', [$request->from_date, $request->to_date]);
        }

        // Filter by recipient
        if ($request->filled('recipient_id')) {
            $query->whereHas('recipients', function ($q) use ($request) {
                $q->where('user_id', $request->recipient_id);
            });
        }

        $surats = $query->latest()->paginate(10)->withQueryString();

        // Get customers for filter dropdown (based on admin location, fallback ke semua customer)
        $customers = $this->getCustomersForAdmin();

        return view('admin.surats.index', compact('surats', 'customers'));
    }

    /**
     * Show form for creating a new surat
     */
    public function create()
    {
        // Get available recipients (prioritas lokasi sama, fallback ke semua customer)
        $customers = $this->getCustomersForAdmin();

        // Generate suggested nomor surat
        $suggestedNumber = $this->generateNomorSurat();

        return view('admin.surats.create', compact('customers', 'suggestedNumber'));
    }

    /**
     * Store a newly created surat
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:100|unique:surats,nomor_surat',
            'tanggal_surat' => 'required|date',
            'tanggal_datang' => 'required|date',
            'perihal' => 'required|string|max:255',
            'pengirim' => 'required|string|max:255',
            'isi_ringkasan' => 'nullable|string',
            'file_surat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'status' => 'required|in:draft,published',
            'recipients' => 'required_if:status,published|array',
            'recipients.*' => 'exists:users,id',
            'status_pengambilan' => 'required|in:belum_diambil,sudah_diambil',
            'tanggal_diambil' => 'required_if:status_pengambilan,sudah_diambil|nullable|date',
            'metode_pengambilan' => 'required_if:status_pengambilan,sudah_diambil|nullable|in:offline,delivery',
            'kurir_pengiriman' => 'required_if:metode_pengambilan,delivery|nullable|string|max:100',
            'resi_pengiriman' => 'required_if:metode_pengambilan,delivery|nullable|string|max:100',
        ], [
            'recipients.required_if' => 'Pilih minimal satu penerima jika status Published.',
            'file_surat.max' => 'Ukuran file maksimal 10MB.',
            'tanggal_diambil.required_if' => 'Tanggal diambil wajib diisi jika status Sudah Diambil.',
            'metode_pengambilan.required_if' => 'Metode pengambilan wajib diisi jika status Sudah Diambil.',
            'kurir_pengiriman.required_if' => 'Nama ekspedisi wajib diisi jika metode pengiriman adalah Delivery.',
            'resi_pengiriman.required_if' => 'Nomor resi wajib diisi jika metode pengiriman adalah Delivery.',
        ]);

        DB::beginTransaction();
        try {
            // Handle file upload
            $filePath = null;
            $fileName = null;
            
            if ($request->hasFile('file_surat')) {
                $file = $request->file('file_surat');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs(
                    'surats/' . date('Y/m'),
                    $fileName,
                    'public'
                );
            }

            // Create surat
            $surat = Surat::create([
                'nomor_surat' => $validated['nomor_surat'],
                'tanggal_surat' => $validated['tanggal_surat'],
                'tanggal_datang' => $validated['tanggal_datang'],
                'perihal' => $validated['perihal'],
                'pengirim' => $validated['pengirim'],
                'isi_ringkasan' => $validated['isi_ringkasan'] ?? null,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'status' => $validated['status'],
                'status_pengambilan' => $validated['status_pengambilan'],
                'tanggal_diambil' => $validated['status_pengambilan'] === 'sudah_diambil' ? $validated['tanggal_diambil'] : null,
                'metode_pengambilan' => $validated['status_pengambilan'] === 'sudah_diambil' ? $validated['metode_pengambilan'] : null,
                'kurir_pengiriman' => ($validated['status_pengambilan'] === 'sudah_diambil' && $validated['metode_pengambilan'] === 'delivery') ? $validated['kurir_pengiriman'] : null,
                'resi_pengiriman' => ($validated['status_pengambilan'] === 'sudah_diambil' && $validated['metode_pengambilan'] === 'delivery') ? $validated['resi_pengiriman'] : null,
                'created_by' => auth()->id(),
            ]);

            // Attach recipients & send notifications (only if published)
            if ($validated['status'] === 'published' && !empty($validated['recipients'])) {
                // Attach recipients ke surat
                $surat->recipients()->attach($validated['recipients'], [
                    'notified_at' => now(),
                ]);

                // 🔔 TRIGGER BROWSER NOTIFICATION
                try {
                    $this->browserNotificationService->sendToMultipleUsers(
                        $validated['recipients'],
                        $surat
                    );

                    // Jika status_pengambilan adalah sudah_diambil, kirimkan juga notifikasi pengambilan!
                    if ($validated['status_pengambilan'] === 'sudah_diambil') {
                        $this->browserNotificationService->sendPickupNotificationToMultipleUsers(
                            $validated['recipients'],
                            $surat
                        );
                    }
                } catch (\Exception $e) {
                    // Log error tapi jangan gagalkan penyimpanan surat
                    \Log::error('Failed to send browser notifications: ' . $e->getMessage());
                }
            }

            DB::commit();

            $message = $validated['status'] === 'published' 
                ? 'Surat berhasil dipublikasikan dan notifikasi telah dikirim!'
                : 'Surat berhasil disimpan sebagai draft.';

            return redirect()
                ->route('admin.surats.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded file if error
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            \Log::error('Error creating surat: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan surat. Silakan coba lagi.');
        }
    }

    /**
     * Display the specified surat (Admin detail view)
     */
    public function show(Surat $surat)
    {
        $surat->load(['creator', 'recipients' => function ($query) {
            $query->withPivot('is_read', 'read_at', 'notified_at');
        }]);

        // Read statistics
        $totalRecipients = $surat->recipients->count();
        $readCount = $surat->recipients->where('pivot.is_read', true)->count();
        $unreadCount = $totalRecipients - $readCount;
        $readPercentage = $totalRecipients > 0 ? round(($readCount / $totalRecipients) * 100) : 0;

        return view('admin.surats.show', compact(
            'surat',
            'totalRecipients',
            'readCount',
            'unreadCount',
            'readPercentage'
        ));
    }

    /**
     * Show form for editing the specified surat
     */
    public function edit(Surat $surat)
    {
        // Get customers for recipient selection (prioritas lokasi sama, fallback ke semua)
        $customers = $this->getCustomersForAdmin();

        // Get currently selected recipients
        $selectedRecipients = $surat->recipients()->pluck('users.id')->toArray();

        return view('admin.surats.edit', compact('surat', 'customers', 'selectedRecipients'));
    }

    /**
     * Update the specified surat
     */
    public function update(Request $request, Surat $surat)
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:100|unique:surats,nomor_surat,' . $surat->id,
            'tanggal_surat' => 'required|date',
            'tanggal_datang' => 'required|date',
            'perihal' => 'required|string|max:255',
            'pengirim' => 'required|string|max:255',
            'isi_ringkasan' => 'nullable|string',
            'file_surat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'status' => 'required|in:draft,published',
            'recipients' => 'required_if:status,published|array',
            'recipients.*' => 'exists:users,id',
            'status_pengambilan' => 'required|in:belum_diambil,sudah_diambil',
            'tanggal_diambil' => 'required_if:status_pengambilan,sudah_diambil|nullable|date',
            'metode_pengambilan' => 'required_if:status_pengambilan,sudah_diambil|nullable|in:offline,delivery',
            'kurir_pengiriman' => 'required_if:metode_pengambilan,delivery|nullable|string|max:100',
            'resi_pengiriman' => 'required_if:metode_pengambilan,delivery|nullable|string|max:100',
            'remove_file' => 'nullable|boolean',
        ], [
            'recipients.required_if' => 'Pilih minimal satu penerima jika status Published.',
            'file_surat.max' => 'Ukuran file maksimal 10MB.',
            'tanggal_diambil.required_if' => 'Tanggal diambil wajib diisi jika status Sudah Diambil.',
            'metode_pengambilan.required_if' => 'Metode pengambilan wajib diisi jika status Sudah Diambil.',
            'kurir_pengiriman.required_if' => 'Nama ekspedisi wajib diisi jika metode pengiriman adalah Delivery.',
            'resi_pengiriman.required_if' => 'Nomor resi wajib diisi jika metode pengiriman adalah Delivery.',
        ]);

        $oldStatusPengambilan = $surat->status_pengambilan;

        DB::beginTransaction();
        try {
            // Handle file removal
            if ($request->boolean('remove_file') && $surat->file_path) {
                Storage::disk('public')->delete($surat->file_path);
                $surat->file_path = null;
                $surat->file_name = null;
            }

            // Handle new file upload
            if ($request->hasFile('file_surat')) {
                // Delete old file
                if ($surat->file_path) {
                    Storage::disk('public')->delete($surat->file_path);
                }

                $file = $request->file('file_surat');
                $surat->file_name = time() . '_' . $file->getClientOriginalName();
                $surat->file_path = $file->storeAs(
                    'surats/' . date('Y/m'),
                    $surat->file_name,
                    'public'
                );
            }

            // Update surat data
            $surat->update([
                'nomor_surat' => $validated['nomor_surat'],
                'tanggal_surat' => $validated['tanggal_surat'],
                'tanggal_datang' => $validated['tanggal_datang'],
                'perihal' => $validated['perihal'],
                'pengirim' => $validated['pengirim'],
                'isi_ringkasan' => $validated['isi_ringkasan'] ?? $surat->isi_ringkasan,
                'status' => $validated['status'],
                'status_pengambilan' => $validated['status_pengambilan'],
                'tanggal_diambil' => $validated['status_pengambilan'] === 'sudah_diambil' ? $validated['tanggal_diambil'] : null,
                'metode_pengambilan' => $validated['status_pengambilan'] === 'sudah_diambil' ? $validated['metode_pengambilan'] : null,
                'kurir_pengiriman' => ($validated['status_pengambilan'] === 'sudah_diambil' && $validated['metode_pengambilan'] === 'delivery') ? $validated['kurir_pengiriman'] : null,
                'resi_pengiriman' => ($validated['status_pengambilan'] === 'sudah_diambil' && $validated['metode_pengambilan'] === 'delivery') ? $validated['resi_pengiriman'] : null,
                'file_path' => $surat->file_path,
                'file_name' => $surat->file_name,
            ]);

            // Handle recipients
            if ($validated['status'] === 'published') {
                $oldRecipients = $surat->recipients()->pluck('users.id')->toArray();
                $newRecipients = $validated['recipients'] ?? [];

                // Sync recipients
                $surat->recipients()->sync($newRecipients);

                // Cari user yang baru ditambahkan
                $newlyAdded = array_diff($newRecipients, $oldRecipients);

                // 🔔 TRIGGER BROWSER NOTIFICATION hanya untuk user baru
                if (!empty($newlyAdded)) {
                    // Update notified_at untuk user baru
                    $surat->recipients()->updateExistingPivot($newlyAdded, [
                        'notified_at' => now(),
                    ]);

                    try {
                        $this->browserNotificationService->sendToMultipleUsers(
                            $newlyAdded,
                            $surat
                        );
                    } catch (\Exception $e) {
                        \Log::error('Failed to send browser notifications: ' . $e->getMessage());
                    }
                }

                // Jika status_pengambilan diubah menjadi sudah_diambil, kirim push notification ke semua recipients!
                if ($validated['status_pengambilan'] === 'sudah_diambil' && $oldStatusPengambilan !== 'sudah_diambil') {
                    try {
                        $recipientIds = $surat->recipients()->pluck('users.id')->toArray();
                        if (!empty($recipientIds)) {
                            $this->browserNotificationService->sendPickupNotificationToMultipleUsers(
                                $recipientIds,
                                $surat
                            );
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to send pickup notifications: ' . $e->getMessage());
                    }
                }
            } else {
                // Jika status draft, hapus semua recipients
                $surat->recipients()->detach();
            }

            DB::commit();

            $message = $validated['status'] === 'published'
                ? 'Surat berhasil diupdate dan dipublikasikan!'
                : 'Surat berhasil disimpan sebagai draft.';

            return redirect()
                ->route('admin.surats.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error updating surat: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Gagal mengupdate surat. Silakan coba lagi.');
        }
    }

    /**
     * Remove the specified surat (Soft Delete)
     */
    public function destroy(Surat $surat)
    {
        try {
            $surat->delete(); // Soft delete

            return redirect()
                ->route('admin.surats.index')
                ->with('success', 'Surat berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Error deleting surat: ' . $e->getMessage());

            return back()->with('error', 'Gagal menghapus surat.');
        }
    }

    /**
     * Download surat file
     */
    public function download(Surat $surat)
    {
        if (!$surat->file_path || !Storage::disk('public')->exists($surat->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($surat->file_path, $surat->file_name);
    }

    /**
     * Get recipients status (for AJAX/tracking)
     */
    public function recipients(Surat $surat)
    {
        $recipients = $surat->recipients()
            ->withPivot('is_read', 'read_at', 'notified_at')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_read' => $user->pivot->is_read,
                    'read_at' => $user->pivot->read_at?->diffForHumans(),
                    'notified_at' => $user->pivot->notified_at?->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'recipients' => $recipients,
        ]);
    }

    /**
     * Resend notification to a specific user
     */
    public function resendNotification(Surat $surat, User $user)
    {
        try {
            // Update notified_at dan reset status baca agar user mendapat notifikasi lagi
            $surat->recipients()->updateExistingPivot($user->id, [
                'notified_at' => now(),
                'is_read' => false,
            ]);

            // Kirim ulang notifikasi
            $this->browserNotificationService->sendToUser($user, $surat);

            return back()->with('success', "Notifikasi berhasil dikirim ulang ke {$user->name}.");
        } catch (\Exception $e) {
            \Log::error('Error resending notification: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim ulang notifikasi.');
        }
    }

    /**
     * Get customers untuk admin - menampilkan semua customer & mitra (ordered by name)
     */
    private function getCustomersForAdmin()
    {
        return User::whereIn('role', ['customer', 'mitra'])
                   ->orderBy('name')
                   ->get();
    }

    /**
     * Generate suggested nomor surat — ambil nomor urut terbesar dari SEMUA surat
     * agar tidak restart dari 001 setiap bulan.
     */
    private function generateNomorSurat()
    {
        $month = date('m');
        $year  = date('Y');

        // Ambil surat dengan prefix angka terbesar (format: 001/SM-ADM/MM/YYYY)
        $lastSurat = Surat::selectRaw("nomor_surat, CAST(SUBSTRING_INDEX(nomor_surat, '/', 1) AS UNSIGNED) as seq")
                          ->orderByRaw("CAST(SUBSTRING_INDEX(nomor_surat, '/', 1) AS UNSIGNED) DESC")
                          ->first();

        if ($lastSurat) {
            $lastSeq    = (int) explode('/', $lastSurat->nomor_surat)[0];
            $nextNumber = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        return "{$nextNumber}/SM-ADM/{$month}/{$year}";
    }
}