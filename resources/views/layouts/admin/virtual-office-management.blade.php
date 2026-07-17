{{-- resources/views/layouts/admin/virtual-office-management.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manajemen Virtual Office')

@section('content')
<div class="min-h-screen bg-slate-50 p-4 md:p-6" x-data="virtualOfficeManager()" x-init="init()">

    {{-- Loading Overlay --}}
    <div x-show="loading" x-cloak
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 flex flex-col items-center gap-4 shadow-2xl">
            <div class="w-12 h-12 border-4 border-orange-100 border-t-orange-600 rounded-full animate-spin"></div>
            <p class="text-slate-600 font-medium text-sm">Memproses...</p>
        </div>
    </div>

    {{-- Page Header --}}
    <div class="relative bg-gradient-to-br from-slate-900 via-orange-950 to-slate-800 rounded-2xl p-6 md:p-8 mb-6 overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500/10 rounded-full -translate-y-1/2 translate-x-1/4 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-1/3 w-48 h-48 bg-emerald-500/10 rounded-full translate-y-1/2 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-2 bg-orange-500/20 border border-orange-400/30 text-orange-300 text-xs font-semibold tracking-widest uppercase px-3 py-1.5 rounded-full mb-3">
                    <i class="fas fa-building text-xs"></i> Virtual Office
                </span>
                <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">Manajemen Virtual Office</h1>
                <p class="text-slate-400 text-sm mt-1">Kelola transaksi, dokumen, dan verifikasi penyewa</p>
            </div>

        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-receipt text-orange-600 text-sm"></i>
                </div>
                <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full">All</span>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ number_format($stats['total']) }}</p>
            <p class="text-xs text-slate-500 mt-0.5 font-medium">Total Transaksi</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                </div>
                <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Paid</span>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ number_format($stats['settlement']) }}</p>
            <p class="text-xs text-slate-500 mt-0.5 font-medium">Settlement</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-amber-500 text-sm"></i>
                </div>
                <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Wait</span>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ number_format($stats['pending']) }}</p>
            <p class="text-xs text-slate-500 mt-0.5 font-medium">Pending</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-500 text-sm"></i>
                </div>
                <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-0.5 rounded-full">Exp</span>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ number_format($stats['expire']) }}</p>
            <p class="text-xs text-slate-500 mt-0.5 font-medium">Expire</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-sky-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-piggy-bank text-sky-500 text-sm"></i>
                </div>
                <span class="text-xs font-medium text-sky-600 bg-sky-50 px-2 py-0.5 rounded-full">IDR</span>
            </div>
            <p class="text-lg font-bold text-slate-800 leading-tight">{{ number_format($stats['total_deposit']) }}</p>
            <p class="text-xs text-slate-500 mt-0.5 font-medium">Total Deposit</p>
        </div>

        <div class="bg-gradient-to-br from-orange-600 to-orange-700 rounded-2xl p-5 shadow-sm hover:shadow-lg hover:shadow-orange-500/30 hover:-translate-y-1 transition-all duration-200">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-white text-sm"></i>
                </div>
                <span class="text-xs font-medium text-orange-100 bg-white/20 px-2 py-0.5 rounded-full">Rev</span>
            </div>
            <p class="text-lg font-bold text-white leading-tight">{{ number_format($stats['total_revenue']) }}</p>
            <p class="text-xs text-orange-200 mt-0.5 font-medium">Total Revenue</p>
        </div>

    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-filter text-slate-500 text-xs"></i>
                </div>
                <h5 class="font-semibold text-slate-700 text-sm">Filter Data</h5>
            </div>
            <a href="{{ route('admin.virtual-office.index') }}"
               class="text-xs text-slate-400 hover:text-slate-600 flex items-center gap-1.5 transition-colors">
                <i class="fas fa-undo text-xs"></i> Reset
            </a>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('admin.virtual-office.index') }}"
                  class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Pencarian</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Nama, email, phone, order ID..."
                               class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/30 focus:border-orange-400 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Status</label>
                    <div class="relative">
                        <select name="status"
                                class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700 focus:outline-none focus:ring-2 focus:ring-orange-500/30 focus:border-orange-400 transition-all appearance-none">
                            <option value="">Semua Status</option>
                            <option value="settlement" {{ request('status') == 'settlement' ? 'selected' : '' }}>Settlement</option>
                            <option value="pending"    {{ request('status') == 'pending'    ? 'selected' : '' }}>Pending</option>
                            <option value="expire"     {{ request('status') == 'expire'     ? 'selected' : '' }}>Expire</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700 focus:outline-none focus:ring-2 focus:ring-orange-500/30 focus:border-orange-400 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700 focus:outline-none focus:ring-2 focus:ring-orange-500/30 focus:border-orange-400 transition-all">
                </div>

                <div class="sm:col-span-2 lg:col-span-5 flex items-center gap-3 pt-1">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-500 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-200 hover:shadow-lg hover:shadow-orange-500/30 hover:-translate-y-0.5">
                        <i class="fas fa-search text-xs"></i> Cari
                    </button>
                    <a href="{{ route('admin.virtual-office.index') }}"
                       class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold px-4 py-2.5 rounded-xl transition-all duration-200">
                        <i class="fas fa-sync text-xs"></i> Reset
                    </a>

                    {{-- Button Import Excel --}}
                    <button type="button" @click="showImportModal = true"
                            class="inline-flex items-center gap-2 bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 text-sm font-semibold px-4 py-2.5 rounded-xl transition-all duration-200 hover:bg-emerald-500/20 hover:shadow-lg hover:shadow-emerald-500/10">
                        <i class="fas fa-file-excel text-xs"></i> Import Excel
                    </button>
                    
                    {{-- Dropdown Export PDF --}}
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" @click.away="open = false"
                                class="inline-flex items-center gap-2 bg-red-500/10 text-red-600 border border-red-500/20 text-sm font-semibold px-4 py-2.5 rounded-xl transition-all duration-200 hover:bg-red-500/20 hover:shadow-lg hover:shadow-red-500/10">
                            <i class="fas fa-file-pdf text-xs"></i> Export PDF <i class="fas fa-chevron-down text-[10px] ml-0.5"></i>
                        </button>
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-56 rounded-xl bg-white border border-slate-200 shadow-xl z-[9999] overflow-hidden"
                             style="display: none;">
                            <div class="py-1">
                                <a href="#" @click.prevent="exportPdf('')" class="block px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 hover:text-slate-900 border-b border-slate-100">
                                    <i class="fas fa-file-alt mr-2 text-slate-400"></i> Export Semua Data
                                </a>
                                <a href="#" @click.prevent="exportPdf('50')" class="block px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 hover:text-slate-900">
                                    <i class="fas fa-list-ol mr-2 text-slate-400"></i> Export 50 Data Terbaru
                                </a>
                                <a href="#" @click.prevent="exportPdf('100')" class="block px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 hover:text-slate-900">
                                    <i class="fas fa-list-ol mr-2 text-slate-400"></i> Export 100 Data Terbaru
                                </a>
                                <a href="#" @click.prevent="exportPdf('200')" class="block px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 hover:text-slate-900">
                                    <i class="fas fa-list-ol mr-2 text-slate-400"></i> Export 200 Data Terbaru
                                </a>
                                <a href="#" @click.prevent="exportPdf('500')" class="block px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 hover:text-slate-900">
                                    <i class="fas fa-list-ol mr-2 text-slate-400"></i> Export 500 Data Terbaru
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Cards Grid --}}
    @if($transactions->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 text-center">
        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-building text-slate-300 text-3xl"></i>
        </div>
        <h5 class="text-slate-700 font-semibold text-lg mb-1">Belum ada data Virtual Office</h5>
        <p class="text-slate-400 text-sm">Data akan muncul ketika ada transaksi Virtual Office</p>
    </div>
    @else
    {{-- 1 col mobile → 2 col tablet (md) → 3 col desktop (lg+) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @foreach($transactions as $transaction)

        @php
            $statusMap = [
                'settlement' => [
                    'label'  => 'Settlement',
                    'badge'  => 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200',
                    'icon'   => 'fas fa-check-circle text-emerald-600',
                    'iconBg' => 'bg-emerald-100',
                ],
                'pending' => [
                    'label'  => 'Pending',
                    'badge'  => 'bg-amber-100 text-amber-700 ring-1 ring-amber-200',
                    'icon'   => 'fas fa-clock text-amber-600',
                    'iconBg' => 'bg-amber-100',
                ],
                'expire' => [
                    'label'  => 'Expire',
                    'badge'  => 'bg-red-100 text-red-600 ring-1 ring-red-200',
                    'icon'   => 'fas fa-times-circle text-red-500',
                    'iconBg' => 'bg-red-100',
                ],
            ];
            $st = $statusMap[$transaction->status] ?? [
                'label'  => ucfirst($transaction->status),
                'badge'  => 'bg-slate-100 text-slate-600',
                'icon'   => 'fas fa-circle text-slate-400',
                'iconBg' => 'bg-slate-100',
            ];
        @endphp

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 overflow-hidden flex flex-col">

            {{-- Card Header --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 bg-slate-50/60">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-7 h-7 {{ $st['iconBg'] }} rounded-lg flex items-center justify-center shrink-0">
                        <i class="{{ $st['icon'] }} text-xs"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-slate-800 text-xs truncate">{{ Str::limit($transaction->nama_lengkap, 24) }}</p>
                        <p class="text-xs text-slate-400 font-mono truncate leading-tight">{{ $transaction->order_id }}</p>
                    </div>
                </div>
                <span class="shrink-0 text-xs font-semibold px-2 py-0.5 rounded-md {{ $st['badge'] }}">
                    {{ $st['label'] }}
                </span>
            </div>

            <div class="p-4 space-y-3 flex-1 flex flex-col">

                {{-- Contact Info --}}
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 bg-slate-100 rounded-md flex items-center justify-center shrink-0">
                            <i class="fas fa-user text-slate-400" style="font-size:9px"></i>
                        </div>
                        <span class="text-xs text-slate-600 truncate">{{ $transaction->user->name ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 bg-slate-100 rounded-md flex items-center justify-center shrink-0">
                            <i class="fas fa-envelope text-slate-400" style="font-size:9px"></i>
                        </div>
                        <span class="text-xs text-slate-600 truncate">{{ $transaction->email }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 bg-slate-100 rounded-md flex items-center justify-center shrink-0">
                            <i class="fas fa-phone text-slate-400" style="font-size:9px"></i>
                        </div>
                        <span class="text-xs text-slate-600">{{ $transaction->phone }}</span>
                    </div>
                </div>

                {{-- Booking Period --}}
                @php
                    $startDate = $transaction->booking_date ? Carbon\Carbon::parse($transaction->booking_date) : null;
                    $endDate = null;
                    $remainingText = '-';
                    
                    if ($startDate) {
                        $endDate = clone $startDate;
                        if ($transaction->jam) {
                            $endDate->addHours($transaction->jam);
                        } elseif ($transaction->hari) {
                            $endDate->addDays($transaction->hari);
                        } elseif ($transaction->minggu) {
                            $endDate->addWeeks($transaction->minggu);
                        } elseif ($transaction->bulan) {
                            $endDate->addMonths($transaction->bulan);
                        } elseif ($transaction->tahun) {
                            $endDate->addYears($transaction->tahun);
                        }
                        
                        $today = now()->startOfDay();
                        $startDateComp = clone $startDate;
                        $startDateComp->startOfDay();
                        $endDateComp = clone $endDate;
                        $endDateComp->startOfDay();
                        
                        if ($today->gt($endDateComp)) {
                            $remainingText = 'Expired';
                        } elseif ($today->lt($startDateComp)) {
                            $remainingText = $transaction->duration_text . ' (Belum Mulai)';
                        } else {
                            $diff = $today->diff($endDateComp);
                            $m = $diff->m + ($diff->y * 12);
                            $d = $diff->d;
                            
                            $parts = [];
                            if ($m > 0) {
                                $parts[] = $m . ' Bulan';
                            }
                            if ($d > 0) {
                                $parts[] = $d . ' Hari';
                            }
                            
                            $remainingText = count($parts) > 0 ? implode(' ', $parts) : '0 Hari';
                        }
                    }
                @endphp
                <div class="bg-orange-50/70 border border-orange-100 rounded-xl px-3 py-2.5 space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 flex items-center gap-1.5">
                            <i class="fas fa-calendar-alt text-orange-500 w-3.5 text-center"></i> Mulai
                        </span>
                        <span class="font-semibold text-slate-700">
                            {{ $startDate ? $startDate->format('d M Y') : '-' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 flex items-center gap-1.5">
                            <i class="fas fa-hourglass-half text-orange-500 w-3.5 text-center"></i> Durasi
                        </span>
                        <span class="font-semibold text-slate-700">
                            {{ $transaction->duration_text }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs border-t border-orange-200/50 pt-1.5">
                        <span class="text-slate-500 flex items-center gap-1.5">
                            <i class="fas fa-clock text-orange-500 w-3.5 text-center"></i> Sisa Sewa
                        </span>
                        <span class="font-bold {{ $remainingText === 'Expired' ? 'text-red-600' : 'text-orange-700' }}">
                            {{ $remainingText }}
                        </span>
                    </div>
                </div>

                {{-- Pricing --}}
                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Harga Sewa</span>
                        <span class="font-semibold text-slate-700">
                            Rp {{ number_format($transaction->gross_amount - ($transaction->deposit ?? 0)) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Deposit</span>
                        <span class="font-semibold text-slate-700">Rp {{ number_format($transaction->deposit ?? 0) }}</span>
                    </div>
                    <div class="border-t border-dashed border-slate-200 pt-1.5 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-600">Gross Amount</span>
                        <span class="text-xs font-extrabold text-orange-600">Rp {{ number_format($transaction->gross_amount) }}</span>
                    </div>
                </div>

                {{-- Payment Type --}}
                <div class="flex items-center gap-1.5">
                    <i class="fas fa-credit-card text-slate-400 text-xs"></i>
                    <span class="text-xs text-slate-500 font-medium">{{ $transaction->payment_type ?? 'N/A' }}</span>
                </div>

                {{-- Spacer --}}
                <div class="flex-1"></div>

                {{-- Dokumen Footer --}}
                <div class="border-t border-slate-100 pt-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dokumen</span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                            {{ $transaction->documents->count() > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $transaction->documents->count() }} file
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <button class="flex-1 inline-flex items-center justify-center gap-1.5 bg-orange-600 hover:bg-orange-500 text-white text-xs font-semibold py-2 rounded-lg transition-all duration-200 hover:shadow-md hover:shadow-orange-500/30"
                                @click="showDocuments({{ $transaction->id }}, '{{ addslashes($transaction->nama_lengkap) }}')">
                            <i class="fas fa-eye text-xs"></i> Lihat Dokumen
                        </button>
                        <button type="button"
                                @click="showTransactionDetail({{ json_encode($transaction->load('user', 'location')) }})"
                                class="inline-flex items-center justify-center w-8 h-8 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition-all duration-200"
                                title="Detail Transaksi">
                            <i class="fas fa-info-circle text-xs"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Pagination --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-slate-500">
            Menampilkan
            <span class="font-semibold text-slate-700">{{ $transactions->firstItem() ?? 0 }}</span>–<span class="font-semibold text-slate-700">{{ $transactions->lastItem() ?? 0 }}</span>
            dari <span class="font-semibold text-slate-700">{{ $transactions->total() }}</span> data
        </p>
        <div>{{ $transactions->links() }}</div>
    </div>


    {{-- ===================== MODAL DOKUMEN ===================== --}}
    <div x-show="showModal" x-cloak
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeDocumentsModal()"></div>

        <div class="relative w-full sm:max-w-lg lg:max-w-xl bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl overflow-hidden max-h-[92vh] sm:max-h-[90vh] flex flex-col"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95">

            {{-- Drag handle (mobile) --}}
            <div class="flex justify-center pt-3 pb-1 sm:hidden">
                <div class="w-10 h-1 bg-slate-200 rounded-full"></div>
            </div>

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 bg-slate-50 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-alt text-orange-600 text-sm"></i>
                    </div>
                    <div>
                        <h5 class="font-bold text-slate-800 text-sm">Dokumen Penyewa</h5>
                        <p class="text-xs text-slate-500" x-text="transactionName"></p>
                    </div>
                </div>
                <button @click="closeDocumentsModal()"
                        class="w-8 h-8 bg-slate-100 hover:bg-slate-200 rounded-xl flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-slate-500 text-xs"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="overflow-y-auto flex-1 p-4 space-y-3">

                <div x-show="loadingDocuments" class="flex flex-col items-center justify-center py-16 gap-4">
                    <div class="w-10 h-10 border-4 border-orange-100 border-t-orange-600 rounded-full animate-spin"></div>
                    <p class="text-slate-500 text-sm">Memuat dokumen...</p>
                </div>

                <template x-if="!loadingDocuments && documents.length === 0">
                    <div class="flex flex-col items-center justify-center py-16 gap-3">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-folder-open text-slate-300 text-2xl"></i>
                        </div>
                        <p class="text-slate-500 text-sm font-medium">Belum ada dokumen</p>
                        <p class="text-slate-400 text-xs">Penyewa belum mengupload dokumen</p>
                    </div>
                </template>

                <template x-for="doc in documents" :key="doc.id">
                    <div class="border border-slate-200 rounded-2xl overflow-hidden hover:border-orange-200 hover:shadow-sm transition-all duration-200">

                        <div class="flex items-center justify-between px-4 py-3 bg-slate-50/80 border-b border-slate-100">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center"
                                     :class="{
                                         'bg-emerald-100': doc.status === 'verified',
                                         'bg-amber-100':   doc.status === 'pending',
                                         'bg-red-100':     doc.status === 'rejected'
                                     }">
                                    <i class="text-xs"
                                       :class="{
                                           'fas fa-check text-emerald-600':  doc.status === 'verified',
                                           'fas fa-clock text-amber-600':    doc.status === 'pending',
                                           'fas fa-times text-red-500':      doc.status === 'rejected'
                                       }"></i>
                                </div>
                                <span class="font-semibold text-slate-700 text-sm" x-text="doc.type_label"></span>
                            </div>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg capitalize"
                                  :class="{
                                      'bg-emerald-100 text-emerald-700': doc.status === 'verified',
                                      'bg-amber-100 text-amber-700':     doc.status === 'pending',
                                      'bg-red-100 text-red-600':         doc.status === 'rejected'
                                  }"
                                  x-text="doc.status"></span>
                        </div>

                        <div class="px-4 py-3 space-y-2">
                            <div class="flex items-center gap-2 text-xs text-slate-600">
                                <i class="fas fa-file text-slate-400 w-3.5 text-center"></i>
                                <span class="truncate font-medium" x-text="doc.original_filename"></span>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-clock"></i>
                                    <span x-text="doc.uploaded_at"></span>
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-hdd"></i>
                                    <span x-text="doc.file_size"></span>
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-tag"></i>
                                    <span x-text="doc.mime_type"></span>
                                </span>
                            </div>

                            <template x-if="doc.verified_by">
                                <div class="flex items-center gap-1.5 text-xs text-emerald-700 bg-emerald-50 border border-emerald-100 px-3 py-2 rounded-xl">
                                    <i class="fas fa-check-circle shrink-0"></i>
                                    <span>Diverifikasi oleh <strong x-text="doc.verified_by"></strong>
                                        (<span x-text="doc.verified_at"></span>)</span>
                                </div>
                            </template>

                            <template x-if="doc.notes">
                                <div class="flex items-start gap-1.5 text-xs text-red-700 bg-red-50 border border-red-100 px-3 py-2 rounded-xl">
                                    <i class="fas fa-exclamation-circle shrink-0 mt-0.5"></i>
                                    <span x-text="doc.notes"></span>
                                </div>
                            </template>

                            <div class="flex flex-wrap items-center gap-2 pt-1">
                                <a :href="doc.file_url" target="_blank"
                                   class="inline-flex items-center gap-1.5 bg-sky-50 hover:bg-sky-100 text-sky-700 text-xs font-semibold px-3 py-2 rounded-xl transition-colors border border-sky-100">
                                    <i class="fas fa-eye"></i> Preview
                                </a>
                                <a :href="`/admin/virtual-office/documents/${doc.id}/download`"
                                   class="inline-flex items-center gap-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold px-3 py-2 rounded-xl transition-colors border border-slate-200"
                                   :class="{ 'opacity-40 pointer-events-none': !doc.can_download }">
                                    <i class="fas fa-download"></i> Download
                                </a>
                                <template x-if="doc.status === 'pending'">
                                    <button @click="verifyDocument(doc.id)"
                                            class="inline-flex items-center gap-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold px-3 py-2 rounded-xl transition-colors border border-emerald-100">
                                        <i class="fas fa-check"></i> Setujui
                                    </button>
                                </template>
                                <template x-if="doc.status === 'pending'">
                                    <button @click="rejectDocument(doc.id)"
                                            class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold px-3 py-2 rounded-xl transition-colors border border-red-100">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/60 shrink-0">
                <button @click="closeDocumentsModal()"
                        class="w-full bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-sm py-2.5 rounded-xl transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ===================== MODAL DETAIL TRANSAKSI ===================== --}}
    <div x-show="showDetailModal" x-cloak
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">

        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showDetailModal = false"></div>

        <div class="relative w-full sm:max-w-lg bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl overflow-hidden max-h-[92vh] sm:max-h-[90vh] flex flex-col"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95">

            {{-- Drag handle (mobile) --}}
            <div class="flex justify-center pt-3 pb-1 sm:hidden">
                <div class="w-10 h-1 bg-slate-200 rounded-full"></div>
            </div>

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-info-circle text-orange-600 text-sm"></i>
                    </div>
                    <div>
                        <h5 class="font-bold text-slate-800 text-sm">Detail Transaksi</h5>
                        <p class="text-xs text-slate-500" x-text="selectedTransaction ? selectedTransaction.order_id : ''"></p>
                    </div>
                </div>
                <button @click="showDetailModal = false"
                        class="w-8 h-8 bg-slate-100 hover:bg-slate-200 rounded-xl flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-slate-500 text-xs"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="overflow-y-auto flex-1 p-6 space-y-6" x-show="selectedTransaction">
                
                {{-- Status & Location --}}
                <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block tracking-wider mb-1">Status</span>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-md"
                              :class="{
                                  'bg-emerald-100 text-emerald-700 border border-emerald-200': selectedTransaction && selectedTransaction.status === 'settlement',
                                  'bg-amber-100 text-amber-700 border border-amber-200': selectedTransaction && selectedTransaction.status === 'pending',
                                  'bg-red-100 text-red-700 border border-red-200': selectedTransaction && selectedTransaction.status === 'expire'
                              }"
                              x-text="selectedTransaction ? selectedTransaction.status.toUpperCase() : ''">
                        </span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block tracking-wider mb-1 text-right">Lokasi Cabang</span>
                        <span class="text-xs font-semibold text-slate-700 block text-right" 
                              x-text="selectedTransaction && selectedTransaction.location ? selectedTransaction.location.name : 'Tidak Terdaftar'"></span>
                    </div>
                </div>

                {{-- Tenant Info --}}
                <div class="space-y-3">
                    <h6 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Informasi Penyewa</h6>
                    <div class="grid grid-cols-1 gap-3 bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                        <div>
                            <span class="text-[10px] text-slate-400 block">Nama Lengkap</span>
                            <span class="text-xs font-semibold text-slate-800" x-text="selectedTransaction ? selectedTransaction.nama_lengkap : '-'"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-[10px] text-slate-400 block">Email</span>
                                <span class="text-xs font-semibold text-slate-800 break-all" x-text="selectedTransaction ? selectedTransaction.email : '-'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block">Telepon</span>
                                <span class="text-xs font-semibold text-slate-800" x-text="selectedTransaction ? selectedTransaction.phone : '-'"></span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-[10px] text-slate-400 block">NIK</span>
                                <span class="text-xs font-semibold text-slate-800" x-text="selectedTransaction ? selectedTransaction.nik : '-'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block">NPWP</span>
                                <span class="text-xs font-semibold text-slate-800" x-text="selectedTransaction ? selectedTransaction.npwp : '-'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Company Info --}}
                <div class="space-y-3">
                    <h6 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Informasi Perusahaan</h6>
                    <div class="grid grid-cols-1 gap-3 bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                        <div>
                            <span class="text-[10px] text-slate-400 block">Nama Perusahaan</span>
                            <span class="text-xs font-semibold text-slate-800" x-text="selectedTransaction ? selectedTransaction.company_name : '-'"></span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block">Alamat Perusahaan</span>
                            <span class="text-xs font-semibold text-slate-800 leading-relaxed" x-text="selectedTransaction ? selectedTransaction.company_address : '-'"></span>
                        </div>
                    </div>
                </div>

                {{-- Service & Pricing Details --}}
                <div class="space-y-3">
                    <h6 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Detail Layanan & Pembayaran</h6>
                    <div class="border border-slate-200 rounded-2xl overflow-hidden divide-y divide-slate-100">
                        <div class="p-4 bg-slate-50/50 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500">Layanan</span>
                                <span class="font-bold text-slate-800">Virtual Office</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500">Paket</span>
                                <span class="font-bold text-orange-600" x-text="selectedTransaction ? (selectedTransaction.paket || 'Standard') : 'Standard'"></span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500">Durasi</span>
                                <span class="font-semibold text-slate-700" 
                                      x-text="selectedTransaction ? (selectedTransaction.jam ? selectedTransaction.jam + ' Jam' : 
                                              (selectedTransaction.hari ? selectedTransaction.hari + ' Hari' : 
                                              (selectedTransaction.minggu ? selectedTransaction.minggu + ' Minggu' : 
                                              (selectedTransaction.bulan ? selectedTransaction.bulan + ' Bulan' : 
                                              (selectedTransaction.tahun ? selectedTransaction.tahun + ' Tahun' : '-'))))) : '-'"></span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500">Tanggal Mulai</span>
                                <span class="font-semibold text-slate-700" 
                                      x-text="selectedTransaction && selectedTransaction.booking_date ? new Date(selectedTransaction.booking_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'}) : '-'"></span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500">Tanggal Berakhir</span>
                                <span class="font-semibold text-slate-700" 
                                      x-text="getLeaseDetails().endDate"></span>
                            </div>
                            <div class="flex items-center justify-between text-xs border-t border-dashed border-slate-100 pt-1.5">
                                <span class="text-slate-500">Sisa Durasi Sewa</span>
                                <span class="font-bold" 
                                      :class="getLeaseDetails().remaining === 'Expired' ? 'text-red-600' : 'text-orange-600'"
                                      x-text="getLeaseDetails().remaining"></span>
                            </div>
                        </div>
                        <div class="p-4 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Harga Sewa</span>
                                <span class="font-semibold text-slate-700" x-text="selectedTransaction ? 'Rp ' + new Intl.NumberFormat('id-ID').format(selectedTransaction.gross_amount - (selectedTransaction.deposit || 0)) : 'Rp 0'"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Deposit</span>
                                <span class="font-semibold text-slate-700" x-text="selectedTransaction ? 'Rp ' + new Intl.NumberFormat('id-ID').format(selectedTransaction.deposit || 0) : 'Rp 0'"></span>
                            </div>
                            <div class="border-t border-dashed border-slate-200 pt-2 flex items-center justify-between">
                                <span class="font-bold text-slate-700">Gross Amount</span>
                                <span class="font-extrabold text-orange-600 text-sm" x-text="selectedTransaction ? 'Rp ' + new Intl.NumberFormat('id-ID').format(selectedTransaction.gross_amount) : 'Rp 0'"></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end shrink-0">
                <button @click="showDetailModal = false"
                        class="bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition-all duration-150">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ===================== MODAL VERIFIKASI COMPACT ===================== --}}
    <div x-show="showVerifyModal" x-cloak
        class="fixed inset-0 z-[60] flex items-center justify-center p-2 sm:p-4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showVerifyModal = false"></div>

        {{-- 
            RESPONSIVE SIZING:
            - Mobile (default): 260px
            - Tablet (sm): 280px  
            - Desktop (md): 300px
        --}}
        <div class="verify-modal-panel relative w-[260px] sm:w-[280px] md:w-[300px] bg-white rounded-xl shadow-2xl overflow-hidden"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            {{-- Header lebih compact --}}
            <div class="flex items-center justify-between px-3 py-2 border-b border-slate-100 bg-slate-50">
                <div class="flex items-center gap-1.5">
                    <div class="w-5 h-5 bg-orange-100 rounded flex items-center justify-center">
                        <i class="fas fa-shield-alt text-orange-600" style="font-size:8px"></i>
                    </div>
                    <h5 class="font-semibold text-slate-800 text-[11px] leading-none">Verifikasi Dokumen</h5>
                </div>
                <button @click="showVerifyModal = false"
                        class="w-5 h-5 bg-slate-100 hover:bg-slate-200 rounded flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-slate-500" style="font-size:8px"></i>
                </button>
            </div>

            {{-- Body lebih compact --}}
            <div class="p-2.5 space-y-2.5">

                {{-- Status Radio lebih compact --}}
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wide mb-1">Status</label>
                    <div class="grid grid-cols-2 gap-1">
                        {{-- Setujui Option --}}
                        <label class="flex items-center gap-1 px-2 py-1.5 border rounded-lg cursor-pointer transition-all"
                            :class="verifyForm.status === 'verified' ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" x-model="verifyForm.status" value="verified" class="hidden">
                            <div class="w-2.5 h-2.5 rounded-full border flex items-center justify-center shrink-0 transition-all"
                                :class="verifyForm.status === 'verified' ? 'border-emerald-500 bg-emerald-500' : 'border-slate-300'">
                                <div x-show="verifyForm.status === 'verified'" class="w-1 h-1 bg-white rounded-full"></div>
                            </div>
                            <span class="text-[11px] font-medium text-slate-700">Setujui</span>
                        </label>
                        
                        {{-- Tolak Option --}}
                        <label class="flex items-center gap-1 px-2 py-1.5 border rounded-lg cursor-pointer transition-all"
                            :class="verifyForm.status === 'rejected' ? 'border-red-400 bg-red-50' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" x-model="verifyForm.status" value="rejected" class="hidden">
                            <div class="w-2.5 h-2.5 rounded-full border flex items-center justify-center shrink-0 transition-all"
                                :class="verifyForm.status === 'rejected' ? 'border-red-500 bg-red-500' : 'border-slate-300'">
                                <div x-show="verifyForm.status === 'rejected'" class="w-1 h-1 bg-white rounded-full"></div>
                            </div>
                            <span class="text-[11px] font-medium text-slate-700">Tolak</span>
                        </label>
                    </div>
                </div>

                {{-- Catatan lebih compact --}}
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wide mb-1">
                        Catatan <span class="text-slate-400 normal-case font-normal">(opsional)</span>
                    </label>
                    <textarea x-model="verifyForm.notes" rows="2"
                            placeholder="Alasan..."
                            class="w-full px-2 py-1.5 text-[11px] border border-slate-200 rounded-lg bg-slate-50 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500/30 focus:border-orange-400 transition-all resize-none"></textarea>
                </div>

                {{-- Actions lebih compact --}}
                <div class="flex gap-1.5 pt-0.5">
                    <button @click="showVerifyModal = false"
                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium text-[11px] py-1.5 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button @click="submitVerification()"
                            :disabled="!verifyForm.status"
                            class="flex-1 bg-orange-600 hover:bg-orange-500 disabled:opacity-40 disabled:cursor-not-allowed text-white font-medium text-[11px] py-1.5 rounded-lg transition-all duration-200">
                        Simpan
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- ===================== MODAL IMPORT EXCEL ===================== --}}
    <div x-show="showImportModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showImportModal = false"></div>
        
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600">
                        <i class="fas fa-file-excel text-base"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 text-base">Import Penyewa VO</h3>
                </div>
                <button @click="showImportModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times text-base"></i>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <p class="text-xs text-slate-600 leading-relaxed">
                    Silakan unggah file Excel penyewa Virtual Office. Anda bisa mengunduh file template di bawah untuk menyesuaikan struktur kolom data.
                </p>
                
                {{-- Download Template Link --}}
                <div class="flex items-center justify-between p-3.5 bg-emerald-50/50 border border-emerald-100/60 rounded-xl">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 bg-emerald-100 rounded-md flex items-center justify-center text-emerald-600 text-xs">
                            <i class="fas fa-download"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-emerald-800">Template Impor</p>
                            <p class="text-[9px] text-emerald-600/80">format template resmi (.xlsx)</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.virtual-office.import.template') }}"
                       class="text-[11px] font-bold text-emerald-700 hover:text-emerald-800 underline">
                        Unduh Template
                    </a>
                </div>
                
                {{-- Drag and drop zone --}}
                <div @dragover.prevent="" @drop.prevent="handleFileDrop($event)" @click="triggerFileInput()"
                     class="border-2 border-dashed border-slate-200 hover:border-emerald-400 rounded-2xl p-6 text-center cursor-pointer transition-colors bg-slate-50/30">
                    <input type="file" x-ref="fileInput" @change="handleFileChange($event)" class="hidden" accept=".xlsx,.xls">
                    <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-2 text-slate-400">
                        <i class="fas fa-cloud-upload-alt text-lg"></i>
                    </div>
                    <p class="text-xs font-semibold text-slate-700">Tarik & lepas file di sini</p>
                    <p class="text-[10px] text-slate-400 mt-1">atau klik untuk memilih file dari komputer Anda (.xlsx, .xls)</p>
                </div>
                
                {{-- Selected File Info --}}
                <div x-show="importFileName" class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-xl" style="display: none;">
                    <div class="flex items-center gap-2 overflow-hidden">
                        <i class="fas fa-file-excel text-emerald-600 shrink-0"></i>
                        <span class="text-xs font-semibold text-slate-700 truncate" x-text="importFileName"></span>
                    </div>
                    <button @click="importFile = null; importFileName = ''" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                </div>
            </div>
            
            <div class="flex gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50">
                <button @click="showImportModal = false"
                        class="flex-1 bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 font-semibold text-xs py-2.5 rounded-xl transition-all duration-150">
                    Batal
                </button>
                <button @click="submitImport()" :disabled="!importFile"
                        class="flex-1 bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 hover:bg-emerald-500/20 disabled:opacity-40 disabled:cursor-not-allowed font-semibold text-xs py-2.5 rounded-xl transition-all duration-200">
                    Unggah Data
                </button>
            </div>
        </div>
    </div>

    {{-- ===================== MODAL ERROR POP-UP (ROLLBACK) ===================== --}}
    <div x-show="showErrorModal" x-cloak
         class="fixed inset-0 z-[60] flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showErrorModal = false"></div>
        
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <div class="flex items-center justify-between px-6 py-4 border-b border-red-100 bg-red-50">
                <div class="flex items-center gap-2 text-red-600">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-base"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 text-base">Impor Gagal (Rollback Aktif)</h3>
                </div>
                <button @click="showErrorModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times text-base"></i>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl">
                    <p class="text-xs text-red-800 leading-relaxed font-semibold">
                        Seluruh transaksi impor dibatalkan karena terdeteksi data yang tidak valid. Silakan perbaiki file Excel Anda berdasarkan daftar kesalahan di bawah ini, lalu unggah kembali.
                    </p>
                </div>
                
                {{-- Error List Container --}}
                <div class="max-h-60 overflow-y-auto border border-slate-100 rounded-xl divide-y divide-slate-100">
                    <template x-for="err in importErrors" :key="err">
                        <div class="p-3 flex items-start gap-2.5 text-xs text-slate-700 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                            <i class="fas fa-exclamation-circle text-red-500 mt-1 text-xs shrink-0"></i>
                            <span x-text="err" class="leading-relaxed"></span>
                        </div>
                    </template>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end">
                <button @click="showErrorModal = false"
                        class="bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition-all duration-150">
                    Tutup & Perbaiki
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function virtualOfficeManager() {
    return {
        loading: false,
        loadingDocuments: false,
        documents: [],
        transactionId: null,
        transactionName: '',
        currentDocumentId: null,
        showModal: false,
        showVerifyModal: false,
        showImportModal: false,
        showDetailModal: false,
        selectedTransaction: null,
        showErrorModal: false,
        importErrors: [],
        importFile: null,
        importFileName: '',

        verifyForm: { status: '', notes: '' },

        csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,

        init() {},

        triggerFileInput() {
            this.$refs.fileInput.click();
        },
        
        handleFileChange(e) {
            const files = e.target.files;
            if (files.length > 0) {
                this.importFile = files[0];
                this.importFileName = files[0].name;
            }
        },
        
        handleFileDrop(e) {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                this.importFile = files[0];
                this.importFileName = files[0].name;
            }
        },

        async submitImport() {
            if (!this.importFile) {
                alert('Silakan pilih file Excel terlebih dahulu.');
                return;
            }

            this.loading = true;
            this.importErrors = [];

            const formData = new FormData();
            formData.append('file', this.importFile);

            try {
                const res = await fetch('/admin/virtual-office/import', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: formData
                });

                const data = await res.json();

                if (res.ok) {
                    this.showImportModal = false;
                    this.importFile = null;
                    this.importFileName = '';
                    this.showToast(data.message || 'Impor data berhasil.', 'success');
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else if (res.status === 422) {
                    this.importErrors = data.errors || ['Terjadi kesalahan saat validasi data.'];
                    this.showErrorModal = true;
                } else {
                    alert(data.message || 'Gagal mengimpor data.');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan koneksi saat mengunggah file.');
            } finally {
                this.loading = false;
            }
        },

        showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-6 right-6 z-[9999] p-4 rounded-2xl shadow-xl border border-emerald-100 bg-white/95 backdrop-blur-sm flex items-center gap-3 transition-all duration-300 transform translate-y-2 opacity-0`;
            
            toast.innerHTML = `
                <div class="w-8 h-8 rounded-full flex items-center justify-center ${
                    type === 'success' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600'
                }">
                    <i class="fas ${type === 'success' ? 'fa-check' : 'fa-exclamation-triangle'} text-xs"></i>
                </div>
                <div class="flex flex-col">
                    <p class="text-xs font-bold text-slate-800">${type === 'success' ? 'Sukses' : 'Gagal'}</p>
                    <p class="text-[11px] text-slate-500">${message}</p>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Trigger animation
            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 10);
            
            // Auto remove after 3.5 seconds
            setTimeout(() => {
                toast.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, 3500);
        },

        exportPdf(limit) {
            const search = document.querySelector('input[name="search"]')?.value || '';
            const status = document.querySelector('select[name="status"]')?.value || '';
            const dateFrom = document.querySelector('input[name="date_from"]')?.value || '';
            const dateTo = document.querySelector('input[name="date_to"]')?.value || '';
            
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (status) params.append('status', status);
            if (dateFrom) params.append('date_from', dateFrom);
            if (dateTo) params.append('date_to', dateTo);
            if (limit) params.append('limit', limit);
            
            window.location.href = "{{ route('admin.virtual-office.export') }}?" + params.toString();
        },

        showTransactionDetail(transaction) {
            this.selectedTransaction = transaction;
            this.showDetailModal = true;
        },

        getLeaseDetails() {
            if (!this.selectedTransaction) return { endDate: '-', remaining: '-' };
            
            const startStr = this.selectedTransaction.booking_date;
            if (!startStr) return { endDate: '-', remaining: '-' };
            
            const startDate = new Date(startStr);
            const endDate = new Date(startStr);
            
            const t = this.selectedTransaction;
            if (t.jam) {
                endDate.setHours(endDate.getHours() + parseInt(t.jam));
            } else if (t.hari) {
                endDate.setDate(endDate.getDate() + parseInt(t.hari));
            } else if (t.minggu) {
                endDate.setDate(endDate.getDate() + (parseInt(t.minggu) * 7));
            } else if (t.bulan) {
                endDate.setMonth(endDate.getMonth() + parseInt(t.bulan));
            } else if (t.tahun) {
                endDate.setFullYear(endDate.getFullYear() + parseInt(t.tahun));
            }
            
            // Format End Date
            const endDateFormatted = endDate.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
            
            // Calculate remaining
            const today = new Date();
            today.setHours(0,0,0,0);
            
            const startDateComp = new Date(startDate);
            startDateComp.setHours(0,0,0,0);
            
            const endDateComp = new Date(endDate);
            endDateComp.setHours(0,0,0,0);
            
            let remainingText = '-';
            if (today > endDateComp) {
                remainingText = 'Expired';
            } else if (today < startDateComp) {
                if (t.tahun) {
                    remainingText = (parseInt(t.tahun) * 12) + ' Bulan (Belum Mulai)';
                } else if (t.bulan) {
                    remainingText = t.bulan + ' Bulan (Belum Mulai)';
                } else if (t.minggu) {
                    remainingText = t.minggu + ' Minggu (Belum Mulai)';
                } else if (t.hari) {
                    remainingText = t.hari + ' Hari (Belum Mulai)';
                } else if (t.jam) {
                    remainingText = t.jam + ' Jam (Belum Mulai)';
                } else {
                    remainingText = 'Belum Mulai';
                }
            } else {
                // Calculate exact difference in months and days
                let tempDate = new Date(today);
                let mDiff = 0;
                
                while (true) {
                    let testDate = new Date(tempDate);
                    testDate.setMonth(testDate.getMonth() + 1);
                    if (testDate <= endDateComp) {
                        tempDate = testDate;
                        mDiff++;
                    } else {
                        break;
                    }
                }
                
                const diffTime = Math.abs(endDateComp - tempDate);
                const dDiff = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                let parts = [];
                if (mDiff > 0) {
                    parts.push(mDiff + ' Bulan');
                }
                if (dDiff > 0) {
                    parts.push(dDiff + ' Hari');
                }
                
                remainingText = parts.length > 0 ? parts.join(' ') : '0 Hari';
            }
            
            return {
                endDate: endDateFormatted,
                remaining: remainingText
            };
        },

        async showDocuments(transactionId, customerName) {
            this.transactionId  = transactionId;
            this.transactionName = customerName;
            this.loadingDocuments = true;
            this.documents = [];
            this.showModal = true;

            try {
                const res = await fetch(`/admin/virtual-office/${transactionId}/documents`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken }
                });
                if (!res.ok) throw new Error('Network error');
                const data = await res.json();
                if (data.success) this.documents = data.documents;
                else alert('Gagal memuat dokumen');
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan saat memuat dokumen');
            } finally {
                this.loadingDocuments = false;
            }
        },

        closeDocumentsModal() {
            this.showModal = false;
        },

        openVerifyModal(documentId) {
            this.currentDocumentId = documentId;
            this.verifyForm = { status: '', notes: '' };
            this.showVerifyModal = true;
        },

        async submitVerification() {
            if (!this.verifyForm.status) return;
            this.loading = true;
            try {
                const res = await fetch(`/admin/virtual-office/documents/${this.currentDocumentId}/verify`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                    body: JSON.stringify(this.verifyForm)
                });
                const data = await res.json();
                if (data.success) {
                    this.showVerifyModal = false;
                    await this.showDocuments(this.transactionId, this.transactionName);
                } else {
                    alert(data.message || 'Gagal memverifikasi dokumen');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan saat verifikasi');
            } finally {
                this.loading = false;
            }
        },

        async verifyDocument(documentId) {
            if (!confirm('Setujui dokumen ini?')) return;
            this.loading = true;
            try {
                const res = await fetch(`/admin/virtual-office/documents/${documentId}/verify`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                    body: JSON.stringify({ status: 'verified', notes: '' })
                });
                const data = await res.json();
                if (data.success) await this.showDocuments(this.transactionId, this.transactionName);
                else alert(data.message || 'Gagal memverifikasi dokumen');
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan');
            } finally {
                this.loading = false;
            }
        },

        async rejectDocument(documentId) {
            const notes = prompt('Alasan penolakan:');
            if (!notes) return;
            this.loading = true;
            try {
                const res = await fetch(`/admin/virtual-office/documents/${documentId}/verify`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                    body: JSON.stringify({ status: 'rejected', notes })
                });
                const data = await res.json();
                if (data.success) await this.showDocuments(this.transactionId, this.transactionName);
                else alert(data.message || 'Gagal menolak dokumen');
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush

@push('styles')
<style>
    .verify-modal-panel {
        width: 288px !important;
        max-width: 288px !important;
        min-width: unset !important;
        flex: unset !important;
    }
</style>
@endpush
