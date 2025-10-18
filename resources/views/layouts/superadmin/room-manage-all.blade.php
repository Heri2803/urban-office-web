@extends('layouts.superadmin')

@section('title', 'All Rooms Management')

@section('content')
<div x-data="roomManagement()" class="p-4 md:p-6 space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">🏢 All Rooms Management</h1>
            <p class="text-sm text-gray-600 mt-1">Manage all rooms across all branches and mitra</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button @click="openStatusDashboard" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                📊 Room Status Dashboard
            </button>
            <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                ➕ Add New Room
            </button>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="text-sm text-gray-600">Total Rooms</div>
            <div class="text-2xl font-bold text-gray-800 mt-1">248</div>
            <div class="text-xs text-gray-500 mt-1">Across all branches</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="text-sm text-gray-600">Available</div>
            <div class="text-2xl font-bold text-green-600 mt-1">142</div>
            <div class="text-xs text-gray-500 mt-1">57% occupancy</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <div class="text-sm text-gray-600">Occupied</div>
            <div class="text-2xl font-bold text-yellow-600 mt-1">89</div>
            <div class="text-xs text-gray-500 mt-1">36% of total</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <div class="text-sm text-gray-600">Maintenance</div>
            <div class="text-2xl font-bold text-red-600 mt-1">17</div>
            <div class="text-xs text-gray-500 mt-1">7% unavailable</div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                🔍 Filter & Search
            </h3>
            <button @click="filterExpanded = !filterExpanded" class="md:hidden text-blue-600">
                <span x-text="filterExpanded ? 'Hide' : 'Show'"></span>
            </button>
        </div>

        <div x-show="filterExpanded" x-transition class="space-y-4">
            {{-- First Row --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mitra</label>
                    <select x-model="filters.mitra" @change="updateBranchOptions" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Mitra</option>
                        <option value="1">Urban Office Surabaya</option>
                        <option value="2">Urban Office Jakarta</option>
                        <option value="3">Urban Office Bandung</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                    <select x-model="filters.branch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Branches</option>
                        <template x-for="branch in availableBranches" :key="branch.value">
                            <option :value="branch.value" x-text="branch.text"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Service Type</label>
                    <select x-model="filters.serviceType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Types</option>
                        <option value="private_office">Private Office</option>
                        <option value="meeting_room">Meeting Room</option>
                        <option value="coworking">Co-working Space</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select x-model="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
            </div>

            {{-- Second Row --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input x-model="filters.search" type="text" placeholder="Room number, name, or description..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="flex items-end gap-2">
                    <button @click="applyFilters" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Apply Filters
                    </button>
                    <button @click="resetFilters" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- View Options --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white rounded-lg shadow p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Group By</label>
                <select x-model="groupBy" @change="notifyChange('Grouping changed')" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="mitra">Mitra</option>
                    <option value="branch">Branch</option>
                    <option value="service_type">Service Type</option>
                    <option value="status">Status</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                <select x-model="sortBy" @change="notifyChange('Sorting changed')" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="room_number">Room Number</option>
                    <option value="name">Room Name</option>
                    <option value="capacity">Capacity</option>
                    <option value="price">Price</option>
                    <option value="status">Status</option>
                </select>
            </div>
        </div>

        <div class="text-sm text-gray-600">
            Showing <span class="font-semibold">1-20</span> of <span class="font-semibold">248</span> rooms
        </div>
    </div>

    {{-- Room Groups --}}
    <div class="space-y-4">
        <template x-for="(group, index) in roomGroups" :key="index">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                {{-- Group Header --}}
                <div @click="toggleGroup(group.id)" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4 cursor-pointer hover:from-blue-600 hover:to-blue-700 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg :class="{'rotate-0': group.expanded, 'rotate-[-90deg]': !group.expanded}" class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            <h3 class="text-lg font-semibold" x-text="group.name"></h3>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm bg-white/20 px-3 py-1 rounded-full" x-text="`${group.rooms.length} rooms`"></span>
                        </div>
                    </div>
                </div>

                {{-- Group Content --}}
                <div x-show="group.expanded" x-collapse class="divide-y divide-gray-200">
                    <template x-for="room in group.rooms" :key="room.id">
                        <div class="p-4 hover:bg-gray-50 transition">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                {{-- Room Info --}}
                                <div class="flex-1">
                                    <div class="flex items-start gap-4">
                                        <img :src="room.image" :alt="room.name" class="w-20 h-20 object-cover rounded-lg">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h4 class="font-semibold text-gray-800" x-text="room.name"></h4>
                                                <span :class="{
                                                    'bg-green-100 text-green-700': room.status === 'available',
                                                    'bg-yellow-100 text-yellow-700': room.status === 'occupied',
                                                    'bg-red-100 text-red-700': room.status === 'maintenance'
                                                }" class="text-xs px-2 py-1 rounded-full" x-text="room.status"></span>
                                            </div>
                                            <div class="text-sm text-gray-600 mt-1 space-y-1">
                                                <div><span class="font-medium">Room:</span> <span x-text="room.number"></span></div>
                                                <div><span class="font-medium">Type:</span> <span x-text="room.type"></span></div>
                                                <div><span class="font-medium">Capacity:</span> <span x-text="`${room.capacity} people`"></span></div>
                                                <div><span class="font-medium">Price:</span> <span class="text-blue-600 font-semibold" x-text="room.price"></span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex flex-wrap gap-2">
                                    <button @click="viewRoomDetails(room)" class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm">
                                        👁️ View Details
                                    </button>
                                    <button class="px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition text-sm">
                                        ✏️ Edit
                                    </button>
                                    <button class="px-3 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition text-sm">
                                        📅 Calendar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>

    {{-- Pagination --}}
    <div class="flex items-center justify-between bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-600">
            Page <span class="font-semibold">1</span> of <span class="font-semibold">13</span>
        </div>
        <div class="flex gap-2">
            <button @click="changePage('prev')" class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50" disabled>
                Previous
            </button>
            <button @click="changePage(1)" class="px-3 py-2 bg-blue-600 text-white rounded-lg">1</button>
            <button @click="changePage(2)" class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">2</button>
            <button @click="changePage(3)" class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">3</button>
            <span class="px-3 py-2">...</span>
            <button @click="changePage(13)" class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">13</button>
            <button @click="changePage('next')" class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Next
            </button>
        </div>
    </div>

    {{-- Room Detail Modal --}}
    <div x-show="modalOpen" x-cloak x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background overlay --}}
            <div @click="modalOpen = false" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>

            {{-- Modal panel --}}
            <div class="inline-block w-full max-w-4xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                {{-- Modal Header --}}
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold" x-text="selectedRoom?.name || 'Room Details'"></h3>
                        <button @click="modalOpen = false" class="text-white hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                    <template x-if="selectedRoom">
                        <div class="space-y-6">
                            {{-- Room Image --}}
                            <img :src="selectedRoom.image" :alt="selectedRoom.name" class="w-full h-64 object-cover rounded-lg">

                            {{-- Room Info Grid --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-700">Room Number:</span>
                                        <span x-text="selectedRoom.number"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-700">Type:</span>
                                        <span x-text="selectedRoom.type"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-700">Capacity:</span>
                                        <span x-text="`${selectedRoom.capacity} people`"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-700">Price:</span>
                                        <span class="text-blue-600 font-semibold" x-text="selectedRoom.price"></span>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-700">Branch:</span>
                                        <span x-text="selectedRoom.branch"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-700">Floor:</span>
                                        <span x-text="`${selectedRoom.floor}F`"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-700">Size:</span>
                                        <span x-text="`${selectedRoom.size} m²`"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-700">Status:</span>
                                        <span :class="{
                                            'bg-green-100 text-green-700': selectedRoom.status === 'available',
                                            'bg-yellow-100 text-yellow-700': selectedRoom.status === 'occupied',
                                            'bg-red-100 text-red-700': selectedRoom.status === 'maintenance'
                                        }" class="px-2 py-1 rounded-full text-sm" x-text="selectedRoom.status"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Facilities --}}
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-2">Facilities</h4>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="facility in selectedRoom.facilities" :key="facility">
                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm" x-text="facility"></span>
                                    </template>
                                </div>
                            </div>

                            {{-- Quick Actions --}}
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <button @click="setMaintenance" class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition">
                                    🔧 Set Maintenance
                                </button>
                                <button @click="viewCalendar" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">
                                    📅 View Calendar
                                </button>
                                <button @click="editDetails" class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition">
                                    ✏️ Edit Details
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <button @click="modalOpen = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Notification Toast --}}
    <div x-show="notification.show" x-transition x-cloak class="fixed top-4 right-4 z-[60] px-6 py-3 rounded-lg shadow-lg flex items-center gap-3" :class="{
        'bg-green-500 text-white': notification.type === 'success',
        'bg-red-500 text-white': notification.type === 'error',
        'bg-orange-500 text-white': notification.type === 'warning',
        'bg-blue-500 text-white': notification.type === 'info'
    }" style="display: none;">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path x-show="notification.type === 'success'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            <path x-show="notification.type === 'error'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            <path x-show="notification.type === 'warning'" fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            <path x-show="notification.type === 'info'" fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        <span x-text="notification.message"></span>
    </div>
</div>

@push('scripts')
<script>
function roomManagement() {
    return {
        // Filter state
        filterExpanded: true,
        filters: {
            mitra: '',
            branch: '',
            serviceType: '',
            status: '',
            search: ''
        },
        
        // View options
        groupBy: 'mitra',
        sortBy: 'room_number',
        
        // Pagination
        currentPage: 1,
        totalPages: 13,
        itemsPerPage: 20,
        
        // Modal state
        modalOpen: false,
        selectedRoom: null,
        
        // Notification state
        notification: {
            show: false,
            message: '',
            type: 'info'
        },
        
        // Branch data
        branchData: {
            '1': [
                { value: '1', text: 'Surabaya - Gubeng' },
                { value: '2', text: 'Surabaya - HR Muhammad' },
                { value: '3', text: 'Sidoarjo - Delta' }
            ],
            '2': [
                { value: '4', text: 'Jakarta - Senayan' },
                { value: '5', text: 'Jakarta - Sudirman' }
            ],
            '3': [
                { value: '6', text: 'Bandung - Dago' },
                { value: '7', text: 'Bandung - Riau' }
            ]
        },
        
        availableBranches: [],
        
        // All rooms data (untuk filtering dan sorting)
        allRooms: [
            {
                id: 1,
                name: 'Executive Office A1',
                number: 'SBY-GBG-101',
                type: 'Private Office',
                capacity: 4,
                price: 'Rp 2.500.000/month',
                priceValue: 2500000,
                status: 'available',
                image: 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=400',
                branch: 'Surabaya - Gubeng',
                branchId: '1',
                mitraId: '1',
                mitraName: 'Urban Office Surabaya',
                floor: 1,
                size: 25,
                facilities: ['AC', 'WiFi', 'Whiteboard', 'Furniture']
            },
            {
                id: 2,
                name: 'Meeting Room B2',
                number: 'SBY-GBG-201',
                type: 'Meeting Room',
                capacity: 8,
                price: 'Rp 150.000/hour',
                priceValue: 150000,
                status: 'occupied',
                image: 'https://images.unsplash.com/photo-1497366811353-6870744d04b2?w=400',
                branch: 'Surabaya - Gubeng',
                branchId: '1',
                mitraId: '1',
                mitraName: 'Urban Office Surabaya',
                floor: 2,
                size: 35,
                facilities: ['AC', 'WiFi', 'Projector', 'TV', 'Whiteboard']
            },
            {
                id: 3,
                name: 'Co-working Space C1',
                number: 'SBY-HRM-301',
                type: 'Co-working Space',
                capacity: 20,
                price: 'Rp 50.000/day',
                priceValue: 50000,
                status: 'maintenance',
                image: 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?w=400',
                branch: 'Surabaya - HR Muhammad',
                branchId: '2',
                mitraId: '1',
                mitraName: 'Urban Office Surabaya',
                floor: 3,
                size: 100,
                facilities: ['AC', 'WiFi', 'Coffee Machine', 'Printer']
            },
            {
                id: 4,
                name: 'Premium Office D1',
                number: 'JKT-SEN-401',
                type: 'Private Office',
                capacity: 6,
                price: 'Rp 4.500.000/month',
                priceValue: 4500000,
                status: 'available',
                image: 'https://images.unsplash.com/photo-1497366672149-e5e4b4d34eb3?w=400',
                branch: 'Jakarta - Senayan',
                branchId: '4',
                mitraId: '2',
                mitraName: 'Urban Office Jakarta',
                floor: 4,
                size: 40,
                facilities: ['AC', 'WiFi', 'Pantry', 'Reception']
            },
            {
                id: 5,
                name: 'Conference Room E1',
                number: 'JKT-SEN-501',
                type: 'Meeting Room',
                capacity: 12,
                price: 'Rp 250.000/hour',
                priceValue: 250000,
                status: 'available',
                image: 'https://images.unsplash.com/photo-1497366811353-6870744d04b2?w=400',
                branch: 'Jakarta - Senayan',
                branchId: '4',
                mitraId: '2',
                mitraName: 'Urban Office Jakarta',
                floor: 5,
                size: 45,
                facilities: ['AC', 'WiFi', 'Video Conference', 'Projector', 'Sound System']
            },
            {
                id: 6,
                name: 'Creative Studio F1',
                number: 'BDG-DAG-601',
                type: 'Private Office',
                capacity: 8,
                price: 'Rp 3.200.000/month',
                priceValue: 3200000,
                status: 'occupied',
                image: 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=400',
                branch: 'Bandung - Dago',
                branchId: '6',
                mitraId: '3',
                mitraName: 'Urban Office Bandung',
                floor: 6,
                size: 50,
                facilities: ['AC', 'WiFi', 'Whiteboard', 'Pantry', 'Lounge']
            }
        ],
        
        // Room groups (akan di-generate dari allRooms)
        roomGroups: [],
        
        // Stats
        stats: {
            total: 0,
            available: 0,
            occupied: 0,
            maintenance: 0
        },
        
        // Initialization
        init() {
            this.calculateStats();
            this.updateRoomGroups();
            this.showNotification('Room Management initialized successfully', 'success');
        },
        
        // Calculate statistics
        calculateStats() {
            const filtered = this.getFilteredRooms();
            this.stats.total = filtered.length;
            this.stats.available = filtered.filter(r => r.status === 'available').length;
            this.stats.occupied = filtered.filter(r => r.status === 'occupied').length;
            this.stats.maintenance = filtered.filter(r => r.status === 'maintenance').length;
        },
        
        // Get filtered rooms
        getFilteredRooms() {
            let filtered = [...this.allRooms];
            
            // Apply filters
            if (this.filters.mitra) {
                filtered = filtered.filter(r => r.mitraId === this.filters.mitra);
            }
            if (this.filters.branch) {
                filtered = filtered.filter(r => r.branchId === this.filters.branch);
            }
            if (this.filters.serviceType) {
                const typeMap = {
                    'private_office': 'Private Office',
                    'meeting_room': 'Meeting Room',
                    'coworking': 'Co-working Space'
                };
                filtered = filtered.filter(r => r.type === typeMap[this.filters.serviceType]);
            }
            if (this.filters.status) {
                filtered = filtered.filter(r => r.status === this.filters.status);
            }
            if (this.filters.search) {
                const search = this.filters.search.toLowerCase();
                filtered = filtered.filter(r => 
                    r.name.toLowerCase().includes(search) ||
                    r.number.toLowerCase().includes(search) ||
                    r.branch.toLowerCase().includes(search)
                );
            }
            
            return filtered;
        },
        
        // Sort rooms
        sortRooms(rooms) {
            const sorted = [...rooms];
            
            switch(this.sortBy) {
                case 'room_number':
                    return sorted.sort((a, b) => a.number.localeCompare(b.number));
                case 'name':
                    return sorted.sort((a, b) => a.name.localeCompare(b.name));
                case 'capacity':
                    return sorted.sort((a, b) => b.capacity - a.capacity);
                case 'price':
                    return sorted.sort((a, b) => b.priceValue - a.priceValue);
                case 'status':
                    return sorted.sort((a, b) => a.status.localeCompare(b.status));
                default:
                    return sorted;
            }
        },
        
        // Update room groups based on groupBy
        updateRoomGroups() {
            const filtered = this.getFilteredRooms();
            const sorted = this.sortRooms(filtered);
            
            let groups = {};
            
            switch(this.groupBy) {
                case 'mitra':
                    sorted.forEach(room => {
                        if (!groups[room.mitraId]) {
                            groups[room.mitraId] = {
                                id: `mitra-${room.mitraId}`,
                                name: `🤝 ${room.mitraName}`,
                                expanded: true,
                                rooms: []
                            };
                        }
                        groups[room.mitraId].rooms.push(room);
                    });
                    break;
                    
                case 'branch':
                    sorted.forEach(room => {
                        if (!groups[room.branchId]) {
                            groups[room.branchId] = {
                                id: `branch-${room.branchId}`,
                                name: `🏢 ${room.branch}`,
                                expanded: true,
                                rooms: []
                            };
                        }
                        groups[room.branchId].rooms.push(room);
                    });
                    break;
                    
                case 'service_type':
                    sorted.forEach(room => {
                        if (!groups[room.type]) {
                            const icons = {
                                'Private Office': '🏢',
                                'Meeting Room': '📅',
                                'Co-working Space': '👥'
                            };
                            groups[room.type] = {
                                id: `type-${room.type.replace(/\s+/g, '-')}`,
                                name: `${icons[room.type] || '📦'} ${room.type}`,
                                expanded: true,
                                rooms: []
                            };
                        }
                        groups[room.type].rooms.push(room);
                    });
                    break;
                    
                case 'status':
                    sorted.forEach(room => {
                        if (!groups[room.status]) {
                            const icons = {
                                'available': '✅',
                                'occupied': '🔒',
                                'maintenance': '🔧'
                            };
                            const names = {
                                'available': 'Available',
                                'occupied': 'Occupied',
                                'maintenance': 'Maintenance'
                            };
                            groups[room.status] = {
                                id: `status-${room.status}`,
                                name: `${icons[room.status]} ${names[room.status]}`,
                                expanded: true,
                                rooms: []
                            };
                        }
                        groups[room.status].rooms.push(room);
                    });
                    break;
            }
            
            this.roomGroups = Object.values(groups);
        },
        
        // Methods
        updateBranchOptions() {
            if (this.filters.mitra && this.branchData[this.filters.mitra]) {
                this.availableBranches = this.branchData[this.filters.mitra];
            } else {
                this.availableBranches = [];
            }
            this.filters.branch = '';
            this.showNotification('Branch filter updated based on selected Mitra', 'info');
        },
        
        applyFilters() {
            let filterMsg = 'Applying filters: ';
            let filters = [];
            
            if (this.filters.mitra) {
                const mitraNames = {
                    '1': 'Urban Office Surabaya',
                    '2': 'Urban Office Jakarta',
                    '3': 'Urban Office Bandung'
                };
                filters.push(`Mitra: ${mitraNames[this.filters.mitra]}`);
            }
            if (this.filters.branch) {
                const branch = this.availableBranches.find(b => b.value === this.filters.branch);
                if (branch) filters.push(`Branch: ${branch.text}`);
            }
            if (this.filters.serviceType) {
                const types = {
                    'private_office': 'Private Office',
                    'meeting_room': 'Meeting Room',
                    'coworking': 'Co-working Space'
                };
                filters.push(`Service: ${types[this.filters.serviceType]}`);
            }
            if (this.filters.status) {
                filters.push(`Status: ${this.filters.status.charAt(0).toUpperCase() + this.filters.status.slice(1)}`);
            }
            if (this.filters.search) filters.push(`Search: "${this.filters.search}"`);

            if (filters.length > 0) {
                filterMsg += filters.join(', ');
            } else {
                filterMsg = 'No filters applied - showing all rooms';
            }

            this.showNotification(filterMsg, 'info');
            
            // Update groups and stats
            setTimeout(() => {
                this.updateRoomGroups();
                this.calculateStats();
                this.currentPage = 1;
                this.showNotification(`Found ${this.stats.total} rooms`, 'success');
            }, 300);
        },
        
        resetFilters() {
            this.filters = {
                mitra: '',
                branch: '',
                serviceType: '',
                status: '',
                search: ''
            };
            this.availableBranches = [];
            this.currentPage = 1;
            
            this.showNotification('Resetting filters...', 'info');
            
            setTimeout(() => {
                this.updateRoomGroups();
                this.calculateStats();
                this.showNotification('All filters have been reset', 'success');
            }, 300);
        },
        
        toggleGroup(groupId) {
            const group = this.roomGroups.find(g => g.id === groupId);
            if (group) {
                group.expanded = !group.expanded;
            }
        },
        
        viewRoomDetails(room) {
            this.selectedRoom = room;
            this.showNotification('Loading room details...', 'info');
            
            setTimeout(() => {
                this.modalOpen = true;
                document.body.style.overflow = 'hidden';
            }, 200);
        },
        
        closeModal() {
            this.modalOpen = false;
            document.body.style.overflow = 'auto';
            this.selectedRoom = null;
        },
        
        setMaintenance() {
            if (confirm(`Are you sure you want to set "${this.selectedRoom.name}" to maintenance mode?`)) {
                this.showNotification(`${this.selectedRoom.name} set to maintenance mode`, 'warning');
                
                // Update room status in allRooms
                const room = this.allRooms.find(r => r.id === this.selectedRoom.id);
                if (room) {
                    room.status = 'maintenance';
                }
                
                setTimeout(() => {
                    this.updateRoomGroups();
                    this.calculateStats();
                    this.closeModal();
                }, 1000);
            }
        },
        
        viewCalendar() {
            this.showNotification('Opening booking calendar...', 'info');
            setTimeout(() => {
                this.showNotification('Calendar feature coming soon!', 'warning');
            }, 800);
        },
        
        editDetails() {
            this.showNotification('Opening edit form...', 'info');
            setTimeout(() => {
                this.showNotification('Edit feature coming soon!', 'warning');
            }, 800);
        },
        
        openStatusDashboard() {
            this.showNotification('Opening Room Status Dashboard...', 'info');
            setTimeout(() => {
                this.showNotification('Dashboard feature coming soon!', 'warning');
            }, 800);
        },
        
        changePage(page) {
            let targetPage = this.currentPage;
            
            if (page === 'prev' && this.currentPage > 1) {
                targetPage = this.currentPage - 1;
            } else if (page === 'next' && this.currentPage < this.totalPages) {
                targetPage = this.currentPage + 1;
            } else if (typeof page === 'number') {
                targetPage = page;
            }
            
            if (targetPage !== this.currentPage) {
                this.showNotification(`Loading page ${targetPage}...`, 'info');
                
                setTimeout(() => {
                    this.currentPage = targetPage;
                    this.showNotification(`Page ${targetPage} loaded successfully`, 'success');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }, 500);
            }
        },
        
        notifyChange(message) {
            this.showNotification(message, 'info');
            
            setTimeout(() => {
                this.updateRoomGroups();
                this.showNotification('View updated successfully', 'success');
            }, 300);
        },
        
        showNotification(message, type = 'info') {
            this.notification = {
                show: true,
                message: message,
                type: type
            };
            
            setTimeout(() => {
                this.notification.show = false;
            }, 3000);
        }
    }
}
</script>
@endpush

@push('styles')
<style>
    [x-cloak] { 
        display: none !important; 
    }
    
    /* Custom scrollbar for modal */
    .max-h-\[70vh\]::-webkit-scrollbar {
        width: 8px;
    }
    
    .max-h-\[70vh\]::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .max-h-\[70vh\]::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    .max-h-\[70vh\]::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    /* Smooth transitions */
    .transition {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
    
    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
        .md\:grid-cols-4 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
        
        .md\:grid-cols-3 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
        
        .md\:grid-cols-2 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
    }
</style>
@endpush
@endsection