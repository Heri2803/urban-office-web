@extends('layouts.admin')

@section('title', 'Service Confirmation')

@section('content')
<div x-data="serviceConfirmation()" class="container mx-auto px-4 py-6 max-w-7xl">
    
    <!-- Header Section dengan Stats -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Service Confirmation 📑</h1>
                <p class="text-gray-600">Manage and confirm bookings for Coworking Space and Event Space.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <!-- Quick Stats -->
                <div class="bg-white rounded-lg border border-gray-200 px-4 py-3 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <div>
                            <p class="text-sm text-gray-600">Pending</p>
                            <p class="text-lg font-bold text-gray-800" x-text="totalPending"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 px-4 py-3 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <div>
                            <p class="text-sm text-gray-600">Confirmed</p>
                            <p class="text-lg font-bold text-gray-800" x-text="totalConfirmed"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section yang Lebih Clean -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-gray-100" 
         x-data="filterState()">
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
                    <input type="date" x-model="startDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <input type="date" x-model="endDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
            </div>

            <!-- Di bagian filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service Type</label>
                <select x-model="serviceType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">All Services</option>
                    <option value="Coworking Space">Coworking Space</option> <!-- ✅ Update -->
                    <option value="Event Space">Event Space</option> <!-- ✅ Update -->
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select x-model="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">All Status</option>
                    <option value="settlement">Waiting Confirmation</option>
                    <option value="confirmed">Confirmed</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Time Frame</label>
                <select x-model="timeFrame" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
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
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        
        <!-- Coworking Space Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-blue-200 bg-gradient-to-r from-blue-50 to-blue-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857m0 0c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Coworking Space
                        </h2>
                        <p class="text-sm text-blue-700 mt-1" x-text="`Pending: ${coworkingStats.pending} | Confirmed: ${coworkingStats.confirmed} | Capacity: ${coworkingStats.capacity}`"></p>
                    </div>
                    <div class="bg-white rounded-lg px-3 py-2 border border-blue-200">
                        <p class="text-xs text-blue-600 font-medium">Real-time Updates</p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Action Section -->
                <div class="pb-6 border-b border-gray-200">
                    <h3 class="text-md font-semibold text-gray-700 mb-4">Confirm New Booking</h3>
                    <div class="space-y-4">
                        <div class="p-4 rounded-lg bg-blue-50 border border-blue-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Pending Booking</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" 
                                    x-model="selectedCoworkingBooking">
                                <option value="">-- Select Booking --</option>
                                <template x-for="booking in filteredCoworkingPending" :key="booking.id">
                                    <option :value="booking.id" x-text="`${booking.customer_name} - ${booking.booking_code} - ${formatDate(booking.booking_date)}`"></option>
                                </template>
                            </select>
                            <button @click="openConfirmModal(selectedCoworkingBooking, 'coworking')" 
                                    :disabled="!selectedCoworkingBooking"
                                    :class="!selectedCoworkingBooking ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-700'"
                                    class="w-full mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg transition-colors font-medium text-sm shadow-md">
                                Confirm Booking
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Booking Lists dengan Real-time Countdown -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Waiting Confirmation -->
                    <div>
                        <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <span class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></span>
                            Waiting Confirmation (<span x-text="coworkingStats.pending"></span>)
                        </h3>
                        <div class="space-y-3 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                            <template x-for="booking in filteredCoworkingPending" :key="booking.id">
                                <div class="border border-yellow-300 bg-yellow-50 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900" x-text="booking.booking_code"></p>
                                            <p class="text-xs text-gray-600" x-text="booking.customer_name"></p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-200 text-yellow-800">
                                            Settlement
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-600 space-y-1">
                                        <div class="flex justify-between">
                                            <span>Booking Date:</span>
                                            <span class="text-gray-900 font-medium" x-text="formatDate(booking.booking_date)"></span>
                                        </div>
                                        <!-- Di bagian booking lists - perbaiki tampilan waktu -->
                                        <div class="flex justify-between">
                                            <span>Time:</span>
                                            <span class="text-gray-900" x-text="booking.start_time + ' - ' + (booking.end_time || 'Calculating...')"></span>
                                        </div>

                                        <!-- Real-time Countdown -->
                                        <div class="flex justify-between items-center pt-2 mt-2 border-t border-yellow-200">
                                            <span class="text-yellow-700 font-medium" x-text="getBookingStatus(booking.booking_date, booking.start_time) === 'Upcoming' ? 'Starts in:' : 'Ends in:'"></span>
                                            <span class="text-sm font-bold" 
                                                :class="getBookingStatusClass(booking.booking_date, booking.start_time)"
                                                x-text="getRemainingTime(booking.booking_date, booking.start_time)"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Duration:</span>
                                            <span class="text-gray-900" x-text="formatDuration(booking.duration_type, booking.duration_value)"></span>
                                        </div>
                                        <!-- Real-time Countdown -->
                                        <div class="flex justify-between items-center pt-2 mt-2 border-t border-yellow-200">
                                            <span class="text-yellow-700 font-medium">Starts in:</span>
                                            <span class="text-sm font-bold text-yellow-700" 
                                                  x-text="getRemainingTime(booking.booking_date, booking.start_time)"></span>
                                        </div>
                                        <div class="flex justify-between font-bold text-sm text-blue-600 pt-2">
                                            <span>Amount:</span>
                                            <span x-text="`Rp ${formatPrice(booking.amount)}`"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Confirmed Bookings -->
                    <div>
                        <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                            Confirmed (<span x-text="coworkingStats.confirmed"></span>)
                        </h3>
                        <div class="space-y-3 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                            <template x-for="booking in filteredCoworkingConfirmed" :key="booking.id">
                                <div class="border border-green-300 bg-green-50 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900" x-text="booking.booking_code"></p>
                                            <p class="text-xs text-gray-600" x-text="booking.customer_name"></p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-200 text-green-800">
                                            Confirmed
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-600 space-y-1 mb-3">
                                        <div class="flex justify-between">
                                            <span>Booking Date:</span>
                                            <span class="text-gray-900 font-medium" x-text="formatDate(booking.booking_date)"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Start Time:</span>
                                            <span class="text-gray-900" x-text="booking.start_time"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Duration:</span>
                                            <span class="text-gray-900" x-text="formatDuration(booking.duration_type, booking.duration_value)"></span>
                                        </div>
                                        <!-- Real-time Countdown untuk Confirmed -->
                                        <div class="flex justify-between items-center pt-2 mt-2 border-t border-green-200">
                                            <span class="text-green-700 font-medium">Status:</span>
                                            <span class="text-xs font-bold" 
                                                  :class="getBookingStatusClass(booking.booking_date, booking.start_time)"
                                                  x-text="getBookingStatus(booking.booking_date, booking.start_time)"></span>
                                        </div>
                                    </div>
                                    <button @click="openCancelModal(booking.id, 'coworking')" 
                                            class="w-full px-3 py-1.5 bg-red-100 text-red-700 text-xs rounded-lg hover:bg-red-200 transition-colors font-medium">
                                        Cancel Confirmation
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Space Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-orange-200 bg-gradient-to-r from-orange-50 to-orange-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Event Space
                        </h2>
                        <p class="text-sm text-orange-700 mt-1" x-text="`Pending: ${eventSpaceStats.pending} | Confirmed: ${eventSpaceStats.confirmed}`"></p>
                    </div>
                    <div class="bg-white rounded-lg px-3 py-2 border border-orange-200">
                        <p class="text-xs text-orange-600 font-medium">Cleanup Schedule Included</p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Action Section -->
                <div class="pb-6 border-b border-gray-200">
                    <h3 class="text-md font-semibold text-gray-700 mb-4">Confirm New Booking</h3>
                    <div class="space-y-4">
                        <div class="p-3 mb-3 rounded-lg bg-blue-100 border border-blue-300">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-800">Cleanup Schedule</p>
                                    <p class="text-xs text-blue-700 mt-0.5">1 hour block time after each event for cleanup</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 rounded-lg bg-orange-50 border border-orange-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Pending Booking</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                    x-model="selectedEventSpaceBooking">
                                <option value="">-- Select Booking --</option>
                                <template x-for="booking in filteredEventSpacePending" :key="booking.id">
                                    <option :value="booking.id" x-text="`${booking.customer_name} - ${booking.booking_code} - ${formatDate(booking.booking_date)}`"></option>
                                </template>
                            </select>
                            <button @click="openConfirmModal(selectedEventSpaceBooking, 'event_space')" 
                                    :disabled="!selectedEventSpaceBooking"
                                    :class="!selectedEventSpaceBooking ? 'opacity-50 cursor-not-allowed' : 'hover:bg-orange-700'"
                                    class="w-full mt-3 px-4 py-2 bg-orange-600 text-white rounded-lg transition-colors font-medium text-sm shadow-md">
                                Confirm Booking
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Booking Lists dengan Real-time Countdown -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Waiting Confirmation -->
                    <div>
                        <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <span class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></span>
                            Waiting Confirmation (<span x-text="eventSpaceStats.pending"></span>)
                        </h3>
                        <div class="space-y-3 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                            <template x-for="booking in filteredEventSpacePending" :key="booking.id">
                                <div class="border border-yellow-300 bg-yellow-50 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900" x-text="booking.booking_code"></p>
                                            <p class="text-xs text-gray-600" x-text="booking.customer_name"></p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-200 text-yellow-800">
                                            Settlement
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-600 space-y-1">
                                        <div class="flex justify-between">
                                            <span>Event Date:</span>
                                            <span class="text-gray-900 font-medium" x-text="formatDate(booking.booking_date)"></span>
                                        </div>
                                        <!-- Di bagian booking lists - perbaiki tampilan waktu -->
                                        <div class="flex justify-between">
                                            <span>Time:</span>
                                            <span class="text-gray-900" x-text="booking.start_time + ' - ' + (booking.end_time || 'Calculating...')"></span>
                                        </div>

                                        <!-- Real-time Countdown -->
                                        <div class="flex justify-between items-center pt-2 mt-2 border-t border-yellow-200">
                                            <span class="text-yellow-700 font-medium" x-text="getBookingStatus(booking.booking_date, booking.start_time) === 'Upcoming' ? 'Starts in:' : 'Ends in:'"></span>
                                            <span class="text-sm font-bold" 
                                                :class="getBookingStatusClass(booking.booking_date, booking.start_time)"
                                                x-text="getRemainingTime(booking.booking_date, booking.start_time)"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Cleanup:</span>
                                            <span class="text-orange-700" x-text="`${booking.end_time} - ${addOneHour(booking.end_time)}`"></span>
                                        </div>
                                        <!-- Real-time Countdown -->
                                        <div class="flex justify-between items-center pt-2 mt-2 border-t border-yellow-200">
                                            <span class="text-yellow-700 font-medium">Starts in:</span>
                                            <span class="text-sm font-bold text-yellow-700" 
                                                  x-text="getRemainingTime(booking.booking_date, booking.start_time)"></span>
                                        </div>
                                        <div class="flex justify-between font-bold text-sm text-orange-600 pt-2">
                                            <span>Amount:</span>
                                            <span x-text="`Rp ${formatPrice(booking.amount)}`"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Confirmed Bookings -->
                    <div>
                        <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                            Confirmed (<span x-text="eventSpaceStats.confirmed"></span>)
                        </h3>
                        <div class="space-y-3 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                            <template x-for="booking in filteredEventSpaceConfirmed" :key="booking.id">
                                <div class="border border-green-300 bg-green-50 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900" x-text="booking.booking_code"></p>
                                            <p class="text-xs text-gray-600" x-text="booking.customer_name"></p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-200 text-green-800">
                                            Confirmed
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-600 space-y-1 mb-3">
                                        <div class="flex justify-between">
                                            <span>Event Date:</span>
                                            <span class="text-gray-900 font-medium" x-text="formatDate(booking.booking_date)"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Time:</span>
                                            <span class="text-gray-900" x-text="`${booking.start_time} - ${booking.end_time}`"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Cleanup:</span>
                                            <span class="text-orange-700" x-text="`${booking.end_time} - ${addOneHour(booking.end_time)}`"></span>
                                        </div>
                                        <!-- Real-time Status -->
                                        <div class="flex justify-between items-center pt-2 mt-2 border-t border-green-200">
                                            <span class="text-green-700 font-medium">Status:</span>
                                            <span class="text-xs font-bold" 
                                                  :class="getBookingStatusClass(booking.booking_date, booking.start_time)"
                                                  x-text="getBookingStatus(booking.booking_date, booking.start_time)"></span>
                                        </div>
                                    </div>
                                    <button @click="openCancelModal(booking.id, 'event_space')" 
                                            class="w-full px-3 py-1.5 bg-red-100 text-red-700 text-xs rounded-lg hover:bg-red-200 transition-colors font-medium">
                                        Cancel Confirmation
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Confirmation Modal -->
    <div id="confirmModal" x-show="showConfirmModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full" @click.outside="closeConfirmModal">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Confirm Booking</h3>
                <p class="text-sm text-gray-600 text-center mb-6">Are you sure you want to confirm this booking?</p>
                
                <!-- ✅ Tambahkan x-show untuk mencegah akses null -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6" x-show="selectedBooking">
                    <template x-if="selectedBooking">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Booking ID:</span>
                                <span class="font-medium text-gray-900" x-text="selectedBooking.booking_code"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Customer:</span>
                                <span class="font-medium text-gray-900" x-text="selectedBooking.customer_name"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Service:</span>
                                <span class="font-medium text-gray-900" x-text="selectedBooking.room_type === 'coworking_space' ? 'Coworking Space' : 'Event Space'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date:</span>
                                <span class="font-medium text-gray-900" x-text="formatDate(selectedBooking.booking_date)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Time:</span>
                                <span class="font-medium text-gray-900" x-text="selectedBooking.start_time"></span>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div class="flex gap-3">
                    <button @click="closeConfirmModal" 
                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        Cancel
                    </button>
                    <button @click="confirmBookingAction" 
                            :disabled="!selectedBooking"
                            :class="!selectedBooking ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-700'"
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg transition-colors font-medium">
                        Confirm Booking
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div id="cancelModal" x-show="showCancelModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full" @click.outside="closeCancelModal">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Cancel Confirmation</h3>
                <p class="text-sm text-gray-600 text-center mb-6">Are you sure you want to cancel this confirmation? The booking will return to waiting confirmation list.</p>
                
                <!-- ✅ Tambahkan x-show untuk mencegah akses null -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6" x-show="selectedBooking">
                    <template x-if="selectedBooking">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Booking ID:</span>
                                <span class="font-medium text-gray-900" x-text="selectedBooking.booking_code"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Customer:</span>
                                <span class="font-medium text-gray-900" x-text="selectedBooking.customer_name"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Service:</span>
                                <span class="font-medium text-gray-900" x-text="selectedBooking.room_type === 'coworking_space' ? 'Coworking Space' : 'Event Space'"></span>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div class="flex gap-3">
                    <button @click="closeCancelModal" 
                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        No, Keep It
                    </button>
                    <button @click="cancelConfirmationAction" 
                            :disabled="!selectedBooking"
                            :class="!selectedBooking ? 'opacity-50 cursor-not-allowed' : 'hover:bg-red-700'"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg transition-colors font-medium">
                        Yes, Cancel
                    </button>
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
        // Modal states
        showConfirmModal: false,
        showCancelModal: false,
        showSuccessNotification: false,
        showErrorNotification: false,
        successMessage: '',
        errorMessage: '',
        selectedBooking: null,
        selectedServiceType: '',
        
        // Data dari backend
        coworkingBookings: @json($coworkingBookings ?? []),
        eventSpaceBookings: @json($eventSpaceBookings ?? []),
        
        // Selected bookings untuk dropdown
        selectedCoworkingBooking: '',
        selectedEventSpaceBooking: '',

        // ✅ NEW: Real-time properties
        autoRefreshInterval: null,
        isLoading: false,
        lastRefresh: null,

        // Computed properties
        // Di Alpine.js - Perbaiki computed properties
        get filteredCoworkingPending() {
            return this.coworkingBookings.filter(b => b.status === 'settlement');
        },
        get filteredCoworkingConfirmed() {
            return this.coworkingBookings.filter(b => b.status === 'confirmed');
        },
        get filteredEventSpacePending() {
            return this.eventSpaceBookings.filter(b => b.status === 'settlement');
        },
        get filteredEventSpaceConfirmed() {
            return this.eventSpaceBookings.filter(b => b.status === 'confirmed');
        },
        get coworkingStats() {
            const pending = this.filteredCoworkingPending.length;
            const confirmed = this.filteredCoworkingConfirmed.length;
            return {
                pending: pending,
                confirmed: confirmed,
                capacity: this.calculateCapacity(this.coworkingBookings)
            };
        },
        get eventSpaceStats() {
            const pending = this.filteredEventSpacePending.length;
            const confirmed = this.filteredEventSpaceConfirmed.length;
            return {
                pending: pending,
                confirmed: confirmed
            };
        },
        get totalPending() {
            return this.coworkingStats.pending + this.eventSpaceStats.pending;
        },
        get totalConfirmed() {
            return this.coworkingStats.confirmed + this.eventSpaceStats.confirmed;
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

        // ✅ NEW: Setup reactive watchers
        setupWatchers() {
            // Cleanup selected booking ketika modal ditutup
            this.$watch('showConfirmModal', (value) => {
                if (!value) {
                    this.selectedBooking = null;
                    this.selectedServiceType = '';
                }
            });
            
            this.$watch('showCancelModal', (value) => {
                if (!value) {
                    this.selectedBooking = null;
                    this.selectedServiceType = '';
                }
            });
            
            // Log perubahan data untuk debugging
            this.$watch('coworkingBookings', (newVal, oldVal) => {
                console.log('Coworking bookings updated:', newVal.length, 'items');
            });
            
            this.$watch('eventSpaceBookings', (newVal, oldVal) => {
                console.log('Event space bookings updated:', newVal.length, 'items');
            });
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
            console.log('🔄 Refreshing data from server...');
            
            try {
                const response = await fetch('/booking/service-confirmation/api/data');
                
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
            // Preserve current state
            const currentSelectedId = this.selectedBooking?.id;
            const currentSelectedType = this.selectedServiceType;
            
            console.log('Updating bookings data:', {
                newCoworking: newCoworking.length,
                newEventSpace: newEventSpace.length
            });

            // Update dengan smart merge
            this.coworkingBookings = this.mergeBookingsData(this.coworkingBookings, newCoworking, 'coworking');
            this.eventSpaceBookings = this.mergeBookingsData(this.eventSpaceBookings, newEventSpace, 'event_space');

            // Restore selected booking state
            if (currentSelectedId) {
                this.restoreSelectedBooking(currentSelectedId, currentSelectedType);
            }
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

        // ✅ NEW: Restore selected booking setelah refresh
        restoreSelectedBooking(bookingId, serviceType) {
            const bookings = serviceType === 'coworking' ? this.coworkingBookings : this.eventSpaceBookings;
            const foundBooking = bookings.find(b => b.id == bookingId);
            
            if (foundBooking) {
                this.selectedBooking = foundBooking;
                this.selectedServiceType = serviceType;
                console.log('Restored selected booking:', foundBooking.booking_code);
            } else {
                console.warn('Selected booking not found after refresh, clearing selection');
                this.selectedBooking = null;
                this.selectedServiceType = '';
            }
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

        // Modal Methods
         openConfirmModal(bookingId, serviceType) {
            console.log('🔓 Opening confirm modal for:', bookingId, serviceType);
            
            const bookings = serviceType === 'coworking' ? this.coworkingBookings : this.eventSpaceBookings;
            console.log('Available bookings:', bookings.map(b => ({ id: b.id, code: b.booking_code })));
            
            this.selectedBooking = bookings.find(b => b.id == bookingId);
            this.selectedServiceType = serviceType;
            
            if (this.selectedBooking) {
                console.log('✅ Found booking:', this.selectedBooking.booking_code, this.selectedBooking);
                this.showConfirmModal = true;
            } else {
                console.error('❌ Booking not found:', bookingId, 'in', serviceType);
                console.error('Available IDs:', bookings.map(b => b.id));
                this.showError('Booking not found. Please refresh and try again.');
            }
        },

        openCancelModal(bookingId, serviceType) {
            console.log('Opening cancel modal for:', bookingId, serviceType);
            
            const bookings = serviceType === 'coworking' ? this.coworkingBookings : this.eventSpaceBookings;
            this.selectedBooking = bookings.find(b => b.id == bookingId);
            this.selectedServiceType = serviceType;
            
            if (this.selectedBooking) {
                console.log('Found booking:', this.selectedBooking.booking_code);
                this.showCancelModal = true;
            } else {
                console.error('Booking not found:', bookingId);
                this.showError('Booking not found. Please refresh and try again.');
            }
        },

        closeConfirmModal() {
            this.showConfirmModal = false;
            this.selectedBooking = null;
            this.selectedServiceType = '';
        },

        closeCancelModal() {
            this.showCancelModal = false;
            this.selectedBooking = null;
            this.selectedServiceType = '';
        },

        async confirmBookingAction() {
            if (!this.selectedBooking) return;
            
            try {
                const response = await fetch(`/booking/service-confirmation/${this.selectedBooking.id}/confirm`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    this.showSuccessNotification = true;
                    this.successMessage = result.message;
                    
                    // Update local data
                    if (this.selectedServiceType === 'coworking') {
                        const index = this.coworkingBookings.findIndex(b => b.id === this.selectedBooking.id);
                        if (index !== -1) {
                            this.coworkingBookings[index].status = 'confirmed';
                        }
                    } else {
                        const index = this.eventSpaceBookings.findIndex(b => b.id === this.selectedBooking.id);
                        if (index !== -1) {
                            this.eventSpaceBookings[index].status = 'confirmed';
                        }
                    }
                    
                    // Reset dropdown selection
                    this.selectedCoworkingBooking = '';
                    this.selectedEventSpaceBooking = '';
                    
                    // ✅ NEW: Trigger immediate refresh untuk sync dengan server
                    setTimeout(() => {
                        this.refreshData();
                    }, 1000);
                    
                    setTimeout(() => {
                        this.showSuccessNotification = false;
                    }, 3000);
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                this.showErrorNotification = true;
                this.errorMessage = error.message;
                
                setTimeout(() => {
                    this.showErrorNotification = false;
                }, 5000);
            }
            
            this.closeConfirmModal();
        },

        async cancelConfirmationAction() {
            if (!this.selectedBooking) return;
            
            try {
                const response = await fetch(`/booking/service-confirmation/${this.selectedBooking.id}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    this.showSuccessNotification = true;
                    this.successMessage = result.message;
                    
                    // Update local data
                    if (this.selectedServiceType === 'coworking') {
                        const index = this.coworkingBookings.findIndex(b => b.id === this.selectedBooking.id);
                        if (index !== -1) {
                            this.coworkingBookings[index].status = 'settlement';
                        }
                    } else {
                        const index = this.eventSpaceBookings.findIndex(b => b.id === this.selectedBooking.id);
                        if (index !== -1) {
                            this.eventSpaceBookings[index].status = 'settlement';
                        }
                    }
                    
                    // ✅ NEW: Trigger immediate refresh untuk sync dengan server
                    setTimeout(() => {
                        this.refreshData();
                    }, 1000);
                    
                    setTimeout(() => {
                        this.showSuccessNotification = false;
                    }, 3000);
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                this.showErrorNotification = true;
                this.errorMessage = error.message;
                
                setTimeout(() => {
                    this.showErrorNotification = false;
                }, 5000);
            }
            
            this.closeCancelModal();
        },

        // Utility functions
        getRemainingTime(bookingDate, startTime) {
            if (!bookingDate || !startTime) {
                return 'Invalid time';
            }
            
            try {
                const now = new Date();
                const bookingDateTime = new Date(`${bookingDate}T${startTime}`);
                
                // Validasi date
                if (isNaN(bookingDateTime.getTime())) {
                    return 'Invalid date';
                }
                
                const diffMs = bookingDateTime - now;
                
                if (diffMs <= 0) {
                    // Sudah mulai, hitung waktu sampai selesai
                    const endTime = this.calculateActualEndTime(bookingDate, startTime);
                    const endDateTime = new Date(`${bookingDate}T${endTime}`);
                    const remainingMs = endDateTime - now;
                    
                    if (remainingMs <= 0) {
                        return 'Completed';
                    }
                    
                    // Tampilkan waktu tersisa sampai selesai
                    return this.formatTimeRemaining(remainingMs, true);
                }
                
                // Belum mulai, hitung waktu sampai mulai
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

        calculateCapacity(bookings) {
            const maxCapacity = 20;
            const confirmedCount = bookings.filter(b => b.status === 'confirmed').length;
            const percentage = (confirmedCount / maxCapacity) * 100;
            return `${confirmedCount}/${maxCapacity} (${Math.round(percentage)}%)`;
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

function filterState() {
    return {
        startDate: '',
        endDate: '',
        serviceType: '',
        status: '',
        timeFrame: '',
        
        applyFilters() {
            const filters = {
                startDate: this.startDate,
                endDate: this.endDate,
                serviceType: this.serviceType,
                status: this.status,
                timeFrame: this.timeFrame
            };
            
            console.log('Applying filters:', filters);
            
            // ✅ NEW: Trigger refresh dengan filter
            if (window.Alpine && Alpine.$data && Alpine.$data.serviceConfirmation) {
                Alpine.$data.serviceConfirmation.refreshData();
            }
        },
        
        resetFilters() {
            this.startDate = '';
            this.endDate = '';
            this.serviceType = '';
            this.status = '';
            this.timeFrame = '';
            
            console.log('Filters reset');
            
            // ✅ NEW: Refresh data setelah reset filter
            if (window.Alpine && Alpine.$data && Alpine.$data.serviceConfirmation) {
                Alpine.$data.serviceConfirmation.refreshData();
            }
        }
    }
}

// Initialize when page loads
document.addEventListener('alpine:init', () => {
    Alpine.data('serviceConfirmation', serviceConfirmation);
    Alpine.data('filterState', filterState);
});

// ✅ NEW: Global function untuk manual refresh
window.refreshServiceConfirmation = function() {
    if (window.Alpine && Alpine.$data && Alpine.$data.serviceConfirmation) {
        Alpine.$data.serviceConfirmation.manualRefresh();
    }
};
</script>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #d1d5db;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: #9ca3af;
}
</style>
@endpush