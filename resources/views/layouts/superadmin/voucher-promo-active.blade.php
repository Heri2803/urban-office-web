@extends('layouts.superadmin')

@section('content')
<main x-data="activeVouchers()" x-init="init()" class="p-4 sm:p-6 lg:p-10 space-y-6 bg-gray-50 min-h-screen">

    {{-- Header & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800">✅ Active Vouchers</h1>
            <p class="text-sm text-gray-500 mt-1">Manage and monitor all promotional vouchers</p>
        </div>
        <div class="flex space-x-2">
            <button @click="openCreateVoucher()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create New
            </button>
            <button @click="importVouchers()" class="px-4 py-2 border border-gray-300 bg-white rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hidden sm:flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Import
            </button>
        </div>
    </div>

    {{-- Quick Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white rounded-xl shadow-md border-l-4 border-indigo-500 p-3 sm:p-4">
            <p class="text-xs text-gray-500">Total Vouchers</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-800" x-text="stats.total"></p>
        </div>
        <div class="bg-white rounded-xl shadow-md border-l-4 border-green-500 p-3 sm:p-4">
            <p class="text-xs text-gray-500">Active Now</p>
            <p class="text-xl sm:text-2xl font-bold text-green-600" x-text="stats.active"></p>
        </div>
        <div class="bg-white rounded-xl shadow-md border-l-4 border-yellow-500 p-3 sm:p-4">
            <p class="text-xs text-gray-500">Scheduled</p>
            <p class="text-xl sm:text-2xl font-bold text-yellow-600" x-text="stats.scheduled"></p>
        </div>
        <div class="bg-white rounded-xl shadow-md border-l-4 border-red-500 p-3 sm:p-4">
            <p class="text-xs text-gray-500">Expired</p>
            <p class="text-xl sm:text-2xl font-bold text-red-600" x-text="stats.expired"></p>
        </div>
    </div>

    {{-- Second Row Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-md p-3 sm:p-4">
            <p class="text-xs text-blue-700 font-medium">Total Codes</p>
            <p class="text-xl sm:text-2xl font-bold text-blue-800" x-text="stats.totalCodes.toLocaleString('id-ID')"></p>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-md p-3 sm:p-4">
            <p class="text-xs text-green-700 font-medium">Redeemed</p>
            <p class="text-xl sm:text-2xl font-bold text-green-800" x-text="stats.redeemed.toLocaleString('id-ID')"></p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-md p-3 sm:p-4">
            <p class="text-xs text-purple-700 font-medium">Usage Rate</p>
            <p class="text-xl sm:text-2xl font-bold text-purple-800" x-text="stats.usageRate + '%'"></p>
        </div>
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl shadow-md p-3 sm:p-4">
            <p class="text-xs text-orange-700 font-medium">Discount Given</p>
            <p class="text-lg sm:text-xl font-bold text-orange-800" x-text="'Rp ' + stats.discountGiven.toLocaleString('id-ID')"></p>
        </div>
    </div>

    {{-- Quick Filter Tabs & Search --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4">
        {{-- Search --}}
        <div class="mb-4">
            <div class="relative">
                <input type="text" x-model="filters.search" placeholder="Search by code, name, or description..." 
                       class="w-full rounded-lg border-gray-300 pl-10 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        {{-- Quick Tabs --}}
        <div class="flex flex-wrap gap-2">
            <button @click="quickFilter('all')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                    :class="activeTab === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                All (<span x-text="getFilteredVouchers('all').length"></span>)
            </button>
            <button @click="quickFilter('active')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                    :class="activeTab === 'active' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200'">
                Active (<span x-text="getFilteredVouchers('active').length"></span>)
            </button>
            <button @click="quickFilter('scheduled')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                    :class="activeTab === 'scheduled' ? 'bg-yellow-600 text-white' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200'">
                Scheduled (<span x-text="getFilteredVouchers('scheduled').length"></span>)
            </button>
            <button @click="quickFilter('expired')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                    :class="activeTab === 'expired' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200'">
                Expired (<span x-text="getFilteredVouchers('expired').length"></span>)
            </button>
            <button @click="quickFilter('draft')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                    :class="activeTab === 'draft' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200'">
                Draft (<span x-text="getFilteredVouchers('draft').length"></span>)
            </button>
            <button @click="showAdvancedFilters = !showAdvancedFilters" 
                    class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 flex items-center ml-auto">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filters
            </button>
        </div>
    </div>

    {{-- Advanced Filters --}}
    <div x-show="showAdvancedFilters" x-transition class="bg-white rounded-xl shadow-md border border-gray-200 p-4">
        <h3 class="font-semibold text-gray-700 mb-3">Advanced Filters</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Discount Type</label>
                <select x-model="filters.discountType" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">All Types</option>
                    <option value="percentage">Percentage</option>
                    <option value="fixed">Fixed Amount</option>
                    <option value="free">Free Service</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                <select x-model="filters.service" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">All Services</option>
                    <option value="Meeting Room">Meeting Room</option>
                    <option value="Private Office">Private Office</option>
                    <option value="Coworking Space">Coworking Space</option>
                    <option value="Event Space">Event Space</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                <select x-model="filters.branch" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">All Branches</option>
                    <option value="Surabaya Center">Surabaya Center</option>
                    <option value="Jakarta Selatan">Jakarta Selatan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mitra</label>
                <select x-model="filters.mitra" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">All Mitra</option>
                    <option value="PT SBY Office">PT SBY Office</option>
                    <option value="PT JKT Workspace">PT JKT Workspace</option>
                </select>
            </div>
        </div>
        <div class="flex justify-end space-x-2 mt-4">
            <button @click="resetFilters()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                Reset
            </button>
            <button @click="exportExcel()" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
                Export Excel
            </button>
        </div>
    </div>

    {{-- Desktop Table View --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden lg:block">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Discount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valid Period</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Usage</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Redeemed</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <template x-for="voucher in getCurrentPageVouchers()" :key="voucher.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full whitespace-nowrap"
                                      :class="getStatusBadgeClass(voucher.status)"
                                      x-text="voucher.status.charAt(0).toUpperCase() + voucher.status.slice(1)">
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-indigo-600" x-text="voucher.code"></div>
                                <div class="text-xs text-gray-500" x-text="voucher.type"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900" x-text="voucher.name"></div>
                                <div class="text-xs text-gray-500" x-text="voucher.description"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-green-600" x-text="voucher.discountDisplay"></div>
                                <div class="text-xs text-gray-500" x-show="voucher.minTransaction">
                                    Min: <span x-text="'Rp ' + voucher.minTransaction.toLocaleString('id-ID')"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div x-text="voucher.startDate"></div>
                                <div class="text-xs text-gray-500">to <span x-text="voucher.endDate"></span></div>
                            </td>
                            <td class="px-6 py-4 text-center text-sm">
                                <div class="font-medium" x-text="voucher.usageLimit === 'unlimited' ? '∞' : voucher.totalLimit"></div>
                                <div class="text-xs text-gray-500">Per user: <span x-text="voucher.perUserLimit"></span></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="font-bold text-gray-900" x-text="voucher.redeemed"></div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all" 
                                         :style="'width: ' + voucher.usagePercentage + '%'">
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1" x-text="voucher.usagePercentage + '%'"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center space-x-1">
                                    <button @click="viewDetails(voucher)" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg" title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button @click="editVoucher(voucher)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="showActionsMenu(voucher)" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg" title="More">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium" x-text="getPaginationInfo().start"></span> 
                    to <span class="font-medium" x-text="getPaginationInfo().end"></span> 
                    of <span class="font-medium" x-text="getPaginationInfo().total"></span> vouchers
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="changePage(currentPage - 1)" 
                            :disabled="currentPage === 1"
                            class="px-3 py-1 border rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    <template x-for="page in getPageNumbers()" :key="page">
                        <button @click="changePage(page)"
                                class="px-3 py-1 border rounded-lg text-sm"
                                :class="page === currentPage ? 'bg-indigo-600 text-white border-indigo-600' : 'hover:bg-gray-100'">
                            <span x-text="page"></span>
                        </button>
                    </template>
                    <button @click="changePage(currentPage + 1)" 
                            :disabled="currentPage === getTotalPages()"
                            class="px-3 py-1 border rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tablet Grid View --}}
    <div class="hidden md:grid lg:hidden grid-cols-2 gap-4">
        <template x-for="voucher in getCurrentPageVouchers()" :key="voucher.id">
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 hover:shadow-lg transition-shadow">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full"
                              :class="getStatusBadgeClass(voucher.status)"
                              x-text="voucher.status">
                        </span>
                        <h3 class="font-bold text-indigo-600 mt-2" x-text="voucher.code"></h3>
                        <p class="text-sm text-gray-900 font-medium" x-text="voucher.name"></p>
                    </div>
                </div>
                
                <div class="space-y-2 text-sm mb-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Discount:</span>
                        <span class="font-bold text-green-600" x-text="voucher.discountDisplay"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Redeemed:</span>
                        <span class="font-medium" x-text="voucher.redeemed + ' / ' + (voucher.usageLimit === 'unlimited' ? '∞' : voucher.totalLimit)"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-indigo-600 h-2 rounded-full" :style="'width: ' + voucher.usagePercentage + '%'"></div>
                    </div>
                </div>
                
                <div class="flex justify-between items-center pt-3 border-t">
                    <button @click="viewDetails(voucher)" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                        View Details
                    </button>
                    <div class="flex space-x-2">
                        <button @click="editVoucher(voucher)" class="text-blue-600 hover:text-blue-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button @click="showActionsMenu(voucher)" class="text-gray-600 hover:text-gray-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Mobile Card View --}}
    <div class="md:hidden space-y-3">
        <template x-for="voucher in getCurrentPageVouchers()" :key="voucher.id">
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full"
                              :class="getStatusBadgeClass(voucher.status)"
                              x-text="voucher.status">
                        </span>
                        <h3 class="font-bold text-indigo-600 text-lg mt-2" x-text="voucher.code"></h3>
                        <p class="text-sm text-gray-700 font-medium" x-text="voucher.name"></p>
                    </div>
                </div>
                
                <div class="space-y-2 text-sm mb-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Discount:</span>
                        <span class="font-bold text-green-600" x-text="voucher.discountDisplay"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Valid until:</span>
                        <span class="font-medium text-gray-700" x-text="voucher.endDate"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Usage:</span>
                        <span class="font-medium" x-text="voucher.redeemed + ' / ' + (voucher.usageLimit === 'unlimited' ? '∞' : voucher.totalLimit)"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 mt-2">
                        <div class="bg-indigo-600 h-3 rounded-full flex items-center justify-end pr-1" 
                             :style="'width: ' + voucher.usagePercentage + '%'">
                            <span class="text-white text-xs font-bold" x-show="voucher.usagePercentage > 20" x-text="voucher.usagePercentage + '%'"></span>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-2 pt-3 border-t">
                    <button @click="viewDetails(voucher)" class="flex-1 px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                        View Details
                    </button>
                    <button @click="showActionsMenu(voucher)" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                        </svg>
                    </button>
                </div>
            </div>
        </template>

        {{-- Mobile Pagination --}}
        <div class="flex justify-center mt-4">
            <div class="flex items-center space-x-2">
                <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1"
                        class="px-4 py-2 border rounded-lg text-sm disabled:opacity-50">
                    Previous
                </button>
                <span class="px-4 py-2 text-sm font-medium">
                    <span x-text="currentPage"></span> / <span x-text="getTotalPages()"></span>
                </span>
                <button @click="changePage(currentPage + 1)" :disabled="currentPage === getTotalPages()"
                        class="px-4 py-2 border rounded-lg text-sm disabled:opacity-50">
                    Next
                </button>
            </div>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showDetailModal = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
            
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-3xl p-6 sm:p-8 my-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b pb-4 mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Voucher Details</h3>
                    <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <template x-if="selectedVoucher">
                    <div class="space-y-6">
                        {{-- Voucher Code Display --}}
                        <div class="text-center py-4 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl">
                            <p class="text-sm text-gray-500 mb-2">Voucher Code</p>
                            <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg">
                                <span class="text-2xl font-bold text-white tracking-wider" x-text="selectedVoucher.code"></span>
                            </div>
                            <div class="mt-3">
                                <span class="px-3 py-1 text-sm font-semibold rounded-full"
                                      :class="getStatusBadgeClass(selectedVoucher.status)"
                                      x-text="selectedVoucher.status">
                                </span>
                            </div>
                        </div>

                        {{-- Basic Info --}}
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Voucher Name</p>
                                <p class="font-medium text-gray-900" x-text="selectedVoucher.name"></p>
                            </div>
                            <div>
                                <p class="text-gray-500">Type</p>
                                <p class="font-medium text-gray-900" x-text="selectedVoucher.type"></p>
                            </div>
                        </div>

                        {{-- Discount Details --}}
                        <div class="border-t pt-4">
                            <p class="font-semibold text-gray-700 mb-3">💰 Discount Details</p>
                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600">Discount Value</span>
                                    <span class="font-bold text-green-700 text-xl" x-text="selectedVoucher.discountDisplay"></span>
                                </div>
                                <div x-show="selectedVoucher.minTransaction" class="text-sm text-gray-600">
                                    Minimum Transaction: Rp <span x-text="selectedVoucher.minTransaction.toLocaleString('id-ID')"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Valid Period --}}
                        <div class="border-t pt-4">
                            <p class="font-semibold text-gray-700 mb-3">📅 Validity Period</p>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Start Date</p>
                                    <p class="font-medium" x-text="selectedVoucher.startDate"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">End Date</p>
                                    <p class="font-medium" x-text="selectedVoucher.endDate"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Usage Statistics --}}
                        <div class="border-t pt-4">
                            <p class="font-semibold text-gray-700 mb-3">📊 Usage Statistics</p>
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Total Limit</span>
                                    <span class="font-medium" x-text="selectedVoucher.usageLimit === 'unlimited' ? 'Unlimited' : selectedVoucher.totalLimit"></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Per User Limit</span>
                                    <span class="font-medium" x-text="selectedVoucher.perUserLimit + ' time(s)'"></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Redeemed</span>
                                    <span class="font-bold text-indigo-600" x-text="selectedVoucher.redeemed"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-4">
                                    <div class="bg-indigo-600 h-4 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                         :style="'width: ' + selectedVoucher.usagePercentage + '%'">
                                        <span x-show="selectedVoucher.usagePercentage > 15" x-text="selectedVoucher.usagePercentage + '%'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Applicable Services --}}
                        <div class="border-t pt-4">
                            <p class="font-semibold text-gray-700 mb-3">✅ Applicable To</p>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="service in selectedVoucher.applicableServices" :key="service">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm rounded-full" x-text="service"></span>
                                </template>
                            </div>
                        </div>

                        {{-- Branch & Mitra --}}
                        <div class="border-t pt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="font-semibold text-gray-700 mb-2">🏢 Branches</p>
                                <p class="text-sm text-gray-600" x-text="selectedVoucher.branches"></p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-700 mb-2">🤝 Mitra</p>
                                <p class="text-sm text-gray-600" x-text="selectedVoucher.mitra"></p>
                            </div>
                        </div>

                        {{-- Additional Info --}}
                        <div class="border-t pt-4">
                            <p class="font-semibold text-gray-700 mb-3">ℹ️ Additional Information</p>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center">
                                    <span x-show="selectedVoucher.combinable" class="text-green-600">✓ Can be combined with other discounts</span>
                                    <span x-show="!selectedVoucher.combinable" class="text-orange-600">✗ Exclusive discount only</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Customer Eligibility: </span>
                                    <span class="font-medium" x-text="selectedVoucher.customerEligibility"></span>
                                </div>
                                <div x-show="selectedVoucher.description" class="text-gray-600">
                                    <span class="font-medium">Description:</span> <span x-text="selectedVoucher.description"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="border-t pt-4 flex flex-col sm:flex-row gap-2">
                            <button @click="viewUsageReport(selectedVoucher)" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                                View Usage Report
                            </button>
                            <button @click="generateShareLink(selectedVoucher)" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Generate Share Link
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Actions Menu Modal --}}
    <div x-show="showActionsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showActionsModal = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
            
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
                <div class="flex items-center justify-between border-b pb-3 mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Voucher Actions</h3>
                    <button @click="showActionsModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <template x-if="selectedVoucher">
                    <div class="space-y-2">
                        <p class="text-sm text-gray-600 mb-4">
                            Voucher: <span class="font-bold text-indigo-600" x-text="selectedVoucher.code"></span>
                        </p>

                        <button @click="viewDetails(selectedVoucher)" class="w-full flex items-center px-4 py-3 border rounded-lg hover:bg-gray-50 text-left">
                            <svg class="w-5 h-5 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span class="text-sm font-medium">View Details & Analytics</span>
                        </button>

                        <button @click="editVoucher(selectedVoucher)" class="w-full flex items-center px-4 py-3 border rounded-lg hover:bg-gray-50 text-left">
                            <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span class="text-sm font-medium">Edit Voucher</span>
                        </button>

                        <button @click="duplicateVoucher(selectedVoucher)" class="w-full flex items-center px-4 py-3 border rounded-lg hover:bg-gray-50 text-left">
                            <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-medium">Duplicate Voucher</span>
                        </button>

                        <button @click="viewUsageReport(selectedVoucher)" class="w-full flex items-center px-4 py-3 border rounded-lg hover:bg-gray-50 text-left">
                            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span class="text-sm font-medium">View Usage Report</span>
                        </button>

                        <button @click="generateShareLink(selectedVoucher)" class="w-full flex items-center px-4 py-3 border rounded-lg hover:bg-gray-50 text-left">
                            <svg class="w-5 h-5 text-cyan-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            <span class="text-sm font-medium">Generate Share Link</span>
                        </button>

                        <button @click="sendEmail(selectedVoucher)" class="w-full flex items-center px-4 py-3 border rounded-lg hover:bg-gray-50 text-left">
                            <svg class="w-5 h-5 text-pink-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-medium">Send via Email (Bulk)</span>
                        </button>

                        <button @click="toggleVoucherStatus(selectedVoucher)" 
                                class="w-full flex items-center px-4 py-3 border rounded-lg hover:bg-gray-50 text-left"
                                :class="selectedVoucher.status === 'active' ? 'border-orange-300 bg-orange-50' : 'border-green-300 bg-green-50'">
                            <svg class="w-5 h-5 mr-3" :class="selectedVoucher.status === 'active' ? 'text-orange-600' : 'text-green-600'" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-medium" x-text="selectedVoucher.status === 'active' ? 'Pause Voucher' : 'Resume Voucher'"></span>
                        </button>

                        <button @click="exportCodes(selectedVoucher)" class="w-full flex items-center px-4 py-3 border rounded-lg hover:bg-gray-50 text-left">
                            <svg class="w-5 h-5 text-teal-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-sm font-medium">Export Codes (CSV)</span>
                        </button>

                        <button @click="deactivateVoucher(selectedVoucher)" class="w-full flex items-center px-4 py-3 border border-red-300 bg-red-50 rounded-lg hover:bg-red-100 text-left">
                            <svg class="w-5 h-5 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span class="text-sm font-medium text-red-700">Deactivate Voucher</span>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

</main>

<script>
    function activeVouchers() {
        return {
            activeTab: 'all',
            showAdvancedFilters: false,
            showDetailModal: false,
            showActionsModal: false,
            selectedVoucher: null,
            currentPage: 1,
            perPage: 10,

            filters: {
                search: '',
                discountType: '',
                service: '',
                branch: '',
                mitra: ''
            },

            stats: {
                total: 0,
                active: 0,
                scheduled: 0,
                expired: 0,
                totalCodes: 0,
                redeemed: 0,
                usageRate: 0,
                discountGiven: 0
            },

            allVouchers: [],

            init() {
                this.generateDummyData();
                this.calculateStats();
            },

            generateDummyData() {
                this.allVouchers = [
                    {
                        id: 1,
                        code: 'WELCOME2025',
                        name: 'Welcome Promo',
                        description: 'New customer welcome discount',
                        type: 'Single Code',
                        status: 'active',
                        discountType: 'percentage',
                        discountValue: 20,
                        discountDisplay: '20% (Max Rp 100k)',
                        minTransaction: 200000,
                        startDate: '2025-10-20',
                        endDate: '2025-12-31',
                        usageLimit: 'limited',
                        totalLimit: 1000,
                        perUserLimit: 1,
                        redeemed: 234,
                        usagePercentage: 23.4,
                        applicableServices: ['All Services'],
                        branches: 'All Branches',
                        mitra: 'All Mitra',
                        combinable: true,
                        customerEligibility: 'New Customers Only'
                    },
                    {
                        id: 2,
                        code: 'FREEMR1H',
                        name: 'Free Meeting Room 1 Hour',
                        description: 'Complimentary 1 hour meeting room',
                        type: 'Single Code',
                        status: 'scheduled',
                        discountType: 'free',
                        discountValue: 100,
                        discountDisplay: '100% (1 hour)',
                        minTransaction: 0,
                        startDate: '2025-11-01',
                        endDate: '2025-12-31',
                        usageLimit: 'limited',
                        totalLimit: 50,
                        perUserLimit: 1,
                        redeemed: 0,
                        usagePercentage: 0,
                        applicableServices: ['Meeting Room'],
                        branches: 'All Branches',
                        mitra: 'All Mitra',
                        combinable: true,
                        customerEligibility: 'All Customers'
                    },
                    {
                        id: 3,
                        code: 'FLASH20',
                        name: 'Flash Sale',
                        description: 'Limited time flash sale',
                        type: 'Single Code',
                        status: 'expired',
                        discountType: 'fixed',
                        discountValue: 50000,
                        discountDisplay: 'Rp 50,000',
                        minTransaction: 100000,
                        startDate: '2025-09-01',
                        endDate: '2025-09-30',
                        usageLimit: 'limited',
                        totalLimit: 500,
                        perUserLimit: 1,
                        redeemed: 487,
                        usagePercentage: 97.4,
                        applicableServices: ['Coworking Space', 'Meeting Room'],
                        branches: 'Surabaya Center, Jakarta Selatan',
                        mitra: 'PT SBY Office',
                        combinable: false,
                        customerEligibility: 'All Customers'
                    },
                    {
                        id: 4,
                        code: 'LOYALTY50',
                        name: 'Loyalty Reward',
                        description: 'For our loyal customers',
                        type: 'Unique Codes',
                        status: 'active',
                        discountType: 'percentage',
                        discountValue: 15,
                        discountDisplay: '15%',
                        minTransaction: 300000,
                        startDate: '2025-10-01',
                        endDate: '2025-12-31',
                        usageLimit: 'limited',
                        totalLimit: 200,
                        perUserLimit: 3,
                        redeemed: 45,
                        usagePercentage: 22.5,
                        applicableServices: ['Private Office', 'Event Space'],
                        branches: 'All Branches',
                        mitra: 'All Mitra',
                        combinable: true,
                        customerEligibility: 'Returning Customers Only'
                    },
                    {
                        id: 5,
                        code: 'NEWYEAR25',
                        name: 'New Year Promo',
                        description: 'Celebrate new year with us',
                        type: 'Single Code',
                        status: 'draft',
                        discountType: 'percentage',
                        discountValue: 25,
                        discountDisplay: '25% (Max Rp 200k)',
                        minTransaction: 500000,
                        startDate: '2025-12-20',
                        endDate: '2026-01-10',
                        usageLimit: 'unlimited',
                        totalLimit: 0,
                        perUserLimit: 1,
                        redeemed: 0,
                        usagePercentage: 0,
                        applicableServices: ['All Services'],
                        branches: 'All Branches',
                        mitra: 'All Mitra',
                        combinable: false,
                        customerEligibility: 'All Customers'
                    },
                    {
                        id: 6,
                        code: 'EARLYBIRD',
                        name: 'Early Bird Discount',
                        description: 'Book early and save',
                        type: 'Single Code',
                        status: 'active',
                        discountType: 'fixed',
                        discountValue: 75000,
                        discountDisplay: 'Rp 75,000',
                        minTransaction: 250000,
                        startDate: '2025-10-15',
                        endDate: '2025-11-30',
                        usageLimit: 'limited',
                        totalLimit: 300,
                        perUserLimit: 2,
                        redeemed: 128,
                        usagePercentage: 42.7,
                        applicableServices: ['Event Space', 'Meeting Room'],
                        branches: 'Jakarta Selatan, Jakarta Barat',
                        mitra: 'PT JKT Workspace',
                        combinable: true,
                        customerEligibility: 'All Customers'
                    }
                ];
            },

            calculateStats() {
                this.stats.total = this.allVouchers.length;
                this.stats.active = this.allVouchers.filter(v => v.status === 'active').length;
                this.stats.scheduled = this.allVouchers.filter(v => v.status === 'scheduled').length;
                this.stats.expired = this.allVouchers.filter(v => v.status === 'expired').length;
                this.stats.totalCodes = this.allVouchers.reduce((sum, v) => sum + (v.usageLimit === 'unlimited' ? 1000 : v.totalLimit), 0);
                this.stats.redeemed = this.allVouchers.reduce((sum, v) => sum + v.redeemed, 0);
                this.stats.usageRate = this.stats.totalCodes > 0 ? ((this.stats.redeemed / this.stats.totalCodes) * 100).toFixed(1) : 0;
                this.stats.discountGiven = 45200000; // Dummy value
            },

            quickFilter(tab) {
                this.activeTab = tab;
                this.currentPage = 1;
            },

            getFilteredVouchers(status = null) {
                let filtered = this.allVouchers;
                const targetStatus = status || this.activeTab;

                // Status filter
                if (targetStatus !== 'all') {
                    filtered = filtered.filter(v => v.status === targetStatus);
                }

                // Search filter
                if (this.filters.search) {
                    const search = this.filters.search.toLowerCase();
                    filtered = filtered.filter(v =>
                        v.code.toLowerCase().includes(search) ||
                        v.name.toLowerCase().includes(search) ||
                        v.description.toLowerCase().includes(search)
                    );
                }

                // Advanced filters
                if (this.filters.discountType) {
                    filtered = filtered.filter(v => v.discountType === this.filters.discountType);
                }
                if (this.filters.service) {
                    filtered = filtered.filter(v =>
                        v.applicableServices.includes('All Services') ||
                        v.applicableServices.includes(this.filters.service)
                    );
                }
                if (this.filters.branch) {
                    filtered = filtered.filter(v =>
                        v.branches === 'All Branches' ||
                        v.branches.includes(this.filters.branch)
                    );
                }
                if (this.filters.mitra) {
                    filtered = filtered.filter(v =>
                        v.mitra === 'All Mitra' ||
                        v.mitra.includes(this.filters.mitra)
                    );
                }

                return filtered;
            },

            getCurrentPageVouchers() {
                const filtered = this.getFilteredVouchers();
                const start = (this.currentPage - 1) * this.perPage;
                const end = start + this.perPage;
                return filtered.slice(start, end);
            },

            getTotalPages() {
                const filtered = this.getFilteredVouchers();
                return Math.ceil(filtered.length / this.perPage) || 1;
            },

            getPageNumbers() {
                const total = this.getTotalPages();
                const current = this.currentPage;
                const pages = [];

                let startPage = Math.max(1, current - 2);
                let endPage = Math.min(total, current + 2);

                if (current <= 3) {
                    endPage = Math.min(5, total);
                }
                if (current >= total - 2) {
                    startPage = Math.max(1, total - 4);
                }

                for (let i = startPage; i <= endPage; i++) {
                    pages.push(i);
                }

                return pages;
            },

            getPaginationInfo() {
                const filtered = this.getFilteredVouchers();
                const start = (this.currentPage - 1) * this.perPage + 1;
                const end = Math.min(start + this.perPage - 1, filtered.length);
                return {
                    start: filtered.length > 0 ? start : 0,
                    end: end,
                    total: filtered.length
                };
            },

            changePage(page) {
                const totalPages = this.getTotalPages();
                if (page >= 1 && page <= totalPages) {
                    this.currentPage = page;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            resetFilters() {
                this.filters = {
                    search: '',
                    discountType: '',
                    service: '',
                    branch: '',
                    mitra: ''
                };
                this.activeTab = 'all';
                this.currentPage = 1;
            },

            getStatusBadgeClass(status) {
                switch(status) {
                    case 'active': return 'bg-green-100 text-green-800';
                    case 'scheduled': return 'bg-yellow-100 text-yellow-800';
                    case 'expired': return 'bg-red-100 text-red-800';
                    case 'draft': return 'bg-blue-100 text-blue-800';
                    case 'paused': return 'bg-orange-100 text-orange-800';
                    default: return 'bg-gray-100 text-gray-800';
                }
            },

            // Modal Actions
            viewDetails(voucher) {
                this.selectedVoucher = voucher;
                this.showActionsModal = false;
                this.showDetailModal = true;
            },

            showActionsMenu(voucher) {
                this.selectedVoucher = voucher;
                this.showActionsModal = true;
            },

            // Voucher Actions
            openCreateVoucher() {
                alert('Redirecting to Create Voucher page...');
                // window.location.href = '/superadmin/vouchers/create';
            },

            editVoucher(voucher) {
                alert(`Edit voucher: ${voucher.code}`);
                this.showActionsModal = false;
                // Redirect to edit page
            },

            duplicateVoucher(voucher) {
                if (confirm(`Duplicate voucher "${voucher.code}"?`)) {
                    const newVoucher = { 
                        ...voucher, 
                        id: this.allVouchers.length + 1,
                        code: voucher.code + '-COPY',
                        name: voucher.name + ' (Copy)',
                        status: 'draft',
                        redeemed: 0,
                        usagePercentage: 0
                    };
                    this.allVouchers.unshift(newVoucher);
                    this.calculateStats();
                    this.showActionsModal = false;
                    alert('Voucher duplicated successfully!');
                }
            },

            viewUsageReport(voucher) {
                alert(`View usage report for: ${voucher.code}`);
                this.showActionsModal = false;
                this.showDetailModal = false;
                // window.location.href = '/superadmin/vouchers/reports?code=' + voucher.code;
            },

            generateShareLink(voucher) {
                const link = `https://yoursite.com/booking?voucher=${voucher.code}`;
                navigator.clipboard.writeText(link).then(() => {
                    alert(`Share link copied to clipboard!\n\n${link}`);
                });
                this.showActionsModal = false;
                this.showDetailModal = false;
            },

            sendEmail(voucher) {
                alert(`Open email blast interface for voucher: ${voucher.code}`);
                this.showActionsModal = false;
            },

            toggleVoucherStatus(voucher) {
                const newStatus = voucher.status === 'active' ? 'paused' : 'active';
                if (confirm(`${newStatus === 'active' ? 'Resume' : 'Pause'} voucher "${voucher.code}"?`)) {
                    const index = this.allVouchers.findIndex(v => v.id === voucher.id);
                    if (index !== -1) {
                        this.allVouchers[index].status = newStatus;
                        this.calculateStats();
                        this.showActionsModal = false;
                        alert(`Voucher ${newStatus === 'active' ? 'resumed' : 'paused'} successfully!`);
                    }
                }
            },

            exportCodes(voucher) {
                alert(`Exporting codes for voucher: ${voucher.code}\nFormat: CSV`);
                this.showActionsModal = false;
                // Trigger CSV download
            },

            deactivateVoucher(voucher) {
                if (confirm(`Are you sure you want to deactivate voucher "${voucher.code}"?\n\nThis action cannot be undone. The voucher will no longer be usable.`)) {
                    const index = this.allVouchers.findIndex(v => v.id === voucher.id);
                    if (index !== -1) {
                        this.allVouchers[index].status = 'expired';
                        this.calculateStats();
                        this.showActionsModal = false;
                        alert('Voucher deactivated successfully!');
                    }
                }
            },

            importVouchers() {
                alert('Import vouchers from CSV:\n\n1. Download CSV template\n2. Fill in voucher data\n3. Upload file');
                // Open import modal
            },

            exportExcel() {
                const filtered = this.getFilteredVouchers();
                alert(`Exporting ${filtered.length} vouchers to Excel...`);
                // Trigger Excel download
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection