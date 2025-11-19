@extends('layouts.mitrapanel')

@section('title', 'Detail Lokasi')
@section('page-title', 'Detail Lokasi')
@section('page-subtitle', 'Transaksi per layanan')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    
    .service-card {
        transition: all 0.3s ease;
    }
    .service-card:hover {
        transform: translateY(-4px);
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .loading-overlay {
        background: rgba(255, 255, 255, 0.9);
    }
    
    /* Modal Animations */
    .modal-backdrop {
        backdrop-filter: blur(4px);
    }
</style>
@endpush

@section('content')
<div class="space-y-6" 
     x-data="lokasiDetailData()" 
     x-cloak
     x-init="init()">

    {{-- Loading Overlay --}}
    <div x-show="loading" 
         class="fixed inset-0 loading-overlay flex items-center justify-center z-50">
        <div class="text-center">
            <svg class="animate-spin h-12 w-12 text-orange-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-4 text-gray-600 font-medium">Memuat data...</p>
        </div>
    </div>

    {{-- Modal Detail Service --}}
    <div x-show="showModal" 
         x-cloak
         @click.self="closeModal()"
         class="fixed inset-0 z-50 overflow-y-auto modal-backdrop bg-black bg-opacity-50"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showModal"
                 @click.stop
                 class="relative inline-block w-full max-w-2xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                {{-- Modal Header --}}
                <div class="relative p-6 border-b border-gray-200"
                     :class="{
                         'bg-gradient-to-r from-blue-50 to-blue-100': selectedService?.type === 'private-office',
                         'bg-gradient-to-r from-purple-50 to-purple-100': selectedService?.type === 'virtual-office',
                         'bg-gradient-to-r from-green-50 to-green-100': selectedService?.type === 'sharing-room',
                         'bg-gradient-to-r from-orange-50 to-orange-100': selectedService?.type === 'meeting-room',
                         'bg-gradient-to-r from-pink-50 to-pink-100': selectedService?.type === 'event-space',
                         'bg-gradient-to-r from-indigo-50 to-indigo-100': selectedService?.type === 'coworking-space'
                     }">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="text-4xl" x-text="selectedService?.icon"></div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900" x-text="selectedService?.name"></h3>
                                <p class="text-sm text-gray-600 mt-1">Detail Transaksi Layanan</p>
                            </div>
                        </div>
                        <button @click="closeModal()" 
                                class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    {{-- Stats Cards --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        {{-- Total Transaksi --}}
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-5 rounded-xl border border-blue-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-blue-900">Total Transaksi</span>
                                <div class="p-2 bg-blue-200 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-3xl font-bold text-blue-900" x-text="selectedService?.transactions"></p>
                            <p class="text-xs text-blue-700 mt-1">Transaksi berhasil</p>
                        </div>

                        {{-- Total Nominal --}}
                        <div class="bg-gradient-to-br from-green-50 to-green-100 p-5 rounded-xl border border-green-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-green-900">Total Nominal</span>
                                <div class="p-2 bg-green-200 rounded-lg">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-2xl font-bold text-green-900" x-text="formatCurrency(selectedService?.total)"></p>
                            <p class="text-xs text-green-700 mt-1">Total pemasukan</p>
                        </div>
                    </div>

                    {{-- Detail Statistics --}}
                    <div class="bg-gray-50 rounded-xl p-5 mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                            Statistik Detail
                        </h4>
                        
                        <div class="space-y-4">
                            {{-- Rata-rata --}}
                            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-blue-100 rounded-lg">
                                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11 4a1 1 0 10-2 0v4a1 1 0 102 0V7zm-3 1a1 1 0 10-2 0v3a1 1 0 102 0V8zM8 9a1 1 0 00-2 0v2a1 1 0 102 0V9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Rata-rata per Transaksi</p>
                                        <p class="text-xs text-gray-500">Nilai tengah transaksi</p>
                                    </div>
                                </div>
                                <span class="text-lg font-bold text-gray-900" x-text="formatCurrency(selectedService?.average)"></span>
                            </div>

                            {{-- Tertinggi --}}
                            <div class="flex items-center justify-between py-3 border-b border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-orange-100 rounded-lg">
                                        <svg class="w-4 h-4 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Transaksi Tertinggi</p>
                                        <p class="text-xs text-gray-500">Nilai maksimal</p>
                                    </div>
                                </div>
                                <span class="text-lg font-bold text-gray-900" x-text="formatCurrency(selectedService?.highest)"></span>
                            </div>

                            {{-- Terendah (optional) --}}
                            <div class="flex items-center justify-between py-3">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-purple-100 rounded-lg">
                                        <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Transaksi Terendah</p>
                                        <p class="text-xs text-gray-500">Nilai minimal</p>
                                    </div>
                                </div>
                                <span class="text-lg font-bold text-gray-900" x-text="formatCurrency(selectedService?.lowest || selectedService?.average)"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Info Box --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-blue-900">Informasi Periode</p>
                                <p class="text-xs text-blue-700 mt-1">Data transaksi untuk periode <span x-text="currentPeriod === 'daily' ? 'hari ini' : currentPeriod === 'monthly' ? 'bulan ini' : 'tahun ini'"></span> di lokasi <strong x-text="currentLocation?.name"></strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-end space-x-3">
                        <button @click="closeModal()" 
                                type="button"
                                class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition">
                            Tutup
                        </button>
                        <button @click="exportServiceDetail()" 
                                type="button"
                                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            Export Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Header dengan Back Button, Location Filter & Export --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        {{-- Back Button --}}
        <a href="{{ route('mitrapanel.dashboard') }}" 
           class="inline-flex items-center text-gray-600 hover:text-gray-900 transition group">
            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="font-medium">Kembali ke Dashboard</span>
        </a>

        {{-- Location Filter & Export Buttons --}}
        <div class="flex items-center space-x-2 sm:space-x-3">
            {{-- Location Filter Dropdown --}}
            <div class="relative" @click.outside="locationFilterOpen = false">
                <button @click="locationFilterOpen = !locationFilterOpen"
                        type="button"
                        class="inline-flex items-center px-4 py-2 bg-white border-2 border-orange-500 text-orange-600 text-sm font-medium rounded-lg hover:bg-orange-50 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span x-text="getCurrentLocationName()"></span>
                    <svg class="w-4 h-4 ml-2 transition-transform" 
                         :class="locationFilterOpen ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Location Dropdown --}}
                <div x-show="locationFilterOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-80 overflow-hidden">
                    
                    {{-- Header --}}
                    <div class="px-4 py-3 bg-gradient-to-r from-orange-500 to-orange-400 border-b">
                        <h3 class="text-sm font-semibold text-white flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            Pilih Lokasi Mitra
                        </h3>
                    </div>

                    {{-- Location List --}}
                    <div class="py-2 max-h-64 overflow-y-auto custom-scrollbar">
                        <template x-for="location in availableLocations" :key="location.id">
                            <button type="button" @click="changeLocation(location)"
                               class="w-full flex items-center px-4 py-3 hover:bg-orange-50 transition border-b border-gray-50 last:border-b-0 group"
                               :class="location.id === currentLocation.id ? 'bg-orange-50' : ''">
                                <div class="flex items-center flex-1">
                                    <span class="text-lg mr-3">📍</span>
                                    <div class="text-left">
                                        <span class="text-sm font-medium text-gray-700 group-hover:text-orange-600" 
                                              x-text="location.name"></span>
                                        <p class="text-xs text-gray-500 mt-0.5" x-text="location.transactionCount + ' transaksi'"></p>
                                    </div>
                                </div>
                                <svg x-show="location.id === currentLocation.id"
                                     class="w-5 h-5 text-orange-500" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Export PDF Button --}}
            <button @click="exportPDF" 
                    type="button"
                    :disabled="loading"
                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition shadow-sm disabled:opacity-50">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"/>
                </svg>
                <span class="hidden sm:inline">PDF</span>
            </button>

            {{-- Export Excel Button --}}
            <button @click="exportExcel" 
                    type="button"
                    :disabled="loading"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition shadow-sm disabled:opacity-50">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                <span class="hidden sm:inline">Excel</span>
            </button>
        </div>
    </div>

    {{-- Filter Periode --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h3 class="text-sm font-semibold text-gray-700">Filter Periode</h3>
            
            <div class="flex items-center space-x-2 bg-gray-100 p-1 rounded-lg">
                <button @click="changePeriod('daily')"
                        type="button"
                        :disabled="loading"
                        class="px-4 py-2 rounded-md text-sm font-medium transition disabled:opacity-50"
                        :class="currentPeriod === 'daily' ? 'bg-white text-gray-900 shadow' : 'text-gray-600 hover:text-gray-900'">
                    Hari Ini
                </button>
                <button @click="changePeriod('monthly')"
                        type="button"
                        :disabled="loading"
                        class="px-4 py-2 rounded-md text-sm font-medium transition disabled:opacity-50"
                        :class="currentPeriod === 'monthly' ? 'bg-white text-gray-900 shadow' : 'text-gray-600 hover:text-gray-900'">
                    Bulan Ini
                </button>
                <button @click="changePeriod('yearly')"
                        type="button"
                        :disabled="loading"
                        class="px-4 py-2 rounded-md text-sm font-medium transition disabled:opacity-50"
                        :class="currentPeriod === 'yearly' ? 'bg-white text-gray-900 shadow' : 'text-gray-600 hover:text-gray-900'">
                    Tahun Ini
                </button>
            </div>
        </div>
    </div>

    {{-- Empty State --}}
    <div x-show="services.length === 0 && !loading" 
         class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Transaksi</h3>
        <p class="text-gray-500">Tidak ada transaksi untuk periode ini.</p>
    </div>

    {{-- Service Cards Grid --}}
    <div x-show="services.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <template x-for="service in services" :key="service.id">
            <div class="service-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                {{-- Card Header dengan Icon --}}
                <div class="p-5 border-b border-gray-100" 
                     :class="{
                         'bg-gradient-to-r from-blue-50 to-blue-100': service.type === 'private-office',
                         'bg-gradient-to-r from-purple-50 to-purple-100': service.type === 'virtual-office',
                         'bg-gradient-to-r from-green-50 to-green-100': service.type === 'sharing-room',
                         'bg-gradient-to-r from-orange-50 to-orange-100': service.type === 'meeting-room',
                         'bg-gradient-to-r from-pink-50 to-pink-100': service.type === 'event-space',
                         'bg-gradient-to-r from-indigo-50 to-indigo-100': service.type === 'coworking-space'
                     }">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="text-3xl" x-text="service.icon"></div>
                            <div>
                                <h3 class="font-semibold text-gray-800" x-text="service.name"></h3>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="service.transactions + ' transaksi'"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Body dengan Total --}}
                <div class="p-5">
                    <div class="flex items-baseline justify-between mb-4">
                        <span class="text-sm text-gray-500">Total Nominal:</span>
                        <span class="text-xl font-bold text-gray-900" x-text="formatCurrency(service.total)"></span>
                    </div>

                    {{-- Stats Mini --}}
                    <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100">
                        <div class="text-center">
                            <p class="text-xs text-gray-500">Rata-rata</p>
                            <p class="text-sm font-semibold text-gray-700 mt-1" x-text="formatCurrency(service.average)"></p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500">Tertinggi</p>
                            <p class="text-sm font-semibold text-gray-700 mt-1" x-text="formatCurrency(service.highest)"></p>
                        </div>
                    </div>

                    {{-- View Detail Button --}}
                    <button type="button" 
                            @click="viewServiceDetail(service)"
                            class="w-full mt-4 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                        Lihat Detail →
                    </button>
                </div>
            </div>
        </template>
    </div>

    {{-- Summary Section --}}
    <div x-show="services.length > 0" class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
                Ringkasan Keuangan
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                {{-- Total Omzet --}}
                <div class="bg-white p-5 rounded-xl border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-600">Total Omzet</span>
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(summary.totalOmzet)"></p>
                    <p class="text-xs text-gray-500 mt-2" x-text="currentPeriod === 'daily' ? 'Hari ini' : currentPeriod === 'monthly' ? 'Bulan ini' : 'Tahun ini'"></p>
                </div>

                {{-- Total Pajak --}}
                <div class="bg-white p-5 rounded-xl border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-600">Pajak (10%)</span>
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(summary.totalPajak)"></p>
                    <p class="text-xs text-gray-500 mt-2">Perlu dilaporkan</p>
                </div>

                {{-- Status Pelaporan --}}
                <div class="bg-white p-5 rounded-xl border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-600">Status Pelaporan</span>
                        <div class="p-2 rounded-lg"
                             :class="summary.status === 'reported' ? 'bg-green-100' : 'bg-red-100'">
                            <svg class="w-5 h-5" :class="summary.status === 'reported' ? 'text-green-600' : 'text-red-600'" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xl font-bold mb-1"
                       :class="summary.status === 'reported' ? 'text-green-600' : 'text-red-600'"
                       x-text="summary.status === 'reported' ? 'Sudah Dilaporkan' : 'Belum Dilaporkan'">
                    </p>
                    <p class="text-xs text-gray-500 mt-2" x-text="summary.reportDate ? 'Tanggal: ' + summary.reportDate : 'Segera laporkan'"></p>
                    
                    {{-- Action Button --}}
                    <button type="button"
                            x-show="summary.status !== 'reported'"
                            @click="reportTax"
                            :disabled="loading"
                            class="w-full mt-3 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition disabled:opacity-50">
                        Laporkan Sekarang
                    </button>
                </div>
            </div>

            {{-- Additional Info --}}
            <div class="mt-6 p-4 bg-blue-50 border border-blue-100 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-900">Informasi Pajak</p>
                        <p class="text-xs text-blue-700 mt-1">Perhitungan pajak berdasarkan tarif 10% dari total omzet. Pastikan untuk melaporkan pajak sebelum tanggal 15 setiap bulannya.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function lokasiDetailData() {
    console.log("ROUTE GET DATA:", "{{ route('mitrapanel.lokasi.getData') }}");
    return {
        
        // State
        loading: false,
        locationFilterOpen: false,
        currentPeriod: '{{ $period }}',
        showModal: false,
        selectedService: null,
        
        // Data dari Laravel
        availableLocations: @json($locations),
        currentLocation: @json($selectedLocation),
        
        // Data dinamis
        services: [],
        summary: {
            totalOmzet: 0,
            totalPajak: 0,
            status: 'not-reported',
            reportDate: null
        },
        

        // Format angka jadi rupiah
        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
        },

        // Ambil nama lokasi aktif
        getCurrentLocationName() {
            return this.currentLocation ? this.currentLocation.name : 'Pilih Lokasi';
        },

        // Di method fetchData(), tambahkan handling untuk empty data
        async fetchData() {
            this.loading = true;
            
            try {
                const response = await fetch(`{{ route('mitrapanel.lokasi.getData') }}?location_id=${this.currentLocation.id}&period=${this.currentPeriod}`);
                const data = await response.json();
                
                if (data.success) {
                    this.services = data.services;
                    this.summary = data.summary;
                    
                    // ✅ HANDLE EMPTY DATA: Jika tidak ada services, tetap tampilkan UI kosong
                    if (data.services.length === 0) {
                        console.log('📭 No transaction data found for this period');
                        // Biarkan empty state menangani tampilan
                    }
                } else {
                    // Jika API return error, mungkin karena location tidak valid
                    console.error('API Error:', data.message);
                    // Tidak perlu redirect, biarkan user tetap di halaman dengan empty state
                }
            } catch (error) {
                console.error('Error fetching data:', error);
                // Jangan alert, biarkan empty state menangani
            } finally {
                this.loading = false;
            }
        },

        // Ganti periode
        async changePeriod(period) {
            if (this.currentPeriod === period || this.loading) return;
            
            this.currentPeriod = period;
            
            // Update URL tanpa reload
            const url = new URL(window.location);
            url.searchParams.set('period', period);
            window.history.pushState({}, '', url);
            
            await this.fetchData();
        },

        // Ganti lokasi
        async changeLocation(location) {
            if (this.currentLocation.id === location.id || this.loading) return;
            
            this.currentLocation = location;
            this.locationFilterOpen = false;
            
            // Update URL tanpa reload
            const url = new URL(window.location);
            url.searchParams.set('location', location.slug);
            window.history.pushState({}, '', url);
            
            await this.fetchData();
        },

        // Lihat detail layanan (buka modal)
        viewServiceDetail(service) {
            this.selectedService = service;
            this.showModal = true;
            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';
        },

        // Tutup modal
        closeModal() {
            this.showModal = false;
            this.selectedService = null;
            // Restore body scroll
            document.body.style.overflow = '';
        },

        // Export detail service
        async exportServiceDetail() {
            if (!this.selectedService || this.loading) return;

            this.loading = true;

            try {
                console.log('Exporting service detail...', {
                    location_id: this.currentLocation.id,
                    period: this.currentPeriod,
                    service_name: this.selectedService.name
                });

                // Buat URL baru
                const url = `{{ route('mitrapanel.lokasi.exportServiceDetail') }}?location_id=${this.currentLocation.id}&period=${this.currentPeriod}&service_name=${encodeURIComponent(this.selectedService.name)}`;

                console.log('Export URL:', url);

                // Download
                window.location.href = url;

                setTimeout(() => {
                    this.closeModal();
                }, 800);

            } catch (error) {
                console.error('Error exporting detail:', error);
                alert('Gagal export detail layanan: ' + error.message);
            } finally {
                this.loading = false;
            }
        },

        // Laporkan pajak
        async reportTax() {
            if (this.loading) return;
            
            if (!confirm('Apakah Anda yakin ingin melaporkan pajak untuk periode ini?')) {
                return;
            }
            
            this.loading = true;
            
            try {
                const response = await fetch('{{ route('mitrapanel.lokasi.reportTax') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        location_id: this.currentLocation.id,
                        period: this.currentPeriod,
                        total_omzet: this.summary.totalOmzet,
                        total_pajak: this.summary.totalPajak
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.summary.status = 'reported';
                    this.summary.reportDate = data.reportDate;
                    alert('Pajak berhasil dilaporkan!');
                } else {
                    alert('Gagal melaporkan pajak. Silakan coba lagi.');
                }
            } catch (error) {
                console.error('Error reporting tax:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                this.loading = false;
            }
        },

        // Export PDF - FIXED VERSION
        // ✅ SIMPLE VERSION - TANPA BLOB PROCESSING
        exportPDF() {
            this.loading = true;
            
            const locationId = this.currentLocation?.id || 0;
            const url = `/mitrapanel/lokasi/export-pdf?location_id=${locationId}&period=${this.currentPeriod}`;
            
            console.log('🚀 Opening PDF URL:', url);
            
            // Biarkan browser handle download PDF secara native
            window.open(url, '_blank');
            
            this.loading = false;
        },

        // Export Excel
        async exportExcel() {
            try {
                this.loading = true;
                
                console.log('📍 Current Location:', this.currentLocation);
                console.log('📅 Current Period:', this.currentPeriod);
                
                // ✅ GUNAKAN currentLocation BUKAN selectedLocation
                const locationId = this.currentLocation?.id || 0;
                
                const params = new URLSearchParams({
                    location_id: locationId,
                    period: this.currentPeriod
                });

                const url = `/mitrapanel/lokasi/export-excel?${params}`;
                
                console.log('📤 Export Excel URL:', url);
                
                // ✅ Download langsung
                window.location.href = url;
                
            } catch (error) {
                console.error('Error exporting Excel:', error);
                alert('Gagal mengekspor Excel: ' + error.message);
            } finally {
                this.loading = false;
            }
        },

        // Inisialisasi
        async init() {
            await this.fetchData();
            
            // Handle ESC key to close modal
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.showModal) {
                    this.closeModal();
                }
            });
        }
    }
}
</script>
@endpush