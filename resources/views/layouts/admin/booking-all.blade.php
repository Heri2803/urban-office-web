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
            <button @click="exportExcel()" class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center justify-center gap-2 text-sm">
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
                    <p class="text-xs text-gray-600 truncate">Expired</p>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800" x-text="summary.expired">0</h3>
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
                <select x-model="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="settlement">Settlement</option>
                    <option value="pending">Pending</option>
                    <option value="expired">Expired</option>
                </select>
            </div>

            {{-- Service Filter --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Service Type</label>
                <select x-model="filters.service" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Services</option>
                    <option value="meeting">Meeting Room</option>
                    <option value="private">Private Office</option>
                    <option value="sharing">Sharing Room</option>
                    <option value="coworking">Coworking Space</option>
                    <option value="virtual">Virtual Office</option>
                    <option value="event">Event Space</option>
                </select>
            </div>

            {{-- Assignment Status Filter --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assignment Status</label>
                <select x-model="filters.assignment" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All</option>
                    <option value="assigned">Sudah Ditempatkan</option>
                    <option value="unassigned">Belum Ditempatkan</option>
                </select>
            </div>

            {{-- Date Filter --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Booking Date</label>
                <input type="date" x-model="filters.date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Date Range From --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" x-model="filters.dateFrom" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Date Range To --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" x-model="filters.dateTo" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
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
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p class="text-sm text-gray-600">
                Showing <span class="font-semibold" x-text="paginationStart"></span> to <span class="font-semibold" x-text="paginationEnd"></span> of <span class="font-semibold" x-text="filteredBookings.length"></span> entries
            </p>
            <div class="flex gap-2">
                <button @click="previousPage()" :disabled="currentPage === 1" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Previous
                </button>
                <template x-for="page in totalPages" :key="page">
                    <button @click="currentPage = page" :class="currentPage === page ? 'bg-blue-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50'" class="px-3 py-1.5 rounded-lg text-sm transition" x-text="page"></button>
                </template>
                <button @click="nextPage()" :disabled="currentPage === totalPages" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed">
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
                            <div class="flex justify-between text-sm" x-show="selectedBooking?.discount > 0">
                                <span class="text-gray-600">Discount</span>
                                <span class="font-medium text-green-600" x-text="'- ' + formatCurrency(selectedBooking?.discount || 0)"></span>
                            </div>
                            <div class="flex justify-between text-sm" x-show="selectedBooking?.voucher">
                                <span class="text-gray-600">Voucher Applied</span>
                                <span class="font-medium text-green-600" x-text="selectedBooking?.voucher || '-'"></span>
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
                    <button x-show="selectedBooking?.paymentStatus === 'settlement' && selectedBooking?.assignmentStatus === 'unassigned'" @click="assignNow(selectedBooking)" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                        Assign Room Now
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
        // Summary Stats
        summary: {
            settlement: 25,
            pending: 10,
            expired: 3,
            total: 38
        },

        // Filters
        filters: {
            status: '',
            service: '',
            assignment: '',
            date: '',
            dateFrom: '',
            dateTo: '',
            search: ''
        },

        // Pagination
        currentPage: 1,
        entriesPerPage: 10,

        // Modal
        showDetailModal: false,
        selectedBooking: null,

        // Sample Booking Data
        bookings: [
            {
                id: 1,
                bookingId: '#MR-089',
                transactionDate: '08 Oct 2025, 15:30',
                bookingDate: '09 Oct 2025',
                bookingTime: '09:00 - 11:00',
                customerName: 'Budi Santoso',
                customerPhone: '0812-3456-7890',
                customerEmail: 'budi@email.com',
                customerCompany: 'PT Maju Jaya',
                service: 'Meeting Room',
                package: '2 Jam',
                duration: '2 Hours',
                participants: '8 people',
                paymentStatus: 'settlement',
                assignmentStatus: 'assigned',
                roomNumber: 'Room 201',
                basePrice: 200000,
                discount: 20000,
                voucher: null,
                total: 180000,
                serviceType: 'meeting'
            },
            {
                id: 2,
                bookingId: '#PO-045',
                transactionDate: '08 Oct 2025, 14:15',
                bookingDate: '09 Oct 2025',
                bookingTime: 'Full Day',
                customerName: 'Ani Wijaya',
                customerPhone: '0813-5678-9012',
                customerEmail: 'ani@email.com',
                customerCompany: 'CV Sukses Mandiri',
                service: 'Private Office',
                package: 'Monthly',
                duration: '1 Month',
                participants: '4 people',
                paymentStatus: 'settlement',
                assignmentStatus: 'assigned',
                roomNumber: 'Room 301',
                basePrice: 3000000,
                discount: 0,
                voucher: null,
                total: 3000000,
                serviceType: 'private'
            },
            {
                id: 3,
                bookingId: '#MR-090',
                transactionDate: '08 Oct 2025, 13:45',
                bookingDate: '10 Oct 2025',
                bookingTime: '14:00 - 18:00',
                customerName: 'Siti Rahayu',
                customerPhone: '0814-6789-0123',
                customerEmail: 'siti@email.com',
                customerCompany: null,
                service: 'Meeting Room',
                package: '4 Jam',
                duration: '4 Hours',
                participants: '12 people',
                paymentStatus: 'pending',
                assignmentStatus: 'unassigned',
                roomNumber: null,
                basePrice: 400000,
                discount: 80000,
                voucher: null,
                total: 320000,
                serviceType: 'meeting'
            },
            {
                id: 4,
                bookingId: '#CW-012',
                transactionDate: '08 Oct 2025, 12:30',
                bookingDate: '09 Oct 2025',
                bookingTime: '08:00 - 17:00',
                customerName: 'Joko Prasetyo',
                customerPhone: '0815-7890-1234',
                customerEmail: 'joko@email.com',
                customerCompany: 'Freelancer',
                service: 'Coworking Space',
                package: 'Day Pass',
                duration: '1 Day',
                participants: '1 person',
                paymentStatus: 'settlement',
                assignmentStatus: 'assigned',
                roomNumber: 'N/A',
                basePrice: 50000,
                discount: 0,
                voucher: null,
                total: 50000,
                serviceType: 'coworking'
            },
            {
                id: 5,
                bookingId: '#VO-008',
                transactionDate: '08 Oct 2025, 11:00',
                bookingDate: '09 Oct 2025',
                bookingTime: 'Start Date',
                customerName: 'Dewi Kusuma',
                customerPhone: '0816-8901-2345',
                customerEmail: 'dewi@email.com',
                customerCompany: 'Startup ABC',
                service: 'Virtual Office',
                package: 'Yearly',
                duration: '1 Year',
                participants: null,
                paymentStatus: 'settlement',
                assignmentStatus: 'assigned',
                roomNumber: 'N/A',
                basePrice: 5000000,
                discount: 0,
                voucher: 'FREE-VO-2025',
                total: 5000000,
                serviceType: 'virtual'
            },
            {
                id: 6,
                bookingId: '#MR-091',
                transactionDate: '08 Oct 2025, 10:45',
                bookingDate: '09 Oct 2025',
                bookingTime: '10:00 - 11:00',
                customerName: 'Ahmad Fauzi',
                customerPhone: '0817-9012-3456',
                customerEmail: 'ahmad@email.com',
                customerCompany: null,
                service: 'Meeting Room',
                package: '1 Jam',
                duration: '1 Hour',
                participants: '6 people',
                paymentStatus: 'expired',
                assignmentStatus: 'unassigned',
                roomNumber: null,
                basePrice: 100000,
                discount: 0,
                voucher: null,
                total: 100000,
                serviceType: 'meeting'
            },
            {
                id: 7,
                bookingId: '#SR-023',
                transactionDate: '08 Oct 2025, 09:30',
                bookingDate: '09 Oct 2025',
                bookingTime: 'Full Month',
                customerName: 'Linda Permata',
                customerPhone: '0818-0123-4567',
                customerEmail: 'linda@email.com',
                customerCompany: 'PT Digital',
                service: 'Sharing Room',
                package: 'Monthly',
                duration: '1 Month',
                participants: '6 people',
                paymentStatus: 'settlement',
                assignmentStatus: 'assigned',
                roomNumber: 'Room 306',
                basePrice: 2500000,
                discount: 0,
                voucher: null,
                total: 2500000,
                serviceType: 'sharing'
            },
            {
                id: 8,
                bookingId: '#ES-005',
                transactionDate: '08 Oct 2025, 08:00',
                bookingDate: '12 Oct 2025',
                bookingTime: '08:00 - 18:00',
                customerName: 'Ridwan Kamil',
                customerPhone: '0819-1234-5678',
                customerEmail: 'ridwan@email.com',
                customerCompany: 'Event Organizer XYZ',
                service: 'Event Space',
                package: 'Full Day',
                duration: '10 Hours',
                participants: '100 people',
                paymentStatus: 'settlement',
                assignmentStatus: 'unassigned',
                roomNumber: null,
                basePrice: 5000000,
                discount: 500000,
                voucher: null,
                total: 4500000,
                serviceType: 'event'
            },
            {
                id: 9,
                bookingId: '#MR-092',
                transactionDate: '07 Oct 2025, 16:20',
                bookingDate: '11 Oct 2025',
                bookingTime: '13:00 - 15:00',
                customerName: 'Rina Susanti',
                customerPhone: '0821-2345-6789',
                customerEmail: 'rina@email.com',
                customerCompany: 'CV Sejahtera',
                service: 'Meeting Room',
                package: '2 Jam',
                duration: '2 Hours',
                participants: '10 people',
                paymentStatus: 'settlement',
                assignmentStatus: 'unassigned',
                roomNumber: null,
                basePrice: 200000,
                discount: 20000,
                voucher: null,
                total: 180000,
                serviceType: 'meeting'
            },
            {
                id: 10,
                bookingId: '#PO-046',
                transactionDate: '07 Oct 2025, 15:10',
                bookingDate: '15 Oct 2025',
                bookingTime: 'Full Month',
                customerName: 'Hendra Wijaya',
                customerPhone: '0822-3456-7890',
                customerEmail: 'hendra@email.com',
                customerCompany: 'PT Teknologi',
                service: 'Private Office',
                package: 'Monthly',
                duration: '1 Month',
                participants: '6 people',
                paymentStatus: 'pending',
                assignmentStatus: 'unassigned',
                roomNumber: null,
                basePrice: 3500000,
                discount: 0,
                voucher: null,
                total: 3500000,
                serviceType: 'private'
            }
        ],

        // Initialize
        init() {
            // Any initialization logic
        },

        // Computed Properties
        get filteredBookings() {
            let filtered = this.bookings;

            // Filter by payment status
            if (this.filters.status) {
                filtered = filtered.filter(b => b.paymentStatus === this.filters.status);
            }

            // Filter by service type
            if (this.filters.service) {
                filtered = filtered.filter(b => b.serviceType === this.filters.service);
            }

            // Filter by assignment status
            if (this.filters.assignment) {
                filtered = filtered.filter(b => b.assignmentStatus === this.filters.assignment);
            }

            // Filter by specific date
            if (this.filters.date) {
                filtered = filtered.filter(b => {
                    const bookingDate = new Date(b.bookingDate).toISOString().split('T')[0];
                    return bookingDate === this.filters.date;
                });
            }

            // Filter by date range
            if (this.filters.dateFrom && this.filters.dateTo) {
                filtered = filtered.filter(b => {
                    const bookingDate = new Date(b.bookingDate);
                    const fromDate = new Date(this.filters.dateFrom);
                    const toDate = new Date(this.filters.dateTo);
                    return bookingDate >= fromDate && bookingDate <= toDate;
                });
            }

            // Filter by search
            if (this.filters.search) {
                const search = this.filters.search.toLowerCase();
                filtered = filtered.filter(b => 
                    b.bookingId.toLowerCase().includes(search) ||
                    b.customerName.toLowerCase().includes(search) ||
                    b.customerPhone.includes(search) ||
                    (b.customerEmail && b.customerEmail.toLowerCase().includes(search))
                );
            }

            return filtered;
        },

        get paginatedBookings() {
            const start = (this.currentPage - 1) * this.entriesPerPage;
            const end = start + parseInt(this.entriesPerPage);
            return this.filteredBookings.slice(start, end);
        },

        get totalPages() {
            return Math.ceil(this.filteredBookings.length / this.entriesPerPage);
        },

        get paginationStart() {
            return (this.currentPage - 1) * this.entriesPerPage + 1;
        },

        get paginationEnd() {
            const end = this.currentPage * this.entriesPerPage;
            return end > this.filteredBookings.length ? this.filteredBookings.length : end;
        },

        // Methods
        formatCurrency(value) {
            return 'Rp ' + value.toLocaleString('id-ID');
        },

        resetFilter() {
            this.filters = {
                status: '',
                service: '',
                assignment: '',
                date: '',
                dateFrom: '',
                dateTo: '',
                search: ''
            };
            this.currentPage = 1;
        },

        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },

        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },

        viewDetail(booking) {
            this.selectedBooking = booking;
            this.showDetailModal = true;
        },

        assignNow(booking) {
            // Redirect to room assignment page
            alert('Redirecting to room assignment for ' + booking.bookingId);
            // window.location.href = '/admin/booking/room-assignment?booking=' + booking.id;
        },

        exportExcel() {
            alert('Exporting ' + this.filteredBookings.length + ' bookings to Excel...');
            // Implementation for Excel export
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