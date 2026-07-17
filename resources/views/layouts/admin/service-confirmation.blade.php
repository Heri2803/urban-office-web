@extends('layouts.admin')

@section('title', 'Service Confirmation')

@section('content')
<div x-data="serviceConfirmation()" class="container mx-auto px-4 py-6 max-w-7xl">
    
    <!-- Header Section dengan Stats -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Service Bookings 📑</h1>
                <p class="text-gray-600">Monitor real-time booking status from database.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <!-- Quick Stats -->
                <div class="bg-white rounded-lg border border-gray-200 px-4 py-3 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <div>
                            <p class="text-sm text-gray-600">Pending</p>
                            <p class="text-lg font-bold text-gray-800" x-text="totalStats.pending"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 px-4 py-3 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <div>
                            <p class="text-sm text-gray-600">Confirmed</p>
                            <p class="text-lg font-bold text-gray-800" x-text="totalStats.settlement"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 px-4 py-3 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                    <div>
                        <p class="text-sm text-gray-600">Expired</p>
                        <p class="text-lg font-bold text-gray-800" x-text="totalStats.expired"></p>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

    <!-- Filter Section yang Lebih Clean -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <h2 class="text-xl font-semibold text-gray-700">Filter Bookings</h2>
            <div class="flex gap-3">
                <button @click="applyFilters" 
                        class="px-5 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Apply Filter
                </button>
                <button @click="resetFilters" 
                        class="px-5 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Reset
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Date Range Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                <div class="flex gap-2">
                    <input type="date" x-model="filterStartDate" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <input type="date" x-model="filterEndDate" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
            </div>

            <!-- Service Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service Type</label>
                <select x-model="filterServiceType" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">All Services</option>
                    <option value="Coworking Space">Coworking Space</option>
                    <option value="Event Space">Event Space</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select x-model="filterStatus" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">All Status</option>
                    <option value="settlement">Confirmed</option>
                    <option value="pending">Waiting Payment</option>
                    <option value="expired">Expired</option>
                </select>
            </div>

            <!-- Time Frame Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Time Frame</label>
                <select x-model="filterTimeFrame" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">All Time</option>
                    <option value="today">Today</option>
                    <option value="tomorrow">Tomorrow</option>
                    <option value="this_week">This Week</option>
                    <option value="next_week">Next Week</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Service Cards Grid - Hanya Coworking & Event Space -->
    <div class="space-y-8">
    
        <!-- Coworking Space Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
            <div class="p-6 border-b border-blue-200 bg-gradient-to-r from-blue-50 to-blue-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857m0 0c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Coworking Space
                        </h2>
                        <p class="text-sm text-blue-700 mt-1" x-text="`Confirmed: ${coworkingStats.settlement} | Waiting: ${coworkingStats.pending} | Expired: ${coworkingStats.expired} | Capacity: ${coworkingStats.capacity}`"></p>
                    </div>
                    <div class="bg-white rounded-lg px-3 py-2 border border-blue-200">
                        <p class="text-xs text-blue-600 font-medium">Real-time Updates</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Booking Lists dengan GRID LAYOUT dan PAGINATION -->
                <div class="space-y-8">
                    <!-- Unified Coworking Bookings Section -->
                    <div class="space-y-4" id="coworking-section">
                        <!-- Header dengan Pagination Info -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <h3 class="text-md font-semibold text-gray-700 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    All Bookings
                                </h3>
                                <span class="text-xs font-bold px-3 py-1 bg-blue-100 text-blue-800 rounded-full"
                                    x-text="filteredCoworkingBookings.length"></span>
                            </div>
                            
                            <!-- Pagination Info -->
                            <div class="text-xs text-gray-500" 
                                x-show="pagination.coworking.totalPages > 1">
                                Page <span class="font-semibold" x-text="pagination.coworking.currentPage"></span>
                                of <span class="font-semibold" x-text="pagination.coworking.totalPages"></span>
                            </div>
                        </div>
                        
                        {{-- Empty State --}}
                        <div x-show="filteredCoworkingBookings.length === 0" class="text-center py-12 bg-white rounded-lg border-2 border-dashed border-gray-300">
                            <div class="text-5xl mb-4">📑</div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Belum Ada Transaksi</h3>
                            <p class="text-gray-500">
                                Belum ada transaksi untuk Coworking Space di cabang ini.
                            </p>
                        </div>

                        <!-- Grid Cards untuk semua status -->
                        <div x-show="filteredCoworkingBookings.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <template x-for="booking in paginatedCoworking" :key="booking.id">
                                <!-- Booking Card - DINAMIS BERDASARKAN STATUS -->
                                <div class="bg-gradient-to-br rounded-xl p-5 hover:shadow-lg transition-all duration-300 h-full"
                                    :class="getStatusCardClass(booking.status)">
                                    
                                    <!-- Header -->
                                    <div class="flex justify-between items-start mb-4 pb-3 border-b"
                                        :class="getStatusBadgeClass(booking.status).replace('bg-', 'border-') + '/50'">
                                        <div class="space-y-1">
                                            <p class="text-sm font-bold text-gray-900 truncate" 
                                            x-text="booking.booking_code"></p>
                                            <p class="text-xs text-gray-600 truncate" 
                                            x-text="booking.customer_name"></p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full shrink-0 flex items-center gap-1"
                                            :class="getStatusBadgeClass(booking.status)">
                                            <span x-html="getStatusIcon(booking.status)"></span>
                                            <span x-text="getStatusDisplay(booking.status).text"></span>
                                        </span>
                                    </div>
                                    
                                    <!-- Compact Details -->
                                    <div class="space-y-3 mb-4">
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Date & Time</p>
                                            <div class="space-y-1">
                                                <p class="text-sm font-medium text-gray-900" 
                                                x-text="formatDate(booking.booking_date)"></p>
                                                <p class="text-xs text-gray-700" 
                                                x-text="booking.start_time + ' - ' + (booking.end_time || '...')"></p>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <p class="text-xs text-gray-500">Duration</p>
                                                <p class="text-xs font-medium text-gray-900" 
                                                x-text="formatDuration(booking.duration_type, booking.duration_value)"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Amount</p>
                                                <p class="text-xs font-bold text-blue-600" 
                                                x-text="`Rp ${formatPrice(booking.amount)}`"></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Status Info -->
                                    <div class="mt-4 p-3 rounded-lg border text-center"
                                        :class="booking.status === 'settlement' ? 'bg-green-50 border-green-200' :
                                                booking.status === 'pending' ? 'bg-yellow-50 border-yellow-200' :
                                                'bg-red-50 border-red-200'">
                                        <p class="text-xs font-medium mb-1"
                                        :class="booking.status === 'settlement' ? 'text-green-700' :
                                                booking.status === 'pending' ? 'text-yellow-700' :
                                                'text-red-700'">
                                            <span x-show="booking.status === 'pending'">Expires in:</span>
                                            <span x-show="booking.status === 'settlement'">Booking starts in:</span>
                                            <span x-show="booking.status === 'expired'">Expired since:</span>
                                        </p>
                                        <span class="text-sm font-bold block"
                                            :class="getBookingStatusClass(booking.booking_date, booking.start_time)"
                                            x-text="getRemainingTime(booking.booking_date, booking.start_time, booking.status)">
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Pagination Controls -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200"
                            x-show="pagination.coworking.totalPages > 1">
                            
                            <!-- Previous Button -->
                            <button @click="prevPage('coworking')"
                                    :disabled="pagination.coworking.currentPage === 1"
                                    :class="pagination.coworking.currentPage === 1 ? 
                                            'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Previous
                            </button>
                            
                            <!-- Page Numbers -->
                            <div class="hidden sm:flex items-center gap-1" 
                                x-show="pagination.coworking.totalPages <= 5">
                                <template x-for="page in pagination.coworking.totalPages" :key="page">
                                    <button @click="goToPage('coworking', page)"
                                            :class="page === pagination.coworking.currentPage ? 
                                                    'bg-blue-600 text-white' : 
                                                    'bg-white text-gray-700 hover:bg-gray-100'"
                                            class="w-8 h-8 flex items-center justify-center text-sm font-medium border border-gray-300 rounded-lg">
                                        <span x-text="page"></span>
                                    </button>
                                </template>
                            </div>
                            
                            <!-- Page Info (for many pages) -->
                            <div class="text-sm text-gray-600" 
                                x-show="pagination.coworking.totalPages > 5">
                                <span x-text="pagination.coworking.currentPage"></span>
                                of
                                <span x-text="pagination.coworking.totalPages"></span>
                                pages
                            </div>
                            
                            <!-- Next Button -->
                            <button @click="nextPage('coworking')"
                                    :disabled="pagination.coworking.currentPage === pagination.coworking.totalPages"
                                    :class="pagination.coworking.currentPage === pagination.coworking.totalPages ? 
                                            'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg flex items-center gap-2">
                                Next
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Space Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
            <div class="p-6 border-b border-orange-200 bg-gradient-to-r from-orange-50 to-orange-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Event Space
                        </h2>
                        <p class="text-sm text-orange-700 mt-1" x-text="`Confirmed: ${eventSpaceStats.settlement} | Waiting: ${eventSpaceStats.pending} | Expired: ${eventSpaceStats.expired}`"></p>
                    </div>
                    <div class="bg-white rounded-lg px-3 py-2 border border-orange-200">
                        <p class="text-xs text-orange-600 font-medium">Cleanup Schedule Included</p>
                    </div>
                </div>
                
                <!-- Cleanup Notice -->
                <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-orange-800">Cleanup Schedule</p>
                            <p class="text-xs text-orange-700 mt-0.5">1 hour block time is automatically added after each event for cleanup</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Booking Lists DUA KOLOM dengan SCROLL HORIZONTAL -->
                <div class="space-y-8">
                    <!-- Unified Event Space Bookings Section -->
                    <div class="space-y-4" id="eventSpace-section">
                        <!-- Header dengan Pagination Info -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <h3 class="text-md font-semibold text-gray-700 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    All Bookings
                                </h3>
                                <span class="text-xs font-bold px-3 py-1 bg-orange-100 text-orange-800 rounded-full"
                                    x-text="filteredEventSpaceBookings.length"></span>
                            </div>
                            
                            <!-- Pagination Info -->
                            <div class="text-xs text-gray-500" 
                                x-show="pagination.eventSpace.totalPages > 1">
                                Page <span class="font-semibold" x-text="pagination.eventSpace.currentPage"></span>
                                of <span class="font-semibold" x-text="pagination.eventSpace.totalPages"></span>
                            </div>
                        </div>
                        
                        {{-- Empty State --}}
                        <div x-show="filteredEventSpaceBookings.length === 0" class="text-center py-12 bg-white rounded-lg border-2 border-dashed border-gray-300">
                            <div class="text-5xl mb-4">📑</div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Belum Ada Transaksi</h3>
                            <p class="text-gray-500">
                                Belum ada transaksi untuk Event Space di cabang ini.
                            </p>
                        </div>

                        <!-- Grid Cards untuk semua status -->
                        <div x-show="filteredEventSpaceBookings.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <template x-for="booking in paginatedEventSpace" :key="booking.id">
                                <!-- Booking Card - DINAMIS BERDASARKAN STATUS -->
                                <div class="bg-gradient-to-br rounded-xl p-5 hover:shadow-lg transition-all duration-300 h-full"
                                    :class="getStatusCardClass(booking.status)">
                                    
                                    <!-- Header -->
                                    <div class="flex justify-between items-start mb-4 pb-3 border-b"
                                        :class="getStatusBadgeClass(booking.status).replace('bg-', 'border-') + '/50'">
                                        <div class="space-y-1">
                                            <p class="text-sm font-bold text-gray-900 truncate" 
                                            x-text="booking.booking_code"></p>
                                            <p class="text-xs text-gray-600 truncate" 
                                            x-text="booking.customer_name"></p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full shrink-0 flex items-center gap-1"
                                            :class="getStatusBadgeClass(booking.status)">
                                            <span x-html="getStatusIcon(booking.status)"></span>
                                            <span x-text="getStatusDisplay(booking.status).text"></span>
                                        </span>
                                    </div>
                                    
                                    <!-- Compact Details -->
                                    <div class="space-y-3 mb-4">
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Event Date</p>
                                            <p class="text-sm font-medium text-gray-900" 
                                            x-text="formatDate(booking.booking_date)"></p>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <p class="text-xs text-gray-500">Event Time</p>
                                                <p class="text-xs font-medium text-gray-900" 
                                                x-text="booking.start_time + ' - ' + (booking.end_time || '...')"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Cleanup</p>
                                                <p class="text-xs font-medium text-orange-700" 
                                                x-text="`${booking.end_time} - ${addOneHour(booking.end_time)}`"></p>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <p class="text-xs text-gray-500">Amount</p>
                                            <p class="text-xs font-bold text-orange-600" 
                                            x-text="`Rp ${formatPrice(booking.amount)}`"></p>
                                        </div>
                                    </div>
                                    
                                    <!-- Status Info -->
                                    <div class="mt-4 p-3 rounded-lg border text-center"
                                        :class="booking.status === 'settlement' ? 'bg-green-50 border-green-200' :
                                                booking.status === 'pending' ? 'bg-yellow-50 border-yellow-200' :
                                                'bg-red-50 border-red-200'">
                                        <p class="text-xs font-medium mb-1"
                                        :class="booking.status === 'settlement' ? 'text-green-700' :
                                                booking.status === 'pending' ? 'text-yellow-700' :
                                                'text-red-700'">
                                            <span x-show="booking.status === 'pending'">Expires in:</span>
                                            <span x-show="booking.status === 'settlement'">Booking starts in:</span>
                                            <span x-show="booking.status === 'expired'">Expired since:</span>
                                        </p>
                                        <span class="text-sm font-bold block"
                                            :class="getBookingStatusClass(booking.booking_date, booking.start_time)"
                                            x-text="getRemainingTime(booking.booking_date, booking.start_time, booking.status)">
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Pagination Controls -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200"
                            x-show="pagination.eventSpace.totalPages > 1">
                            
                            <!-- Previous Button -->
                            <button @click="prevPage('eventSpace')"
                                    :disabled="pagination.eventSpace.currentPage === 1"
                                    :class="pagination.eventSpace.currentPage === 1 ? 
                                            'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Previous
                            </button>
                            
                            <!-- Page Numbers -->
                            <div class="hidden sm:flex items-center gap-1" 
                                x-show="pagination.eventSpace.totalPages <= 5">
                                <template x-for="page in pagination.eventSpace.totalPages" :key="page">
                                    <button @click="goToPage('eventSpace', page)"
                                            :class="page === pagination.eventSpace.currentPage ? 
                                                    'bg-blue-600 text-white' : 
                                                    'bg-white text-gray-700 hover:bg-gray-100'"
                                            class="w-8 h-8 flex items-center justify-center text-sm font-medium border border-gray-300 rounded-lg">
                                        <span x-text="page"></span>
                                    </button>
                                </template>
                            </div>
                            
                            <!-- Page Info (for many pages) -->
                            <div class="text-sm text-gray-600" 
                                x-show="pagination.eventSpace.totalPages > 5">
                                <span x-text="pagination.eventSpace.currentPage"></span>
                                of
                                <span x-text="pagination.eventSpace.totalPages"></span>
                                pages
                            </div>
                            
                            <!-- Next Button -->
                            <button @click="nextPage('eventSpace')"
                                    :disabled="pagination.eventSpace.currentPage === pagination.eventSpace.totalPages"
                                    :class="pagination.eventSpace.currentPage === pagination.eventSpace.totalPages ? 
                                            'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg flex items-center gap-2">
                                Next
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function serviceConfirmation() {
    return {
        // Filter states
        filterStartDate: '',
        filterEndDate: '',
        filterServiceType: '',
        filterStatus: '',
        filterTimeFrame: '',
        // Modal states
        showSuccessNotification: false,
        showErrorNotification: false,
        successMessage: '',
        errorMessage: '',
        statusFilter: '',

        pagination: {
            coworking: { currentPage: 1, perPage: 6, totalPages: 1 },
            eventSpace: { currentPage: 1, perPage: 6, totalPages: 1 }
        },

        filterParams: {
            startDate: '',
            endDate: '',
            serviceType: '',
            status: '',
            timeFrame: ''
        },

        
        // Data dari backend
        coworkingBookings: @json($coworkingBookings ?? []),
        eventSpaceBookings: @json($eventSpaceBookings ?? []),

        // ✅ NEW: Real-time properties
        autoRefreshInterval: null,
        isLoading: false,
        lastRefresh: null,

        // filter functions
        applyFilters() {
            console.log('🎯 Applying filters:', {
                startDate: this.filterStartDate,
                endDate: this.filterEndDate,
                serviceType: this.filterServiceType,
                status: this.filterStatus,
                timeFrame: this.filterTimeFrame
            });
            
            // Reset pagination ke page 1
            this.resetAllPagination();
            
            // Untuk client-side filtering, tidak perlu API call
            console.log('✅ Filters applied (client-side)');
        },
        
        resetFilters() {
            this.filterStartDate = '';
            this.filterEndDate = '';
            this.filterServiceType = '';
            this.filterStatus = '';
            this.filterTimeFrame = '';
            
            console.log('🔄 All filters reset');
            
            // Reset pagination
            this.resetAllPagination();
        },

        // TAMBAHKAN: Filter berdasarkan statusFilter
        get filteredCoworkingBookings() {
            let filtered = this.coworkingBookings;
            
            console.log('🔍 Filtering coworking with serviceType:', this.filterServiceType);
            
            // 1. Filter by service type
            if (this.filterServiceType) {
                if (this.filterServiceType === 'Coworking Space') {
                    // TAMPILKAN hanya Coworking
                    const allowedTypes = ['coworking_space', 'Coworking Space'];
                    filtered = filtered.filter(b => allowedTypes.includes(b.room_type));
                    console.log('✅ Showing ONLY Coworking Space');
                } else if (this.filterServiceType === 'Event Space') {
                    // SEMBUNYIKAN semua Coworking (tampilkan 0)
                    filtered = [];
                    console.log('🚫 Hiding all Coworking (showing Event Space)');
                }
            }
            
            // 2. Filter lainnya (status, date, dll)
            if (this.filterStatus) {
                filtered = filtered.filter(b => b.status === this.filterStatus);
            }
            
            if (this.filterStartDate) {
                const startDate = new Date(this.filterStartDate);
                filtered = filtered.filter(b => {
                    const bookingDate = new Date(b.booking_date);
                    return bookingDate >= startDate;
                });
            }
            
            if (this.filterEndDate) {
                const endDate = new Date(this.filterEndDate);
                filtered = filtered.filter(b => {
                    const bookingDate = new Date(b.booking_date);
                    return bookingDate <= endDate;
                });
            }
            
            if (this.filterTimeFrame) {
                filtered = this.applyTimeFrameFilter(filtered, this.filterTimeFrame);
            }
            
            console.log(`📊 Filtered coworking: ${filtered.length} bookings`);
            return filtered;
        },


        get filteredEventSpaceBookings() {
            let filtered = this.eventSpaceBookings;
            
            console.log('🔍 Filtering event space with serviceType:', this.filterServiceType);
            
            // 1. Filter by service type
            if (this.filterServiceType) {
                if (this.filterServiceType === 'Event Space') {
                    // TAMPILKAN hanya Event Space
                    const allowedTypes = ['event_space', 'Event Space'];
                    filtered = filtered.filter(b => allowedTypes.includes(b.room_type));
                    console.log('✅ Showing ONLY Event Space');
                } else if (this.filterServiceType === 'Coworking Space') {
                    // SEMBUNYIKAN semua Event Space (tampilkan 0)
                    filtered = [];
                    console.log('🚫 Hiding all Event Space (showing Coworking Space)');
                }
            }
            
            // 2. Filter lainnya (status, date, dll)
            if (this.filterStatus) {
                filtered = filtered.filter(b => b.status === this.filterStatus);
            }
            
            if (this.filterStartDate) {
                const startDate = new Date(this.filterStartDate);
                filtered = filtered.filter(b => {
                    const bookingDate = new Date(b.booking_date);
                    return bookingDate >= startDate;
                });
            }
            
            if (this.filterEndDate) {
                const endDate = new Date(this.filterEndDate);
                filtered = filtered.filter(b => {
                    const bookingDate = new Date(b.booking_date);
                    return bookingDate <= endDate;
                });
            }
            
            if (this.filterTimeFrame) {
                filtered = this.applyTimeFrameFilter(filtered, this.filterTimeFrame);
            }
            
            console.log(`📊 Filtered event space: ${filtered.length} bookings`);
            return filtered;
        },


        // TAMBAHKAN: Real-time stats
        get coworkingStats() {
            const settlement = this.coworkingBookings.filter(b => b.status === 'settlement').length;
            const pending = this.coworkingBookings.filter(b => b.status === 'pending').length;
            const expired = this.coworkingBookings.filter(b => b.status === 'expired').length;
            
            return {
                settlement: settlement,
                pending: pending,
                expired: expired,
                total: this.coworkingBookings.length,
                capacity: this.calculateCapacity(this.coworkingBookings)
            };
        },

        get eventSpaceStats() {
            const settlement = this.eventSpaceBookings.filter(b => b.status === 'settlement').length;
            const pending = this.eventSpaceBookings.filter(b => b.status === 'pending').length;
            const expired = this.eventSpaceBookings.filter(b => b.status === 'expired').length;
            
            return {
                settlement: settlement,
                pending: pending,
                expired: expired,
                total: this.eventSpaceBookings.length
            };
        },

        // TAMBAHKAN: Total stats untuk header
        get totalStats() {
            return {
                settlement: this.coworkingStats.settlement + this.eventSpaceStats.settlement,
                pending: this.coworkingStats.pending + this.eventSpaceStats.pending,
                expired: this.coworkingStats.expired + this.eventSpaceStats.expired
            };
        },

        // TAMBAHKAN: Paginated data baru
        get paginatedCoworking() {
            const start = (this.pagination.coworking.currentPage - 1) * 
                        this.pagination.coworking.perPage;
            const end = start + this.pagination.coworking.perPage;
            return this.filteredCoworkingBookings.slice(start, end);
        },

        get paginatedEventSpace() {
            const start = (this.pagination.eventSpace.currentPage - 1) * 
                        this.pagination.eventSpace.perPage;
            const end = start + this.pagination.eventSpace.perPage;
            return this.filteredEventSpaceBookings.slice(start, end);
        },

        // TAMBAHKAN: Fungsi untuk display status
        getStatusDisplay(status) {
            const statusMap = {
                'settlement': { 
                    text: 'Confirmed', 
                    color: 'green',
                    bgColor: 'green',
                    icon: 'check-circle'
                },
                'pending': { 
                    text: 'Waiting Payment', 
                    color: 'yellow',
                    bgColor: 'yellow',
                    icon: 'clock'
                },
                'expired': { 
                    text: 'Expired', 
                    color: 'red',
                    bgColor: 'red',
                    icon: 'x-circle'
                }
            };
            
            return statusMap[status] || { 
                text: 'Unknown', 
                color: 'gray',
                bgColor: 'gray',
                icon: 'question-mark'
            };
        },

        getStatusBadgeClass(status) {
            const map = {
                'settlement': 'bg-green-100 text-green-800 border-green-200',
                'pending': 'bg-yellow-100 text-yellow-800 border-yellow-200',
                'expired': 'bg-red-100 text-red-800 border-red-200'
            };
            return map[status] || 'bg-gray-100 text-gray-800 border-gray-200';
        },

        getStatusCardClass(status) {
            const map = {
                'settlement': 'from-green-50 to-white border-green-200 hover:border-green-300',
                'pending': 'from-yellow-50 to-white border-yellow-200 hover:border-yellow-300',
                'expired': 'from-red-50 to-white border-red-200 hover:border-red-300'
            };
            return map[status] || 'from-gray-50 to-white border-gray-200';
        },

        getStatusIcon(status) {
            const icons = {
                'settlement': `<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>`,
                'pending': `<svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>`,
                'expired': `<svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>`
            };
            return icons[status] || '';
        },

        // Computed properties

        calculateCapacity(bookings) {
            const maxCapacity = 20;
            const settlementCount = bookings.filter(b => b.status === 'settlement').length;
            const percentage = (settlementCount / maxCapacity) * 100;
            return `${settlementCount}/${maxCapacity} (${Math.round(percentage)}%)`;
        },
        
        // ✅ NEW: Last refresh time
        get lastRefreshTime() {
            if (!this.lastRefresh) return 'Never';
            return new Date(this.lastRefresh).toLocaleTimeString('id-ID');
        },

        // ✅ NEW: Initialize real-time functionality
        init() {
            console.log('Service Confirmation initialized with real-time updates');
            this.lastRefresh = new Date();
            
            // Start auto-refresh
            this.startAutoRefresh();
            
            // Setup watchers untuk cleanup
            this.setupWatchers();
            
            // Initial data validation
            this.validateInitialData();

            // ✅ NEW: Setup pagination watchers
            this.setupPaginationWatchers();
            
            // ✅ NEW: Update initial pagination stats
            this.updateAllPaginationStats();
        },

        setupPaginationWatchers() {
            // Update pagination ketika data berubah
            this.$watch('filteredCoworkingBookings', () => {
                this.updatePaginationStats('coworking');
            });
            
            this.$watch('filteredEventSpaceBookings', () => {
                this.updatePaginationStats('eventSpace');
            });
        },

        // UPDATE: Di updateAllPaginationStats()
        updateAllPaginationStats() {
            this.updatePaginationStats('coworking');
            this.updatePaginationStats('eventSpace');
        },
        
        // ✅ NEW: Update pagination statistics untuk satu section
        updatePaginationStats(section) {
            const filteredData = this.getFilteredDataBySection(section);
            const pagination = this.pagination[section];
            
            if (filteredData && pagination) {
                // Calculate total pages
                pagination.totalPages = Math.max(1, Math.ceil(filteredData.length / pagination.perPage));
                
                // Reset to page 1 jika current page melebihi total pages
                if (pagination.currentPage > pagination.totalPages && pagination.totalPages > 0) {
                    pagination.currentPage = 1;
                }
                
                console.log(`Updated pagination for ${section}:`, {
                    currentPage: pagination.currentPage,
                    totalPages: pagination.totalPages,
                    totalItems: filteredData.length,
                    perPage: pagination.perPage
                });
            }
        },
        
        // ✅ NEW: Helper untuk mendapatkan filtered data berdasarkan section
        getFilteredDataBySection(section) {
            const dataMap = {
                'coworking': () => this.filteredCoworkingBookings,
                'eventSpace': () => this.filteredEventSpaceBookings
            };
            
            return dataMap[section] ? dataMap[section]() : [];
        },

        // Helper function untuk time frame filtering
        applyTimeFrameFilter(bookings, timeFrame) {
            const today = new Date();
            const startOfWeek = new Date(today);
            startOfWeek.setDate(today.getDate() - today.getDay());
            
            switch(timeFrame) {
                case 'today':
                    return bookings.filter(b => {
                        const bookingDate = new Date(b.booking_date);
                        return bookingDate.toDateString() === today.toDateString();
                    });
                    
                case 'tomorrow':
                    const tomorrow = new Date(today);
                    tomorrow.setDate(today.getDate() + 1);
                    return bookings.filter(b => {
                        const bookingDate = new Date(b.booking_date);
                        return bookingDate.toDateString() === tomorrow.toDateString();
                    });
                    
                case 'this_week':
                    const endOfWeek = new Date(startOfWeek);
                    endOfWeek.setDate(startOfWeek.getDate() + 6);
                    return bookings.filter(b => {
                        const bookingDate = new Date(b.booking_date);
                        return bookingDate >= startOfWeek && bookingDate <= endOfWeek;
                    });
                    
                case 'next_week':
                    const nextWeekStart = new Date(startOfWeek);
                    nextWeekStart.setDate(startOfWeek.getDate() + 7);
                    const nextWeekEnd = new Date(nextWeekStart);
                    nextWeekEnd.setDate(nextWeekStart.getDate() + 6);
                    return bookings.filter(b => {
                        const bookingDate = new Date(b.booking_date);
                        return bookingDate >= nextWeekStart && bookingDate <= nextWeekEnd;
                    });
                    
                default:
                    return bookings;
            }
        },
        
        // ✅ NEW: Pagination navigation methods
        nextPage(section) {
            if (this.pagination[section].currentPage < this.pagination[section].totalPages) {
                this.pagination[section].currentPage++;
                this.scrollToSectionTop(section);
            }
        },
        
        prevPage(section) {
            if (this.pagination[section].currentPage > 1) {
                this.pagination[section].currentPage--;
                this.scrollToSectionTop(section);
            }
        },
        
        goToPage(section, page) {
            if (page >= 1 && page <= this.pagination[section].totalPages) {
                this.pagination[section].currentPage = page;
                this.scrollToSectionTop(section);
            }
        },
        
        // UPDATE: Di scrollToSectionTop()
        scrollToSectionTop(section) {
            setTimeout(() => {
                const element = document.getElementById(`${section}-section`);
                if (element) {
                    element.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                }
            }, 50);
        },
        
        // ✅ NEW: Reset pagination ke page 1 (saat filter diterapkan)
        resetAllPagination() {
            Object.keys(this.pagination).forEach(section => {
                this.pagination[section].currentPage = 1;
            });
            console.log('All pagination reset to page 1');
        },
        
        // ✅ NEW: Responsive perPage adjustment (optional)
        adjustPerPageBasedOnScreen() {
            const width = window.innerWidth;
            let perPage = 6; // Default
            
            if (width < 640) { // Mobile
                perPage = 2; // 1 kolom × 2 rows
            } else if (width < 1024) { // Tablet
                perPage = 4; // 2 kolom × 2 rows
            } else { // Desktop
                perPage = 6; // 3 kolom × 2 rows
            }
            
            // Update semua sections
            Object.keys(this.pagination).forEach(section => {
                this.pagination[section].perPage = perPage;
            });
            
            this.updateAllPaginationStats();
        },
        
        // ✅ NEW: Cleanup pagination (saat component di-destroy)
        destroy() {
            // Clear auto-refresh interval
            if (this.autoRefreshInterval) {
                clearInterval(this.autoRefreshInterval);
                console.log('Auto-refresh stopped');
            }
            
            // Remove resize listener jika ada
            if (this.resizeListener) {
                window.removeEventListener('resize', this.resizeListener);
            }
        },

        // ✅ NEW: Start auto-refresh interval
        startAutoRefresh() {
            // Clear existing interval
            if (this.autoRefreshInterval) {
                clearInterval(this.autoRefreshInterval);
            }
            
            // Refresh setiap 30 detik
            this.autoRefreshInterval = setInterval(() => {
                this.refreshData();
            }, 30000);
            
            console.log('Auto-refresh started (30 seconds interval)');
        },

        // ✅ NEW: Validate initial data
        validateInitialData() {
            const totalBookings = this.coworkingBookings.length + this.eventSpaceBookings.length;
            console.log(`Initial data loaded: ${totalBookings} bookings total`);
            console.log(`- Coworking: ${this.coworkingBookings.length} bookings`);
            console.log(`- Event Space: ${this.eventSpaceBookings.length} bookings`);
        },

         validateBookingsStructure(bookings, type) {
            if (!Array.isArray(bookings)) {
                console.error(`${type} bookings is not an array:`, typeof bookings);
                return;
            }

            console.log(`Validating ${type} bookings:`, bookings);
            
            const requiredFields = ['id', 'booking_code', 'customer_name', 'room_type', 'status', 'booking_date'];
            
            bookings.forEach((booking, index) => {
                const missingFields = requiredFields.filter(field => 
                    !booking.hasOwnProperty(field) || booking[field] === null || booking[field] === undefined
                );
                
                if (missingFields.length > 0) {
                    console.warn(`${type} booking ${index} missing fields:`, missingFields, booking);
                } else {
                    console.log(`✓ ${type} booking ${index} valid:`, booking.booking_code, booking.customer_name);
                }
            });
        },

        // ✅ NEW: Refresh data dari server
        async refreshData() {
            if (this.isLoading) {
                console.log('Refresh skipped: already loading');
                return;
            }
            
            this.isLoading = true;
            console.log('🔄 Refreshing data from server...', this.filterParams);
            
            try {
                // Build query parameters dari filter
                const queryParams = new URLSearchParams();
                
                // Tambahkan parameter filter jika ada
                if (this.filterParams.startDate) queryParams.append('start_date', this.filterParams.startDate);
                if (this.filterParams.endDate) queryParams.append('end_date', this.filterParams.endDate);
                if (this.filterParams.serviceType) queryParams.append('service_type', this.filterParams.serviceType);
                if (this.filterParams.status) queryParams.append('status', this.filterParams.status);
                if (this.filterParams.timeFrame) queryParams.append('time_frame', this.filterParams.timeFrame);
                
                // Build URL dengan query parameters
                const url = `/booking/service-confirmation/api/data?${queryParams.toString()}`;
                console.log('📤 Request URL:', url);
                
                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                console.log('📊 Raw API response:', data);
                
                // Validate API response structure
                if (!data.success) {
                    throw new Error('API returned error: ' + (data.message || 'Unknown error'));
                }
                
                if (!data.hasOwnProperty('coworkingBookings') || !data.hasOwnProperty('eventSpaceBookings')) {
                    throw new Error('Invalid API response structure - missing bookings arrays');
                }
                
                // Update data
                this.updateBookingsData(data.coworkingBookings, data.eventSpaceBookings);
                
                this.lastRefresh = new Date();
                console.log('✅ Data refreshed successfully at', this.lastRefreshTime);
                console.log(`📈 Now have ${this.coworkingBookings.length} coworking and ${this.eventSpaceBookings.length} event space bookings`);
                
            } catch (error) {
                console.error('❌ Failed to refresh data:', error);
                this.showError('Failed to refresh data: ' + error.message);
            } finally {
                this.isLoading = false;
            }
        },

        // ✅ NEW: Smart update bookings data
        updateBookingsData(newCoworking, newEventSpace) {
            
            console.log('Updating bookings data:', {
                newCoworking: newCoworking.length,
                newEventSpace: newEventSpace.length
            });

            // Update dengan smart merge
            this.coworkingBookings = this.mergeBookingsData(this.coworkingBookings, newCoworking, 'coworking');
            this.eventSpaceBookings = this.mergeBookingsData(this.eventSpaceBookings, newEventSpace, 'event_space');
        },

        // ✅ NEW: Smart merge function dengan conflict resolution
        mergeBookingsData(current, incoming, type) {
            if (!incoming || !Array.isArray(incoming)) {
                console.warn(`Invalid incoming data for ${type}, using current data`);
                return current;
            }

            const merged = [...incoming];
            const locallyModified = [];
            
            // Cari booking yang ada di current tapi tidak di incoming (mungkin di-delete di server)
            current.forEach(currentBooking => {
                const incomingBooking = incoming.find(b => b.id === currentBooking.id);
                
                if (!incomingBooking) {
                    // Booking tidak ada di server data - mungkin deleted
                    console.log(`Booking ${currentBooking.id} not found in server data, might be deleted`);
                } else {
                    // Compare untuk detect conflicts
                    if (this.hasDataConflict(currentBooking, incomingBooking)) {
                        console.warn(`Data conflict detected for booking ${currentBooking.id}`, {
                            local: currentBooking.status,
                            server: incomingBooking.status
                        });
                    }
                }
            });

            console.log(`Merged ${type} data: ${merged.length} bookings`);
            return merged;
        },

        // ✅ NEW: Check data conflicts
        hasDataConflict(local, server) {
            // Cek jika status berbeda antara local dan server
            return local.status !== server.status;
        },

        setupWatchers() {
            // Log perubahan data untuk debugging
            this.$watch('coworkingBookings', (newVal, oldVal) => {
                console.log('Coworking bookings updated:', newVal.length, 'items');
                this.updateAllPaginationStats(); // Update pagination ketika data berubah
            });
            
            this.$watch('eventSpaceBookings', (newVal, oldVal) => {
                console.log('Event space bookings updated:', newVal.length, 'items');
                this.updateAllPaginationStats(); // Update pagination ketika data berubah
            });
            
            // Update pagination ketika filter berubah
            this.$watch('statusFilter', () => {
                this.resetAllPagination(); // Reset ke page 1 saat filter berubah
            });
        },

        // ✅ NEW: Manual refresh dengan user feedback
        async manualRefresh() {
            this.showSuccessNotification = true;
            this.successMessage = 'Refreshing data...';
            
            await this.refreshData();
            
            this.successMessage = `Data updated successfully at ${this.lastRefreshTime}`;
            setTimeout(() => {
                this.showSuccessNotification = false;
            }, 3000);
        },

        // ✅ NEW: Show error message
        showError(message) {
            this.showErrorNotification = true;
            this.errorMessage = message;
            
            setTimeout(() => {
                this.showErrorNotification = false;
            }, 5000);
        },

        // Utility functions
        getRemainingTime(bookingDate, startTime, status) {
            if (!bookingDate || !startTime) return 'Invalid time';
            
            if (status === 'expired') {
                return 'Payment Timeout';
            }
            
            // Untuk pending, hitung waktu sampai expired
            if (status === 'pending') {
                // Asumsi: pending expired dalam 24 jam dari waktu booking dibuat
                const bookingTime = new Date(bookingDate + 'T' + startTime);
                const expiryTime = new Date(bookingTime.getTime() + (24 * 60 * 60 * 1000)); // 24 jam
                const now = new Date();
                
                if (now > expiryTime) {
                    return 'Expired';
                }
                
                const diffMs = expiryTime - now;
                return this.formatTimeRemaining(diffMs, false);
            }
            
            // Untuk settlement, hitung waktu booking normal
            try {
                const now = new Date();
                const bookingDateTime = new Date(`${bookingDate}T${startTime}`);
                
                if (isNaN(bookingDateTime.getTime())) {
                    return 'Invalid date';
                }
                
                const diffMs = bookingDateTime - now;
                
                if (diffMs <= 0) {
                    // Sudah mulai
                    const endTime = this.calculateActualEndTime(bookingDate, startTime);
                    const endDateTime = new Date(`${bookingDate}T${endTime}`);
                    const remainingMs = endDateTime - now;
                    
                    if (remainingMs <= 0) {
                        return 'Completed';
                    }
                    
                    return this.formatTimeRemaining(remainingMs, true);
                }
                
                // Belum mulai
                return this.formatTimeRemaining(diffMs, false);
                
            } catch (error) {
                console.error('Error calculating remaining time:', error);
                return 'Error';
            }
        },

        calculateActualEndTime(bookingDate, startTime) {
            try {
                const startDateTime = new Date(`${bookingDate}T${startTime}`);
                
                // Cari booking data untuk mendapatkan durasi
                const allBookings = [...this.coworkingBookings, ...this.eventSpaceBookings];
                const booking = allBookings.find(b => 
                    b.booking_date === bookingDate && b.start_time === startTime
                );
                
                let durationHours = 4; // Default 4 jam
                
                if (booking) {
                    // Hitung berdasarkan duration_type dan duration_value
                    if (booking.duration_type === 'hour' && booking.duration_value) {
                        durationHours = booking.duration_value;
                    } else if (booking.duration_type === 'day' && booking.duration_value) {
                        durationHours = booking.duration_value * 8; // 8 jam per hari
                    } else if (booking.duration_type === 'daily') {
                        durationHours = 8;
                    } else if (booking.room_type === 'Event Space') {
                        durationHours = 4; // Default event 4 jam
                    } else if (booking.room_type === 'Coworking Space') {
                        durationHours = 8; // Default coworking 8 jam
                    }
                }
                
                const endDateTime = new Date(startDateTime.getTime() + (durationHours * 60 * 60 * 1000));
                return endDateTime.toTimeString().slice(0, 5);
                
            } catch (error) {
                console.error('Error calculating end time:', error);
                return '17:00';
            }
        },

        // ✅ NEW: Format time remaining dengan lebih baik
        formatTimeRemaining(ms, isOngoing) {
            if (ms <= 0) return isOngoing ? 'Completed' : 'Started';
            
            const days = Math.floor(ms / (1000 * 60 * 60 * 24));
            const hours = Math.floor((ms % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((ms % (1000 * 60 * 60)) / (1000 * 60));
            
            if (days > 0) {
                return `${days}d ${hours}h`;
            } else if (hours > 0) {
                return `${hours}h ${minutes}m`;
            } else {
                return `${minutes}m`;
            }
        },

        getBookingStatus(bookingDate, startTime) {
            if (!bookingDate || !startTime) return 'Unknown';
            
            try {
                const now = new Date();
                const bookingDateTime = new Date(`${bookingDate}T${startTime}`);
                
                if (isNaN(bookingDateTime.getTime())) {
                    return 'Invalid';
                }
                
                const diffMs = bookingDateTime - now;
                
                if (diffMs <= 0) {
                    // Sudah mulai, cek apakah sudah selesai
                    const endTime = this.calculateActualEndTime(bookingDate, startTime);
                    const endDateTime = new Date(`${bookingDate}T${endTime}`);
                    return now < endDateTime ? 'Ongoing' : 'Completed';
                }
                
                return 'Upcoming';
                
            } catch (error) {
                console.error('Error getting booking status:', error);
                return 'Error';
            }
        },

        getBookingStatusClass(bookingDate, startTime) {
            const status = this.getBookingStatus(bookingDate, startTime);
            switch(status) {
                case 'Ongoing': return 'text-green-600';
                case 'Completed': return 'text-gray-600';
                case 'Upcoming': return 'text-blue-600';
                default: return 'text-gray-600';
            }
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('id-ID');
        },

        formatPrice(amount) {
            return new Intl.NumberFormat('id-ID').format(amount);
        },

        formatDuration(type, value) {
            const durations = {
                'hour': `${value} Jam`,
                'day': `${value} Hari`,
                'week': `${value} Minggu`,
                'month': `${value} Bulan`,
                'year': `${value} Tahun`
            };
            return durations[type] || `${value} ${type}`;
        },

        addOneHour(timeString) {
            const [hours, minutes] = timeString.split(':').map(Number);
            const date = new Date();
            date.setHours(hours + 1, minutes);
            return date.toTimeString().slice(0, 5);
        },

        // ✅ NEW: Cleanup function
        destroy() {
            if (this.autoRefreshInterval) {
                clearInterval(this.autoRefreshInterval);
                console.log('Auto-refresh stopped');
            }
        }
    }
}

// Initialize when page loads
document.addEventListener('alpine:init', () => {
    Alpine.data('serviceConfirmation', serviceConfirmation);
});

// ✅ NEW: Global function untuk manual refresh
window.refreshServiceConfirmation = function() {
    if (window.Alpine && Alpine.$data && Alpine.$data.serviceConfirmation) {
        Alpine.$data.serviceConfirmation.manualRefresh();
    }
};
</script>
@endpush