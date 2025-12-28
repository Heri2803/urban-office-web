{{-- resources/views/admin/booking/room-assignment.blade.php --}}

@extends('layouts.admin')

@section('title', 'Room Assignment')

@section('content')
<div x-data="roomAssignment()" x-init="init()" class="space-y-6 pb-20 md:pb-6">

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">🚪 Room Assignment</h1>
            <p class="text-sm text-gray-500 mt-1">Assign customers to specific rooms</p>
            <p x-show="lastUpdated" class="text-xs text-gray-400 mt-1">
                Last updated: <span x-text="lastUpdated"></span>
                <span x-show="isLoading" class="text-blue-500"> (Updating...)</span>
            </p>
        </div>
        <div class="flex gap-2">
            <button 
                @click="refreshData()" 
                :disabled="isLoading"
                :class="isLoading ? 'bg-blue-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                class="px-4 py-2 text-white rounded-lg transition text-sm font-medium flex items-center gap-2"
            >
                <svg x-show="!isLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <svg x-show="isLoading" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span x-text="isLoading ? 'Refreshing...' : '🔄 Refresh'"></span>
            </button>
            <button @click="showLegend = !showLegend" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                📖 Legend
            </button>
        </div>
    </div>

    {{-- Legend Modal/Dropdown --}}
    <div x-show="showLegend" x-transition class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <span>📖</span>
            <span>Room Status Legend</span>
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex items-start gap-3 p-3 bg-green-50 rounded-lg border border-green-200">
                <div class="w-6 h-6 bg-green-500 rounded flex-shrink-0 mt-0.5"></div>
                <div>
                    <p class="font-semibold text-green-900 text-sm">Available</p>
                    <p class="text-xs text-green-700 mt-1">Room is empty and ready to be assigned to customers</p>
                </div>
            </div>
            
            <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <div class="w-6 h-6 bg-blue-500 rounded flex-shrink-0 mt-0.5 relative">
                    <svg class="absolute -top-1 -right-1 w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-blue-900 text-sm">Booked</p>
                    <p class="text-xs text-blue-700 mt-1">Reserved for future use, not yet started. Shows countdown to check-in</p>
                </div>
            </div>
            
            <div class="flex items-start gap-3 p-3 bg-red-50 rounded-lg border border-red-200">
                <div class="w-6 h-6 bg-red-500 rounded flex-shrink-0 mt-0.5 relative">
                    <div class="absolute -top-1 -right-1 w-2 h-2 bg-red-600 rounded-full animate-pulse"></div>
                </div>
                <div>
                    <p class="font-semibold text-red-900 text-sm">Occupied</p>
                    <p class="text-xs text-red-700 mt-1">Currently in use by customer. Shows remaining time until checkout</p>
                </div>
            </div>
            
            <div class="flex items-start gap-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                <div class="w-6 h-6 bg-yellow-500 rounded flex-shrink-0 mt-0.5"></div>
                <div>
                    <p class="font-semibold text-yellow-900 text-sm">Maintenance</p>
                    <p class="text-xs text-yellow-700 mt-1">Under repair or cleaning, temporarily unavailable</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Service Tabs --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        {{-- Tab Headers --}}
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="flex min-w-max md:min-w-0">
                {{-- Ganti hardcoded services dengan dynamic --}}
                <template x-for="service in services" :key="service.id">
                    <button 
                        @click="activeTab = service.id; selectedRoom = null; selectedCustomer = null;"
                        :class="activeTab === service.id ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 md:px-6 py-3 md:py-4 border-b-2 font-medium text-sm whitespace-nowrap transition"
                    >
                        <span x-text="service.icon"></span>
                        <span x-text="service.name" class="ml-2"></span>
                        <span x-text="`(${service.rooms.length})`" class="ml-1 text-xs opacity-75"></span>
                    </button>
                </template>

                {{-- Tambahkan loading state --}}
                <div x-show="isLoading" class="text-center py-8">
                    <div class="inline-flex items-center gap-2 text-gray-500">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Loading rooms data...</span>
                    </div>
                </div>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="p-4 md:p-6">
            <template x-for="service in services" :key="service.id">
                <div x-show="activeTab === service.id" x-transition>
                    
                    {{-- Info Bar --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">ℹ️</span>
                            <div>
                                <h4 class="font-semibold text-blue-900 mb-1">Assignment Instructions</h4>
                                <p class="text-sm text-blue-700">
                                    1. Select an available room by clicking the green button<br>
                                    2. Choose a customer with settlement status from dropdown<br>
                                    3. Click "Confirm Assignment" to assign customer to room
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Stats Summary --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <p class="text-xs text-gray-500 mb-1">Total Rooms</p>
                            <p class="text-xl font-bold text-gray-800" x-text="getRoomsByService(service.id).length"></p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                            <p class="text-xs text-green-700 mb-1">Available</p>
                            <p class="text-xl font-bold text-green-600" x-text="getRoomsByStatus(service.id, 'available').length"></p>
                        </div>
                        <div class="bg-red-50 rounded-lg p-3 border border-red-200">
                            <p class="text-xs text-red-700 mb-1">Occupied</p>
                            <p class="text-xl font-bold text-red-600" x-text="getRoomsByStatus(service.id, 'occupied').length"></p>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-200">
                            <p class="text-xs text-yellow-700 mb-1">Maintenance</p>
                            <p class="text-xl font-bold text-yellow-600" x-text="getRoomsByStatus(service.id, 'maintenance').length"></p>
                        </div>
                    </div>

                    {{-- Room Selection Grid --}}
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <span x-text="service.icon"></span>
                            Select Room Number
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                            <template x-for="room in getRoomsByService(service.id)" :key="room.id">
                                <button
                                    @click="selectRoom(room)"
                                    :disabled="room.status !== 'available'"
                                    :class="[
                                        getStatusColor(room.status),
                                        room.status === 'available' && selectedRoom?.id === room.id ? 'ring-4 ring-green-300' : '',
                                        room.status !== 'available' ? 'cursor-not-allowed' : 'hover:scale-105'
                                    ]"
                                    class="relative p-3 rounded-lg text-white font-semibold text-sm transition-all transform disabled:hover:scale-100"
                                >
                                    {{-- Room Number --}}
                                    <div x-text="room.number" class="text-xl font-bold mb-1"></div>
                                    
                                    {{-- Status Badge --}}
                                    <div 
                                        :class="getStatusBadgeColor(room.status)"
                                        class="text-xs px-2 py-1 rounded-full font-medium capitalize mb-1"
                                        x-text="room.status"
                                    ></div>
                                    
                                    {{-- ✅ BARU: Remaining Time Display --}}
                                    <template x-if="room.remainingTime && (room.status === 'booked' || room.status === 'occupied')">
                                        <div class="text-xs mt-1 bg-orange bg-opacity-20 rounded px-2 py-1">
                                            <span x-text="formatRemainingTime(room)"></span>
                                        </div>
                                    </template>
                                    
                                    {{-- Selected Checkmark --}}
                                    <template x-if="room.status === 'available' && selectedRoom?.id === room.id">
                                        <div class="absolute top-2 right-2">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </template>
                                    
                                    {{-- Occupied Indicator --}}
                                    <template x-if="room.status === 'occupied'">
                                        <div class="absolute top-2 right-2 w-3 h-3 bg-white rounded-full animate-pulse"></div>
                                    </template>
                                    
                                    {{-- Booked Indicator --}}
                                    <template x-if="room.status === 'booked'">
                                        <div class="absolute top-2 right-2">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    </template>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Assignment Form --}}
                    <div x-show="selectedRoom" x-transition class="bg-gray-50 rounded-xl p-4 md:p-6 border-2 border-blue-200">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                                <span x-text="selectedRoom?.number"></span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">
                                    Room <span x-text="selectedRoom?.number"></span>
                                </h3>
                                <p class="text-sm text-gray-600" x-text="service.name"></p>
                            </div>
                        </div>

                        {{-- Customer Selection --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Select Customer (Settlement Status Only)
                                </label>
                                <select 
                                    x-model="selectedCustomer"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                >
                                    <option value="">-- Choose Customer --</option>
                                    <template x-for="customer in getSettlementCustomers(service.id)" :key="customer.orderId">
                                        <option :value="customer.orderId">
                                            <span x-text="customer.orderId"></span> - 
                                            <span x-text="customer.name"></span> - 
                                            <span x-text="customer.duration"></span>
                                        </option>
                                    </template>
                                </select>
                                
                                {{-- Customer Details --}}
                                <template x-if="selectedCustomer">
                                    <div class="mt-3 p-3 bg-white rounded-lg border border-gray-200">
                                        <template x-for="customer in getSettlementCustomers(service.id)" :key="customer.orderId">
                                            <div x-show="customer.orderId === selectedCustomer">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                                    <div>
                                                        <span class="text-gray-500">Name:</span>
                                                        <span class="font-medium ml-2" x-text="customer.name"></span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Email:</span>
                                                        <span class="font-medium ml-2" x-text="customer.email"></span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Phone:</span>
                                                        <span class="font-medium ml-2" x-text="customer.phone"></span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Duration:</span>
                                                        <span class="font-medium ml-2" x-text="customer.duration"></span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Check-in:</span>
                                                        <span class="font-medium ml-2" x-text="customer.checkIn"></span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Price:</span>
                                                        <span class="font-medium ml-2" x-text="'Rp ' + formatNumber(customer.price)"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                {{-- No Customers Available --}}
                                <div x-show="getSettlementCustomers(service.id).length === 0" class="mt-3 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <p class="text-sm text-yellow-800">⚠️ No customers with settlement status available for this service.</p>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex flex-col sm:flex-row gap-3">
                                <button 
                                    @click="confirmAssignment()"
                                    :disabled="!selectedCustomer"
                                    :class="selectedCustomer ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-300 cursor-not-allowed'"
                                    class="flex-1 px-6 py-3 text-white rounded-lg font-semibold transition text-sm"
                                >
                                    ✅ Confirm Assignment
                                </button>
                                <button 
                                    @click="selectedRoom = null; selectedCustomer = null;"
                                    class="flex-1 sm:flex-initial px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition text-sm"
                                >
                                    ❌ Cancel
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Occupied Rooms List --}}
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <span>Currently Occupied & Booked Rooms</span>
                            <span 
                                class="text-sm font-normal text-gray-500"
                                x-text="`(${getRoomsByStatus(service.id, 'occupied').length + getRoomsByStatus(service.id, 'booked').length})`"
                            ></span>
                        </h3>
                        
                        <div class="space-y-3">
                            {{-- Occupied Rooms --}}
                            <template x-for="room in getRoomsByStatus(service.id, 'occupied')" :key="room.id">
                                <div class="bg-white rounded-lg border-2 border-red-200 p-4 hover:shadow-md transition">
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                                        <div class="flex items-center gap-4">
                                            <div class="w-14 h-14 bg-red-500 rounded-lg flex items-center justify-center text-white font-bold text-xl relative">
                                                <span x-text="room.number"></span>
                                                <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-600 rounded-full animate-pulse"></div>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <p class="font-semibold text-gray-800" x-text="room.booking?.customerName"></p>
                                                    <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded-full font-medium">
                                                        OCCUPIED
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-500" x-text="room.booking?.email"></p>
                                                <p class="text-sm text-gray-500" x-text="room.booking?.phone"></p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
                                            {{-- ✅ Booking Info dengan Countdown --}}
                                            <div class="text-sm bg-red-50 rounded-lg px-4 py-2 border border-red-200">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span class="font-semibold text-red-900" x-text="room.remainingTime || 'Calculating...'"></span>
                                                </div>
                                                <p class="text-xs text-gray-600">
                                                    Started: <span class="font-medium" x-text="room.booking?.bookingDate + ' ' + room.booking?.startTime"></span>
                                                </p>
                                                <p class="text-xs text-gray-600">
                                                    Duration: <span class="font-medium" x-text="room.booking?.durationText"></span>
                                                </p>
                                            </div>
                                            
                                            <button 
                                                @click="cancelAssignment(room)"
                                                class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm font-medium whitespace-nowrap"
                                            >
                                                ✕ Release Room
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            {{-- ✅ BARU: Booked Rooms (Future bookings) --}}
                            <template x-for="room in getRoomsByStatus(service.id, 'booked')" :key="room.id">
                                <div class="bg-white rounded-lg border-2 border-blue-200 p-4 hover:shadow-md transition">
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                                        <div class="flex items-center gap-4">
                                            <div class="w-14 h-14 bg-blue-500 rounded-lg flex items-center justify-center text-white font-bold text-xl relative">
                                                <span x-text="room.number"></span>
                                                <svg class="absolute -top-1 -right-1 w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <p class="font-semibold text-gray-800" x-text="room.booking?.customerName"></p>
                                                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded-full font-medium">
                                                        BOOKED
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-500" x-text="room.booking?.email"></p>
                                                <p class="text-sm text-gray-500" x-text="room.booking?.phone"></p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
                                            <div class="text-sm bg-blue-50 rounded-lg px-4 py-2 border border-blue-200">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span class="font-semibold text-blue-900" x-text="room.remainingTime || 'Calculating...'"></span>
                                                </div>
                                                <p class="text-xs text-gray-600">
                                                    Check-in: <span class="font-medium" x-text="room.booking?.bookingDate + ' ' + room.booking?.startTime"></span>
                                                </p>
                                                <p class="text-xs text-gray-600">
                                                    Duration: <span class="font-medium" x-text="room.booking?.durationText"></span>
                                                </p>
                                            </div>
                                            
                                            <button 
                                                @click="cancelAssignment(room)"
                                                class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm font-medium whitespace-nowrap"
                                            >
                                                ✕ Cancel Booking
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Empty State --}}
                            <div 
                                x-show="getRoomsByStatus(service.id, 'occupied').length === 0 && getRoomsByStatus(service.id, 'booked').length === 0" 
                                class="text-center py-12 bg-gray-50 rounded-lg"
                            >
                                <div class="text-gray-400">
                                    <svg class="w-20 h-20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    <p class="text-base font-medium text-gray-600">No occupied or booked rooms</p>
                                    <p class="text-sm mt-1 text-gray-500">All rooms are available for assignment</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Confirmation Modal --}}
    <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showConfirmModal = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">✅</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Confirm Assignment</h3>
                    <p class="text-gray-600 mb-6">
                        Assign <span class="font-semibold" x-text="confirmData.customerName"></span> 
                        to Room <span class="font-semibold" x-text="confirmData.roomNumber"></span>?
                    </p>
                    <div class="flex gap-3">
                        <button 
                            @click="processAssignment()"
                            class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold transition"
                        >
                            Yes, Confirm
                        </button>
                        <button 
                            @click="showConfirmModal = false"
                            class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel/Release Modal --}}
    <div x-show="showCancelModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showCancelModal = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">⚠️</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Release Room</h3>
                    <p class="text-gray-600 mb-6">
                        Release Room <span class="font-semibold" x-text="cancelData.roomNumber"></span> 
                        from <span class="font-semibold" x-text="cancelData.customerName"></span>?
                    </p>
                    <div class="flex gap-3">
                        <button 
                            @click="processCancel()"
                            class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition"
                        >
                            Yes, Release
                        </button>
                        <button 
                            @click="showCancelModal = false"
                            class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Success Toast --}}
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
function roomAssignment() {
    return {
        // UI State
        showLegend: false,
        showConfirmModal: false,
        showCancelModal: false,
        showToast: false,
        toastMessage: '',
        activeTab: 'meeting-room',
        
        // Selection State
        selectedRoom: null,
        selectedCustomer: null,
        
        // Modal Data
        confirmData: {},
        cancelData: {},
        
        // Data Collections
        services: [],
        rooms: [],
        customers: [], // Untuk kompatibilitas
        
        // Loading & Status
        isLoading: false,
        lastUpdated: null,
        isOnline: true,
        
        // Intervals
        refreshInterval: null,
        countdownInterval: null,
        dataSyncInterval: null,

        async init() {
            console.log('🚀 Room Assignment initialized - REAL-TIME MODE');
            await this.loadInitialData();
            
            // ✅ REAL-TIME: Frontend countdown setiap detik
            this.setupSecondBySecondUpdates();
            
            // ✅ DATA SYNC: Polling untuk new bookings (30 detik)
            this.setupDataSync();
            
            console.log('✅ Real-time system activated');
        },

        setupSecondBySecondUpdates() {
            this.countdownInterval = setInterval(() => {
                this.updateAllRoomStatusesRealTime();
            }, 1000);
        },

        setupDataSync() {
            this.dataSyncInterval = setInterval(async () => {
                await this.syncNewBookings();
            }, 30000);
        },

        async loadInitialData() {
            try {
                this.isLoading = true;
                const response = await fetch('/booking/room-assignment/api/rooms-status?t=' + Date.now());
                const data = await response.json();
                
                if (data.success) {
                    this.services = data.service_categories;
                    this.rooms = data.all_rooms;
                    this.lastUpdated = new Date().toLocaleTimeString('id-ID');
                    
                    this.updateAllRoomStatusesRealTime();
                    console.log('✅ Initial data loaded:', this.rooms.length, 'rooms');
                }
            } catch (error) {
                console.error('❌ Initial load failed:', error);
                this.showError('Failed to load initial data: ' + error.message);
            } finally {
                this.isLoading = false;
            }
        },

        updateAllRoomStatusesRealTime() {
            const now = new Date();
            let hasChanges = false;
            
            this.rooms.forEach(room => {
                if (room.booking) {
                    const newStatus = this.calculateRealTimeStatus(room, now);
                    const timeInfo = this.calculateRealTimeDisplay(room, now);
                    
                    if (room.status !== newStatus || 
                        room.remainingTime !== timeInfo.display || 
                        room.remainingMinutes !== timeInfo.minutes) {
                        
                        room.status = newStatus;
                        room.remainingTime = timeInfo.display;
                        room.remainingMinutes = timeInfo.minutes;
                        hasChanges = true;
                    }
                    
                    if (newStatus === 'available' && room.booking) {
                        console.log(`🔄 Auto-cleaning expired booking: Room ${room.number}`);
                        room.booking = null;
                        room.remainingTime = null;
                        room.remainingMinutes = null;
                        hasChanges = true;
                    }
                } else if (room.status !== 'available' && room.status !== 'maintenance') {
                    room.status = 'available';
                    room.remainingTime = null;
                    room.remainingMinutes = null;
                    hasChanges = true;
                }
            });
            
            if (hasChanges) {
                this.rooms = [...this.rooms];
            }
        },

        calculateRealTimeStatus(room, now) {
            if (!room.booking) return 'available';
            if (room.status === 'maintenance') return 'maintenance';
            
            try {
                const startTime = new Date(room.booking.startDateTime);
                const endTime = new Date(room.booking.endDateTime);
                
                if (now < startTime) {
                    return 'booked';
                } else if (now <= endTime) {
                    return 'occupied';
                } else {
                    return 'available';
                }
            } catch (error) {
                console.error('Status calculation error for room:', room.number, error);
                return 'available';
            }
        },

        calculateRealTimeDisplay(room, now) {
            if (!room.booking) return { display: null, minutes: null };
            
            try {
                const startTime = new Date(room.booking.startDateTime);
                const endTime = new Date(room.booking.endDateTime);
                
                let targetTime, prefix;
                
                if (now < startTime) {
                    targetTime = startTime;
                    prefix = 'Starts in';
                } else if (now <= endTime) {
                    targetTime = endTime;
                    prefix = 'remaining';
                } else {
                    return { display: null, minutes: 0 };
                }
                
                const diffMs = targetTime - now;
                if (diffMs <= 0) {
                    return { display: null, minutes: 0 };
                }
                
                const totalMinutes = Math.floor(diffMs / 60000);
                const days = Math.floor(totalMinutes / 1440);
                const hours = Math.floor((totalMinutes % 1440) / 60);
                const minutes = totalMinutes % 60;
                
                let display;
                if (days > 0) {
                    display = `${days}d ${hours}h ${prefix}`;
                } else if (hours > 0) {
                    display = `${hours}h ${minutes}m ${prefix}`;
                } else {
                    display = `${minutes}m ${prefix}`;
                }
                
                return { display, minutes: totalMinutes };
            } catch (error) {
                console.error('Time calculation error for room:', room.number, error);
                return { display: null, minutes: null };
            }
        },

        async syncNewBookings() {
            try {
                const response = await fetch('/booking/room-assignment/api/rooms-status?t=' + Date.now());
                const data = await response.json();
                
                if (data.success) {
                    this.mergeRoomData(data.all_rooms);
                    this.lastUpdated = new Date().toLocaleTimeString('id-ID');
                }
            } catch (error) {
                console.log('🔄 Sync failed, will retry');
                this.isOnline = false;
            }
        },

        mergeRoomData(newRooms) {
            let changes = false;
            
            newRooms.forEach(newRoom => {
                const existingIndex = this.rooms.findIndex(r => r.id === newRoom.id);
                
                if (existingIndex === -1) {
                    this.applyRealTimeStatusToRoom(newRoom);
                    this.rooms.push(newRoom);
                    changes = true;
                    console.log('➕ New room added:', newRoom.number);
                } else {
                    const existingRoom = this.rooms[existingIndex];
                    const existingBookingId = existingRoom.booking?.orderId;
                    const newBookingId = newRoom.booking?.orderId;
                    
                    if (existingBookingId !== newBookingId) {
                        this.applyRealTimeStatusToRoom(newRoom);
                        this.rooms[existingIndex] = newRoom;
                        changes = true;
                        console.log('🔄 Room updated:', newRoom.number);
                    }
                }
            });
            
            if (changes) {
                this.rooms = [...this.rooms];
                console.log('📥 Room data synced');
            }
        },

        applyRealTimeStatusToRoom(room) {
            if (room.booking) {
                const now = new Date();
                room.status = this.calculateRealTimeStatus(room, now);
                const timeInfo = this.calculateRealTimeDisplay(room, now);
                room.remainingTime = timeInfo.display;
                room.remainingMinutes = timeInfo.minutes;
            }
        },

        // ✅ METHOD YANG DIBUTUHKAN OLEH HTML:

        // Untuk room selection
        selectRoom(room) {
            if (room.status === 'available') {
                this.selectedRoom = room;
                this.selectedCustomer = null;
                console.log('✅ Room selected:', room.number);
            } else {
                console.log('❌ Room not available:', room.number, room.status);
            }
        },

        // Untuk customer dropdown (jika masih digunakan)
        getSettlementCustomers(serviceId) {
            // ✅ Return empty array karena kita tidak butuh assignment functionality
            return [];
            
            // Atau jika ingin tetap load customers, uncomment:
            /*
            if (!this.customers || this.customers.length === 0) {
                this.loadCustomersFromAPI();
            }
            return this.customers.filter(customer => 
                customer.service_category_id === this.getServiceIdFromTab(serviceId)
            );
            */
        },

        getServiceIdFromTab(serviceTabId) {
            // Convert service tab ID ke service category ID
            if (serviceTabId === 'meeting-room') return 1;
            if (serviceTabId.startsWith('room-type-')) {
                return parseInt(serviceTabId.replace('room-type-', ''));
            }
            return 1;
        },

        async loadCustomersFromAPI() {
            try {
                const response = await fetch('/booking/room-assignment/api/available-customers');
                const data = await response.json();
                
                if (data.success) {
                    this.customers = data.customers;
                }
            } catch (error) {
                console.error('❌ Customers load failed:', error);
            }
        },

        // Untuk room management
        getRoomsByService(serviceId) {
            const service = this.services.find(s => s.id === serviceId);
            return service ? service.rooms : [];
        },

        getRoomsByStatus(serviceId, status) {
            return this.getRoomsByService(serviceId).filter(room => room.status === status);
        },

        // Modal functions
        confirmAssignment() {
            if (!this.selectedRoom || !this.selectedCustomer) {
                this.showError('Please select both room and customer');
                return;
            }

            this.confirmData = {
                roomNumber: this.selectedRoom.number,
                customerName: 'Customer Name', // Ganti dengan data customer sebenarnya
                roomId: this.selectedRoom.id,
                customerOrderId: this.selectedCustomer
            };
            this.showConfirmModal = true;
        },

        cancelAssignment(room) {
            if (!room.booking) {
                this.showError('This room is not occupied');
                return;
            }

            this.cancelData = {
                roomNumber: room.number,
                customerName: room.booking.customerName || 'Customer',
                roomId: room.id
            };
            this.showCancelModal = true;
        },

        processAssignment() {
            console.log('Processing assignment...');
            this.showConfirmModal = false;
            this.selectedRoom = null;
            this.selectedCustomer = null;
            this.showSuccess('Assignment processed successfully!');
        },

        processCancel() {
            console.log('Processing cancel...');
            this.showCancelModal = false;
            this.showSuccess('Room released successfully!');
        },

        // UI Helper Functions
        getStatusColor(status) {
            const colors = {
                'available': 'bg-green-500 hover:bg-green-600',
                'booked': 'bg-blue-500',
                'occupied': 'bg-red-500',
                'maintenance': 'bg-yellow-500'
            };
            return colors[status] || 'bg-gray-500';
        },

        getStatusBadgeColor(status) {
            const colors = {
                'available': 'bg-green-100 text-green-700',
                'booked': 'bg-blue-100 text-blue-700',
                'occupied': 'bg-red-100 text-red-700',
                'maintenance': 'bg-yellow-100 text-yellow-700'
            };
            return colors[status] || 'bg-gray-100 text-gray-700';
        },

        formatRemainingTime(room) {
            if (!room.remainingTime) return '';
            
            if (room.status === 'booked') {
                return `⏰ ${room.remainingTime}`;
            } else if (room.status === 'occupied') {
                return `⏱️ ${room.remainingTime}`;
            }
            
            return room.remainingTime;
        },

        // Data Refresh
        async refreshData() {
            try {
                this.isLoading = true;
                await this.loadInitialData();
                this.showSuccess('Data refreshed successfully!');
                this.isOnline = true;
            } catch (error) {
                this.showError('Refresh failed: ' + error.message);
            } finally {
                this.isLoading = false;
            }
        },

        // Utility Functions
        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        },

        showSuccess(message) {
            this.toastMessage = message;
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 4000);
        },

        showError(message) {
            this.toastMessage = '❌ ' + message;
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 5000);
        },

        // Cleanup
        destroy() {
            if (this.countdownInterval) clearInterval(this.countdownInterval);
            if (this.dataSyncInterval) clearInterval(this.dataSyncInterval);
            if (this.refreshInterval) clearInterval(this.refreshInterval);
            console.log('🧹 Real-time system cleaned up');
        }
    }
}
</script>
@endpush

<style>
[x-cloak] { display: none !important; }
</style>
@endsection