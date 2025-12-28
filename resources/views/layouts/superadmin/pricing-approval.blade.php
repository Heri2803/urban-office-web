{{-- resources/views/superadmin/pricing/approval-requests.blade.php --}}
@extends('layouts.superadmin')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl" 
     x-data="pricingApproval()" 
     x-init="init()"
     @keydown.escape.window="closeAllModals()">
    
    {{-- Loading Overlay --}}
    <div x-show="isLoading" 
         x-transition
         class="fixed inset-0 bg-white bg-opacity-75 z-50 flex items-center justify-center"
         x-cloak>
        <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <p class="mt-4 text-gray-600">Loading pricing requests...</p>
        </div>
    </div>

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">✅ Pricing Approval Requests</h1>
                <p class="text-gray-600 mt-1 text-sm md:text-base">Review and approve branch pricing changes</p>
            </div>
            <div class="flex gap-2">
                <!-- <button @click="bulkApprove()" 
                        :disabled="selectedRequests.length === 0 || isLoading"
                        :class="selectedRequests.length === 0 || isLoading ? 'bg-green-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700'"
                        class="px-4 py-2 text-white rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span x-text="selectedRequests.length > 0 ? `Approve (${selectedRequests.length})` : 'Approve Selected'"></span>
                </button>
                <button @click="bulkReject()" 
                        :disabled="selectedRequests.length === 0 || isLoading"
                        :class="selectedRequests.length === 0 || isLoading ? 'bg-red-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700'"
                        class="px-4 py-2 text-white rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span x-text="selectedRequests.length > 0 ? `Reject (${selectedRequests.length})` : 'Reject Selected'"></span>
                </button> -->
            </div>
        </div>
    </div>

    {{-- Quick Filter Tabs --}}
    <div class="bg-white rounded-lg shadow-sm p-3 mb-6">
        <div class="flex flex-wrap gap-2">
            <button @click="filterByStatus('all')" 
                    :class="currentFilter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                All (<span x-text="stats.all">0</span>)
            </button>
            <button @click="filterByStatus('pending')" 
                    :class="currentFilter === 'pending' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Pending (<span x-text="stats.pending">0</span>)
            </button>
            <button @click="filterByStatus('active')" 
                    :class="currentFilter === 'active' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Approved (<span x-text="stats.active">0</span>)
            </button>
            <button @click="filterByStatus('rejected')" 
                    :class="currentFilter === 'rejected' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Rejected (<span x-text="stats.rejected">0</span>)
            </button>
        </div>
    </div>

    {{-- Advanced Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            {{-- Branch Filter --}}
            <select x-model="filters.branch" 
                    @change="applyFilters()" 
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Branches</option>
                <template x-for="branch in branches" :key="branch.id">
                    <option :value="branch.id" x-text="branch.name"></option>
                </template>
            </select>
            
            {{-- Service Filter --}}
            <select x-model="filters.service" 
                    @change="applyFilters()" 
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Services</option>
                <template x-for="service in services" :key="service.id">
                    <option :value="service.id" x-text="service.name"></option>
                </template>
            </select>
            
            {{-- Date Range Filter --}}
            <select x-model="filters.dateRange" 
                    @change="applyFilters()" 
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="all">All Time</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
            </select>
            
            <button @click="resetFilters()" 
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                Reset Filters
            </button>
        </div>
    </div>

    {{-- Requests Table/Cards --}}
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">
                <span x-text="getFilterTitle()"></span> Requests (<span x-text="filteredRequests.length"></span>)
            </h2>
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden lg:block overflow-x-auto" x-show="!isLoading && filteredRequests.length > 0">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left w-12">
                            <template x-if="currentFilter === 'pending'">
                                <input type="checkbox" 
                                       @change="toggleSelectAll()"
                                       :checked="selectedRequests.length === filteredRequests.length && filteredRequests.length > 0"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </template>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price Change</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Impact</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="request in filteredRequests" :key="request.id">
                        <tr class="hover:bg-gray-50 transition-colors"
                            :class="{'bg-blue-50': selectedRequests.includes(request.id)}">
                            <td class="px-4 py-3">
                                <template x-if="currentFilter === 'pending'">
                                    <input type="checkbox" 
                                           @change="toggleSelectRequest(request.id)"
                                           :checked="selectedRequests.includes(request.id)"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </template>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <span x-text="formatDate(request.date)"></span><br>
                                <span class="text-xs text-gray-500" x-text="request.time"></span>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900" x-text="request.branchName"></td>
                            <td class="px-4 py-3 text-sm text-gray-700" x-text="request.serviceName"></td>
                            <td class="px-4 py-3 text-sm text-gray-700" x-text="request.packageName"></td>
                            <td class="px-4 py-3 text-sm">
                                <div>Rp <span x-text="formatCurrency(request.masterPrice)"></span> →</div>
                                <div class="font-semibold">Rp <span x-text="formatCurrency(request.requestedPrice)"></span></div>
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                <span :class="getImpactClass(request.difference)" 
                                      class="font-semibold">
                                    <span x-text="getImpactIcon(request.difference)"></span>
                                    <span x-text="getImpactText(request.difference)"></span>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <template x-if="request.status === 'pending'">
                                    <button @click="openReviewModal(request)" 
                                            class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                        Review
                                    </button>
                                </template>
                                <template x-if="request.status === 'active'">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">✅ Approved</span>
                                </template>
                                <template x-if="request.status === 'rejected'">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">❌ Rejected</span>
                                </template>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Mobile/Tablet Card View --}}
        <div class="lg:hidden space-y-4" x-show="!isLoading && filteredRequests.length > 0">
            <template x-for="request in filteredRequests" :key="request.id">
                <div class="bg-white border border-gray-200 rounded-lg p-4"
                     :class="{'border-blue-500 bg-blue-50': selectedRequests.includes(request.id)}">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-start gap-3 flex-1">
                            <template x-if="currentFilter === 'pending'">
                                <input type="checkbox" 
                                       @change="toggleSelectRequest(request.id)"
                                       :checked="selectedRequests.includes(request.id)"
                                       class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </template>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900" x-text="request.branchName + ' - ' + request.serviceName"></h3>
                                <p class="text-sm text-gray-600 mt-1" x-text="request.packageName"></p>
                            </div>
                        </div>
                        <span :class="getImpactClass(request.difference)" 
                              class="font-bold text-lg"
                              x-text="getImpactIcon(request.difference)"></span>
                    </div>
                    
                    <div class="space-y-2 mb-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Date:</span>
                            <span class="text-gray-700" x-text="formatDate(request.date) + ' ' + request.time"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Current Price:</span>
                            <span class="text-gray-900">Rp <span x-text="formatCurrency(request.masterPrice)"></span></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Requested Price:</span>
                            <span class="font-semibold text-gray-900">Rp <span x-text="formatCurrency(request.requestedPrice)"></span></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Change:</span>
                            <span :class="getImpactClass(request.difference)" 
                                  class="font-semibold"
                                  x-text="getImpactText(request.difference)"></span>
                        </div>
                    </div>
                    
                    <template x-if="request.status === 'pending'">
                        <button @click="openReviewModal(request)" 
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                            Review Request
                        </button>
                    </template>
                    <template x-if="request.status === 'active'">
                        <div class="text-center">
                            <span class="px-3 py-1.5 inline-flex text-xs font-medium rounded-full bg-green-100 text-green-800">
                                ✅ Approved by <span x-text="request.approvedBy"></span>
                            </span>
                        </div>
                    </template>
                    <template x-if="request.status === 'rejected'">
                        <div class="text-center">
                            <span class="px-3 py-1.5 inline-flex text-xs font-medium rounded-full bg-red-100 text-red-800">
                                ❌ Rejected by <span x-text="request.rejectedBy"></span>
                            </span>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        {{-- Loading State --}}
        <div x-show="isLoading && filteredRequests.length === 0" class="text-center py-12">
            <svg class="w-12 h-12 text-gray-300 animate-spin mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-500">Loading requests...</p>
        </div>

        {{-- Empty State --}}
        <div x-show="!isLoading && filteredRequests.length === 0" class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500 text-lg font-medium">No requests found</p>
            <p class="text-gray-400 text-sm mt-2">All pricing requests have been processed</p>
        </div>
    </div>

    {{-- Review Request Modal --}}
    <div x-show="showReviewModal" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-cloak
         x-transition>
        <div class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
            <div @click="closeReviewModal()" 
                 class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-3xl p-6 sm:p-8 my-8 max-h-[90vh] overflow-y-auto transform transition-all">
                <div class="flex items-center justify-between border-b pb-3 mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Review Pricing Request</h3>
                    <button @click="closeReviewModal()" 
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div x-html="reviewContent"></div>
            </div>
        </div>
    </div>

    {{-- Bulk Action Confirmation Modal --}}
    <div x-show="showBulkConfirmModal" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="closeBulkConfirmModal()" 
                 class="fixed inset-0 bg-black bg-opacity-50"></div>
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4"
                         :class="bulkActionType === 'approve' ? 'text-green-500' : 'text-red-500'">
                        <svg class="w-full h-full" fill="currentColor" viewBox="0 0 20 20">
                            <template x-if="bulkActionType === 'approve'">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </template>
                            <template x-if="bulkActionType === 'reject'">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </template>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2" x-text="bulkActionType === 'approve' ? 'Approve Multiple Requests?' : 'Reject Multiple Requests?'"></h3>
                    <p class="text-sm text-gray-600 mb-6" 
                       x-text="bulkActionType === 'approve' ? 
                               `Are you sure you want to approve ${selectedRequests.length} pricing request(s)?` :
                               `Are you sure you want to reject ${selectedRequests.length} pricing request(s)?`"></p>
                    <div class="flex gap-3">
                        <button @click="closeBulkConfirmModal()" 
                                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50">
                            Cancel
                        </button>
                        <button @click="confirmBulkAction()" 
                                :class="bulkActionType === 'approve' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'"
                                class="flex-1 px-4 py-2 rounded-lg font-medium text-white">
                            <span x-text="bulkActionType === 'approve' ? 'Approve All' : 'Reject All'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('pricingApproval', () => ({
        // ============================================
        // STATE
        // ============================================
        requests: [],
        branches: [], // Akan diisi dengan lokasi/room info
        services: [], // Akan diisi dengan room_type/category
        isLoading: false,
        currentFilter: 'all',
        selectedRequests: [],
        
        filters: {
            branch: '',
            service: '',
            dateRange: 'all'
        },
        
        stats: { all: 0, pending: 0, active: 0, rejected: 0 },

        // Modal states
        showReviewModal: false,
        showBulkConfirmModal: false,
        bulkActionType: '',
        currentReviewRequest: null,
        reviewContent: '',

        pagination: { total: 0, per_page: 20, current_page: 1, last_page: 1 },
        
        filterTimeout: null,
        
        // ============================================
        // COMPUTED PROPERTIES - TRANSFORM DATA
        // ============================================
        get filteredRequests() {
            const { branch, service, dateRange } = this.filters;
            
            return this.requests.filter(req => {
                // Transform status: API 'active'/'inactive'/'rejected', Blade 'pending'/'active'/'rejected'
                const status = this.transformStatus(req.status);
                
                // Status filter
                if (this.currentFilter !== 'all' && status !== this.currentFilter) {
                    return false;
                }
                
                // Branch filter (gunakan location atau room info)
                if (branch) {
                    const branchId = req.location_id || (req.room ? req.room.id : null);
                    if (!branchId || branchId != branch) {
                        return false;
                    }
                }
                
                // Service filter (gunakan room_type atau category)
                if (service) {
                    const serviceId = req.room_type_id || req.category_id;
                    if (!serviceId || serviceId != service) {
                        return false;
                    }
                }
                
                // Date range filter
                if (dateRange !== 'all') {
                    const dateToCheck = req.status_info?.reviewed_at || req.requested_at || req.created_at;
                    if (!dateToCheck) return false;
                    
                    const checkDate = new Date(dateToCheck);
                    const today = new Date();
                    
                    switch(dateRange) {
                        case 'today':
                            return checkDate.toDateString() === today.toDateString();
                        case 'week':
                            const weekAgo = new Date(today);
                            weekAgo.setDate(weekAgo.getDate() - 7);
                            return checkDate >= weekAgo;
                        case 'month':
                            const monthAgo = new Date(today);
                            monthAgo.setMonth(monthAgo.getMonth() - 1);
                            return checkDate >= monthAgo;
                        default:
                            return true;
                    }
                }
                
                return true;
            }).map(req => this.transformRequestData(req)); // Transform setiap request
        },
        
        // ============================================
        // DATA TRANSFORMATION METHODS
        // ============================================
        transformStatus(apiStatus) {
            // Ubah status API ke format Blade
            switch(apiStatus) {
                case 'pending': return 'pending';
                case 'active': return 'active';
                case 'inactive': return 'rejected'; // Atau sesuaikan dengan kebutuhan
                case 'rejected': return 'rejected';
                default: return apiStatus;
            }
        },
        
        transformRequestData(apiData) {
            // Transform data API ke format Blade template
            return {
                id: apiData.id,
                // Untuk branchName: gunakan location name atau room info
                branchName: apiData.location?.name || 
                           `Room ${apiData.room?.room_number || 'N/A'}` || 
                           'Unknown Branch',
                branch_id: apiData.location_id || apiData.room?.id,
                
                // Untuk serviceName: gunakan room_type + category
                serviceName: `${apiData.room_type}${apiData.category ? ' - ' + apiData.category : ''}`,
                service_id: apiData.room_type_id || apiData.category_id,
                
                // Untuk packageName: duration display + category
                packageName: `${apiData.duration_display}${apiData.category ? ' (' + apiData.category + ')' : ''}`,
                
                // Pricing data
                masterPrice: apiData.previous_price || 0,
                requestedPrice: apiData.price || 0,
                difference: apiData.price_change_percent || 0,
                
                // Date/time data
                date: apiData.requested_at || apiData.status_info?.reviewed_at || apiData.created_at,
                time: this.formatTime(apiData.requested_at || apiData.status_info?.reviewed_at || apiData.created_at),
                
                // Status data
                status: this.transformStatus(apiData.status),
                approvedBy: apiData.status_info?.reviewed_by || 'System',
                rejectedBy: apiData.status_info?.reviewed_by || 'System',
                reason: apiData.reason || 'No reason provided',
                requested_by: apiData.requested_by || 'System',
                
                // Original API data (untuk reference)
                original: apiData
            };
        },
        
        // ============================================
        // LIFECYCLE METHODS
        // ============================================
        async init() {
            console.log('🔄 Pricing Approval Requests - Initializing');
            
            try {
                await Promise.all([
                    this.loadFilterOptions(),
                    this.fetchRequests()
                ]);
                console.log('✅ Initialization complete');
            } catch (error) {
                console.error('❌ Initialization failed:', error);
                this.showToast('error', 'Failed to initialize');
            }
        },

        // ============================================
        // FILTER OPTIONS LOADING
        // ============================================
        async loadFilterOptions() {
            try {
                const response = await this.apiRequest('/superadmin/api/pricing/requests?filter_options=true');
                
                if (response.success) {
                    // Jika API tidak menyediakan filter options, extract dari data
                    if (response.meta?.filters_available) {
                        // Handle jika API punya filter options
                        this.extractFilterOptionsFromMeta(response);
                    } else {
                        // Fallback: tunggu data requests
                        console.log('No filter options in response, will extract from requests');
                    }
                }
            } catch (error) {
                console.error('Load filter options error:', error);
            }
        },
        
        extractFilterOptionsFromMeta(response) {
            // Extract branches dan services dari meta jika ada
            // Ini akan di-override setelah fetchRequests jika diperlukan
        },
        
        extractFilterOptionsFromRequests() {
            if (this.requests.length === 0) return;
            
            // Extract unique branches
            const branchMap = new Map();
            this.requests.forEach(req => {
                const branchId = req.location_id || req.room?.id;
                const branchName = req.location?.name || 
                                 `Room ${req.room?.room_number || 'N/A'}`;
                
                if (branchId && branchName) {
                    branchMap.set(branchId, {
                        id: branchId,
                        name: branchName
                    });
                }
            });
            this.branches = Array.from(branchMap.values());
            
            // Extract unique services
            const serviceMap = new Map();
            this.requests.forEach(req => {
                const serviceId = req.room_type_id || req.category_id;
                const serviceName = `${req.room_type}${req.category ? ' - ' + req.category : ''}`;
                
                if (serviceId && serviceName) {
                    serviceMap.set(serviceId, {
                        id: serviceId,
                        name: serviceName
                    });
                }
            });
            this.services = Array.from(serviceMap.values());
        },
        
        // ============================================
        // DATA FETCHING
        // ============================================
        async fetchRequests(page = 1) {
            if (this.isLoading) return;
            
            this.isLoading = true;
            
            try {
                const params = new URLSearchParams({
                    status: this.currentFilter === 'pending' ? 'pending' : 'all',
                    page: page,
                    per_page: this.pagination.per_page
                });
                
                // Tambahkan filters jika ada
                if (this.filters.branch) params.append('branch_id', this.filters.branch);
                if (this.filters.service) params.append('service_id', this.filters.service);
                if (this.filters.dateRange !== 'all') params.append('date_range', this.filters.dateRange);
                
                const response = await this.apiRequest(
                    `/superadmin/api/pricing/requests?${params}`
                );
                
                if (response.success) {
                    // Simpan data API asli
                    this.requests = response.data || [];
                    
                    // Update stats dari meta
                    if (response.meta?.stats) {
                        // Transform stats: API menggunakan 'inactive', Blade mungkin butuh 'rejected'
                        this.stats = {
                            all: response.meta.stats.all || 0,
                            pending: 0, // API tampaknya tidak punya pending
                            active: response.meta.stats.active || 0,
                            rejected: (response.meta.stats.inactive || 0) + (response.meta.stats.rejected || 0)
                        };
                    }
                    
                    // Update pagination
                    if (response.meta?.pagination) {
                        this.pagination = response.meta.pagination;
                    }
                    
                    // Extract filter options dari data
                    this.extractFilterOptionsFromRequests();
                    
                } else {
                    throw new Error(response.message || 'API Error');
                }
                
            } catch (error) {
                console.error('Fetch requests error:', error);
                this.showToast('error', this.getErrorMessage(error));
                this.requests = [];
            } finally {
                this.isLoading = false;
            }
        },
        
        // ============================================
        // SELECTION METHODS (tetap sama)
        // ============================================
        toggleSelectRequest(requestId) {
            const index = this.selectedRequests.indexOf(requestId);
            
            if (index === -1) {
                this.selectedRequests.push(requestId);
            } else {
                this.selectedRequests.splice(index, 1);
            }
        },
        
        toggleSelectAll() {
            const allFilteredIds = this.filteredRequests.map(req => req.id);
            
            if (this.selectedRequests.length !== allFilteredIds.length) {
                this.selectedRequests = [...allFilteredIds];
            } else {
                this.selectedRequests = [];
            }
        },
        
        // ============================================
        // FILTER METHODS
        // ============================================
        filterByStatus: Alpine.debounce(function(status) {
            this.currentFilter = status;
            this.selectedRequests = [];
            this.fetchRequests(1);
        }, 300),
        
        applyFilters() {
            if (this.filterTimeout) {
                clearTimeout(this.filterTimeout);
            }
            
            this.filterTimeout = setTimeout(() => {
                this.selectedRequests = [];
                this.fetchRequests(1);
            }, 500);
        },
        
        resetFilters() {
            this.filters = {
                branch: '',
                service: '',
                dateRange: 'all'
            };
            this.currentFilter = 'all';
            this.selectedRequests = [];
            this.fetchRequests(1);
        },

        getFilterTitle() {
            switch(this.currentFilter) {
                case 'all': return 'All';
                case 'pending': return 'Pending';
                case 'active': return 'Approved';
                case 'rejected': return 'Rejected';
                default: return this.currentFilter.charAt(0).toUpperCase() + this.currentFilter.slice(1);
            }
        },

        // ============================================
        // MODAL METHODS
        // ============================================
        openReviewModal(request) {
            this.currentReviewRequest = request;
            this.reviewContent = this.generateReviewContent(request);
            this.showReviewModal = true;
        },
        
        closeReviewModal() {
            this.showReviewModal = false;
            this.currentReviewRequest = null;
            this.reviewContent = '';
        },
        
        closeAllModals() {
            this.closeReviewModal();
            this.closeBulkConfirmModal();
        },
        
        generateReviewContent(request) {
            const safeText = (text) => {
                const div = document.createElement('div');
                div.textContent = text || 'N/A';
                return div.innerHTML;
            };
            
            return `
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 mb-2">Request Details</h4>
                        <p><strong>Branch:</strong> ${safeText(request.branchName)}</p>
                        <p><strong>Service:</strong> ${safeText(request.serviceName)}</p>
                        <p><strong>Package:</strong> ${safeText(request.packageName)}</p>
                        <p><strong>Requested by:</strong> ${safeText(request.requested_by)}</p>
                        <p><strong>Reason:</strong> ${safeText(request.reason)}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 mb-2">Pricing Change</h4>
                        <p>Current: Rp ${this.formatCurrency(request.masterPrice)}</p>
                        <p>Requested: Rp ${this.formatCurrency(request.requestedPrice)}</p>
                        <p>Change: <span class="${this.getImpactClass(request.difference)}">
                            ${this.getImpactText(request.difference)}
                        </span></p>
                    </div>
                    <div class="flex gap-3 pt-4 border-t">
                        <button @click="approveSingleRequest(${request.id})" 
                                class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700">
                            Approve
                        </button>
                        <button @click="rejectSingleRequest(${request.id})" 
                                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700">
                            Reject
                        </button>
                        <button @click="closeReviewModal()" 
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50">
                            Cancel
                        </button>
                    </div>
                </div>
            `;
        },
        
        // ============================================
        // UTILITY METHODS (update beberapa)
        // ============================================
        getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.content : '';
        },
        
        async apiRequest(url, method = 'GET', body = null) {
            const options = {
                method: method,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            };
            
            if (body && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
                options.body = JSON.stringify(body);
            }
            
            const response = await fetch(url, options);
            
            if (!response.ok) {
                const error = new Error(`HTTP ${response.status}: ${response.statusText}`);
                error.response = response;
                throw error;
            }
            
            return await response.json();
        },
        
        getErrorMessage(error) {
            if (error.message.includes('Network')) {
                return 'Network error. Please check connection';
            }
            if (error.response?.status === 401) {
                return 'Session expired. Please login again';
            }
            if (error.response?.status === 403) {
                return 'You dont have permission';
            }
            if (error.response?.status === 404) {
                return 'Resource not found';
            }
            if (error.response?.status === 422) {
                return 'Validation error';
            }
            if (error.response?.status >= 500) {
                return 'Server error. Please try again later';
            }
            return 'An error occurred';
        },
        
        formatCurrency(amount) {
            if (!amount && amount !== 0) return '0';
            return new Intl.NumberFormat('id-ID').format(amount);
        },
        
        formatDate(dateString) {
            if (!dateString) return '-';
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            } catch (e) {
                return '-';
            }
        },
        
        formatTime(dateString) {
            if (!dateString) return '-';
            try {
                const date = new Date(dateString);
                return date.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });
            } catch (e) {
                return '-';
            }
        },
        
        getImpactClass(difference) {
            if (!difference && difference !== 0) return 'text-gray-600';
            const absDiff = Math.abs(difference);
            return absDiff > 15 ? 'text-red-600' : 
                   absDiff > 10 ? 'text-yellow-600' : 'text-green-600';
        },
        
        getImpactIcon(difference) {
            if (!difference && difference !== 0) return '⚪';
            const absDiff = Math.abs(difference);
            if (difference > 0) {
                return absDiff > 15 ? '📈🔴' : 
                       absDiff > 10 ? '📈🟡' : '📈🟢';
            } else {
                return absDiff > 15 ? '📉🔴' : 
                       absDiff > 10 ? '📉🟡' : '📉🟢';
            }
        },
        
        getImpactText(difference) {
            if (!difference && difference !== 0) return '0%';
            const sign = difference > 0 ? '+' : '';
            return `${sign}${difference.toFixed(1)}%`;
        },
        
        showToast(type, message) {
            const toastId = 'toast-' + Date.now();
            const toast = document.createElement('div');
            toast.id = toastId;
            toast.className = `fixed bottom-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 ${
                type === 'success' ? 
                'bg-green-100 text-green-800 border border-green-200' : 
                'bg-red-100 text-red-800 border border-red-200'
            }`;
            toast.textContent = message;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'polite');
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                const toastEl = document.getElementById(toastId);
                if (toastEl && toastEl.parentNode) {
                    toastEl.remove();
                }
            }, 3000);
        },
        
        // ============================================
        // ACTION METHODS
        // ============================================
        async approveSingleRequest(requestId) {
            this.isLoading = true;
            
            try {
                const response = await this.apiRequest(
                    `/superadmin/api/pricing/requests/${requestId}/approve`,
                    'POST'
                );
                
                if (response.success) {
                    this.showToast('success', 'Request approved successfully');
                    this.closeReviewModal();
                    await this.fetchRequests();
                } else {
                    throw new Error(response.message || 'Approve failed');
                }
            } catch (error) {
                console.error('Approve error:', error);
                this.showToast('error', this.getErrorMessage(error));
            } finally {
                this.isLoading = false;
            }
        },
        
        async rejectSingleRequest(requestId) {
            this.isLoading = true;
            
            try {
                const response = await this.apiRequest(
                    `/superadmin/api/pricing/requests/${requestId}/reject`,
                    'POST',
                    { reason: 'Rejected via review modal' }
                );
                
                if (response.success) {
                    this.showToast('success', 'Request rejected successfully');
                    this.closeReviewModal();
                    await this.fetchRequests();
                } else {
                    throw new Error(response.message || 'Reject failed');
                }
            } catch (error) {
                console.error('Reject error:', error);
                this.showToast('error', this.getErrorMessage(error));
            } finally {
                this.isLoading = false;
            }
        },
        
        // ============================================
        // BULK ACTIONS (jika diperlukan)
        // ============================================
        bulkApprove() {
            if (this.selectedRequests.length === 0 || this.isLoading) return;
            
            this.bulkActionType = 'approve';
            this.showBulkConfirmModal = true;
        },
        
        bulkReject() {
            if (this.selectedRequests.length === 0 || this.isLoading) return;
            
            this.bulkActionType = 'reject';
            this.showBulkConfirmModal = true;
        },
        
        closeBulkConfirmModal() {
            this.showBulkConfirmModal = false;
            this.bulkActionType = '';
        },
        
        async confirmBulkAction() {
            if (this.bulkActionType === 'approve') {
                await this.executeBulkApprove();
            } else if (this.bulkActionType === 'reject') {
                await this.executeBulkReject();
            }
            
            this.closeBulkConfirmModal();
        },
        
        async executeBulkApprove() {
            this.isLoading = true;
            
            try {
                const response = await this.apiRequest(
                    '/superadmin/api/pricing/requests/bulk-approve',
                    'POST',
                    { request_ids: this.selectedRequests }
                );
                
                if (response.success) {
                    this.showToast('success', `${response.approved_count || this.selectedRequests.length} request(s) approved`);
                    this.selectedRequests = [];
                    await this.fetchRequests();
                } else {
                    throw new Error(response.message || 'Bulk approve failed');
                }
            } catch (error) {
                console.error('Bulk approve error:', error);
                this.showToast('error', this.getErrorMessage(error));
            } finally {
                this.isLoading = false;
            }
        },
        
        async executeBulkReject() {
            this.isLoading = true;
            
            try {
                const response = await this.apiRequest(
                    '/superadmin/api/pricing/requests/bulk-reject',
                    'POST',
                    { 
                        request_ids: this.selectedRequests,
                        reason: 'Bulk rejection by admin'
                    }
                );
                
                if (response.success) {
                    this.showToast('success', `${response.rejected_count || this.selectedRequests.length} request(s) rejected`);
                    this.selectedRequests = [];
                    await this.fetchRequests();
                } else {
                    throw new Error(response.message || 'Bulk reject failed');
                }
            } catch (error) {
                console.error('Bulk reject error:', error);
                this.showToast('error', this.getErrorMessage(error));
            } finally {
                this.isLoading = false;
            }
        },
        
        // ============================================
        // CLEANUP
        // ============================================
        destroy() {
            if (this.filterTimeout) {
                clearTimeout(this.filterTimeout);
            }
            
            document.querySelectorAll('[id^="toast-"]').forEach(toast => {
                if (toast.parentNode) {
                    toast.remove();
                }
            });
        }
    }));
});
</script>
@endsection