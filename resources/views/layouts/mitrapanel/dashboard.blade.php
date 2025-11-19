@extends('layouts.mitrapanel')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang di panel mitra')

@push('styles')
<style>
    /* Custom scrollbar untuk dropdown */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="dashboardData()">

    {{-- Header Section --}}
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        {{-- Filter Lokasi --}}
        <div class="relative" @click.away="openLocationFilter = false">
            <button @click="openLocationFilter = !openLocationFilter"
                    type="button"
                    class="flex items-center justify-between w-full sm:w-64 px-4 py-2.5 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700" x-text="selectedLocation.name"></span>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{'rotate-180': openLocationFilter}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Dropdown --}}
            <div x-show="openLocationFilter"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute right-0 mt-2 w-full sm:w-64 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden z-50">
                <div class="max-h-64 overflow-y-auto custom-scrollbar">
                    <template x-for="location in locations" :key="location.id">
                        <button @click="selectLocation(location)"
                                type="button"
                                class="w-full text-left px-4 py-3 hover:bg-gray-50 transition flex items-center space-x-3"
                                :class="selectedLocation.id === location.id ? 'bg-blue-50' : ''">
                            <svg class="w-4 h-4" :class="selectedLocation.id === location.id ? 'text-blue-600' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium" :class="selectedLocation.id === location.id ? 'text-blue-600' : 'text-gray-700'" x-text="location.name"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </header>

    {{-- Quick Stats Cards --}}
    <section class="grid grid-cols-2 sm:grid-cols-2 xl:grid-cols-4 gap-3 md:gap-4 lg:gap-6">
        {{-- Total Transaksi --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-4 rounded-xl shadow-sm text-white relative">
            {{-- Icon di pojok atas kanan --}}
            <div class="absolute top-3 right-3 bg-white/20 p-1.5 rounded-lg flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-7 lg:h-7" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                </svg>
            </div>
            <div class="pr-10"> {{-- Beri padding right untuk space icon --}}
                <p class="text-blue-100 text-xs font-medium">Total Transaksi</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold mt-1" x-text="stats.totalTransactions"></p>
                <p class="text-blue-100 text-xs mt-0.5">Bulan ini</p>
            </div>
        </div>

        {{-- Total Revenue --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-4 rounded-xl shadow-sm text-white relative">
            <div class="absolute top-3 right-3 bg-white/20 p-1.5 rounded-lg flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-7 lg:h-7" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="pr-10">
                <p class="text-green-100 text-xs font-medium">Total Revenue</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold mt-1" x-text="formatCurrency(stats.totalRevenue)"></p>
                <p class="text-green-100 text-xs mt-0.5">Bulan ini</p>
            </div>
        </div>

        {{-- Lokasi Aktif --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-4 rounded-xl shadow-sm text-white relative">
            <div class="absolute top-3 right-3 bg-white/20 p-1.5 rounded-lg flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-7 lg:h-7" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="pr-10">
                <p class="text-purple-100 text-xs font-medium">Lokasi Aktif</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold mt-1" x-text="stats.activeLocations"></p>
                <p class="text-purple-100 text-xs mt-0.5">Tersedia</p>
            </div>
        </div>

        {{-- Booking Pending --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-4 rounded-xl shadow-sm text-white relative">
            <div class="absolute top-3 right-3 bg-white/20 p-1.5 rounded-lg flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-7 lg:h-7" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="pr-10">
                <p class="text-orange-100 text-xs font-medium">Booking Pending</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold mt-1" x-text="stats.pendingBookings"></p>
                <p class="text-orange-100 text-xs mt-0.5">Perlu review</p>
            </div>
        </div>
    </section>

    {{-- Chart Section --}}
    <section class="bg-white p-6 rounded-2xl shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Grafik Transaksi</h3>
                <p class="text-sm text-gray-500 mt-1">Visualisasi transaksi per periode</p>
            </div>

            {{-- Period Filter --}}
            <div class="flex items-center space-x-2 bg-gray-100 p-1 rounded-lg">
                <button @click="changePeriod('daily')"
                        type="button"
                        class="px-4 py-2 rounded-md text-sm font-medium transition"
                        :class="currentPeriod === 'daily' ? 'bg-white text-gray-900 shadow' : 'text-gray-600 hover:text-gray-900'">
                    Hari
                </button>
                <button @click="changePeriod('monthly')"
                        type="button"
                        class="px-4 py-2 rounded-md text-sm font-medium transition"
                        :class="currentPeriod === 'monthly' ? 'bg-white text-gray-900 shadow' : 'text-gray-600 hover:text-gray-900'">
                    Bulan
                </button>
                <button @click="changePeriod('yearly')"
                        type="button"
                        class="px-4 py-2 rounded-md text-sm font-medium transition"
                        :class="currentPeriod === 'yearly' ? 'bg-white text-gray-900 shadow' : 'text-gray-600 hover:text-gray-900'">
                    Tahun
                </button>
            </div>
        </div>

        {{-- Canvas untuk Chart --}}
        <div class="relative" style="height: 400px;">
            <canvas id="transactionChart"></canvas>
        </div>
    </section>

    {{-- Recent Transactions --}}
    <section class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Transaksi Terbaru</h3>
                <p class="text-sm text-gray-500 mt-1">5 transaksi terakhir</p>
            </div>
            <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700 transition self-start sm:self-auto">
                Lihat Semua →
            </a>
        </div>

        <div class="overflow-x-auto -mx-2 sm:mx-0">
            <div class="min-w-[500px] sm:min-w-0">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-2 py-2 sm:px-3 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Tanggal</th>
                            <th class="px-2 py-2 sm:px-3 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layanan</th>
                            <th class="px-2 py-2 sm:px-3 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Customer</th>
                            <th class="px-2 py-2 sm:px-3 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Nominal</th>
                            <th class="px-2 py-2 sm:px-3 sm:py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="transaction in recentTransactions" :key="transaction.id">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-2 py-3 sm:px-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-600" x-text="transaction.date"></td>
                                <td class="px-2 py-3 sm:px-3 sm:py-4">
                                    <span class="text-xs sm:text-sm font-medium text-gray-900 break-words line-clamp-1" x-text="transaction.service"></span>
                                </td>
                                <td class="px-2 py-3 sm:px-3 sm:py-4 text-xs sm:text-sm text-gray-600 hidden sm:table-cell break-words" x-text="transaction.customer"></td>
                                <td class="px-2 py-3 sm:px-3 sm:py-4 whitespace-nowrap text-right text-xs sm:text-sm font-semibold text-gray-900" x-text="formatCurrency(transaction.amount)"></td>
                                <td class="px-2 py-3 sm:px-3 sm:py-4 whitespace-nowrap text-center">
                                    <span class="px-1.5 py-0.5 sm:px-2 sm:py-1 inline-flex text-xs leading-4 font-semibold rounded-full"
                                        :class="{
                                            'bg-green-100 text-green-800': transaction.status === 'success',
                                            'bg-yellow-100 text-yellow-800': transaction.status === 'pending',
                                            'bg-red-100 text-red-800': transaction.status === 'failed'
                                        }"
                                        x-text="transaction.status === 'success' ? 'Sukses' : transaction.status === 'pending' ? 'Pending' : 'Gagal'">
                                    </span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function dashboardData() {
    return {
        // State
        openLocationFilter: false,
        currentPeriod: 'monthly',
        chart: null,
        isLoading: false,
        
        // Data Lokasi (akan di-fetch dari API)
        locations: [],
        selectedLocation: { id: 0, name: 'Semua Lokasi', slug: 'all' },
        
        // Stats (akan di-fetch dari API)
        stats: {
            totalTransactions: 0,
            totalRevenue: 0,
            activeLocations: 0,
            pendingBookings: 0
        },
        
        // Recent Transactions (akan di-fetch dari API)
        recentTransactions: [],
        
        // Chart Data (akan di-fetch dari API)
        chartData: {
            daily: { labels: [], data: [] },
            monthly: { labels: [], data: [] },
            yearly: { labels: [], data: [] }
        },
        
        // Methods
        async init() {
            await this.fetchLocations();
            await this.fetchDashboardData();
            this.$nextTick(() => {
                this.initChart();
            });
        },
        
        // ✅ GUNAKAN URL YANG SUDAH ADA
        async fetchLocations() {
            try {
                const response = await fetch('/mitrapanel/api/locations');
                const data = await response.json();
                
                console.log('📍 Locations API Response:', data);
                
                if (data.success) {
                    this.locations = [
                        { id: 0, name: 'Semua Lokasi', slug: 'all' },
                        ...data.locations.map(loc => ({
                            id: loc.id,
                            name: loc.name,
                            slug: loc.slug,
                            transactionCount: loc.transaction_count || 0
                        }))
                    ];
                }
            } catch (error) {
                console.error('Error fetching locations:', error);
            }
        },
        
        // ✅ GUNAKAN URL YANG SUDAH ADA
         async fetchDashboardData() {
            this.isLoading = true;
            try {
                const params = new URLSearchParams({
                    location_id: this.selectedLocation.id,
                    period: this.currentPeriod
                });

                const response = await fetch(`/mitrapanel/api/dashboard-data?${params}`);
                const data = await response.json();

                console.log('📊 Dashboard API Response:', data);

                if (data.success) {
                    // ✅ Gunakan data dari API
                    this.stats = data.stats;
                    this.recentTransactions = data.recentTransactions;
                    this.chartData = data.chartData;
                    
                    console.log('✅ Stats from API:', this.stats);
                    console.log('✅ Recent Transactions from API:', this.recentTransactions); // ✅ DEBUG TRANSAKSI
                    
                    // ✅ Handle chart update dengan delay
                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.updateChart();
                        }, 100);
                    });
                } else {
                    console.error('❌ API returned error:', data);
                    this.getDefaultData(); // Fallback ke data default
                }
            } catch (error) {
                console.error('❌ Error fetching dashboard data:', error);
                this.getDefaultData(); // Fallback ke data default
            } finally {
                this.isLoading = false;
            }
        },
        
        // Handle location selection
        async selectLocation(location) {
            this.selectedLocation = location;
            this.openLocationFilter = false;
            await this.fetchDashboardData();
        },
        
        // Handle period change
        async changePeriod(period) {
            this.currentPeriod = period;
            await this.fetchDashboardData();
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },
        
        initChart() {
            const ctx = document.getElementById('transactionChart');
            if (!ctx) {
                console.warn('Chart canvas not found');
                return;
            }
            
            // ✅ Hancurkan chart existing jika ada
            if (this.chart) {
                this.chart.destroy();
            }
            
            const currentData = this.chartData[this.currentPeriod];
            
            console.log('🔄 Initializing chart with data:', currentData);
            
            try {
                this.chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: currentData.labels,
                        datasets: [{
                            label: 'Jumlah Transaksi',
                            data: currentData.data,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: 'rgb(59, 130, 246)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 13,
                                    weight: '600'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y + ' transaksi';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: 11
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        },
                        // ✅ TAMBAHKAN: Prevent re-render loops
                        animation: {
                            duration: 300,
                            easing: 'linear'
                        }
                    }
                });
                
                console.log('✅ Chart initialized successfully');
            } catch (error) {
                console.error('❌ Chart initialization error:', error);
            }
        },
        
        updateChart() {
            if (!this.chart) {
                console.log('📊 No chart instance, initializing...');
                this.initChart();
                return;
            }
            
            const data = this.chartData[this.currentPeriod];
            
            // ✅ Cek apakah data benar-benar berubah
            const labelsChanged = JSON.stringify(this.chart.data.labels) !== JSON.stringify(data.labels);
            const dataChanged = JSON.stringify(this.chart.data.datasets[0].data) !== JSON.stringify(data.data);
            
            if (!labelsChanged && !dataChanged) {
                console.log('📊 No data changes, skipping chart update');
                return;
            }
            
            console.log('🔄 Updating chart with new data:', data);
            
            try {
                // ✅ Update data
                this.chart.data.labels = data.labels;
                this.chart.data.datasets[0].data = data.data;
                
                // ✅ Update dengan options yang aman
                this.chart.update({
                    duration: 300,
                    lazy: true, // ✅ Prevent aggressive updates
                    easing: 'easeOutQuart'
                });
                
                console.log('✅ Chart updated successfully');
            } catch (error) {
                console.error('❌ Chart update error:', error);
                // Fallback: destroy and reinit
                this.chart.destroy();
                this.chart = null;
                this.$nextTick(() => {
                    this.initChart();
                });
            }
        },
        
        // Fallback default data
        getDefaultLocations() {
            this.locations = [
                { id: 0, name: 'Semua Lokasi', slug: 'all' },
                { id: 1, name: 'Urban Office Jakarta Pusat', slug: 'jakarta-pusat' },
                { id: 2, name: 'Urban Office Surabaya', slug: 'surabaya' },
                { id: 3, name: 'Urban Office Bandung', slug: 'bandung' },
                { id: 4, name: 'Urban Office Yogyakarta', slug: 'yogyakarta' },
                { id: 5, name: 'Urban Office Bali', slug: 'bali' }
            ];
        },
        
        getDefaultData() {
            this.stats = {
                totalTransactions: 156,
                totalRevenue: 45750000,
                activeLocations: 5,
                pendingBookings: 8
            };
            
            this.recentTransactions = [
                { id: 1, date: '07 Oct 2024', service: 'Private Office', customer: 'PT Maju Jaya', amount: 5000000, status: 'success' },
                { id: 2, date: '06 Oct 2024', service: 'Meeting Room', customer: 'CV Sukses Bersama', amount: 750000, status: 'success' },
                { id: 3, date: '06 Oct 2024', service: 'Coworking Space', customer: 'John Doe', amount: 500000, status: 'pending' },
                { id: 4, date: '05 Oct 2024', service: 'Virtual Office', customer: 'PT Digital Solusi', amount: 2500000, status: 'success' },
                { id: 5, date: '05 Oct 2024', service: 'Event Space', customer: 'Yayasan Pendidikan', amount: 8000000, status: 'success' }
            ];
            
            this.getDefaultChartData();
        },
        
        getDefaultChartData() {
            return {
                daily: {
                    labels: ['1 Oct', '2 Oct', '3 Oct', '4 Oct', '5 Oct', '6 Oct', '7 Oct'],
                    data: [12, 15, 8, 18, 22, 16, 20]
                },
                monthly: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    data: [65, 59, 80, 81, 56, 78, 95, 88, 92, 105, 0, 0]
                },
                yearly: {
                    labels: ['2020', '2021', '2022', '2023', '2024'],
                    data: [450, 620, 780, 890, 950]
                }
            };
        }
    }
}
</script>
@endpush