@extends('layouts.admin')

@section('title', 'All Booking')

@section('content')
<div x-data="bookingAllData()" x-init="init()" class="space-y-4 md:space-y-6">
    
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">All Booking</h1>
            <p class="text-sm text-gray-600 mt-1">Manage and track all booking transactions</p>
        </div>
        <div class="flex items-center gap-2 sm:gap-3">
            <button x-on:click="exportExcel()" 
                    class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center justify-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="hidden sm:inline">Export Excel</span>
                <span class="sm:hidden">Export</span>
            </button>   
            <a href="{{ route('admin.booking.walk-in-booking') }}" class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="hidden sm:inline">Walk-in Booking</span>
                <span class="sm:hidden">Walk-in</span>
            </a>
        </div>
    </div>
    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-600 truncate">Settlement</p>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800" x-text="summary.settlement">0</h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-600 truncate">Pending</p>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800" x-text="summary.pending">0</h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-600 truncate">Expire</p>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800" x-text="summary.expire">0</h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-600 truncate">Total</p>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800" x-text="summary.total">0</h3>
                </div>
            </div>
        </div>
    </div>
    {{-- Filter Section --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base md:text-lg font-semibold text-gray-800">Filter & Search</h2>
            <button @click="resetFilter()" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                Reset All
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            {{-- Status Filter --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Payment</label>
                <select x-model="filters.status" @change="applyFilters()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="settlement">Settlement</option>
                    <option value="pending">Pending</option>
                    <option value="expire">Expire</option>
                </select>
            </div>

            {{-- Service Filter --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Service Type</label>
                <select x-model="filters.service" @change="applyFilters()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Services</option>
                    <option value="Meeting Room">Meeting Room</option>
                    <option value="Private Office">Private Office</option>
                    <option value="Sharing Room">Sharing Room</option>
                    <option value="Coworking Space">Coworking Space</option>
                    <option value="Virtual Office">Virtual Office</option>
                    <option value="Event Space">Event Space</option>
                </select>
            </div>

            {{-- Date Filter --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Booking Date</label>
                <input type="date" x-model="filters.date" @change="applyFilters()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Date Range From --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" x-model="filters.dateFrom" @change="applyFilters()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Date Range To --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" x-model="filters.dateTo" @change="applyFilters()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Search --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <div class="relative">
                    <input type="text" x-model="filters.search" placeholder="Search by name, booking ID, phone..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Loading State --}}
        <div x-show="loading" class="mt-4 flex items-center justify-center py-4">
            <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-2 text-sm text-gray-600">Loading bookings...</span>
        </div>

        {{-- Error State --}}
        <div x-show="error" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-800" x-text="error"></p>
            <button @click="loadBookings()" class="mt-2 text-sm text-red-600 hover:text-red-700 font-medium">
                Try Again
            </button>
        </div>

        {{-- HAPUS BAGIAN INI: --}}
        {{-- 
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mt-4 pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                Showing <span class="font-semibold" x-text="filteredBookings.length"></span> of <span class="font-semibold" x-text="bookings.length"></span> bookings
            </p>
            <div class="flex items-center gap-3">
                <label class="text-sm text-gray-600">Show</label>
                <select x-model="entriesPerPage" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <label class="text-sm text-gray-600">entries</label>
            </div>
        </div>
        --}}
    </div>

    {{-- Table Section - Desktop View --}}
    {{-- Container Pembungkus Utama untuk tampilan responsif --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden p-0">

        {{-- ========================================================================= --}}
        {{-- 1. CARD VIEW (Mobile: 1 kolom, Tablet: 2 kolom, Desktop: 3 kolom)           --}}
        {{--    Menggantikan tampilan tabel di semua ukuran, sesuai permintaan 3 cards/baris di desktop --}}
        {{-- ========================================================================= --}}
        <div class="p-4 sm:p-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                <template x-for="booking in paginatedBookings" :key="booking.id">
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 hover:shadow-md transition">
                        {{-- Header Card --}}
                        <div class="flex items-start justify-between border-b border-gray-100 pb-3 mb-3">
                            <div>
                                <p class="text-xs text-gray-500">Booking ID</p>
                                <span class="text-sm font-semibold text-blue-600 cursor-pointer hover:underline" @click="viewDetail(booking)" x-text="booking.bookingId"></span>
                            </div>
                            
                            {{-- Status Pembayaran --}}
                            <span :class="{
                                'bg-green-100 text-green-800': booking.paymentStatus === 'settlement',
                                'bg-yellow-100 text-yellow-800': booking.paymentStatus === 'pending',
                                'bg-red-100 text-red-800': booking.paymentStatus === 'expired'
                            }" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium whitespace-nowrap">
                                <span x-text="booking.paymentStatus.charAt(0).toUpperCase() + booking.paymentStatus.slice(1)"></span>
                            </span>
                        </div>

                        {{-- Detail Card --}}
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <p class="text-gray-600">Customer:</p>
                                <p class="font-medium text-right text-gray-800" x-text="booking.customerName"></p>
                            </div>
                            <div class="flex justify-between">
                                <p class="text-gray-600">Service:</p>
                                <p class="font-medium text-right text-gray-800" x-text="booking.service"></p>
                            </div>
                            <div class="flex justify-between">
                                <p class="text-gray-600">Date/Time:</p>
                                <p class="font-medium text-right text-gray-800">
                                    <span x-text="booking.bookingDate"></span> - <span x-text="booking.bookingTime"></span>
                                </p>
                            </div>
                            <div class="flex justify-between">
                                <p class="text-gray-600">Duration:</p>
                                <p class="font-medium text-right text-gray-800" x-text="booking.duration"></p>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-100">
                                <p class="text-lg font-semibold text-gray-800">Total:</p>
                                <p class="text-lg font-semibold text-blue-700" x-text="formatCurrency(booking.total)"></p>
                            </div>
                        </div>

                        {{-- Footer/Action Card --}}
                        <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end gap-2">
                            <button @click="viewDetail(booking)" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs rounded-lg hover:bg-gray-200 transition">
                                Detail
                            </button>
                            <button x-show="booking.paymentStatus === 'settlement' && booking.assignmentStatus === 'unassigned'" @click="assignNow(booking)" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition">
                                Assign
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>


        {{-- ================================================= --}}
        {{-- 2. PAGINATION (Berada di luar grid)                --}}
        {{-- ================================================= --}}
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200 flex items-center justify-between" x-show="!loading && bookings.length > 0">
            <p class="text-sm text-gray-600">
                Showing <span class="font-semibold" x-text="paginationStart"></span> to 
                <span class="font-semibold" x-text="paginationEnd"></span> of 
                <span class="font-semibold" x-text="totalItems"></span> entries
            </p>
            <div class="flex gap-2">
                <button @click="previousPage()" 
                        :disabled="currentPage === 1 || loading"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Previous
                </button>
                
                <template x-for="page in totalPages" :key="page">
                    <button @click="goToPage(page)" 
                            :class="currentPage === page ? 'bg-blue-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50'"
                            class="px-3 py-1.5 rounded-lg text-sm transition"
                            x-text="page"></button>
                </template>
                
                <button @click="nextPage()" 
                        :disabled="currentPage === totalPages || loading"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Next
                </button>
            </div>
        </div>
    </div>

    {{-- Booking Detail Modal --}}
    <div x-show="showDetailModal" x-cloak @click.away="showDetailModal = false" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-black opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-4 md:p-6 transform transition-all max-h-screen overflow-y-auto">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between mb-4 md:mb-6 pb-4 border-b border-gray-200">
                    <div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-800">Booking Detail</h3>
                        <p class="text-sm text-gray-600 mt-1" x-text="selectedBooking?.bookingId"></p>
                    </div>
                    <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Modal Content --}}
                <div class="space-y-4 md:space-y-6">
                    {{-- Booking Information --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Booking Information
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-gray-50 rounded-lg p-4">
                            <div>
                                <p class="text-xs text-gray-600">Booking ID</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedBooking?.bookingId"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Transaction Date</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedBooking?.transactionDate"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Booking Date</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedBooking?.bookingDate"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Booking Time</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedBooking?.bookingTime"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Duration</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedBooking?.duration"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Payment Status</p>
                                <span x-show="selectedBooking?.paymentStatus === 'settlement'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Settlement</span>
                                <span x-show="selectedBooking?.paymentStatus === 'pending'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                <span x-show="selectedBooking?.paymentStatus === 'expired'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Expired</span>
                            </div>
                        </div>
                    </div>

                    {{-- Customer Information --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Customer Information
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-gray-50 rounded-lg p-4">
                            <div>
                                <p class="text-xs text-gray-600">Full Name</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedBooking?.customerName"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Phone Number</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedBooking?.customerPhone"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Email</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedBooking?.customerEmail || '-'"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Company</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedBooking?.customerCompany || '-'"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Service Details --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Service Details
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-gray-50 rounded-lg p-4">
                            <div>
                                <p class="text-xs text-gray-600">Service Type</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedBooking?.service"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Package</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedBooking?.package"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Participants</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedBooking?.participants || '-'"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Room Assigned</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedBooking?.roomNumber || 'Not assigned yet'"></p>
                            </div>
                        </div>
                    </div>
                    {{-- Payment Information --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Payment Information
                        </h4>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Base Price</span>
                                <span class="font-medium text-gray-800" x-text="formatCurrency(selectedBooking?.basePrice || 0)"></span>
                            </div>
                            
                            {{-- ✅ LUNCH ITEMS --}}
                            <template x-if="selectedBooking?.lunchItems && selectedBooking.lunchItems.length > 0">
                                <div class="border-t border-gray-300 pt-2 mt-2">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Lunch Items:</p>
                                    <template x-for="lunch in selectedBooking.lunchItems" :key="lunch.lunch_option">
                                        <div class="flex justify-between text-sm mb-1">
                                            <div>
                                                <span class="text-gray-600" x-text="lunch.quantity + ' x ' + lunch.lunch_option"></span>
                                                <span class="text-xs text-gray-500 ml-2" x-text="'@ ' + formatCurrency(lunch.unit_price)"></span>
                                            </div>
                                            <span class="font-medium text-gray-800" x-text="formatCurrency(lunch.subtotal)"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            
                            {{-- ✅ LUNCH TOTAL --}}
                            <div x-show="selectedBooking?.lunchTotal > 0" class="flex justify-between text-sm border-t border-gray-300 pt-2">
                                <span class="text-gray-600">Lunch Total</span>
                                <span class="font-medium text-green-600" x-text="formatCurrency(selectedBooking?.lunchTotal || 0)"></span>
                            </div>
                            
                            <div class="flex justify-between text-sm" x-show="selectedBooking?.discount > 0">
                                <span class="text-gray-600">Discount</span>
                                <span class="font-medium text-green-600" x-text="'- ' + formatCurrency(selectedBooking?.discount || 0)"></span>
                            </div>
                            
                            <div class="flex justify-between pt-2 border-t border-gray-300">
                                <span class="text-sm font-semibold text-gray-700">Total Payment</span>
                                <span class="text-lg font-bold text-gray-800" x-text="formatCurrency(selectedBooking?.total || 0)"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Actions --}}
                <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-6 border-t border-gray-200">
                    <button @click="showDetailModal = false" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Close
                    </button>
                    <button x-show="selectedBooking?.assignmentStatus === 'assigned'" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">
                        View Assignment
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Alpine.js Data & Logic --}}
@push('scripts')
<script>
function bookingAllData() {
    return {
        // State Management
        loading: false,
        error: null,
        
        // Data dari API
        bookings: [],
        summary: {
            settlement: 0,
            pending: 0,
            expire: 0,
            total: 0
        },

        // Filters
        filters: {
            status: '',
            service: '',
            date: '',
            dateFrom: '',
            dateTo: '',
            search: '',
            searchQuery: '' // Untuk debounced search
        },

        // Pagination
        currentPage: 1,
        entriesPerPage: 10,
        totalItems: 0,
        totalPages: 0,

        // Modal
        showDetailModal: false,
        selectedBooking: null,

        // Search debounce
        searchTimeout: null,

        // Initialize - Load data dari API
        async init() {
            await this.loadBookings();
            
            // Setup search debounce
            this.$watch('filters.search', (value) => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.filters.searchQuery = value;
                    this.currentPage = 1;
                    this.loadBookings();
                }, 500);
            });
        },

        // Load data dari API
        async loadBookings() {
            this.loading = true;
            this.error = null;
            
            try {
                const params = new URLSearchParams();
                
                // Add filters to params
                if (this.filters.status) params.append('status', this.filters.status);
                if (this.filters.service) params.append('service', this.filters.service);
                if (this.filters.date) params.append('date', this.filters.date);
                if (this.filters.dateFrom) params.append('date_from', this.filters.dateFrom);
                if (this.filters.dateTo) params.append('date_to', this.filters.dateTo);
                if (this.filters.searchQuery) params.append('search', this.filters.searchQuery);
                
                // Pagination
                params.append('per_page', this.entriesPerPage);
                params.append('page', this.currentPage);
                
                const response = await fetch(`/booking/all/api/data?${params.toString()}`);
                const result = await response.json();
                
                if (result.success) {
                    this.bookings = result.data.bookings;
                    this.summary = result.data.summary;
                    this.totalItems = result.data.pagination.total_items;
                    this.totalPages = result.data.pagination.total_pages;
                    this.currentPage = result.data.pagination.current_page;
                } else {
                    this.error = result.message || 'Failed to load bookings';
                    console.error('API Error:', result);
                }
            } catch (error) {
                this.error = 'Network error: Failed to fetch bookings';
                console.error('Fetch Error:', error);
            } finally {
                this.loading = false;
            }
        },

        // Load filter options dari API
        async loadFilterOptions() {
            try {
                const response = await fetch('/booking/all/api/filter-options');
                const result = await response.json();
                
                if (result.success) {
                    return result.data;
                }
            } catch (error) {
                console.error('Failed to load filter options:', error);
            }
            return null;
        },

        // Computed Properties - Disesuaikan dengan data dari API
        get paginatedBookings() {
            return this.bookings;
        },

        get paginationStart() {
            return ((this.currentPage - 1) * this.entriesPerPage) + 1;
        },

        get paginationEnd() {
            const end = this.currentPage * this.entriesPerPage;
            return end > this.totalItems ? this.totalItems : end;
        },

        // Methods
        formatCurrency(value) {
            return 'Rp ' + Number(value).toLocaleString('id-ID');
        },

        async resetFilter() {
            this.filters = {
                status: '',
                service: '',
                date: '',
                dateFrom: '',
                dateTo: '',
                search: '',
                searchQuery: ''
            };
            this.currentPage = 1;
            await this.loadBookings();
        },

        async applyFilters() {
            this.currentPage = 1;
            await this.loadBookings();
        },

        async previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                await this.loadBookings();
            }
        },

        async nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                await this.loadBookings();
            }
        },

        async goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
                await this.loadBookings();
            }
        },

        viewDetail(booking) {
            this.selectedBooking = booking;
            this.showDetailModal = true;
        },

        // Di Alpine.js - PERBAIKI METHOD exportExcel
       async exportExcel() {
            try {
                if (this.totalItems === 0) {
                    this.showNotification('No data to export', 'error');
                    return;
                }
                
                console.log('Starting export...');
                
                // Build export URL dengan filters
                const params = new URLSearchParams();
                
                if (this.filters.status) params.append('status', this.filters.status);
                if (this.filters.service) params.append('service', this.filters.service);
                if (this.filters.date) params.append('date', this.filters.date);
                if (this.filters.dateFrom) params.append('date_from', this.filters.dateFrom);
                if (this.filters.dateTo) params.append('date_to', this.filters.dateTo);
                if (this.filters.searchQuery) params.append('search', this.filters.searchQuery);
                
                console.log('Exporting with params:', params.toString());
                
                this.showNotification('Preparing export file...', 'info');
                
                // Panggil API export
                const response = await fetch(`/booking/all/api/export?${params.toString()}`);
                
                if (response.ok) {
                    const blob = await response.blob();
                    
                    // Cek jika response error (JSON)
                    if (blob.type === 'application/json') {
                        const errorData = await blob.text();
                        throw new Error(JSON.parse(errorData).message || 'Export failed');
                    }
                    
                    // Download CSV file
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    
                    // Get filename from header atau default
                    const contentDisposition = response.headers.get('content-disposition');
                    let filename = `bookings-${new Date().toISOString().split('T')[0]}.csv`;
                    
                    if (contentDisposition) {
                        const filenameMatch = contentDisposition.match(/filename="(.+)"/);
                        if (filenameMatch) {
                            filename = filenameMatch[1];
                        }
                    }
                    
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                    
                    this.showNotification(`Successfully exported ${this.totalItems} bookings to CSV`, 'success');
                    
                } else {
                    // Handle HTTP errors
                    const errorText = await response.text();
                    let errorMessage = `Export failed: ${response.status}`;
                    
                    try {
                        const errorData = JSON.parse(errorText);
                        errorMessage = errorData.message || errorMessage;
                    } catch (e) {
                        errorMessage = errorText || errorMessage;
                    }
                    
                    throw new Error(errorMessage);
                }
                
            } catch (error) {
                console.error('Export error:', error);
                this.showNotification(
                    error.message || 'Failed to export data. Please try again.', 
                    'error'
                );
            }
        },

        // ✅ Notification method
        showNotification(message, type = 'info') {
            // Buat notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg border-l-4 ${
                type === 'success' ? 'bg-green-50 border-green-400 text-green-700' :
                type === 'error' ? 'bg-red-50 border-red-400 text-red-700' :
                'bg-blue-50 border-blue-400 text-blue-700'
            }`;
            
            notification.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${
                            type === 'success' ? 
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
                            type === 'error' ?
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                        }
                    </svg>
                    <span class="text-sm">${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove setelah 5 detik
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        },

        // ✅ METHOD BARU: Reset export button
        resetExportButton() {
            const buttons = this.$root.querySelectorAll('button[ x-on\\:click="exportExcel()"]');
            buttons.forEach(button => {
                button.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="hidden sm:inline">Export Excel</span>
                    <span class="sm:hidden">Export</span>
                `;
                button.disabled = false;
            });
        },

        // Helper method untuk status colors
        getStatusColor(status) {
            const colors = {
                settlement: { bg: 'bg-green-100', text: 'text-green-800' },
                pending: { bg: 'bg-yellow-100', text: 'text-yellow-800' },
                expire: { bg: 'bg-red-100', text: 'text-red-800' }
            };
            return colors[status] || { bg: 'bg-gray-100', text: 'text-gray-800' };
        },

        // Format date untuk display
        formatDate(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }
    }
}
</script>
@endpush

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
@endsection