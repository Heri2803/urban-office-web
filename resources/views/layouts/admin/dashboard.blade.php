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
            <a href="#" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
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

    {{-- Real-time Room Status --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Real-time Room Status</h2>
                <p class="text-sm text-gray-600 mt-1">Current status of all rooms across services</p>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <span class="text-gray-600">Available</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                    <span class="text-gray-600">Occupied</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    <span class="text-gray-600">Booked</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                    <span class="text-gray-600">Cleaning</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-gray-500 rounded-full"></div>
                    <span class="text-gray-600">Maintenance</span>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            {{-- Meeting Rooms --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Meeting Rooms</h3>
                <div class="flex flex-wrap gap-2">
                    <template x-for="room in roomStatus.meetingRooms" :key="room.number">
                        <button @click="showRoomDetail(room)" :class="{
                            'bg-green-100 border-green-500 text-green-700': room.status === 'available',
                            'bg-red-100 border-red-500 text-red-700': room.status === 'occupied',
                            'bg-yellow-100 border-yellow-500 text-yellow-700': room.status === 'booked',
                            'bg-purple-100 border-purple-500 text-purple-700': room.status === 'cleaning',
                            'bg-gray-100 border-gray-500 text-gray-700': room.status === 'maintenance'
                        }" class="px-4 py-2 rounded-lg border-2 text-sm font-medium hover:shadow-md transition cursor-pointer">
                            <span x-text="room.number"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Private Office --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Private Office</h3>
                <div class="flex flex-wrap gap-2">
                    <template x-for="room in roomStatus.privateOffice" :key="room.number">
                        <button @click="showRoomDetail(room)" :class="{
                            'bg-green-100 border-green-500 text-green-700': room.status === 'available',
                            'bg-red-100 border-red-500 text-red-700': room.status === 'occupied',
                            'bg-yellow-100 border-yellow-500 text-yellow-700': room.status === 'booked',
                            'bg-purple-100 border-purple-500 text-purple-700': room.status === 'cleaning',
                            'bg-gray-100 border-gray-500 text-gray-700': room.status === 'maintenance'
                        }" class="px-4 py-2 rounded-lg border-2 text-sm font-medium hover:shadow-md transition cursor-pointer">
                            <span x-text="room.number"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Sharing Room --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Sharing Room</h3>
                <div class="flex flex-wrap gap-2">
                    <template x-for="room in roomStatus.sharingRoom" :key="room.number">
                        <button @click="showRoomDetail(room)" :class="{
                            'bg-green-100 border-green-500 text-green-700': room.status === 'available',
                            'bg-red-100 border-red-500 text-red-700': room.status === 'occupied',
                            'bg-yellow-100 border-yellow-500 text-yellow-700': room.status === 'booked',
                            'bg-purple-100 border-purple-500 text-purple-700': room.status === 'cleaning',
                            'bg-gray-100 border-gray-500 text-gray-700': room.status === 'maintenance'
                        }" class="px-4 py-2 rounded-lg border-2 text-sm font-medium hover:shadow-md transition cursor-pointer">
                            <span x-text="room.number"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Recent Transactions Today</h2>
                <p class="text-sm text-gray-600 mt-1">Latest bookings and their payment status</p>
            </div>
        </div>

        {{-- Service Tabs --}}
        <div class="border-b border-gray-200 mb-4">
            <nav class="flex gap-6 overflow-x-auto whitespace-nowrap">
                <button @click="activeServiceTab = 'all'" :class="activeServiceTab === 'all' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800'" class="py-3 px-1 border-b-2 font-medium text-sm transition">
                    Semua (25)
                </button>
                <button @click="activeServiceTab = 'meeting'" :class="activeServiceTab === 'meeting' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800'" class="py-3 px-1 border-b-2 font-medium text-sm transition">
                    Meeting Room (8)
                </button>
                <button @click="activeServiceTab = 'private'" :class="activeServiceTab === 'private' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800'" class="py-3 px-1 border-b-2 font-medium text-sm transition">
                    Private Office (4)
                </button>
                <button @click="activeServiceTab = 'sharing'" :class="activeServiceTab === 'sharing' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800'" class="py-3 px-1 border-b-2 font-medium text-sm transition">
                    Sharing Room (3)
                </button>
                <button @click="activeServiceTab = 'coworking'" :class="activeServiceTab === 'coworking' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800'" class="py-3 px-1 border-b-2 font-medium text-sm transition">
                    Coworking (5)
                </button>
                <button @click="activeServiceTab = 'virtual'" :class="activeServiceTab === 'virtual' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800'" class="py-3 px-1 border-b-2 font-medium text-sm transition">
                    Virtual Office (3)
                </button>
                <button @click="activeServiceTab = 'event'" :class="activeServiceTab === 'event' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800'" class="py-3 px-1 border-b-2 font-medium text-sm transition">
                    Event Space (2)
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
                    <button @click="statusFilter = 'all'" :class="statusFilter === 'all' ? 'bg-white shadow-sm' : ''" class="px-3 py-1.5 text-xs rounded-md transition">
                        All
                    </button>
                    <button @click="statusFilter = 'settlement'" :class="statusFilter === 'settlement' ? 'bg-white shadow-sm' : ''" class="px-3 py-1.5 text-xs rounded-md transition">
                        Settlement
                    </button>
                    <button @click="statusFilter = 'pending'" :class="statusFilter === 'pending' ? 'bg-white shadow-sm' : ''" class="px-3 py-1.5 text-xs rounded-md transition">
                        Pending
                    </button>
                    <button @click="statusFilter = 'expired'" :class="statusFilter === 'expired' ? 'bg-white shadow-sm' : ''" class="px-3 py-1.5 text-xs rounded-md transition">
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
                    <template x-for="(transaction, index) in filteredTransactions" :key="index">
                        <tr class="hover:bg-gray-50 transition">
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
                                <span x-show="transaction.status === 'expired'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
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
                                <button x-show="transaction.status === 'settlement'" @click="assignRoom(transaction)" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition">
                                    Assign Room
                                </button>
                                <button x-show="transaction.status !== 'settlement'" class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs rounded-lg hover:bg-gray-200 transition">
                                    View Detail
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">Showing 1 to 5 of 25 entries</p>
            <div class="flex gap-2">
                <button class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition">
                    1
                </button>
                <button class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition">
                    2
                </button>
                <button class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition">
                    3
                </button>
                <button class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition">
                    Next
                </button>
            </div>
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
            totalBookingToday: 25,
            revenueToday: 4500000,
            pendingConfirmation: 5,
            roomsOccupied: 12,
            totalRooms: 16,
            occupancyRate: 75
        },

        // Chart Data
        chartMetric: 'revenue',
        chartPeriod: 'daily',
        transactionChart: null,

        // Room Status
        roomStatus: {
            meetingRooms: [
                { number: '201', status: 'occupied', capacity: '10 pax', facilities: ['Proyektor', 'Whiteboard', 'AC'], currentBooking: { customer: 'Budi Santoso', time: '09:00-11:00', remaining: '45 mins' } },
                { number: '202', status: 'available', capacity: '6 pax', facilities: ['Proyektor', 'Flipchart'] },
                { number: '203', status: 'booked', capacity: '20 pax', facilities: ['LED TV', 'Sound System'], nextBooking: { customer: 'Ani Wijaya', time: '14:00-16:00' } },
                { number: '204', status: 'cleaning', capacity: '8 pax', facilities: ['Proyektor', 'Whiteboard'] },
                { number: '205', status: 'maintenance', capacity: '12 pax', facilities: ['Proyektor', 'AC'] }
            ],
            privateOffice: [
                { number: '301', status: 'occupied', capacity: '4 pax', facilities: ['Desk', 'Chair', 'Cabinet'] },
                { number: '302', status: 'occupied', capacity: '6 pax', facilities: ['Desk', 'Chair', 'Cabinet'] },
                { number: '303', status: 'available', capacity: '4 pax', facilities: ['Desk', 'Chair'] },
                { number: '304', status: 'booked', capacity: '8 pax', facilities: ['Desk', 'Chair', 'Cabinet'] },
                { number: '305', status: 'available', capacity: '4 pax', facilities: ['Desk', 'Chair'] }
            ],
            sharingRoom: [
                { number: '306', status: 'occupied', capacity: '12 pax', facilities: ['Desks', 'Chairs', 'Lockers'] },
                { number: '307', status: 'cleaning', capacity: '15 pax', facilities: ['Desks', 'Chairs', 'Lockers'] },
                { number: '308', status: 'available', capacity: '10 pax', facilities: ['Desks', 'Chairs'] }
            ]
        },

        // Transactions
        activeServiceTab: 'all',
        statusFilter: 'all',
        entriesPerPage: 5,
        transactions: [
            { bookingId: '#MR-089', customerName: 'Budi Santoso', customerPhone: '0812-3456-7890', service: 'Meeting Room', package: '2 Jam', status: 'settlement', time: '08:30 WIB', serviceType: 'meeting' },
            { bookingId: '#PO-045', customerName: 'Ani Wijaya', customerPhone: '0813-5678-9012', service: 'Private Office', package: 'Monthly', status: 'settlement', time: '08:15 WIB', serviceType: 'private' },
            { bookingId: '#MR-090', customerName: 'Siti Rahayu', customerPhone: '0814-6789-0123', service: 'Meeting Room', package: '4 Jam', status: 'pending', time: '07:45 WIB', serviceType: 'meeting' },
            { bookingId: '#CW-012', customerName: 'Joko Prasetyo', customerPhone: '0815-7890-1234', service: 'Coworking Space', package: 'Day Pass', status: 'settlement', time: '07:30 WIB', serviceType: 'coworking' },
            { bookingId: '#VO-008', customerName: 'Dewi Kusuma', customerPhone: '0816-8901-2345', service: 'Virtual Office', package: 'Yearly', status: 'settlement', time: '07:00 WIB', serviceType: 'virtual' },
            { bookingId: '#MR-091', customerName: 'Ahmad Fauzi', customerPhone: '0817-9012-3456', service: 'Meeting Room', package: '1 Jam', status: 'expired', time: '06:45 WIB', serviceType: 'meeting' },
            { bookingId: '#SR-023', customerName: 'Linda Permata', customerPhone: '0818-0123-4567', service: 'Sharing Room', package: 'Monthly', status: 'settlement', time: '06:30 WIB', serviceType: 'sharing' },
            { bookingId: '#ES-005', customerName: 'Ridwan Kamil', customerPhone: '0819-1234-5678', service: 'Event Space', package: 'Full Day', status: 'settlement', time: '06:00 WIB', serviceType: 'event' }
        ],

        // Modal
        showRoomModal: false,
        selectedRoom: null,

        // Initialize
        init() {
            this.initChart();
        },

        // Chart Methods
        initChart() {
            const ctx = document.getElementById('transactionChart');
            if (!ctx) return;

            this.transactionChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['03 Oct', '04 Oct', '05 Oct', '06 Oct', '07 Oct', '08 Oct', '09 Oct'],
                    datasets: [{
                        label: 'Revenue (Rp)',
                        data: [3200000, 4100000, 3800000, 4500000, 3900000, 4200000, 4500000],
                        borderColor: 'rgb(37, 99, 235)',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value / 1000000) + 'jt';
                                }
                            }
                        }
                    }
                }
            });
        },

        updateChart() {
            if (!this.transactionChart) return;

            let labels, data;
            
            if (this.chartPeriod === 'daily') {
                labels = ['03 Oct', '04 Oct', '05 Oct', '06 Oct', '07 Oct', '08 Oct', '09 Oct'];
                data = this.chartMetric === 'revenue' 
                    ? [3200000, 4100000, 3800000, 4500000, 3900000, 4200000, 4500000]
                    : [18, 23, 21, 25, 22, 24, 25];
            } else if (this.chartPeriod === 'monthly') {
                labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                data = this.chartMetric === 'revenue'
                    ? [85000000, 92000000, 88000000, 95000000, 90000000, 98000000, 93000000, 100000000, 96000000, 105000000, 0, 0]
                    : [520, 580, 550, 600, 570, 620, 590, 650, 610, 680, 0, 0];
            } else {
                labels = ['2023', '2024', '2025'];
                data = this.chartMetric === 'revenue'
                    ? [980000000, 1150000000, 950000000]
                    : [6500, 7200, 6800];
            }

            this.transactionChart.data.labels = labels;
            this.transactionChart.data.datasets[0].data = data;
            this.transactionChart.data.datasets[0].label = this.chartMetric === 'revenue' ? 'Revenue (Rp)' : 'Booking Count';
            this.transactionChart.update();
        },

        // Computed
        get filteredTransactions() {
            let filtered = this.transactions;

            // Filter by service tab
            if (this.activeServiceTab !== 'all') {
                filtered = filtered.filter(t => t.serviceType === this.activeServiceTab);
            }

            // Filter by status
            if (this.statusFilter !== 'all') {
                filtered = filtered.filter(t => t.status === this.statusFilter);
            }

            return filtered.slice(0, this.entriesPerPage);
        },

        // Methods
        formatCurrency(value) {
            return 'Rp ' + value.toLocaleString('id-ID');
        },

        refreshData() {
            alert('Refreshing data...');
        },

        showRoomDetail(room) {
            this.selectedRoom = room;
            this.showRoomModal = true;
        },

        assignRoom(transaction) {
            alert('Assigning room for ' + transaction.bookingId);
            // Redirect to room assignment page
        }
    }
}
</script>
@endpush
<style>
[x-cloak] { display: none !important; }
</style>
@endsection


