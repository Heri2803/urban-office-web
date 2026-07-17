@extends('layouts.admin')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen" x-data="promoUsageManagement()" x-init="init()">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 leading-tight">Promo Usage Report</h1>
        <p class="mt-1 text-sm text-gray-500">Monitor perjalanan promo customer — dari diklaim ke wallet sampai dipakai di transaksi (Banner, Discount & Voucher)</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card: Total Diklaim -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="p-3 rounded-full bg-amber-50 text-amber-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Diklaim (Wallet)</p>
                <h3 class="text-2xl font-bold text-gray-900" x-text="stats.total_claimed">0</h3>
            </div>
        </div>

        <!-- Card: Total Digunakan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="p-3 rounded-full bg-emerald-50 text-emerald-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Digunakan (Settlement)</p>
                <h3 class="text-2xl font-bold text-gray-900" x-text="stats.total_used">0</h3>
            </div>
        </div>

        <!-- Card: Total Diskon -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="p-3 rounded-full bg-rose-50 text-rose-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Diskon Diberikan</p>
                <h3 class="text-xl font-bold text-gray-900" x-text="stats.total_discount">Rp 0</h3>
            </div>
        </div>

        <!-- Card: Promo Terpopuler -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
            <div class="p-3 rounded-full bg-amber-50 text-amber-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Promo Terpopuler</p>
                <h3 class="text-lg font-bold text-gray-900 truncate" style="max-width: 150px;" x-text="stats.top_promo ? stats.top_promo.name : '-'">-</h3>
                <p class="text-xs text-gray-400 mt-1" x-text="stats.top_promo ? stats.top_promo.count + ' klaim' : ''"></p>
            </div>
        </div>
    </div>

    <!-- Filters & Table Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Filter Bar -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                <div class="relative w-full lg:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                    </div>
                    <input type="text" x-model="filters.search" @keyup.enter="applyFilters()" class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Search user / kode promo...">
                </div>
                
                <select x-model="filters.promo_id" @change="applyFilters()" class="block w-full lg:w-auto border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Semua Promo</option>
                    @foreach($promos as $promo)
                        <option value="{{ $promo->id }}">{{ $promo->name }} ({{ $promo->code }})</option>
                    @endforeach
                </select>

                <select x-model="filters.location" @change="applyFilters()" class="block w-full lg:w-auto border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Semua Lokasi</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                    @endforeach
                </select>

                <select x-model="filters.event_type" @change="applyFilters()" class="block w-full lg:w-auto border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Semua Status</option>
                    <option value="claimed">Diklaim (Wallet)</option>
                    <option value="used">Digunakan (Settlement)</option>
                </select>

                <input type="date" x-model="filters.date_from" @change="applyFilters()" class="block w-full lg:w-auto border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Dari Tanggal">
                <span class="text-gray-500">-</span>
                <input type="date" x-model="filters.date_to" @change="applyFilters()" class="block w-full lg:w-auto border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Sampai Tanggal">

                <button @click="resetFilters()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Reset
                </button>
            </div>
            
            <div>
                <button @click="exportCsv()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export CSV
                </button>
            </div>
        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Promo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diskon / Transaksi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 relative">
                    <!-- Loading Overlay -->
                    <tr x-show="loading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
                        <td>
                            <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </td>
                    </tr>
                    
                    <template x-for="(usage, index) in usages" :key="usage.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="(pagination.current_page - 1) * pagination.per_page + index + 1"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900" x-text="usage.promo_name"></div>
                                <div class="text-sm text-gray-500" x-text="usage.promo_code + ' · ' + usage.promo_type"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="usage.user_name"></div>
                                <div class="text-sm text-gray-500 truncate" style="max-width: 150px;" x-show="usage.event_type === 'used'">
                                    Lokasi: <span x-text="getLocationName(usage.location)"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="usage.event_type === 'claimed' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" x-text="usage.event_type === 'claimed' ? 'Diklaim' : 'Digunakan'"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-rose-600" x-text="usage.formatted_discount"></div>
                                <div class="text-xs text-gray-500" x-show="usage.event_type === 'used'">dari <span x-text="usage.formatted_transaction_amount"></span></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="usage.created_at"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="openDetailModal(usage)" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md transition-colors">Detail</button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="!loading && usages.length === 0">
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <p class="text-lg font-medium">Tidak ada data ditemukan</p>
                            <p class="text-sm text-gray-400">Coba sesuaikan filter pencarian Anda.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between" x-show="usages.length > 0">
            <div class="flex-1 flex justify-between sm:hidden">
                <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50">Previous</button>
                <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50">Next</button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium" x-text="(pagination.current_page - 1) * pagination.per_page + 1"></span>
                        to <span class="font-medium" x-text="Math.min(pagination.current_page * pagination.per_page, pagination.total)"></span>
                        of <span class="font-medium" x-text="pagination.total"></span> results
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <select x-model="filters.per_page" @change="applyFilters()" class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                    </select>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button @click="changePage(1)" :disabled="pagination.current_page === 1" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
                            <span class="sr-only">First</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M15.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 010 1.414zm-6 0a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 1.414L5.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button disabled class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-gray-50 text-sm font-medium text-gray-700 disabled:opacity-100">
                            <span x-text="pagination.current_page + ' / ' + pagination.last_page"></span>
                        </button>
                        <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
                            <span class="sr-only">Next</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button @click="changePage(pagination.last_page)" :disabled="pagination.current_page === pagination.last_page" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
                            <span class="sr-only">Last</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414zm6 0a1 1 0 011.414 0l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414-1.414L14.586 10l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="selectedUsage" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div x-show="selectedUsage" class="fixed inset-0 transition-opacity" aria-hidden="true" @click="closeDetailModal()">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="selectedUsage" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <!-- Modal Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title" x-text="selectedUsage && selectedUsage.event_type === 'claimed' ? 'Detail Klaim Promo' : 'Detail Penggunaan Promo'">Detail Penggunaan Promo</h3>
                    <button @click="closeDetailModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="bg-white px-6 py-5">
                    <template x-if="selectedUsage">
                        <div>
                            <!-- User Info -->
                            <div class="mb-6">
                                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Informasi Customer</h4>
                                <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500">Nama Lengkap</p>
                                        <p class="text-sm font-medium text-gray-900" x-text="selectedUsage.user_name"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Email</p>
                                        <p class="text-sm font-medium text-gray-900" x-text="selectedUsage.user_email || '-'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Telepon</p>
                                        <p class="text-sm font-medium text-gray-900" x-text="selectedUsage.user_phone || '-'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500" x-text="selectedUsage.event_type === 'claimed' ? 'Tanggal Klaim' : 'Tanggal Digunakan'"></p>
                                        <p class="text-sm font-medium text-gray-900" x-text="selectedUsage.created_at"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Promo Info -->
                            <div class="mb-6">
                                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Detail Promo</h4>
                                <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <p class="text-xs text-gray-500">Nama Promo</p>
                                        <p class="text-sm font-medium text-gray-900" x-text="selectedUsage.promo_name"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Kode Promo</p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 mt-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800" x-text="selectedUsage.promo_code"></span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Tipe / Kategori</p>
                                        <p class="text-sm font-medium text-gray-900" x-text="selectedUsage.promo_type + ' · ' + selectedUsage.promo_category"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Transaction Info: cuma relevan kalau promo sudah benar-benar dipakai -->
                            <div x-show="selectedUsage.event_type === 'used'">
                                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Transaksi</h4>
                                <div class="bg-gray-50 rounded-lg p-4 border border-rose-100">
                                    <div class="flex justify-between items-center mb-3 pb-3 border-b border-gray-200">
                                        <div>
                                            <p class="text-xs text-gray-500">ID Transaksi</p>
                                            <p class="text-sm font-medium text-blue-600 hover:text-blue-800 cursor-pointer" x-text="selectedUsage.transaction_id || '-'"></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">Lokasi</p>
                                            <p class="text-sm font-medium text-gray-900" x-text="getLocationName(selectedUsage.location)"></p>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="text-xs text-gray-500">Nilai Transaksi (Gross)</p>
                                            <p class="text-sm font-medium text-gray-900" x-text="selectedUsage.formatted_transaction_amount"></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">Potongan Diskon</p>
                                            <p class="text-lg font-bold text-rose-600" x-text="'− ' + selectedUsage.formatted_discount"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div x-show="selectedUsage.event_type === 'claimed'" class="bg-amber-50 border border-amber-100 rounded-lg p-4 text-sm text-amber-700">
                                Promo ini baru diklaim ke wallet customer dan belum dipakai pada transaksi manapun.
                            </div>

                        </div>
                    </template>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex justify-end rounded-b-lg">
                    <button @click="closeDetailModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function promoUsageManagement() {
    return {
        loading: false,
        usages: [],
        stats: {
            total_claimed: 0,
            total_used: 0,
            total_discount: 'Rp 0',
            top_promo: null
        },
        filters: {
            search: '',
            promo_id: '',
            location: '',
            event_type: '',
            date_from: '',
            date_to: '',
            per_page: '25'
        },
        pagination: {
            current_page: 1,
            last_page: 1,
            total: 0,
            per_page: 25
        },
        selectedUsage: null,
        locationsMap: {
            @foreach($locations as $loc)
                '{{$loc->id}}': '{{$loc->name}}',
            @endforeach
        },

        getLocationName(id) {
            if(!id) return '-';
            return this.locationsMap[id] || id;
        },

        init() {
            this.loadStats();
            this.loadUsages();
        },

        async loadStats() {
            try {
                const response = await fetch('/admin/promo-usage/stats');
                const result = await response.json();
                if (result.success) {
                    this.stats = result.data;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        },

        async loadUsages() {
            this.loading = true;
            try {
                // Build query parameters
                const params = new URLSearchParams({
                    page: this.pagination.current_page,
                    per_page: this.filters.per_page,
                    search: this.filters.search,
                    promo_id: this.filters.promo_id,
                    location: this.filters.location,
                    event_type: this.filters.event_type,
                    date_from: this.filters.date_from,
                    date_to: this.filters.date_to
                });

                const response = await fetch(`/admin/promo-usage/api?${params}`);
                const result = await response.json();
                
                if (result.success) {
                    this.usages = result.data;
                    this.pagination = result.meta;
                }
            } catch (error) {
                console.error('Error loading usages:', error);
            } finally {
                this.loading = false;
            }
        },

        applyFilters() {
            this.pagination.current_page = 1;
            this.loadUsages();
        },

        resetFilters() {
            this.filters = {
                search: '',
                promo_id: '',
                location: '',
                event_type: '',
                date_from: '',
                date_to: '',
                per_page: this.filters.per_page // pertahankan per_page
            };
            this.applyFilters();
        },

        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.pagination.current_page = page;
                this.loadUsages();
            }
        },

        openDetailModal(usage) {
            this.selectedUsage = usage;
            document.body.style.overflow = 'hidden'; // prevent scrolling
        },

        closeDetailModal() {
            this.selectedUsage = null;
            document.body.style.overflow = 'auto'; // allow scrolling
        },

        exportCsv() {
            const params = new URLSearchParams({
                search: this.filters.search,
                promo_id: this.filters.promo_id,
                location: this.filters.location,
                event_type: this.filters.event_type,
                date_from: this.filters.date_from,
                date_to: this.filters.date_to
            });

            window.location.href = `/admin/promo-usage/export?${params}`;
        }
    };
}
</script>
@endpush
