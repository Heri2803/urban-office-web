@extends('layouts.admin')

@section('title', 'Detail Surat - ' . $surat->nomor_surat)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- ===== BREADCRUMB ===== --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.surats.index') }}" class="hover:text-orange-600 transition-colors font-medium">Surat Masuk</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-800 font-semibold">Detail Surat</span>
    </div>

    {{-- ===== ALERTS ===== --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
        <svg class="w-5 h-5 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    {{-- ===== TOP HEADER CARD ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-orange-50 border-b border-orange-200 p-6 relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-48 h-48 bg-orange-200/40 rounded-full pointer-events-none"></div>
            <div class="absolute -bottom-16 -left-6 w-40 h-40 bg-orange-200/40 rounded-full pointer-events-none"></div>

            <div class="relative flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-3 flex-wrap">
                        @if($surat->status === 'published')
                        <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-700 text-[11px] font-bold rounded-full border border-emerald-200">✅ Published</span>
                        @else
                        <span class="px-2.5 py-0.5 bg-amber-100 text-amber-700 text-[11px] font-bold rounded-full border border-amber-200">📝 Draft</span>
                        @endif
                        <span class="text-[10px] sm:text-xs md:text-sm lg:text-base text-orange-600 font-mono">{{ $surat->nomor_surat }}</span>
                    </div>
                    <h1 class="text-xl font-bold leading-snug text-orange-900">{{ $surat->perihal }}</h1>
                    <p class="text-orange-700 text-sm mt-1.5">dari <span class="font-semibold">{{ $surat->pengirim }}</span></p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <a href="{{ route('admin.surats.edit', $surat) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-orange-700 text-sm font-bold rounded-xl hover:bg-orange-50 transition-all shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    @if($surat->file_path)
                    <a href="{{ route('admin.surats.download', $surat) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-orange-700 text-sm font-bold rounded-xl hover:bg-orange-50 transition-all shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- META INFO GRID --}}
        <div class="grid grid-cols-2 sm:grid-cols-5 divide-y sm:divide-y-0 sm:divide-x divide-gray-100">
            <div class="p-4">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Pengirim</div>
                <div class="text-sm font-semibold text-gray-800">{{ $surat->pengirim }}</div>
            </div>
            <div class="p-4">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Tanggal Surat</div>
                <div class="text-sm font-semibold text-gray-800">{{ $surat->tanggal_surat->format('d M Y') }}</div>
            </div>
            <div class="p-4">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Tiba di Kantor</div>
                <div class="text-sm font-semibold text-gray-800">{{ $surat->tanggal_datang ? $surat->tanggal_datang->format('d M Y') : '-' }}</div>
            </div>
            <div class="p-4">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Dibuat Oleh</div>
                <div class="text-sm font-semibold text-gray-800">{{ $surat->creator?->name ?? 'Admin' }}</div>
            </div>
            <div class="p-4">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Tanggal Input</div>
                <div class="text-sm font-semibold text-gray-800">{{ $surat->created_at->format('d M Y') }}</div>
            </div>
        </div>
    </div>

    {{-- ===== READING STATS + STATUS PENGAMBILAN SIDE BY SIDE ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- STATUS PENGAMBILAN --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center
                            {{ $surat->status_pengambilan === 'sudah_diambil' ? 'bg-emerald-100' : 'bg-amber-100' }}">
                    @if($surat->status_pengambilan === 'sudah_diambil')
                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    @else
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @endif
                </div>
                <h2 class="text-sm font-bold text-gray-800">Status Pengambilan Fisik</h2>
            </div>
            <div class="p-5 space-y-4">

                {{-- Status badge --}}
                <div>
                    @if($surat->status_pengambilan === 'sudah_diambil')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-xl">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                            Sudah Diambil / Dikirim
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 text-amber-800 text-xs font-bold rounded-xl">
                            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                            Belum Diambil
                        </span>
                    @endif
                </div>

                @if($surat->status_pengambilan === 'sudah_diambil')
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tanggal Diambil</div>
                        <div class="text-sm font-bold text-gray-800">
                            {{ $surat->tanggal_diambil ? $surat->tanggal_diambil->format('d M Y') : '-' }}
                        </div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Metode</div>
                        <div class="text-sm font-bold {{ $surat->metode_pengambilan === 'delivery' ? 'text-orange-700' : 'text-emerald-700' }}">
                            {{ $surat->metode_pengambilan === 'delivery' ? 'Delivery' : 'Offline (Kantor)' }}
                        </div>
                    </div>
                </div>

                @if($surat->metode_pengambilan === 'delivery')
                <div class="p-4 bg-orange-50 border border-orange-200 rounded-xl space-y-2">
                    <div class="text-[10px] font-bold text-orange-500 uppercase tracking-wider">Informasi Pengiriman</div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-orange-600">Ekspedisi</span>
                        <span class="text-sm font-bold text-orange-900">{{ $surat->kurir_pengiriman }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-orange-600">No. Resi</span>
                        <span class="font-mono text-sm font-bold text-orange-900">{{ $surat->resi_pengiriman }}</span>
                    </div>
                </div>
                @endif
                @else
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <p class="text-xs text-amber-700">Surat fisik belum diambil atau dikirimkan. Update status saat surat diserahkan kepada customer.</p>
                    <a href="{{ route('admin.surats.edit', $surat) }}" class="inline-flex items-center gap-1 mt-2 text-xs font-bold text-amber-800 hover:text-amber-900 transition-colors">
                        Update Status
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- READING STATS --}}
        @if($surat->status === 'published')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
                <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-gray-800">Statistik Pembacaan</h2>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-3 gap-3">
                    <div class="text-center p-3 bg-orange-50 rounded-xl">
                        <div class="text-xl font-black text-orange-600">{{ $totalRecipients }}</div>
                        <div class="text-[10px] text-gray-500 font-medium mt-0.5">Total Penerima</div>
                    </div>
                    <div class="text-center p-3 bg-emerald-50 rounded-xl">
                        <div class="text-xl font-black text-emerald-600">{{ $readCount }}</div>
                        <div class="text-[10px] text-gray-500 font-medium mt-0.5">Sudah Baca</div>
                    </div>
                    <div class="text-center p-3 bg-red-50 rounded-xl">
                        <div class="text-xl font-black text-red-500">{{ $unreadCount }}</div>
                        <div class="text-[10px] text-gray-500 font-medium mt-0.5">Belum Baca</div>
                    </div>
                </div>

                {{-- Progress bar --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-600">Tingkat Pembacaan</span>
                        <span class="text-sm font-black text-orange-600">{{ $readPercentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5">
                        <div class="bg-gradient-to-r from-orange-500 to-amber-600 h-2.5 rounded-full transition-all duration-1000"
                             style="width: {{ $readPercentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center p-8">
            <div class="text-center">
                <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-700">Surat masih Draft</p>
                <p class="text-xs text-gray-400 mt-1">Publish surat untuk melihat statistik pembacaan</p>
            </div>
        </div>
        @endif
    </div>

    {{-- ===== ISI RINGKASAN ===== --}}
    @if($surat->isi_ringkasan)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Ringkasan Isi Surat
        </h2>
        <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap bg-gray-50 p-4 rounded-xl border border-gray-100">{{ $surat->isi_ringkasan }}</div>
    </div>
    @endif

    {{-- ===== RECIPIENTS TABLE ===== --}}
    @if($surat->status === 'published' && $surat->recipients->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Daftar Penerima
            </h2>
            <span class="text-xs text-gray-500 font-medium">{{ $surat->recipients->count() }} orang</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-5 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Status Baca</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Waktu Baca</th>
                        <th class="px-5 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($surat->recipients as $recipient)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3.5 font-semibold text-gray-900">{{ $recipient->name }}</td>
                        <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $recipient->email }}</td>
                        <td class="px-5 py-3.5 text-center">
                            @if($recipient->pivot->is_read)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 text-emerald-800 text-[11px] font-bold rounded-lg">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Sudah Baca
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 text-gray-600 text-[11px] font-bold rounded-lg">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Belum Baca
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-gray-400 text-xs">
                            {{ $recipient->pivot->read_at ? \Carbon\Carbon::parse($recipient->pivot->read_at)->format('d M Y, H:i') : '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <form method="POST" action="{{ route('admin.surats.resend', [$surat, $recipient]) }}">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-[11px] font-bold bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    Kirim Ulang
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection

