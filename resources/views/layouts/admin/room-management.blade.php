{{-- resources/views/admin/rooms.blade.php --}}

@extends('layouts.admin')

@section('title', 'Room Management')

@section('content')
<div x-data="roomManagement()" x-init="init()" class="space-y-4 md:space-y-6 pb-20 md:pb-6 max-w-full overflow-hidden">
    
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="min-w-0">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 truncate">🏢 Room Management</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Manage all rooms and their status</p>
        </div>
        <button 
            @click="openAddModal()"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium flex items-center justify-center gap-2 whitespace-nowrap"
        >
            <span>➕</span>
            <span class="hidden sm:inline">Add New Room</span>
            <span class="sm:hidden">Add Room</span>
        </button>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
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
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
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
                    
                    {{-- Room Grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4">
                        <template x-for="room in getServiceRooms(service.id)" :key="room.id">
                            <div 
                                class="bg-white rounded-lg border-2 transition-all hover:shadow-lg p-3 sm:p-4"
                                :class="{
                                    'border-green-300 bg-green-50': room.status === 'available',
                                    'border-red-300 bg-red-50': room.status === 'occupied',
                                    'border-yellow-300 bg-yellow-50': room.status === 'maintenance'
                                }"
                            >
                                {{-- Room Number --}}
                                <div class="text-center mb-3">
                                    <p class="text-2xl sm:text-3xl font-bold text-gray-800" x-text="room.number"></p>
                                </div>

                                {{-- Status Badge --}}
                                <div class="flex justify-center mb-3">
                                    <span 
                                        class="px-2 sm:px-3 py-1 rounded-full text-xs font-semibold capitalize"
                                        :class="{
                                            'bg-green-200 text-green-800': room.status === 'available',
                                            'bg-red-200 text-red-800': room.status === 'occupied',
                                            'bg-yellow-200 text-yellow-800': room.status === 'maintenance'
                                        }"
                                        x-text="room.status"
                                    ></span>
                                </div>

                                {{-- Info --}}
                                <div class="text-center mb-3">
                                    <p class="text-xs text-gray-500">Last updated</p>
                                    <p class="text-xs font-medium text-gray-700" x-text="room.updatedAt"></p>
                                </div>

                                {{-- Maintenance Info --}}
                                <div x-show="room.status === 'maintenance' && room.maintenanceEnd" class="mb-3 p-2 bg-white rounded border border-yellow-300">
                                    <p class="text-xs text-gray-600 mb-1">Until:</p>
                                    <p class="text-xs font-semibold text-gray-800" x-text="room.maintenanceEnd"></p>
                                </div>

                                {{-- Actions --}}
                                <div class="space-y-1.5">
                                    <button 
                                        @click="editRoom(room)"
                                        class="w-full px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 text-xs font-medium transition"
                                    >
                                        ✏️ Edit
                                    </button>
                                    <button 
                                        @click="openMaintenanceModal(room)"
                                        :disabled="room.status === 'occupied'"
                                        :class="room.status === 'occupied' ? 'opacity-50 cursor-not-allowed' : 'hover:bg-yellow-200'"
                                        class="w-full px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded-lg text-xs font-medium transition"
                                    >
                                        🔧 Maintenance
                                    </button>
                                    <button 
                                        @click="deleteRoom(room)"
                                        :disabled="room.status === 'occupied'"
                                        :class="room.status === 'occupied' ? 'opacity-50 cursor-not-allowed' : 'hover:bg-red-200'"
                                        class="w-full px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-xs font-medium transition"
                                    >
                                        🗑️ Delete
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Empty State --}}
                    <div 
                        x-show="getServiceRooms(service.id).length === 0"
                        class="text-center py-12 sm:py-16 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300"
                    >
                        <div class="text-gray-400">
                            <div class="text-4xl sm:text-5xl mb-3">🏢</div>
                            <p class="text-base sm:text-lg font-medium text-gray-600 mb-2">No rooms yet</p>
                            <p class="text-xs sm:text-sm text-gray-500 mb-4">Add rooms for this service</p>
                            <button 
                                @click="openAddModal(service)"
                                class="px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium inline-flex items-center gap-2 text-sm"
                            >
                                <span>➕</span>
                                <span>Add Room</span>
                            </button>
                        </div>
                    </div>

                </div>
            </template>
        </div>
    </div>

    {{-- Add/Edit Room Modal --}}
    <div x-show="showRoomModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-3 sm:px-4">
            <div @click="showRoomModal = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-4 sm:p-6 my-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800" x-text="editingRoom ? 'Edit Room' : 'Add New Room'"></h3>
                    <button @click="showRoomModal = false" class="text-gray-400 hover:text-gray-600 flex-shrink-0 ml-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-3 sm:space-y-4">
                    {{-- Service Selection --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Service <span class="text-red-500">*</span></label>
                        <select x-model="roomForm.service" :disabled="editingRoom" class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">-- Select Service --</option>
                            <template x-for="service in services" :key="service.id">
                                <option :value="service.id" x-text="service.icon + ' ' + service.name"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Room Number --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Room Number <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            x-model="roomForm.number"
                            placeholder="e.g., 201"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                        >
                    </div>

                    {{-- Status --}}
                    <div x-show="!editingRoom || editingRoom.status !== 'occupied'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select x-model="roomForm.status" class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="available">Available</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Occupied status is managed from Room Assignment</p>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea 
                            x-model="roomForm.notes"
                            rows="3"
                            placeholder="Additional information..."
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                        ></textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-4">
                        <button 
                            @click="saveRoom()"
                            :disabled="!roomForm.service || !roomForm.number"
                            :class="(!roomForm.service || !roomForm.number) ? 'bg-gray-300 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                            class="flex-1 px-4 sm:px-6 py-2 sm:py-3 text-white rounded-lg font-semibold transition text-sm"
                        >
                            <span x-text="editingRoom ? 'Update Room' : 'Add Room'"></span>
                        </button>
                        <button 
                            @click="showRoomModal = false"
                            class="px-4 sm:px-6 py-2 sm:py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Maintenance Modal --}}
    <div x-show="showMaintenanceModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-3 sm:px-4">
            <div @click="showMaintenanceModal = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-4 sm:p-6 my-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800">Set Maintenance</h3>
                    <button @click="showMaintenanceModal = false" class="text-gray-400 hover:text-gray-600 flex-shrink-0 ml-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <template x-if="selectedRoom">
                    <div class="space-y-3 sm:space-y-4">
                        {{-- Room Info --}}
                        <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-200">
                            <p class="text-xs text-yellow-700 mb-1">Room</p>
                            <p class="text-base font-semibold text-yellow-900" x-text="'Room ' + selectedRoom.number"></p>
                        </div>

                        {{-- Action Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                            <select x-model="maintenanceForm.action" class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="set">Set to Maintenance</option>
                                <option value="release">Release from Maintenance</option>
                            </select>
                        </div>

                        {{-- Maintenance Form (Only when setting) --}}
                        <div x-show="maintenanceForm.action === 'set'">
                            <div class="space-y-3">
                                {{-- Start Date --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date <span class="text-red-500">*</span></label>
                                    <input type="date" x-model="maintenanceForm.startDate" class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                </div>

                                {{-- End Date --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date (Optional)</label>
                                    <input type="date" x-model="maintenanceForm.endDate" class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                    <p class="text-xs text-gray-500 mt-1">Leave empty for indefinite maintenance</p>
                                </div>

                                {{-- Reason --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason <span class="text-red-500">*</span></label>
                                    <textarea 
                                        x-model="maintenanceForm.reason"
                                        rows="3"
                                        placeholder="e.g., Air conditioner repair"
                                        class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                                    ></textarea>
                                </div>

                                {{-- Auto Release --}}
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <div class="min-w-0 mr-3">
                                        <p class="text-sm font-medium text-gray-800">Auto-release</p>
                                        <p class="text-xs text-gray-600">Automatically set to available after end date</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                        <input type="checkbox" x-model="maintenanceForm.autoRelease" :disabled="!maintenanceForm.endDate" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 peer-disabled:opacity-50"></div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-4">
                            <button 
                                @click="submitMaintenance()"
                                :disabled="maintenanceForm.action === 'set' && (!maintenanceForm.startDate || !maintenanceForm.reason)"
                                :class="(maintenanceForm.action === 'set' && (!maintenanceForm.startDate || !maintenanceForm.reason)) ? 'bg-gray-300 cursor-not-allowed' : maintenanceForm.action === 'set' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700'"
                                class="flex-1 px-4 sm:px-6 py-2 sm:py-3 text-white rounded-lg font-semibold transition text-sm"
                            >
                                <span x-text="maintenanceForm.action === 'set' ? 'Set Maintenance' : 'Release to Available'"></span>
                            </button>
                            <button 
                                @click="showMaintenanceModal = false"
                                class="px-4 sm:px-6 py-2 sm:py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </template>
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
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-2">Delete Room</h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">
                        Are you sure you want to delete this room?<br>
                        <span class="text-xs sm:text-sm text-gray-500">This action cannot be undone.</span>
                    </p>

                    <template x-if="deletingRoom">
                        <div class="mb-3 sm:mb-4 p-2 sm:p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm font-medium text-gray-800" x-text="'Room ' + deletingRoom.number"></p>
                        </div>
                    </template>

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

        stats: {
            total: 0,
            available: 0,
            occupied: 0,
            maintenance: 0
        },

        services: [
            { id: 'meeting-room', name: 'Meeting Room', icon: '🏢', range: '201-205' },
            { id: 'private-office', name: 'Private Office', icon: '🚪', range: '301-305' },
            { id: 'sharing-room', name: 'Sharing Room', icon: '👥', range: '306-308' }
        ],

        rooms: [],

        roomForm: {
            service: '',
            number: '',
            status: 'available',
            notes: ''
        },

        maintenanceForm: {
            action: 'set',
            startDate: '',
            endDate: '',
            reason: '',
            autoRelease: false
        },

        init() {
            this.generateDummyRooms();
            this.calculateStats();
        },

        generateDummyRooms() {
            // Generate 2 rooms per service dengan status berbeda untuk contoh
            const roomConfigs = [
                { 
                    service: 'meeting-room', 
                    rooms: [
                        { number: '201', status: 'available' },
                        { number: '202', status: 'occupied' }
                    ]
                },
                { 
                    service: 'private-office', 
                    rooms: [
                        { number: '301', status: 'maintenance' },
                        { number: '302', status: 'available' }
                    ]
                },
                { 
                    service: 'sharing-room', 
                    rooms: [
                        { number: '306', status: 'available' },
                        { number: '307', status: 'occupied' }
                    ]
                }
            ];

            this.rooms = [];
            let idCounter = 1000;

            roomConfigs.forEach(config => {
                config.rooms.forEach(roomData => {
                    const room = {
                        id: idCounter++,
                        service: config.service,
                        number: roomData.number,
                        status: roomData.status,
                        notes: '',
                        updatedAt: this.getRandomDate(),
                        maintenanceStart: roomData.status === 'maintenance' ? this.getDateAgo(2) : null,
                        maintenanceEnd: roomData.status === 'maintenance' ? this.getDateAgo(7) : null,
                        maintenanceReason: roomData.status === 'maintenance' ? 'Air conditioner maintenance' : null
                    };

                    this.rooms.push(room);
                });
            });
        },

        getRandomDate() {
            const days = Math.floor(Math.random() * 30);
            return days === 0 ? 'Today' : days === 1 ? 'Yesterday' : `${days} days ago`;
        },

        getDateAgo(days) {
            const date = new Date();
            date.setDate(date.getDate() + days);
            return date.toISOString().split('T')[0];
        },

        calculateStats() {
            this.stats.total = this.rooms.length;
            this.stats.available = this.rooms.filter(r => r.status === 'available').length;
            this.stats.occupied = this.rooms.filter(r => r.status === 'occupied').length;
            this.stats.maintenance = this.rooms.filter(r => r.status === 'maintenance').length;
        },

        getRoomCount(serviceId) {
            return this.rooms.filter(r => r.service === serviceId).length;
        },

        getServiceRooms(serviceId) {
            return this.rooms.filter(r => r.service === serviceId).sort((a, b) => a.number.localeCompare(b.number));
        },

        openAddModal(service = null) {
            this.editingRoom = null;
            this.roomForm = {
                service: service?.id || '',
                number: '',
                status: 'available',
                notes: ''
            };
            this.showRoomModal = true;
        },

        editRoom(room) {
            this.editingRoom = room;
            this.roomForm = {
                service: room.service,
                number: room.number,
                status: room.status,
                notes: room.notes || ''
            };
            this.showRoomModal = true;
        },

        saveRoom() {
            if (!this.roomForm.service || !this.roomForm.number) {
                this.showToastMessage('Please fill all required fields', 'error');
                return;
            }

            if (this.editingRoom) {
                // Update existing room
                const index = this.rooms.findIndex(r => r.id === this.editingRoom.id);
                if (index !== -1) {
                    this.rooms[index] = {
                        ...this.rooms[index],
                        number: this.roomForm.number,
                        status: this.roomForm.status,
                        notes: this.roomForm.notes,
                        updatedAt: 'Just now'
                    };
                }
                this.showToastMessage('Room updated successfully!', 'success');
            } else {
                // Add new room
                const newRoom = {
                    id: Date.now(),
                    service: this.roomForm.service,
                    number: this.roomForm.number,
                    status: this.roomForm.status,
                    notes: this.roomForm.notes,
                    updatedAt: 'Just now',
                    maintenanceStart: null,
                    maintenanceEnd: null,
                    maintenanceReason: null
                };
                this.rooms.push(newRoom);
                this.showToastMessage('Room added successfully!', 'success');
            }

            this.showRoomModal = false;
            this.calculateStats();
        },

        deleteRoom(room) {
            if (room.status === 'occupied') {
                this.showToastMessage('Cannot delete occupied room', 'error');
                return;
            }
            this.deletingRoom = room;
            this.showDeleteModal = true;
        },

        confirmDelete() {
            const index = this.rooms.findIndex(r => r.id === this.deletingRoom.id);
            if (index !== -1) {
                this.rooms.splice(index, 1);
                this.showToastMessage('Room deleted successfully!', 'success');
                this.calculateStats();
            }
            this.showDeleteModal = false;
        },

        openMaintenanceModal(room) {
            if (room.status === 'occupied') {
                this.showToastMessage('Cannot set maintenance for occupied room', 'error');
                return;
            }

            this.selectedRoom = room;
            this.maintenanceForm = {
                action: room.status === 'maintenance' ? 'release' : 'set',
                startDate: room.status === 'maintenance' ? room.maintenanceStart : this.getDateAgo(0),
                endDate: room.status === 'maintenance' ? room.maintenanceEnd || '' : '',
                reason: room.status === 'maintenance' ? room.maintenanceReason || '' : '',
                autoRelease: false
            };
            this.showMaintenanceModal = true;
        },

        submitMaintenance() {
            const index = this.rooms.findIndex(r => r.id === this.selectedRoom.id);
            if (index === -1) return;

            if (this.maintenanceForm.action === 'set') {
                if (!this.maintenanceForm.startDate || !this.maintenanceForm.reason) {
                    this.showToastMessage('Please fill all required fields', 'error');
                    return;
                }

                this.rooms[index].status = 'maintenance';
                this.rooms[index].maintenanceStart = this.maintenanceForm.startDate;
                this.rooms[index].maintenanceEnd = this.maintenanceForm.endDate || null;
                this.rooms[index].maintenanceReason = this.maintenanceForm.reason;
                this.rooms[index].updatedAt = 'Just now';

                this.showToastMessage('Room set to maintenance!', 'success');
            } else {
                this.rooms[index].status = 'available';
                this.rooms[index].maintenanceStart = null;
                this.rooms[index].maintenanceEnd = null;
                this.rooms[index].maintenanceReason = null;
                this.rooms[index].updatedAt = 'Just now';

                this.showToastMessage('Room released from maintenance!', 'success');
            }

            this.showMaintenanceModal = false;
            this.calculateStats();
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