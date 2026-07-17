<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuratController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List surat untuk customer yang login
     */
    public function index(Request $request)
    {
        $query = auth()->user()->receivedSurats();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%");
            });
        }

        // Filter read status
        if ($request->filled('read_status')) {
            if ($request->read_status === 'unread') {
                $query->wherePivot('is_read', false);
            } elseif ($request->read_status === 'read') {
                $query->wherePivot('is_read', true);
            }
        }

        // Filter by date
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('tanggal_surat', [$request->from_date, $request->to_date]);
        }

        $surats = $query->orderBy('surat_user.created_at', 'desc')->paginate(10);

        return view('customer.surats.index', compact('surats'));
    }

    /**
     * Detail surat + auto mark as read
     */
    public function show(Surat $surat)
    {
        // Pastikan user adalah penerima
        if (!$surat->recipients()->where('user_id', auth()->id())->exists()) {
            abort(403, 'Anda tidak memiliki akses ke surat ini.');
        }

        // Auto mark as read
        if (!$surat->isReadByUser(auth()->id())) {
            $surat->markAsReadByUser(auth()->id());
        }

        $surat->load('creator');

        return view('customer.surats.show', compact('surat'));
    }

    /**
     * Download file surat
     */
    public function download(Surat $surat)
    {
        if (!$surat->recipients()->where('user_id', auth()->id())->exists()) {
            abort(403);
        }

        if (!$surat->file_path || !Storage::disk('public')->exists($surat->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($surat->file_path, $surat->file_name);
    }

    /**
     * Mark surat as read (AJAX)
     */
    public function markAsRead(Surat $surat)
    {
        if (!$surat->isReadByUser(auth()->id())) {
            $surat->markAsReadByUser(auth()->id());
        }

        return response()->json(['success' => true]);
    }
}