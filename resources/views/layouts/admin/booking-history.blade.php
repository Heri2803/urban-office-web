{{-- resources/views/admin/booking/history.blade.php --}}

@extends('layouts.admin')

@section('title', 'Booking History')

@section('content')
<div x-data="bookingHistory()" x-init="init()" class="space-y-6 pb-20 md:pb-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">📜 Booking History</h1>
            <p class="text-sm text-gray-500 mt-1">Complete log of all booking transactions and room assignments</p>
        </div>
        <div class="flex gap-2">
            <button @click="exportData()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium">
                📥 Export CSV
            </button>
            <button @click="refreshData()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                🔄 Refresh
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Total Records</p>
                    <p class="text-2xl font-bold text-gray-800" x-text="stats.total"></p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-xl">
                    📊
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">This Month</p>
                    <p class="text-2xl font-bold text-blue-600" x-text="stats.thisMonth"></p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-xl">
                    📅
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Completed</p>
                    <p class="text-2xl font-bold text-green-600" x-text="stats.completed"></p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center text-xl">
                    ✅
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Cancelled</p>
                    <p class="text-2xl font-bold text-red-600" x-text="stats.cancelled"></p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center text-xl">
                    ❌
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">🔍 Filters</h3>
            <button 
                @click="showFilters = !showFilters"
                class="md:hidden px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium"
            >
                <span x-text="showFilters ? 'Hide' : 'Show'"></span>
            </button>
        </div>

        <div x-show="showFilters" x-transition class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Date Range --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input type="date" x-model="filters.startDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input type="date" x-model="filters.endDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>

                {{-- Service Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Type</label>
                    <select x-model="filters.service" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">All Services</option>
                        <option value="meeting-room">Meeting Room</option>
                        <option value="private-office">Private Office</option>
                        <option value="sharing-room">Sharing Room</option>
                        <option value="virtual-office">Virtual Office</option>
                        <option value="coworking-space">Coworking Space</option>
                        <option value="event-space">Event Space</option>
                    </select>
                </div>

                {{-- Action Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Action Type</label>
                    <select x-model="filters.action" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">All Actions</option>
                        <option value="assigned">Assigned</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="released">Released</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>

            {{-- Search --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input 
                    type="text" 
                    x-model="filters.search" 
                    placeholder="Order ID, Customer Name, Room Number..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                >
            </div>

            {{-- Filter Actions --}}
            <div class="flex flex-col sm:flex-row gap-2">
                <button @click="applyFilters()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                    Apply Filters
                </button>
                <button @click="resetFilters()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                    Reset All
                </button>
            </div>
        </div>
    </div>

    {{-- History Timeline/Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        {{-- View Toggle --}}
        <div class="border-b border-gray-200 p-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h3 class="text-lg font-semibold text-gray-800">History Records</h3>
                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium" x-text="getFilteredHistory().length + ' records'"></span>
            </div>
            <div class="flex gap-2">
                <button 
                    @click="viewMode = 'timeline'"
                    :class="viewMode === 'timeline' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                    class="px-3 py-1.5 rounded-lg transition text-sm font-medium"
                >
                    📋 Timeline
                </button>
                <button 
                    @click="viewMode = 'table'"
                    :class="viewMode === 'table' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                    class="px-3 py-1.5 rounded-lg transition text-sm font-medium"
                >
                    📊 Table
                </button>
            </div>
        </div>

        {{-- Timeline View --}}
        <div x-show="viewMode === 'timeline'" x-transition class="p-4 md:p-6">
            <div class="space-y-4">
                <template x-for="(record, index) in getPaginatedHistory()" :key="record.id">
                    <div class="relative">
                        {{-- Timeline Line (except last item) --}}
                        <template x-if="index < getPaginatedHistory().length - 1">
                            <div class="absolute left-6 top-12 bottom-0 w-0.5 bg-gray-200 hidden md:block"></div>
                        </template>

                        <div class="flex gap-4">
                            {{-- Timeline Dot --}}
                            <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center font-bold text-white text-sm z-10"
                                :class="{
                                    'bg-green-500': record.action === 'assigned' || record.action === 'confirmed',
                                    'bg-blue-500': record.action === 'completed',
                                    'bg-red-500': record.action === 'released' || record.action === 'cancelled'
                                }"
                            >
                                <span x-text="getActionIcon(record.action)"></span>
                            </div>

                            {{-- Content Card --}}
                            <div class="flex-1 bg-gray-50 rounded-lg p-4 border border-gray-200 hover:shadow-md transition">
                                <div class="flex flex-col md:flex-row md:items-start justify-between gap-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span 
                                                class="px-2 py-1 rounded-full text-xs font-semibold capitalize"
                                                :class="{
                                                    'bg-green-100 text-green-700': record.action === 'assigned' || record.action === 'confirmed',
                                                    'bg-blue-100 text-blue-700': record.action === 'completed',
                                                    'bg-red-100 text-red-700': record.action === 'released' || record.action === 'cancelled'
                                                }"
                                                x-text="record.action"
                                            ></span>
                                            <span class="text-xs text-gray-500" x-text="record.timestamp"></span>
                                        </div>

                                        <h4 class="font-semibold text-gray-800 mb-1" x-text="record.title"></h4>
                                        <p class="text-sm text-gray-600 mb-2" x-text="record.description"></p>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-gray-600">
                                            <div><span class="font-medium">Order ID:</span> <span x-text="record.orderId"></span></div>
                                            <div><span class="font-medium">Customer:</span> <span x-text="record.customerName"></span></div>
                                            <div><span class="font-medium">Service:</span> <span x-text="record.serviceName"></span></div>
                                            <div x-show="record.roomNumber"><span class="font-medium">Room:</span> <span x-text="record.roomNumber"></span></div>
                                            <div><span class="font-medium">Admin:</span> <span x-text="record.adminName"></span></div>
                                            <div><span class="font-medium">Duration:</span> <span x-text="record.duration"></span></div>
                                        </div>
                                    </div>

                                    <button 
                                        @click="viewDetail(record)"
                                        class="px-4 py-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition text-sm font-medium whitespace-nowrap"
                                    >
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Empty State --}}
                <div x-show="getFilteredHistory().length === 0" class="text-center py-12">
                    <div class="text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-sm font-medium">No history records found</p>
                        <p class="text-xs mt-1">Try adjusting your filters</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table View --}}
        <div x-show="viewMode === 'table'" x-transition class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Timestamp</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Service</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Room</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Admin</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="record in getPaginatedHistory()" :key="record.id">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap" x-text="record.timestamp"></td>
                            <td class="px-4 py-3">
                                <span 
                                    class="px-2 py-1 rounded-full text-xs font-semibold capitalize whitespace-nowrap"
                                    :class="{
                                        'bg-green-100 text-green-700': record.action === 'assigned' || record.action === 'confirmed',
                                        'bg-blue-100 text-blue-700': record.action === 'completed',
                                        'bg-red-100 text-red-700': record.action === 'released' || record.action === 'cancelled'
                                    }"
                                    x-text="record.action"
                                ></span>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900" x-text="record.orderId"></td>
                            <td class="px-4 py-3 text-sm text-gray-900" x-text="record.customerName"></td>
                            <td class="px-4 py-3 text-sm text-gray-600" x-text="record.serviceName"></td>
                            <td class="px-4 py-3 text-sm text-gray-900" x-text="record.roomNumber || '-'"></td>
                            <td class="px-4 py-3 text-sm text-gray-600" x-text="record.adminName"></td>
                            <td class="px-4 py-3">
                                <button 
                                    @click="viewDetail(record)"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                >
                                    View
                                </button>
                            </td>
                        </tr>
                    </template>

                    {{-- Empty State --}}
                    <tr x-show="getFilteredHistory().length === 0">
                        <td colspan="8" class="px-4 py-12 text-center">
                            <div class="text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm font-medium">No history records found</p>
                                <p class="text-xs mt-1">Try adjusting your filters</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="border-t border-gray-200 px-4 py-3 flex flex-col md:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Per page:</label>
                <select x-model="perPage" @change="currentPage = 1" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            <div class="text-sm text-gray-600">
                Showing <span x-text="getPageStart()"></span> to <span x-text="getPageEnd()"></span> of <span x-text="getFilteredHistory().length"></span>
            </div>

            <div class="flex gap-2">
                <button 
                    @click="currentPage = Math.max(1, currentPage - 1)"
                    :disabled="currentPage === 1"
                    :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium transition"
                >
                    Previous
                </button>
                <span class="px-4 py-2 text-sm text-gray-600">
                    Page <span x-text="currentPage"></span> of <span x-text="getTotalPages()"></span>
                </span>
                <button 
                    @click="currentPage = Math.min(getTotalPages(), currentPage + 1)"
                    :disabled="currentPage === getTotalPages()"
                    :class="currentPage === getTotalPages() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium transition"
                >
                    Next
                </button>
            </div>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showModal = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full p-6 md:p-8 max-h-[90vh] overflow-y-auto">
                {{-- Close Button --}}
                <button @click="showModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <h3 class="text-xl font-bold text-gray-800 mb-6">Booking History Details</h3>
                
                <template x-if="selectedRecord">
                    <div class="space-y-6">
                        {{-- Action Badge --}}
                        <div class="flex items-center gap-3">
                            <span 
                                class="px-4 py-2 rounded-lg text-sm font-semibold capitalize"
                                :class="{
                                    'bg-green-100 text-green-700': selectedRecord.action === 'assigned' || selectedRecord.action === 'confirmed',
                                    'bg-blue-100 text-blue-700': selectedRecord.action === 'completed',
                                    'bg-red-100 text-red-700': selectedRecord.action === 'released' || selectedRecord.action === 'cancelled'
                                }"
                                x-text="selectedRecord.action"
                            ></span>
                            <span class="text-sm text-gray-500" x-text="selectedRecord.timestamp"></span>
                        </div>

                        {{-- Title & Description --}}
                        <div>
                            <h4 class="font-semibold text-lg text-gray-800 mb-2" x-text="selectedRecord.title"></h4>
                            <p class="text-gray-600" x-text="selectedRecord.description"></p>
                        </div>

                        {{-- Details Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 rounded-lg p-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Order ID</p>
                                <p class="text-sm font-medium text-gray-900" x-text="selectedRecord.orderId"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Customer Name</p>
                                <p class="text-sm font-medium text-gray-900" x-text="selectedRecord.customerName"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Email</p>
                                <p class="text-sm font-medium text-gray-900" x-text="selectedRecord.email"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Phone</p>
                                <p class="text-sm font-medium text-gray-900" x-text="selectedRecord.phone"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Service Type</p>
                                <p class="text-sm font-medium text-gray-900" x-text="selectedRecord.serviceName"></p>
                            </div>
                            <div x-show="selectedRecord.roomNumber">
                                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Room Number</p>
                                <p class="text-sm font-medium text-gray-900" x-text="selectedRecord.roomNumber"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Duration</p>
                                <p class="text-sm font-medium text-gray-900" x-text="selectedRecord.duration"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Price</p>
                                <p class="text-sm font-bold text-blue-600" x-text="'Rp ' + formatNumber(selectedRecord.price)"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Check-in Date</p>
                                <p class="text-sm font-medium text-gray-900" x-text="selectedRecord.checkIn"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Check-out Date</p>
                                <p class="text-sm font-medium text-gray-900" x-text="selectedRecord.checkOut"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Admin</p>
                                <p class="text-sm font-medium text-gray-900" x-text="selectedRecord.adminName"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Payment Status</p>
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Settlement</span>
                            </div>
                        </div>

                        {{-- Notes/Remarks --}}
                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Additional Notes</p>
                            <p class="text-sm text-gray-600" x-text="selectedRecord.notes || 'No additional notes'"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Export Toast --}}
    <div 
        x-show="showToast" 
        x-transition
        class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-4 rounded-lg shadow-xl max-w-sm"
    >
        <div class="flex items-center gap-3">
            <span class="text-2xl">✅</span>
            <div>
                <p class="font-semibold" x-text="toastMessage"></p>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function bookingHistory() {
    return {
        showFilters: true,
        showModal: false,
        showToast: false,
        toastMessage: '',
        viewMode: 'timeline',
        currentPage: 1,
        perPage: 10,
        selectedRecord: null,

        filters: {
            startDate: '',
            endDate: '',
            service: '',
            action: '',
            search: ''
        },

        stats: {
            total: 0,
            thisMonth: 0,
            completed: 0,
            cancelled: 0
        },

        history: [],

        init() {
            // Set default date range (last 30 days)
            const today = new Date();
            const lastMonth = new Date(today);
            lastMonth.setDate(lastMonth.getDate() - 30);
            
            this.filters.endDate = today.toISOString().split('T')[0];
            this.filters.startDate = lastMonth.toISOString().split('T')[0];

            this.generateDummyHistory();
            this.calculateStats();
        },

        generateDummyHistory() {
            const services = [
                { id: 'meeting-room', name: 'Meeting Room', hasRoom: true },
                { id: 'private-office', name: 'Private Office', hasRoom: true },
                { id: 'sharing-room', name: 'Sharing Room', hasRoom: true },
                { id: 'virtual-office', name: 'Virtual Office', hasRoom: false },
                { id: 'coworking-space', name: 'Coworking Space', hasRoom: false },
                { id: 'event-space', name: 'Event Space', hasRoom: false }
            ];

            const actions = [
                { type: 'assigned', title: 'Room Assigned', desc: 'Customer assigned to room' },
                { type: 'confirmed', title: 'Booking Confirmed', desc: 'Booking confirmed by admin' },
                { type: 'released', title: 'Room Released', desc: 'Room released and made available' },
                { type: 'cancelled', title: 'Booking Cancelled', desc: 'Booking cancelled by admin' },
                { type: 'completed', title: 'Booking Completed', desc: 'Customer checked out successfully' }
            ];

            const names = ['John Doe', 'Jane Smith', 'Bob Johnson', 'Alice Brown', 'Charlie Wilson', 'Emma Davis', 'Michael Lee', 'Sarah Taylor'];
            const admins = ['Admin User', 'Super Admin', 'Manager Admin'];
            const durations = ['1 Day', '3 Days', '1 Week', '2 Weeks', '1 Month'];

            this.history = [];

            // Generate 100 history records
            for (let i = 0; i < 100; i++) {
                const service = services[Math.floor(Math.random() * services.length)];
                const action = actions[Math.floor(Math.random() * actions.length)];
                const name = names[Math.floor(Math.random() * names.length)];
                const admin = admins[Math.floor(Math.random() * admins.length)];
                const duration = durations[Math.floor(Math.random() * durations.length)];
                
                const daysAgo = Math.floor(Math.random() * 60);
                const timestamp = this.getDateAgo(daysAgo);
                const checkIn = this.getDateAgo(daysAgo - 1);
                const checkOut = this.getDateAgo(daysAgo - (parseInt(duration) || 7));

                const record = {
                    id: `LOG-${1000 + i}`,
                    orderId: `ORD-${Math.floor(Math.random() * 9000) + 1000}`,
                    timestamp: timestamp,
                    action: action.type,
                    title: action.title,
                    description: action.desc,
                    customerName: name,
                    email: name.toLowerCase().replace(' ', '.') + '@example.com',
                    phone: `+62812${Math.floor(Math.random() * 90000000 + 10000000)}`,
                    service: service.id,
                    serviceName: service.name,
                    roomNumber: service.hasRoom ? (service.id === 'meeting-room' ? `20${Math.floor(Math.random() * 5) + 1}` : service.id === 'private-office' ? `30${Math.floor(Math.random() * 5) + 1}` : `30${Math.floor(Math.random() * 3) + 6}`) : null,
                    duration: duration,
                    price: Math.floor(Math.random() * 5000000 + 500000),
                    checkIn: checkIn,
                    checkOut: checkOut,
                    adminName: admin,
                    notes: Math.random() > 0.7 ? 'Customer requested early check-in' : null
                };

                this.history.push(record);
            }

            // Sort by timestamp descending
            this.history.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
        },

        getDateAgo(daysAgo) {
            const date = new Date();
            date.setDate(date.getDate() - daysAgo);
            return date.toISOString().slice(0, 16).replace('T', ' ');
        },

        calculateStats() {
            this.stats.total = this.history.length;
            
            const thisMonth = new Date();
            thisMonth.setDate(1);
            this.stats.thisMonth = this.history.filter(h => new Date(h.timestamp) >= thisMonth).length;
            
            this.stats.completed = this.history.filter(h => h.action === 'completed').length;
            this.stats.cancelled = this.history.filter(h => h.action === 'cancelled').length;
        },

        getFilteredHistory() {
            let filtered = [...this.history];

            // Date range filter
            if (this.filters.startDate) {
                filtered = filtered.filter(h => new Date(h.timestamp) >= new Date(this.filters.startDate));
            }
            if (this.filters.endDate) {
                const endDate = new Date(this.filters.endDate);
                endDate.setHours(23, 59, 59);
                filtered = filtered.filter(h => new Date(h.timestamp) <= endDate);
            }

            // Service filter
            if (this.filters.service) {
                filtered = filtered.filter(h => h.service === this.filters.service);
            }

            // Action filter
            if (this.filters.action) {
                filtered = filtered.filter(h => h.action === this.filters.action);
            }

            // Search filter
            if (this.filters.search) {
                const search = this.filters.search.toLowerCase();
                filtered = filtered.filter(h => 
                    h.orderId.toLowerCase().includes(search) ||
                    h.customerName.toLowerCase().includes(search) ||
                    (h.roomNumber && h.roomNumber.toLowerCase().includes(search)) ||
                    h.email.toLowerCase().includes(search)
                );
            }

            return filtered;
        },

        getPaginatedHistory() {
            const filtered = this.getFilteredHistory();
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + parseInt(this.perPage);
            return filtered.slice(start, end);
        },

        getTotalPages() {
            return Math.ceil(this.getFilteredHistory().length / this.perPage) || 1;
        },

        getPageStart() {
            return ((this.currentPage - 1) * this.perPage) + 1;
        },

        getPageEnd() {
            const total = this.getFilteredHistory().length;
            return Math.min(this.currentPage * this.perPage, total);
        },

        getActionIcon(action) {
            const icons = {
                'assigned': '✅',
                'confirmed': '✓',
                'released': '🔓',
                'cancelled': '✕',
                'completed': '🎉'
            };
            return icons[action] || '📋';
        },

        applyFilters() {
            this.currentPage = 1;
            this.toastMessage = 'Filters applied successfully!';
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 3000);
        },

        resetFilters() {
            const today = new Date();
            const lastMonth = new Date(today);
            lastMonth.setDate(lastMonth.getDate() - 30);
            
            this.filters = {
                startDate: lastMonth.toISOString().split('T')[0],
                endDate: today.toISOString().split('T')[0],
                service: '',
                action: '',
                search: ''
            };
            this.currentPage = 1;
        },

        refreshData() {
            this.generateDummyHistory();
            this.calculateStats();
            this.toastMessage = 'Data refreshed successfully!';
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 3000);
        },

        viewDetail(record) {
            this.selectedRecord = record;
            this.showModal = true;
        },

        exportData() {
            const data = this.getFilteredHistory();
            
            // Create CSV content
            const headers = ['Timestamp', 'Action', 'Order ID', 'Customer', 'Email', 'Service', 'Room', 'Duration', 'Price', 'Admin'];
            const rows = data.map(r => [
                r.timestamp,
                r.action,
                r.orderId,
                r.customerName,
                r.email,
                r.serviceName,
                r.roomNumber || '-',
                r.duration,
                r.price,
                r.adminName
            ]);

            let csvContent = headers.join(',') + '\n';
            rows.forEach(row => {
                csvContent += row.map(cell => `"${cell}"`).join(',') + '\n';
            });

            // Download CSV
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `booking_history_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            this.toastMessage = 'Data exported successfully!';
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 3000);
        },

        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }
    }
}
</script>
@endpush

<style>
[x-cloak] { display: none !important; }
</style>
@endsection