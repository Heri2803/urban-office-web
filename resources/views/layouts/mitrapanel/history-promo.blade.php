@extends('layouts.mitrapanel')

@section('title', 'History Promo')

@section('page-title', 'History Promo')
@section('page-subtitle', 'Lihat performa promo di semua lokasi')

@push('styles')
<style>
    .promo-card-hover {
        transition: all 0.3s ease;
    }
    .promo-card-hover:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="historyPromoData()">

    {{-- Header dengan Export --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Riwayat Promo</h2>
            <p class="text-sm text-gray-600 mt-1">Analisis dampak promo terhadap transaksi</p>
        </div>

        {{-- Export Buttons --}}
        <div class="flex items-center space-x-2">
            <button @click="exportPDF" 
                    type="button"
                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"/>
                </svg>
                Export PDF
            </button>
            <button @click="exportExcel" 
                    type="button"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            
            {{-- Filter Lokasi --}}
            <div class="relative">
                <label class="block text-xs font-medium text-gray-700 mb-1">Lokasi</label>
                <select x-model="filters.location" 
                        @change="applyFilters"
                        class="w-full px-2 py-1.5 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="all">Semua Lokasi</option>
                    <option value="jakarta-pusat">Jakarta Pusat</option>
                    <option value="surabaya">Surabaya</option>
                    <option value="bandung">Bandung</option>
                </select>
            </div>

            {{-- Filter Status --}}
            <div class="relative">
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select x-model="filters.status" 
                        @change="applyFilters"
                        class="w-full px-2 py-1.5 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="all">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="ended">Berakhir</option>
                    <option value="upcoming">Akan Datang</option>
                </select>
            </div>

            {{-- Filter Layanan --}}
            <div class="relative">
                <label class="block text-xs font-medium text-gray-700 mb-1">Layanan</label>
                <select x-model="filters.service" 
                        @change="applyFilters"
                        class="w-full px-2 py-1.5 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="all">Semua Layanan</option>
                    <option value="all-services">All Services</option>
                    <option value="private-office">Private Office</option>
                    <option value="virtual-office">Virtual Office</option>
                    <option value="coworking-space">Coworking Space</option>
                    <option value="meeting-room">Meeting Room</option>
                    <option value="event-space">Event Space</option>
                </select>
            </div>

            {{-- Search --}}
            <div class="relative">
                <label class="block text-xs font-medium text-gray-700 mb-1">Cari Promo</label>
                <input type="text" 
                    x-model="filters.search" 
                    @input="applyFilters"
                    placeholder="Nama atau kode promo..."
                    class="w-full px-2 py-1.5 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 lg:gap-6">
    
        {{-- Total Promo --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-sm p-4 text-white relative">
            <div class="absolute top-3 right-3 bg-white bg-opacity-20 p-1.5 rounded-lg">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="#FFA500" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                </svg>
            </div>
            <div class="pr-10">
                <p class="text-sm font-medium opacity-90">Total Promo</p>
                <p class="text-lg sm:text-xl lg:text-2xl font-bold mt-1" x-text="summary.totalPromos"></p>
                <p class="text-xs opacity-75 mt-0.5">Promo yang pernah berjalan</p>
            </div>
        </div>

        {{-- Promo Aktif --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-sm p-4 text-white relative">
            <div class="absolute top-3 right-3 bg-white bg-opacity-20 p-1.5 rounded-lg">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="#FFA500" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="pr-10">
                <p class="text-sm font-medium opacity-90">Promo Aktif</p>
                <p class="text-lg sm:text-xl lg:text-2xl font-bold mt-1" x-text="summary.activePromos"></p>
                <p class="text-xs opacity-75 mt-0.5">Sedang berjalan</p>
            </div>
        </div>

        {{-- Rata-rata Dampak --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-sm p-4 text-white relative">
            <div class="absolute top-3 right-3 bg-white bg-opacity-20 p-1.5 rounded-lg">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="#FFA500" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="pr-10">
                <p class="text-sm font-medium opacity-90">Rata-rata Dampak</p>
                <p class="text-lg sm:text-xl lg:text-2xl font-bold mt-1" x-text="summary.avgImpact + '%'"></p>
                <p class="text-xs opacity-75 mt-0.5">Peningkatan transaksi</p>
            </div>
        </div>

        {{-- Best Promo --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-sm p-4 text-white relative">
            <div class="absolute top-3 right-3 bg-white bg-opacity-20 p-1.5 rounded-lg">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="#FFA500" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            </div>
            <div class="pr-10">
                <p class="text-sm font-medium opacity-90">Promo Terbaik</p>
                <p class="text-base sm:text-lg font-semibold truncate mt-1" x-text="summary.bestPromo.name"></p>
                <p class="text-xs opacity-75 mt-0.5" x-text="'+' + summary.bestPromo.impact + '% impact'"></p>
            </div>
        </div>

    </div>

    {{-- Table Section --}}
    {{-- Header Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-6 mb-4 md:mb-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Promo</h3>
            <span class="text-sm text-gray-500" x-text="filteredPromos.length + ' promo'"></span>
        </div>
    </div>

    {{-- Promo Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
        <template x-for="promo in filteredPromos" :key="promo.id">
            {{-- Card Wrapper (Tidak ada perubahan, hanya untuk konteks) --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300 flex flex-col">
                
                {{-- Card Header: Mengubah p-2.5 menjadi p-4 untuk padding yang lebih besar --}}
                <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-purple-50">
                    <div class="flex items-start space-x-2 mb-2">
                        <span class="text-xl flex-shrink-0">📢</span>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-semibold text-gray-900 mb-1 line-clamp-2 leading-snug" x-text="promo.name"></h4>
                            <code class="inline-block px-1.5 py-0.5 bg-white text-gray-800 text-xs font-mono rounded border border-gray-200" x-text="promo.code"></code>
                        </div>
                    </div>
                    
                    {{-- Status Badge (Tidak ada perubahan di sini) --}}
                    <div class="flex justify-end">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                                :class="{
                                    'bg-green-100 text-green-800': promo.status === 'active',
                                    'bg-gray-100 text-gray-800': promo.status === 'ended',
                                    'bg-blue-100 text-blue-800': promo.status === 'upcoming'
                                }">
                            <span class="w-2 h-2 rounded-full mr-1"
                                    :class="{
                                        'bg-green-500 animate-pulse': promo.status === 'active',
                                        'bg-gray-400': promo.status === 'ended',
                                        'bg-blue-500': promo.status === 'upcoming'
                                    }">
                            </span>
                            <span x-text="promo.status === 'active' ? 'Aktif' : promo.status === 'ended' ? 'Berakhir' : 'Akan Datang'"></span>
                        </span>
                    </div>
                </div>

                {{-- Card Body: Mengubah p-2.5 space-y-2.5 menjadi p-4 space-y-4 untuk padding dan jarak yang lebih lega --}}
                <div class="p-4 space-y-4">
                    
                    {{-- Row 1: Service & Location --}}
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Service Type --}}
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 block">Layanan</label>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium leading-tight"
                                    :class="{
                                        'bg-blue-100 text-blue-800': promo.service === 'Private Office',
                                        'bg-purple-100 text-purple-800': promo.service === 'Virtual Office',
                                        'bg-green-100 text-green-800': promo.service === 'Coworking Space',
                                        'bg-orange-100 text-orange-800': promo.service === 'Meeting Room',
                                        'bg-pink-100 text-pink-800': promo.service === 'Event Space',
                                        'bg-gray-100 text-gray-800': promo.service === 'All Services'
                                    }"
                                    x-text="promo.service">
                            </span>
                        </div>

                        {{-- Location --}}
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 block">Lokasi</label>
                            <div class="flex items-center text-sm text-gray-900">
                                <svg class="w-3 h-3 mr-1 text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium truncate text-xs" x-text="promo.location"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Period & Impact --}}
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Period --}}
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 block">Periode</label>
                            <div class="text-xs text-gray-700 space-y-0.5">
                                <div class="flex items-center">
                                    <svg class="w-3 h-3 text-gray-400 flex-shrink-0 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-medium" x-text="promo.startDate"></span>
                                </div>
                                <div class="flex items-center pl-4">
                                    <span class="text-gray-400 mr-1">s/d</span>
                                    <span class="font-medium" x-text="promo.endDate"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Impact --}}
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1 block">Dampak</label>
                            <template x-if="promo.impact !== null">
                                <div class="inline-flex items-center px-2 py-1 rounded"
                                        :class="{
                                            'bg-green-50': promo.impact > 30,
                                            'bg-blue-50': promo.impact > 0 && promo.impact <= 30,
                                            'bg-red-50': promo.impact < 0
                                        }">
                                    <svg class="w-3 h-3 mr-0.5"
                                            :class="{
                                                'text-green-500': promo.impact > 0,
                                                'text-red-500': promo.impact < 0
                                            }"
                                            fill="currentColor" viewBox="0 0 20 20">
                                        <path x-show="promo.impact > 0" fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                        <path x-show="promo.impact < 0" fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-xs font-bold"
                                            :class="{
                                                'text-green-600': promo.impact > 30,
                                                'text-blue-600': promo.impact > 0 && promo.impact <= 30,
                                                'text-red-600': promo.impact < 0
                                            }"
                                            x-text="(promo.impact > 0 ? '+' : '') + promo.impact + '%'">
                                    </span>
                                </div>
                            </template>
                            <template x-if="promo.impact === null">
                                <div class="inline-flex items-center px-2 py-1 bg-gray-50 rounded">
                                    <span class="text-xs text-gray-500 font-medium">N/A</span>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>

                {{-- Card Footer: Mengubah px-2.5 pb-2.5 menjadi p-4 untuk padding yang lebih seragam --}}
                <div class="p-4 pt-0 mt-auto">
                    <button @click="viewDetail(promo)" 
                            type="button"
                            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                        </svg>
                        Lihat Detail
                    </button>
                </div>

            </div>
        </template>
    </div>

    {{-- Empty State --}}
    <div x-show="filteredPromos.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada promo ditemukan</h3>
        <p class="text-sm text-gray-500">Coba ubah filter atau kata kunci pencarian</p>
    </div>

    {{-- Detail Modal --}}
    <div x-show="showModal" 
        x-cloak
        @click.self="showModal = false"
        class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
        
        {{-- Container Modal: max-w-lg diubah menjadi max-w-md, rounded-xl dipertahankan --}}
        <div @click.away="showModal = false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            
            <template x-if="selectedPromo">
                <div>
                    {{-- Modal Header: px-5 py-3 diubah menjadi px-4 py-3 --}}
                    <div class="sticky top-0 bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between rounded-t-xl z-10">
                        <div class="flex items-center space-x-2">
                            <span class="text-xl">📢</span>
                            <div>
                                {{-- Ukuran teks judul dikecilkan --}}
                                <h3 class="text-base font-semibold text-gray-900 leading-tight" x-text="selectedPromo.name"></h3>
                                <code class="text-xs text-gray-600 font-mono" x-text="selectedPromo.code"></code>
                            </div>
                        </div>
                        {{-- Tombol close sedikit dikecilkan --}}
                        <button @click="showModal = false" 
                                type="button"
                                class="p-1 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Body: px-5 py-5 space-y-5 diubah menjadi px-4 py-4 space-y-4 --}}
                    <div class="px-4 py-4 space-y-4">
                        
                        {{-- Info Grid --}}
                        <div class="grid grid-cols-2 gap-3">
                            {{-- Padding di dalam card info dikurangi menjadi p-3 --}}
                            <div class="bg-gray-50 rounded-lg p-3">
                                <span class="text-xs text-gray-500 font-medium">Lokasi</span>
                                <p class="text-sm font-semibold text-gray-900 mt-1" x-text="selectedPromo.location"></p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <span class="text-xs text-gray-500 font-medium">Periode Mulai</span>
                                <p class="text-sm font-semibold text-gray-900 mt-1" x-text="selectedPromo.startDate"></p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <span class="text-xs text-gray-500 font-medium">Periode Berakhir</span>
                                <p class="text-sm font-semibold text-gray-900 mt-1" x-text="selectedPromo.endDate"></p>
                            </div>
                        </div>

                        {{-- Status Badge: Padding disesuaikan --}}
                        <div class="flex items-center justify-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                                    :class="{
                                        'bg-green-100 text-green-800': selectedPromo.status === 'active',
                                        'bg-gray-100 text-gray-800': selectedPromo.status === 'ended',
                                        'bg-blue-100 text-blue-800': selectedPromo.status === 'upcoming'
                                    }">
                                <span class="w-2 h-2 rounded-full mr-1"
                                        :class="{
                                            'bg-green-500 animate-pulse': selectedPromo.status === 'active',
                                            'bg-gray-400': selectedPromo.status === 'ended',
                                            'bg-blue-500': selectedPromo.status === 'upcoming'
                                        }">
                                </span>
                                <span class="text-xs" x-text="selectedPromo.status === 'active' ? 'Promo Aktif' : selectedPromo.status === 'ended' ? 'Promo Berakhir' : 'Akan Datang'"></span>
                            </span>
                        </div>

                        {{-- Impact Section --}}
                        <template x-if="selectedPromo.impact !== null">
                            <div>
                                <div class="border-t border-gray-200 pt-4">
                                    {{-- Ukuran ikon dan teks sub-judul disesuaikan --}}
                                    <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                                        </svg>
                                        Dampak Transaksi
                                    </h4>

                                    {{-- Before Period --}}
                                    <div class="mb-3 bg-gray-50 rounded-lg p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-700">📊 Sebelum Promo</span>
                                            <span class="text-xs text-gray-500">(Pembanding)</span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3 mt-3">
                                            <div>
                                                <p class="text-xs text-gray-500">Transaksi</p>
                                                <p class="text-base font-bold text-gray-900" x-text="selectedPromo.impact.before.transactions"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Revenue</p>
                                                <p class="text-base font-bold text-gray-900" x-text="formatCurrency(selectedPromo.impact.before.revenue)"></p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- During Period (Detail di dalamnya disesuaikan) --}}
                                    <div class="mb-3 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-3 border-2 border-blue-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-blue-900">🎯 Selama Promo Berjalan</span>
                                            <span class="text-xs text-blue-700">(Periode)</span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3 mt-3">
                                            <div>
                                                <p class="text-xs text-blue-700">Transaksi</p>
                                                <div class="flex items-baseline space-x-1">
                                                    <p class="text-base font-bold text-blue-900" x-text="selectedPromo.impact.during.transactions"></p>
                                                    <span class="text-xs font-semibold"
                                                            :class="selectedPromo.impact.transactionIncrease > 0 ? 'text-green-600' : 'text-red-600'"
                                                            x-text="'(' + (selectedPromo.impact.transactionIncrease > 0 ? '+' : '') + selectedPromo.impact.transactionIncrease + '%)'">
                                                    </span>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="text-xs text-blue-700">Revenue</p>
                                                <div class="flex items-baseline space-x-1">
                                                    <p class="text-base font-bold text-blue-900" x-text="formatCurrency(selectedPromo.impact.during.revenue)"></p>
                                                    <span class="text-xs font-semibold"
                                                            :class="selectedPromo.impact.revenueIncrease > 0 ? 'text-green-600' : 'text-red-600'"
                                                            x-text="'(' + (selectedPromo.impact.revenueIncrease > 0 ? '+' : '') + selectedPromo.impact.revenueIncrease + '%)'">
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-3 pt-3 border-t border-blue-200">
                                            <p class="text-xs text-blue-700">Penggunaan Promo</p>
                                            <p class="text-sm font-semibold text-blue-900 mt-1">
                                                <span x-text="selectedPromo.impact.during.promoUsage"></span>
                                                <span class="text-xs font-normal">
                                                    dari <span x-text="selectedPromo.impact.during.transactions"></span> transaksi
                                                    (<span x-text="Math.round((selectedPromo.impact.during.promoUsage / selectedPromo.impact.during.transactions) * 100)"></span>%)
                                                </span>
                                            </p>
                                        </div>
                                    </div>

                                    {{-- After Period --}}
                                    <template x-if="selectedPromo.impact.after !== null">
                                        <div class="bg-purple-50 rounded-lg p-3">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm font-medium text-purple-900">📈 Setelah Promo Berakhir</span>
                                                <span class="text-xs text-purple-700">(Lasting effect)</span>
                                            </div>
                                            <div class="grid grid-cols-2 gap-3 mt-3">
                                                <div>
                                                    <p class="text-xs text-purple-700">Transaksi</p>
                                                    <div class="flex items-baseline space-x-1">
                                                        <p class="text-base font-bold text-purple-900" x-text="selectedPromo.impact.after.transactions"></p>
                                                        <span class="text-xs font-semibold"
                                                                :class="selectedPromo.impact.afterEffect > 0 ? 'text-green-600' : 'text-orange-600'"
                                                                x-text="'(' + (selectedPromo.impact.afterEffect > 0 ? '+' : '') + selectedPromo.impact.afterEffect + '%)'">
                                                        </span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-purple-700">Revenue</p>
                                                    <p class="text-base font-bold text-purple-900" x-text="formatCurrency(selectedPromo.impact.after.revenue)"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                </div>

                                {{-- Insight Box: Padding dan ukuran ikon disesuaikan --}}
                                <div class="mt-4 rounded-lg p-3"
                                        :class="{
                                            'bg-green-50 border border-green-200': selectedPromo.impact.transactionIncrease > 30,
                                            'bg-blue-50 border border-blue-200': selectedPromo.impact.transactionIncrease > 0 && selectedPromo.impact.transactionIncrease <= 30,
                                            'bg-red-50 border border-red-200': selectedPromo.impact.transactionIncrease < 0
                                        }">
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 mt-0.5 mr-2 flex-shrink-0"
                                                :class="{
                                                    'text-green-600': selectedPromo.impact.transactionIncrease > 30,
                                                    'text-blue-600': selectedPromo.impact.transactionIncrease > 0 && selectedPromo.impact.transactionIncrease <= 30,
                                                    'text-red-600': selectedPromo.impact.transactionIncrease < 0
                                                }"
                                                fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium"
                                                :class="{
                                                    'text-green-900': selectedPromo.impact.transactionIncrease > 30,
                                                    'text-blue-900': selectedPromo.impact.transactionIncrease > 0 && selectedPromo.impact.transactionIncrease <= 30,
                                                    'text-red-900': selectedPromo.impact.transactionIncrease < 0
                                                }">
                                                Insight Promo
                                            </p>
                                            <p class="text-sm mt-1"
                                                :class="{
                                                    'text-green-700': selectedPromo.impact.transactionIncrease > 30,
                                                    'text-blue-700': selectedPromo.impact.transactionIncrease > 0 && selectedPromo.impact.transactionIncrease <= 30,
                                                    'text-red-700': selectedPromo.impact.transactionIncrease < 0
                                                }"
                                                x-text="getInsight(selectedPromo)">
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </template>

                        {{-- No Data State: Ukuran ikon disesuaikan --}}
                        <template x-if="selectedPromo.impact === null">
                            <div class="text-center py-6">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <p class="text-sm text-gray-500">Belum ada data dampak transaksi</p>
                                <p class="text-xs text-gray-400 mt-1">Data akan tersedia setelah promo berjalan</p>
                            </div>
                        </template>

                    </div>

                    {{-- Modal Footer: px-5 py-3 diubah menjadi px-4 py-3. Tombol sedikit lebih kecil --}}
                    <div class="sticky bottom-0 bg-gray-50 px-4 py-3 flex justify-end space-x-2 border-t border-gray-200 rounded-b-xl">
                        <button @click="showModal = false" 
                                type="button"
                                class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                            Tutup
                        </button>
                        <button @click="exportDetailPDF(selectedPromo)" 
                                type="button"
                                class="px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                            Export Detail PDF
                        </button>
                    </div>
                </div>
            </template>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function historyPromoData() {
    return {
        // State
        showModal: false,
        selectedPromo: null,
        loading: false,
        
        // Filters
        filters: {
            location: 'all',
            status: 'all',
            service: 'all',
            search: ''
        },
        
        // Data
        promos: [],
        summary: {
            totalPromos: 0,
            activePromos: 0,
            avgImpact: 0,
            bestPromo: { name: '-', impact: 0 }
        },
        
        // Computed
        get filteredPromos() {
            return this.promos;
        },
        
        // Methods
        async init() {
            await this.loadPromoHistory();
        },
        
        async loadPromoHistory() {
            this.loading = true;
            try {
                const params = new URLSearchParams(this.filters);
                const response = await fetch(`/mitrapanel/api/promo-history?${params}`);
                const result = await response.json();
                
                if (result.success) {
                    this.promos = result.data;
                    this.summary = result.summary;
                } else {
                    console.error('Failed to load promo history:', result.message);
                    alert('Gagal memuat data promo: ' + result.message);
                }
            } catch (error) {
                console.error('Network error:', error);
                alert('Error jaringan saat memuat data promo');
            } finally {
                this.loading = false;
            }
        },
        
        applyFilters() {
            this.loadPromoHistory();
        },
        
        viewDetail(promo) {
            this.selectedPromo = promo;
            this.showModal = true;
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },
        
        getInsight(promo) {
            if (!promo.impact) return 'Belum ada data dampak transaksi';
            
            const impact = promo.impact.transactionIncrease;
            if (impact > 50) {
                return `Promo ini sangat sukses dengan peningkatan transaksi ${impact}%. Sangat disarankan untuk menjalankan promo serupa di periode mendatang.`;
            } else if (impact > 30) {
                return `Promo berhasil meningkatkan transaksi sebesar ${impact}%. Promo ini efektif dan bisa dipertimbangkan untuk dijalankan kembali.`;
            } else if (impact > 10) {
                return `Promo menghasilkan dampak positif dengan peningkatan ${impact}%. Performa cukup baik untuk kategori promo ini.`;
            } else if (impact > 0) {
                return `Promo memberikan dampak marginal dengan peningkatan ${impact}%. Pertimbangkan untuk meningkatkan benefit atau durasi promo.`;
            } else {
                return `Promo mengalami penurunan transaksi sebesar ${Math.abs(impact)}%. Evaluasi strategi promo dan timing pelaksanaan diperlukan.`;
            }
        },
        
        async exportPDF() {
            try {
                const response = await fetch('/mitrapanel/api/export/promo-history', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        ...this.filters,
                        type: 'pdf'
                    })
                });
                
                const result = await response.json();
                if (result.success) {
                    alert(`Export berhasil! ${result.data_count} data promo akan di-export.`);
                } else {
                    alert('Export gagal: ' + result.message);
                }
            } catch (error) {
                console.error('Export error:', error);
                alert('Error saat export data');
            }
        },
        
        async exportExcel() {
            try {
                const response = await fetch('/mitrapanel/api/export/promo-history', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        ...this.filters,
                        type: 'excel'
                    })
                });
                
                const result = await response.json();
                if (result.success) {
                    alert(`Export berhasil! ${result.data_count} data promo akan di-export.`);
                } else {
                    alert('Export gagal: ' + result.message);
                }
            } catch (error) {
                console.error('Export error:', error);
                alert('Error saat export data');
            }
        },
        
        exportDetailPDF(promo) {
            alert(`Export detail PDF untuk: ${promo.name} (${promo.code})`);
            // TODO: Implement detail PDF export
        }
    }
}
</script>
@endpush
