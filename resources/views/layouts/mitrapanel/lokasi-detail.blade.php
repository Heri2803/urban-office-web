@extends('layouts.mitrapanel')

@section('title', 'Detail Lokasi - ' . $locationName)

@section('page-title', $locationName)
@section('page-subtitle', 'Transaksi per layanan')

@push('styles')
<style>
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
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="lokasiDetailData()">

    {{-- Header dengan Back Button & Export --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        {{-- Back Button --}}
        <a href="{{ route('mitrapanel.dashboard') }}" 
           class="inline-flex items-center text-gray-600 hover:text-gray-900 transition group">
            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="font-medium">Kembali ke Dashboard</span>
        </a>

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

    {{-- Filter Periode --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h3 class="text-sm font-semibold text-gray-700">Filter Periode</h3>
            
            <div class="flex items-center space-x-2 bg-gray-100 p-1 rounded-lg">
                <button @click="changePeriod('daily')"
                        type="button"
                        class="px-4 py-2 rounded-md text-sm font-medium transition"
                        :class="currentPeriod === 'daily' ? 'bg-white text-gray-900 shadow' : 'text-gray-600 hover:text-gray-900'">
                    Hari Ini
                </button>
                <button @click="changePeriod('monthly')"
                        type="button"
                        class="px-4 py-2 rounded-md text-sm font-medium transition"
                        :class="currentPeriod === 'monthly' ? 'bg-white text-gray-900 shadow' : 'text-gray-600 hover:text-gray-900'">
                    Bulan Ini
                </button>
                <button @click="changePeriod('yearly')"
                        type="button"
                        class="px-4 py-2 rounded-md text-sm font-medium transition"
                        :class="currentPeriod === 'yearly' ? 'bg-white text-gray-900 shadow' : 'text-gray-600 hover:text-gray-900'">
                    Tahun Ini
                </button>
            </div>
        </div>
    </div>

    {{-- Service Cards Grid (6 Layanan) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
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

    {{-- Summary Section (Omzet, Pajak, Status) --}}
    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl shadow-sm border border-slate-200 overflow-hidden">
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
                            class="w-full mt-3 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
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
    return {
        currentPeriod: 'monthly',
        locationSlug: '{{ $locationSlug }}',
        
        // Data per lokasi (berbeda-beda)
        locationData: {
            'jakarta-pusat': {
                services: [
                    { id: 1, type: 'private-office', name: 'Private Office', icon: '🏢', transactions: 45, total: 22500000, average: 500000, highest: 1200000 },
                    { id: 2, type: 'virtual-office', name: 'Virtual Office', icon: '💼', transactions: 38, total: 9500000, average: 250000, highest: 500000 },
                    { id: 3, type: 'sharing-room', name: 'Sharing Room', icon: '👥', transactions: 52, total: 7800000, average: 150000, highest: 300000 },
                    { id: 4, type: 'meeting-room', name: 'Meeting Room', icon: '🤝', transactions: 68, total: 13600000, average: 200000, highest: 450000 },
                    { id: 5, type: 'event-space', name: 'Event Space', icon: '🎪', transactions: 12, total: 36000000, average: 3000000, highest: 8000000 },
                    { id: 6, type: 'coworking-space', name: 'Coworking Space', icon: '💻', transactions: 95, total: 14250000, average: 150000, highest: 250000 }
                ],
                summary: {
                    totalOmzet: 103650000,
                    totalPajak: 10365000,
                    status: 'reported',
                    reportDate: '10 Okt 2024'
                }
            },
            'surabaya': {
                services: [
                    { id: 1, type: 'private-office', name: 'Private Office', icon: '🏢', transactions: 32, total: 16000000, average: 500000, highest: 1000000 },
                    { id: 2, type: 'virtual-office', name: 'Virtual Office', icon: '💼', transactions: 28, total: 7000000, average: 250000, highest: 450000 },
                    { id: 3, type: 'sharing-room', name: 'Sharing Room', icon: '👥', transactions: 41, total: 6150000, average: 150000, highest: 280000 },
                    { id: 4, type: 'meeting-room', name: 'Meeting Room', icon: '🤝', transactions: 55, total: 11000000, average: 200000, highest: 400000 },
                    { id: 5, type: 'event-space', name: 'Event Space', icon: '🎪', transactions: 8, total: 24000000, average: 3000000, highest: 6500000 },
                    { id: 6, type: 'coworking-space', name: 'Coworking Space', icon: '💻', transactions: 72, total: 10800000, average: 150000, highest: 230000 }
                ],
                summary: {
                    totalOmzet: 74950000,
                    totalPajak: 7495000,
                    status: 'unreported',
                    reportDate: null
                }
            },
            'bandung': {
                services: [
                    { id: 1, type: 'private-office', name: 'Private Office', icon: '🏢', transactions: 28, total: 14000000, average: 500000, highest: 950000 },
                    { id: 2, type: 'virtual-office', name: 'Virtual Office', icon: '💼', transactions: 22, total: 5500000, average: 250000, highest: 420000 },
                    { id: 3, type: 'sharing-room', name: 'Sharing Room', icon: '👥', transactions: 35, total: 5250000, average: 150000, highest: 270000 },
                    { id: 4, type: 'meeting-room', name: 'Meeting Room', icon: '🤝', transactions: 48, total: 9600000, average: 200000, highest: 380000 },
                    { id: 5, type: 'event-space', name: 'Event Space', icon: '🎪', transactions: 6, total: 18000000, average: 3000000, highest: 5500000 },
                    { id: 6, type: 'coworking-space', name: 'Coworking Space', icon: '💻', transactions: 62, total: 9300000, average: 150000, highest: 220000 }
                ],
                summary: {
                    totalOmzet: 61650000,
                    totalPajak: 6165000,
                    status: 'reported',
                    reportDate: '08 Okt 2024'
                }
            }
        },
        
        // Computed
        get services() {
            return this.locationData[this.locationSlug]?.services || [];
        },
        
        get summary() {
            return this.locationData[this.locationSlug]?.summary || {};
        },
        
        // Methods
        changePeriod(period) {
            this.currentPeriod = period;
            // TODO: Fetch data berdasarkan period
            console.log('Period changed to:', period);
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },
        
        viewServiceDetail(service) {
            alert('Detail untuk ' + service.name + '\nTotal: ' + this.formatCurrency(service.total));
            // TODO: Buka modal atau redirect ke detail page
        },
        
        exportPDF() {
            alert('Export PDF untuk ' + this.locationSlug + '\nPeriode: ' + this.currentPeriod);
            // TODO: Implementasi export PDF
        },
        
        exportExcel() {
            alert('Export Excel untuk ' + this.locationSlug + '\nPeriode: ' + this.currentPeriod);
            // TODO: Implementasi export Excel
        },
        
        reportTax() {
            if (confirm('Apakah Anda yakin ingin melaporkan pajak untuk lokasi ini?')) {
                this.summary.status = 'reported';
                this.summary.reportDate = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                alert('Pajak berhasil dilaporkan!');
                // TODO: Save to database
            }
        }
    }
}
</script>
@endpush