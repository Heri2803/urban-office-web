{{-- resources/views/admin/rooms.blade.php --}}
@extends('layouts.admin')

@section('title', 'Room Management')

@section('content')
<div x-data="roomManagement()" x-init="init()" class="space-y-4 md:space-y-6 pb-20 md:pb-6 max-w-full overflow-hidden">
    
    {{-- Page Header dengan Refresh Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="min-w-0">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 truncate">🏢 Room Management</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Manage all rooms and their status</p>
        </div>
        <div class="flex gap-2">
            {{-- Refresh Button --}}
            <button 
                @click="manualRefresh()"
                :disabled="isLoading"
                :class="isLoading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-600'"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg transition text-sm font-medium flex items-center justify-center gap-2 whitespace-nowrap"
            >
                <svg x-show="!isLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <svg x-show="isLoading" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span class="hidden sm:inline" x-text="isLoading ? 'Loading...' : 'Refresh'"></span>
            </button>
            
            {{-- Add Room Button (Optional - jika masih diperlukan) --}}
            <button 
                @click="openAddModal()"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium flex items-center justify-center gap-2 whitespace-nowrap"
            >
                <span>➕</span>
                <span class="hidden sm:inline">Add New Room</span>
                <span class="sm:hidden">Add Room</span>
            </button>
        </div>
    </div>

    {{-- Loading State --}}
    <div x-show="isLoading" class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
        <div class="flex flex-col items-center justify-center space-y-3">
            <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600 font-medium">Loading room data...</p>
            <p class="text-sm text-gray-500">Fetching real-time status from bookings</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div x-show="!isLoading" class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 mb-1">Total Rooms</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-800" x-text="stats.total"></p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center text-xl sm:text-2xl flex-shrink-0">
                    🏢
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 mb-1">Available</p>
                    <p class="text-xl sm:text-2xl font-bold text-green-600" x-text="stats.available"></p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center text-xl sm:text-2xl flex-shrink-0">
                    ✅
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 mb-1">Occupied</p>
                    <p class="text-xl sm:text-2xl font-bold text-red-600" x-text="stats.occupied"></p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-100 rounded-lg flex items-center justify-center text-xl sm:text-2xl flex-shrink-0">
                    🔴
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 mb-1">Booked</p>
                    <p class="text-xl sm:text-2xl font-bold text-purple-600" x-text="stats.booked"></p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center text-xl sm:text-2xl flex-shrink-0">
                    📅
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 mb-1">Maintenance</p>
                    <p class="text-xl sm:text-2xl font-bold text-yellow-600" x-text="stats.maintenance"></p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-100 rounded-lg flex items-center justify-center text-xl sm:text-2xl flex-shrink-0">
                    🔧
                </div>
            </div>
        </div>
    </div>

    {{-- Service Tabs --}}
    <div x-show="!isLoading && rooms.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Tab Headers --}}
        <div class="border-b border-gray-200 overflow-x-auto scrollbar-hide">
            <nav class="flex">
                <template x-for="service in services" :key="service.id">
                    <button 
                        @click="activeTab = service.id"
                        :class="activeTab === service.id ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-4 sm:px-6 py-3 sm:py-4 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-2 flex-shrink-0"
                    >
                        <span x-text="service.icon"></span>
                        <span x-text="service.name"></span>
                        <span 
                            class="px-1.5 py-0.5 text-xs rounded-full"
                            :class="activeTab === service.id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'"
                            x-text="getRoomCount(service.id)"
                        ></span>
                    </button>
                </template>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="p-3 sm:p-4 md:p-6">
            <template x-for="service in services" :key="service.id">
                <div x-show="activeTab === service.id" x-transition>
                    
                    {{-- Room Grid - FIXED: Gunakan currentServiceRooms --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4">
                        <template x-for="room in currentServiceRooms" :key="room.id">
                            <div 
                                class="bg-white rounded-lg border-2 transition-all hover:shadow-lg p-3 sm:p-4"
                                :class="getRoomCardClasses(room)"
                            >
                                {{-- Room Number & Type --}}
                                <div class="text-center mb-3">
                                    <p class="text-2xl sm:text-3xl font-bold text-gray-800" x-text="room.number"></p>
                                    <p class="text-xs text-gray-500 mt-1 capitalize" x-text="room.room_type?.replace('_', ' ')"></p>
                                </div>

                                {{-- Status Badge --}}
                                <div class="flex justify-center mb-3">
                                    <span 
                                        class="px-2 sm:px-3 py-1 rounded-full text-xs font-semibold capitalize"
                                        :class="getStatusBadgeClasses(room)"
                                        x-text="getStatusDisplayText(room)"
                                    ></span>
                                </div>

                                {{-- Booking Information --}}
                                <template x-if="room.effectiveStatus === 'occupied'">
                                    <div class="mb-3 p-2 bg-red-100 rounded border border-red-300">
                                        <p class="text-xs text-red-700 mb-1">Occupied Until:</p>
                                        <p class="text-xs font-semibold text-red-800" x-text="room.bookingUntil"></p>
                                        <p class="text-xs text-red-600 mt-1" x-text="room.currentBooking?.nama_lengkap"></p>
                                    </div>
                                </template>

                                <template x-if="room.effectiveStatus === 'booked' && room.statusType === 'today'">
                                    <div class="mb-3 p-2 bg-blue-100 rounded border border-blue-300">
                                        <p class="text-xs text-blue-700 mb-1">Starts at:</p>
                                        <p class="text-xs font-semibold text-blue-800" x-text="room.bookingStarts"></p>
                                    </div>
                                </template>

                                <template x-if="room.effectiveStatus === 'booked' && room.statusType === 'future'">
                                    <div class="mb-3 p-2 bg-purple-100 rounded border border-purple-300">
                                        <p class="text-xs text-purple-700 mb-1">Booked for:</p>
                                        <p class="text-xs font-semibold text-purple-800" x-text="room.bookingDate"></p>
                                    </div>
                                </template>

                                {{-- Maintenance Info --}}
                                <div x-show="room.baseStatus === 'maintenance'" class="mb-3 p-2 bg-yellow-100 rounded border border-yellow-300">
                                    <p class="text-xs text-yellow-700 mb-1">Maintenance</p>
                                    <p class="text-xs font-semibold text-yellow-800" x-text="room.maintenanceReason"></p>
                                    <p class="text-xs text-yellow-600 mt-1" x-text="'Until: ' + (room.maintenanceEnd || 'Indefinite')"></p>
                                </div>

                                {{-- Last Updated --}}
                                <div class="text-center mb-3">
                                    <p class="text-xs text-gray-500">Last updated</p>
                                    <p class="text-xs font-medium text-gray-700" x-text="room.updatedAt"></p>
                                </div>

                                {{-- Actions - FIXED: Tambahkan .stop modifier --}}
                                <div class="space-y-1.5">
                                    <button 
                                        @click.stop="editRoom(room)"
                                        :disabled="!canEditRoom(room)"
                                        :class="canEditRoom(room) ? 'hover:bg-blue-200' : 'opacity-50 cursor-not-allowed'"
                                        class="w-full px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-xs font-medium transition"
                                    >
                                        ✏️ Edit
                                    </button>
                                    <button 
                                        @click.stop="openMaintenanceModal(room)"
                                        :disabled="!canSetMaintenance(room)"
                                        :class="canSetMaintenance(room) ? 'hover:bg-yellow-200' : 'opacity-50 cursor-not-allowed'"
                                        class="w-full px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded-lg text-xs font-medium transition"
                                    >
                                        🔧 Maintenance
                                    </button>
                                    <button 
                                        @click.stop="deleteRoom(room)"
                                        :disabled="!canDeleteRoom(room)"
                                        :class="canDeleteRoom(room) ? 'hover:bg-red-200' : 'opacity-50 cursor-not-allowed'"
                                        class="w-full px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-xs font-medium transition"
                                    >
                                        🗑️ Delete
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Empty State untuk Service --}}
                    <div 
                        x-show="currentServiceRooms.length === 0"
                        class="text-center py-12 sm:py-16 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300"
                    >
                        <div class="text-gray-400">
                            <div class="text-4xl sm:text-5xl mb-3" x-text="service.icon"></div>
                            <p class="text-base sm:text-lg font-medium text-gray-600 mb-2">No <span x-text="service.name"></span> rooms</p>
                            <p class="text-xs sm:text-sm text-gray-500 mb-4">No rooms found for this service type</p>
                        </div>
                    </div>

                </div>
            </template>
        </div>
    </div>

    {{-- Empty State untuk Semua Rooms --}}
    <div x-show="!isLoading && rooms.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="max-w-md mx-auto">
            <div class="text-6xl mb-4">🏢</div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">No Rooms Found</h3>
            <p class="text-gray-600 mb-6">
                No room data found in the system. Rooms are automatically detected from booking transactions.
            </p>
            <div class="space-y-3">
                <button 
                    @click="manualRefresh()"
                    class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                >
                    🔄 Refresh Data
                </button>
                <p class="text-xs text-gray-500">
                    Make sure you have booking transactions with room information
                </p>
            </div>
        </div>
    </div>

    {{-- Add/Edit Room Modal --}}
    <div x-show="showRoomModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                x-show="showRoomModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"></div>

            <!-- Modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6"
                x-show="showRoomModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <!-- Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900" x-text="editingRoom ? 'Edit Room' : 'Add New Room'"></h3>
                    <button @click="showRoomModal = false" class="text-gray-400 hover:text-gray-600 text-xl">
                        ✕
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="saveRoom()">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Service Selection -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Service *</label>
                            <select x-model="roomForm.service" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Select Service</option>
                                <template x-for="service in services" :key="service.id">
                                    <option :value="service.id" x-text="service.name"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Room Number -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Room Number *</label>
                            <input type="text" x-model="roomForm.number" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter room number" required>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select x-model="roomForm.status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="booked">Booked</option>
                            </select>
                        </div>

                        <!-- Floor -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Floor</label>
                            <input type="number" x-model="roomForm.floor" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Floor number" min="1" max="50">
                        </div>

                        <!-- Capacity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
                            <input type="number" x-model="roomForm.capacity" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Number of people" min="1" max="100">
                        </div>

                        <!-- Size (m²) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Size (m²)</label>
                            <input type="number" x-model="roomForm.size_m2" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Room size in m²" step="0.01" min="0">
                        </div>

                        <!-- Location ID -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location ID *</label>
                            <input type="number" x-model="roomForm.location_id" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Location ID" required min="1">
                            <p class="text-xs text-gray-500 mt-1">Default: 1 (sesuaikan dengan kebutuhan)</p>
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea x-model="roomForm.notes" 
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    rows="3" placeholder="Optional notes about the room"></textarea>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="showRoomModal = false" 
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <span x-text="editingRoom ? 'Update Room' : 'Add Room'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Maintenance Modal --}}
    <div x-show="showMaintenanceModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900" x-text="maintenanceForm.action === 'set' ? 'Set Maintenance' : 'Release Maintenance'"></h3>
                    <button @click="showMaintenanceModal = false" class="text-gray-400 hover:text-gray-600 text-xl">
                        ✕
                    </button>
                </div>

                <!-- Di dalam modal maintenance -->
                <form @submit.prevent="submitMaintenance()">
                    <div class="space-y-4">
                        <template x-if="maintenanceForm.action === 'set'">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                                    <input type="date" x-model="maintenanceForm.startDate" 
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date (Optional)</label>
                                    <input type="date" x-model="maintenanceForm.endDate" 
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason *</label>
                                    <textarea x-model="maintenanceForm.reason" 
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            rows="3" placeholder="Reason for maintenance" required></textarea>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" x-model="maintenanceForm.autoRelease" 
                                        id="autoRelease" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="autoRelease" class="ml-2 block text-sm text-gray-700">
                                        Auto-release after end date
                                    </label>
                                </div>
                            </div>
                        </template>

                        <template x-if="maintenanceForm.action === 'release'">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">
                                            Release Maintenance
                                        </h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <p>Are you sure you want to release <span x-text="selectedRoom ? selectedRoom.number : ''" class="font-semibold"></span> from maintenance?</p>
                                            <p class="mt-1">This will make the room available for bookings.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="showMaintenanceModal = false" 
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                :class="{'bg-green-600 hover:bg-green-700 focus:ring-green-500': maintenanceForm.action === 'release'}">
                            <span x-text="maintenanceForm.action === 'set' ? 'Set Maintenance' : 'Release Maintenance'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6">
                
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Room</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Are you sure you want to delete room 
                                <span x-text="deletingRoom ? deletingRoom.number : ''" class="font-semibold"></span>? 
                                This action cannot be undone.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button @click="confirmDelete()" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete
                    </button>
                    <button @click="showDeleteModal = false" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
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
function roomManagement() {
    return {
        activeTab: 'meeting-room',
        showRoomModal: false,
        showMaintenanceModal: false,
        showDeleteModal: false,
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        editingRoom: null,
        selectedRoom: null,
        deletingRoom: null,
        isLoading: true,

        stats: {
            total: 0,
            available: 0,
            occupied: 0,
            maintenance: 0,
            booked: 0
        },

        services: [
            { id: 'meeting-room', name: 'Meeting Room', icon: '🏢' },
            { id: 'private-office', name: 'Private Office', icon: '🚪' },
            { id: 'sharing-room', name: 'Sharing Room', icon: '👥' }
        ],

        rooms: [],

        roomForm: {
            service: '',
            number: '',
            status: 'available',
            notes: '',
            floor: '',
            capacity: '',
            size_m2: '',
        },

        maintenanceForm: {
            action: 'set',
            startDate: '',
            endDate: '',
            reason: '',
            autoRelease: false
        },

        async init() {
            await this.loadRoomsFromDatabase();
            this.startAutoRefresh();
        },

        // Tambahkan di dalam roomManagement() function, setelah init()
        mapServiceToRoomTypeId(service) {
            const mapping = {
                'private-office': 1,
                'sharing-room': 6,
                'meeting-room': null,
            };
            return mapping[service] ?? null;
        },

        getRoomTypeName(service) {
            const mapping = {
                'private-office': 'Private Office',
                'sharing-room': 'Sharing Room', 
                'meeting-room': 'Meeting Room',
            };
            return mapping[service] ?? 'Meeting Room';
        },

        // ✅ FIXED: Load rooms dengan service yang benar
        async loadRoomsFromDatabase() {
            this.isLoading = true;
            
            try {
                console.log('🔄 Loading rooms from API...');
                const roomsResponse = await fetch('/admin/room-management/unique-rooms');
                
                if (!roomsResponse.ok) {
                    throw new Error(`HTTP error! status: ${roomsResponse.status}`);
                }
                
                const roomsData = await roomsResponse.json();
                console.log('📊 API Response:', roomsData);
                
                if (roomsData.success) {
                    // ✅ FIX: Gunakan service langsung dari API, jangan mapping ulang
                    this.rooms = roomsData.data.map(room => {
                        console.log(`📝 Processing room: ${room.number} → service: ${room.service}`);
                        return {
                            id: room.id,
                            service: room.service, // ✅ LANGSUNG DARI API
                            number: room.number,   // ✅ GUNAKAN room.number, bukan "Room ${room.id}"
                            room_type: room.room_type,
                            room_type_id: room.room_type_id,
                            floor: room.floor,
                            capacity: room.capacity,
                            size_m2: room.size_m2,
                            baseStatus: room.base_status || 'available',
                            effectiveStatus: 'available',
                            statusType: 'free',
                            notes: '',
                            updatedAt: 'Loading...',
                            maintenanceStart: null,
                            maintenanceEnd: null,
                            maintenanceReason: null,
                            currentBooking: null,
                            bookingUntil: null,
                            bookingStarts: null,
                            bookingDate: null,
                            originalData: room
                        };
                    });
                    
                    // Debug service distribution
                    this.debugServiceDistribution();
                    
                    await this.refreshAllRoomStatuses();
                    this.showToastMessage(`Loaded ${this.rooms.length} rooms`, 'success');
                } else {
                    throw new Error('API returned unsuccessful response');
                }
                
            } catch (error) {
                console.error('Error loading rooms:', error);
                await this.generateFallbackRooms();
            } finally {
                this.isLoading = false;
            }
        },

        // ✅ ADD MISSING FUNCTIONS
        openAddModal(service = null) {
            this.editingRoom = null; // Pastikan ini null
            this.roomForm = {
                service: service?.id || this.activeTab, // Default ke active tab
                number: '',
                status: 'available',
                notes: '',
                floor: '',
                capacity: '',
                size_m2: '',
            };
            console.log('🔄 Opening add modal for service:', this.roomForm.service);
            this.showRoomModal = true;
        },

        editRoom(room) {
            console.log('🔄 Edit room clicked:', room);
            
            if (!this.canEditRoom(room)) {
                this.showToastMessage('Cannot edit occupied room', 'error');
                return;
            }

            try {
                this.editingRoom = room;
                this.roomForm = {
                    service: room.service,
                    number: room.number,
                    status: room.baseStatus,
                    floor: room.floor || '',
                    capacity: room.capacity || '',
                    size_m2: room.size_m2 || '',
                    location_id: room.location_id || 1,
                    notes: room.notes || ''
                };
                
                console.log('✅ Opening edit modal for room:', room.number);
                this.showRoomModal = true;
                
            } catch (error) {
                console.error('❌ Error in editRoom:', error);
                this.showToastMessage('Error opening edit form', 'error');
            }
        },

        async saveRoom() {
            if (!this.roomForm.service || !this.roomForm.number) {
                this.showToastMessage('Please fill all required fields', 'error');
                return;
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                
                console.log('🔄 Saving room...', this.roomForm);
                console.log('📝 Room ID:', this.editingRoom?.id);
                console.log('📝 Is editing:', !!this.editingRoom);

                if (this.editingRoom) {
                    // Update existing room
                    const payload = {
                        room_number: this.roomForm.number,
                        status: this.roomForm.status,
                        floor: this.roomForm.floor || null,
                        capacity: this.roomForm.capacity || null,
                        size_m2: this.roomForm.size_m2 || null,
                        location_id: this.roomForm.location_id,
                        notes: this.roomForm.notes || ''
                    };

                    console.log('📤 Update payload:', payload);

                    const response = await fetch('/admin/room-management/rooms/' + this.editingRoom.id, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(payload)
                    });

                    console.log('📡 Update response status:', response.status);
                    
                    let result;
                    try {
                        result = await response.json();
                        console.log('📡 Update response data:', result);
                    } catch (jsonError) {
                        console.error('❌ JSON parse error:', jsonError);
                        const text = await response.text();
                        console.error('❌ Raw response:', text);
                        throw new Error('Invalid JSON response from server: ' + text);
                    }

                    if (!response.ok) {
                        throw new Error(result.message || `HTTP error! status: ${response.status}`);
                    }

                    if (!result.success) {
                        throw new Error(result.message || 'Failed to update room');
                    }

                    // Update local data
                    const index = this.rooms.findIndex(r => r.id === this.editingRoom.id);
                    if (index !== -1) {
                        this.rooms[index] = {
                            ...this.rooms[index],
                            number: this.roomForm.number,
                            service: this.roomForm.service,
                            baseStatus: this.roomForm.status,
                            effectiveStatus: this.roomForm.status,
                            floor: this.roomForm.floor || null,
                            capacity: this.roomForm.capacity || null,
                            size_m2: this.roomForm.size_m2 || null,
                            location_id: this.roomForm.location_id,
                            notes: this.roomForm.notes,
                            updatedAt: 'Just now'
                        };
                        
                        await this.refreshRoomStatus(this.editingRoom.id);
                    }
                    
                    this.showToastMessage(result.message || 'Room updated successfully!', 'success');
                    
                } else {
                    // Add new room - PERBAIKI INI!
                    console.log('🔄 RAW FORM DATA:', this.roomForm);
                    
                    // Extract data dari Proxy object
                    const formData = JSON.parse(JSON.stringify(this.roomForm));
                    console.log('🔄 EXTRACTED FORM DATA:', formData);
                    
                    const payload = {
                        room_number: formData.number,
                        status: formData.status,
                        service: formData.service,
                        location_id: parseInt(formData.location_id) || 1,
                        notes: formData.notes || '',
                        floor: formData.floor ? parseInt(formData.floor) : null,  // ✅ PERBAIKI: ambil dari formData
                        capacity: formData.capacity ? parseInt(formData.capacity) : null,  // ✅ PERBAIKI: ambil dari formData
                        size_m2: formData.size_m2 ? parseFloat(formData.size_m2) : null  // ✅ PERBAIKI: ambil dari formData
                    };

                    console.log('📤 Create payload (FIXED):', payload);

                    const response = await fetch('/admin/room-management/rooms', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(payload)
                    });

                    console.log('📡 Create response status:', response.status);
                    
                    let result;
                    try {
                        result = await response.json();
                        console.log('📡 Create response data:', result);
                    } catch (jsonError) {
                        console.error('❌ JSON parse error:', jsonError);
                        const text = await response.text();
                        console.error('❌ Raw response:', text);
                        throw new Error('Invalid JSON response from server: ' + text);
                    }

                    if (!response.ok) {
                        throw new Error(result.message || `HTTP error! status: ${response.status}`);
                    }

                    if (!result.success) {
                        throw new Error(result.message || 'Failed to create room');
                    }

                    // Add to local data - juga perbaiki bagian ini
                    const newRoom = {
                        id: result.data.id,
                        service: formData.service,
                        number: formData.number,
                        room_type: this.getRoomTypeName(formData.service),
                        room_type_id: this.mapServiceToRoomTypeId(formData.service),
                        baseStatus: formData.status,
                        effectiveStatus: formData.status,
                        statusType: 'free',
                        floor: formData.floor ? parseInt(formData.floor) : null,  // ✅ PERBAIKI
                        capacity: formData.capacity ? parseInt(formData.capacity) : null,  // ✅ PERBAIKI
                        size_m2: formData.size_m2 ? parseFloat(formData.size_m2) : null,  // ✅ PERBAIKI
                        location_id: parseInt(formData.location_id) || 1,
                        notes: formData.notes,
                        updatedAt: 'Just now',
                        maintenanceStart: null,
                        maintenanceEnd: null,
                        maintenanceReason: null,
                        currentBooking: null
                    };
                    
                    this.rooms.push(newRoom);
                    this.showToastMessage(result.message || 'Room added successfully!', 'success');
                    
                    // Refresh status untuk room baru
                    await this.refreshRoomStatus(result.data.id);
                }

                this.showRoomModal = false;
                this.calculateStats();
                
            } catch (error) {
                console.error('❌ Error saving room:', error);
                this.showToastMessage('Error: ' + error.message, 'error');
            }
        },

        deleteRoom(room) {
            if (!this.canDeleteRoom(room)) {
                this.showToastMessage('Cannot delete room with active or future bookings', 'error');
                return;
            }
            this.deletingRoom = room;
            this.showDeleteModal = true;
        },

        async confirmDelete() {
            try {
                const response = await fetch('/admin/room-management/rooms/' + this.deletingRoom.id, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Failed to delete room');
                }

                // Remove from local data
                const index = this.rooms.findIndex(r => r.id === this.deletingRoom.id);
                if (index !== -1) {
                    this.rooms.splice(index, 1);
                    this.showToastMessage(result.message, 'success');
                    this.calculateStats();
                }
                
            } catch (error) {
                console.error('Error deleting room:', error);
                this.showToastMessage(error.message, 'error');
            }
            
            this.showDeleteModal = false;
        },

        openMaintenanceModal(room) {
            if (!this.canSetMaintenance(room)) {
                this.showToastMessage('Cannot set maintenance for this room', 'error');
                return;
            }

            this.selectedRoom = room;
            
            // Reset form based on current room status
            if (room.baseStatus === 'maintenance') {
                // Release maintenance mode
                this.maintenanceForm = {
                    action: 'release',
                    startDate: '',
                    endDate: '',
                    reason: '',
                    autoRelease: false
                };
            } else {
                // Set maintenance mode
                this.maintenanceForm = {
                    action: 'set',
                    startDate: this.getCurrentDate(),
                    endDate: '',
                    reason: '',
                    autoRelease: false
                };
            }
            
            console.log('🔄 Opening maintenance modal for room:', room.number);
            console.log('📝 Maintenance form:', this.maintenanceForm);
            this.showMaintenanceModal = true;
        },

        async submitMaintenance() {
            const index = this.rooms.findIndex(r => r.id === this.selectedRoom.id);
            if (index === -1) {
                this.showMaintenanceModal = false; // Tutup modal jika room tidak ditemukan
                return;
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                
                console.log('🔄 Submitting maintenance action:', this.maintenanceForm.action);

                // Prepare payload based on action
                let payload = {
                    status: this.maintenanceForm.action === 'set' ? 'maintenance' : 'available'
                };

                // Only send maintenance data when setting maintenance
                if (this.maintenanceForm.action === 'set') {
                    // Extract data dari Proxy
                    const formData = JSON.parse(JSON.stringify(this.maintenanceForm));
                    
                    payload = {
                        ...payload,
                        maintenance_start: formData.startDate,
                        maintenance_end: formData.endDate || null,
                        maintenance_reason: formData.reason
                    };
                }

                console.log('📤 Maintenance payload:', payload);

                const response = await fetch('/admin/room-management/rooms/' + this.selectedRoom.id + '/maintenance', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                });

                console.log('📡 Response status:', response.status);
                
                // Clone response untuk backup
                const responseClone = response.clone();
                
                let result;
                try {
                    result = await response.json();
                    console.log('📡 Response data:', result);
                    
                } catch (jsonError) {
                    console.error('❌ JSON parse error:', jsonError);
                    const text = await responseClone.text();
                    console.error('❌ Raw response:', text);
                    throw new Error('Invalid response from server');
                }

                if (!response.ok) {
                    if (result && result.errors) {
                        console.error('❌ VALIDATION ERRORS:', result.errors);
                        const errorMessages = Object.values(result.errors).flat().join(', ');
                        throw new Error('Validation: ' + errorMessages);
                    }
                    throw new Error(result?.message || `Server error: ${response.status}`);
                }

                if (!result.success) {
                    throw new Error(result.message || 'Failed to update maintenance status');
                }

                // ✅ UPDATE LOCAL DATA - Pastikan ini dieksekusi
                if (this.maintenanceForm.action === 'set') {
                    this.rooms[index].baseStatus = 'maintenance';
                    this.rooms[index].maintenanceStart = this.maintenanceForm.startDate;
                    this.rooms[index].maintenanceEnd = this.maintenanceForm.endDate || null;
                    this.rooms[index].maintenanceReason = this.maintenanceForm.reason;
                } else {
                    this.rooms[index].baseStatus = 'available';
                    this.rooms[index].maintenanceStart = null;
                    this.rooms[index].maintenanceEnd = null;
                    this.rooms[index].maintenanceReason = null;
                }

                this.rooms[index].updatedAt = 'Just now';
                
                // Refresh status
                await this.refreshRoomStatus(this.selectedRoom.id);
                
                // ✅ TUTUP MODAL SETELAH SUKSES
                this.showMaintenanceModal = false;
                this.calculateStats();
                
                this.showToastMessage(result.message, 'success');
                
            } catch (error) {
                console.error('❌ Error in maintenance action:', error);
                this.showToastMessage('Error: ' + error.message, 'error');
                
                // ✅ TUTUP MODAL MESKIPUN ADA ERROR (opsional)
                // this.showMaintenanceModal = false;
            }
        },

        // ✅ UTILITY FUNCTIONS
        getCurrentDate() {
            return new Date().toISOString().split('T')[0];
        },

        // ✅ Debug function untuk lihat service distribution
        debugServiceDistribution() {
            const serviceGroups = {};
            this.rooms.forEach(room => {
                if (!serviceGroups[room.service]) {
                    serviceGroups[room.service] = [];
                }
                serviceGroups[room.service].push(room.number);
            });
            console.log('🎯 SERVICE DISTRIBUTION:', serviceGroups);
            
            // Check jika ada masalah
            const uniqueServices = Object.keys(serviceGroups);
            console.log('🔍 UNIQUE SERVICES:', uniqueServices);
            
            if (uniqueServices.length === 1) {
                console.warn('⚠️ WARNING: All rooms have the same service:', uniqueServices[0]);
            }
        },

        // ✅ SINGLE refreshRoomStatus function (hapus duplikat)
        async refreshRoomStatus(roomId) {
            try {
                const room = this.rooms.find(r => r.id === roomId);
                if (!room) {
                    console.warn(`Room ${roomId} not found`);
                    return;
                }

                console.log(`🔄 Refreshing status for room ${room.number} (ID: ${roomId})`);
                const response = await fetch(`/admin/room-management/${roomId}/status`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.status !== 'error') {
                    room.effectiveStatus = data.status;
                    room.statusType = data.type;
                    room.currentBooking = data.booking;
                    room.updatedAt = new Date().toLocaleTimeString();
                    
                    if (data.status === 'occupied') {
                        room.bookingUntil = data.until;
                    } else if (data.status === 'booked' && data.type === 'today') {
                        room.bookingStarts = data.starts;
                    } else if (data.status === 'booked' && data.type === 'future') {
                        room.bookingDate = data.on_date;
                    }
                    
                    console.log(`✅ Room ${room.number} status: ${data.status}`);
                }
            } catch (error) {
                console.error(`Error refreshing status for room ${roomId}:`, error);
            }
        },

        async refreshAllRoomStatuses() {
            try {
                console.log('🔄 Refreshing all room statuses...');
                const promises = this.rooms.map(room => this.refreshRoomStatus(room.id));
                await Promise.all(promises);
                this.calculateStats();
            } catch (error) {
                console.error('Error refreshing room statuses:', error);
            }
        },

        // ✅ Enhanced getServiceRooms dengan debug
        getServiceRooms(serviceId) {
            const filteredRooms = this.rooms.filter(r => {
                const matches = r.service === serviceId;
                return matches;
            }).sort((a, b) => a.number.localeCompare(b.number));
            
            console.log(`🔍 Service "${serviceId}": ${filteredRooms.length} rooms`, 
                      filteredRooms.map(r => r.number));
            
            return filteredRooms;
        },

        getRoomCount(serviceId) {
            const count = this.rooms.filter(r => r.service === serviceId).length;
            console.log(`📊 Room count for "${serviceId}": ${count}`);
            return count;
        },

        // ✅ Fallback function (pastikan ada)
        async generateFallbackRooms() {
            console.log('🔄 Using fallback demo data');
            
            const demoRooms = [
                { id: 201, number: '201', service: 'meeting-room', room_type: 'Meeting Room' },
                { id: 202, number: '202', service: 'meeting-room', room_type: 'Meeting Room' },
                { id: 301, number: '301', service: 'private-office', room_type: 'Private Office' },
                { id: 302, number: '302', service: 'private-office', room_type: 'Private Office' },
                { id: 306, number: '306', service: 'sharing-room', room_type: 'Sharing Room' },
            ];

            this.rooms = demoRooms.map(room => ({
                id: room.id,
                service: room.service,
                number: room.number,
                room_type: room.room_type,
                baseStatus: 'available',
                effectiveStatus: 'available',
                statusType: 'free',
                notes: '',
                updatedAt: new Date().toLocaleTimeString(),
                maintenanceStart: null,
                maintenanceEnd: null,
                maintenanceReason: null,
                currentBooking: null,
                bookingUntil: null,
                bookingStarts: null,
                bookingDate: null,
                originalData: room
            }));

            this.calculateStats();
            this.showToastMessage('Demo data loaded', 'info');
        },

        startAutoRefresh() {
            setInterval(() => {
                this.refreshAllRoomStatuses();
            }, 30000);
        },

        async manualRefresh() {
            this.showToastMessage('Refreshing room data...', 'info');
            await this.loadRoomsFromDatabase();
        },

        // ... (functions lainnya tetap sama)
        getStatusDisplayText(room) {
            const statusMap = {
                'occupied': 'Occupied',
                'booked': room.statusType === 'today' ? 'Booked Today' : 'Booked Future',
                'available': 'Available',
                'maintenance': 'Maintenance'
            };
            return statusMap[room.effectiveStatus] || room.effectiveStatus;
        },

        getStatusBadgeClasses(room) {
            const baseClasses = 'px-2 sm:px-3 py-1 rounded-full text-xs font-semibold capitalize';
            const variantClasses = {
                'occupied': 'bg-red-200 text-red-800',
                'booked': room.statusType === 'today' ? 'bg-blue-200 text-blue-800' : 'bg-purple-200 text-purple-800',
                'available': 'bg-green-200 text-green-800',
                'maintenance': 'bg-yellow-200 text-yellow-800'
            };
            return `${baseClasses} ${variantClasses[room.effectiveStatus] || 'bg-gray-200 text-gray-800'}`;
        },

        getRoomCardClasses(room) {
            const baseClasses = 'bg-white rounded-lg border-2 transition-all hover:shadow-lg p-3 sm:p-4';
            const variantClasses = {
                'occupied': 'border-red-300 bg-red-50',
                'booked': room.statusType === 'today' ? 'border-blue-300 bg-blue-50' : 'border-purple-300 bg-purple-50',
                'available': 'border-green-300 bg-green-50',
                'maintenance': 'border-yellow-300 bg-yellow-50'
            };
            return `${baseClasses} ${variantClasses[room.effectiveStatus] || 'border-gray-300'}`;
        },

        canSetMaintenance(room) {
            return room.effectiveStatus === 'available' || room.effectiveStatus === 'maintenance';
        },

        canDeleteRoom(room) {
            return room.effectiveStatus === 'available' && room.baseStatus !== 'maintenance';
        },

        canEditRoom(room) {
            return room.effectiveStatus !== 'occupied';
        },

        calculateStats() {
            this.stats.total = this.rooms.length;
            this.stats.available = this.rooms.filter(r => r.effectiveStatus === 'available').length;
            this.stats.occupied = this.rooms.filter(r => r.effectiveStatus === 'occupied').length;
            this.stats.maintenance = this.rooms.filter(r => r.effectiveStatus === 'maintenance').length;
            this.stats.booked = this.rooms.filter(r => r.effectiveStatus === 'booked').length;
        },
        // Di Alpine.js roomManagement(), tambahkan:
        get currentServiceRooms() {
            const rooms = this.getServiceRooms(this.activeTab);
            console.log(`📊 Current service (${this.activeTab}): ${rooms.length} rooms`);
            return rooms;
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