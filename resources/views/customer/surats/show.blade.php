@extends('layouts.app')

@section('title', 'Detail Surat')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-orange-50/30 to-amber-50/20 py-6 px-4">
    <div class="max-w-3xl mx-auto space-y-5">

        {{-- ===== BACK BUTTON ===== --}}
        <a href="{{ route('dashboard.surats.index') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-orange-600 transition-colors group">
            <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Kotak Surat
        </a>

        {{-- ===== HEADER CARD ===== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-amber-50 p-6 relative overflow-hidden border-b border-amber-100">
                {{-- Decorative circles --}}
                <div class="absolute -top-8 -right-8 w-40 h-40 bg-amber-100/50 rounded-full"></div>
                <div class="absolute -bottom-12 -left-4 w-32 h-32 bg-amber-100/50 rounded-full"></div>

                <div class="relative z-10 flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-[10px] font-bold tracking-widest text-amber-800 uppercase">📬 Surat Masuk</span>
                            <span class="px-2 py-0.5 bg-amber-500 text-white text-[10px] font-semibold rounded-full shadow-sm">✅ Telah Dibaca</span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-bold text-amber-900 leading-snug">{{ $surat->perihal }}</h1>
                        <p class="text-amber-700 text-sm mt-2 font-mono font-medium">{{ $surat->nomor_surat }}</p>
                    </div>
                </div>
            </div>

            {{-- ===== META INFO GRID ===== --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-0 divide-y sm:divide-y-0 sm:divide-x divide-gray-100 border-b border-gray-100">
                <div class="p-4">
                    <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Pengirim</div>
                    <div class="text-sm font-semibold text-gray-800">{{ $surat->pengirim }}</div>
                </div>
                <div class="p-4">
                    <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Tanggal Surat</div>
                    <div class="text-sm font-semibold text-gray-800">{{ $surat->tanggal_surat->format('d M Y') }}</div>
                </div>
                <div class="p-4">
                    <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Tiba di Kantor</div>
                    <div class="text-sm font-semibold text-gray-800">
                        {{ $surat->tanggal_datang ? $surat->tanggal_datang->format('d M Y') : '-' }}
                    </div>
                </div>
                <div class="p-4 sm:border-t sm:border-gray-100">
                    <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Diinput Oleh</div>
                    <div class="text-sm font-semibold text-gray-800">{{ $surat->creator?->name ?? 'Admin Urban Office' }}</div>
                </div>
                <div class="p-4 sm:border-t sm:border-gray-100">
                    <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Notifikasi Terkirim</div>
                    <div class="text-sm font-semibold text-gray-800">{{ $surat->created_at->format('d M Y') }}</div>
                </div>
                <div class="p-4 sm:border-t sm:border-gray-100">
                    <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Status Baca</div>
                    <div>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-[11px] font-bold rounded-full">
                            ✓ Sudah Dibaca
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== STATUS PENGAMBILAN CARD ===== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center
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
                <h2 class="text-sm font-bold text-gray-800">Pengambilan Fisik Surat</h2>
            </div>

            <div class="p-5">
                @if($surat->status_pengambilan === 'sudah_diambil')
                    {{-- SUDAH DIAMBIL --}}
                    <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl mb-4">
                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-emerald-800">Surat Telah Diserahkan</div>
                            <div class="text-xs text-emerald-600 mt-0.5">
                                @if($surat->tanggal_diambil)
                                    pada {{ $surat->tanggal_diambil->format('d M Y') }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Metode Penyerahan</div>
                            @if($surat->metode_pengambilan === 'delivery')
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-bold text-orange-700">Delivery (Dikirim)</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-bold text-green-700">Diambil di Kantor</span>
                                </div>
                            @endif
                        </div>

                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Tanggal Penyerahan</div>
                            <div class="text-sm font-bold text-gray-800">
                                {{ $surat->tanggal_diambil ? $surat->tanggal_diambil->format('d M Y') : '-' }}
                            </div>
                        </div>
                    </div>

                    @if($surat->metode_pengambilan === 'delivery')
                    <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-xl">
                        <div class="text-[10px] font-bold text-orange-500 uppercase tracking-wider mb-3">Informasi Pengiriman</div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-orange-600 mb-1">Nama Ekspedisi</div>
                                <div class="text-sm font-bold text-orange-900">{{ $surat->kurir_pengiriman }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-orange-600 mb-1">Nomor Resi</div>
                                <div class="font-mono text-sm font-bold text-orange-900 break-all">{{ $surat->resi_pengiriman }}</div>
                            </div>
                        </div>
                    </div>
                    @endif

                @else
                    {{-- BELUM DIAMBIL --}}
                    <div class="flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                        <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-amber-800">Surat Masih di Urban Office</div>
                            <div class="text-xs text-amber-600 mt-0.5">Silakan hubungi admin untuk informasi lebih lanjut</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ===== ISI SURAT ===== --}}
        @if($surat->isi_ringkasan)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Isi / Ringkasan Surat
            </h2>
            <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap bg-gray-50 p-4 rounded-xl border border-gray-100">{{ $surat->isi_ringkasan }}</div>
        </div>
        @endif

        {{-- ===== FILE LAMPIRAN ===== --}}
        @if($surat->file_path)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
                File Lampiran
            </h2>
            <a href="{{ route('dashboard.surats.download', $surat) }}"
               class="group flex items-center gap-4 p-4 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 transition-all">
                <div class="w-12 h-12 bg-amber-100 group-hover:bg-amber-200 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-bold text-amber-800 truncate">{{ $surat->file_name }}</div>
                    <div class="text-xs text-amber-500 mt-0.5">Klik untuk mengunduh lampiran surat</div>
                </div>
                <svg class="w-5 h-5 text-amber-400 group-hover:translate-x-1 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </a>
        </div>
        @endif

    </div>
</div>
@endsection

