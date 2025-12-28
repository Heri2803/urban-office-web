{{-- resources/views/admin/pricing.blade.php --}}

@php
    $user = Auth::user();
    $user->load('location'); // Eager load location
    
    // Determine if user can manage all locations
    // Business logic: Admin tanpa location_id = global admin
    $canManageAll = $user->isAdmin() && empty($user->location_id);
@endphp

@extends('layouts.admin')

@section('title', 'Pricing Management')

@section('content')
<div x-data="pricingManagement()" x-init="init()" 
    class="space-y-4 md:space-y-6 pb-20 md:pb-6 max-w-full overflow-hidden"
    data-user-location-id="{{ $user->location_id ?? 'null' }}">
    
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
                {{-- Room Type Tabs --}}
                <div class="border-b border-gray-200 mb-4 overflow-x-auto scrollbar-hide">
                    <nav class="flex gap-1 sm:gap-2">
                        <template x-if="roomTypes && roomTypes.length > 0">
                            <template x-for="roomType in roomTypes" :key="roomType.id">
                                <button 
                                    @click="activeRoomType = roomType.id; 
                                            activeCategory = roomTypeCategories[roomType.id]?.[0]?.id || null;
                                            activeRoom = 'all'"
                                    :class="activeRoomType === roomType.id ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500'"
                                    class="px-3 sm:px-4 py-2 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap transition flex-shrink-0"
                                >
                                    <span x-text="getRoomTypeIcon(roomType.name)"></span>
                                    <span class="hidden sm:inline ml-1" x-text="roomType.name"></span>
                                    <span x-show="roomType.categories.length > 0" 
                                        class="ml-1 text-xs" 
                                        x-text="'(' + roomType.categories.length + ')'">
                                    </span>
                                </button>
                            </template>
                        </template>
                        
                        {{-- Loading State --}}
                        <div x-show="isLoading" class="px-4 py-2 text-gray-500 text-sm italic">
                            Loading room types...
                        </div>
                        
                        {{-- Empty State --}}
                        <div x-show="!isLoading && (!roomTypes || roomTypes.length === 0)" 
                            class="px-4 py-2 text-gray-500 text-sm">
                            No room types found
                        </div>
                    </nav>
                </div>

                {{-- Category Tabs --}}
                <div x-show="activeRoomType && roomTypeCategories[activeRoomType] && roomTypeCategories[activeRoomType].length > 0" 
                    class="border-b border-gray-200 mb-4 overflow-x-auto scrollbar-hide">
                    <nav class="flex gap-1 sm:gap-2">
                        <template x-for="category in roomTypeCategories[activeRoomType]" :key="category.id">
                            <button 
                                @click="activeCategory = category.id; activeRoom = 'all'"
                                :class="activeCategory === category.id ? 'border-purple-600 text-purple-600 bg-purple-50' : 'border-transparent text-gray-500'"
                                class="px-3 sm:px-4 py-2 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap transition flex-shrink-0"
                            >
                                <span x-text="category.icon"></span>
                                <span class="ml-1" x-text="category.name"></span>
                                <span x-show="category.isNullCategory" 
                                    class="ml-1 text-xs text-gray-400" 
                                    title="Auto-generated ID">⚠️</span>
                                <span x-show="getCategoryPriceCount(category.id) > 0" 
                                    class="ml-1 text-xs text-green-600">
                                    💰
                                </span>
                            </button>
                        </template>
                    </nav>
                </div>

                {{-- Room Selection Tabs (jika ada room-specific prices) --}}
                <div x-show="activeRoomType && activeCategory && getCategoryRooms(activeCategory).length > 0" 
                    class="border-b border-gray-200 mb-6 overflow-x-auto scrollbar-hide">
                    <nav class="flex gap-1 sm:gap-2">
                        <button 
                            @click="activeRoom = 'all'"
                            :class="activeRoom === 'all' ? 'border-green-600 text-green-600 bg-green-50' : 'border-transparent text-gray-500'"
                            class="px-3 sm:px-4 py-2 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap transition flex-shrink-0"
                        >
                            <span>🏢</span>
                            <span class="ml-1">All Rooms</span>
                            <span class="ml-1 text-xs text-gray-500" 
                                x-text="'(' + getCategoryRooms(activeCategory).length + ')'">
                            </span>
                        </button>
                        
                        <template x-for="room in getCategoryRooms(activeCategory)" :key="room.id">
                            <button 
                                @click="activeRoom = room.id"
                                :class="activeRoom === room.id ? 'border-indigo-600 text-indigo-600 bg-indigo-50' : 'border-transparent text-gray-500'"
                                class="px-3 sm:px-4 py-2 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap transition flex-shrink-0"
                            >
                                <span>🚪</span>
                                <span class="ml-1" x-text="room.name"></span>
                            </button>
                        </template>
                    </nav>
                </div>

                {{-- Main Content --}}
                <template x-if="activeRoomType && activeCategory">
                    <div x-transition>
                        {{-- Find current room type and category --}}
                        <template x-for="roomType in roomTypes" :key="roomType.id">
                            <div x-show="roomType.id === activeRoomType">
                                <template x-for="category in roomType.categories" :key="category.id">
                                    <div x-show="category.id === activeCategory">
                                        
                                        {{-- Header --}}
                                        <div class="mb-6">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="text-xl" x-text="getRoomTypeIcon(roomType.name)"></span>
                                                <h3 class="text-base sm:text-lg font-semibold text-gray-800" 
                                                    x-text="roomType.name">
                                                </h3>
                                                <span class="text-gray-400">→</span>
                                                <span class="text-xl" x-text="category.icon"></span>
                                                <h3 class="text-base sm:text-lg font-semibold text-gray-800" 
                                                    x-text="category.name">
                                                </h3>
                                                
                                                <template x-if="activeRoom && activeRoom !== 'all'">
                                                    <span class="text-gray-400">→</span>
                                                    <span class="text-lg">🚪</span>
                                                    <h3 class="text-base sm:text-lg font-semibold text-indigo-600" 
                                                        x-text="getRoomName(activeRoom)">
                                                    </h3>
                                                </template>
                                            </div>
                                            
                                            <div class="flex flex-wrap gap-2 items-center text-xs sm:text-sm text-gray-500">
                                                <p>Base prices are fixed and cannot be edited directly</p>
                                                
                                                <template x-if="activeRoom === 'all'">
                                                    <span class="px-2 py-0.5 bg-gray-100 rounded">
                                                        <span x-text="getCategoryPriceCount(category.id)"></span> 
                                                        prices across <span x-text="getCategoryRooms(category.id).length"></span> rooms
                                                    </span>
                                                </template>
                                                
                                                <template x-if="activeRoom && activeRoom !== 'all'">
                                                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded">
                                                        Room-specific prices
                                                    </span>
                                                </template>
                                                
                                                <span x-show="category.isNullCategory" 
                                                    class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs">
                                                    Auto-generated ID
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Room Info Card (jika pilih room tertentu) --}}
                                        <div x-show="activeRoom && activeRoom !== 'all'" 
                                            class="mb-6 bg-indigo-50 rounded-lg border border-indigo-200 p-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center text-xl">
                                                    🚪
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-indigo-800" 
                                                        x-text="getRoomInfo(activeRoom).name || getRoomName(activeRoom)">
                                                    </h4>
                                                    <div class="flex flex-wrap gap-3 text-xs text-indigo-600 mt-1">
                                                        <template x-if="getRoomInfo(activeRoom).room_number">
                                                            <span>Room: <span class="font-semibold" 
                                                                x-text="getRoomInfo(activeRoom).room_number"></span></span>
                                                        </template>
                                                        <template x-if="getRoomInfo(activeRoom).floor">
                                                            <span>Floor: <span class="font-semibold" 
                                                                x-text="getRoomInfo(activeRoom).floor"></span></span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Price Cards --}}
                                        <div x-show="getFilteredDurations(category.id).length > 0">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4 mb-6">
                                                <template x-for="duration in getFilteredDurations(category.id)" 
                                                        :key="duration.id">
                                                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-3 sm:p-4 hover:bg-gray-100 transition hover:shadow-sm">
                                                        <div class="flex items-start justify-between mb-2">
                                                            <div class="min-w-0 flex-1">
                                                                <p class="text-xs sm:text-sm font-medium text-gray-600 truncate" 
                                                                x-text="duration.name || duration.displayName">
                                                                </p>
                                                                <template x-if="activeRoom && activeRoom !== 'all'">
                                                                    <p class="text-xs text-gray-500 mt-1 truncate" 
                                                                    x-text="'For: ' + getRoomName(activeRoom)">
                                                                    </p>
                                                                </template>
                                                            </div>
                                                            <span class="text-gray-400 flex-shrink-0 ml-2">🔒</span>
                                                        </div>
                                                        
                                                        <div class="mb-2">
                                                            <p class="text-lg sm:text-2xl font-bold text-gray-800" 
                                                            x-text="'Rp ' + formatNumber(getPriceForDuration(category.id, duration.id, activeRoom))">
                                                            </p>
                                                        
                                                        </div>
                                                        
                                                        <div class="flex items-center justify-between">
                                                            <p class="text-xs text-gray-500">
                                                                Active price
                                                            </p>
                                                            <span class="text-green-600 text-xs">
                                                                ✓
                                                            </span>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Info jika ada durasi tapi tidak punya harga --}}
                                        <div x-show="getFilteredDurations(category.id).length === 0" 
                                            class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 mb-6">
                                            <div class="text-gray-400">
                                                <div class="text-5xl mb-3">⚠️</div>
                                                <p class="text-base font-medium text-gray-600 mb-2">No matching prices</p>
                                                <p class="text-sm text-gray-500 mb-4">
                                                    This category doesn't have prices for available durations
                                                </p>
                                                <template x-if="activeRoom && activeRoom !== 'all'">
                                                    <p class="text-xs text-gray-500">
                                                        Try selecting "All Rooms" to see other available prices
                                                    </p>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Empty State for Prices --}}
                                        <div x-show="getCategoryPriceCount(category.id) === 0" 
                                            class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 mb-6">
                                            <div class="text-gray-400">
                                                <div class="text-5xl mb-3">💰</div>
                                                <p class="text-base font-medium text-gray-600 mb-2">No prices configured</p>
                                                <p class="text-sm text-gray-500 mb-4">
                                                    This category doesn't have any base prices yet
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="flex flex-col sm:flex-row gap-3">
                                            <template x-if="activeRoom && activeRoom !== 'all'">
                                                <button 
                                                    @click="openRequestModal(category, activeRoom)"
                                                    class="px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm flex items-center justify-center gap-2 flex-1"
                                                >
                                                    <span>📝</span>
                                                    <span>Request Price Change for <span x-text="getRoomName(activeRoom)"></span></span>
                                                </button>
                                            </template>
                                            <template x-if="activeRoom === 'all'">
                                                <button 
                                                    @click="openRequestModal(category)"
                                                    class="px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm flex items-center justify-center gap-2 flex-1"
                                                >
                                                    <span>📝</span>
                                                    <span>Request Price Change</span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                
                                {{-- No Categories in Room Type --}}
                                <div x-show="roomType.categories.length === 0" 
                                    class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                    <div class="text-gray-400">
                                        <div class="text-5xl mb-3">📂</div>
                                        <p class="text-base font-medium text-gray-600 mb-2">No categories</p>
                                        <p class="text-sm text-gray-500 mb-4">
                                            This room type doesn't have any pricing categories yet
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- No Selection State --}}
                <div x-show="(!activeRoomType || !activeCategory) && roomTypes && roomTypes.length > 0" 
                    class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <div class="text-gray-400">
                        <div class="text-5xl mb-3">🔍</div>
                        <p class="text-sm text-gray-500">Select a room type and category to view prices</p>
                    </div>
                </div>

                {{-- Global Empty State --}}
                <div x-show="!isLoading && roomTypes && roomTypes.length === 0" 
                    class="text-center py-16 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <div class="text-gray-400">
                        <div class="text-5xl mb-3">🏢</div>
                        <p class="text-base font-medium text-gray-600 mb-2">No room types configured</p>
                        <p class="text-sm text-gray-500 mb-4">
                            Please configure room types and categories first
                        </p>
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

                {{-- ✅ UPDATED: Semua Requests dengan Status --}}
                <div class="space-y-4">
                    <template x-if="priceRequests && priceRequests.length > 0">
                        <div>
                            {{-- Filter Tabs --}}
                            <div class="border-b border-gray-200 mb-4">
                                <nav class="flex flex-wrap gap-2 sm:gap-4">
                                    <button 
                                        @click="activeRequestFilter = 'all'"
                                        :class="activeRequestFilter === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                        class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                                    >
                                        All (<span x-text="priceRequests?.length || 0"></span>)
                                    </button>
                                    <button 
                                        @click="activeRequestFilter = 'pending'"
                                        :class="activeRequestFilter === 'pending' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                        class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                                    >
                                        ⏳ Pending (<span x-text="stats.pendingRequests || 0"></span>)
                                    </button>
                                    <button 
                                        @click="activeRequestFilter = 'active'"
                                        :class="activeRequestFilter === 'active' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                        class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                                    >
                                        ✅ Active (<span x-text="stats.activeRequests || 0"></span>)
                                    </button>
                                    <button 
                                        @click="activeRequestFilter = 'rejected'"
                                        :class="activeRequestFilter === 'rejected' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                        class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                                    >
                                        ❌ Rejected (<span x-text="stats.rejectedRequests || 0"></span>)
                                    </button>
                                    <button 
                                        @click="activeRequestFilter = 'inactive'"
                                        :class="activeRequestFilter === 'inactive' ? 'border-gray-500 text-gray-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                        class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                                    >
                                        ⏸️ Inactive (<span x-text="stats.inactiveRequests || 0"></span>)
                                    </button>
                                </nav>
                            </div>

                            {{-- Requests List --}}
                            <div class="space-y-3">
                                <template x-for="request in getFilteredRequests()" :key="request._uid || request.id">
                                    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow"
                                        :class="{
                                            'border-yellow-200 bg-yellow-50': request.status === 'pending',
                                            'border-green-200 bg-green-50': request.status === 'approved',
                                            'border-red-200 bg-red-50': request.status === 'rejected',
                                            'border-gray-200': !request.status
                                        }">
                                        
                                        {{-- Header dengan Status Badge --}}
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="min-w-0 flex-1">
                                                <h4 class="text-sm sm:text-base font-semibold text-gray-800 mb-1">
                                                    <span x-text="getServiceName(request.service) || request.serviceName"></span>
                                                    <span> - </span>
                                                    <span x-text="getDurationName(request.duration) || request.durationName"></span>
                                                </h4>
                                                <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                                    <template x-if="request.roomTypeName">
                                                        <span x-text="request.roomTypeName"></span>
                                                    </template>
                                                    <template x-if="request.room_id">
                                                        <span>• Room: <span x-text="getRoomName(request.room_id)"></span></span>
                                                    </template>
                                                    <template x-if="request.submittedAt">
                                                        <span>• <span x-text="request.submittedAt"></span></span>
                                                    </template>
                                                    <template x-if="request.submittedBy">
                                                        <span>• By: <span x-text="request.submittedBy"></span></span>
                                                    </template>
                                                </div>
                                            </div>
                                            
                                            {{-- ✅ STATUS BADGE --}}
                                            <div class="flex flex-col items-end gap-2">
                                                <span :class="getStatusBadgeClass(request.status)"
                                                    class="px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap"
                                                    x-text="getStatusDisplay(request.status)">
                                                </span>
                                                
                                                {{-- Tampilkan icon khusus untuk active status --}}
                                                <template x-if="request.status === 'active'">
                                                    <div class="flex items-center gap-1 text-xs text-green-600">
                                                        <span>💰</span>
                                                        <span>Active Price</span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                        
                                        {{-- Price Comparison --}}
                                        <div class="grid grid-cols-2 gap-3 mb-3">
                                            <div class="text-center p-2 bg-gray-50 rounded">
                                                <p class="text-xs text-gray-500 mb-1">Current Price</p>
                                                <p class="text-sm font-bold text-gray-700" 
                                                x-text="'Rp ' + formatNumber(request.currentPrice || request.previous_price)">
                                                </p>
                                            </div>
                                            <div class="text-center p-2" 
                                                :class="request.status === 'approved' ? 'bg-green-50' : 'bg-blue-50'">
                                                <p class="text-xs text-gray-500 mb-1">Proposed Price</p>
                                                <p class="text-sm font-bold" 
                                                :class="request.status === 'approved' ? 'text-green-700' : 'text-blue-700'"
                                                x-text="'Rp ' + formatNumber(request.proposedPrice || request.base_price)">
                                                </p>
                                            </div>
                                        </div>
                                        
                                        {{-- Reason --}}
                                        <div x-show="request.reason || request.request_reason" class="mb-3">
                                            <p class="text-xs text-gray-500 mb-1">Reason:</p>
                                            <p class="text-xs sm:text-sm text-gray-700" 
                                            x-text="request.reason || request.request_reason || 'No reason provided'">
                                            </p>
                                        </div>
                                        
                                        {{-- Metadata Footer --}}
                                        <div class="flex flex-wrap justify-between items-center pt-3 border-t border-gray-200">
                                            <div class="text-xs text-gray-500">
                                                <template x-if="request.requested_at">
                                                    <span>Requested: <span x-text="formatRelativeTime(request.requested_at)"></span></span>
                                                </template>
                                                <template x-if="!request.requested_at && request.submittedAt">
                                                    <span>Submitted: <span x-text="request.submittedAt"></span></span>
                                                </template>
                                            </div>
                                            
                                            <div class="flex items-center gap-2">
                                                {{-- Action Buttons --}}
                                                
                                                {{-- Edit Button --}}
                                                <template x-if="request.status === 'pending'">
                                                    <button 
                                                        @click="openEditRequestModal(request)"
                                                        class="text-xs px-3 py-1 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-lg transition flex items-center gap-1"
                                                        title="Edit Request">
                                                        <span>Update</span>
                                                        <span class="hidden sm:inline">Edit</span>
                                                    </button>
                                                </template>
                                                
                                                {{-- Delete Button --}}
                                                <template x-if="request.status === 'pending'">
                                                    <button 
                                                        @click="openDeleteRequestModal(request)"
                                                        class="text-xs px-3 py-1 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg transition flex items-center gap-1"
                                                        title="Delete Request">
                                                        <span>🗑️</span>
                                                        <span class="hidden sm:inline">Delete</span>
                                                    </button>
                                                </template>
                                                
                                                {{-- Non-editable State --}}
                                                <template x-if="request.status !== 'pending'">
                                                    <span class="text-xs text-gray-500 italic">
                                                        Read-only (<span x-text="request.status"></span>)
                                                    </span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Empty State --}}
                    <div x-show="!priceRequests || priceRequests.length === 0" 
                        class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <div class="text-gray-400">
                            <div class="text-5xl mb-3">📭</div>
                            <p class="text-sm font-medium text-gray-600 mb-1">No price requests</p>
                            <p class="text-xs text-gray-500">Submit your first price change request to get started</p>
                        </div>
                    </div>
                    
                    <div x-show="priceRequests && priceRequests.length > 0 && getFilteredRequests().length === 0" 
                        class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-500">No requests match the selected filter</p>
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
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800">
                        <span x-text="requestForm.apply_to_all ? 'Bulk Price Change Request' : 'Submit Price Change Request'"></span>
                    </h3>
                    <button @click="showRequestModal = false" class="text-gray-400 hover:text-gray-600 flex-shrink-0 ml-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-3 sm:space-y-4">
                    {{-- Toggle Apply to All --}}
                    <div class="mb-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" 
                                x-model="requestForm.apply_to_all"
                                @change="onApplyToAllToggle"
                                class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm font-medium text-gray-700">
                                Apply to All Rooms with Same Criteria
                            </span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-7" 
                        x-show="requestForm.apply_to_all">
                            When checked, price change will apply to ALL rooms with matching category, duration type, and duration
                        </p>
                    </div>

                    {{-- Context Info (jika dibuka dari Base Price tab) --}}
                    <div x-show="requestForm.contextInfo" class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-sm text-blue-700">
                            <span x-text="requestForm.contextInfo"></span>
                        </p>
                    </div>

                    {{-- MODE INDICATOR --}}
                    <div x-show="requestForm.apply_to_all" 
                        class="mb-4 px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center gap-2">
                            <span class="text-blue-600">🏢</span>
                            <span class="text-sm font-medium text-blue-700">Bulk Update Mode</span>
                            <span class="text-xs text-blue-500 ml-auto">Will affect multiple rooms</span>
                        </div>
                    </div>

                    {{-- ======================================== --}}
                    {{-- SECTION A: CATEGORY/SERVICE SELECTION --}}
                    {{-- ======================================== --}}

                    {{-- Conditional: Category untuk Apply to All --}}
                    <div x-show="requestForm.apply_to_all">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-500">(Applies to all rooms)</span>
                        </label>
                        <select x-model="requestForm.category_id"
                                @change="updateCurrentPrice"
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">-- Select Category --</option>
                            <template x-for="category in availableCategoriesForApplyAll" :key="category.id">
                                <option :value="category.id" x-text="category.icon + ' ' + category.name"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Conditional: Service Category untuk Single Mode --}}
                    <div x-show="!requestForm.apply_to_all">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Service Category <span class="text-red-500">*</span>
                        </label>
                        <select x-model="requestForm.service_category_id" 
                                @change="onServiceCategoryChange"
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">-- Select Service Category --</option>
                            <option value="null">⚠️ NULL Category (General)</option>
                            <template x-for="service in services" :key="service.id">
                                <option :value="service.id" x-text="service.icon + ' ' + service.name"></option>
                            </template>
                        </select>
                        <p x-show="requestForm.service_category_id === 'null'" 
                        class="text-xs text-yellow-600 mt-1">
                            This request doesn't have a specific service category
                        </p>
                    </div>

                    {{-- ======================================== --}}
                    {{-- SECTION B: ROOM TYPE & ROOM SELECTION --}}
                    {{-- ======================================== --}}

                    {{-- Room Type (COMMON untuk kedua mode) --}}
                    <div x-show="showRoomTypeField || requestForm.apply_to_all">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Room Type <span class="text-red-500">*</span>
                        </label>
                        <select x-model="requestForm.room_type_id" 
                                @change="updateCurrentPrice"
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">-- Select Room Type --</option>
                            <template x-for="roomType in filteredRoomTypes" :key="roomType.id">
                                <option :value="roomType.id" x-text="roomType.name"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Specific Room (Hanya untuk Single Mode) --}}
                    <div x-show="!requestForm.apply_to_all && requestForm.room_type_id && showRoomSelection">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Specific Room
                            <template x-if="requestForm.room_id">
                                <span class="text-green-600 ml-2">(Selected: <span x-text="getRoomName(requestForm.room_id)"></span>)</span>
                            </template>
                        </label>
                        
                        {{-- Room Info jika sudah ada room_id --}}
                        <div x-show="requestForm.room_id" class="mb-2 p-2 bg-blue-50 border border-blue-200 rounded">
                            <div class="flex items-center justify-between">
                                <div class="text-xs">
                                    <span class="font-medium text-blue-700">Selected Room:</span>
                                    <span class="ml-2" x-text="getRoomName(requestForm.room_id)"></span>
                                </div>
                                <button type="button" 
                                        @click="requestForm.room_id = ''"
                                        class="text-xs text-red-600 hover:text-red-800">
                                    Clear Selection
                                </button>
                            </div>
                        </div>
                        
                        <select x-model="requestForm.room_id" 
                                @change="updateCurrentPrice"
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">-- All Rooms --</option>
                            <template x-for="room in filteredRooms" :key="room.id">
                                <option :value="room.id" x-text="room.name"></option>
                            </template>
                        </select>
                        
                        {{-- Info jika tidak ada rooms --}}
                        <div x-show="filteredRooms.length === 0" class="text-xs text-gray-500 mt-1">
                            No rooms available for this room type
                        </div>
                    </div>

                    {{-- ======================================== --}}
                    {{-- SECTION C: DURATION (COMMON untuk kedua mode) --}}
                    {{-- ======================================== --}}

                    {{-- Duration --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Duration Type <span class="text-red-500">*</span>
                            </label>
                            <select x-model="requestForm.duration_type" 
                                    @change="updateCurrentPrice"
                                    class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="">-- Select Type --</option>
                                <option value="hour">Hour</option>
                                <option value="day">Day</option>
                                <option value="week">Week</option>
                                <option value="month">Month</option>
                                <option value="year">Year</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Duration Value <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                x-model="requestForm.duration" 
                                @change="updateCurrentPrice"
                                placeholder="e.g., 1" 
                                min="1" 
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                    </div>

                    {{-- ======================================== --}}
                    {{-- SECTION D: PRICE DISPLAY & INPUT --}}
                    {{-- ======================================== --}}

                    {{-- Current Price Display --}}
                    <div x-show="currentPrice > 0" class="bg-gray-50 rounded-lg p-3 sm:p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Current Base Price</p>
                                <p class="text-lg sm:text-xl font-bold text-gray-800" 
                                x-text="'Rp ' + formatNumber(currentPrice)"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 mb-1">For</p>
                                <p class="text-sm font-medium text-gray-700" x-text="priceContext"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Proposed Price --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Proposed New Price <span class="text-red-500">*</span>
                        </label>
                        
                        {{-- ✅ TAMBAHKAN AUTO-FILL ROW --}}
                        <div x-show="currentPrice > 0" class="mb-2 flex justify-between items-center">
                            <span class="text-xs text-gray-500">
                                Current: <span class="font-semibold" x-text="'Rp ' + formatNumber(currentPrice)"></span>
                            </span>
                            <div class="flex gap-2">
                                {{-- ✅ AUTO-FILL BUTTON --}}
                                <button type="button"
                                    @click="requestForm.base_price = currentPrice"
                                    class="text-xs bg-blue-100 text-blue-600 px-3 py-1 rounded-lg hover:bg-blue-200 border border-blue-200">
                                    Auto-fill Current Price
                                </button>
                                
                                {{-- ✅ QUICK INCREASE BUTTONS --}}
                                <button type="button"
                                    @click="requestForm.base_price = Math.round(currentPrice * 1.05)"
                                    class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded-lg hover:bg-green-200 border border-green-200">
                                    +5%
                                </button>
                                <button type="button"
                                    @click="requestForm.base_price = Math.round(currentPrice * 1.1)"
                                    class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded-lg hover:bg-green-200 border border-green-200">
                                    +10%
                                </button>
                            </div>
                        </div>
                        
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-500">Rp</span>
                            <input type="number" 
                                x-model="requestForm.base_price"
                                placeholder="e.g., 175000" 
                                min="0"
                                class="w-full pl-10 pr-3 sm:pl-10 sm:pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        
                        {{-- ✅ SIMPLE PERCENTAGE DISPLAY --}}
                        <div x-show="currentPrice > 0 && requestForm.base_price" 
                            class="text-xs mt-2 flex justify-between">
                            <span :class="percentageChange >= 0 ? 'text-green-600' : 'text-red-600'">
                                <span x-text="percentageChange >= 0 ? 'Increase' : 'Decrease'"></span>: 
                                <span x-text="Math.abs(percentageChange).toFixed(1) + '%'"></span>
                            </span>
                            <span class="text-gray-500">
                                Change: <span x-text="'Rp ' + formatNumber(requestForm.base_price - currentPrice)"></span>
                            </span>
                        </div>
                    </div>

                    {{-- Additional Options --}}
                    <div class="space-y-3">
                        {{-- Coffee Break Option --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Coffee Break Option <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <select x-model="requestForm.coffee_break_option" 
                                    class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="">-- No Coffee Break --</option>
                                <option value="1">Standard Coffee Break</option>
                                <option value="2">Premium Coffee Break</option>
                            </select>
                        </div>

                        {{-- Coffee Break Price --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Coffee Break Price <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-500">Rp</span>
                                <input type="number" 
                                    x-model="requestForm.coffee_break_price"
                                    placeholder="e.g., 50000" 
                                    min="0"
                                    class="w-full pl-10 pr-3 sm:pl-10 sm:pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- Deposit Field (HANYA untuk Virtual Office & Private Office) --}}
                    <div x-show="showDepositField" x-transition class="space-y-3 mt-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Deposit Amount <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-500">Rp</span>
                                <input type="number" 
                                    x-model="requestForm.deposit"
                                    placeholder="e.g., 500000" 
                                    min="0"
                                    class="w-full pl-10 pr-3 sm:pl-10 sm:pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Optional deposit for Virtual Office & Private Office bookings
                            </p>
                        </div>
                    </div>

                    {{-- Reason --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Change <span class="text-red-500">*</span>
                        </label>
                        <textarea x-model="requestForm.reason" 
                                rows="3" 
                                placeholder="Explain why this price change is needed (market adjustment, competition, etc.)..." 
                                class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"></textarea>
                        <p class="text-xs text-gray-500 mt-1">
                            <span x-text="requestForm.reason?.length || 0"></span>/500 characters
                        </p>
                    </div>

                    {{-- Validation Summary --}}
                    <div x-show="validationErrors.length > 0" class="bg-red-50 border border-red-200 rounded-lg p-3">
                        <p class="text-sm font-medium text-red-700 mb-2">Please fix the following:</p>
                        <ul class="text-xs text-red-600 list-disc list-inside">
                            <template x-for="error in validationErrors" :key="error">
                                <li x-text="error"></li>
                            </template>
                        </ul>
                    </div>

                    {{-- DEBUG SECTION - Hanya untuk development --}}
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-bold text-yellow-800">🐛 Debug Info (Development Only)</h4>
                            <button @click="debugForm()" 
                                    class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded hover:bg-yellow-200">
                                Run Debug
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="font-medium">price_id:</span>
                                <span x-text="requestForm.price_id || 'NULL'" 
                                    :class="requestForm.price_id ? 'text-green-600' : 'text-red-600'"></span>
                            </div>
                            <div>
                                <span class="font-medium">isFormValid:</span>
                                <span x-text="isFormValid" 
                                    :class="isFormValid ? 'text-green-600' : 'text-red-600'"></span>
                            </div>
                            <div>
                                <span class="font-medium">Mode:</span>
                                <span x-text="requestForm.apply_to_all ? 'BULK' : 'SINGLE'" 
                                    class="text-blue-600"></span>
                            </div>
                            <div>
                                <span class="font-medium">Current Price:</span>
                                <span x-text="'Rp ' + formatNumber(currentPrice)"></span>
                            </div>
                        </div>
                        
                        <div class="mt-2 text-xs">
                            <details>
                                <summary class="cursor-pointer text-yellow-700 hover:text-yellow-900">
                                    Show Full Form State
                                </summary>
                                <pre class="mt-2 p-2 bg-white border border-yellow-300 rounded text-xs overflow-auto max-h-40" 
                                    x-text="JSON.stringify(requestForm, null, 2)"></pre>
                            </details>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-4">
                        <button 
                            @click="isEditMode ? updateRequest() : submitRequest()"
                            :disabled="(isEditMode ? isUpdating : isSubmitting) || !isFormValid"
                            :class="{
                                'bg-gray-300 cursor-not-allowed': (isEditMode ? isUpdating : isSubmitting) || !isFormValid,
                                'bg-blue-600 hover:bg-blue-700': !(isEditMode ? isUpdating : isSubmitting) && isFormValid
                            }">
                            
                            <span x-show="isEditMode ? isUpdating : isSubmitting" class="animate-spin">⏳</span>
                            <span x-show="!(isEditMode ? isUpdating : isSubmitting)">
                                <span x-text="isEditMode ? '✏️' : (requestForm.apply_to_all ? '🏢' : '📝')"></span>
                            </span>
                            
                            <span x-text="(isEditMode ? isUpdating : isSubmitting) ? 'Processing...' : 
                                        (isEditMode ? 'Update Request' : 
                                        (requestForm.apply_to_all ? 'Submit Bulk Request' : 'Submit Request'))"></span>
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
    <div x-show="showDeleteRequestModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-3 sm:px-4">
            <div @click="showDeleteRequestModal = false" 
                class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-4 sm:p-6 my-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg sm:text-xl font-bold text-red-600">
                        🗑️ Delete Request
                    </h3>
                    <button @click="showDeleteRequestModal = false" 
                        class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    {{-- Warning Message --}}
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                        <div class="flex items-start gap-2">
                            <span class="text-red-600 text-xl">⚠️</span>
                            <div>
                                <p class="text-sm font-medium text-red-700">This action cannot be undone</p>
                                <p class="text-xs text-red-600 mt-1">
                                    You are about to delete a price change request. This will permanently remove it from the system.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Request Details --}}
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <p class="text-sm font-medium text-gray-700 mb-2">
                            <span x-text="deletingRequest?.serviceName || 'Request'"></span>
                            - 
                            <span x-text="deletingRequest?.durationName || 'Duration'"></span>
                        </p>
                        <div class="text-xs text-gray-500 space-y-1">
                            <template x-if="deletingRequest?.roomTypeName">
                                <p>Room Type: <span x-text="deletingRequest.roomTypeName"></span></p>
                            </template>
                            <template x-if="deletingRequest?.submittedAt">
                                <p>Submitted: <span x-text="deletingRequest.submittedAt"></span></p>
                            </template>
                            <p>Status: <span x-text="getStatusDisplay(deletingRequest?.status)"></span></p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-4">
                        <button 
                            @click="deleteRequest()"
                            :disabled="isDeleting"
                            :class="isDeleting 
                                ? 'bg-red-300 cursor-not-allowed' 
                                : 'bg-red-600 hover:bg-red-700'"
                            class="px-4 sm:px-6 py-2 sm:py-3 text-white rounded-lg transition font-medium text-sm flex items-center justify-center gap-2 flex-1">
                            
                            <span x-show="isDeleting" class="animate-spin">⏳</span>
                            <span x-show="!isDeleting">🗑️</span>
                            <span x-text="isDeleting ? 'Deleting...' : 'Delete Request'"></span>
                        </button>
                        
                        <button 
                            @click="showDeleteRequestModal = false"
                            :disabled="isDeleting"
                            class="px-4 sm:px-6 py-2 sm:py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition text-sm">
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
        activeRoomType: null,
        activeCategory: null,
        activeRoom: null,
        activeService: 1,
        activeRequestFilter: 'all',
        showRequestModal: false,
        showDeleteModal: false,
        showToast: false,
        showRoomView: false,
        toastMessage: '',
        toastType: 'success',
        deletingItem: null,
        deletingRequest: null,
        isLoading: false,
        categoryRoomsMap: {}, // Maps categoryId -> Set of roomIds
        nullCategoryMapping: {}, // Untuk categories dengan ID null
        userLocationId: null,
        userLocationName: 'Loading...',
        userCanManageAllLocations: false,

        validationErrors: [], // Untuk menyimpan error messages
        isSubmitting: false,  // Untuk loading state
        isEditMode: false,
        isUpdating: false,
        isDeleting: false,
        editingRequestId: null,
        deletingRequestId: null,
        showDeleteRequestModal: false,
        editValidationErrors: [],

        stats: {
            pendingRequests: 0,
            totalRoomTypes: 0,
            totalCategories: 0
        },

        roomTypes: [],   
        roomTypeCategories: {},
        roomPrices: {}, 
        rooms: {},  

        services: [],
        durations: [],
        basePrices: {},
        priceRequests: [],
        promos: [],

        requestForm: {
            parent_id: null, // ✅ GANTI price_id MENJADI parent_id
            service_category_id: '',
            room_type_id: '',
            room_id: '',
            duration_type: '',
            duration: '',
            base_price: '', // ✅ GANTI proposed_price MENJADI base_price (sesuai API)
            reason: '', // ✅ GANTI request_reason MENJADI reason (sesuai API)
            coffee_break_option: '',
            coffee_break_price: null,
            deposit: '',
            contextInfo: '',

            // ✅ APPLY TO ALL MODE
            apply_to_all: false,     // FLAG baru
            category_id: '',         // Untuk apply_to_all     
        },

        get showRoomSelection() {
            // Simple logic: show room selection jika ada room_type_id
            return !!this.requestForm.room_type_id;
        },

        // TAMBAHKAN setelah requestForm:
        get isFormValid() {
            console.group('🔍 isFormValid CHECK');
            
            // 1. COMMON FIELDS (untuk semua mode)
            const hasBasePrice = !!this.requestForm.base_price;
            const hasReason = !!this.requestForm.reason;
            const validReasonLength = this.requestForm.reason ? 
                this.requestForm.reason.length <= 500 : false;
            
            console.log('Common fields:', { hasBasePrice, hasReason, validReasonLength });
            
            if (!hasBasePrice || !hasReason || !validReasonLength) {
                console.log('❌ Common fields invalid');
                console.groupEnd();
                return false;
            }
            
            // 2. MODE-SPECIFIC VALIDATION
            if (this.requestForm.apply_to_all) {
                // ✅ BULK MODE VALIDATION
                console.log('📌 Validating BULK mode');
                
                const bulkRequired = {
                    category_id: this.requestForm.category_id,
                    room_type_id: this.requestForm.room_type_id,
                    duration_type: this.requestForm.duration_type,
                    duration: this.requestForm.duration
                };
                
                console.log('Bulk required fields:', bulkRequired);
                
                const hasBulkRequired = !!(
                    bulkRequired.category_id &&
                    bulkRequired.room_type_id &&
                    bulkRequired.duration_type &&
                    bulkRequired.duration
                );
                
                console.log('✅ Bulk mode valid:', hasBulkRequired);
                console.groupEnd();
                return hasBulkRequired;
                
            } else {
                // ✅ SINGLE MODE VALIDATION
                console.log('📌 Validating SINGLE mode');
                
                const singleRequired = {
                    service_category_id: this.requestForm.service_category_id,
                    room_type_id: this.requestForm.room_type_id,
                    duration_type: this.requestForm.duration_type,
                    duration: this.requestForm.duration
                };
                
                console.log('Single required fields:', singleRequired);
                
                const hasSingleRequired = !!(
                    singleRequired.service_category_id &&
                    singleRequired.room_type_id &&
                    singleRequired.duration_type &&
                    singleRequired.duration
                );
                
                console.log('✅ Single mode valid:', hasSingleRequired);
                console.groupEnd();
                return hasSingleRequired;
            }
        },

        get currentPrice() {
            if (!this.requestForm.service_category_id || 
                !this.requestForm.duration_type || 
                !this.requestForm.duration) {
                return 0;
            }
            
            const durationKey = `${this.requestForm.duration_type}-${this.requestForm.duration}`;
            
            // Jika ada room_id spesifik, cari price untuk room tersebut
            if (this.requestForm.room_id) {
                return this.roomPrices[this.requestForm.service_category_id]?.[this.requestForm.room_id]?.[durationKey] || 0;
            }
            
            // Jika tidak, gunakan base price
            return this.basePrices[this.requestForm.service_category_id]?.[durationKey] || 0;
        },

        get percentageChange() {
            if (!this.currentPrice || !this.requestForm.proposed_price) return 0;
            const current = parseFloat(this.currentPrice);
            const proposed = parseFloat(this.requestForm.proposed_price);
            return ((proposed - current) / current) * 100;
        },

        get priceContext() {
            let context = '';
            
            // Service category
            const service = this.services.find(s => s.id == this.requestForm.service_category_id);
            if (service) context += service.name;
            
            // Duration
            if (this.requestForm.duration_type && this.requestForm.duration) {
                context += ` - ${this.requestForm.duration} ${this.requestForm.duration_type}(s)`;
            }
            
            // Room
            if (this.requestForm.room_id) {
                const room = this.rooms[this.requestForm.room_id];
                if (room) context += ` - ${room.name}`;
            }
            
            return context;
        },

        get showRoomTypeField() {
            // Tampilkan room type field jika service category tidak memberikan info room type
            return true;
        },

        get filteredRoomTypes() {
            // Filter room types berdasarkan service category jika perlu
            return this.roomTypes;
        },

        get filteredRooms() {
            if (!this.requestForm.room_type_id) return [];
            
            // Cari rooms untuk room type ini
            const rooms = [];
            Object.values(this.rooms).forEach(room => {
                // Logic sederhana: semua rooms
                rooms.push(room);
            });
            
            return rooms;
        },

        get showAdditionalOptions() {
            // ✅ Coffee break TAMPIL untuk SEMUA room types (tapi optional)
            return true;
        },

        get showDepositField() {
            // ✅ Deposit HANYA untuk Virtual Office & Private Office
            if (!this.requestForm.room_type_id) return false;
            
            const roomType = this.roomTypes.find(rt => rt.id == this.requestForm.room_type_id);
            if (!roomType) return false;
            
            const roomTypeName = (roomType.name || '').toLowerCase();
            
            // HANYA tampilkan deposit untuk Virtual Office dan Private Office
            return roomTypeName.includes('virtual office') || 
                roomTypeName.includes('private office');
        },

        // MODIFY bagian akhir dari init():
        async init() {
            this.getUserLocationFromHTML();
            await this.fetchInitialData();
            this.calculateStats();
        },

        async fetchInitialData() {
            try {
                this.isLoading = true;
                
                // 1. Fetch service categories
                await this.fetchServiceCategories();
                
                // 2. Fetch pending price requests
                await this.fetchAllRequests();
                
                // 3. Generate durations (static for now)
                this.generateDurations();
                
                // 4. Fetch current active prices
                await this.fetchRoomTypesWithPrices();
                
            } catch (error) {
                console.error('Error fetching initial data:', error);
                this.showToastMessage('Failed to load data', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        getUserLocationFromHTML() {
            const element = this.$el;
            
            // Get location_id (handle 'null' string)
            const locationIdAttr = element.getAttribute('data-user-location-id');
            this.userLocationId = (locationIdAttr === 'null' || !locationIdAttr) 
                ? null 
                : parseInt(locationIdAttr);
            
            // Get location name
            this.userLocationName = element.getAttribute('data-user-location-name') || 'Not Assigned';

        },

        async fetchServiceCategories() {
            try {
                const response = await fetch('/pricing/api/service-categories');
                const data = await response.json();
                
                if (data.success) {
                    this.services = data.data.map(category => ({
                        id: category.id,
                        name: category.name,
                        icon: this.getServiceIcon(category.name),
                        slug: category.slug
                    }));
                    
                    // Set first service as active
                    if (this.services.length > 0) {
                        this.activeService = this.services[0].id;
                    }
                }
            } catch (error) {
                console.error('Error fetching service categories:', error);
            }
        },

        async fetchAllRequests() {
            try {
    
                const response = await fetch('/pricing/api/requests/history');
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                if (data.success && Array.isArray(data.data)) {
                    
                    const allRequests = data.data.map((request, index) => {
                        // Normalize status
                        let status = String(request.status || '').toLowerCase().trim();
                        if (status === 'approved') {
                            status = 'active';
                        }
                        
                        const requestData = {
                            // IDs
                            id: request.id ? `req-${request.id}` : `req-auto-${Date.now()}-${index}`,
                            _uid: `req-${request.id || 'auto'}-${index}-${Date.now()}`,
                            rawId: request.id,
                            
                            // Service info
                            serviceName: request.category || 'General',
                            category_id: request.category_id || null,
                            
                            // Room info
                            roomTypeName: request.room_type || 'Unknown',
                            room_type_id: request.room_type_id || null,
                            
                            // Duration
                            duration_type: request.duration_type || this.getDurationTypeFromRequest(request.duration_display),
                            duration: request.duration || this.getDurationValue(request.duration_display),
                            durationName: request.duration_display || '1 Hour',
                            
                            // Prices
                            currentPrice: parseFloat(request.previous_price) || 0,
                            proposedPrice: parseFloat(request.price) || 0,
                            
                            // Metadata
                            reason: request.reason || 'No reason provided',
                            status: status,
                            submittedAt: request.requested_at ? this.formatRelativeTime(request.requested_at) : 'Not submitted',
                            submittedBy: request.requested_by || 'Unknown',
                            reviewedAt: request.reviewed_at ? this.formatRelativeTime(request.reviewed_at) : null,
                            reviewedBy: request.reviewed_by || null,
                            requested_at: request.requested_at,
                            reviewed_at: request.reviewed_at,
                        };
                        
                        // 🟡 TAMBAHKAN ROOM DATA JIKA ADA
                        if (request.room_id) {
                            requestData.room_id = request.room_id;
                            requestData.roomName = request.room_name || `Room ${request.room_id}`;
                            requestData.roomNumber = request.room_number;
                            requestData.floor = request.floor;
                        }
                        
                        // 🟡 TAMBAHKAN ADDITIONAL DATA
                        if (request.coffee_break_option) {
                            requestData.coffee_break_option = request.coffee_break_option;
                        }
                        if (request.coffee_break_price) {
                            requestData.coffee_break_price = request.coffee_break_price;
                        }
                        if (request.deposit) {
                            requestData.deposit = request.deposit;
                        }
                        
                        return requestData;
                    });
                    
                    this.priceRequests = allRequests;
                    
                    // Debug: Hitung berapa yang punya room_id
                    const withRoom = allRequests.filter(r => r.room_id);
                    const withoutRoom = allRequests.filter(r => !r.room_id);
                    
                } else {
                    this.priceRequests = [];
                }
                
            } catch (error) {
                this.showToastMessage('Failed to load requests', 'error');
                this.priceRequests = [];
            }
        },

        async fetchRoomTypesWithPrices() {
            try {
                
                const response = await fetch('/pricing/api/prices-by-roomtype');
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                if (data.success && Array.isArray(data.data)) {
                    // 🔄 RESET SEMUA DATA (TAMBAHKAN categoryRoomsMap)
                    this.roomTypes = [];
                    this.roomTypeCategories = {};
                    this.basePrices = {};
                    this.nullCategoryMapping = {};
                    this.durations = [];
                    this.rooms = {};
                    this.roomPrices = {};
                    this.categoryRoomsMap = {}; // ✅ LINE BARU YANG PERLU DITAMBAH
                    
                    // Set untuk menyimpan semua durasi unik
                    const allDurationKeys = new Set();
                    
                    // Process each room type
                    data.data.forEach((roomType, index) => {
                        
                        const roomTypeObj = {
                            id: roomType.room_type?.id,
                            name: roomType.room_type?.name || 'Unknown',
                            categories: []
                        };
                        
                        // Process categories
                        if (roomType.categories && Array.isArray(roomType.categories)) {
                            
                            roomType.categories.forEach((category, catIndex) => {
                                const categoryId = category.category_id;
                                const categoryName = category.category_name || 'Unknown';
                                
                                // Handle null category ID
                                let safeCategoryId;
                                let isNullCategory = false;
                                
                                if (!categoryId) {
                                    safeCategoryId = `null-${roomType.room_type.id}-${categoryName.toLowerCase().replace(/\s+/g, '-')}`;
                                    isNullCategory = true;
                                    
                                    this.nullCategoryMapping[safeCategoryId] = {
                                        originalName: categoryName,
                                        roomTypeId: roomType.room_type.id,
                                        roomTypeName: roomType.room_type.name
                                    };
                                } else {
                                    safeCategoryId = categoryId;
                                }
                                
                                const categoryObj = {
                                    id: safeCategoryId,
                                    originalId: categoryId,
                                    name: categoryName,
                                    slug: category.category_slug,
                                    icon: this.getServiceIcon(categoryName),
                                    roomTypeId: roomType.room_type.id,
                                    roomTypeName: roomType.room_type.name,
                                    hasPrices: (category.stats?.total || 0) > 0,
                                    isNullCategory: isNullCategory,
                                    // ✅ Simpan FULL category data untuk debugging
                                    rawData: category, // ✅ LINE BARU
                                    roomSpecificData: category.prices?.room_specific || []
                                };
                                
                                roomTypeObj.categories.push(categoryObj);
                                
                                // Store in mapping
                                if (!this.roomTypeCategories[roomType.room_type.id]) {
                                    this.roomTypeCategories[roomType.room_type.id] = [];
                                }
                                this.roomTypeCategories[roomType.room_type.id].push(categoryObj);
                                
                                this.extractCategoryPricesV2(safeCategoryId, category, allDurationKeys); 
                            });
                        }
                        
                        this.roomTypes.push(roomTypeObj);
                    });
                    
                    // 🎯 GENERATE DURATIONS DARI DATA API
                    this.generateDurationsFromSet(allDurationKeys);
                    
                    // Set default active selections
                    if (this.roomTypes.length > 0) {
                        this.activeRoomType = this.roomTypes[0].id;
                        
                        const firstCategory = this.roomTypeCategories[this.activeRoomType];
                        if (firstCategory && firstCategory.length > 0) {
                            this.activeCategory = firstCategory[0].id;
                        }
                    }
                    
                } else {
                    console.warn('⚠️ Invalid API response format:', data);
                }
            } catch (error) {
                console.error('❌ Error fetching room types:', error);
                this.showToastMessage('Failed to load room types', 'error');
            }
        },

        generateDurations() {
            this.durations = [
                { id: 'hour-1', type: 'hour', value: 1, name: '1 Hour' },
                { id: 'hour-2', type: 'hour', value: 2, name: '2 Hours' },
                { id: 'hour-3', type: 'hour', value: 3, name: '3 Hours' },
                { id: 'hour-4', type: 'hour', value: 4, name: '4 Hours' },
                { id: 'day-1', type: 'day', value: 1, name: '1 Day' },
                { id: 'day-7', type: 'day', value: 7, name: '1 Week' },
                { id: 'day-30', type: 'day', value: 30, name: '1 Month' }
            ];
        },

        getServiceIcon(serviceName) {
            const icons = {
                'Small meeting': '🏢',
                'Big meeting': '🏢',
                'office': '🚪',
                'Executive': '👔',
                'Premiere': '⭐',
                'Empire': '👑',
                'Daily Pass': '🎫',
                'Student Pass': '🎓',
                'Membership Coworking Space': '👥'
            };
            return icons[serviceName] || '🏢';
        },

        getDurationTypeFromRequest(durationDisplay) {
            if (durationDisplay.includes('Hour')) return 'hour';
            if (durationDisplay.includes('Day')) return 'day';
            if (durationDisplay.includes('Week')) return 'day'; // 7 days
            if (durationDisplay.includes('Month')) return 'day'; // 30 days
            return 'hour';
        },

        getDurationValue(durationDisplay) {
            const match = durationDisplay.match(/\d+/);
            return match ? parseInt(match[0]) : 1;
        },

        formatRelativeTime(dateString) {
            if (!dateString) return '';
            
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);
            
            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins} minutes ago`;
            if (diffHours < 24) return `${diffHours} hours ago`;
            if (diffDays < 7) return `${diffDays} days ago`;
            return date.toLocaleDateString();
        },

        calculateStats() {
            if (!Array.isArray(this.priceRequests) || this.priceRequests.length === 0) {
                this.stats = {
                    pendingRequests: 0,
                    activeRequests: 0,
                    rejectedRequests: 0,
                    inactiveRequests: 0,
                    totalRequests: 0,
                    totalRoomTypes: this.roomTypes.length,
                    totalCategories: Object.values(this.roomTypeCategories)
                        .reduce((total, categories) => total + categories.length, 0)
                };
                return;
            }
            
            // Hitung berdasarkan 4 status utama
            const pending = this.priceRequests.filter(r => r.status === 'pending').length;
            const active = this.priceRequests.filter(r => r.status === 'active').length;
            const rejected = this.priceRequests.filter(r => r.status === 'rejected').length;
            const inactive = this.priceRequests.filter(r => r.status === 'inactive').length;
            
            this.stats = {
                pendingRequests: pending,
                activeRequests: active,
                rejectedRequests: rejected,
                inactiveRequests: inactive,
                totalRequests: this.priceRequests.length,
                totalRoomTypes: this.roomTypes.length,
                totalCategories: Object.values(this.roomTypeCategories)
                    .reduce((total, categories) => total + categories.length, 0)
            };
            
        },

        getFilteredRequests() {
            if (!Array.isArray(this.priceRequests) || this.priceRequests.length === 0) {
                return [];
            }
            
            if (this.activeRequestFilter === 'all') {
                return this.priceRequests;
            }
            
            return this.priceRequests.filter(request => {
                const status = (request.status || '').toLowerCase();
                return status === this.activeRequestFilter;
            });
        },

        getStatusBadgeClass(status) {
            const statusMap = {
                'pending': 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                'active': 'bg-green-100 text-green-800 border border-green-200',
                'rejected': 'bg-red-100 text-red-800 border border-red-200',
                'inactive': 'bg-gray-100 text-gray-800 border border-gray-200'
            };
            return statusMap[status] || 'bg-gray-100 text-gray-800 border border-gray-200';
        },

        getStatusDisplay(status) {
            const statusDisplay = {
                'pending': '⏳ PENDING',
                'active': '✅ ACTIVE',
                'rejected': '❌ REJECTED',
                'inactive': '⏸️ INACTIVE'
            };
        },

        getServiceName(serviceId) {
            return this.services.find(s => s.id === serviceId)?.name || '';
        },

        getDurationName(durationId) {
            const duration = this.durations.find(d => d.id === durationId);
            return duration ? duration.name : '';
        },

        get availableCategoriesForApplyAll() {
            if (!this.requestForm.room_type_id) return [];
            return this.roomTypeCategories[this.requestForm.room_type_id] || [];
        },

        onApplyToAllToggle() {
            console.group('🔄 onApplyToAllToggle');
            console.log('Toggle to:', this.requestForm.apply_to_all);
            
            if (this.requestForm.apply_to_all) {
                // ✅ SWITCHING TO BULK MODE
                console.log('📌 Switching to BULK mode');
                
                // Copy service_category_id to category_id
                if (this.requestForm.service_category_id) {
                    this.requestForm.category_id = this.requestForm.service_category_id;
                    console.log('✅ Copied service_category_id to category_id:', 
                            this.requestForm.category_id);
                }
                
                // Clear single-mode specific fields
                this.requestForm.price_id = null;
                this.requestForm.room_id = '';
                
                console.log('State after switch to bulk:', {
                    category_id: this.requestForm.category_id,
                    room_type_id: this.requestForm.room_type_id,
                    room_id: this.requestForm.room_id
                });
                
            } else {
                // ✅ SWITCHING TO SINGLE MODE
                console.log('📌 Switching to SINGLE mode');
                
                // Copy category_id to service_category_id
                if (this.requestForm.category_id) {
                    this.requestForm.service_category_id = this.requestForm.category_id;
                    console.log('✅ Copied category_id to service_category_id:', 
                            this.requestForm.service_category_id);
                }
                
                // Clear bulk-mode specific field
                this.requestForm.category_id = '';
                
                console.log('State after switch to single:', {
                    service_category_id: this.requestForm.service_category_id,
                    room_type_id: this.requestForm.room_type_id
                });
            }
            
            // Reset current price display
            this.updateCurrentPrice();
            this.validationErrors = [];
            
            console.groupEnd();
        },

        async submitRequest() {
            
            if (this.isSubmitting) {
                console.groupEnd();
                return;
            }
            
            this.validationErrors = [];
            this.isSubmitting = true;
            
            try {
                // ✅ VALIDATION dengan satu function
                if (!this.validateForm()) {
                    this.showToastMessage('Please fix validation errors', 'error');
                    this.isSubmitting = false;
                    console.groupEnd();
                    return;
                }
                
                // ✅ PAYLOAD PREPARATION untuk CREATE
                let requestData;
                
                if (this.requestForm.apply_to_all) {
                    // ✅ BULK MODE CREATE
                    let categoryId = this.requestForm.category_id;
                    
                    // Convert "null-xxx" to null
                    if (categoryId && typeof categoryId === 'string' && categoryId.startsWith('null-')) {
                        categoryId = null;
                    } else if (categoryId) {
                        categoryId = parseInt(categoryId);
                    }
                    
                    requestData = {
                        apply_to_all: true,
                        category_id: categoryId,
                        duration_type: this.requestForm.duration_type,
                        duration: parseInt(this.requestForm.duration),
                        room_type_id: parseInt(this.requestForm.room_type_id),
                        proposed_price: parseFloat(this.requestForm.base_price),
                        reason: this.requestForm.reason.trim(),
                        coffee_break_price: this.requestForm.coffee_break_price ? 
                            parseFloat(this.requestForm.coffee_break_price) : null,
                        deposit: this.requestForm.deposit ? 
                            parseFloat(this.requestForm.deposit) : null
                    };
                    
                } else {
                    // ✅ SINGLE MODE CREATE
                    requestData = {
                        apply_to_all: false,
                        price_id: parseInt(this.requestForm.price_id),
                        proposed_price: parseFloat(this.requestForm.base_price),
                        reason: this.requestForm.reason.trim(),
                        coffee_break_price: this.requestForm.coffee_break_price ? 
                            parseFloat(this.requestForm.coffee_break_price) : null,
                        deposit: this.requestForm.deposit ? 
                            parseFloat(this.requestForm.deposit) : null
                    };
                    
                }
                
                // ✅ VALIDASI USER LOCATION (hanya untuk CREATE)
                if (!this.userLocationId) {
                    this.validationErrors.push('User location not found. Please contact admin.');
                    this.showToastMessage('Cannot submit: User location not configured', 'error');
                    this.isSubmitting = false;
                    console.groupEnd();
                    return;
                }
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                
                if (!csrfToken) {
                    throw new Error('CSRF token not found');
                }
                
                const response = await fetch('/pricing/api/request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'include',
                    body: JSON.stringify(requestData)
                });

                const data = await response.json();

                // ✅ HANDLE CREATE RESPONSE
                if (data.success) {
                    this.showRequestModal = false;
                    
                    let message;
                    if (this.requestForm.apply_to_all) {
                        message = `✅ Price change submitted for ${data.rooms_affected} room(s)`;
                        if (data.user_location_name) {
                            message += ` at ${data.user_location_name}`;
                        }
                    } else {
                        message = '✅ Price change request submitted!';
                        if (data.user_location_name) {
                            message += ` for location: ${data.user_location_name}`;
                        }
                    }
                    
                    this.showToastMessage(message, 'success');
                    
                    // ✅ REFRESH DATA
                    await this.fetchAllRequests();
                    await this.fetchRoomTypesWithPrices();
                    this.calculateStats();
                    
                    // ✅ RESET FORM
                    this.resetRequestForm();
                    
                } else {
                    console.error('❌ CREATE Backend error:', data);
                    
                    if (data.errors) {
                        this.validationErrors = Object.values(data.errors).flat();
                    } else if (data.error) {
                        this.validationErrors = [data.error];
                    } else {
                        this.validationErrors = ['Failed to submit request'];
                    }
                    
                    this.showToastMessage(this.validationErrors[0], 'error');
                }
                
            } catch (error) {
                console.error('❌ CREATE Network error:', error);
                this.validationErrors = ['Network error. Please try again.'];
                this.showToastMessage('Failed to submit request', 'error');
            } finally {
                this.isSubmitting = false;
                console.groupEnd();
            }
        },

        async updateRequest() {
            
            if (this.isUpdating || !this.editingRequestId) {
                console.groupEnd();
                return;
            }
            
            this.editValidationErrors = [];
            this.isUpdating = true;
            
            try {
                // ✅ VALIDATION dengan satu function (auto detect UPDATE mode)
                if (!this.validateForm()) {
                    this.showToastMessage('Please fix validation errors', 'error');
                    this.isUpdating = false;
                    console.groupEnd();
                    return;
                }
                
                // ✅ PAYLOAD untuk UPDATE (sederhana)
                const requestData = {
                    proposed_price: parseFloat(this.requestForm.base_price),
                    reason: this.requestForm.reason.trim()
                };
            
                
                // ✅ SEND UPDATE REQUEST
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                
                if (!csrfToken) {
                    throw new Error('CSRF token not found');
                }
                
                const response = await fetch(`/pricing/api/requests/${this.editingRequestId}/update`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'include',
                    body: JSON.stringify(requestData)
                });

                const data = await response.json();

                // ✅ HANDLE UPDATE RESPONSE
                if (data.success) {
                    this.showRequestModal = false;
                    this.showToastMessage('✅ Request updated successfully!', 'success');
                    
                    // ✅ REFRESH DATA
                    await this.fetchAllRequests();
                    this.calculateStats();
                    
                    // ✅ RESET
                    this.resetRequestForm();
                    
                } else {
                    
                    if (data.errors) {
                        this.editValidationErrors = Object.values(data.errors).flat();
                    } else if (data.error) {
                        this.editValidationErrors = [data.error];
                    } else {
                        this.editValidationErrors = ['Failed to update request'];
                    }
                    
                    this.showToastMessage(this.editValidationErrors[0], 'error');
                }
                
            } catch (error) {
                this.editValidationErrors = ['Network error. Please try again.'];
                this.showToastMessage('Failed to update request', 'error');
            } finally {
                this.isUpdating = false;
            }
        },

        async deleteRequest() {
            
            if (this.isDeleting || !this.deletingRequestId) {
                console.groupEnd();
                return;
            }
            
            this.isDeleting = true;
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                
                if (!csrfToken) {
                    throw new Error('CSRF token not found');
                }
                
                const response = await fetch(`/pricing/api/requests/${this.deletingRequestId}/delete`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'include'
                });

                const data = await response.json();

                if (data.success) {
                    this.showDeleteRequestModal = false;
                    this.showToastMessage('✅ Request deleted successfully!', 'success');
                    
                    // Remove from local array
                    const index = this.priceRequests.findIndex(r => r.rawId === this.deletingRequestId);
                    if (index !== -1) {
                        this.priceRequests.splice(index, 1);
                    }
                    
                    // Recalculate stats
                    this.calculateStats();
                    
                    // Clear reference
                    this.deletingRequestId = null;
                    
                } else {
                    this.showToastMessage(data.error || 'Failed to delete request', 'error');
                }
                
            } catch (error) {
                console.error('❌ DELETE Network error:', error);
                this.showToastMessage('Network error. Please try again.', 'error');
            } finally {
                this.isDeleting = false;
            }
        },

        // ✅ TAMBAH METHOD UNTUK RESET FORM
        resetRequestForm() {
            this.requestForm = {
                apply_to_all: false,
                price_id: null,
                service_category_id: '',
                category_id: '',
                room_type_id: '',
                room_id: '',
                duration_type: '',
                duration: '',
                base_price: '',
                reason: '',
                coffee_break_option: '',
                coffee_break_price: null,
                deposit: null,
                contextInfo: ''
            };
            this.isEditMode = false;
            this.editingRequestId = null;
            this.validationErrors = [];
            this.editValidationErrors = [];
            this.currentPrice = 0;
        },

        // ✅ ONLY ONE HELPER - Validation
        validateForm() {
            const errors = [];
            const isCreateMode = !this.isEditMode;
            
            // ✅ COMMON VALIDATION (untuk CREATE & UPDATE)
            if (!this.requestForm.base_price || parseFloat(this.requestForm.base_price) <= 0) {
                errors.push('Proposed price is required');
            }
            
            if (!this.requestForm.reason || this.requestForm.reason.trim().length === 0) {
                errors.push('Reason is required');
            } else if (this.requestForm.reason.length > 500) {
                errors.push('Reason must be 500 characters or less');
            }
            
            // ✅ CREATE-SPECIFIC VALIDATION
            if (isCreateMode) {
                if (this.requestForm.apply_to_all) {
                    // BULK MODE CREATE
                    if (!this.requestForm.room_type_id) {
                        errors.push('Room type is required');
                    }
                    if (!this.requestForm.duration_type) {
                        errors.push('Duration type is required');
                    }
                    if (!this.requestForm.duration || this.requestForm.duration < 1) {
                        errors.push('Duration must be at least 1');
                    }
                } else {
                    // SINGLE MODE CREATE
                    if (!this.requestForm.price_id) {
                        errors.push('Price selection is required');
                    }
                }
            }
            
            // ✅ ASSIGN ERRORS KE PROPERTY YANG TEPAT
            if (isCreateMode) {
                this.validationErrors = errors;
            } else {
                this.editValidationErrors = errors;
            }
            
            return errors.length === 0;
        },

        openRequestModal(category = null, roomId = null) {
            console.group('🚪 openRequestModal');
            console.log('Called with:', { category, roomId, type: typeof category });
            
            // ✅ NORMALIZE PARAMETER
            if (typeof category === 'string' || typeof category === 'number') {
                const categoryId = category;
                category = null;
                
                for (const roomType of this.roomTypes) {
                    for (const cat of roomType.categories) {
                        if (cat.id == categoryId) {
                            category = cat;
                            console.log('Found category object:', cat);
                            break;
                        }
                    }
                    if (category) break;
                }
                
                if (!category) {
                    console.error('Category not found with ID:', categoryId);
                    this.showToastMessage('Category data not found', 'error');
                    console.groupEnd();
                    return;
                }
            }
            
            // ✅ RESET STATE
            this.validationErrors = [];
            this.isSubmitting = false;
            this.isEditMode = false;
            this.editingRequestId = null;
            
            // ✅ DETERMINE MODE FROM CONTEXT
            const isBulkMode = !roomId;  // Jika tidak ada roomId, assume bulk mode
            
            console.log('Detected mode:', isBulkMode ? 'BULK' : 'SINGLE');
            
            // ✅ INITIALIZE FORM WITH PROPER MODE
            this.requestForm = {
                price_id: null,
                service_category_id: !isBulkMode ? (category?.id || '') : '',
                room_type_id: category?.roomTypeId || this.activeRoomType || '',
                room_id: roomId || '',
                duration_type: '',
                duration: '',
                base_price: '',
                reason: '',
                coffee_break_option: '',
                coffee_break_price: null,
                deposit: null,
                contextInfo: '',
                
                // ✅ SET BULK MODE PROPERLY
                apply_to_all: isBulkMode,
                category_id: isBulkMode ? (category?.id || '') : '',
            };
            
            console.log('Form initialized:', {
                mode: isBulkMode ? 'BULK' : 'SINGLE',
                category_id: this.requestForm.category_id,
                service_category_id: this.requestForm.service_category_id,
                room_id: this.requestForm.room_id,
                apply_to_all: this.requestForm.apply_to_all
            });
            
            // ✅ SET CONTEXT INFO
            if (category) {
                if (isBulkMode) {
                    this.requestForm.contextInfo = 
                        `Requesting bulk price change for ALL rooms in ${category.name}`;
                } else {
                    this.requestForm.contextInfo = roomId ? 
                        `Requesting price change for ${this.getRoomName(roomId)} in ${category.name}` :
                        `Requesting price change for ${category.name}`;
                }
                
                // ✅ AUTO-SELECT DURATION
                if (roomId && category.id) {
                    this.autoSelectFirstAvailableDuration(category.id, roomId);
                } else if (isBulkMode && category.id) {
                    // For bulk mode, select first available duration for category
                    this.autoSelectFirstDurationForCategory(category.id);
                }
            }
            
            this.showRequestModal = true;
            console.groupEnd();
        },

        autoSelectFirstDurationForCategory(categoryId) {
            console.group('⏱️ autoSelectFirstDurationForCategory');
            console.log('Category ID:', categoryId);
            
            // Get all available durations for this category
            const availableDurations = this.durations.filter(duration => {
                const price = this.getPriceForDuration(categoryId, duration.id, 'all');
                return price > 0;
            });
            
            console.log('Available durations:', availableDurations.length);
            
            if (availableDurations.length > 0) {
                const firstDuration = availableDurations[0];
                
                this.requestForm.duration_type = firstDuration.type;
                this.requestForm.duration = firstDuration.value;
                
                console.log('✅ Auto-selected:', {
                    type: firstDuration.type,
                    value: firstDuration.value,
                    name: firstDuration.name
                });
                
                // Trigger price lookup
                this.updateCurrentPrice();
            } else {
                console.log('⚠️ No available durations found');
            }
            
            console.groupEnd();
        },

        openEditRequestModal(request) {
            
            // ✅ RESET STATE
            this.validationErrors = [];
            this.isSubmitting = false;
            this.isEditMode = true;
            this.editingRequestId = request.rawId;
            
            // ✅ DETERMINE REQUEST TYPE
            const isRoomSpecific = !!request.room_id;
            
            // ✅ DECODE DURATION
            let durationType = 'hour';
            let durationValue = 1;
            
            if (request.durationName) {
                const match = request.durationName.match(/(\d+)\s+(\w+)/);
                if (match) {
                    durationValue = parseInt(match[1]);
                    const type = match[2].toLowerCase();
                    if (type.includes('hour')) durationType = 'hour';
                    else if (type.includes('day')) durationType = 'day';
                    else if (type.includes('week')) durationType = 'day';
                    else if (type.includes('month')) durationType = 'day';
                }
            }
            
            // ✅ HANDLE SERVICE CATEGORY
            let serviceCategoryId = '';
            if (request.category_id) {
                serviceCategoryId = request.category_id;
            } else if (request.serviceName) {
                // Fallback: cari berdasarkan nama
                for (const service of this.services) {
                    if (service.name.toLowerCase() === request.serviceName.toLowerCase()) {
                        serviceCategoryId = service.id;
                        break;
                    }
                }
            }
            
            // ✅ HANDLE ROOM TYPE
            let roomTypeId = '';
            if (request.room_type_id) {
                roomTypeId = request.room_type_id;
            } else if (request.roomTypeName && this.roomTypes.length > 0) {
                // Fallback: cari berdasarkan nama
                for (const roomType of this.roomTypes) {
                    if (roomType.name.toLowerCase() === request.roomTypeName.toLowerCase()) {
                        roomTypeId = roomType.id;
                        break;
                    }
                }
            }
            
            // ✅ FILL FORM
            this.requestForm = {
                price_id: request.rawId,
                service_category_id: serviceCategoryId,
                room_type_id: roomTypeId,
                room_id: request.room_id || '', // ✅ AKAN ADA JIKA ROOM-SPECIFIC
                duration_type: request.duration_type || durationType,
                duration: request.duration || durationValue,
                base_price: request.proposedPrice || request.base_price || '',
                reason: request.reason || request.request_reason || '',
                coffee_break_option: request.coffee_break_option || '',
                coffee_break_price: request.coffee_break_price || null,
                deposit: request.deposit || null,
                contextInfo: this.buildEditContext(request, isRoomSpecific),
                apply_to_all: !isRoomSpecific, // ✅ FALSE jika room-specific, TRUE jika general
                category_id: serviceCategoryId === 'null' ? 'null' : ''
            };
            
            // ✅ SET CURRENT PRICE
            this.currentPrice = request.currentPrice || request.previous_price || 0;
            
            this.showRequestModal = true;
        },

        // ✅ HELPER: Build context info dengan room info
        buildEditContext(request, isRoomSpecific) {
            let context = `Editing: ${request.serviceName || 'Request'}`;
            
            if (isRoomSpecific && request.roomName) {
                context += ` - Room: ${request.roomName}`;
                if (request.roomNumber) {
                    context += ` (${request.roomNumber})`;
                }
            } else {
                context += ` - ${request.roomTypeName || 'Room Type'}`;
            }
            
            context += ` - ${request.durationName || 'Duration'}`;
            
            if (isRoomSpecific) {
                context += ' (Room-specific)';
            } else {
                context += ' (Applies to ALL rooms)';
            }
            
            return context;
        },

        openDeleteRequestModal(request) {
            this.deletingRequest = request;
            this.deletingRequestId = request.rawId;
            this.showDeleteRequestModal = true;
            this.isDeleting = false;
        },

        autoSelectFirstAvailableDuration(categoryId, roomId) {
            
            // Cari durations yang available untuk category+room ini
            const availableDurations = this.getFilteredDurationsForRoom(categoryId, roomId);
            
            if (availableDurations.length > 0) {
                const firstDuration = availableDurations[0];
                
                this.requestForm.duration_type = firstDuration.type;
                this.requestForm.duration = firstDuration.value;
                
                // Trigger price lookup
                this.updateCurrentPrice();
            }
        },

        // ✅ TAMBAH METHOD UNTUK GET DURATIONS PER ROOM
        getFilteredDurationsForRoom(categoryId, roomId) {
            if (!categoryId || !roomId) return [];
            
            return this.durations.filter(duration => {
                const price = this.getPriceForDuration(categoryId, duration.id, roomId);
                return price > 0;
            });
        },

        onServiceCategoryChange() {
            // Reset dependent fields saat service category berubah
            this.requestForm.room_type_id = '';
            this.requestForm.room_id = '';
            this.requestForm.duration_type = '';
            this.requestForm.duration = '';
            this.requestForm.proposed_price = '';
        },

        findPriceId() {
            // Validasi input
            if (!this.requestForm.service_category_id || 
                !this.requestForm.duration_type || 
                !this.requestForm.duration) {
                return null;
            }
            
            // Cari price_id
            const durationKey = `${this.requestForm.duration_type}-${this.requestForm.duration}`;
            
            // ✅ Langsung panggil findPriceIdInData dengan parameter yang benar
            return this.findPriceIdInData(
                this.requestForm.service_category_id,
                this.requestForm.room_id || null,
                durationKey
            );
        },

        findPriceIdInData(categoryId, roomId, durationKey) {
            console.group('🎯 SIMPLE findPriceIdInData');
            console.log('Looking for:', { categoryId, roomId, durationKey });
            
            // Convert to string untuk comparison yang konsisten
            const searchCatId = String(categoryId || '');
            const searchRoomId = String(roomId || '');
            
            console.log('Normalized:', { searchCatId, searchRoomId, durationKey });
            
            // SIMPLE SEARCH - langsung loop data
            for (const roomType of this.roomTypes) {
                for (const category of roomType.categories) {
                    // Cek jika category match
                    const catId = String(category.id || '');
                    const origId = String(category.originalId || '');
                    
                    const isCategoryMatch = catId === searchCatId || origId === searchCatId;
                    
                    if (isCategoryMatch) {
                        console.log(`✅ Category match: ${category.name}`);
                        
                        // Cek jika ada price data
                        if (!category.rawData?.prices) {
                            console.log('❌ No price data');
                            continue;
                        }
                        
                        // Cari di room_specific
                        if (category.rawData.prices.room_specific) {
                            for (const roomPrice of category.rawData.prices.room_specific) {
                                if (roomPrice.room && String(roomPrice.room.id) === searchRoomId) {
                                    console.log(`✅ Room match: ${roomPrice.room.name}`);
                                    
                                    if (roomPrice.prices) {
                                        for (const price of roomPrice.prices) {
                                            const priceKey = `${price.duration_type}-${price.duration}`;
                                            
                                            if (priceKey === durationKey) {
                                                console.log(`🎯 Price found! ID: ${price.id}`);
                                                console.groupEnd();
                                                return price.id;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            console.log('❌ Price not found');
            console.groupEnd();
            return null;
        },

        updateCurrentPrice() {
            console.group('💰 updateCurrentPrice');
            
            // ✅ DETERMINE CATEGORY ID based on mode
            let categoryId;
            
            if (this.requestForm.apply_to_all) {
                categoryId = this.requestForm.category_id;
                console.log('📌 BULK Mode, using category_id:', categoryId);
            } else {
                categoryId = this.requestForm.service_category_id;
                console.log('📌 SINGLE Mode, using service_category_id:', categoryId);
            }
            
            if (!categoryId) {
                console.log('❌ No category ID');
                this.requestForm.currentPrice = 0;
                this.requestForm.price_id = null;
                console.groupEnd();
                return;
            }
            
            if (!this.requestForm.duration_type || !this.requestForm.duration) {
                console.log('❌ Missing duration');
                this.requestForm.currentPrice = 0;
                this.requestForm.price_id = null;
                console.groupEnd();
                return;
            }
            
            const durationKey = `${this.requestForm.duration_type}-${this.requestForm.duration}`;
            const categoryIdStr = String(categoryId || '');
            
            console.log('Looking for price:', {
                categoryId: categoryIdStr,
                roomId: this.requestForm.room_id || 'all',
                durationKey: durationKey
            });
            
            // ✅ GET CURRENT PRICE
            const currentPrice = this.getPriceForDuration(
                categoryIdStr,
                durationKey,
                this.requestForm.room_id || 'all'
            );
            
            console.log('💵 Current price:', currentPrice);
            
            this.requestForm.currentPrice = currentPrice;
            
            // Auto-fill base price
            if (currentPrice > 0 && !this.requestForm.base_price) {
                this.requestForm.base_price = currentPrice;
                console.log('✅ Auto-filled base_price');
            }
            
            // ✅ FIND PRICE ID (ONLY for single mode)
            if (!this.requestForm.apply_to_all && 
                categoryIdStr && 
                !categoryIdStr.startsWith('roomtype-')) {
                
                console.log('🔍 Finding price_id for SINGLE mode...');
                
                const priceId = this.findPriceIdInData(
                    categoryIdStr,
                    this.requestForm.room_id || null,
                    durationKey
                );
                
                this.requestForm.price_id = priceId;
                console.log('🎯 Price ID:', priceId);
                
            } else {
                this.requestForm.price_id = null;
                console.log('ℹ️ Price ID not needed for BULK mode');
            }
            
            console.log('📊 Final state:', {
                price_id: this.requestForm.price_id,
                currentPrice: this.requestForm.currentPrice,
                base_price: this.requestForm.base_price,
                mode: this.requestForm.apply_to_all ? 'BULK' : 'SINGLE'
            });
            
            console.groupEnd();
        },

        formatNumber(num) {
            // Handle berbagai tipe input
            if (num === null || num === undefined || num === '' || isNaN(num)) {
                return '0';
            }
            
            // Convert ke number jika string
            const numberValue = typeof num === 'string' ? parseFloat(num) : num;
            
            if (isNaN(numberValue)) {
                return '0';
            }
            
            return new Intl.NumberFormat('id-ID').format(numberValue);
        },

        extractCategoryPricesV2(categoryId, categoryData, allDurationKeys) {
            console.group('📊 extractCategoryPricesV2 - QUICK FIX');
            
            // ✅ FORCE STRING CONVERSION
            const categoryIdStr = String(categoryId || '');
            
            console.log('Processing:', {
                inputCategoryId: categoryId,
                categoryIdStr: categoryIdStr,  // ✅ Forced string
                apiCategoryId: categoryData?.category_id,
                categoryName: categoryData?.category_name
            });
            
            if (!categoryIdStr || !categoryData) {
                console.log('❌ Invalid input');
                console.groupEnd();
                return;
            }
            
            // Initialize with STRING key
            if (!this.priceIdMap) {
                this.priceIdMap = {};
            }
            if (!this.basePrices[categoryIdStr]) {
                this.basePrices[categoryIdStr] = {};
            }
            if (!this.roomPrices[categoryIdStr]) {
                this.roomPrices[categoryIdStr] = {};
            }
            if (!this.categoryRoomsMap[categoryIdStr]) {
                this.categoryRoomsMap[categoryIdStr] = new Set();
            }
            
            const pricesData = categoryData.prices;
            
            // Process room-specific prices
            if (pricesData.room_specific && Array.isArray(pricesData.room_specific)) {
                console.log(`🏠 Processing ${pricesData.room_specific.length} room-specific entries`);
                
                pricesData.room_specific.forEach((roomPrice, index) => {
                    const room = roomPrice.room;
                    
                    if (!room || !room.id) {
                        console.warn(`⚠️ Invalid room at index ${index}`);
                        return;
                    }
                    
                    // ✅ FORCE STRING for room ID too
                    const roomIdStr = String(room.id);
                    
                    // Store room info
                    if (!this.rooms[roomIdStr]) {
                        this.rooms[roomIdStr] = {
                            id: room.id,  // Keep original type
                            name: room.name,
                            room_number: room.room_number,
                            floor: room.floor,
                            displayName: `${room.name}${room.floor ? ` (Floor ${room.floor})` : ''}`
                        };
                    }
                    
                    this.categoryRoomsMap[categoryIdStr].add(room.id);
                    
                    if (!this.roomPrices[categoryIdStr][roomIdStr]) {
                        this.roomPrices[categoryIdStr][roomIdStr] = {};
                    }
                    
                    if (!roomPrice.prices || !Array.isArray(roomPrice.prices)) {
                        console.warn(`⚠️ No prices for room ${roomIdStr}`);
                        return;
                    }
                    
                    roomPrice.prices.forEach((price, priceIndex) => {
                        const durationKey = `${price.duration_type}-${price.duration}`;
                        const priceValue = parseFloat(price.base_price) || 0;
                        const priceId = price.id;
                        
                        const priceObj = {
                            price: priceValue,
                            id: priceId,
                            durationKey: durationKey,
                            categoryId: categoryIdStr,
                            roomId: roomIdStr
                        };
                        
                        // Store
                        this.roomPrices[categoryIdStr][roomIdStr][durationKey] = priceObj;
                        
                        if (!this.basePrices[categoryIdStr][durationKey]) {
                            this.basePrices[categoryIdStr][durationKey] = priceObj;
                        }
                        
                        // ✅ CRITICAL: Use string keys consistently
                        const lookupKey = `${categoryIdStr}-${roomIdStr}-${durationKey}`;
                        this.priceIdMap[lookupKey] = priceId;
                        
                        console.log(`  ✅ [${index}][${priceIndex}] Stored:`, {
                            lookupKey: lookupKey,
                            priceId: priceId,
                            price: priceValue
                        });
                        
                        if (allDurationKeys) {
                            allDurationKeys.add(durationKey);
                        }
                    });
                });
            }
            
            // Process general prices
            if (pricesData.general && Array.isArray(pricesData.general)) {
                console.log(`🌐 Processing ${pricesData.general.length} general prices`);
                
                pricesData.general.forEach((price, index) => {
                    const durationKey = `${price.duration_type}-${price.duration}`;
                    const priceValue = parseFloat(price.base_price) || 0;
                    const priceId = price.id;
                    
                    const priceObj = {
                        price: priceValue,
                        id: priceId,
                        durationKey: durationKey,
                        categoryId: categoryIdStr,
                        roomId: null
                    };
                    
                    this.basePrices[categoryIdStr][durationKey] = priceObj;
                    
                    if (!this.roomPrices[categoryIdStr]['0']) {
                        this.roomPrices[categoryIdStr]['0'] = {};
                    }
                    this.roomPrices[categoryIdStr]['0'][durationKey] = priceObj;
                    
                    const lookupKey = `${categoryIdStr}-0-${durationKey}`;
                    this.priceIdMap[lookupKey] = priceId;
                    
                    console.log(`  ✅ [${index}] General price stored:`, {
                        lookupKey: lookupKey,
                        priceId: priceId
                    });
                    
                    if (allDurationKeys) {
                        allDurationKeys.add(durationKey);
                    }
                });
            }
            
            // ✅ DIAGNOSTIC OUTPUT
            const priceIdMapKeys = Object.keys(this.priceIdMap)
                .filter(k => k.startsWith(categoryIdStr));
            
            console.log('✅ Finished processing:', {
                category: categoryData.category_name,
                categoryIdStr: categoryIdStr,
                roomsInCategory: this.categoryRoomsMap[categoryIdStr].size,
                priceIdMapKeysForThisCategory: priceIdMapKeys.length,
                sampleKeys: priceIdMapKeys.slice(0, 3)
            });
            
            console.groupEnd();
        },

        generateDurationsFromSet(durationKeysSet) {
        if (!durationKeysSet || durationKeysSet.size === 0) {
            // Fallback ke defaults jika tidak ada data
            this.generateDefaultDurations();
            return;
        }
    
        const durationKeys = Array.from(durationKeysSet);
        
        // Sort durations: hour dulu, lalu day, week, month, year
        durationKeys.sort((a, b) => {
            const [typeA, valueA] = a.split('-');
            const [typeB, valueB] = b.split('-');
            
            // Order by type
            const typeOrder = { hour: 1, day: 2, week: 3, month: 4, year: 5 };
            const orderA = typeOrder[typeA] || 99;
            const orderB = typeOrder[typeB] || 99;
            
            if (orderA !== orderB) return orderA - orderB;
            
            // Order by value within same type
            return parseInt(valueA) - parseInt(valueB);
        });
        
        // Convert to durations array
        this.durations = durationKeys.map(key => {
            const [type, value] = key.split('-');
            const valueNum = parseInt(value);
            
            // Buat nama display yang friendly
            let name = '';
            let displayName = '';
            
            if (type === 'hour') {
                name = valueNum === 1 ? '1 Hour' : `${valueNum} Hours`;
                displayName = `${valueNum} ${valueNum === 1 ? 'Hour' : 'Hours'}`;
            } 
            else if (type === 'day') {
                if (valueNum === 1) {
                    name = '1 Day';
                    displayName = '1 Day';
                } else if (valueNum === 7) {
                    name = '1 Week';
                    displayName = '1 Week (7 Days)';
                } else if (valueNum === 30) {
                    name = '1 Month';
                    displayName = '1 Month (30 Days)';
                } else {
                    name = `${valueNum} Days`;
                    displayName = `${valueNum} Days`;
                }
            }
            else if (type === 'week') {
                name = valueNum === 1 ? '1 Week' : `${valueNum} Weeks`;
                displayName = `${valueNum} ${valueNum === 1 ? 'Week' : 'Weeks'}`;
            }
            else if (type === 'month') {
                name = valueNum === 1 ? '1 Month' : `${valueNum} Months`;
                displayName = `${valueNum} ${valueNum === 1 ? 'Month' : 'Months'}`;
            }
            else if (type === 'year') {
                name = valueNum === 1 ? '1 Year' : `${valueNum} Years`;
                displayName = `${valueNum} ${valueNum === 1 ? 'Year' : 'Years'}`;
            }
            else {
                name = `${valueNum} ${type}`;
                displayName = `${valueNum} ${type}`;
            }
            
            return {
                id: key,
                type: type,
                value: valueNum,
                name: name,
                displayName: displayName,
                key: key
            };
        });
    },

    // 1. Get rooms for a specific category
    // REPLACE function existing Anda (sekitar line 2500) dengan:
    getCategoryRooms(categoryId) {
        
        if (!categoryId) return [];
        
        // 1. Coba dari categoryRoomsMap (NEW)
        if (this.categoryRoomsMap && this.categoryRoomsMap[categoryId]) {
            const roomIds = Array.from(this.categoryRoomsMap[categoryId]);
            const rooms = roomIds.map(id => this.rooms[id]).filter(Boolean);
            return rooms;
        }
        
        // 2. Fallback: cari dari roomPrices structure
        if (this.roomPrices && this.roomPrices[categoryId]) {
            const roomIds = Object.keys(this.roomPrices[categoryId]);
            const rooms = roomIds
                .map(id => this.rooms[id])
                .filter(room => room && room.id && room.id !== '0'); // Exclude virtual room 0
            return rooms;
        }
        
        return [];
    },

    // 2. Get room name by ID
    getRoomName(roomId) {
        if (!roomId) return '';
        if (roomId === 'all') return 'All Rooms';
        
        // Cari di rooms object
        const room = this.rooms[roomId];
        if (room) {
            return room.name || `Room ${roomId}`;
        }
        
        // Fallback: coba cari di semua data
        for (const [id, roomData] of Object.entries(this.rooms)) {
            if (id == roomId) { // Use loose comparison
                return roomData.name || `Room ${roomId}`;
            }
        }
        
        return `Room ${roomId}`;
    },

    // 3. Get room info
    getRoomInfo(roomId) {
        if (roomId === 'all') return {};
        return this.rooms[roomId] || {};
    },

    // 4. Get price for specific duration and room
    getPriceForDuration(categoryId, durationId, roomId = 'all') {
        // ✅ FIX 1: Extract ID dari object jika diperlukan
        let categoryIdStr = categoryId;
        
        if (categoryId && typeof categoryId === 'object') {
            categoryIdStr = categoryId.id || categoryId.originalId || null;
            
            if (categoryIdStr && typeof categoryIdStr === 'object') {
                categoryIdStr = categoryIdStr.id || categoryIdStr.toString();
            }
        }
        
        // ✅ FIX 2: Convert ke string untuk konsistensi
        categoryIdStr = String(categoryIdStr || '');
        
        if (!categoryIdStr || !durationId) {
            return 0;
        }
        
        // ✅ FIX 3: Perbaiki kondisi .startsWith()
        if (typeof categoryIdStr === 'string' && categoryIdStr.startsWith('roomtype-')) {
            const roomTypeId = categoryIdStr.replace('roomtype-', '');
            
            // Coba cari harga dengan beberapa cara:
            // 1. Cari di basePrices dengan key khusus
            const priceFromBase = this.basePrices[`roomtype-${roomTypeId}`]?.[durationId] || 
                                this.basePrices[roomTypeId]?.[durationId];
            if (priceFromBase) {
                // ✅ MODIFIKASI: Handle jika priceFromBase adalah object
                if (priceFromBase && typeof priceFromBase === 'object') {
                    return priceFromBase.price || priceFromBase;
                }
                return priceFromBase;
            }
            
            // 2. Cari di semua categories untuk room type ini
            const roomTypeCategories = this.roomTypeCategories[roomTypeId];
            if (roomTypeCategories && roomTypeCategories.length > 0) {
                for (const category of roomTypeCategories) {
                    const price = this.basePrices[category.id]?.[durationId];
                    if (price) {
                        // ✅ MODIFIKASI: Handle jika price adalah object
                        if (price && typeof price === 'object') {
                            return price.price || price;
                        }
                        return price;
                    }
                }
            }
            return 0;
        }
        
        // ✅ FIX 4: Gunakan categoryIdStr yang sudah dinormalisasi
        if (roomId !== 'all' && roomId !== null && roomId !== undefined) {
            const roomSpecificPrice = this.roomPrices[categoryIdStr]?.[roomId]?.[durationId];
            
            if (roomSpecificPrice !== undefined) {
                // ✅ MODIFIKASI: Extract price jika itu object
                if (roomSpecificPrice && typeof roomSpecificPrice === 'object') {
                    return roomSpecificPrice.price || roomSpecificPrice;
                }
                return roomSpecificPrice;
            }
        }
        
        const basePrice = this.basePrices[categoryIdStr]?.[durationId] || 0;
        
        // ✅ MODIFIKASI: Handle jika basePrice adalah object
        if (basePrice && typeof basePrice === 'object') {
            return basePrice.price || basePrice;
        }
        
        return basePrice;
    },

    // 5. Get filtered durations untuk category
    getFilteredDurations(categoryId) {
        
        if (!categoryId || !this.durations) {
            return [];
        }
        
        // CASE 1: "All Rooms" selected
        if (this.activeRoom === 'all') {
            const durationsWithPrice = this.durations.filter(duration => {
                // Cek apakah category punya price untuk duration ini
                const hasPrice = this.basePrices[categoryId]?.[duration.id] > 0;
                
                return hasPrice;
            });
            
            return durationsWithPrice;
        }
        
        // CASE 2: Specific room selected
        if (this.activeRoom && this.activeRoom !== 'all') {
            const durationsWithPrice = this.durations.filter(duration => {
                const price = this.getPriceForDuration(categoryId, duration.id, this.activeRoom);
                const hasPrice = price > 0;
                
                return hasPrice;
            });
            
            return durationsWithPrice;
        }
        
        // CASE 3: No room selected (shouldn't happen)
        return [];
    },

        // 6. Count prices in category
        getCategoryPriceCount(categoryId) {
            if (this.activeRoom === 'all') {
                return Object.keys(this.basePrices[categoryId] || {}).length;
            }
            // Count untuk room tertentu
            return this.getFilteredDurations(categoryId).length;
        },

        getRoomTypeIcon(roomTypeName) {
            const icons = {
                'Private Office': '🏢',
                'Meeting Room': '👥',
                'Event Space': '🎉',
                'Coworking Space': '💻',
                'Virtual Office': '🌐',
                'Sharing Room': '👥'
            };
            return icons[roomTypeName] || '🏢';
        },

        showToastMessage(message, type = 'success') {
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;
            setTimeout(() => {
                this.showToast = false;
            }, 3000);
        }
    };
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