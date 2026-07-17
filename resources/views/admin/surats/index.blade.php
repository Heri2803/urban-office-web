@extends('layouts.admin')

@section('title', 'Manajemen Surat Masuk')

@section('content')
<div class="space-y-6">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <span class="w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-4.5 h-4.5 text-white w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </span>
                Manajemen Surat Masuk
            </h1>
            <p class="text-sm text-gray-500 mt-1 ml-12">Kelola, lacak, dan kirimkan surat masuk kepada customer</p>
        </div>
        <a href="{{ route('admin.surats.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Surat
        </a>
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
    @if(session('error'))
    <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
        <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif

    {{-- ===== STATS CARDS ===== --}}
    <div class="grid grid-cols-4 gap-2 sm:gap-4">

        {{-- Total Surat --}}
        <div class="bg-white rounded-xl sm:rounded-2xl border border-gray-100 shadow-sm p-3 sm:p-5">
            <div class="flex items-center gap-2 sm:block">
                <div class="w-8 h-8 sm:w-9 sm:h-9 bg-blue-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0 sm:mb-3">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-lg sm:text-2xl font-black text-blue-600 leading-tight">{{ $surats->total() }}</div>
                    <div class="text-[10px] sm:text-xs text-gray-500 font-medium leading-tight mt-0.5">Total Surat</div>
                </div>
            </div>
        </div>

        {{-- Published --}}
        <div class="bg-white rounded-xl sm:rounded-2xl border border-gray-100 shadow-sm p-3 sm:p-5">
            <div class="flex items-center gap-2 sm:block">
                <div class="w-8 h-8 sm:w-9 sm:h-9 bg-emerald-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0 sm:mb-3">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-lg sm:text-2xl font-black text-emerald-600 leading-tight">{{ \App\Models\Surat::where('status','published')->count() }}</div>
                    <div class="text-[10px] sm:text-xs text-gray-500 font-medium leading-tight mt-0.5">Published</div>
                </div>
            </div>
        </div>

        {{-- Draft --}}
        <div class="bg-white rounded-xl sm:rounded-2xl border border-gray-100 shadow-sm p-3 sm:p-5">
            <div class="flex items-center gap-2 sm:block">
                <div class="w-8 h-8 sm:w-9 sm:h-9 bg-amber-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0 sm:mb-3">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-lg sm:text-2xl font-black text-amber-600 leading-tight">{{ \App\Models\Surat::where('status','draft')->count() }}</div>
                    <div class="text-[10px] sm:text-xs text-gray-500 font-medium leading-tight mt-0.5">Draft</div>
                </div>
            </div>
        </div>

        {{-- Ada Lampiran --}}
        <div class="bg-white rounded-xl sm:rounded-2xl border border-gray-100 shadow-sm p-3 sm:p-5">
            <div class="flex items-center gap-2 sm:block">
                <div class="w-8 h-8 sm:w-9 sm:h-9 bg-violet-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0 sm:mb-3">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-lg sm:text-2xl font-black text-violet-600 leading-tight">{{ $surats->whereNotNull('file_path')->count() }}</div>
                    <div class="text-[10px] sm:text-xs text-gray-500 font-medium leading-tight mt-0.5">Lampiran</div>
                </div>
            </div>
        </div>

    </div>

    {{-- ===== FILTER & SEARCH ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <form method="GET" action="{{ route('admin.surats.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Cari Surat</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Nomor, perihal, pengirim..."
                               class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status Publish</label>
                    <select name="status" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white text-gray-700">
                        <option value="">Semua</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status Pengambilan</label>
                    <select name="status_pengambilan" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white text-gray-700">
                        <option value="">Semua</option>
                        <option value="belum_diambil" {{ request('status_pengambilan') === 'belum_diambil' ? 'selected' : '' }}>Belum Diambil</option>
                        <option value="sudah_diambil" {{ request('status_pengambilan') === 'sudah_diambil' ? 'selected' : '' }}>Sudah Diambil</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Dari Tanggal</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}"
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}"
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>
            <div class="flex gap-2 mt-3">
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-sm hover:shadow-md">
                    Filter
                </button>
                @if(request()->hasAny(['search','status','status_pengambilan','from_date','to_date']))
                <a href="{{ route('admin.surats.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-200 transition-colors flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset Filter
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ===== TABLE ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Surat</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Pengirim</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Status Pengambilan</th>
                        <th class="px-5 py-3.5 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Penerima</th>
                        <th class="px-5 py-3.5 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Publish</th>
                        <th class="px-5 py-3.5 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($surats as $surat)
                    <tr class="hover:bg-blue-50/30 transition-colors group">

                        {{-- SURAT INFO --}}
                        <td class="px-5 py-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 text-sm leading-snug max-w-[220px] truncate">{{ $surat->perihal }}</div>
                                    <div class="text-[11px] font-mono text-gray-400 mt-0.5">{{ $surat->nomor_surat }}</div>
                                    @if($surat->file_path)
                                    @php
                                        $fileUrl  = asset('storage/' . $surat->file_path);
                                        $isPdf    = str_ends_with(strtolower($surat->file_path), '.pdf');
                                    @endphp
                                    <button type="button"
                                            onclick="openLampiranModal('{{ $fileUrl }}', '{{ $surat->file_name }}', {{ $isPdf ? 'true' : 'false' }})"
                                            class="inline-flex items-center gap-1 mt-1 text-[10px] font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-1.5 py-0.5 rounded transition-colors cursor-pointer">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        Lampiran
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- PENGIRIM --}}
                        <td class="px-5 py-4">
                            <div class="text-sm text-gray-700 font-medium">{{ $surat->pengirim }}</div>
                        </td>

                        {{-- TANGGAL --}}
                        <td class="px-5 py-4">
                            <div class="text-sm font-semibold text-gray-800">{{ $surat->tanggal_surat->format('d M Y') }}</div>
                            <div class="flex items-center gap-1 mt-1">
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-[11px] text-gray-400">Datang: {{ $surat->tanggal_datang ? $surat->tanggal_datang->format('d M Y') : '-' }}</span>
                            </div>
                        </td>

                        {{-- STATUS PENGAMBILAN --}}
                        <td class="px-5 py-4">
                            @if($surat->status_pengambilan === 'sudah_diambil')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 text-emerald-800 text-[11px] font-bold rounded-lg">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Sudah Diambil
                                </span>
                                @if($surat->metode_pengambilan === 'delivery')
                                    <div class="mt-1.5 text-[11px] text-gray-600 space-y-0.5">
                                        <div class="flex items-center gap-1">
                                            <span class="font-semibold text-blue-600">Delivery:</span>
                                            <span>{{ $surat->kurir_pengiriman }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <span class="text-gray-400">Resi:</span>
                                            <span class="font-mono font-medium">{{ $surat->resi_pengiriman }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-1 text-[11px] text-gray-500 font-medium">Offline (Kantor)</div>
                                @endif
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-100 text-amber-800 text-[11px] font-bold rounded-lg">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Belum Diambil
                                </span>
                            @endif
                        </td>

                        {{-- PENERIMA --}}
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-xl text-xs font-black">
                                {{ $surat->recipients_count }}
                            </span>
                        </td>

                        {{-- PUBLISH STATUS --}}
                        <td class="px-5 py-4 text-center">
                            @if($surat->status === 'published')
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-[11px] font-bold rounded-lg">Published</span>
                            @else
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-800 text-[11px] font-bold rounded-lg">Draft</span>
                            @endif
                        </td>

                        {{-- ACTIONS --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.surats.show', $surat) }}"
                                   title="Detail"
                                   class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.surats.edit', $surat) }}"
                                   title="Edit"
                                   class="p-2 text-amber-600 hover:bg-amber-100 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.surats.destroy', $surat) }}"
                                      onsubmit="return confirm('Hapus surat ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Hapus"
                                            class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center">
                                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                </div>
                                <p class="text-gray-600 font-semibold text-sm">Belum ada surat masuk</p>
                                <a href="{{ route('admin.surats.create') }}" class="text-blue-600 text-sm font-semibold hover:underline">Buat surat pertama &rarr;</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($surats->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $surats->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>

{{-- ===== MODAL PREVIEW LAMPIRAN ===== --}}
<div id="lampiranModal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     onclick="closeLampiranModal(event)">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    {{-- Modal Box --}}
    <div class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden"
         onclick="event.stopPropagation()">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-800">Preview Lampiran</div>
                    <div id="modalFileName" class="text-xs text-gray-400 truncate max-w-[280px]"></div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a id="modalDownloadBtn" href="#" download
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download
                </a>
                <button onclick="closeLampiranModal()" type="button"
                        class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Content Area --}}
        <div class="flex-1 overflow-auto bg-gray-50 flex items-center justify-center min-h-[300px] p-4">

            {{-- Image Preview --}}
            <div id="modalImageContainer" class="hidden w-full flex items-center justify-center">
                <img id="modalImage" src="" alt="Lampiran"
                     class="max-w-full max-h-[65vh] object-contain rounded-lg shadow-md">
            </div>

            {{-- PDF Preview --}}
            <div id="modalPdfContainer" class="hidden w-full h-full">
                <iframe id="modalPdf" src="" class="w-full rounded-lg" style="height: 65vh; border: none;"></iframe>
            </div>

            {{-- Loading --}}
            <div id="modalLoading" class="flex flex-col items-center gap-3 text-gray-400">
                <svg class="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span class="text-sm">Memuat lampiran...</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openLampiranModal(url, fileName, isPdf) {
    const modal    = document.getElementById('lampiranModal');
    const imgBox   = document.getElementById('modalImageContainer');
    const pdfBox   = document.getElementById('modalPdfContainer');
    const loading  = document.getElementById('modalLoading');
    const img      = document.getElementById('modalImage');
    const pdf      = document.getElementById('modalPdf');
    const dlBtn    = document.getElementById('modalDownloadBtn');
    const nameEl   = document.getElementById('modalFileName');

    // Reset state
    imgBox.classList.add('hidden');
    pdfBox.classList.add('hidden');
    loading.classList.remove('hidden');
    img.src = '';
    pdf.src = '';

    // Set meta
    nameEl.textContent = fileName;
    dlBtn.href = url;
    dlBtn.setAttribute('download', fileName);

    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';

    if (isPdf) {
        pdf.src = url;
        pdf.onload = () => loading.classList.add('hidden');
        pdfBox.classList.remove('hidden');
        pdfBox.classList.add('flex');
    } else {
        img.src = url;
        img.onload  = () => { loading.classList.add('hidden'); imgBox.classList.remove('hidden'); imgBox.classList.add('flex'); };
        img.onerror = () => { loading.innerHTML = '<p class="text-sm text-red-500">Gagal memuat gambar.</p>'; };
    }
}

function closeLampiranModal(e) {
    if (e && e.target !== document.getElementById('lampiranModal')) return;
    const modal = document.getElementById('lampiranModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('modalImage').src = '';
    document.getElementById('modalPdf').src   = '';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeLampiranModal();
});
</script>
@endpush

@endsection
