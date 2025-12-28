{{-- resources/views/superadmin/pricing/approval.blade.php --}}
@extends('layouts.superadmin')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl" x-data="pricingApproval()" x-init="init()">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">⏳ Pending Price Approvals</h1>
                <p class="text-gray-600 mt-1 text-sm md:text-base">
                    Review and approve/reject price change requests from admins
                    <span x-text="`(${pendingRequests.length} pending)`" class="font-semibold text-blue-600"></span>
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button @click="fetchPendingRequests()" 
                        :disabled="isLoading"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors flex items-center gap-2">
                    <svg x-show="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <svg x-show="isLoading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
            <div class="text-2xl font-bold text-gray-900" x-text="pendingRequests.length">0</div>
            <div class="text-sm text-gray-600 mt-1">Pending Requests</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
            <div class="text-lg font-bold text-green-600" x-text="priceRange">Rp 0 - Rp 0</div>
            <div class="text-sm text-gray-600 mt-1">Price Range</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
            <div class="text-2xl font-bold text-purple-600" x-text="increaseCount">0</div>
            <div class="text-sm text-gray-600 mt-1">Price Increases</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-orange-500">
            <div class="text-lg font-bold text-orange-600" x-text="lastRequestTime">-</div>
            <div class="text-sm text-gray-600 mt-1">Last Request</div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-3">
            {{-- Location Filter --}}
            <select x-model="filters.location" 
                    @change="applyFilters()" 
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full md:w-auto">
                <option value="">All Locations</option>
                <template x-for="loc in locations" :key="loc.id">
                    <option :value="loc.name" x-text="loc.name"></option>
                </template>
            </select>
            
            {{-- Room Type Filter --}}
            <select x-model="filters.roomType" 
                    @change="applyFilters()" 
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full md:w-auto">
                <option value="">All Room Types</option>
                <template x-for="type in roomTypes" :key="type.id">
                    <option :value="type.name" x-text="type.name"></option>
                </template>
            </select>
            
            {{-- Search Input - PERBAIKAN: gunakan @keyup dan @change --}}
            <input type="text" 
                x-model="filters.search" 
                @keyup="applyFilters()"
                @change="applyFilters()"
                placeholder="Search by location, requested by, or reason..." 
                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full">
            
            <button @click="resetFilters()" 
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 whitespace-nowrap w-full md:w-auto">
                Reset Filters
            </button>
        </div>
    
        {{-- Active Filters Indicator --}}
        <div x-show="filters.location || filters.roomType || filters.search" 
            class="mt-3 flex flex-wrap gap-2">
            <template x-if="filters.location">
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                    Location: <span x-text="filters.location"></span>
                    <button @click="filters.location = ''; applyFilters();" class="text-blue-600 hover:text-blue-800">
                        &times;
                    </button>
                </span>
            </template>
            <template x-if="filters.roomType">
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">
                    Room Type: <span x-text="filters.roomType"></span>
                    <button @click="filters.roomType = ''; applyFilters();" class="text-purple-600 hover:text-purple-800">
                        &times;
                    </button>
                </span>
            </template>
            <template x-if="filters.search">
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">
                    Search: "<span x-text="filters.search"></span>"
                    <button @click="filters.search = ''; applyFilters();" class="text-gray-600 hover:text-gray-800">
                        &times;
                    </button>
                </span>
            </template>
        </div>
    </div>
    {{-- Pending Requests List --}}
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">
                Pending Approval Requests (<span x-text="filteredRequests.length"></span>)
            </h2>
        </div>

        {{-- Loading State --}}
        <div x-show="isLoading && pendingRequests.length === 0" class="text-center py-12">
            <svg class="w-12 h-12 text-gray-300 animate-spin mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-500">Loading pending requests...</p>
        </div>

        {{-- Empty State --}}
        <div x-show="!isLoading && filteredRequests.length === 0" class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500 text-lg font-medium">No pending approval requests</p>
            <p class="text-gray-400 text-sm mt-2">All price change requests have been processed</p>
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden lg:block overflow-x-auto" x-show="!isLoading && filteredRequests.length > 0">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price Change</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested By</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested At</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="request in filteredRequests" :key="request.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900" x-text="'#' + request.id"></td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <div x-text="request.location_name || 'N/A'"></div>
                                <div class="text-xs text-gray-500" x-text="request.city_name"></div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <div x-text="request.room_type || 'N/A'"></div>
                                <div class="text-xs text-gray-500" x-text="request.duration_display"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold" x-text="request.price_formatted"></div>
                                <div class="text-xs" :class="request.price_change_percent > 0 ? 'text-red-600' : 'text-green-600'" 
                                      x-text="request.price_change_display || 'No change'"></div>
                                <div class="text-xs text-gray-500" x-text="'From: ' + (request.previous_price_formatted || 'N/A')"></div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <div x-text="request.requested_by"></div>
                                <div class="text-xs text-gray-500 truncate max-w-xs" x-text="request.request_reason"></div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600" x-text="request.requested_at"></td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    <button @click="openApproveModal(request)" 
                                            class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-sm font-medium hover:bg-green-200 transition-colors flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Approve
                                    </button>
                                    <button @click="openRejectModal(request)" 
                                            class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-sm font-medium hover:bg-red-200 transition-colors flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Reject
                                    </button>
                                    <button @click="viewDetails(request.id)" 
                                            class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-200 transition-colors flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Details
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Mobile/Tablet Card View --}}
        <div class="lg:hidden space-y-4" x-show="!isLoading && filteredRequests.length > 0">
            <template x-for="request in filteredRequests" :key="request.id">
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 mb-1" x-text="'Request #' + request.id"></h3>
                            <p class="text-sm text-gray-600 mb-2" x-text="request.room_type || 'N/A'"></p>
                            <div class="flex flex-wrap gap-2 mb-2">
                                <template x-if="request.location_name">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-700 rounded-full text-xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span x-text="request.location_name"></span>
                                    </span>
                                </template>
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span x-text="request.duration_display"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-2 mb-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Current Price:</span>
                            <span class="text-gray-900" x-text="request.previous_price_formatted || 'N/A'"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Requested Price:</span>
                            <span class="font-semibold" :class="request.price_change_percent > 0 ? 'text-red-600' : 'text-green-600'" 
                                  x-text="request.price_formatted"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Change:</span>
                            <span :class="request.price_change_percent > 0 ? 'text-red-600' : 'text-green-600'" 
                                  x-text="request.price_change_display || 'No change'"></span>
                        </div>
                        <div class="text-sm text-gray-600">
                            <div class="truncate" x-text="request.request_reason || 'No reason provided'"></div>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 pt-3 border-t">
                        <button @click="openApproveModal(request)" 
                                class="flex-1 px-3 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
                            Approve
                        </button>
                        <button @click="openRejectModal(request)" 
                                class="flex-1 px-3 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700">
                            Reject
                        </button>
                        <button @click="viewDetails(request.id)" 
                                class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                            Details
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Success/Error Toast --}}
    <div x-show="showToast" 
        x-transition 
        class="fixed bottom-4 right-4 z-50"
        x-cloak
        x-init="if(showToast) setTimeout(() => showToast = false, 5000)">
        <div class="rounded-lg shadow-lg p-4" 
            :class="toastType === 'success' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
            <div class="flex items-center gap-3">
                {{-- GANTI template dengan Alpine directive --}}
                <svg :class="toastType === 'success' ? 'text-green-500' : 'text-red-500'" 
                    class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {{-- Gunakan x-show bukan template --}}
                    <g x-show="toastType === 'success'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </g>
                    {{-- Atau alternatif: duplicate SVG dengan x-show --}}
                    <g x-show="toastType === 'error'" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </g>
                </svg>
                
                <div>
                    <p class="font-medium" 
                    :class="toastType === 'success' ? 'text-green-800' : 'text-red-800'" 
                    x-text="toastMessage"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div x-show="showRejectModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showRejectModal = false; rejectReason = ''" 
                class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
                <div class="flex items-center justify-between border-b pb-3 mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Reject Price Request</h3>
                    <button @click="showRejectModal = false; rejectReason = ''" 
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-3">
                        Please provide a reason for rejecting this price change request:
                    </p>
                    <textarea x-model="rejectReason" 
                            rows="4" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                            placeholder="Example: Price increase is too high without proper justification..."
                            maxlength="500"></textarea>
                    <div class="flex justify-between mt-1">
                        <span class="text-xs text-gray-500">Minimum 10 characters</span>
                        <span class="text-xs" 
                            :class="rejectReason.length > 500 ? 'text-red-600' : 'text-gray-500'"
                            x-text="rejectReason.length + '/500'"></span>
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t">
                    <button @click="showRejectModal = false; rejectReason = ''" 
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                        Cancel
                    </button>
                    <button @click="rejectRequest(selectedRequest.id)" 
                            :disabled="rejectReason.length < 10 || rejectReason.length > 500"
                            :class="rejectReason.length >= 10 && rejectReason.length <= 500 ? 'bg-red-600 hover:bg-red-700' : 'bg-red-300 cursor-not-allowed'"
                            class="flex-1 px-4 py-2 text-white rounded-lg text-sm font-medium">
                        Confirm Reject
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Approve Modal --}}
    <div x-show="showApproveModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showApproveModal = false" 
                class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
                <div class="flex items-center justify-between border-b pb-3 mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Approve Price Request</h3>
                    <button @click="showApproveModal = false" 
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-3">
                        Are you sure you want to approve this price change request?
                    </p>
                    
                    {{-- Request Info --}}
                    <template x-if="selectedRequest">
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 text-sm">
                            <div class="flex justify-between mb-1">
                                <span class="text-gray-600">Request ID:</span>
                                <span class="font-semibold" x-text="'#' + selectedRequest.id"></span>
                            </div>
                            <div class="flex justify-between mb-1">
                                <span class="text-gray-600">New Price:</span>
                                <span class="font-bold text-green-600" x-text="selectedRequest.price_formatted"></span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1" x-text="selectedRequest.location_name"></div>
                        </div>
                    </template>
                </div>

                <div class="flex gap-3 pt-4 border-t">
                    <button @click="showApproveModal = false" 
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                        Cancel
                    </button>
                    <button @click="approveRequest(selectedRequest.id)" 
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
                        Confirm Approve
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Details Modal (Read-only, Smaller) --}}
    <div x-show="showDetailsModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showDetailsModal = false" 
                class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6">
                <div class="flex items-center justify-between border-b pb-3 mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Request Details #<span x-text="selectedDetails?.id"></span></h3>
                    <button @click="showDetailsModal = false" 
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                    <template x-if="selectedDetails">
                        {{-- BASIC INFO --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Request ID</label>
                                    <div class="text-sm text-gray-900" x-text="selectedDetails.id"></div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                                        x-text="'Pending'"></span>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Requested By</label>
                                    <div class="text-sm text-gray-900" x-text="selectedDetails.requested_by"></div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Requested At</label>
                                    <div class="text-sm text-gray-900" x-text="selectedDetails.requested_at"></div>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Location</label>
                                    <div class="text-sm text-gray-900" x-text="selectedDetails.location?.name || 'N/A'"></div>
                                    <div class="text-xs text-gray-600 mt-1" x-text="selectedDetails.location?.address"></div>
                                    <div class="text-xs text-gray-600" x-text="selectedDetails.location?.city"></div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Service Type</label>
                                    <div class="text-sm text-gray-900" x-text="selectedDetails.room_type"></div>
                                    <div class="text-xs text-gray-600" x-text="selectedDetails.category"></div>
                                </div>
                            </div>
                        </div>

                        {{-- PRICE COMPARISON --}}
                        <div class="border-t pt-4">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Price Comparison</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="text-xs font-medium text-gray-500 mb-2">Current Active Price</div>
                                    <template x-if="selectedDetails.parent_data">
                                        <div>
                                            <div class="text-lg font-semibold text-gray-900" 
                                                x-text="selectedDetails.parent_data.price_formatted"></div>
                                            <div class="text-sm text-gray-600 mt-1" 
                                                x-text="selectedDetails.parent_data.duration_display"></div>
                                            <div class="text-xs text-gray-500 mt-2">
                                                <div>Deposit: <span x-text="'Rp ' + parseInt(selectedDetails.parent_data.deposit || 0).toLocaleString('id-ID')"></span></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                
                                <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                                    <div class="text-xs font-medium text-blue-600 mb-2">Requested New Price</div>
                                    <div class="text-lg font-bold text-blue-900" 
                                        x-text="selectedDetails.price_formatted"></div>
                                    <div class="text-sm text-blue-700 mt-1" 
                                        x-text="selectedDetails.duration_display"></div>
                                    <div class="text-xs text-blue-600 mt-2">
                                        <div>Change: <span :class="selectedDetails.price_change_percent > 0 ? 'text-red-600' : 'text-green-600'" 
                                                        x-text="selectedDetails.price_change_display || 'No change'"></span></div>
                                        <div>Deposit: <span x-text="'Rp ' + parseInt(selectedDetails.deposit || 0).toLocaleString('id-ID')"></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ADDITIONAL INFO --}}
                        <div class="border-t pt-4">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Additional Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Request Reason</label>
                                    <div class="text-sm text-gray-900 bg-gray-50 p-3 rounded" 
                                        x-text="selectedDetails.reason || 'No reason provided'"></div>
                                </div>
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Coffee Break</label>
                                        <div class="text-sm text-gray-900" 
                                            x-text="selectedDetails.additional_details?.coffee_break_option_label || 'N/A'"></div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Created At</label>
                                        <div class="text-sm text-gray-900" 
                                            x-text="selectedDetails.additional_details?.created_at"></div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Last Updated</label>
                                        <div class="text-sm text-gray-900" 
                                            x-text="selectedDetails.additional_details?.updated_at"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="flex gap-3 pt-6 border-t mt-4">
                    <button @click="showDetailsModal = false" 
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                        Close
                    </button>
                    <template x-if="selectedDetails">
                        <div class="flex gap-2">
                            {{-- APPROVE BUTTON: Tutup details modal, lalu buka approve modal --}}
                            <button @click="showDetailsModal = false; openApproveModal(selectedDetails)" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
                                Approve
                            </button>
                            
                            {{-- REJECT BUTTON: Tutup details modal, lalu buka reject modal --}}
                            <button @click="showDetailsModal = false; openRejectModal(selectedDetails)" 
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700">
                                Reject
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function pricingApproval() {
    return {
        // State
        pendingRequests: [],
        filteredRequests: [],
        isLoading: false,
        showRejectModal: false,
        showApproveModal: false,
        showDetailsModal: false,
        selectedRequest: null,
        selectedDetails: null,
        rejectReason: '',
        
        // Filters
        filters: {
            location: '',
            roomType: '',
            search: ''
        },
        
        // Dropdown options (harus dari API atau Blade)
        locations: [],
        roomTypes: [],
        
        // Toast
        showToast: false,
        toastType: 'success',
        toastMessage: '',
        
        // Computed Properties sesuai Blade
        get priceRange() {
            if (this.pendingRequests.length === 0) return 'Rp 0 - Rp 0';
            
            const prices = this.pendingRequests.map(r => parseFloat(r.price));
            const min = Math.min(...prices);
            const max = Math.max(...prices);
            
            return `Rp ${this.formatCurrency(min)} - Rp ${this.formatCurrency(max)}`;
        },
        
        get increaseCount() {
            return this.pendingRequests.filter(r => 
                parseFloat(r.price_change_percent) > 0
            ).length;
        },
        
        get lastRequestTime() {
            if (this.pendingRequests.length === 0) return '-';
            
            const latest = this.pendingRequests.sort((a, b) => 
                new Date(b.requested_at) - new Date(a.requested_at)
            )[0];
            
            return this.timeAgo(latest.requested_at);
        },
        
        // Methods
       async init() {
            console.log('🔄 Alpine init started');
            
            // Load filter options dan pending requests secara parallel
            await Promise.all([
                this.loadFilterOptions(),
                this.fetchPendingRequests()
            ]);
            
            console.log('✅ Initialization complete');
            console.log('📊 Locations count:', this.locations.length);
            console.log('📊 RoomTypes count:', this.roomTypes.length);
            console.log('📊 Pending requests:', this.pendingRequests.length);
            
            this.isLoading = false;
            
            // Auto-refresh
            setInterval(() => {
                this.fetchPendingRequests();
            }, 30000);
        },
        
        async fetchPendingRequests() {
            this.isLoading = true;
            
            try {
                const response = await fetch('/superadmin/api/pricing/pending', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (!response.ok) throw new Error('Failed to fetch data');
                
                const data = await response.json();
                
                if (data.success) {
                    // Data dari backend sudah sesuai dengan Blade!
                    this.pendingRequests = data.data || [];
                    this.filteredRequests = [...this.pendingRequests];
                    
                    // Extract unique locations & room types untuk filters
                    this.extractFilterOptions();
                } else {
                    throw new Error(data.message || 'Failed to load data');
                }
                
            } catch (error) {
                this.showToastMessage('error', 'Failed to load pending requests');
                console.error('Fetch error:', error);
            } finally {
                this.isLoading = false;
            }
        },
        
        async loadFilterOptions() {
            try {
                console.log('🔄 Loading filter options...');
                
                const response = await fetch('/superadmin/api/pricing/filter-options', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                console.log('📊 Filter options response:', data);
                
                if (data.success) {
                    this.locations = data.locations || [];
                    this.roomTypes = data.roomTypes || [];
                    
                    console.log('✅ Locations loaded:', this.locations);
                    console.log('✅ Room Types loaded:', this.roomTypes);
                    
                    // Cek jika data kosong
                    if (this.locations.length === 0) {
                        console.warn('⚠️ Locations array is empty');
                    }
                    if (this.roomTypes.length === 0) {
                        console.warn('⚠️ RoomTypes array is empty');
                    }
                } else {
                    console.error('❌ API error:', data.message);
                }
                
            } catch (error) {
                console.error('❌ Network error:', error);
            }
        },
        
        extractFilterOptions() {
            // Hanya sebagai fallback
            const locationMap = new Map();
            const roomTypeMap = new Map();
            
            this.pendingRequests.forEach(request => {
                // Locations
                if (request.location_id && request.location_name) {
                    locationMap.set(request.location_id, {
                        id: request.location_id,
                        name: request.location_name
                    });
                }
                
                // Room Types (gunakan dari data API yang sudah ada room_type)
                if (request.room_type && request.room_type !== 'N/A') {
                    roomTypeMap.set(request.room_type, {
                        id: request.room_type, // Use name as ID for filtering
                        name: request.room_type
                    });
                }
            });
            
            this.locations = Array.from(locationMap.values());
            this.roomTypes = Array.from(roomTypeMap.values());
        },
        
        applyFilters() {
            // Debounce untuk search input (optional)
            if (this.filterTimeout) {
                clearTimeout(this.filterTimeout);
            }
            
            this.filterTimeout = setTimeout(() => {
                this.performFiltering();
            }, 300); // Delay 300ms untuk search input
        },

        performFiltering() {
            let filtered = [...this.pendingRequests];
            
            // 1. Filter by Location (gunakan location_id jika ada, atau location_name)
            if (this.filters.location) {
                filtered = filtered.filter(request => {
                    // Cek apakah location_name mengandung filter
                    if (request.location_name && 
                        request.location_name.toLowerCase().includes(this.filters.location.toLowerCase())) {
                        return true;
                    }
                    
                    // Atau cek apakah ada location yang match
                    const selectedLocation = this.locations.find(loc => 
                        loc.name === this.filters.location
                    );
                    
                    if (selectedLocation && request.location_id === selectedLocation.id) {
                        return true;
                    }
                    
                    return false;
                });
            }
            
            // 2. Filter by Room Type
            if (this.filters.roomType) {
                filtered = filtered.filter(request => {
                    return request.room_type && 
                           request.room_type.toLowerCase() === this.filters.roomType.toLowerCase();
                });
            }
            
            // 3. Filter by Search
            if (this.filters.search) {
                const searchTerm = this.filters.search.toLowerCase().trim();
                
                filtered = filtered.filter(request => {
                    // Cari di semua field yang relevan
                    return (
                        (request.location_name && request.location_name.toLowerCase().includes(searchTerm)) ||
                        (request.city_name && request.city_name.toLowerCase().includes(searchTerm)) ||
                        (request.requested_by && request.requested_by.toLowerCase().includes(searchTerm)) ||
                        (request.request_reason && request.request_reason.toLowerCase().includes(searchTerm)) ||
                        (request.room_type && request.room_type.toLowerCase().includes(searchTerm)) ||
                        (request.price_formatted && request.price_formatted.toLowerCase().includes(searchTerm))
                    );
                });
            }
            
            this.filteredRequests = filtered;
        },
        
        resetFilters() {
            this.filters = {
                location: '',
                roomType: '',
                search: ''
            };
            this.filteredRequests = [...this.pendingRequests];
        },
        
        async approveRequest(requestId) {
            this.showApproveModal = false;
            
            try {
                const url = `/superadmin/api/pricing/${requestId}/approve`;
                console.log('✅ Approving request:', url);
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Remove dari lists
                    this.pendingRequests = this.pendingRequests.filter(r => r.id !== requestId);
                    this.filteredRequests = this.filteredRequests.filter(r => r.id !== requestId);
                    
                    this.showToastMessage('success', data.message || 'Price request approved successfully');
                    this.showDetailsModal = false;
                    this.selectedRequest = null; // Reset selected request
                } else {
                    throw new Error(data.message || 'Approval failed');
                }
                
            } catch (error) {
                console.error('Approval error:', error);
                this.showToastMessage('error', error.message);
            }
        },
        
        async rejectRequest(requestId) {
            if (this.rejectReason.length < 10) {
                this.showToastMessage('error', 'Please provide a reason (min 10 characters)');
                return;
            }
            
            try {
                // PERBAIKAN: Ganti urutan parameter
                const url = `/superadmin/api/pricing/${requestId}/reject`;
                console.log('✅ Correct reject URL:', url);
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        reason: this.rejectReason
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Remove dari lists
                    this.pendingRequests = this.pendingRequests.filter(r => r.id !== requestId);
                    this.filteredRequests = this.filteredRequests.filter(r => r.id !== requestId);
                    
                    this.showToastMessage('success', data.message || 'Price request rejected');
                    this.showRejectModal = false;
                    this.rejectReason = '';
                    this.selectedRequest = null;
                } else {
                    throw new Error(data.message || 'Rejection failed');
                }
                
            } catch (error) {
                console.error('Rejection error:', error);
                this.showToastMessage('error', error.message);
            }
        },
        
        async viewDetails(requestId) {
            try {
                // Menggunakan data yang sudah ada (tidak perlu API call tambahan)
                const request = this.pendingRequests.find(r => r.id === requestId);
                
                if (!request) {
                    throw new Error('Request not found');
                }
                
                // Format data sesuai modal details di Blade
                this.selectedDetails = {
                    // Basic info - sesuai Blade
                    id: request.id,
                    requested_by: request.requested_by,
                    requested_at: request.requested_at,
                    
                    // Location info - sesuai Blade
                    location: {
                        name: request.location_name,
                        city: request.city_name
                    },
                    
                    // Service info - sesuai Blade  
                    room_type: this.getRoomTypeFromDuration(request.duration_display),
                    category: this.getCategoryFromDuration(request.duration_display),
                    
                    // Price info - sesuai Blade
                    price_formatted: request.price_formatted,
                    duration_display: request.duration_display,
                    price_change_percent: parseFloat(request.price_change_percent),
                    price_change_display: request.price_change_display,
                    
                    // Parent/previous data - sesuai Blade
                    parent_data: {
                        price_formatted: request.previous_price_formatted,
                        duration_display: request.duration_display,
                        deposit: request.deposit
                    },
                    
                    // Additional info - sesuai Blade
                    reason: request.request_reason,
                    additional_details: {
                        coffee_break_option_label: request.coffee_break_option || 'N/A',
                        created_at: request.requested_at,
                        updated_at: request.requested_at
                    },
                    
                    // Deposit - sesuai Blade
                    deposit: request.deposit
                };
                
                this.showDetailsModal = true;
                
            } catch (error) {
                this.showToastMessage('error', 'Failed to load request details');
            }
        },

        openApproveModal(request) {
            this.showDetailsModal = false;
            this.showRejectModal = false;
            this.selectedRequest = request;
            this.showApproveModal = true;
        },
        
        openRejectModal(request) {
            this.showDetailsModal = false;
            this.showApproveModal = false;
            this.selectedRequest = request;
            this.rejectReason = '';
            this.showRejectModal = true;
        },
        
        // Helper functions
        getRoomTypeFromDuration(durationDisplay) {
            if (!durationDisplay) return 'N/A';
            if (durationDisplay.includes('hour')) return 'Hourly Meeting Room';
            if (durationDisplay.includes('month')) return 'Monthly Office';
            return durationDisplay;
        },
        
        getCategoryFromDuration(durationDisplay) {
            if (!durationDisplay) return 'General';
            if (durationDisplay.includes('hour')) return 'Meeting Room';
            if (durationDisplay.includes('month')) return 'Private Office';
            return 'Other';
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID').format(amount);
        },
        
        timeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const seconds = Math.floor((now - date) / 1000);
            
            const intervals = {
                year: 31536000,
                month: 2592000,
                week: 604800,
                day: 86400,
                hour: 3600,
                minute: 60,
                second: 1
            };
            
            for (const [unit, secondsInUnit] of Object.entries(intervals)) {
                const interval = Math.floor(seconds / secondsInUnit);
                
                if (interval >= 1) {
                    return interval === 1 ? `1 ${unit} ago` : `${interval} ${unit}s ago`;
                }
            }
            
            return 'Just now';
        },
        
        showToastMessage(type, message) {
            this.toastType = type;
            this.toastMessage = message;
            this.showToast = true;
            
            // Auto-hide setelah 5 detik
            setTimeout(() => {
                this.showToast = false;
            }, 5000);
        }
    };
}

document.addEventListener('alpine:init', () => {
    Alpine.data('pricingApproval', pricingApproval);
});

</script>
@push('styles')
<style>
[x-cloak] { display: none !important; }
/* Custom scrollbar for modal */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}
</style>
@endpush
@endsection