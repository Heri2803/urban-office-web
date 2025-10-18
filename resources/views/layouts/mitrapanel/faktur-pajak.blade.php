@extends('layouts.mitrapanel')

@section('title', 'Faktur Pajak')

@section('page-title', 'Faktur Pajak')
@section('page-subtitle', 'Download dan kelola faktur pajak per lokasi')

@section('content')
<div class="space-y-6" x-data="fakturPajakData()">

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Faktur Pajak</h2>
            <p class="text-sm text-gray-600 mt-1">Kelola dan download faktur pajak untuk pelaporan</p>
        </div>

        {{-- Download All Button --}}
        <button @click="downloadAllFaktur" 
                type="button"
                class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
            Download Semua Faktur
        </button>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            {{-- Filter Lokasi --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Lokasi</label>
                <select x-model="filters.location" 
                        @change="applyFilters"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="all">Semua Lokasi</option>
                    <option value="jakarta-pusat">Jakarta Pusat</option>
                    <option value="surabaya">Surabaya</option>
                    <option value="bandung">Bandung</option>
                </select>
            </div>

            {{-- Filter Tahun --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Tahun</label>
                <select x-model="filters.year" 
                        @change="applyFilters"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="all">Semua Tahun</option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                    <option value="2022">2022</option>
                </select>
            </div>

            {{-- Filter Status --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Status Pelaporan</label>
                <select x-model="filters.status" 
                        @change="applyFilters"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="all">Semua Status</option>
                    <option value="reported">Sudah Dilaporkan</option>
                    <option value="unreported">Belum Dilaporkan</option>
                </select>
            </div>

        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Total Faktur --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-sm p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium opacity-90">Total Faktur</span>
                <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-5 h-5" fill="#FFA500" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold" x-text="summary.totalFaktur"></p>
            <p class="text-xs opacity-75 mt-1">Faktur tersedia</p>
        </div>

        {{-- Faktur Belum Dilaporkan --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-sm p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium opacity-90">Belum Dilaporkan</span>
                <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-5 h-5" fill="#FFA500" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold" x-text="summary.unreported"></p>
            <p class="text-xs opacity-75 mt-1">Perlu segera dilaporkan</p>
        </div>

        {{-- Total Pajak Tahun Ini --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-sm p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium opacity-90">Total Pajak 2024</span>
                <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-5 h-5" fill="#FFA500" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold" x-text="formatCurrency(summary.totalTaxThisYear)"></p>
            <p class="text-xs opacity-75 mt-1">Akumulasi tahun ini</p>
        </div>

        {{-- Faktur Terbaru --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-sm p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium opacity-90">Faktur Terbaru</span>
                <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                    <svg class="w-5 h-5" fill="#FFA500" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <p class="text-xl font-bold truncate" x-text="summary.latestFaktur.period"></p>
            <p class="text-xs opacity-75 mt-1" x-text="summary.latestFaktur.location"></p>
        </div>

    </div>

    {{-- Faktur List by Location --}}
    <div class="space-y-6">
        <template x-for="location in groupedFaktur" :key="location.name">
            <div>
                {{-- Location Header Card: Tetap sama, berfungsi sebagai header grup --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800" x-text="location.name"></h3>
                                    <p class="text-xs text-gray-500 mt-0.5" x-text="location.fakturs.length + ' faktur tersedia'"></p>
                                </div>
                            </div>
                            <button @click="downloadLocationFaktur(location.slug)" 
                                    type="button"
                                    class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                Download Semua
                            </button>
                        </div>
                    </div>
                </div> {{-- End Location Header Card --}}

                {{-- FAKTUR CARD GRID RESPONSIVE BARU --}}
                {{-- Menggunakan grid-cols-1 (Mobile), md:grid-cols-2 (Tablet), lg:grid-cols-3 (Desktop) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="faktur in location.fakturs" :key="faktur.id">
                        {{-- CARD FAKTUR INDIVIDUAL --}}
                        <div class="bg-white rounded-lg shadow-md border border-gray-100 p-5 flex flex-col justify-between hover:shadow-lg transition-shadow duration-300">
                            
                            {{-- Top Info --}}
                            <div>
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <span class="text-sm font-semibold text-gray-900 block leading-tight" x-text="faktur.period"></span>
                                            <span class="text-xs text-gray-500" x-text="faktur.year"></span>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold flex-shrink-0 ml-2"
                                            :class="faktur.status === 'reported' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                            x-text="faktur.status === 'reported' ? 'Dilaporkan' : 'Belum Lapor'">
                                    </span>
                                </div>

                                <p class="text-xs text-gray-500 mb-3">Nomor Faktur:</p>
                                <code class="text-xs text-gray-600 font-mono inline-block px-2 py-1 bg-gray-50 rounded mb-4" x-text="faktur.invoiceNumber"></code>

                                {{-- Financial Info Grid --}}
                                <div class="grid grid-cols-2 gap-4 border-t border-gray-100 pt-4 mb-4">
                                    <div>
                                        <span class="text-xs text-gray-500">Total Omzet:</span>
                                        <div class="font-bold text-sm text-gray-900 mt-0.5" x-text="formatCurrency(faktur.totalOmzet)"></div>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500">Pajak (10%):</span>
                                        <div class="font-bold text-sm text-orange-600 mt-0.5" x-text="formatCurrency(faktur.pajak)"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer & Action --}}
                            <div class="mt-auto">
                                <template x-if="faktur.reportDate">
                                    <p class="text-xs text-gray-500 mb-4">
                                        Dilaporkan: <span class="font-medium" x-text="faktur.reportDate"></span>
                                    </p>
                                </template>
                                <template x-if="!faktur.reportDate">
                                    <p class="text-xs text-gray-500 mb-4 h-[18px]"></p> {{-- Placeholder untuk menjaga tinggi --}}
                                </template>

                                <button @click="downloadFaktur(faktur)" 
                                        type="button"
                                        class="w-full px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 text-sm font-medium rounded-lg transition flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    Download Faktur PDF
                                </button>
                            </div>
                        </div> {{-- End CARD FAKTUR INDIVIDUAL --}}
                    </template>
                </div> {{-- End FAKTUR CARD GRID RESPONSIVE BARU --}}
            </div>
        </template>
    </div>

    {{-- Info Box --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h4 class="text-sm font-semibold text-blue-900 mb-1">Informasi Pelaporan Pajak</h4>
                <p class="text-sm text-blue-700">
                    Faktur pajak dihasilkan otomatis setiap akhir bulan berdasarkan total transaksi. 
                    Pastikan untuk melaporkan pajak sebelum tanggal 15 bulan berikutnya. 
                    Download faktur dalam format PDF untuk dilampirkan saat pelaporan.
                </p>
            </div>
        </div>
    </div>

    <div x-show="showDownloadModal" 
                x-cloak
                @click.self="showDownloadModal = false"
                class="fixed inset-0 z-[60] overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
                
                <div x-show="showDownloadModal" 
            x-cloak
            @click.self="showDownloadModal = false"
            {{-- Z-INDEX DITINGKATKAN DARI z-[60] MENJADI z-[100] untuk menutupi sidebar --}}
            class="fixed inset-0 z-[100] overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
            
            <div x-show="showDownloadModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90"
                @click.away="showDownloadModal = false"
                class="bg-white rounded-xl shadow-2xl max-w-sm w-full p-6 space-y-5">
                
                <template x-if="fakturToDownload">
                    <div>
                        {{-- Header --}}
                        <div class="flex items-center space-x-3 border-b border-gray-100 pb-4 mb-4">
                            <span class="text-2xl text-blue-600">📄</span>
                            <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Download Faktur</h3>
                        </div>

                        {{-- Detail Faktur --}}
                        <div class="space-y-3 text-sm">
                            {{-- Row 1: Periode & Nomor --}}
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-xs text-gray-500">Periode</p>
                                    <p class="font-medium text-gray-800" x-text="fakturToDownload.period + ' ' + fakturToDownload.year"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Nomor Faktur</p>
                                    <code class="text-xs text-gray-700 font-mono" x-text="fakturToDownload.invoiceNumber"></code>
                                </div>
                            </div>

                            {{-- Row 2: Lokasi --}}
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500">Lokasi</p>
                                <p class="font-medium text-gray-800" x-text="fakturToDownload.location"></p>
                            </div>

                            {{-- Financial Summary --}}
                            <div class="pt-2 border-t border-gray-100">
                                <div class="flex justify-between items-center py-1">
                                    <p class="text-sm text-gray-600">Total Omzet</p>
                                    <p class="text-sm font-semibold text-gray-900" x-text="formatCurrency(fakturToDownload.totalOmzet)"></p>
                                </div>
                                <div class="flex justify-between items-center py-1">
                                    <p class="text-sm text-gray-600">Pajak (10%)</p>
                                    <p class="text-sm font-semibold text-orange-600" x-text="formatCurrency(fakturToDownload.pajak)"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Status & Info --}}
                        <p x-cloak x-show="fakturToDownload.status === 'reported'" class="text-xs text-green-700 bg-green-50 p-3 rounded-lg mt-4 text-center">
                            Faktur telah dilaporkan pada <span x-text="fakturToDownload.reportDate"></span>
                        </p>

                        {{-- Aksi --}}
                        <div class="flex space-x-3 pt-4 border-t border-gray-100">
                            <button @click="showDownloadModal = false" 
                                    type="button"
                                    class="flex-1 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                                Batal
                            </button>
                            <button @click="
                                        showDownloadModal = false;
                                        // window.open('/api/faktur/' + fakturToDownload.id + '/download', '_blank');
                                    " 
                                    type="button"
                                    class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                Download PDF Sekarang
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function fakturPajakData() {
    return {
        // Filters
        filters: {
            location: 'all',
            year: 'all',
            status: 'all'
        },
        
        // Data Faktur (3 lokasi x berbagai periode)
        fakturs: [
            // Jakarta Pusat
            { id: 1, location: 'Jakarta Pusat', locationSlug: 'jakarta-pusat', period: 'September 2024', year: 2024, invoiceNumber: 'FP-JKT-2024-09', totalOmzet: 103650000, pajak: 10365000, status: 'reported', reportDate: '10 Okt 2024' },
            { id: 2, location: 'Jakarta Pusat', locationSlug: 'jakarta-pusat', period: 'Agustus 2024', year: 2024, invoiceNumber: 'FP-JKT-2024-08', totalOmzet: 98500000, pajak: 9850000, status: 'reported', reportDate: '12 Sep 2024' },
            { id: 3, location: 'Jakarta Pusat', locationSlug: 'jakarta-pusat', period: 'Juli 2024', year: 2024, invoiceNumber: 'FP-JKT-2024-07', totalOmzet: 105200000, pajak: 10520000, status: 'reported', reportDate: '14 Ags 2024' },
            { id: 4, location: 'Jakarta Pusat', locationSlug: 'jakarta-pusat', period: 'Juni 2024', year: 2024, invoiceNumber: 'FP-JKT-2024-06', totalOmzet: 92800000, pajak: 9280000, status: 'reported', reportDate: '10 Jul 2024' },
            
            // Surabaya
            { id: 5, location: 'Surabaya', locationSlug: 'surabaya', period: 'September 2024', year: 2024, invoiceNumber: 'FP-SBY-2024-09', totalOmzet: 74950000, pajak: 7495000, status: 'unreported', reportDate: null },
            { id: 6, location: 'Surabaya', locationSlug: 'surabaya', period: 'Agustus 2024', year: 2024, invoiceNumber: 'FP-SBY-2024-08', totalOmzet: 68200000, pajak: 6820000, status: 'reported', reportDate: '13 Sep 2024' },
            { id: 7, location: 'Surabaya', locationSlug: 'surabaya', period: 'Juli 2024', year: 2024, invoiceNumber: 'FP-SBY-2024-07', totalOmzet: 71500000, pajak: 7150000, status: 'reported', reportDate: '15 Ags 2024' },
            { id: 8, location: 'Surabaya', locationSlug: 'surabaya', period: 'Juni 2024', year: 2024, invoiceNumber: 'FP-SBY-2024-06', totalOmzet: 65300000, pajak: 6530000, status: 'reported', reportDate: '11 Jul 2024' },
            
            // Bandung
            { id: 9, location: 'Bandung', locationSlug: 'bandung', period: 'September 2024', year: 2024, invoiceNumber: 'FP-BDG-2024-09', totalOmzet: 61650000, pajak: 6165000, status: 'unreported', reportDate: null },
            { id: 10, location: 'Bandung', locationSlug: 'bandung', period: 'Agustus 2024', year: 2024, invoiceNumber: 'FP-BDG-2024-08', totalOmzet: 58900000, pajak: 5890000, status: 'reported', reportDate: '14 Sep 2024' },
            { id: 11, location: 'Bandung', locationSlug: 'bandung', period: 'Juli 2024', year: 2024, invoiceNumber: 'FP-BDG-2024-07', totalOmzet: 62400000, pajak: 6240000, status: 'reported', reportDate: '12 Ags 2024' },
            { id: 12, location: 'Bandung', locationSlug: 'bandung', period: 'Juni 2024', year: 2024, invoiceNumber: 'FP-BDG-2024-06', totalOmzet: 55800000, pajak: 5580000, status: 'reported', reportDate: '09 Jul 2024' },
            
            // 2023 Data (beberapa faktur tahun lalu)
            { id: 13, location: 'Jakarta Pusat', locationSlug: 'jakarta-pusat', period: 'Desember 2023', year: 2023, invoiceNumber: 'FP-JKT-2023-12', totalOmzet: 95300000, pajak: 9530000, status: 'reported', reportDate: '10 Jan 2024' },
            { id: 14, location: 'Surabaya', locationSlug: 'surabaya', period: 'Desember 2023', year: 2023, invoiceNumber: 'FP-SBY-2023-12', totalOmzet: 63500000, pajak: 6350000, status: 'reported', reportDate: '12 Jan 2024' },
            { id: 15, location: 'Bandung', locationSlug: 'bandung', period: 'Desember 2023', year: 2023, invoiceNumber: 'FP-BDG-2023-12', totalOmzet: 52700000, pajak: 5270000, status: 'reported', reportDate: '11 Jan 2024' },
        ],
        
        // Computed
        get filteredFakturs() {
            return this.fakturs.filter(faktur => {
                // Filter location
                if (this.filters.location !== 'all' && faktur.locationSlug !== this.filters.location) {
                    return false;
                }
                
                // Filter year
                if (this.filters.year !== 'all' && faktur.year !== parseInt(this.filters.year)) {
                    return false;
                }
                
                // Filter status
                if (this.filters.status !== 'all' && faktur.status !== this.filters.status) {
                    return false;
                }
                
                return true;
            });
        },
        
        get groupedFaktur() {
            // Group by location
            const groups = {};
            
            this.filteredFakturs.forEach(faktur => {
                if (!groups[faktur.locationSlug]) {
                    groups[faktur.locationSlug] = {
                        name: faktur.location,
                        slug: faktur.locationSlug,
                        fakturs: []
                    };
                }
                groups[faktur.locationSlug].fakturs.push(faktur);
            });
            
            // Sort fakturs by year and period (newest first)
            Object.values(groups).forEach(group => {
                group.fakturs.sort((a, b) => {
                    if (a.year !== b.year) return b.year - a.year;
                    return b.id - a.id; // Assuming id is chronological
                });
            });
            
            return Object.values(groups);
        },
        
        get summary() {
            const currentYear = 2024;
            const totalFaktur = this.fakturs.length;
            const unreported = this.fakturs.filter(f => f.status === 'unreported').length;
            
            // Total pajak tahun 2024
            const totalTaxThisYear = this.fakturs
                .filter(f => f.year === currentYear)
                .reduce((sum, f) => sum + f.pajak, 0);
            
            // Latest faktur
            const sortedFakturs = [...this.fakturs].sort((a, b) => b.id - a.id);
            const latestFaktur = sortedFakturs[0] || { period: '-', location: '-' };
            
            return {
                totalFaktur,
                unreported,
                totalTaxThisYear,
                latestFaktur: {
                    period: latestFaktur.period,
                    location: latestFaktur.location
                }
            };
        },
        
        // Methods
        applyFilters() {
            // Filters are reactive
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },
        
        downloadFaktur(faktur) {
            // 1. Set data faktur ke properti Alpine
            this.fakturToDownload = faktur;
            
            // 2. Tampilkan modal
            this.showDownloadModal = true;
        },
        
        downloadLocationFaktur(locationSlug) {
            const location = this.groupedFaktur.find(g => g.slug === locationSlug);
            if (!location) return;
            
            alert(`Download Semua Faktur untuk ${location.name}:\n\nTotal: ${location.fakturs.length} faktur\nFormat: PDF (Gabungan)`);
            // TODO: Implementasi bulk download per lokasi
            // window.open(`/api/faktur/location/${locationSlug}/download-all`, '_blank');
        },
        
        downloadAllFaktur() {
            const total = this.filteredFakturs.length;
            const filterInfo = [];
            
            if (this.filters.location !== 'all') {
                const loc = this.filteredFakturs[0]?.location || '';
                filterInfo.push(`Lokasi: ${loc}`);
            }
            if (this.filters.year !== 'all') {
                filterInfo.push(`Tahun: ${this.filters.year}`);
            }
            if (this.filters.status !== 'all') {
                filterInfo.push(`Status: ${this.filters.status === 'reported' ? 'Dilaporkan' : 'Belum Dilaporkan'}`);
            }
            
            const filterText = filterInfo.length > 0 ? `\n\nFilter:\n${filterInfo.join('\n')}` : '\n\nFilter: Semua';
            
            alert(`Download Semua Faktur:\n\nTotal: ${total} faktur${filterText}\n\nFormat: PDF (Zip Archive)`);
            // TODO: Implementasi bulk download dengan filter
            // window.open(`/api/faktur/download-all?${new URLSearchParams(this.filters)}`, '_blank');
        }
    }
}
</script>
@endpush