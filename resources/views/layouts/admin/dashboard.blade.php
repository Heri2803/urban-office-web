@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div x-data="dashboardData()" x-init="init()" class="space-y-6">
    
    {{-- Header Section --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-600 mt-1">Welcome back, <span class="font-semibold">Admin Budi</span> - Cabang Surabaya</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="refreshData()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span class="text-sm">Refresh</span>
            </button>
            <a href="{{ route('admin.booking.walk-in-booking') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="text-sm">Walk-in Booking</span>
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Card 1: Total Booking Hari Ini --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Booking Hari Ini</p>
                    <h3 class="text-3xl font-bold text-gray-800" x-text="stats.totalBookingToday">0</h3>
                    <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                        </svg>
                        <span>+12% dari kemarin</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Card 2: Revenue Hari Ini --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Revenue Hari Ini</p>
                    <h3 class="text-3xl font-bold text-gray-800" x-text="formatCurrency(stats.revenueToday)">Rp 0</h3>
                    <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                        </svg>
                        <span>+8% dari kemarin</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Card 3: Booking Pending Konfirmasi --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pending Konfirmasi</p>
                    <h3 class="text-3xl font-bold text-gray-800" x-text="stats.pendingConfirmation">0</h3>
                    <p class="text-xs text-orange-600 mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Perlu perhatian</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Card 4: Ruangan Terisi Sekarang --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Ruangan Terisi</p>
                    <h3 class="text-3xl font-bold text-gray-800">
                        <span x-text="stats.roomsOccupied">0</span><span class="text-lg text-gray-500">/</span><span class="text-lg text-gray-500" x-text="stats.totalRooms">0</span>
                    </h3>
                    <p class="text-xs text-gray-600 mt-2" x-text="stats.occupancyRate + '% Occupancy Rate'">0% Occupancy Rate</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Transaction Overview</h2>
                <p class="text-sm text-gray-600 mt-1">Monitor your daily, monthly, and yearly transactions</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- Metric Toggle --}}
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button @click="chartMetric = 'revenue'" :class="chartMetric === 'revenue' ? 'bg-white shadow-sm' : ''" class="px-3 py-1.5 text-sm rounded-md transition">
                        Revenue
                    </button>
                    <button @click="chartMetric = 'booking'" :class="chartMetric === 'booking' ? 'bg-white shadow-sm' : ''" class="px-3 py-1.5 text-sm rounded-md transition">
                        Booking
                    </button>
                </div>
                
                {{-- Period Filter --}}
                <select x-model="chartPeriod" @change="updateChart()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="daily">7 Hari Terakhir</option>
                    <option value="monthly">12 Bulan Terakhir</option>
                    <option value="yearly">3 Tahun Terakhir</option>
                </select>
            </div>
        </div>

        {{-- Chart Canvas --}}
        <div class="h-80">
            <canvas id="transactionChart"></canvas>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Recent Transactions</h2>
                <p class="text-sm text-gray-600 mt-1">Latest 50 bookings and their payment status</p>
            </div>
            {{-- ✅ ADD: Link to All Bookings --}}
            <a href="{{ route('admin.booking.all') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                View All Bookings
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        {{-- Service Tabs - ✅ DYNAMIC COUNTS --}}
        <div class="border-b border-gray-200 mb-4">
            <nav class="flex gap-6 overflow-x-auto whitespace-nowrap">
                <button @click="activeServiceTab = 'all'" 
                        :class="activeServiceTab === 'all' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800'" 
                        class="py-3 px-1 border-b-2 font-medium text-sm transition">
                    <span x-text="`Semua (${tabCounts.all})`">Semua (0)</span>
                </button>
                <button @click="activeServiceTab = 'meeting'" 
                        :class="activeServiceTab === 'meeting' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800'" 
                        class="py-3 px-1 border-b-2 font-medium text-sm transition">
                    <span x-text="`Meeting Room (${tabCounts.meeting})`">Meeting Room (0)</span>
                </button>
                <button @click="activeServiceTab = 'private'" 
                        :class="activeServiceTab === 'private' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800'" 
                        class="py-3 px-1 border-b-2 font-medium text-sm transition">
                    <span x-text="`Private Office (${tabCounts.private})`">Private Office (0)</span>
                </button>
                <button @click="activeServiceTab === 'sharing'" 
                        :class="activeServiceTab === 'sharing' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800'" 
                        class="py-3 px-1 border-b-2 font-medium text-sm transition">
                    <span x-text="`Sharing Room (${tabCounts.sharing})`">Sharing Room (0)</span>
                </button>
                <button @click="activeServiceTab = 'coworking'" 
                        :class="activeServiceTab === 'coworking' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800'" 
                        class="py-3 px-1 border-b-2 font-medium text-sm transition">
                    <span x-text="`Coworking (${tabCounts.coworking})`">Coworking (0)</span>
                </button>
                <button @click="activeServiceTab = 'virtual'" 
                        :class="activeServiceTab === 'virtual' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800'" 
                        class="py-3 px-1 border-b-2 font-medium text-sm transition">
                    <span x-text="`Virtual Office (${tabCounts.virtual})`">Virtual Office (0)</span>
                </button>
                <button @click="activeServiceTab = 'event'" 
                        :class="activeServiceTab === 'event' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800'" 
                        class="py-3 px-1 border-b-2 font-medium text-sm transition">
                    <span x-text="`Event Space (${tabCounts.event})`">Event Space (0)</span>
                </button>
            </nav>
        </div>

        {{-- Filter & Entries --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <label class="text-sm text-gray-600">Show</label>
                <select x-model="entriesPerPage" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                </select>
                <label class="text-sm text-gray-600">entries</label>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button @click="statusFilter = 'all'" 
                            :class="statusFilter === 'all' ? 'bg-white shadow-sm' : ''" 
                            class="px-3 py-1.5 text-xs rounded-md transition">
                        All
                    </button>
                    <button @click="statusFilter = 'settlement'" 
                            :class="statusFilter === 'settlement' ? 'bg-white shadow-sm' : ''" 
                            class="px-3 py-1.5 text-xs rounded-md transition">
                        Settlement
                    </button>
                    <button @click="statusFilter = 'pending'" 
                            :class="statusFilter === 'pending' ? 'bg-white shadow-sm' : ''" 
                            class="px-3 py-1.5 text-xs rounded-md transition">
                        Pending
                    </button>
                    <button @click="statusFilter = 'expire'" 
                            :class="statusFilter === 'expire' ? 'bg-white shadow-sm' : ''" 
                            class="px-3 py-1.5 text-xs rounded-md transition">
                        Expired
                    </button>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Booking ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Service</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status Payment</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    {{-- ✅ Show message if no data --}}
                    <template x-if="filteredTransactions.length === 0">
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-sm">No transactions found</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                    
                    <template x-for="(transaction, index) in filteredTransactions" :key="transaction.id">
                        <tr class="hover:bg-gray-50 transition cursor-pointer" @click="viewDetail(transaction)">
                            <td class="px-4 py-4">
                                <span class="text-sm font-medium text-blue-600" x-text="transaction.bookingId"></span>
                            </td>
                            <td class="px-4 py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-800" x-text="transaction.customerName"></p>
                                    <p class="text-xs text-gray-500" x-text="transaction.customerPhone"></p>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-800" x-text="transaction.service"></p>
                                    <p class="text-xs text-gray-500" x-text="transaction.package"></p>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span x-show="transaction.status === 'settlement'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Settlement
                                </span>
                                <span x-show="transaction.status === 'pending'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Pending
                                </span>
                                <span x-show="transaction.status === 'expire'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Expired
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm text-gray-800" x-text="transaction.time"></p>
                            </td>
                            <td class="px-4 py-4">
                                {{-- ✅ SIMPLIFIED: Single button that redirects --}}
                                <button @click.stop="viewDetail(transaction)" 
                                        class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Pagination - ✅ DYNAMIC --}}
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600" x-text="`Showing ${paginationInfo.from} to ${paginationInfo.to} of ${paginationInfo.total} entries`">
                Showing 0 to 0 of 0 entries
            </p>
        </div>
    </div>

    {{-- Room Detail Modal --}}
    <div x-show="showRoomModal" x-cloak @click.away="showRoomModal = false" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 transform transition-all">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">
                            Room <span x-text="selectedRoom?.number"></span> - Detail
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Real-time room information</p>
                    </div>
                    <button @click="showRoomModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Modal Content --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-3 pb-4 border-b border-gray-200">
                        <div class="flex-1">
                            <p class="text-sm text-gray-600">Current Status</p>
                            <div class="mt-2">
                                <span x-show="selectedRoom?.status === 'available'" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    🟢 Available
                                </span>
                                <span x-show="selectedRoom?.status === 'occupied'" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    🔴 Occupied
                                </span>
                                <span x-show="selectedRoom?.status === 'booked'" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    🟡 Booked
                                </span>
                                <span x-show="selectedRoom?.status === 'cleaning'" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                    🟣 Cleaning
                                </span>
                                <span x-show="selectedRoom?.status === 'maintenance'" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    ⚫ Maintenance
                                </span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-600">Capacity</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1" x-text="selectedRoom?.capacity + ' pax'"></p>
                        </div>
                    </div>

                    <div class="pb-4 border-b border-gray-200">
                        <p class="text-sm font-semibold text-gray-700 mb-2">Facilities</p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="facility in selectedRoom?.facilities" :key="facility">
                                <span class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span x-text="facility"></span>
                                </span>
                            </template>
                        </div>
                    </div>

                    <div class="pb-4 border-b border-gray-200" x-show="selectedRoom?.currentBooking">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Current Booking</p>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Customer:</span>
                                <span class="text-sm font-medium text-gray-800" x-text="selectedRoom?.currentBooking?.customer"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Time:</span>
                                <span class="text-sm font-medium text-gray-800" x-text="selectedRoom?.currentBooking?.time"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Remaining:</span>
                                <span class="text-sm font-medium text-orange-600" x-text="selectedRoom?.currentBooking?.remaining"></span>
                            </div>
                        </div>
                    </div>

                    <div x-show="selectedRoom?.nextBooking">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Next Booking</p>
                        <div class="bg-blue-50 rounded-lg p-4 space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Customer:</span>
                                <span class="text-sm font-medium text-gray-800" x-text="selectedRoom?.nextBooking?.customer"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Time:</span>
                                <span class="text-sm font-medium text-gray-800" x-text="selectedRoom?.nextBooking?.time"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Actions --}}
                <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200">
                    <button @click="showRoomModal = false" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Close
                    </button>
                    <button class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                        Go to Room Management
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Alpine.js Data & Logic --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function dashboardData() {
    return {
        // Stats Data
        stats: {
            totalBookingToday: 0,
            revenueToday: 0,
            pendingConfirmation: 0,
            roomsOccupied: 0,
            totalRooms: 0,
            occupancyRate: 0
        },

        // Chart Data
        chartMetric: 'revenue',
        chartPeriod: 'daily',
        chartInstance: null,
        chartLoading: false,
        chartUpdateInProgress: false,
        currentLocationId: 1, // Default location
        availableLocations: [],

        // Transactions
        activeServiceTab: 'all',
        statusFilter: 'all',
        entriesPerPage: 5,
        transactions: [],
        allTransactions: [],
        showRoomModal: false,
        selectedRoom: null,

        // Loading & Error
        loading: false,
        refreshing: false,

        // ==========================================
        // INITIALIZATION
        // ==========================================
        async init() {
            console.log('🚀 Dashboard initializing...');
            this.loading = true;
            
            try {
                // Load data first
                await Promise.all([
                    this.fetchDashboardStats(),
                    this.fetchAllTransactions() // ✅ CHANGED: Load all, not just today
                ]);
                
                // Wait for DOM to be ready
                await this.$nextTick();
                
                // Initialize chart once
                setTimeout(() => {
                    this.createChart();
                }, 300);
                
                console.log('✅ Dashboard initialized successfully');
                
            } catch (error) {
                console.error('❌ Initialization error:', error);
            } finally {
                this.loading = false;
            }
        },

        // ==========================================
        // CHART METHODS
        // ==========================================
        createChart() {
            const ctx = document.getElementById('transactionChart');
            if (!ctx) {
                console.error('❌ Chart canvas not found');
                return;
            }

            // Destroy previous chart instance
            if (this.chartInstance) {
                try {
                    this.chartInstance.destroy();
                } catch (e) {
                    console.warn('Chart destroy error:', e);
                }
                this.chartInstance = null;
            }

            try {
                const staticData = this.getStaticChartData();
                
                this.chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: staticData.labels,
                        datasets: [{
                            label: this.chartMetric === 'revenue' ? 'Revenue (Rp)' : 'Booking Count',
                            data: staticData.data,
                            borderColor: this.chartMetric === 'revenue' ? 'rgb(37, 99, 235)' : 'rgb(16, 185, 129)',
                            backgroundColor: this.chartMetric === 'revenue' ? 'rgba(37, 99, 235, 0.1)' : 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: false, // ✅ Disable to prevent loop
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            legend: { 
                                display: false 
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 14
                                },
                                bodyFont: {
                                    size: 13
                                },
                                callbacks: {
                                    label: (context) => {
                                        if (this.chartMetric === 'revenue') {
                                            return 'Revenue: Rp ' + context.parsed.y.toLocaleString('id-ID');
                                        }
                                        return 'Bookings: ' + context.parsed.y;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    callback: (value) => {
                                        if (this.chartMetric === 'revenue') {
                                            if (value >= 1000000) {
                                                return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                            }
                                            return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                                        }
                                        return value;
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                console.log('✅ Chart created successfully');
                
                // Try to load real data in background
                setTimeout(() => {
                    this.loadChartData();
                }, 2000);

            } catch (error) {
                console.error('❌ Chart creation failed:', error);
            }
        },

        async loadChartData() {
            if (this.chartLoading || !this.chartInstance) return;

            try {
                this.chartLoading = true;
                console.log('🔄 Loading real chart data...');

                const response = await fetch(
                    `/dashboard/chart-data?period=${this.chartPeriod}&metric=${this.chartMetric}`
                );
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    console.log('📊 Chart data received:', result.data);
                    
                    if (this.chartInstance && this.chartInstance.data) {
                        this.chartInstance.data.labels = result.data.labels || [];
                        this.chartInstance.data.datasets[0].data = result.data.data || [];
                        this.chartInstance.update('none');
                        console.log('✅ Chart updated with real data');
                    }
                } else {
                    console.warn('⚠️ Invalid chart data response');
                }

            } catch (error) {
                console.log('ℹ️ Using static chart data. Error:', error.message);
            } finally {
                this.chartLoading = false;
            }
        },

        async updateChart() {
            // Prevent concurrent updates
            if (this.chartUpdateInProgress) {
                console.log('⏳ Chart update already in progress, skipping...');
                return;
            }

            this.chartUpdateInProgress = true;

            try {
                if (!this.chartInstance) {
                    console.log('🔄 Chart instance not found, recreating...');
                    this.createChart();
                    return;
                }

                // Update with static data first (instant feedback)
                const staticData = this.getStaticChartData();
                
                if (this.chartInstance && this.chartInstance.data) {
                    this.chartInstance.data.labels = staticData.labels;
                    this.chartInstance.data.datasets[0].data = staticData.data;
                    this.chartInstance.data.datasets[0].label = this.chartMetric === 'revenue' ? 'Revenue (Rp)' : 'Booking Count';
                    this.chartInstance.data.datasets[0].borderColor = this.chartMetric === 'revenue' ? 'rgb(37, 99, 235)' : 'rgb(16, 185, 129)';
                    this.chartInstance.data.datasets[0].backgroundColor = this.chartMetric === 'revenue' ? 'rgba(37, 99, 235, 0.1)' : 'rgba(16, 185, 129, 0.1)';
                    
                    this.chartInstance.update('none');
                    console.log('✅ Chart updated with static data');
                }

                // Load real data in background
                setTimeout(() => {
                    this.loadChartData();
                }, 500);

            } catch (error) {
                console.error('❌ Chart update failed:', error);
                // Fallback: recreate chart
                this.createChart();
            } finally {
                this.chartUpdateInProgress = false;
            }
        },

        getStaticChartData() {
            if (this.chartPeriod === 'daily') {
                return {
                    labels: ['23 Nov', '24 Nov', '25 Nov', '26 Nov', '27 Nov', '28 Nov', '29 Nov'],
                    data: this.chartMetric === 'revenue' 
                        ? [1200000, 1500000, 1800000, 2100000, 2500000, 2200000, 1900000]
                        : [8, 10, 12, 15, 18, 16, 14]
                };
            } else if (this.chartPeriod === 'monthly') {
                return {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    data: this.chartMetric === 'revenue'
                        ? [25000000, 28000000, 30000000, 32000000, 35000000, 38000000, 40000000, 42000000, 45000000, 48000000, 50000000, 52000000]
                        : [150, 180, 200, 220, 250, 280, 300, 320, 350, 380, 400, 420]
                };
            } else {
                return {
                    labels: ['2023', '2024', '2025'],
                    data: this.chartMetric === 'revenue'
                        ? [450000000, 520000000, 480000000]
                        : [2500, 3000, 2800]
                };
            }
        },

        // ==========================================
        // API METHODS
        // ==========================================
        async fetchDashboardStats() {
            try {
                console.log('📊 Fetching dashboard stats...');
                const response = await fetch(`/dashboard/stats?location_id=${this.currentLocationId}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    this.stats = result.data;
                    console.log('✅ Stats loaded:', this.stats);
                } else {
                    console.warn('⚠️ Invalid stats response');
                }
            } catch (error) {
                console.error('❌ Failed to fetch stats:', error);
            }
        },

        // ✅ NEW METHOD: Fetch all recent transactions (not just today)
        async fetchAllTransactions() {
            try {
                console.log('📋 Fetching all recent transactions...');
                
                // ✅ Remove date filter to get all recent bookings
                const url = `/booking/all/api/data?per_page=50`;
                console.log('🔗 Fetching from:', url);
                
                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                
                const result = await response.json();
                
                console.log('📦 API Response:', result);
                console.log('📊 Total bookings:', result.data?.bookings?.length);
                
                if (result.success && result.data && result.data.bookings) {
                    this.allTransactions = result.data.bookings.map(booking => 
                        this.transformBookingForDashboard(booking)
                    );
                    console.log('✅ Transactions loaded:', this.allTransactions.length);
                } else {
                    console.warn('⚠️ Invalid transactions response');
                    this.allTransactions = [];
                }
            } catch (error) {
                console.error('❌ Failed to fetch transactions:', error);
                this.allTransactions = [];
            }
        },

        transformBookingForDashboard(booking) {
            // ✅ FIXED MAPPING - Match with Model
            const serviceTypeMap = {
                'meeting': 'meeting',           // ✅ No suffix
                'private': 'private',           // ✅ No suffix
                'sharing': 'sharing',           // ✅ No suffix
                'coworking': 'coworking',       // ✅ No suffix
                'virtual': 'virtual',           // ✅ No suffix
                'event': 'event'                // ✅ No suffix
            };

            const statusMap = {
                'pending': 'pending',
                'settlement': 'settlement',
                'expire': 'expire',
                'cancel': 'expire',
                'deny': 'expire'
            };

            // ✅ DEBUG (remove after testing)
            if (this.allTransactions.length < 3) {
                console.log('🔍 Transform booking:', {
                    originalServiceType: booking.serviceType,
                    mappedServiceType: serviceTypeMap[booking.serviceType] || 'other',
                    service: booking.service
                });
            }

            return {
                id: booking.id,
                bookingId: booking.bookingId,
                customerName: booking.customerName,
                customerPhone: booking.customerPhone,
                customerEmail: booking.customerEmail,
                service: booking.service,
                serviceType: serviceTypeMap[booking.serviceType] || booking.serviceType || 'other',
                package: booking.package,
                status: statusMap[booking.paymentStatus] || booking.paymentStatus,
                time: booking.bookingTime || 'Flexible',
                bookingDate: booking.bookingDate,
                grossAmount: booking.basePrice || 0
            };
        },

        // ==========================================
        // COMPUTED PROPERTIES
        // ==========================================
        get filteredTransactions() {
            let filtered = [...this.allTransactions];

            // Filter by service tab
            if (this.activeServiceTab !== 'all') {
                filtered = filtered.filter(t => t.serviceType === this.activeServiceTab);
            }

            // Filter by status
            if (this.statusFilter !== 'all') {
                filtered = filtered.filter(t => t.status === this.statusFilter);
            }

            // Limit by entries per page
            return filtered.slice(0, parseInt(this.entriesPerPage));
        },

        get tabCounts() {
            return {
                all: this.allTransactions.length,
                meeting: this.allTransactions.filter(t => t.serviceType === 'meeting').length,
                private: this.allTransactions.filter(t => t.serviceType === 'private').length,
                sharing: this.allTransactions.filter(t => t.serviceType === 'sharing').length,
                coworking: this.allTransactions.filter(t => t.serviceType === 'coworking').length,
                virtual: this.allTransactions.filter(t => t.serviceType === 'virtual').length,
                event: this.allTransactions.filter(t => t.serviceType === 'event').length
            };
        },

        get paginationInfo() {
            const filtered = this.filteredTransactions.length;
            const showing = Math.min(parseInt(this.entriesPerPage), filtered);
            return {
                from: filtered > 0 ? 1 : 0,
                to: showing,
                total: this.allTransactions.length
            };
        },

        // ==========================================
        // UTILITY METHODS
        // ==========================================
        formatCurrency(value) {
            if (!value || value === 0) return 'Rp 0';
            
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        },

        async refreshData() {
            if (this.refreshing) return;
            
            this.refreshing = true;
            console.log('🔄 Refreshing dashboard data...');
            
            try {
                await Promise.all([
                    this.fetchDashboardStats(),
                    this.fetchAllTransactions() // ✅ Use new method
                ]);
                
                // Reload chart data
                if (this.chartInstance) {
                    await this.loadChartData();
                }
                
                console.log('✅ Dashboard refreshed successfully');
            } catch (error) {
                console.error('❌ Refresh failed:', error);
            } finally {
                this.refreshing = false;
            }
        },

        assignRoom(transaction) {
            console.log('Assign room for booking:', transaction.bookingId);
            alert(`Assign room feature coming soon!\nBooking ID: ${transaction.bookingId}`);
        },

        get filteredTransactions() {
            let filtered = [...this.allTransactions];

            // Filter by service tab
            if (this.activeServiceTab !== 'all') {
                filtered = filtered.filter(t => t.serviceType === this.activeServiceTab);
            }

            // Filter by status
            if (this.statusFilter !== 'all') {
                filtered = filtered.filter(t => t.status === this.statusFilter);
            }

            // ✅ ADD DEBUG LOG
            console.log('🔍 Filtered transactions:', {
                total: this.allTransactions.length,
                activeTab: this.activeServiceTab,
                statusFilter: this.statusFilter,
                filtered: filtered.length,
                sample: filtered[0]
            });

            // Limit by entries per page
            return filtered.slice(0, parseInt(this.entriesPerPage));
        },

        viewDetail(transaction) {
            // Redirect to All Bookings page with booking ID as query parameter
            const url = `{{ route('admin.booking.all') }}?booking_id=${transaction.id}&show_modal=true`;
            window.location.href = url;
        },
    }
}
</script>
@endpush
<style>
[x-cloak] { display: none !important; }
</style>
@endsection


