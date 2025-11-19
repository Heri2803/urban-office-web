@extends('layouts.superadmin')

@section('content')
<main x-data="occupancyReport()" x-init="init()" class="p-4 sm:p-6 lg:p-10 space-y-6 bg-gray-50 min-h-screen">

    {{-- Header & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800">🏢 Occupancy Report</h1>
            <p class="text-sm text-gray-500 mt-1">Analyze room utilization and maximize revenue</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button @click="exportReport('pdf')" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                PDF
            </button>
            <button @click="exportReport('excel')" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Excel
            </button>
            <button @click="printReport()" class="px-4 py-2 border border-gray-300 bg-white rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hidden sm:flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </button>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">🔍 Filters</h2>
        
        <div class="space-y-4">
            {{-- Date Range --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Report Period</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input type="date" x-model="filters.startDate" 
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <input type="date" x-model="filters.endDate" 
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            {{-- Quick Select --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quick Select</label>
                <div class="flex flex-wrap gap-2">
                    <button @click="setQuickDate('today')" 
                            class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"
                            :class="quickDateActive === 'today' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-gray-300'">
                        Today
                    </button>
                    <button @click="setQuickDate('week')" 
                            class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"
                            :class="quickDateActive === 'week' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-gray-300'">
                        This Week
                    </button>
                    <button @click="setQuickDate('month')" 
                            class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"
                            :class="quickDateActive === 'month' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-gray-300'">
                        This Month
                    </button>
                    <button @click="setQuickDate('lastMonth')" 
                            class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"
                            :class="quickDateActive === 'lastMonth' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-gray-300'">
                        Last Month
                    </button>
                    <button @click="setQuickDate('quarter')" 
                            class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"
                            :class="quickDateActive === 'quarter' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-gray-300'">
                        This Quarter
                    </button>
                </div>
            </div>

            {{-- Filters --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mitra</label>
                    <select x-model="filters.mitra" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">All Mitra</option>
                        <option value="PT SBY Office">PT SBY Office</option>
                        <option value="PT JKT Workspace">PT JKT Workspace</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                    <select x-model="filters.branch" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">All Branches</option>
                        <option value="Surabaya Center">Surabaya Center</option>
                        <option value="Jakarta Selatan">Jakarta Selatan</option>
                        <option value="Surabaya Timur">Surabaya Timur</option>
                        <option value="Jakarta Barat">Jakarta Barat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Service Type</label>
                    <select x-model="filters.service" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">All Services</option>
                        <option value="Meeting Room">Meeting Room</option>
                        <option value="Private Office">Private Office</option>
                        <option value="Sharing Room">Sharing Room</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room Number</label>
                    <select x-model="filters.roomNumber" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">All Rooms</option>
                        <template x-for="room in availableRooms" :key="room">
                            <option :value="room" x-text="room"></option>
                        </template>
                    </select>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-end space-x-3 pt-2">
                <button @click="resetFilters()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Reset
                </button>
                <button @click="generateReport()" class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                    Generate Report
                </button>
            </div>
        </div>
    </div>

    {{-- Occupancy Metrics Dashboard --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white shadow-lg">
            <p class="text-xs text-gray-500 mb-1">Overall Occupancy</p>
            <p class="text-2xl font-bold text-gray-900" x-text="metrics.overallOccupancy + '%'"></p>
            <p class="text-xs text-gray-500 mt-2">Average rate</p>
        </div>
        
        <div class="bg-white rounded-xl p-4 shadow-md border border-gray-200">
            <p class="text-xs text-gray-500 mb-1">Available Hours</p>
            <p class="text-2xl font-bold text-gray-900" x-text="metrics.availableHours.toLocaleString('id-ID')"></p>
            <p class="text-xs text-gray-500 mt-2">Total capacity</p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-md border border-gray-200">
            <p class="text-xs text-gray-500 mb-1">Booked Hours</p>
            <p class="text-2xl font-bold text-green-600" x-text="metrics.bookedHours.toLocaleString('id-ID')"></p>
            <p class="text-xs text-gray-500 mt-2">Utilized time</p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-md border border-gray-200">
            <p class="text-xs text-gray-500 mb-1">Unutilized Hours</p>
            <p class="text-2xl font-bold text-red-600" x-text="metrics.unutilizedHours.toLocaleString('id-ID')"></p>
            <p class="text-xs text-gray-500 mt-2">Idle time</p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-md border border-gray-200">
            <p class="text-xs text-gray-500 mb-1">Revenue/Hour</p>
            <p class="text-xl font-bold text-purple-600" x-text="'Rp ' + metrics.revenuePerHour.toLocaleString('id-ID')"></p>
            <p class="text-xs text-gray-500 mt-2">Average rate</p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-md border border-gray-200">
            <p class="text-xs text-gray-500 mb-1">Peak Day</p>
            <p class="text-xl font-bold text-orange-600" x-text="metrics.peakDay"></p>
            <p class="text-xs text-gray-500 mt-2" x-text="metrics.peakOccupancy + '% occupied'"></p>
        </div>
    </div>



    {{-- Occupancy by Service Type --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <template x-for="service in serviceOccupancy" :key="service.name">
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-700" x-text="service.name"></h4>
                    <div class="text-3xl" x-text="service.icon"></div>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Rooms</span>
                        <span class="font-bold text-gray-900" x-text="service.totalRooms"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Avg. Occupancy</span>
                        <span class="font-bold text-2xl" :class="service.avgOccupancy >= 80 ? 'text-green-600' : service.avgOccupancy >= 50 ? 'text-yellow-600' : 'text-red-600'" 
                              x-text="service.avgOccupancy + '%'"></span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t">
                        <span class="text-sm text-gray-600">Revenue</span>
                        <span class="font-bold text-green-600" x-text="'Rp ' + service.revenue.toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Top & Low Performers --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- High-Performing Rooms --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">🏆 High-Performing Rooms</h3>
            <div class="space-y-3">
                <template x-for="(room, index) in topPerformers" :key="room.number">
                    <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-white"
                             :class="index === 0 ? 'bg-yellow-500' : index === 1 ? 'bg-gray-400' : index === 2 ? 'bg-orange-600' : 'bg-green-500'"
                             x-text="index + 1">
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-gray-900" x-text="room.number"></p>
                            <p class="text-xs text-gray-600" x-text="room.service + ' - ' + room.branch"></p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-green-600" x-text="room.occupancy + '%'"></p>
                            <p class="text-xs text-gray-500" x-text="room.bookedHours + 'h booked'"></p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-green-600 text-sm" x-text="'Rp ' + (room.revenue / 1000) + 'k'"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Underutilized Rooms --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">⚠️ Underutilized Rooms</h3>
            <div class="space-y-3">
                <template x-for="room in lowPerformers" :key="room.number">
                    <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                        <div class="flex-1">
                            <p class="font-bold text-gray-900" x-text="room.number"></p>
                            <p class="text-xs text-gray-600" x-text="room.service + ' - ' + room.branch"></p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-red-600" x-text="room.occupancy + '%'"></p>
                            <p class="text-xs text-gray-500" x-text="room.unutilizedHours + 'h idle'"></p>
                        </div>
                        <button @click="showRecommendation(room)" class="p-2 bg-orange-100 text-orange-600 rounded-lg hover:bg-orange-200" title="View Recommendations">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Room-Level Detail --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-2 sm:space-y-0">
            <h3 class="text-lg font-semibold text-gray-700">📋 Room-Level Details (<span x-text="getFilteredRooms().length"></span>)</h3>
            <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-600">Show:</label>
                <select x-model="roomsPerPage" @change="currentPage = 1" class="text-sm border-gray-300 rounded-lg">
                    <option :value="10">10</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                </select>
                <select x-model="sortBy" @change="sortRooms()" class="text-sm border-gray-300 rounded-lg ml-2">
                    <option value="highest">Highest Occupancy</option>
                    <option value="lowest">Lowest Occupancy</option>
                    <option value="revenue">Highest Revenue</option>
                </select>
            </div>
        </div>

        {{-- Room Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="room in getCurrentPageRooms()" :key="room.number">
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow hover:border-indigo-300">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="font-bold text-indigo-600 text-lg" x-text="room.number"></p>
                            <p class="text-xs text-gray-500" x-text="room.service"></p>
                            <p class="text-xs text-gray-500" x-text="room.branch"></p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full"
                              :class="room.occupancy >= 80 ? 'bg-green-100 text-green-700' : 
                                      room.occupancy >= 50 ? 'bg-yellow-100 text-yellow-700' : 
                                      'bg-red-100 text-red-700'"
                              x-text="room.occupancy >= 80 ? 'High' : room.occupancy >= 50 ? 'Medium' : 'Low'">
                        </span>
                    </div>

                    <div class="space-y-2 text-sm mb-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Available Hours:</span>
                            <span class="font-medium text-gray-900" x-text="room.availableHours + 'h'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Booked Hours:</span>
                            <span class="font-medium text-green-600" x-text="room.bookedHours + 'h'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Unutilized Hours:</span>
                            <span class="font-medium text-red-600" x-text="room.unutilizedHours + 'h'"></span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-200 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-600">Occupancy Rate</span>
                            <span class="text-2xl font-bold" 
                                  :class="room.occupancy >= 80 ? 'text-green-600' : room.occupancy >= 50 ? 'text-yellow-600' : 'text-red-600'"
                                  x-text="room.occupancy + '%'"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Revenue</span>
                            <span class="text-lg font-bold text-green-600" x-text="'Rp ' + room.revenue.toLocaleString('id-ID')"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Empty State --}}
        <div x-show="getCurrentPageRooms().length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <p class="mt-2 text-sm text-gray-500">No rooms found for selected filters.</p>
        </div>

        {{-- Pagination --}}
        <div x-show="getCurrentPageRooms().length > 0" class="mt-6 flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0 border-t pt-4">
            <div class="text-sm text-gray-700">
                Showing <span class="font-medium" x-text="getPaginationInfo().start"></span> 
                to <span class="font-medium" x-text="getPaginationInfo().end"></span> 
                of <span class="font-medium" x-text="getPaginationInfo().total"></span> rooms
            </div>
            <div class="flex items-center space-x-2">
                <button @click="changePage(currentPage - 1)" 
                        :disabled="currentPage === 1"
                        class="px-3 py-2 border rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">
                    Previous
                </button>
                
                <div class="hidden sm:flex items-center space-x-1">
                    <template x-for="page in getPageNumbers()" :key="page">
                        <button @click="changePage(page)"
                                class="px-3 py-2 border rounded-lg text-sm"
                                :class="page === currentPage ? 'bg-indigo-600 text-white border-indigo-600' : 'hover:bg-gray-100'">
                            <span x-text="page"></span>
                        </button>
                    </template>
                </div>

                <div class="sm:hidden px-4 py-2 text-sm font-medium">
                    <span x-text="currentPage"></span> / <span x-text="getTotalPages()"></span>
                </div>
                
                <button @click="changePage(currentPage + 1)" 
                        :disabled="currentPage === getTotalPages()"
                        class="px-3 py-2 border rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">
                    Next
                </button>
            </div>
        </div>
    </div>

    {{-- Insights & Recommendations --}}
    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-700">💡 Insights & Recommendations</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- High Performers --}}
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="text-2xl">🟢</span>
                    <h4 class="font-semibold text-green-800">High Performers</h4>
                </div>
                <p class="text-2xl font-bold text-green-600 mb-1" x-text="insights.high + ' rooms'"></p>
                <p class="text-xs text-green-700">Occupancy > 80%</p>
            </div>

            {{-- Moderate --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="text-2xl">🟡</span>
                    <h4 class="font-semibold text-yellow-800">Moderate</h4>
                </div>
                <p class="text-2xl font-bold text-yellow-600 mb-1" x-text="insights.moderate + ' rooms'"></p>
                <p class="text-xs text-yellow-700">Occupancy 50-80%</p>
            </div>

            {{-- Need Attention --}}
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="text-2xl">🔴</span>
                    <h4 class="font-semibold text-red-800">Need Attention</h4>
                </div>
                <p class="text-2xl font-bold text-red-600 mb-1" x-text="insights.low + ' rooms'"></p>
                <p class="text-xs text-red-700">Occupancy < 50%</p>
            </div>

            {{-- Recommendations --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="text-2xl">💡</span>
                    <h4 class="font-semibold text-blue-800">Suggestions</h4>
                </div>
                <p class="text-2xl font-bold text-blue-600 mb-1" x-text="recommendations.length"></p>
                <button @click="showAllRecommendations()" class="text-xs text-blue-700 underline hover:text-blue-900">View all</button>
            </div>
        </div>

        {{-- Recommendation List --}}
        <div class="bg-gradient-to-br from-orange-50 to-yellow-50 border border-orange-200 rounded-xl p-4">
            <h4 class="font-semibold text-orange-900 mb-3">📌 Top Recommendations</h4>
            <div class="space-y-2">
                <template x-for="(rec, index) in recommendations.slice(0, 3)" :key="index">
                    <div class="flex items-start space-x-2 text-sm">
                        <span class="text-orange-600 mt-0.5">•</span>
                        <p class="text-gray-700" x-text="rec"></p>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Recommendation Modal --}}
    <div x-show="showRecommendationModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showRecommendationModal = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
            
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 transform transition-all">
                <div class="flex items-center justify-between border-b pb-3 mb-4">
                    <h3 class="text-xl font-bold text-gray-800">💡 Recommendations</h3>
                    <button @click="showRecommendationModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <template x-if="selectedRoom">
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="font-bold text-indigo-600" x-text="selectedRoom.number"></p>
                            <p class="text-sm text-gray-600" x-text="selectedRoom.service + ' - ' + selectedRoom.branch"></p>
                            <p class="text-sm text-red-600 mt-1">Occupancy: <span class="font-bold" x-text="selectedRoom.occupancy + '%'"></span></p>
                        </div>

                        <div class="space-y-2">
                            <h4 class="font-semibold text-gray-700">Suggested Actions:</h4>
                            <div class="space-y-2">
                                <div class="flex items-start space-x-2 p-2 bg-blue-50 rounded-lg">
                                    <span class="text-blue-600 mt-0.5">📌</span>
                                    <p class="text-sm text-gray-700">Consider promotional pricing (15-20% discount) to increase bookings</p>
                                </div>
                                <div class="flex items-start space-x-2 p-2 bg-purple-50 rounded-lg">
                                    <span class="text-purple-600 mt-0.5">🎯</span>
                                    <p class="text-sm text-gray-700">Target marketing campaigns during off-peak hours</p>
                                </div>
                                <div class="flex items-start space-x-2 p-2 bg-green-50 rounded-lg">
                                    <span class="text-green-600 mt-0.5">✨</span>
                                    <p class="text-sm text-gray-700">Review and upgrade room facilities if needed</p>
                                </div>
                                <div class="flex items-start space-x-2 p-2 bg-orange-50 rounded-lg">
                                    <span class="text-orange-600 mt-0.5">📊</span>
                                    <p class="text-sm text-gray-700">Analyze competitor pricing in the same area</p>
                                </div>
                            </div>
                        </div>

                        <button @click="showRecommendationModal = false" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                            Got it
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

</main>

<script>
    function occupancyReport() {
        return {
            quickDateActive: 'month',
            roomsPerPage: 10,
            currentPage: 1,
            sortBy: 'highest',
            showRecommendationModal: false,
            selectedRoom: null,

            filters: {
                startDate: '',
                endDate: '',
                mitra: '',
                branch: '',
                service: '',
                roomNumber: ''
            },

            availableRooms: ['MR-201', 'MR-202', 'MR-203', 'PO-301', 'PO-302', 'PO-303', 'PO-304', 'PO-305', 'SR-306', 'SR-307', 'SR-308'],

            metrics: {
                overallOccupancy: 68.5,
                availableHours: 2400,
                bookedHours: 1644,
                unutilizedHours: 756,
                revenuePerHour: 125000,
                peakDay: 'Monday',
                peakOccupancy: 85
            },

            trendData: [],
            roomTypeComparison: [
                { name: 'Meeting Room', occupancy: 75, rooms: 5, color: '#4f46e5', colorDark: '#4338ca' },
                { name: 'Private Office', occupancy: 68, rooms: 5, color: '#0ea5e9', colorDark: '#0284c7' },
                { name: 'Sharing Room', occupancy: 60, rooms: 3, color: '#8b5cf6', colorDark: '#7c3aed' }
            ],

            serviceOccupancy: [
                { name: 'Meeting Room', icon: '📝', totalRooms: 5, avgOccupancy: 75, revenue: 18500000 },
                { name: 'Private Office', icon: '💼', totalRooms: 5, avgOccupancy: 68, revenue: 32400000 },
                { name: 'Sharing Room', icon: '👥', totalRooms: 3, avgOccupancy: 60, revenue: 12800000 }
            ],

            topPerformers: [],
            lowPerformers: [],
            allRooms: [],

            insights: {
                high: 0,
                moderate: 0,
                low: 0
            },

            recommendations: [
                'Consider promotional pricing for rooms with <50% occupancy',
                'Peak hours detected: 9AM-12PM. Adjust pricing strategy?',
                'Room MR-203 consistently underbooked. Review facilities & location',
                'High-performing rooms (MR-201, PO-301) can support premium pricing',
                'Weekend occupancy lower by 25%. Introduce weekend packages'
            ],

            init() {
                this.setQuickDate('month');
                this.generateTrendData();
                this.generateRoomData();
                this.calculateInsights();
            },

            setQuickDate(period) {
                this.quickDateActive = period;
                const today = new Date();
                const endDate = today.toISOString().split('T')[0];

                let startDate;
                switch(period) {
                    case 'today':
                        startDate = endDate;
                        break;
                    case 'week':
                        const weekAgo = new Date(today);
                        weekAgo.setDate(weekAgo.getDate() - 7);
                        startDate = weekAgo.toISOString().split('T')[0];
                        break;
                    case 'month':
                        const monthAgo = new Date(today.getFullYear(), today.getMonth(), 1);
                        startDate = monthAgo.toISOString().split('T')[0];
                        break;
                    case 'lastMonth':
                        const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                        startDate = lastMonth.toISOString().split('T')[0];
                        const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
                        this.filters.endDate = lastMonthEnd.toISOString().split('T')[0];
                        this.filters.startDate = startDate;
                        return;
                    case 'quarter':
                        const quarter = Math.floor(today.getMonth() / 3);
                        const quarterStart = new Date(today.getFullYear(), quarter * 3, 1);
                        startDate = quarterStart.toISOString().split('T')[0];
                        break;
                }

                this.filters.startDate = startDate;
                this.filters.endDate = endDate;
            },

            generateTrendData() {
                this.trendData = [];
                const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                days.forEach(day => {
                    this.trendData.push({
                        label: day,
                        occupancy: Math.floor(Math.random() * 40) + 50 // 50-90%
                    });
                });
            },

            generateRoomData() {
                const services = ['Meeting Room', 'Private Office', 'Sharing Room'];
                const branches = ['Surabaya Center', 'Jakarta Selatan', 'Surabaya Timur'];
                const roomNumbers = this.availableRooms;

                this.allRooms = [];
                roomNumbers.forEach(number => {
                    const occupancy = Math.floor(Math.random() * 80) + 20; // 20-100%
                    const availableHours = 240; // 10 hours x 24 days
                    const bookedHours = Math.floor(availableHours * occupancy / 100);
                    
                    this.allRooms.push({
                        number: number,
                        service: number.startsWith('MR') ? 'Meeting Room' : number.startsWith('PO') ? 'Private Office' : 'Sharing Room',
                        branch: branches[Math.floor(Math.random() * branches.length)],
                        availableHours: availableHours,
                        bookedHours: bookedHours,
                        unutilizedHours: availableHours - bookedHours,
                        occupancy: occupancy,
                        revenue: bookedHours * (Math.floor(Math.random() * 50000) + 100000)
                    });
                });

                this.sortRooms();
                this.updatePerformers();
            },

            calculateInsights() {
                this.insights.high = this.allRooms.filter(r => r.occupancy >= 80).length;
                this.insights.moderate = this.allRooms.filter(r => r.occupancy >= 50 && r.occupancy < 80).length;
                this.insights.low = this.allRooms.filter(r => r.occupancy < 50).length;
            },

            updatePerformers() {
                const sorted = [...this.allRooms].sort((a, b) => b.occupancy - a.occupancy);
                this.topPerformers = sorted.slice(0, 5);
                this.lowPerformers = sorted.slice(-5).reverse();
            },

            resetFilters() {
                this.filters = {
                    startDate: this.filters.startDate,
                    endDate: this.filters.endDate,
                    mitra: '',
                    branch: '',
                    service: '',
                    roomNumber: ''
                };
                this.currentPage = 1;
            },

            generateReport() {
                alert('Generating occupancy report...\n\nPeriod: ' + this.filters.startDate + ' to ' + this.filters.endDate);
                this.generateTrendData();
            },

            sortRooms() {
                switch(this.sortBy) {
                    case 'highest':
                        this.allRooms.sort((a, b) => b.occupancy - a.occupancy);
                        break;
                    case 'lowest':
                        this.allRooms.sort((a, b) => a.occupancy - b.occupancy);
                        break;
                    case 'revenue':
                        this.allRooms.sort((a, b) => b.revenue - a.revenue);
                        break;
                }
                this.currentPage = 1;
            },

            getFilteredRooms() {
                let filtered = this.allRooms;

                if (this.filters.branch) {
                    filtered = filtered.filter(r => r.branch === this.filters.branch);
                }
                if (this.filters.service) {
                    filtered = filtered.filter(r => r.service === this.filters.service);
                }
                if (this.filters.roomNumber) {
                    filtered = filtered.filter(r => r.number === this.filters.roomNumber);
                }

                return filtered;
            },

            getCurrentPageRooms() {
                const filtered = this.getFilteredRooms();
                const start = (this.currentPage - 1) * this.roomsPerPage;
                const end = start + parseInt(this.roomsPerPage);
                return filtered.slice(start, end);
            },

            getTotalPages() {
                const filtered = this.getFilteredRooms();
                return Math.ceil(filtered.length / this.roomsPerPage) || 1;
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
                const filtered = this.getFilteredRooms();
                const start = (this.currentPage - 1) * this.roomsPerPage + 1;
                const end = Math.min(start + parseInt(this.roomsPerPage) - 1, filtered.length);
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

            showRecommendation(room) {
                this.selectedRoom = room;
                this.showRecommendationModal = true;
            },

            showAllRecommendations() {
                alert('All Recommendations:\n\n' + this.recommendations.join('\n\n'));
            },

            exportReport(format) {
                alert(`Exporting occupancy report as ${format.toUpperCase()}...\n\nFilters:\nPeriod: ${this.filters.startDate} to ${this.filters.endDate}\nBranch: ${this.filters.branch || 'All'}\nService: ${this.filters.service || 'All'}`);
            },

            printReport() {
                alert('Printing occupancy report...');
                window.print();
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection