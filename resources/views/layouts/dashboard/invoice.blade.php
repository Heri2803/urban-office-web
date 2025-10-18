@extends('layouts.app') {{-- layout utama --}}

@section('content')

<script>
// ALPINE DATA DEFINITION
document.addEventListener('alpine:init', () => {
    Alpine.data('invoiceData', () => ({
        // Status State
        isFilterOpen: false, 
        // Inisialisasi data transaksi (invoice) dari Blade ke Alpine
        allTransactions: @json($transactions), 
        filterDate: '',   
        filterRoomType: '', // State filter berdasarkan jenis ruangan

        // Pilihan Jenis Ruangan
        roomTypes: [
            'Meeting Room', 
            'Event Space', 
            'Coworking Space', 
            'Virtual Office'
        ],

        // Computed property untuk hasil filter
        get filteredTransactions() {
            return this.allTransactions.filter(transaction => {
                // 1. Filter Jenis Ruangan
                const roomTypeMatch = this.filterRoomType === '' || transaction.room_type === this.filterRoomType;
                
                // 2. Filter Tanggal
                let dateMatch = true;
                if (this.filterDate) {
                    // Asumsi created_at adalah objek/string tanggal yang dapat di-parse
                    const invoiceDate = transaction.created_at ? new Date(transaction.created_at).toISOString().split('T')[0] : '';
                    dateMatch = invoiceDate === this.filterDate;
                }

                return roomTypeMatch && dateMatch;
            });
        },
        
        // Fungsi untuk reset filter
        resetFilter() {
            this.filterDate = '';
            this.filterRoomType = '';
        },

        // Fungsi inisialisasi untuk memicu animasi masuk di wrapper utama
        init() {
            const mainContent = document.getElementById('invoice-content-wrapper');
            if (mainContent) {
                mainContent.classList.remove('opacity-0', 'translate-y-6');
                mainContent.classList.add('opacity-100', 'translate-y-0');
            }
        }
    }));
});
</script>

<div class="flex min-h-screen bg-gray-50">

    {{-- Main Content --}}
    <div class="flex-1 ml-0 md:ml-60 lg:ml-64 xl:ml-64 flex flex-col">

        {{-- Invoice Header Bar --}}
        <div>
            @include('layouts.components.invoice')
        </div>

        {{-- Invoice Content (Diberi ID & Animasi Masuk Ringan) --}}
        <div x-data="invoiceData" id="invoice-content-wrapper" 
             class="flex-1 p-3 md:p-4 lg:p-6 opacity-0 translate-y-6 transition-all duration-500 ease-out">
            
            {{-- Page Header with Back Button & Filter Icon --}}
            <div class="mb-4 md:mb-6 flex items-center justify-between relative">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard.home') }}" class="block">
                        <button class="flex items-center text-gray-700 hover:text-orange-500 transition-colors duration-200 group">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span class="font-medium text-xs md:text-sm lg:text-base">INVOICE</span>
                        </button>
                    </a>
                </div>
                
                {{-- FILTER ICON BUTTON --}}
                <div class="relative" @click.outside="isFilterOpen = false">
                    <button @click="isFilterOpen = !isFilterOpen" 
                            class="p-2 rounded-full text-gray-600 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-colors">
                        {{-- Icon Filter --}}
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1h-1a1 1 0 00-1 1v8a1 1 0 01-1 1H7a1 1 0 01-1-1v-8a1 1 0 00-1-1H4a1 1 0 01-1-1V4z"/>
                        </svg>
                    </button>

                    {{-- FILTER DROPDOWN --}}
                    <div x-show="isFilterOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" 
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-64 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 p-4 z-10"
                        style="display: none;">
                        
                        <div class="space-y-3">
                            <h4 class="text-sm font-semibold text-gray-700">Filter Dokumen</h4>
                            
                            {{-- Filter Jenis Ruangan --}}
                            <select x-model="filterRoomType" class="form-select block w-full pl-3 pr-10 py-2 text-sm border-gray-300 focus:ring-orange-500 focus:border-orange-500 rounded-md shadow-sm">
                                <option value="">Semua Jenis Ruangan</option>
                                <template x-for="room in roomTypes" :key="room">
                                    <option :value="room" x-text="room"></option>
                                </template>
                            </select>
                            
                            {{-- Filter Tanggal --}}
                            <input x-model="filterDate" type="date" placeholder="Filter Tanggal Invoice" class="form-input block w-full px-3 py-2 text-sm border-gray-300 focus:ring-orange-500 focus:border-orange-500 rounded-md shadow-sm">
                            
                            {{-- Reset Button --}}
                            <button @click="resetFilter(); isFilterOpen = false" class="w-full px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md shadow-sm transition-colors">
                                Reset Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Documents Section --}}
            <div class="space-y-3 md:space-y-4 max-w-full md:max-w-3xl lg:max-w-4xl xl:max-w-5xl">
                
                <template x-for="(transaction, index) in filteredTransactions" :key="transaction.order_id">
                    <div>
                        {{-- Category Header --}}
                        <div class="mb-2 md:mb-3">
                            <h3 class="text-xs md:text-sm lg:text-base font-medium text-gray-700 mb-1 md:mb-2" x-text="'Invoice ' + transaction.room_type">
                                
                            </h3>
                        </div>

                        {{-- Document Card --}}
                        <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-200 p-3 md:p-4 lg:p-6 transition-all duration-500 ease-out hover:shadow-md">
                            <div class="flex items-start space-x-2 md:space-x-3 mb-3 md:mb-4">
                                <div class="w-8 h-8 md:w-10 md:h-10 bg-red-500 rounded flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-bold text-xs">PDF</span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-800 text-xs md:text-sm lg:text-base mb-1 truncate"
                                        x-text="transaction.order_id + '. Invoice ' + transaction.room_type + ' - ' + transaction.nama_lengkap">
                                    </h4>
                                    <p class="text-xs text-gray-500" x-text="new Date(transaction.created_at).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'})">
                                    </p>
                                </div>
                            </div>

                            {{-- Download button --}}
                            <a :href="'{{ url('invoice/generate') }}/' + transaction.order_id"
                            class="inline-block px-3 py-1 text-xs md:text-sm bg-orange-500 text-white rounded hover:bg-orange-600">
                                Download PDF
                            </a>
                        </div>
                    </div>
                </template>

                {{-- Message Kosong --}}
                <template x-if="filteredTransactions.length === 0">
                    <div class="text-center py-10 text-gray-500">
                        Tidak ada invoice yang cocok dengan filter.
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection