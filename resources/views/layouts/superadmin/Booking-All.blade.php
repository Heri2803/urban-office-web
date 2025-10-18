@extends('layouts.superadmin')

@section('content')
<main x-data="bookingMonitor()" x-init="init()" class="p-4 sm:p-6 lg:p-10 space-y-6 bg-gray-50 min-h-screen">

    {{-- 1. HEADER & ACTION BUTTONS --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800">📅 All Bookings</h1>
            <p class="text-sm text-gray-500 mt-1">View and manage all bookings across branches</p>
        </div>
        <div class="flex space-x-2">
            <button @click="openManualBookingModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Manual Booking
            </button>
            <button class="px-4 py-2 border border-gray-300 bg-white rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hidden sm:flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Bulk Actions
            </button>
        </div>
    </div>

    {{-- 2. QUICK STATS CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
        <div class="bg-white rounded-xl shadow-md border-l-4 border-indigo-500 p-3 sm:p-4">
            <p class="text-xs text-gray-500">Total Bookings</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-800" x-text="stats.total"></p>
        </div>
        <div class="bg-white rounded-xl shadow-md border-l-4 border-yellow-500 p-3 sm:p-4">
            <p class="text-xs text-gray-500">Pending Payment</p>
            <p class="text-xl sm:text-2xl font-bold text-yellow-600" x-text="stats.pendingCount"></p>
        </div>
        <div class="bg-white rounded-xl shadow-md border-l-4 border-blue-500 p-3 sm:p-4">
            <p class="text-xs text-gray-500">Active Bookings</p>
            <p class="text-xl sm:text-2xl font-bold text-blue-600" x-text="stats.activeCount"></p>
        </div>
        <div class="bg-white rounded-xl shadow-md border-l-4 border-red-500 p-3 sm:p-4">
            <p class="text-xs text-gray-500">Cancelled</p>
            <p class="text-xl sm:text-2xl font-bold text-red-600" x-text="stats.cancelledCount"></p>
        </div>
        <div class="bg-white rounded-xl shadow-md border-l-4 border-green-500 p-3 sm:p-4 col-span-2 md:col-span-2 lg:col-span-2">
            <p class="text-xs text-gray-500">Total Revenue</p>
            <p class="text-xl sm:text-2xl font-bold text-green-600" x-text="'Rp ' + stats.revenue.toLocaleString('id-ID')"></p>
        </div>
    </div>

    {{-- 3. QUICK FILTER TABS --}}
    <div class="bg-white p-4 rounded-xl shadow-md border border-gray-200">
        <div class="flex flex-wrap gap-2">
            <button @click="quickFilter('all')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                    :class="activeQuickFilter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                All
            </button>
            <button @click="quickFilter('today')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                    :class="activeQuickFilter === 'today' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                Today
            </button>
            <button @click="quickFilter('this-week')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                    :class="activeQuickFilter === 'this-week' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                This Week
            </button>
            <button @click="quickFilter('pending')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors flex items-center"
                    :class="activeQuickFilter === 'pending' ? 'bg-yellow-600 text-white' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200'">
                <span class="w-2 h-2 bg-current rounded-full mr-2"></span>
                Pending
            </button>
            <button @click="quickFilter('need-assignment')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors flex items-center"
                    :class="activeQuickFilter === 'need-assignment' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200'">
                <span class="w-2 h-2 bg-current rounded-full mr-2"></span>
                Need Assignment
            </button>
            <button @click="quickFilter('active')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                    :class="activeQuickFilter === 'active' ? 'bg-purple-600 text-white' : 'bg-purple-100 text-purple-700 hover:bg-purple-200'">
                Active
            </button>
            <button @click="showAdvancedFilters = !showAdvancedFilters" 
                    class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 flex items-center ml-auto">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Advanced Filters
            </button>
        </div>
    </div>

    {{-- 4. ADVANCED FILTER SECTION (Collapsible) --}}
    <section x-show="showAdvancedFilters" x-transition class="bg-white p-4 sm:p-6 rounded-xl shadow-lg border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">🔍 Advanced Filters & Search</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Search --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" x-model="filters.search" placeholder="Booking ID, Name, Email, Phone..." 
                       class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- Date Range --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" x-model="filters.startDate" 
                       class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" x-model="filters.endDate" 
                       class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- Mitra --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mitra</label>
                <select x-model="filters.mitra" @change="filters.branch = ''" 
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Mitra</option>
                    <template x-for="mitra in mitras" :key="mitra.id">
                        <option :value="mitra.id" x-text="mitra.name"></option>
                    </template>
                </select>
            </div>

            {{-- Branch --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                <select x-model="filters.branch" 
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Branches</option>
                    <template x-for="branch in getFilteredBranches()" :key="branch.id">
                        <option :value="branch.id" x-text="branch.name"></option>
                    </template>
                </select>
            </div>
            
            {{-- Service Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service Type</label>
                <select x-model="filters.service" 
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Services</option>
                    <template x-for="service in serviceTypes" :key="service">
                        <option :value="service" x-text="service"></option>
                    </template>
                </select>
            </div>

            {{-- Payment Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                <select x-model="filters.paymentStatus" 
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Payment</option>
                    <option value="settlement">Settlement</option>
                    <option value="pending">Pending</option>
                    <option value="expired">Expired</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            {{-- Booking Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Booking Status</label>
                <select x-model="filters.bookingStatus" 
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Booking</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="occupied">Occupied</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="no-show">No-show</option>
                </select>
            </div>

            {{-- Booking Source --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Booking Source</label>
                <select x-model="filters.bookingSource" 
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Sources</option>
                    <option value="online">Online</option>
                    <option value="manual">Manual</option>
                    <option value="walk-in">Walk-in</option>
                </select>
            </div>

            {{-- Payment Method --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                <select x-model="filters.paymentMethod" 
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Methods</option>
                    <option value="va">Virtual Account</option>
                    <option value="qris">QRIS</option>
                    <option value="ewallet">E-Wallet</option>
                    <option value="cash">Cash</option>
                </select>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-center pt-4 border-t mt-4 space-y-2 sm:space-y-0">
            {{-- Filter Actions --}}
            <div class="flex space-x-3 w-full sm:w-auto">
                <button @click="resetFilters()" 
                        class="flex-1 sm:flex-none px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Reset
                </button>
                <button @click="applyFilters()" 
                        class="flex-1 sm:flex-none px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                    Apply Filters
                </button>
            </div>
        </div>
    </section>

    {{-- 5. BOOKING LIST - DESKTOP TABLE VIEW --}}
    <section class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden **hidden sm:block**">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-700">
                    Transactions List (<span x-text="getPaginatedBookings().totalItems"></span>)
                </h2>
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-600">Show:</label>
                    <select x-model="pagination.perPage" @change="pagination.currentPage = 1" 
                            class="rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mitra & Branch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Duration</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <template x-for="booking in getPaginatedBookings().items" :key="booking.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-indigo-600" x-text="booking.id"></td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900" x-text="booking.customerName"></div>
                                <div class="text-xs text-gray-500" x-text="booking.customerEmail"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900" x-text="booking.mitraName || 'N/A'"></div>
                                <div class="text-xs text-gray-500" x-text="booking.branchName"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900" x-text="booking.serviceType"></div>
                                <div class="text-xs text-gray-500" x-text="booking.roomNumber || 'Communal'"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900" x-text="booking.date"></div>
                                <div class="text-xs text-gray-500" x-text="booking.duration"></div>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right" x-text="'Rp ' + booking.revenue.toLocaleString('id-ID')"></td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                      :class="getPaymentBadgeClass(booking.paymentStatus)" 
                                      x-text="booking.paymentStatus.charAt(0).toUpperCase() + booking.paymentStatus.slice(1)">
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    {{-- View Details --}}
                                    <button @click="viewDetails(booking)" 
                                            class="text-indigo-600 hover:text-indigo-900" 
                                            title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    {{-- Assign Room --}}
                                    <button x-show="booking.paymentStatus === 'settlement' && !booking.roomNumber" 
                                            @click="assignRoom(booking)"
                                            class="text-green-600 hover:text-green-900" 
                                            title="Assign Room">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                    </button>

                                    {{-- Mark as Check-in --}}
                                    <button x-show="booking.bookingStatus === 'confirmed' && booking.roomNumber" 
                                            @click="markAsCheckin(booking)"
                                            class="text-purple-600 hover:text-purple-900" 
                                            title="Mark as Check-in">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>

                                    {{-- Print Invoice --}}
                                    <button @click="printInvoice(booking)"
                                            class="text-gray-600 hover:text-gray-900" 
                                            title="Print Invoice">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                    </button>

                                    {{-- Cancel --}}
                                    <button x-show="['pending', 'settlement'].includes(booking.paymentStatus)" 
                                            @click="cancelBooking(booking)"
                                            class="text-red-600 hover:text-red-900" 
                                            title="Cancel Booking">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Empty State --}}
        <div x-show="getPaginatedBookings().items.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="mt-2 text-sm text-gray-500">No bookings found matching the current filters.</p>
        </div>

        {{-- Pagination --}}
        <div x-show="getPaginatedBookings().totalPages > 1" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium" x-text="getPaginatedBookings().startIndex"></span> 
                    to <span class="font-medium" x-text="getPaginatedBookings().endIndex"></span> 
                    of <span class="font-medium" x-text="getPaginatedBookings().totalItems"></span> results
                </div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <button @click="changePage(pagination.currentPage - 1)" 
                            :disabled="pagination.currentPage === 1"
                            :class="pagination.currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    
                    <template x-for="page in getPaginationPages()" :key="page">
                        <button @click="changePage(page)" 
                                :class="page === pagination.currentPage ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'"
                                class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                x-text="page">
                        </button>
                    </template>
                    
                    <button @click="changePage(pagination.currentPage + 1)" 
                            :disabled="pagination.currentPage === getPaginatedBookings().totalPages"
                            :class="pagination.currentPage === getPaginatedBookings().totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </section>

    {{-- 6. BOOKING LIST - TABLET VIEW (Card Grid) --}}
    <section class="hidden md:grid lg:hidden grid-cols-2 gap-4">
        <template x-for="booking in getPaginatedBookings().items" :key="booking.id">
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 hover:shadow-lg transition-shadow">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="text-sm font-bold text-indigo-600" x-text="booking.id"></p>
                        <p class="text-xs text-gray-500" x-text="booking.date"></p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full" 
                          :class="getPaymentBadgeClass(booking.paymentStatus)" 
                          x-text="booking.paymentStatus">
                    </span>
                </div>
                
                <div class="space-y-2 mb-3">
                    <p class="text-sm font-semibold text-gray-800" x-text="booking.customerName"></p>
                    <p class="text-xs text-gray-600" x-text="booking.serviceType + ' - ' + booking.branchName"></p>
                    <p class="text-xs text-gray-500" x-text="'Duration: ' + booking.duration"></p>
                    <p class="text-sm font-bold text-gray-900" x-text="'Rp ' + booking.revenue.toLocaleString('id-ID')"></p>
                </div>
                
                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                    <button @click="viewDetails(booking)" class="text-xs text-indigo-600 hover:text-indigo-900 font-medium">
                        View Details
                    </button>
                    <div class="flex space-x-2">
                        <button x-show="booking.paymentStatus === 'settlement' && !booking.roomNumber" 
                                @click="assignRoom(booking)"
                                class="text-green-600 hover:text-green-900">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </button>
                        <button @click="printInvoice(booking)" class="text-gray-600 hover:text-gray-900">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>
        
        {{-- Pagination for Tablet --}}
        <div x-show="getPaginatedBookings().totalPages > 1" class="col-span-2 flex justify-center mt-4">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                <button @click="changePage(pagination.currentPage - 1)" 
                        :disabled="pagination.currentPage === 1"
                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500">
                    ◄
                </button>
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                    <span x-text="pagination.currentPage"></span> / <span x-text="getPaginatedBookings().totalPages"></span>
                </span>
                <button @click="changePage(pagination.currentPage + 1)" 
                        :disabled="pagination.currentPage === getPaginatedBookings().totalPages"
                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500">
                    ►
                </button>
            </nav>
        </div>
    </section>

    {{-- 7. BOOKING LIST - MOBILE VIEW (Single Card) --}}
    <section class="md:hidden space-y-3">
        <template x-for="booking in getPaginatedBookings().items" :key="booking.id">
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        <p class="text-sm font-bold text-indigo-600" x-text="booking.id"></p>
                        <p class="text-xs text-gray-500" x-text="booking.customerName"></p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full" 
                          :class="getPaymentBadgeClass(booking.paymentStatus)" 
                          x-text="booking.paymentStatus">
                    </span>
                </div>
                
                <div class="space-y-1 text-xs text-gray-600 mb-3">
                    <p><span class="font-medium">Service:</span> <span x-text="booking.serviceType"></span></p>
                    <p><span class="font-medium">Branch:</span> <span x-text="booking.branchName"></span></p>
                    <p><span class="font-medium">Date:</span> <span x-text="booking.date"></span></p>
                    <p><span class="font-medium">Duration:</span> <span x-text="booking.duration"></span></p>
                </div>
                
                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                    <p class="text-sm font-bold text-gray-900" x-text="'Rp ' + booking.revenue.toLocaleString('id-ID')"></p>
                    <button @click="viewDetails(booking)" class="text-xs text-indigo-600 hover:text-indigo-900 font-medium px-3 py-1 border border-indigo-600 rounded-md">
                        Details
                    </button>
                </div>
            </div>
        </template>
        
        {{-- Pagination for Mobile --}}
        <div x-show="getPaginatedBookings().totalPages > 1" class="flex justify-center mt-4">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm">
                <button @click="changePage(pagination.currentPage - 1)" 
                        :disabled="pagination.currentPage === 1"
                        class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500">
                    Previous
                </button>
                <span class="relative inline-flex items-center px-4 py-2 border-t border-b border-gray-300 bg-white text-sm font-medium text-gray-700">
                    <span x-text="pagination.currentPage"></span> / <span x-text="getPaginatedBookings().totalPages"></span>
                </span>
                <button @click="changePage(pagination.currentPage + 1)" 
                        :disabled="pagination.currentPage === getPaginatedBookings().totalPages"
                        class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500">
                    Next
                </button>
            </nav>
        </div>
    </section>

    {{-- 8. DETAIL MODAL --}}
    <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
            <div @click="showDetailModal = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6 sm:p-8 my-8 max-h-[90vh] overflow-y-auto transform transition-all">
                <div class="flex items-center justify-between border-b pb-3 mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Booking Details</h3>
                    <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <template x-if="selectedBooking">
                    <div class="space-y-6">
                        {{-- Booking ID & Status --}}
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500">Booking ID</p>
                                <p class="text-lg font-bold text-indigo-600" x-text="selectedBooking.id"></p>
                            </div>
                            <div class="text-right space-y-2">
                                <span class="px-3 py-1 text-sm font-semibold rounded-full" 
                                      :class="getPaymentBadgeClass(selectedBooking.paymentStatus)" 
                                      x-text="'Payment: ' + selectedBooking.paymentStatus">
                                </span>
                                <span class="block px-3 py-1 text-sm font-semibold rounded-full" 
                                      :class="getBookingBadgeClass(selectedBooking.bookingStatus)" 
                                      x-text="'Status: ' + selectedBooking.bookingStatus">
                                </span>
                            </div>
                        </div>

                        {{-- Customer Info --}}
                        <div class="border-t pt-4">
                            <p class="font-semibold text-gray-700 mb-3">Customer Information</p>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Name</p>
                                    <p class="font-medium" x-text="selectedBooking.customerName"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Email</p>
                                    <p class="font-medium" x-text="selectedBooking.customerEmail"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Phone</p>
                                    <p class="font-medium" x-text="selectedBooking.customerPhone || 'N/A'"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Booking Source</p>
                                    <p class="font-medium capitalize" x-text="selectedBooking.bookingSource || 'Online'"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Service Details --}}
                        <div class="border-t pt-4">
                            <p class="font-semibold text-gray-700 mb-3">Service & Location</p>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Service Type</p>
                                    <p class="font-medium" x-text="selectedBooking.serviceType"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Mitra</p>
                                    <p class="font-medium" x-text="selectedBooking.mitraName || 'N/A'"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Branch</p>
                                    <p class="font-medium" x-text="selectedBooking.branchName"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Room/Area</p>
                                    <p class="font-medium" x-text="selectedBooking.roomNumber || 'Not Assigned'"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Date</p>
                                    <p class="font-medium" x-text="selectedBooking.date"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Duration</p>
                                    <p class="font-medium" x-text="selectedBooking.duration"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Details --}}
                        <div class="border-t pt-4">
                            <p class="font-semibold text-gray-700 mb-3">Payment Information</p>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Base Amount</span>
                                    <span class="font-medium" x-text="'Rp ' + selectedBooking.revenue.toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Discount</span>
                                    <span class="font-medium text-red-600" x-text="'- Rp ' + (selectedBooking.discount || 0).toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex justify-between pt-2 border-t font-bold text-base">
                                    <span>Total Amount</span>
                                    <span x-text="'Rp ' + (selectedBooking.revenue - (selectedBooking.discount || 0)).toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex justify-between pt-2">
                                    <span class="text-gray-500">Payment Method</span>
                                    <span class="font-medium uppercase" x-text="selectedBooking.paymentMethod || 'N/A'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Transaction ID</span>
                                    <span class="font-medium text-xs" x-text="selectedBooking.transactionId || 'N/A'"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Admin Info --}}
                        <div class="border-t pt-4" x-show="selectedBooking.adminPic">
                            <p class="font-semibold text-gray-700 mb-2">Admin Information</p>
                            <p class="text-sm text-gray-600">Handled by: <span class="font-medium" x-text="selectedBooking.adminPic"></span></p>
                        </div>
                        
                        {{-- Action Buttons --}}
                        <div class="border-t pt-4 space-y-2">
                            <button @click="exportBookingPDF(selectedBooking)" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export Invoice PDF
                            </button>

                            <template x-if="selectedBooking.paymentStatus === 'settlement' && !selectedBooking.roomNumber">
                                <button @click="assignRoomFromDetail()" 
                                        class="w-full px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                    Assign Room
                                </button>
                            </template>

                            <template x-if="selectedBooking.bookingStatus === 'confirmed' && selectedBooking.roomNumber">
                                <button @click="markAsCheckin(selectedBooking)" 
                                        class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Mark as Check-in
                                </button>
                            </template>

                            <template x-if="['pending', 'settlement'].includes(selectedBooking.paymentStatus)">
                                <button @click="cancelBookingFromDetail()" 
                                        class="w-full px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Cancel & Refund
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- 9. CANCEL BOOKING MODAL --}}
    <div x-show="showCancelModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showCancelModal = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
            
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md p-6 transform transition-all">
                <h3 class="text-lg font-bold text-gray-800 mb-4">⚠️ Cancel Booking</h3>
                
                <template x-if="bookingToCancel">
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-3 rounded-lg text-sm">
                            <p><span class="font-medium">Booking ID:</span> <span x-text="bookingToCancel.id"></span></p>
                            <p><span class="font-medium">Customer:</span> <span x-text="bookingToCancel.customerName"></span></p>
                            <p><span class="font-medium">Amount:</span> <span x-text="'Rp ' + bookingToCancel.revenue.toLocaleString('id-ID')"></span></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cancellation Reason</label>
                            <select x-model="cancelReason" class="w-full rounded-lg border-gray-300 text-sm">
                                <option value="">Select reason</option>
                                <option value="customer_request">Customer Request</option>
                                <option value="schedule_conflict">Schedule Conflict</option>
                                <option value="maintenance">Emergency Maintenance</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" x-model="processRefund" class="rounded border-gray-300 text-indigo-600">
                                <span class="ml-2 text-sm text-gray-700">Process Refund</span>
                            </label>
                        </div>

                        <div x-show="processRefund">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Refund Proof</label>
                            <input type="file" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                            <textarea x-model="cancelNotes" rows="3" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Additional notes..."></textarea>
                        </div>

                        <div class="flex space-x-3 pt-2">
                            <button @click="showCancelModal = false" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button @click="confirmCancel()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700">
                                Confirm Cancellation
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

</main>

<script>
    // Pastikan Alpine.js sudah dimuat (misalnya dari CDN) sebelum blok script ini

    document.addEventListener('alpine:init', () => {
        // Mendaftarkan objek data Anda di Alpine.js dengan nama 'bookingMonitor'
        Alpine.data('bookingMonitor', () => ({
            // --- STATE ---
            showDetailModal: false,
            showCancelModal: false,
            showAdvancedFilters: false,
            selectedBooking: null,
            bookingToCancel: null,
            activeQuickFilter: 'all',
            cancelReason: '',
            processRefund: false,
            cancelNotes: '',
            
            // Pagination
            pagination: {
                currentPage: 1,
                perPage: 25
            },

            // Data
            mitras: [
                { 
                    id: 1, 
                    name: 'PT SBY Office', 
                    branches: [
                        { id: 101, name: 'Surabaya Center' }, 
                        { id: 102, name: 'Surabaya Timur' }
                    ] 
                },
                { 
                    id: 2, 
                    name: 'PT JKT Workspace', 
                    branches: [
                        { id: 201, name: 'Jakarta Selatan' }, 
                        { id: 202, name: 'Jakarta Barat' }
                    ] 
                },
            ],
            serviceTypes: ['Meeting Room', 'Private Office', 'Sharing Room', 'Coworking Space', 'Virtual Office', 'Event Space'],

            // Filters
            filters: {
                startDate: '',
                endDate: '',
                mitra: '',
                branch: '',
                service: '',
                paymentStatus: '',
                bookingStatus: '',
                bookingSource: '',
                paymentMethod: '',
                search: ''
            },
            
            // Stats
            stats: { 
                total: 0, 
                revenue: 0, 
                pendingCount: 0, 
                activeCount: 0, 
                cancelledCount: 0 
            },
            
            // Bookings
            allBookings: [],

            // --- INIT ---
            init() {
                // Dipanggil otomatis saat komponen dimuat
                this.generateDummyData();
                this.calculateStats();
                this.setInitialDateRange();
            },
            
            setInitialDateRange() {
                const today = new Date();
                this.filters.endDate = today.toISOString().split('T')[0];
                const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                this.filters.startDate = firstDayOfMonth.toISOString().split('T')[0];
            },

            generateDummyData() {
                this.allBookings = [
                    { id: 'MR-101-001', customerName: 'Budi Santoso', customerEmail: 'budi@corp.com', customerPhone: '+62812345678', serviceType: 'Meeting Room', branchId: 101, branchName: 'Surabaya Center', mitraName: 'PT SBY Office', revenue: 500000, discount: 50000, paymentStatus: 'settlement', bookingStatus: 'confirmed', duration: '5 Jam', date: '2025-10-17', roomNumber: '201', adminPic: 'Admin Budi', bookingSource: 'online', paymentMethod: 'qris', transactionId: 'TRX-001' },
                    { id: 'PO-201-002', customerName: 'Siti Rahayu', customerEmail: 'siti@freelance.net', customerPhone: '+62823456789', serviceType: 'Private Office', branchId: 201, branchName: 'Jakarta Selatan', mitraName: 'PT JKT Workspace', revenue: 4500000, paymentStatus: 'settlement', bookingStatus: 'occupied', duration: '1 Bulan', date: '2025-10-15', roomNumber: '302', adminPic: 'Admin Siti', bookingSource: 'manual', paymentMethod: 'va' },
                    { id: 'CS-102-003', customerName: 'Ahmad Wijaya', customerEmail: 'ahmad@startup.co', customerPhone: '+62834567890', serviceType: 'Coworking Space', branchId: 102, branchName: 'Surabaya Timur', mitraName: 'PT SBY Office', revenue: 75000, paymentStatus: 'pending', bookingStatus: 'confirmed', duration: '1 Hari', date: '2025-10-17', roomNumber: null, adminPic: 'Admin Eko', bookingSource: 'online', paymentMethod: 'ewallet' },
                    { id: 'ES-202-004', customerName: 'Dewi Lestari', customerEmail: 'dewi@event.com', customerPhone: '+62845678901', serviceType: 'Event Space', branchId: 202, branchName: 'Jakarta Barat', mitraName: 'PT JKT Workspace', revenue: 15000000, paymentStatus: 'expired', bookingStatus: 'cancelled', duration: '1 Hari', date: '2025-10-14', roomNumber: 'EVT-01', adminPic: 'Auto-booked', bookingSource: 'online', paymentMethod: 'va' },
                    { id: 'VO-101-005', customerName: 'Eko Prasetyo', customerEmail: 'eko@virtual.com', customerPhone: '+62856789012', serviceType: 'Virtual Office', branchId: 101, branchName: 'Surabaya Center', mitraName: 'PT SBY Office', revenue: 1200000, paymentStatus: 'settlement', bookingStatus: 'completed', duration: '1 Tahun', date: '2025-09-01', roomNumber: null, adminPic: null, bookingSource: 'online', paymentMethod: 'va' },
                    { id: 'MR-101-006', customerName: 'Rina Kusuma', customerEmail: 'rina@company.id', customerPhone: '+62867890123', serviceType: 'Meeting Room', branchId: 101, branchName: 'Surabaya Center', mitraName: 'PT SBY Office', revenue: 300000, paymentStatus: 'settlement', bookingStatus: 'confirmed', duration: '3 Jam', date: '2025-10-17', roomNumber: null, adminPic: 'Admin Budi', bookingSource: 'walk-in', paymentMethod: 'cash' },
                    { id: 'SR-102-007', customerName: 'Hadi Saputra', customerEmail: 'hadi@tech.co', customerPhone: '+62878901234', serviceType: 'Sharing Room', branchId: 102, branchName: 'Surabaya Timur', mitraName: 'PT SBY Office', revenue: 2500000, paymentStatus: 'settlement', bookingStatus: 'occupied', duration: '1 Bulan', date: '2025-10-10', roomNumber: '306', adminPic: 'Admin Eko', bookingSource: 'online', paymentMethod: 'qris' },
                    { id: 'MR-201-008', customerName: 'Lisa Anggraini', customerEmail: 'lisa@startup.io', customerPhone: '+62889012345', serviceType: 'Meeting Room', branchId: 201, branchName: 'Jakarta Selatan', mitraName: 'PT JKT Workspace', revenue: 400000, paymentStatus: 'pending', bookingStatus: 'confirmed', duration: '4 Jam', date: '2025-10-18', roomNumber: null, adminPic: null, bookingSource: 'online', paymentMethod: 'va' },
                ];
            },

            calculateStats() {
                this.stats.total = this.allBookings.length;
                this.stats.revenue = this.allBookings.reduce((sum, b) => sum + b.revenue, 0);
                this.stats.pendingCount = this.allBookings.filter(b => b.paymentStatus === 'pending').length;
                this.stats.activeCount = this.allBookings.filter(b => ['confirmed', 'occupied'].includes(b.bookingStatus)).length;
                this.stats.cancelledCount = this.allBookings.filter(b => b.bookingStatus === 'cancelled').length;
            },

            // --- FILTER LOGIC ---
            getFilteredBranches() {
                if (!this.filters.mitra) {
                    return this.mitras.flatMap(m => m.branches);
                }
                const selectedMitra = this.mitras.find(m => m.id == this.filters.mitra);
                return selectedMitra ? selectedMitra.branches : [];
            },
            
            quickFilter(type) {
                this.activeQuickFilter = type;
                const today = new Date().toISOString().split('T')[0];
                
                // Reset status filters
                this.filters.paymentStatus = '';
                this.filters.bookingStatus = '';
                
                switch(type) {
                    case 'all':
                        // Reset date range
                        this.setInitialDateRange(); 
                        break;
                    case 'today':
                        this.filters.startDate = today;
                        this.filters.endDate = today;
                        break;
                    case 'this-week':
                        const startOfWeek = new Date();
                        startOfWeek.setDate(startOfWeek.getDate() - startOfWeek.getDay());
                        this.filters.startDate = startOfWeek.toISOString().split('T')[0];
                        this.filters.endDate = today;
                        break;
                    case 'pending':
                        this.filters.paymentStatus = 'pending';
                        this.setInitialDateRange();
                        break;
                    case 'need-assignment':
                        this.filters.paymentStatus = 'settlement';
                        this.setInitialDateRange();
                        break;
                    case 'active':
                        this.filters.bookingStatus = 'occupied';
                        this.setInitialDateRange();
                        break;
                }
                this.pagination.currentPage = 1;
            },

            applyFilters() {
                this.pagination.currentPage = 1;
                this.activeQuickFilter = 'custom'; // Tandai filter custom
            },

            resetFilters() {
                this.filters = {
                    startDate: this.filters.startDate,
                    endDate: this.filters.endDate,
                    mitra: '',
                    branch: '',
                    service: '',
                    paymentStatus: '',
                    bookingStatus: '',
                    bookingSource: '',
                    paymentMethod: '',
                    search: ''
                };
                this.activeQuickFilter = 'all';
                this.setInitialDateRange();
                this.pagination.currentPage = 1;
            },

            getFilteredBookings() {
                let filtered = this.allBookings;
                
                // --- Apply Date Range Filter ---
                if (this.filters.startDate && this.filters.endDate) {
                    const start = new Date(this.filters.startDate).getTime();
                    const end = new Date(this.filters.endDate).getTime();
                    
                    filtered = filtered.filter(b => {
                        const bookingDate = new Date(b.date).getTime();
                        // Bandingkan tanggal saja, abaikan jam
                        return bookingDate >= start && bookingDate <= end; 
                    });
                }
                
                // --- Apply Other Filters ---
                if (this.filters.mitra) {
                    const mitraBranches = this.getFilteredBranches().map(b => b.id);
                    filtered = filtered.filter(b => mitraBranches.includes(b.branchId));
                }
                
                if (this.filters.branch) {
                    filtered = filtered.filter(b => b.branchId == this.filters.branch);
                }
                
                if (this.filters.service) {
                    filtered = filtered.filter(b => b.serviceType === this.filters.service);
                }
                
                if (this.filters.paymentStatus) {
                    filtered = filtered.filter(b => b.paymentStatus === this.filters.paymentStatus);
                }
                
                if (this.filters.bookingStatus) {
                    filtered = filtered.filter(b => b.bookingStatus === this.filters.bookingStatus);
                }
                
                if (this.filters.bookingSource) {
                    filtered = filtered.filter(b => b.bookingSource === this.filters.bookingSource);
                }
                
                if (this.filters.paymentMethod) {
                    filtered = filtered.filter(b => b.paymentMethod === this.filters.paymentMethod);
                }
                
                // Need assignment filter (special case for quick filter)
                if (this.activeQuickFilter === 'need-assignment') {
                    // Filter berdasarkan paymentStatus sudah diatur di quickFilter
                    filtered = filtered.filter(b => !b.roomNumber);
                }
                
                // Search filter
                if (this.filters.search) {
                    const search = this.filters.search.toLowerCase();
                    filtered = filtered.filter(b => 
                        b.id.toLowerCase().includes(search) || 
                        b.customerName.toLowerCase().includes(search) ||
                        b.customerEmail.toLowerCase().includes(search) ||
                        (b.customerPhone && b.customerPhone.includes(search))
                    );
                }

                return filtered;
            },

            // --- PAGINATION ---
            getPaginatedBookings() {
                const filtered = this.getFilteredBookings();
                const totalItems = filtered.length;
                const totalPages = Math.ceil(totalItems / this.pagination.perPage);
                const startIndex = (this.pagination.currentPage - 1) * this.pagination.perPage;
                const endIndex = Math.min(startIndex + this.pagination.perPage, totalItems);
                const items = filtered.slice(startIndex, endIndex);

                return {
                    items,
                    totalItems,
                    totalPages,
                    startIndex: totalItems > 0 ? startIndex + 1 : 0,
                    endIndex
                };
            },

            getPaginationPages() {
                const totalPages = this.getPaginatedBookings().totalPages;
                const currentPage = this.pagination.currentPage;
                const pages = [];
                
                // Logika pagination untuk menampilkan halaman di sekitar halaman saat ini (maks 7)
                let startPage = Math.max(1, currentPage - 3);
                let endPage = Math.min(totalPages, currentPage + 3);
                
                if (currentPage <= 4) {
                    endPage = Math.min(7, totalPages);
                }
                if (currentPage >= totalPages - 3) {
                    startPage = Math.max(1, totalPages - 6);
                }
                
                for (let i = startPage; i <= endPage; i++) {
                    pages.push(i);
                }
                
                return pages;
            },

            changePage(page) {
                const totalPages = this.getPaginatedBookings().totalPages;
                if (page >= 1 && page <= totalPages) {
                    this.pagination.currentPage = page;
                    // Scroll ke atas tabel untuk pengalaman pengguna yang lebih baik
                    window.scrollTo({ top: 0, behavior: 'smooth' }); 
                }
            },

            // --- MODALS & ACTIONS ---
            viewDetails(booking) {
                this.selectedBooking = booking;
                this.showDetailModal = true;
            },

            openManualBookingModal() {
                alert('Manual Booking Modal - To be implemented');
            },

            assignRoom(booking) {
                alert(`Assign Room for: ${booking.id}\nCustomer: ${booking.customerName}`);
            },

            assignRoomFromDetail() {
                this.showDetailModal = false;
                this.assignRoom(this.selectedBooking);
            },

            markAsCheckin(booking) {
                if (confirm(`Mark booking ${booking.id} as checked-in?`)) {
                    const index = this.allBookings.findIndex(b => b.id === booking.id);
                    if (index !== -1) {
                        this.allBookings[index].bookingStatus = 'occupied';
                        alert('Booking marked as checked-in successfully!');
                        // Gunakan $nextTick atau panggil fungsi ini jika Anda menggunakan data reaktif dari backend
                        this.calculateStats(); 
                    }
                }
            },

            printInvoice(booking) {
                alert(`Print Invoice for: ${booking.id}`);
            },

            exportBookingPDF(booking) {
                alert(`Export PDF for: ${booking.id}`);
            },

            cancelBooking(booking) {
                this.bookingToCancel = booking;
                this.showCancelModal = true;
                this.cancelReason = '';
                this.processRefund = false;
                this.cancelNotes = '';
            },

            cancelBookingFromDetail() {
                this.showDetailModal = false;
                this.cancelBooking(this.selectedBooking);
            },

            confirmCancel() {
                if (!this.cancelReason) {
                    alert('Please select a cancellation reason');
                    return;
                }

                if (confirm(`Are you sure you want to cancel booking ${this.bookingToCancel.id}?`)) {
                    const index = this.allBookings.findIndex(b => b.id === this.bookingToCancel.id);
                    if (index !== -1) {
                        this.allBookings[index].bookingStatus = 'cancelled';
                        this.allBookings[index].paymentStatus = 'cancelled';
                        alert('Booking cancelled successfully!');
                        this.calculateStats();
                        this.showCancelModal = false;
                        this.bookingToCancel = null;
                    }
                }
            },

            // --- UI HELPERS (untuk kelas warna badge) ---
            getPaymentBadgeClass(status) {
                switch(status) {
                    case 'settlement': return 'bg-green-100 text-green-800';
                    case 'pending': return 'bg-yellow-100 text-yellow-800';
                    case 'expired': return 'bg-red-100 text-red-800';
                    case 'cancelled': return 'bg-gray-100 text-gray-800';
                    default: return 'bg-gray-100 text-gray-600';
                }
            },

            getBookingBadgeClass(status) {
                switch(status) {
                    case 'confirmed': return 'bg-indigo-100 text-indigo-800';
                    case 'occupied': return 'bg-purple-100 text-purple-800';
                    case 'completed': return 'bg-green-100 text-green-800';
                    case 'cancelled': 
                    case 'no-show': return 'bg-red-100 text-red-800';
                    default: return 'bg-gray-100 text-gray-600';
                }
            }
        }))
    });
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection