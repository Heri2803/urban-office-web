{{-- resources/views/admin/pricing.blade.php --}}

@extends('layouts.admin')

@section('title', 'Pricing Management')

@section('content')
<div x-data="pricingManagement()" x-init="init()" class="space-y-4 md:space-y-6 pb-20 md:pb-6 max-w-full overflow-hidden">
    
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="min-w-0">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 truncate">💰 Pricing Management</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">View base prices & manage promotions</p>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 mb-1">Active Promos</p>
                    <p class="text-xl sm:text-2xl font-bold text-green-600" x-text="stats.activePromos"></p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center text-xl sm:text-2xl flex-shrink-0">
                    🎉
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 mb-1">Pending Requests</p>
                    <p class="text-xl sm:text-2xl font-bold text-yellow-600" x-text="stats.pendingRequests"></p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-100 rounded-lg flex items-center justify-center text-xl sm:text-2xl flex-shrink-0">
                    ⏳
                </div>
            </div>
        </div>
    </div>

    {{-- Main Tabs --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Tab Headers --}}
        <div class="border-b border-gray-200 overflow-x-auto scrollbar-hide">
            <nav class="flex">
                <button 
                    @click="mainTab = 'base'"
                    :class="mainTab === 'base' ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-4 sm:px-6 py-3 sm:py-4 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap transition flex-shrink-0"
                >
                    🔒 Base Prices
                </button>
                <button 
                    @click="mainTab = 'promos'"
                    :class="mainTab === 'promos' ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-4 sm:px-6 py-3 sm:py-4 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap transition flex-shrink-0"
                >
                    🎉 Promos
                    <span class="ml-1 sm:ml-2 px-1.5 py-0.5 bg-green-100 text-green-700 rounded-full text-xs" x-text="stats.activePromos"></span>
                </button>
                <button 
                    @click="mainTab = 'requests'"
                    :class="mainTab === 'requests' ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-4 sm:px-6 py-3 sm:py-4 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap transition flex-shrink-0"
                >
                    📝 Requests
                    <span class="ml-1 sm:ml-2 px-1.5 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs" x-text="stats.pendingRequests"></span>
                </button>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="p-3 sm:p-4 md:p-6">

            {{-- TAB 1: BASE PRICES --}}
            <div x-show="mainTab === 'base'" x-transition>
                {{-- Service Tabs --}}
                <div class="border-b border-gray-200 mb-4 overflow-x-auto scrollbar-hide">
                    <nav class="flex gap-1 sm:gap-2">
                        <template x-for="service in services" :key="service.id">
                            <button 
                                @click="activeService = service.id"
                                :class="activeService === service.id ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500'"
                                class="px-3 sm:px-4 py-2 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap transition flex-shrink-0"
                            >
                                <span x-text="service.icon"></span>
                                <span class="hidden sm:inline ml-1" x-text="service.name"></span>
                            </button>
                        </template>
                    </nav>
                </div>

                {{-- Base Price Table --}}
                <template x-for="service in services" :key="service.id">
                    <div x-show="activeService === service.id" x-transition>
                        <div class="mb-4">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2" x-text="service.icon + ' ' + service.name"></h3>
                            <p class="text-xs sm:text-sm text-gray-500">Base prices are fixed and cannot be edited directly</p>
                        </div>

                        {{-- Price Cards Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-4">
                            <template x-for="duration in durations" :key="duration.id">
                                <div class="bg-gray-50 rounded-lg border border-gray-200 p-3 sm:p-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs sm:text-sm font-medium text-gray-600 truncate" x-text="duration.name"></p>
                                        </div>
                                        <span class="text-gray-400 flex-shrink-0 ml-2">🔒</span>
                                    </div>
                                    <p class="text-lg sm:text-2xl font-bold text-gray-800 mb-2" x-text="'Rp ' + formatNumber(getBasePrice(service.id, duration.id))"></p>
                                    <p class="text-xs text-gray-500">Last updated: 30 days ago</p>
                                </div>
                            </template>
                        </div>

                        {{-- Request Change Button --}}
                        <button 
                            @click="openRequestModal(service)"
                            class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm flex items-center justify-center gap-2"
                        >
                            <span>📝</span>
                            <span>Request Price Change</span>
                        </button>
                    </div>
                </template>
            </div>

            {{-- TAB 2: PROMOS --}}
            <div x-show="mainTab === 'promos'" x-transition>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">🎉 Active Promos</h3>
                    <button 
                        @click="openPromoModal()"
                        class="px-3 sm:px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-xs sm:text-sm flex items-center gap-1 sm:gap-2 whitespace-nowrap"
                    >
                        <span>➕</span>
                        <span class="hidden sm:inline">Create Promo</span>
                        <span class="sm:hidden">New</span>
                    </button>
                </div>

                {{-- Promo Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                    <template x-for="promo in promos" :key="promo.id">
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg border-2 border-purple-200 p-3 sm:p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-sm sm:text-base font-bold text-gray-800 mb-1" x-text="promo.title"></h4>
                                    <span 
                                        class="px-2 py-1 rounded-full text-xs font-semibold"
                                        :class="{
                                            'bg-green-100 text-green-700': promo.status === 'active',
                                            'bg-gray-100 text-gray-700': promo.status === 'paused',
                                            'bg-red-100 text-red-700': promo.status === 'expired'
                                        }"
                                        x-text="promo.status.toUpperCase()"
                                    ></span>
                                </div>
                            </div>

                            <div class="space-y-2 mb-3">
                                <div class="flex items-center gap-2 text-xs sm:text-sm">
                                    <span class="text-gray-600">💰</span>
                                    <span class="font-semibold text-purple-700" x-text="promo.type === 'percentage' ? promo.value + '% OFF' : 'Rp ' + formatNumber(promo.value) + ' OFF'"></span>
                                </div>
                                <div class="flex items-center gap-2 text-xs sm:text-sm">
                                    <span class="text-gray-600">📅</span>
                                    <span class="text-gray-700" x-text="promo.startDate + ' - ' + promo.endDate"></span>
                                </div>
                                <div class="flex items-center gap-2 text-xs sm:text-sm">
                                    <span class="text-gray-600">🏢</span>
                                    <span class="text-gray-700" x-text="promo.services.includes('all') ? 'All Services' : promo.services.length + ' Services'"></span>
                                </div>
                                <div x-show="promo.code" class="flex items-center gap-2 text-xs sm:text-sm">
                                    <span class="text-gray-600">📍</span>
                                    <span class="font-mono bg-white px-2 py-1 rounded text-gray-700" x-text="'Code: ' + promo.code"></span>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <button 
                                    @click="editPromo(promo)"
                                    class="flex-1 min-w-[80px] px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 text-xs sm:text-sm font-medium transition"
                                >
                                    ✏️ Edit
                                </button>
                                <button 
                                    @click="togglePromo(promo)"
                                    class="flex-1 min-w-[80px] px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 text-xs sm:text-sm font-medium transition"
                                    x-text="promo.status === 'active' ? '⏸️ Pause' : '▶️ Resume'"
                                >
                                </button>
                                <button 
                                    @click="deletePromo(promo)"
                                    class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 text-xs sm:text-sm font-medium transition"
                                >
                                    🗑️
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Empty State --}}
                <div x-show="promos.length === 0" class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <div class="text-gray-400">
                        <div class="text-5xl mb-3">🎉</div>
                        <p class="text-base font-medium text-gray-600 mb-2">No promos yet</p>
                        <p class="text-sm text-gray-500 mb-4">Create your first promotional offer</p>
                        <button 
                            @click="openPromoModal()"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium inline-flex items-center gap-2"
                        >
                            <span>➕</span>
                            <span>Create Promo</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- TAB 3: PRICE REQUESTS --}}
            <div x-show="mainTab === 'requests'" x-transition>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">📝 Price Change Requests</h3>
                    <button 
                        @click="openRequestModal()"
                        class="px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-xs sm:text-sm flex items-center gap-1 sm:gap-2 whitespace-nowrap"
                    >
                        <span>➕</span>
                        <span class="hidden sm:inline">Submit Request</span>
                        <span class="sm:hidden">New</span>
                    </button>
                </div>

                {{-- Pending Requests --}}
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">⏳ Pending Approval</h4>
                    <div class="space-y-3">
                        <template x-for="request in getPendingRequests()" :key="request.id">
                            <div class="bg-yellow-50 rounded-lg border-2 border-yellow-200 p-3 sm:p-4">
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-sm sm:text-base font-semibold text-gray-800 mb-2" x-text="getServiceName(request.service) + ' - ' + getDurationName(request.duration)"></h4>
                                        
                                        <div class="grid grid-cols-2 gap-3 mb-3">
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Current Price</p>
                                                <p class="text-sm sm:text-base font-semibold text-gray-700" x-text="'Rp ' + formatNumber(request.currentPrice)"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Proposed Price</p>
                                                <p class="text-sm sm:text-base font-semibold text-blue-700" x-text="'Rp ' + formatNumber(request.proposedPrice)"></p>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <p class="text-xs text-gray-500 mb-1">Reason:</p>
                                            <p class="text-xs sm:text-sm text-gray-700" x-text="request.reason"></p>
                                        </div>

                                        <p class="text-xs text-gray-500">
                                            Submitted <span x-text="request.submittedAt"></span> by <span x-text="request.submittedBy"></span>
                                        </p>
                                    </div>

                                    <button 
                                        @click="cancelRequest(request)"
                                        class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-xs sm:text-sm font-medium whitespace-nowrap"
                                    >
                                        ❌ Cancel
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="getPendingRequests().length === 0" class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-500">No pending requests</p>
                    </div>
                </div>

                {{-- History --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">📋 History</h4>
                    <div class="space-y-3">
                        <template x-for="request in getCompletedRequests()" :key="request.id">
                            <div class="bg-white rounded-lg border border-gray-200 p-3 sm:p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h4 class="text-sm font-semibold text-gray-800" x-text="getServiceName(request.service) + ' - ' + getDurationName(request.duration)"></h4>
                                            <span 
                                                class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                                :class="request.status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                                x-text="request.status.toUpperCase()"
                                            ></span>
                                        </div>
                                        <p class="text-xs sm:text-sm text-gray-600 mb-1">
                                            <span x-text="'Rp ' + formatNumber(request.currentPrice)"></span>
                                            →
                                            <span x-text="'Rp ' + formatNumber(request.proposedPrice)"></span>
                                        </p>
                                        <p class="text-xs text-gray-500" x-text="'Reviewed ' + request.reviewedAt"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="getCompletedRequests().length === 0" class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-500">No history yet</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Create/Edit Promo Modal --}}
    <div x-show="showPromoModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-3 sm:px-4">
            <div @click="showPromoModal = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full p-4 sm:p-6 my-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800" x-text="editingPromo ? 'Edit Promo' : 'Create New Promo'"></h3>
                    <button @click="showPromoModal = false" class="text-gray-400 hover:text-gray-600 flex-shrink-0 ml-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-3 sm:space-y-4">
                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Promo Title</label>
                        <input type="text" x-model="promoForm.title" placeholder="e.g., Flash Sale Weekend" class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>

                    {{-- Discount Type & Value --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Discount Type</label>
                            <select x-model="promoForm.type" class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (Rp)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Discount Value</label>
                            <input type="number" x-model="promoForm.value" :placeholder="promoForm.type === 'percentage' ? 'e.g., 20' : 'e.g., 100000'" class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                    </div>

                    {{-- Date Range --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" x-model="promoForm.startDate" class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" x-model="promoForm.endDate" class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                    </div>

                    {{-- Promo Code --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Promo Code (Optional)</label>
                        <input type="text" x-model="promoForm.code" placeholder="e.g., WEEKEND20" class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm uppercase">
                    </div>

                    {{-- Services --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Apply to Services</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" x-model="promoForm.allServices" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">All Services</span>
                            </label>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-4">
                        <button 
                            @click="savePromo()"
                            class="flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold transition text-sm"
                        >
                            <span x-text="editingPromo ? 'Update Promo' : 'Create Promo'"></span>
                        </button>
                        <button 
                            @click="showPromoModal = false"
                            class="px-4 sm:px-6 py-2 sm:py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Submit Price Request Modal --}}
    <div x-show="showRequestModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-3 sm:px-4">
            <div @click="showRequestModal = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-4 sm:p-6 my-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800">Submit Price Change Request</h3>
                    <button @click="showRequestModal = false" class="text-gray-400 hover:text-gray-600 flex-shrink-0 ml-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-3 sm:space-y-4">
                    {{-- Service --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                        <select x-model="requestForm.service" @change="updateCurrentPrice()" class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">-- Select Service --</option>
                            <template x-for="service in services" :key="service.id">
                                <option :value="service.id" x-text="service.icon + ' ' + service.name"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Duration --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
                        <select x-model="requestForm.duration" @change="updateCurrentPrice()" class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">-- Select Duration --</option>
                            <template x-for="duration in durations" :key="duration.id">
                                <option :value="duration.id" x-text="duration.name"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Current Price --}}
                    <div x-show="requestForm.service && requestForm.duration" class="bg-gray-50 rounded-lg p-3 sm:p-4">
                        <p class="text-xs text-gray-500 mb-1">Current Base Price</p>
                        <p class="text-lg sm:text-xl font-bold text-gray-800" x-text="'Rp ' + formatNumber(requestForm.currentPrice)"></p>
                    </div>

                    {{-- Proposed Price --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Proposed New Price</label>
                        <input type="number" x-model="requestForm.proposedPrice" placeholder="e.g., 175000" class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>

                    {{-- Reason --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Change</label>
                        <textarea x-model="requestForm.reason" rows="3" placeholder="Explain why this price change is needed..." class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"></textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-4">
                        <button 
                            @click="submitRequest()"
                            :disabled="!requestForm.service || !requestForm.duration || !requestForm.proposedPrice || !requestForm.reason"
                            :class="(!requestForm.service || !requestForm.duration || !requestForm.proposedPrice || !requestForm.reason) ? 'bg-gray-300 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                            class="flex-1 px-4 sm:px-6 py-2 sm:py-3 text-white rounded-lg font-semibold transition text-sm"
                        >
                            Submit Request
                        </button>
                        <button 
                            @click="showRequestModal = false"
                            class="px-4 sm:px-6 py-2 sm:py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-3 sm:px-4">
            <div @click="showDeleteModal = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-4 sm:p-6">
                <div class="text-center">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                        <span class="text-2xl sm:text-3xl">⚠️</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-2">Delete Promo</h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">
                        Are you sure you want to delete this promo?<br>
                        <span class="text-xs sm:text-sm text-gray-500">This action cannot be undone.</span>
                    </p>

                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <button 
                            @click="confirmDelete()"
                            class="flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition text-sm"
                        >
                            Yes, Delete
                        </button>
                        <button 
                            @click="showDeleteModal = false"
                            class="flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div 
        x-show="showToast" 
        x-transition
        class="fixed top-4 right-4 z-50 max-w-xs sm:max-w-sm"
        :class="toastType === 'success' ? 'bg-green-500' : toastType === 'error' ? 'bg-red-500' : 'bg-blue-500'"
    >
        <div class="text-white px-4 sm:px-6 py-3 sm:py-4 rounded-lg shadow-xl flex items-start gap-2 sm:gap-3">
            <span class="text-xl sm:text-2xl flex-shrink-0">
                <span x-show="toastType === 'success'">✅</span>
                <span x-show="toastType === 'error'">❌</span>
                <span x-show="toastType === 'info'">ℹ️</span>
            </span>
            <div class="min-w-0">
                <p class="font-semibold text-sm break-words" x-text="toastMessage"></p>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function pricingManagement() {
    return {
        mainTab: 'base',
        activeService: 'meeting-room',
        showPromoModal: false,
        showRequestModal: false,
        showDeleteModal: false,
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        editingPromo: null,
        deletingItem: null,

        stats: {
            activePromos: 0,
            pendingRequests: 0
        },

        services: [
            { id: 'meeting-room', name: 'Meeting Room', icon: '🏢' },
            { id: 'private-office', name: 'Private Office', icon: '🚪' },
            { id: 'sharing-room', name: 'Sharing Room', icon: '👥' },
            { id: 'virtual-office', name: 'Virtual Office', icon: '💼' },
            { id: 'coworking-space', name: 'Coworking Space', icon: '🖥️' },
            { id: 'event-space', name: 'Event Space', icon: '🎉' }
        ],

        durations: [
            { id: 'per-hour', name: 'Per Hour' },
            { id: 'half-day', name: 'Half Day (4 hours)' },
            { id: 'full-day', name: 'Full Day (8 hours)' },
            { id: '1-week', name: '1 Week' },
            { id: '1-month', name: '1 Month' }
        ],

        basePrices: {},
        promos: [],
        priceRequests: [],

        promoForm: {
            title: '',
            type: 'percentage',
            value: '',
            startDate: '',
            endDate: '',
            code: '',
            allServices: true,
            services: []
        },

        requestForm: {
            service: '',
            duration: '',
            currentPrice: 0,
            proposedPrice: '',
            reason: ''
        },

        init() {
            this.generateBasePrices();
            this.generateDummyPromos();
            this.generateDummyRequests();
            this.calculateStats();
        },

        generateBasePrices() {
            const prices = {
                'per-hour': 150000,
                'half-day': 500000,
                'full-day': 850000,
                '1-week': 5000000,
                '1-month': 18000000
            };

            this.services.forEach(service => {
                this.basePrices[service.id] = { ...prices };
            });
        },

        generateDummyPromos() {
            this.promos = [
                {
                    id: 1,
                    title: 'Flash Sale Weekend',
                    type: 'percentage',
                    value: 20,
                    services: ['all'],
                    startDate: '2025-10-15',
                    endDate: '2025-10-17',
                    code: 'WEEKEND20',
                    status: 'active',
                    createdBy: 'Admin User'
                },
                {
                    id: 2,
                    title: 'Weekday Special',
                    type: 'fixed',
                    value: 100000,
                    services: ['virtual-office', 'coworking-space'],
                    startDate: '2025-10-14',
                    endDate: '2025-10-31',
                    code: 'WEEKDAY100',
                    status: 'active',
                    createdBy: 'Admin User'
                },
                {
                    id: 3,
                    title: 'Grand Opening Promo',
                    type: 'percentage',
                    value: 30,
                    services: ['all'],
                    startDate: '2025-10-01',
                    endDate: '2025-10-10',
                    code: 'GRAND30',
                    status: 'expired',
                    createdBy: 'Admin User'
                }
            ];
        },

        generateDummyRequests() {
            this.priceRequests = [
                {
                    id: 1,
                    service: 'meeting-room',
                    duration: 'per-hour',
                    currentPrice: 150000,
                    proposedPrice: 175000,
                    reason: 'Market adjustment based on competitor analysis',
                    status: 'pending',
                    submittedAt: '2 days ago',
                    submittedBy: 'Admin User',
                    reviewedBy: null,
                    reviewedAt: null
                },
                {
                    id: 2,
                    service: 'virtual-office',
                    duration: '1-month',
                    currentPrice: 18000000,
                    proposedPrice: 20000000,
                    reason: 'Increased operational costs and facility upgrades',
                    status: 'pending',
                    submittedAt: '1 week ago',
                    submittedBy: 'Admin User',
                    reviewedBy: null,
                    reviewedAt: null
                },
                {
                    id: 3,
                    service: 'private-office',
                    duration: 'full-day',
                    currentPrice: 850000,
                    proposedPrice: 900000,
                    reason: 'Premium amenities added',
                    status: 'approved',
                    submittedAt: '1 month ago',
                    submittedBy: 'Admin User',
                    reviewedBy: 'Super Admin',
                    reviewedAt: '3 weeks ago'
                },
                {
                    id: 4,
                    service: 'event-space',
                    duration: 'full-day',
                    currentPrice: 850000,
                    proposedPrice: 1000000,
                    reason: 'High demand during peak season',
                    status: 'rejected',
                    submittedAt: '2 months ago',
                    submittedBy: 'Admin User',
                    reviewedBy: 'Super Admin',
                    reviewedAt: '1 month ago'
                }
            ];
        },

        calculateStats() {
            this.stats.activePromos = this.promos.filter(p => p.status === 'active').length;
            this.stats.pendingRequests = this.priceRequests.filter(r => r.status === 'pending').length;
        },

        getBasePrice(serviceId, durationId) {
            return this.basePrices[serviceId]?.[durationId] || 0;
        },

        getServiceName(serviceId) {
            return this.services.find(s => s.id === serviceId)?.name || '';
        },

        getDurationName(durationId) {
            return this.durations.find(d => d.id === durationId)?.name || '';
        },

        getPendingRequests() {
            return this.priceRequests.filter(r => r.status === 'pending');
        },

        getCompletedRequests() {
            return this.priceRequests.filter(r => r.status !== 'pending');
        },

        openPromoModal(promo = null) {
            if (promo) {
                this.editingPromo = promo;
                this.promoForm = { ...promo };
            } else {
                this.editingPromo = null;
                this.promoForm = {
                    title: '',
                    type: 'percentage',
                    value: '',
                    startDate: '',
                    endDate: '',
                    code: '',
                    allServices: true,
                    services: []
                };
            }
            this.showPromoModal = true;
        },

        savePromo() {
            if (!this.promoForm.title || !this.promoForm.value || !this.promoForm.startDate || !this.promoForm.endDate) {
                this.showToastMessage('Please fill all required fields', 'error');
                return;
            }

            if (this.editingPromo) {
                // Update existing promo
                const index = this.promos.findIndex(p => p.id === this.editingPromo.id);
                if (index !== -1) {
                    this.promos[index] = {
                        ...this.promos[index],
                        ...this.promoForm,
                        services: this.promoForm.allServices ? ['all'] : this.promoForm.services
                    };
                }
                this.showToastMessage('Promo updated successfully!', 'success');
            } else {
                // Create new promo
                this.promos.push({
                    id: Date.now(),
                    ...this.promoForm,
                    services: this.promoForm.allServices ? ['all'] : this.promoForm.services,
                    status: 'active',
                    createdBy: 'Admin User'
                });
                this.showToastMessage('Promo created successfully!', 'success');
            }

            this.showPromoModal = false;
            this.calculateStats();
        },

        editPromo(promo) {
            this.openPromoModal(promo);
        },

        togglePromo(promo) {
            const index = this.promos.findIndex(p => p.id === promo.id);
            if (index !== -1) {
                this.promos[index].status = this.promos[index].status === 'active' ? 'paused' : 'active';
                this.showToastMessage(`Promo ${this.promos[index].status === 'active' ? 'resumed' : 'paused'} successfully!`, 'success');
                this.calculateStats();
            }
        },

        deletePromo(promo) {
            this.deletingItem = promo;
            this.showDeleteModal = true;
        },

        confirmDelete() {
            const index = this.promos.findIndex(p => p.id === this.deletingItem.id);
            if (index !== -1) {
                this.promos.splice(index, 1);
                this.showToastMessage('Promo deleted successfully!', 'success');
                this.calculateStats();
            }
            this.showDeleteModal = false;
        },

        openRequestModal(service = null) {
            this.requestForm = {
                service: service?.id || '',
                duration: '',
                currentPrice: 0,
                proposedPrice: '',
                reason: ''
            };
            this.showRequestModal = true;
        },

        updateCurrentPrice() {
            if (this.requestForm.service && this.requestForm.duration) {
                this.requestForm.currentPrice = this.getBasePrice(this.requestForm.service, this.requestForm.duration);
            }
        },

        submitRequest() {
            if (!this.requestForm.service || !this.requestForm.duration || !this.requestForm.proposedPrice || !this.requestForm.reason) {
                return;
            }

            this.priceRequests.unshift({
                id: Date.now(),
                service: this.requestForm.service,
                duration: this.requestForm.duration,
                currentPrice: this.requestForm.currentPrice,
                proposedPrice: parseInt(this.requestForm.proposedPrice),
                reason: this.requestForm.reason,
                status: 'pending',
                submittedAt: 'Just now',
                submittedBy: 'Admin User',
                reviewedBy: null,
                reviewedAt: null
            });

            this.showRequestModal = false;
            this.showToastMessage('Price change request submitted successfully!', 'success');
            this.calculateStats();
        },

        cancelRequest(request) {
            const index = this.priceRequests.findIndex(r => r.id === request.id);
            if (index !== -1) {
                this.priceRequests.splice(index, 1);
                this.showToastMessage('Request cancelled successfully!', 'success');
                this.calculateStats();
            }
        },

        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        },

        showToastMessage(message, type = 'success') {
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;
            setTimeout(() => {
                this.showToast = false;
            }, 3000);
        }
    }
}
</script>
@endpush

<style>
[x-cloak] { display: none !important; }

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
@endsection