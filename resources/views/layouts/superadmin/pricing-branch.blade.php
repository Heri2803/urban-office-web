{{-- resources/views/layouts/superadmin/pricing-branch.blade.php --}}
@extends('layouts.superadmin')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl" 
     x-data="branchPricingOverride()"
     x-init="init()">

     {{-- Include Modals dari File Terpisah --}}
        @include('layouts.superadmin.components.request')
    
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">🏢 Branch Pricing Override</h1>
                <p class="text-gray-600 mt-1 text-sm md:text-base">Request custom pricing for your branch</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button x-on:click="openRequestModal()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Request Price Change
                </button>
                <button x-on:click="openHistoryModal()" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    View History
                </button>
            </div>
        </div>
    </div>

    {{-- Branch Selector --}}
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-3">Select Branch to View/Manage:</label>
        <div class="flex flex-col sm:flex-row gap-3">
            <select x-model="state.currentBranch" 
                    x-on:change="loadBranchData()"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Select Branch</option>
                <template x-for="location in state.locations" :key="location.id">
                    <option x-bind:value="location.id" x-text="location.full_name"></option>
                </template>
            </select>
            <div class="flex gap-2 overflow-x-auto sm:overflow-visible">
                <template x-for="location in state.locations" :key="location.id">
                    <button x-on:click="selectBranch(location.id)" 
                            :class="{
                                'bg-blue-600 text-white': state.currentBranch == location.id,
                                'border border-gray-300 text-gray-700 hover:bg-gray-50': state.currentBranch != location.id
                            }"
                            class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors"
                            x-text="location.name">
                    </button>
                </template>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
            <div x-text="stats.activeOverrides" class="text-2xl font-bold text-green-600"></div>
            <div class="text-sm text-gray-600 mt-1">Active Overrides</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
            <div x-text="stats.pendingRequests" class="text-2xl font-bold text-yellow-600"></div>
            <div class="text-sm text-gray-600 mt-1">Pending Requests</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
            <div x-text="stats.approvedThisMonth" class="text-2xl font-bold text-blue-600"></div>
            <div class="text-sm text-gray-600 mt-1">Approved This Month</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
            <div x-text="stats.rejectedThisMonth" class="text-2xl font-bold text-red-600"></div>
            <div class="text-sm text-gray-600 mt-1">Rejected This Month</div>
        </div>
    </div>

    {{-- Service Type Tabs --}}
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="overflow-x-auto">
            <div class="flex border-b">
                <template x-for="service in serviceTabs" :key="service.value">
                    <button x-on:click="switchService(service.value)"
                            :class="{
                                'border-blue-500 text-blue-600 bg-blue-50': state.currentService === service.value,
                                'border-transparent text-gray-500 hover:text-gray-700': state.currentService !== service.value
                            }"
                            class="service-tab whitespace-nowrap px-4 md:px-6 py-3 font-medium text-sm border-b-2">
                        <span x-text="service.label"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>

    {{-- Comparison Table --}}
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
            <span x-text="getCurrentBranchName()"></span> - 
            <span x-text="getCurrentServiceLabel()"></span> Pricing Overview
        </h2>

        {{-- Desktop Table --}}
        <div class="hidden lg:block overflow-x-auto" x-show="filteredPackages.length > 0">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Master Price</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch Override</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difference</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    {{-- Loading State --}}
                    <template x-if="state.isLoading && filteredPackages.length === 0">
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-600 mb-2"></div>
                                    <p class="text-gray-500 text-sm">Loading pricing data...</p>
                                </div>
                            </td>
                        </tr>
                    </template>

                    {{-- Error State --}}
                    <template x-if="state.hasError">
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-red-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-red-600 font-medium mb-2" x-text="state.errorMessage"></p>
                                    <button x-on:click="init()" 
                                            class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-medium hover:bg-red-200 transition-colors">
                                        Retry Loading
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    {{-- Data Rows dengan Safe Checks --}}
                    <template x-for="pkg in filteredPackages" :key="pkg.id">
                        {{-- ⭐️ VALIDASI UTAMA: Pastikan package valid sebelum render --}}
                        <template x-if="isPackageValid(pkg)">
                            <tr class="hover:bg-gray-50 transition-colors"
                                :class="{'opacity-50': pkg.status === 'pending'}">
                                
                                {{-- Package Name --}}
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    <span x-text="safeGet(pkg, 'name', 'Unnamed Package')"></span>
                                    <template x-if="pkg.status === 'pending'">
                                        <span class="ml-1 text-xs text-yellow-600">(Pending)</span>
                                    </template>
                                </td>
                                
                                {{-- Master Price --}}
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    Rp <span x-text="formatCurrency(safeGet(pkg, 'price', 0))"></span>
                                </td>
                                
                                {{-- Branch Override --}}
                                <td class="px-4 py-3 text-sm font-semibold" 
                                    :class="pkg.overridePrice ? 'text-gray-900' : 'text-gray-400'">
                                    
                                    {{-- ⭐️ SOLUSI: Gunakan ternary untuk menghindari x-if error --}}
                                    <span x-text="pkg.overridePrice ? 
                                        'Rp ' + formatCurrency(pkg.overridePrice) : 
                                        '= Master'">
                                    </span>
                                </td>
                                
                                {{-- Difference --}}
                                <td class="px-4 py-3 text-sm font-medium" 
                                    :class="getDifferenceClass(pkg)">
                                    
                                    {{-- ⭐️ SAFE ACCESS: Gunakan safeGet untuk nested properties --}}
                                    <template x-if="safeGet(pkg, 'difference')">
                                        <span x-text="getDifferenceText(pkg)"></span>
                                    </template>
                                    <template x-if="!safeGet(pkg, 'difference')">
                                        <span class="text-gray-400">-</span>
                                    </template>
                                </td>
                                
                                {{-- Status Badge --}}
                                <td class="px-4 py-3">
                                    {{-- ⭐️ TAMBAHKAN VALIDASI: Gunakan safe fallbacks --}}
                                    <span :class="getStatusBadgeClass(pkg)" 
                                        class="px-2 py-1 text-xs font-medium rounded-full inline-flex items-center gap-1"
                                        x-text="getStatusText(pkg) || 'Unknown'">
                                    </span>
                                </td>
                                
                                {{-- Action Buttons --}}
                                <td class="px-4 py-3 text-center">
                                    {{-- ⭐️ SIMPLIFIKASI: Gunakan switch case dengan x-show --}}
                                    
                                    {{-- Master Status --}}
                                    <div x-show="pkg.status === 'master'" class="flex justify-center">
                                        <button x-on:click="quickRequest(pkg)"
                                                class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors flex items-center gap-1"
                                                :disabled="state.isLoading"
                                                :class="{'opacity-50 cursor-not-allowed': state.isLoading}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Request
                                        </button>
                                    </div>
                                    
                                    {{-- Pending Status --}}
                                    <div x-show="pkg.status === 'pending'" class="flex justify-center">
                                        <button x-on:click="viewRequestDetail(pkg.overrideId)"
                                                class="px-3 py-1.5 bg-yellow-50 text-yellow-600 rounded-lg text-sm font-medium hover:bg-yellow-100 transition-colors flex items-center gap-1"
                                                :disabled="state.isLoading">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View
                                        </button>
                                    </div>
                                    
                                    {{-- Active Status --}}
                                    <div x-show="pkg.status === 'active'" class="flex gap-2 justify-center">
                                        <button x-on:click="editOverride(pkg.overrideId)"
                                                class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors flex items-center gap-1"
                                                :disabled="state.isLoading">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </button>
                                        <button x-on:click="removeOverride(pkg.overrideId)"
                                                class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors flex items-center gap-1"
                                                :disabled="state.isLoading"
                                                title="Remove override">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Remove
                                        </button>
                                    </div>
                                    
                                    {{-- Fallback jika status tidak dikenali --}}
                                    <div x-show="!['master', 'pending', 'active'].includes(pkg.status)" class="text-center">
                                        <span class="text-xs text-gray-400">No actions</span>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        
                        {{-- Fallback untuk invalid package --}}
                        <template x-if="!isPackageValid(pkg)">
                            <tr class="bg-gray-50">
                                <td colspan="6" class="px-4 py-2 text-center text-xs text-gray-500">
                                    Invalid package data
                                </td>
                            </tr>
                        </template>
                    </template>

                    {{-- Empty State --}}
                    <template x-if="!state.isLoading && !state.hasError && filteredPackages.length === 0 && state.currentLocationId">
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-gray-500 font-medium mb-2">No pricing data available</p>
                                    <p class="text-gray-400 text-sm mb-4">
                                        <span x-text="currentBranchName"></span> - 
                                        <span x-text="currentServiceLabel"></span>
                                    </p>
                                    <button x-on:click="openRequestModal()"
                                            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Create First Override
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    {{-- Initial Empty State --}}
                    <template x-if="!state.isLoading && !state.hasError && !state.currentLocationId">
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <p class="text-gray-500 font-medium mb-2">Select a branch to view pricing</p>
                                    <p class="text-gray-400 text-sm">Choose from the branch selector above</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Mobile/Tablet Card View --}}
        <div class="lg:hidden space-y-4" x-show="filteredPackages.length > 0">
            <template x-for="pkg in filteredPackages" :key="pkg.id">
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="font-semibold text-gray-900" x-text="pkg.name"></h3>
                        <span :class="getStatusBadgeClass(pkg)" 
                            class="px-2 py-1 text-xs font-medium rounded-full"
                            x-text="getStatusText(pkg)"></span>
                    </div>
                    
                    <div class="space-y-2 mb-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Master Price:</span>
                            <span class="text-gray-900">
                                Rp <span x-text="formatCurrency(pkg.price)"></span>
                            </span>
                        </div>
                        
                        {{-- ⭐️ PERBAIKAN: GANTI template x-if DENGAN x-show --}}
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Branch Override:</span>
                            <span :class="pkg.overridePrice ? 'text-gray-900 font-semibold' : 'text-gray-400'">
                                <span x-show="pkg.overridePrice">
                                    Rp <span x-text="formatCurrency(pkg.overridePrice)"></span>
                                </span>
                                <span x-show="!pkg.overridePrice">
                                    = Master
                                </span>
                            </span>
                        </div>
                        
                        {{-- ⭐️ PERBAIKAN: GANTI template x-if DENGAN x-show --}}
                        <div x-show="getDifferenceText(pkg)" class="flex justify-between text-sm">
                            <span class="text-gray-600">Difference:</span>
                            <span class="font-medium" 
                                :class="getDifferenceClass(pkg)"
                                x-text="getDifferenceText(pkg)"></span>
                        </div>
                    </div>
                    
                    {{-- ⭐️ PERBAIKAN: GANTI template x-if DENGAN x-show --}}
                    <div class="flex gap-2">
                        {{-- Master Status --}}
                        <div x-show="pkg.status === 'master'" class="w-full">
                            <button x-on:click="quickRequest(pkg)"
                                    class="w-full px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                                Request Override
                            </button>
                        </div>
                        
                        {{-- Pending Status --}}
                        <div x-show="pkg.status === 'pending'" class="w-full">
                            <button x-on:click="viewRequestDetail(pkg.overrideId)"
                                    class="w-full px-3 py-2 bg-yellow-50 text-yellow-600 rounded-lg text-sm font-medium hover:bg-yellow-100">
                                View Request
                            </button>
                        </div>
                        
                        {{-- Active Status --}}
                        <div x-show="pkg.status === 'active'" class="flex gap-2 w-full">
                            <button x-on:click="editOverride(pkg.overrideId)"
                                    class="flex-1 px-3 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100">
                                Edit
                            </button>
                            <button x-on:click="removeOverride(pkg.overrideId)"
                                    class="flex-1 px-3 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100">
                                Remove
                            </button>
                        </div>
                        
                        {{-- Fallback untuk status lain --}}
                        <div x-show="!['master', 'pending', 'active'].includes(pkg.status)" 
                            class="w-full text-center py-2">
                            <span class="text-xs text-gray-400">No actions</span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Empty State --}}
        <div x-show="filteredPackages.length === 0 && state.currentService" class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500 text-lg font-medium">No pricing data available</p>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div id="deleteModal"
            x-show="state.isDeleteModalOpen"
            x-cloak
            x-transition.opacity
            style="display: none;" {{-- x-cloak akan hide ini --}}
            class="fixed inset-0 z-[9999] overflow-y-auto"
            aria-labelledby="delete-modal-title"
            role="dialog"
            aria-modal="true">
            
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                {{-- Backdrop --}}
                <div x-show="state.isDeleteModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    aria-hidden="true"
                    x-on:click="state.isDeleteModalOpen = false">
                </div>

                {{-- Modal panel --}}
                <div x-show="state.isDeleteModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    
                    {{-- Modal content --}}
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.974-.833-2.744 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="delete-modal-title">
                                Delete Override
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to delete 
                                    <span class="font-semibold" x-text="state.deleteItemName"></span>?
                                    This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="button"
                                x-on:click="confirmDelete()"
                                :disabled="state.isDeleting"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <span x-text="state.isDeleting ? 'Deleting...' : 'Delete'"></span>
                        </button>
                        <button type="button"
                                x-on:click="state.isDeleteModalOpen = false"
                                :disabled="state.isDeleting"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Alpine.js Component --}}
<script>
function branchPricingOverride() {
    return {
        // =============== STATE MANAGEMENT ===============
        state: {
            // Data dari API
            locations: [],
            roomTypes: [],
            durationTypes: [],
            coffeeBreakOptions: [],
            currentUser: null,
            
            // Data untuk display
            activePrices: [],
            pendingRequests: [],

            isLoading: false,
            isInitialized: false,
            hasError: false,
            errorMessage: '',
            
            // UI State
            currentLocationId: null,
            currentRoomTypeId: null,
            currentServiceSlug: 'meeting-room',
            isRequestModalOpen: false,
            isHistoryModalOpen: false,
            selectedPackage: null,
            isSubmitting: false,
            formError: false,
            formErrorMessage: '',
            formErrors: {},

            isDeleteModalOpen: false,
            deleteItemId: null,
            deleteItemName: '',
            deleteConfirmation: '',
            deleteReason: '',
            isDeleting: false,
            deleteError: '',
            
            // Form Data
            formData: {
                room_type_id: '',
                room_id: '',
                duration_type: 'hours',
                duration: 1,
                total_price: '',
                coffee_break_option: 0,
                coffee_break_price: 0,
                deposit: 0,
                location_ids: [],
                reason: '',
                effective_date: '',
                notes: '',
                documents: []
            }
        },

        // =============== CONSTANTS ===============
        serviceTabs: [
            { value: 'meeting-room', label: 'Meeting Room' },
            { value: 'private-office', label: 'Private Office' },
            { value: 'sharing-room', label: 'Sharing Room' },
            { value: 'coworking-space', label: 'Coworking Space' },
            { value: 'virtual-office', label: 'Virtual Office' },
            { value: 'event-space', label: 'Event Space' }
        ],

        durationTypes: [
            { value: 'hours', label: 'Per Jam' },
            { value: 'days', label: 'Per Hari' },
            { value: 'months', label: 'Per Bulan' },
            { value: 'weeks', label: 'Per Minggu' },
            { value: 'years', label: 'Per Tahun' }
        ],

        // =============== COMPUTED PROPERTIES ===============
        get currentBranchName() {
            if (!this.state.currentLocationId) return 'Select Branch';
            const location = this.state.locations.find(l => l.id == this.state.currentLocationId);
            return location ? location.name : 'Unknown';
        },

        get currentServiceLabel() {
            const service = this.serviceTabs.find(s => s.value === this.state.currentServiceSlug);
            return service ? service.label : 'Unknown';
        },

        get filteredPackages() {
            console.log('🔄 Calculating filteredPackages...');
            
            if (!this.state.currentLocationId) {
                console.log('⚠️ No location selected');
                return [];
            }
            
            console.log('📍 Current location ID:', this.state.currentLocationId);
            console.log('🎯 Current service slug:', this.state.currentServiceSlug);
            
            const location = this.state.locations.find(l => l.id == this.state.currentLocationId);
            if (!location || !location.prices) {
                console.log('⚠️ No location or prices found');
                return [];
            }
            
            console.log('🏢 Location:', location.name);
            console.log('💰 Location prices count:', location.prices?.length || 0);
            
            // Room type mapping
            const roomTypeMapping = {
                'meeting-room': 2,
                'private-office': 1,
                'event-space': 3,
                'coworking-space': 4,
                'virtual-office': 5,
                'sharing-room': 6
            };
            
            const currentRoomTypeId = roomTypeMapping[this.state.currentServiceSlug];
            console.log('📦 Current room type ID:', currentRoomTypeId);
            
            if (!currentRoomTypeId) {
                console.log('⚠️ No room type mapping found');
                return [];
            }
            
            // Filter location prices
            const locationPrices = location.prices.filter(
                price => price.room_type_id == currentRoomTypeId
            );
            
            console.log('📊 Filtered location prices:', locationPrices.length);
            locationPrices.forEach((price, idx) => {
                console.log(`  Price ${idx}:`, {
                    id: price.id,
                    duration_type: price.duration_type,
                    duration: price.duration,
                    base_price: price.base_price
                });
            });
            
            // Group by duration
            const packageMap = new Map();
            
            locationPrices.forEach(price => {
                const key = `${price.duration_type}_${price.duration}`;
                if (!packageMap.has(key)) {
                    packageMap.set(key, {
                        id: price.id,
                        name: this.formatPackageName(price),
                        price: parseFloat(price.base_price),
                        duration_type: price.duration_type,
                        duration: price.duration,
                        coffee_break_option: price.coffee_break_option,
                        coffee_break_price: price.coffee_break_price,
                        deposit: price.deposit
                    });
                }
            });
            
            console.log('📦 Package map size:', packageMap.size);
            
            // Convert to array dan cari override
            const result = Array.from(packageMap.values()).map(pkg => {
                console.log(`\n🔍 Processing package: ${pkg.name}`);
                console.log('   Package details:', {
                    duration_type: pkg.duration_type,
                    duration: pkg.duration,
                    price: pkg.price
                });
                
                // ⭐️ DEBUG: Cari active override
                console.log('   Active prices count:', this.state.activePrices.length);
                
                const activeOverride = this.state.activePrices.find(ap => {
                    const match = ap.location_id == this.state.currentLocationId &&
                                ap.room_type_id == currentRoomTypeId &&
                                ap.duration_type === pkg.duration_type &&
                                ap.duration == pkg.duration;
                    
                    if (match) {
                        console.log('   ✅ FOUND MATCH!');
                        console.log('   Override details:', {
                            id: ap.id,
                            base_price: ap.base_price,
                            duration_type: ap.duration_type,
                            duration: ap.duration
                        });
                    }
                    
                    return match;
                });
                
                let status = 'master';
                let overridePrice = null;
                let overrideId = null;
                
                if (activeOverride) {
                    status = 'active';
                    overridePrice = parseFloat(activeOverride.base_price);
                    overrideId = activeOverride.id;
                    console.log(`   🟢 Status: ACTIVE (override: ${overridePrice})`);
                } else {
                    console.log(`   🔵 Status: MASTER (no override)`);
                    
                    // ⭐️ DEBUG: Cek semua active prices untuk troubleshoot
                    this.state.activePrices.forEach((ap, idx) => {
                        console.log(`   Active price ${idx}:`, {
                            loc_match: ap.location_id == this.state.currentLocationId,
                            room_match: ap.room_type_id == currentRoomTypeId,
                            dur_type_match: ap.duration_type === pkg.duration_type,
                            dur_match: ap.duration == pkg.duration,
                            details: `${ap.duration_type}_${ap.duration}`
                        });
                    });
                }
                
                return {
                    ...pkg,
                    overridePrice,
                    overrideId,
                    status,
                    difference: this.calculateDifference(pkg.price, overridePrice)
                };
            });
            
            console.log('✅ Final filtered packages:', result.length);
            console.log('========================================\n');
            
            return result;
        },

        get stats() {
            const locationId = this.state.currentLocationId;
            
            if (!locationId) {
                return {
                    activeOverrides: 0,
                    pendingRequests: 0,
                    approvedThisMonth: 0,
                    rejectedThisMonth: 0
                };
            }
            
            const locationActivePrices = this.state.activePrices.filter(
                price => price.location_id == locationId
            );
            
            const locationPendingRequests = this.state.pendingRequests.filter(
                request => request.location_id == locationId
            );
            
            // Hitung untuk bulan ini
            const now = new Date();
            const thisMonth = now.getMonth();
            const thisYear = now.getFullYear();
            
            const approvedThisMonth = locationActivePrices.filter(price => {
                if (!price.reviewed_at) return false;
                const reviewed = new Date(price.reviewed_at);
                return reviewed.getMonth() === thisMonth && 
                       reviewed.getFullYear() === thisYear;
            }).length;
            
            return {
                activeOverrides: locationActivePrices.length,
                pendingRequests: locationPendingRequests.length,
                approvedThisMonth: approvedThisMonth,
                rejectedThisMonth: 0 // Placeholder
            };
        },

        get historyData() {
            if (!this.state.currentLocationId) return [];
            
            const allRequests = [...this.state.activePrices, ...this.state.pendingRequests];
            
            return allRequests
                .filter(request => request.location_id == this.state.currentLocationId)
                .sort((a, b) => new Date(b.requested_at) - new Date(a.requested_at))
                .map(request => {
                    const roomType = this.state.roomTypes.find(rt => rt.id === request.room_type_id);
                    const location = this.state.locations.find(loc => loc.id === request.location_id);
                    
                    // Format change text
                    let changeText = '';
                    if (request.previous_price) {
                        const change = ((parseFloat(request.base_price) - parseFloat(request.previous_price)) / parseFloat(request.previous_price)) * 100;
                        const sign = change >= 0 ? '+' : '';
                        changeText = `${this.formatCurrency(parseFloat(request.previous_price))} → ${this.formatCurrency(parseFloat(request.base_price))} (${sign}${change.toFixed(1)}%)`;
                    } else {
                        changeText = `${this.formatCurrency(parseFloat(request.base_price))} (New)`;
                    }
                    
                    return {
                        id: request.id,
                        date: new Date(request.requested_at).toISOString().split('T')[0],
                        package: `${roomType?.name || 'Unknown'} - ${request.duration} ${request.duration_type}`,
                        change: changeText,
                        reason: request.request_reason || 'No reason provided',
                        status: request.request_status,
                        effective: request.effective_date ? 
                            new Date(request.effective_date).toLocaleDateString('id-ID') : 'Immediate',
                        reviewedBy: request.reviewed_by || 'Pending',
                        comment: request.notes || ''
                    };
                });
        },

        // =============== METHODS ===============
        async init() {
            // ⭐️ WAIT FOR ALPINE TO INITIALIZE DOM
            await this.$nextTick();
            
            this.state.isLoading = true;
            
            try {
                await this.loadAllData();
                
                if (this.state.locations.length > 0) {
                    this.state.currentLocationId = this.state.locations[0].id;
                    this.state.isInitialized = true;
                }
                
            } catch (error) {
                console.error('Initialization error:', error);
                this.state.hasError = true;
                this.state.errorMessage = 'Failed to initialize. Please refresh.';
                
            } finally {
                this.state.isLoading = false;
            }
        },

        async loadAllData() {
            console.log('Starting loadAllData...');
            
            this.state.isLoading = true;
            this.state.hasError = false;
            
            try {
                // ⭐️ GUNAKAN Promise.all BUKAN Promise.allSettled untuk lebih strict
                const [formDataResult, activePricesResult] = await Promise.all([
                    this.loadFormData(),
                    this.loadActivePrices()
                ]);
                
                console.log('loadAllData completed:', {
                    formData: formDataResult.success,
                    activePrices: activePricesResult?.success
                });
                
                // Set pendingRequests sebagai empty array
                this.state.pendingRequests = [];
                
                // ⭐️ INISIALISASI DEFAULT JIKA ADA DATA LOCATION
                if (this.state.locations.length > 0 && !this.state.currentLocationId) {
                    this.state.currentLocationId = this.state.locations[0].id;
                    console.log('Auto-selected first location:', this.state.currentLocationId);
                }
                
            } catch (error) {
                console.error('Unexpected error in loadAllData:', error);
                this.state.hasError = true;
                this.state.errorMessage = 'Failed to load initial data. Please refresh.';
                
            } finally {
                this.state.isLoading = false;
            }
        },

        async loadFormData() {
            try {
                const response = await fetch('/superadmin/api/generate/form-data');
                const data = await response.json();
                
                if (data.success) {
                    this.state.locations = data.data.locations || [];
                    this.state.roomTypes = data.data.roomTypes || [];
                    this.state.durationTypes = data.data.durationTypes || [];
                    this.state.coffeeBreakOptions = data.data.coffeeBreakOptions || [];
                    this.state.currentUser = data.data.currentUser;
                    
                    console.log('Form data loaded:', {
                        locations: this.state.locations.length,
                        roomTypes: this.state.roomTypes.length
                    });
                    
                    return { success: true, data: data.data }; // ⭐️ RETURN VALUE!
                } else {
                    console.error('API returned error:', data);
                    return { success: false, error: data.message };
                }
                
            } catch (error) {
                console.error('Error loading form data:', error);
                return { success: false, error: error.message }; // ⭐️ RETURN VALUE!
            }
        },

        async loadActivePrices() {
            try {
                console.log('🔍 Loading active prices...');
                
                const response = await fetch('/superadmin/api/generate/my-requests');
                const data = await response.json();
                
                console.log('📦 API Response:', data);
                
                if (data.success) {
                    // ⭐️ DEBUG: Cek struktur data
                    console.log('📊 Data structure:', {
                        hasRequests: !!data.requests,
                        hasData: !!data.requests?.data,
                        isArray: Array.isArray(data.requests?.data),
                        count: data.requests?.data?.length
                    });
                    
                    // Handle berbagai format response
                    let allRequests = [];
                    
                    if (Array.isArray(data.requests)) {
                        allRequests = data.requests;
                    } else if (Array.isArray(data.requests?.data)) {
                        allRequests = data.requests.data;
                    } else if (Array.isArray(data.data)) {
                        allRequests = data.data;
                    }
                    
                    console.log('📋 All requests:', allRequests);
                    
                    // Filter active prices
                    this.state.activePrices = allRequests.filter(
                        request => request.request_status === 'active'
                    );
                    
                    console.log(`✅ Loaded ${this.state.activePrices.length} active prices`);
                    
                    // ⭐️ LOG detail active prices
                    this.state.activePrices.forEach((price, index) => {
                        console.log(`Active price ${index}:`, {
                            id: price.id,
                            location_id: price.location_id,
                            room_type_id: price.room_type_id,
                            duration_type: price.duration_type,
                            duration: price.duration,
                            base_price: price.base_price,
                            status: price.request_status
                        });
                    });
                    
                    return { success: true, count: this.state.activePrices.length };
                    
                } else {
                    console.error('❌ API returned error:', data);
                    this.state.activePrices = [];
                    return { success: false, error: data.message };
                }
                
            } catch (error) {
                console.error('❌ Error loading active prices:', error);
                this.state.activePrices = [];
                return { success: false, error: error.message };
            }
        },

        // =============== BRANCH DATA LOADING ===============
        async loadBranchData() {
            console.log('Loading branch data for:', this.state.currentLocationId);
            
            if (!this.state.currentLocationId) {
                console.warn('No branch selected');
                return;
            }
            
            this.state.isLoading = true;
            
            try {
                // Refresh active prices untuk branch ini
                await this.loadActivePrices();
                
                console.log('Branch data loaded successfully');
                
            } catch (error) {
                console.error('Error loading branch data:', error);
                this.showNotification('Failed to load branch data', 'error');
                
            } finally {
                this.state.isLoading = false;
            }
        },

        // Juga tambahkan selectBranch() jika belum ada:
        selectBranch(branchId) {
            console.log('Selecting branch:', branchId);
            this.state.currentLocationId = branchId;
            
            // Load data untuk branch yang dipilih
            this.$nextTick(() => {
                this.loadBranchData();
            });
        },

        // =============== FORM HELPERS ===============
        updateReasonCount(event) {
            const textarea = event.target;
            const length = textarea.value.length;
            const maxLength = textarea.getAttribute('maxlength') || 500;
            
            // Update counter jika ada elemen counter
            const counter = document.getElementById('reasonCounter');
            if (counter) {
                counter.textContent = `${length}/${maxLength}`;
                
                // Update color based on length
                if (length < 20) {
                    counter.classList.add('text-red-500');
                    counter.classList.remove('text-yellow-500', 'text-green-500');
                } else if (length < 50) {
                    counter.classList.add('text-yellow-500');
                    counter.classList.remove('text-red-500', 'text-green-500');
                } else {
                    counter.classList.add('text-green-500');
                    counter.classList.remove('text-red-500', 'text-yellow-500');
                }
            }
            
            // Juga update validation state
            if (length < 20) {
                this.state.formErrors.reason = 'Reason must be at least 20 characters';
            } else {
                delete this.state.formErrors.reason;
            }
        },

        // Atau versi lebih simple:
        updateReasonCount(event) {
            // Simple version - hanya untuk menghilangkan error
            console.log('Reason updated:', event.target.value.length, 'characters');
        },

        // =============== UTILITY METHODS ===============
        formatCurrency(amount) {
            if (!amount && amount !== 0) return '0';
            return new Intl.NumberFormat('id-ID').format(amount);
        },

        formatPackageName(price) {
            const durationText = this.formatDuration(price);
            return `${price.room_type_name} - ${durationText}`;
        },

        formatDuration(price) {
            if (price.duration_type === 'hours') {
                return `${price.duration} Hour${price.duration > 1 ? 's' : ''}`;
            } else if (price.duration_type === 'days') {
                return `${price.duration} Day${price.duration > 1 ? 's' : ''}`;
            } else if (price.duration_type === 'months') {
                return `${price.duration} Month${price.duration > 1 ? 's' : ''}`;
            } else if (price.duration_type === 'weeks') {
                return `${price.duration} Week${price.duration > 1 ? 's' : ''}`;
            } else if (price.duration_type === 'years') {
                return `${price.duration} Year${price.duration > 1 ? 's' : ''}`;
            }
            return `${price.duration} ${price.duration_type}`;
        },

        calculateDifference(masterPrice, overridePrice) {
            if (!overridePrice && overridePrice !== 0) return null;
            
            const diff = overridePrice - masterPrice;
            const diffPercent = ((diff / masterPrice) * 100).toFixed(1);
            
            return {
                amount: diff,
                percent: diffPercent,
                isPositive: diff > 0,
                isNegative: diff < 0
            };
        },

        getDifferenceText(pkg) {
            if (!pkg.difference) return '';
            
            const sign = pkg.difference.amount > 0 ? '+' : '';
            const amount = Math.abs(pkg.difference.amount);
            
            return `${sign}Rp ${this.formatCurrency(amount)} (${pkg.difference.percent}%)`;
        },

        getDifferenceClass(pkg) {
            if (!pkg.difference) return 'text-gray-600';
            
            if (pkg.difference.amount < 0) {
                return 'text-green-600';
            } else if (pkg.difference.amount > 0) {
                return 'text-red-600';
            }
            return 'text-gray-600';
        },

        getStatusBadgeClass(pkg) {
            const classes = {
                'active': 'bg-green-100 text-green-800',
                'pending': 'bg-yellow-100 text-yellow-800',
                'master': 'bg-blue-100 text-blue-800',
                'rejected': 'bg-red-100 text-red-800'
            };
            return classes[pkg.status] || classes.master;
        },

        getStatusText(pkg) {
            const texts = {
                'active': '🟢 Active',
                'pending': '🟡 Pending',
                'master': '🔵 Master',
                'rejected': '🔴 Rejected'
            };
            return texts[pkg.status] || texts.master;
        },

        // =============== BRANCH & SERVICE METHODS ===============
        selectBranch(branchId) {
            this.state.currentLocationId = branchId;
        },

        switchService(serviceSlug) {
            this.state.currentServiceSlug = serviceSlug;
        },

        getCurrentBranchName() {
            return this.currentBranchName;
        },

        getCurrentServiceLabel() {
            return this.currentServiceLabel;
        },

        // =============== MODAL METHODS ===============
        openRequestModal(packageData = null) {
            // Validasi sebelum buka modal
            if (!this.state.currentLocationId) {
                this.showNotification('Please select a branch first', 'error');
                return;
            }
            
            this.state.selectedPackage = packageData;
            this.state.isRequestModalOpen = true;
            
            // Reset form errors
            this.state.formError = false;
            this.state.formErrors = {};
            
            // ⭐️ FIX: SET location_ids ke currentLocationId
            this.state.formData.location_ids = [this.state.currentLocationId];
            
            // Pre-fill form jika ada package data
            if (packageData) {
                this.$nextTick(() => {
                    this.state.formData = {
                        ...this.state.formData, // Pertahankan location_ids yang sudah di-set
                        room_type_id: this.getRoomTypeIdFromSlug(this.state.currentServiceSlug) || '',
                        duration_type: packageData.duration_type || 'hours',
                        duration: packageData.duration || 1,
                        total_price: packageData.overridePrice || packageData.price || '',
                        effective_date: this.getTodayDate(),
                        // ⭐️ location_ids sudah di-set di atas, jangan di-override
                    };
                });
            } else {
                // Reset form untuk request baru
                this.resetForm();
                // ⭐️ SET location_ids setelah reset
                this.state.formData.location_ids = [this.state.currentLocationId];
            }
            
            console.log('Modal opened with location_ids:', this.state.formData.location_ids);
        },

        getRoomTypeIdFromSlug(slug) {
            const mapping = {
                'meeting-room': 2,
                'private-office': 1,
                'event-space': 3,
                'coworking-space': 4,
                'virtual-office': 5,
                'sharing-room': 6
            };
            return mapping[slug] || '';
        },

        closeRequestModal() {
            this.state.isRequestModalOpen = false;
            this.state.selectedPackage = null;
            this.resetForm();
        },

        quickRequest(pkg) {
            this.openRequestModal(pkg);
        },

        resetForm() {
            this.state.formData = {
                room_type_id: '',
                room_id: '',
                duration_type: 'hours',
                duration: 1,
                total_price: '',
                coffee_break_option: 0,
                coffee_break_price: 0,
                deposit: 0,
                location_ids: [],
                reason: '',
                effective_date: '',
                notes: '',
                documents: []
            };
            this.state.formError = false;
            this.state.formErrors = {};
        },

        getTodayDate() {
            return new Date().toISOString().split('T')[0];
        },

        // =============== FORM SUBMISSION ===============
        async submitRequest() {
            console.log('=== SUBMIT REQUEST START ===');
            
            // ⭐️ VALIDASI: Pastikan location_ids ada
            if (!this.state.formData.location_ids || this.state.formData.location_ids.length === 0) {
                // Set ke current location jika kosong
                if (this.state.currentLocationId) {
                    this.state.formData.location_ids = [this.state.currentLocationId];
                    console.log('Auto-set location_ids to current:', this.state.formData.location_ids);
                } else {
                    this.showNotification('Please select a branch first', 'error');
                    return;
                }
            }
            
            // Validasi form
            if (!this.validateForm()) {
                console.log('Client validation failed');
                return;
            }
            
            this.state.isSubmitting = true;
            
            try {
                // Payload
                const payload = {
                    // Room & duration
                    room_type_id: parseInt(this.state.formData.room_type_id),
                    duration_type: this.state.formData.duration_type || 'hours',
                    duration: parseInt(this.state.formData.duration) || 1,
                    
                    // Prices
                    total_price: parseFloat(this.state.formData.total_price),
                    coffee_break_option: parseInt(this.state.formData.coffee_break_option) || 0,
                    coffee_break_price: parseFloat(this.state.formData.coffee_break_price) || 0,
                    deposit: parseFloat(this.state.formData.deposit) || 0,
                    
                    // ⭐️ GUNAKAN location_ids dari state
                    location_ids: this.state.formData.location_ids,
                    
                    // Request details
                    reason: this.state.formData.reason || '',
                    effective_date: this.state.formData.effective_date || null,
                    notes: this.state.formData.notes || ''
                };
                
                console.log('Payload location_ids:', payload.location_ids);
                
                // Buat FormData
                const formData = new FormData();
                
                // Append fields
                for (const [key, value] of Object.entries(payload)) {
                    if (key === 'location_ids') {
                        // ⭐️ PASTIKAN location_ids adalah array
                        const locationIds = Array.isArray(value) ? value : [value];
                        locationIds.forEach(id => {
                            console.log(`Appending location_ids[]: ${id}`);
                            formData.append('location_ids[]', id);
                        });
                    } else if (value === null || value === undefined || value === '') {
                        // Skip empty values
                        console.log(`Skipping empty ${key}`);
                    } else {
                        console.log(`Appending ${key}: ${value}`);
                        formData.append(key, value);
                    }
                }
                
                // Debug FormData
                console.log('FormData entries:');
                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }
                
                // Kirim request
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const response = await fetch('/superadmin/api/generate/submit', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken.content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                console.log('Response status:', response.status);
                
                const result = await response.json();
                console.log('API Response:', result);
                
                if (response.status === 422) {
                    console.log('Validation errors:', result.errors);
                    this.state.formErrors = result.errors || {};
                    this.state.formError = true;
                    this.state.formErrorMessage = result.message || 'Validation error';
                    this.showNotification('Validation error: ' + result.message, 'error');
                    return;
                }
                
                if (result.success) {
                    this.showNotification(result.message, 'success');
                    this.closeRequestModal();
                    await this.loadActivePrices();
                } else {
                    throw new Error(result.message || 'Submission failed');
                }
                
            } catch (error) {
                console.error('Submit error:', error);
                this.showNotification('Error: ' + error.message, 'error');
            } finally {
                this.state.isSubmitting = false;
                console.log('=== SUBMIT REQUEST END ===');
            }
        },

        validateForm() {
            const errors = {};
            const data = this.state.formData;
            
            // Room Type validation
            if (!data.room_type_id) {
                errors.room_type_id = 'Please select a room type';
            }
            
            // Duration validation
            if (!data.duration || data.duration < 1) {
                errors.duration = 'Duration must be at least 1';
            }
            
            // Price validation
            const price = parseFloat(data.total_price);
            if (isNaN(price) || price < 10000) {
                errors.total_price = 'Price must be at least Rp 10,000';
            }
            
            // Reason validation
            if (!data.reason || data.reason.length < 20) {
                errors.reason = 'Reason must be at least 20 characters';
            } else if (data.reason.length > 500) {
                errors.reason = 'Reason cannot exceed 500 characters';
            }
            
            // Effective date validation (jika diisi)
            if (data.effective_date) {
                const effectiveDate = new Date(data.effective_date);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (effectiveDate < today) {
                    errors.effective_date = 'Effective date cannot be in the past';
                }
            }
            
            // Coffee break price validation (jika coffee break dipilih)
            if (data.coffee_break_option && data.coffee_break_option !== 'none') {
                const coffeePrice = parseFloat(data.coffee_break_price);
                if (isNaN(coffeePrice) || coffeePrice < 0) {
                    errors.coffee_break_price = 'Invalid coffee break price';
                }
            }
            
            // Deposit validation (jika diisi)
            if (data.deposit) {
                const deposit = parseFloat(data.deposit);
                if (isNaN(deposit) || deposit < 0) {
                    errors.deposit = 'Invalid deposit amount';
                }
            }
            
            // Set errors
            if (Object.keys(errors).length > 0) {
                this.state.formErrors = errors;
                this.state.formError = true;
                this.state.formErrorMessage = 'Please fix the errors below';
                return false;
            }
            
            return true;
        },

        // =============== OTHER METHODS ===============
        async editOverride(overrideId) {
            const override = this.state.activePrices.find(o => o.id === overrideId);
            if (override) {
                // Buat package data dari override
                const packageData = {
                    id: override.id,
                    name: `${override.room_type?.name || 'Unknown'} - ${override.duration} ${override.duration_type}`,
                    price: parseFloat(override.base_price),
                    duration_type: override.duration_type,
                    duration: override.duration,
                    coffee_break_option: override.coffee_break_option,
                    coffee_break_price: parseFloat(override.coffee_break_price || 0),
                    deposit: parseFloat(override.deposit || 0)
                };
                
                this.openRequestModal(packageData);
                this.state.formData.reason = override.request_reason || '';
                this.state.formData.notes = override.notes || '';
                
                if (override.effective_date) {
                    const date = new Date(override.effective_date);
                    this.state.formData.effective_date = date.toISOString().split('T')[0];
                }
            }
        },

        async removeOverride(overrideId) {
            const override = this.state.activePrices.find(o => o.id == overrideId);
            if (!override) return;
            
            const roomType = this.state.roomTypes.find(rt => rt.id == override.room_type_id);
            const itemName = roomType ? `${roomType.name} - ${override.duration} ${override.duration_type}` : 'Override';
            
            // Tampilkan modal simple
            this.state.deleteItemId = overrideId;
            this.state.deleteItemName = itemName;
            this.state.isDeleteModalOpen = true;
        },

        async confirmDelete() {
            console.log('🗑️ Deleting item ID:', this.state.deleteItemId);
            
            // Validasi sederhana
            if (!this.state.deleteItemId) {
                this.showNotification('No item selected', 'error');
                return;
            }
            
            this.state.isDeleting = true;
            
            try {
                // ⭐ GUNAKAN FORCE DELETE (lebih straightforward)
                const endpoint = `/superadmin/api/generate/${this.state.deleteItemId}/force-delete`;
                console.log('🔗 Endpoint:', endpoint);
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                
                const response = await fetch(endpoint, {
                    method: 'POST', // ⭐ INGAT: force-delete pakai POST
                    headers: {
                        'X-CSRF-TOKEN': csrfToken.content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        confirmation: 'PERMANENT_DELETE'
                    })
                });
                
                console.log('📊 Response status:', response.status);
                
                const result = await response.json();
                console.log('📦 API Response:', result);
                
                if (response.ok && result.success) {
                    // Success
                    this.showNotification(result.message || 'Deleted successfully', 'success');
                    
                    // Hapus dari state langsung
                    this.state.activePrices = this.state.activePrices.filter(
                        price => price.id != this.state.deleteItemId
                    );
                    
                    // Close modal
                    this.state.isDeleteModalOpen = false;
                    
                } else {
                    // Error dari server
                    throw new Error(result.message || 'Delete failed');
                }
                
            } catch (error) {
                console.error('❌ Delete error:', error);
                this.showNotification(`Delete failed: ${error.message}`, 'error');
            } finally {
                this.state.isDeleting = false;
            }
        },

        closeDeleteModal() {
            console.log('❌ Closing delete modal');
            
            // Reset semua state delete
            this.state.isDeleteModalOpen = false;
            this.state.deleteItemId = null;
            this.state.deleteItemName = '';
            this.state.deleteReason = '';
            this.state.isDeleting = false;
            this.state.deleteError = '';
        },

        viewRequestDetail(requestId) {
            const request = [...this.state.activePrices, ...this.state.pendingRequests]
                .find(req => req.id === requestId);
            
            if (request) {
                const roomType = this.state.roomTypes.find(rt => rt.id === request.room_type_id);
                const location = this.state.locations.find(loc => loc.id === request.location_id);
                
                let message = `Request Details:\n\n`;
                message += `Location: ${location?.name || 'N/A'}\n`;
                message += `Service: ${roomType?.name || 'N/A'}\n`;
                message += `Duration: ${request.duration} ${request.duration_type}\n`;
                message += `Price: Rp ${this.formatCurrency(parseFloat(request.base_price))}\n`;
                message += `Status: ${request.request_status}\n`;
                message += `Requested by: ${request.requested_by}\n`;
                message += `Requested at: ${new Date(request.requested_at).toLocaleDateString('id-ID')}\n`;
                
                if (request.request_reason) {
                    message += `\nReason: ${request.request_reason}\n`;
                }
                
                alert(message);
            }
        },

        openHistoryModal() {
            if (!this.state.currentLocationId) {
                this.showNotification('Please select a branch first', 'error');
                return;
            }
            
            this.state.isHistoryModalOpen = true;
            
            // Trigger load history data
            this.$nextTick(() => {
                this.loadHistoryData();
            });
        },

        closeHistoryModal() {
            this.state.isHistoryModalOpen = false;
        },

        // ⭐️ TAMBAHKAN METHOD INI DI Alpine.js object Anda
        isPackageValid(pkg) {
            // Validasi dasar package
            if (!pkg || typeof pkg !== 'object') return false;
            
            // Pastikan required properties ada
            const requiredProps = ['id', 'name', 'price'];
            for (const prop of requiredProps) {
                if (pkg[prop] === undefined || pkg[prop] === null) {
                    return false;
                }
            }
            
            // Validasi tipe data
            if (typeof pkg.price !== 'number' || isNaN(pkg.price)) {
                return false;
            }
            
            return true;
        },

        // ⭐️ Juga tambahkan safeGet() jika belum ada
        safeGet(obj, path, defaultValue = null) {
            if (!obj || typeof obj !== 'object') return defaultValue;
            
            const keys = path.split('.');
            let current = obj;
            
            for (const key of keys) {
                if (current[key] === undefined || current[key] === null) {
                    return defaultValue;
                }
                current = current[key];
            }
            
            return current;
        },

        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
                type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
                'bg-blue-100 text-blue-800 border border-blue-200'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    };
}
</script>

@endsection