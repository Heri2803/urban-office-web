{{-- resources/views/admin/content/highlights.blade.php --}}

@extends('layouts.admin')

@section('title', 'Service Highlights')

@section('content')
<div x-data="serviceHighlights()" x-init="init()" class="space-y-4 md:space-y-6 pb-20 md:pb-6 w-full">
    
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="min-w-0">
            <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 truncate">⭐ Service Highlights</h1>
            <p class="text-xs text-gray-500 mt-1">Manage features and facilities for each service</p>
        </div>
        
        {{-- Loading Indicator --}}
        <div x-show="loading" class="flex items-center gap-2 text-blue-600">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-xs sm:text-sm">Loading...</span>
        </div>
    </div>

    {{-- Service Tabs --}}
    <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Tab Headers - Mobile Scrollable --}}
        <div class="border-b border-gray-200 overflow-x-auto overflow-y-hidden -webkit-overflow-scrolling-touch" 
            style="scrollbar-width: thin; scrollbar-color: #CBD5E0 #F7FAFC;">
            <nav class="flex">
                <template x-for="roomType in roomTypes" :key="roomType.id">
                    <button 
                        @click="activeTab = roomType.id"
                        :class="activeTab === roomType.id ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="flex items-center gap-1.5 px-3 py-2.5 border-b-2 font-medium whitespace-nowrap transition-colors text-xs sm:text-sm flex-shrink-0"
                    >
                        <span x-text="roomType.icon || '🏢'"></span>
                        <span x-text="roomType.name.split(' ')[0]"></span>
                        <span 
                            class="px-1.5 py-0.5 text-[10px] sm:text-xs rounded-full min-w-[18px] text-center"
                            :class="activeTab === roomType.id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'"
                            x-text="getHighlightCount(roomType.id)"
                        ></span>
                    </button>
                </template>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="p-3 sm:p-4 md:p-6">
            <template x-for="roomType in roomTypes" :key="roomType.id">
                <div x-show="activeTab === roomType.id" x-transition>
                    
                    {{-- Header with Add Button --}}
                    <div class="flex flex-col xs:flex-row xs:items-center justify-between gap-3 mb-4">
                        <h3 class="text-sm sm:text-base font-semibold text-gray-800 flex items-center gap-2">
                            <span x-text="roomType.icon || '🏢'"></span>
                            <span x-text="roomType.name + ' Features'"></span>
                        </h3>
                        <button 
                            @click="openHighlightModal(roomType)"
                            class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-xs flex items-center gap-1 sm:gap-2 whitespace-nowrap w-full xs:w-auto justify-center"
                        >
                            <span class="text-sm">➕</span>
                            <span class="hidden xs:inline">Add Highlight</span>
                            <span class="xs:hidden">Add New</span>
                        </button>
                    </div>

                    {{-- Highlights List --}}
                    <div class="space-y-3">
                        <template x-for="(highlight, index) in getHighlightsForService(roomType.id)" :key="highlight.id">
                            <div class="bg-white rounded-lg border border-gray-200 hover:border-blue-300 transition p-3">
                                <div class="flex items-start gap-3">
                                    {{-- Number instead of icon --}}
                                    <div class="flex-shrink-0 w-7 h-7 sm:w-8 sm:h-8 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-bold text-xs sm:text-sm">
                                        <span x-text="index + 1"></span>
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2 mb-2">
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-semibold text-gray-800 mb-1" x-text="highlight.name"></h4>
                                                <p x-show="highlight.description" class="text-xs text-gray-600 line-clamp-2" x-text="highlight.description"></p>
                                                
                                                {{-- Applied Rooms Info --}}
                                                <div x-show="!highlight.show_in_all_services && (highlight.room_types.length > 0 || highlight.rooms.length > 0)" 
                                                    class="mt-2 text-xs text-gray-500">
                                                    <span class="font-medium">Applied to:</span>
                                                    <span class="block xs:inline" x-text="getAppliedRoomsText(highlight, roomType.id)"></span>
                                                </div>
                                                <div x-show="highlight.show_in_all_services" class="mt-2 text-xs text-green-600 font-medium">
                                                    ✅ Applied to all services
                                                </div>
                                            </div>
                                            <div class="flex flex-row sm:flex-col items-start sm:items-end gap-2 mt-2 sm:mt-0">
                                                <span 
                                                    :class="highlight.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'"
                                                    class="px-2 py-1 rounded-full text-xs font-semibold flex-shrink-0"
                                                    x-text="highlight.is_active ? 'Active' : 'Inactive'"
                                                ></span>
                                                
                                                {{-- Toggle Status Button --}}
                                                <button 
                                                    @click="toggleHighlightStatus(highlight)"
                                                    :class="highlight.is_active ? 'bg-orange-100 text-orange-700 hover:bg-orange-200' : 'bg-green-100 text-green-700 hover:bg-green-200'"
                                                    class="px-2 py-1 rounded text-xs font-medium transition whitespace-nowrap"
                                                    x-text="highlight.is_active ? 'Deactivate' : 'Activate'"
                                                ></button>
                                            </div>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="flex flex-wrap gap-1 sm:gap-2">
                                            <button 
                                                @click="editHighlight(highlight)"
                                                class="px-2 py-1.5 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 text-xs font-medium transition flex items-center gap-1"
                                            >
                                                <span class="text-xs">✏️</span>
                                                <span class="hidden xs:inline">Edit</span>
                                            </button>
                                            <button 
                                                @click="deleteHighlight(highlight)"
                                                class="px-2 py-1.5 bg-red-100 text-red-700 rounded hover:bg-red-200 text-xs font-medium transition flex items-center gap-1"
                                            >
                                                <span class="text-xs">🗑️</span>
                                                <span class="hidden xs:inline">Delete</span>
                                            </button>
                                            <div class="flex gap-1">
                                                <button 
                                                    @click="moveUp(roomType.id, index)"
                                                    :disabled="index === 0"
                                                    :class="index === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                                    class="px-2 py-1.5 bg-gray-100 text-gray-700 rounded text-xs font-medium transition"
                                                    title="Move up"
                                                >
                                                    ↑
                                                </button>
                                                <button 
                                                    @click="moveDown(roomType.id, index)"
                                                    :disabled="index === getHighlightsForService(roomType.id).length - 1"
                                                    :class="index === getHighlightsForService(roomType.id).length - 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                                    class="px-2 py-1.5 bg-gray-100 text-gray-700 rounded text-xs font-medium transition"
                                                    title="Move down"
                                                >
                                                    ↓
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Empty State --}}
                    <div 
                        x-show="getHighlightsForService(roomType.id).length === 0"
                        class="text-center py-8 sm:py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300"
                    >
                        <div class="text-gray-400">
                            <div class="text-3xl sm:text-4xl mb-2">⭐</div>
                            <p class="text-sm sm:text-base font-medium text-gray-600 mb-2">No highlights yet</p>
                            <p class="text-xs text-gray-500 mb-4 px-4">Add features and facilities for this service</p>
                            <button 
                                @click="openHighlightModal(roomType)"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium inline-flex items-center gap-2 text-sm"
                            >
                                <span>➕</span>
                                <span>Add First Highlight</span>
                            </button>
                        </div>
                    </div>

                </div>
            </template>
        </div>
    </div>

    {{-- Add/Edit Highlight Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen p-2 sm:p-4">
            <div @click="showModal = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-lg sm:rounded-xl shadow-xl w-full max-w-md sm:max-w-lg md:max-w-2xl mx-auto p-4 sm:p-6 my-4 max-h-[95vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base sm:text-lg font-bold text-gray-800" x-text="editingHighlight ? 'Edit Highlight' : 'Add New Highlight'"></h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 flex-shrink-0 ml-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    {{-- Highlight Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Highlight Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            x-model="highlightForm.name"
                            placeholder="e.g., Free Wi-Fi, Projector, 24/7 Access"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                        >
                        <p class="text-xs text-gray-500 mt-1">Enter a clear and descriptive name</p>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                        <textarea 
                            x-model="highlightForm.description"
                            rows="3"
                            placeholder="Add more details about this feature..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                        ></textarea>
                    </div>

                    {{-- Show in All Services Toggle --}}
                    <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <input 
                            type="checkbox" 
                            x-model="highlightForm.show_in_all_services"
                            class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">Show in All Services</p>
                            <p class="text-xs text-gray-600 mt-1">Display across all service types</p>
                        </div>
                    </div>

                    {{-- Room Type Selection --}}
                    <div x-show="!highlightForm.show_in_all_services" class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">
                            Select Services <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-gray-500 mb-2">Choose which services should display this highlight</p>
                        
                        <div class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto p-1">
                            <template x-for="roomType in roomTypes" :key="roomType.id">
                                <label class="flex items-center gap-3 p-2 border border-gray-200 rounded hover:bg-gray-50 cursor-pointer transition">
                                    <input 
                                        type="checkbox" 
                                        x-model="highlightForm.selected_room_types" 
                                        :value="roomType.id"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    <span class="text-sm text-gray-700 flex items-center gap-2">
                                        <span x-text="roomType.icon || '🏢'"></span>
                                        <span x-text="roomType.name"></span>
                                    </span>
                                </label>
                            </template>
                        </div>
                    </div>

                    {{-- Room Selection --}}
                    <div x-show="!highlightForm.show_in_all_services && highlightForm.selected_room_types.length > 0" class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">
                            Select Specific Rooms (Optional)
                        </label>
                        <p class="text-xs text-gray-500 mb-2">
                            Choose specific rooms. Leave empty for all rooms in selected services.
                        </p>
                        
                        <div class="space-y-2 max-h-48 overflow-y-auto p-1">
                            <template x-for="roomTypeId in highlightForm.selected_room_types" :key="roomTypeId">
                                <div class="border border-gray-200 rounded-lg p-2 bg-gray-50">
                                    <p class="text-xs font-medium text-gray-800 mb-2" 
                                    x-text="getRoomTypeName(roomTypeId) + ' Rooms'">
                                    </p>
                                    <div class="space-y-1">
                                        <template x-for="room in getRoomsByType(roomTypeId)" :key="room.id">
                                            <label class="flex items-center gap-2 p-2 bg-white rounded border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                                <input 
                                                    type="checkbox" 
                                                    x-model="highlightForm.selected_rooms" 
                                                    :value="room.id"
                                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                >
                                                {{-- Gunakan room_number sebagai nama --}}
                                                <span class="text-xs text-gray-700 flex-1" x-text="room.room_number"></span>
                                                
                                                {{-- Tampilkan additional info jika ada --}}
                                                <template x-if="room.capacity">
                                                    <span class="text-xs text-gray-500" x-text="'👥 ' + room.capacity"></span>
                                                </template>
                                                <template x-if="room.floor">
                                                    <span class="text-xs text-gray-500" x-text="'🏢 ' + room.floor"></span>
                                                </template>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Active Toggle --}}
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                        <div class="min-w-0 mr-3">
                            <p class="text-sm font-medium text-gray-800">Active Status</p>
                            <p class="text-xs text-gray-600">Show to customers</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input type="checkbox" x-model="highlightForm.is_active" class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-5 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                        </label>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-2 pt-4 border-t border-gray-200">
                        <button 
                            @click="saveHighlight()"
                            :disabled="!highlightForm.name || loading"
                            :class="!highlightForm.name || loading ? 'bg-gray-300 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                            class="flex-1 px-4 py-2.5 text-white rounded-lg font-semibold transition text-sm flex items-center justify-center gap-2 order-2 sm:order-1"
                        >
                            <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="editingHighlight ? 'Update' : 'Add Highlight'"></span>
                        </button>
                        <button 
                            @click="showModal = false"
                            :disabled="loading"
                            class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition text-sm order-1 sm:order-2"
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
        <div class="flex items-center justify-center min-h-screen p-2 sm:p-4">
            <div @click="showDeleteModal = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-lg sm:rounded-xl shadow-xl w-full max-w-xs sm:max-w-md mx-auto p-4 sm:p-6">
                <div class="text-center">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-xl sm:text-2xl">⚠️</span>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-2">Delete Highlight</h3>
                    <p class="text-xs sm:text-sm text-gray-600 mb-4">
                        Are you sure you want to delete?<br>
                        <span class="text-xs text-gray-500">This cannot be undone.</span>
                    </p>

                    <template x-if="deletingHighlight">
                        <div class="mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-sm font-medium text-gray-800 mb-1" x-text="deletingHighlight.name"></p>
                            <p x-show="deletingHighlight.description" class="text-xs text-gray-600 line-clamp-2" x-text="deletingHighlight.description"></p>
                        </div>
                    </template>

                    <div class="flex flex-col sm:flex-row gap-2">
                        <button 
                            @click="confirmDelete()"
                            :disabled="loading"
                            :class="loading ? 'bg-red-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700'"
                            class="flex-1 px-4 py-2.5 text-white rounded-lg font-semibold transition text-sm flex items-center justify-center gap-2 order-2 sm:order-1"
                        >
                            <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="loading ? 'Deleting...' : 'Delete'"></span>
                        </button>
                        <button 
                            @click="showDeleteModal = false"
                            :disabled="loading"
                            class="flex-1 px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition text-sm order-1 sm:order-2"
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
        class="fixed top-4 right-4 z-50 max-w-[calc(100vw-2rem)] sm:max-w-sm"
        :class="toastType === 'success' ? 'bg-green-500' : toastType === 'error' ? 'bg-red-500' : 'bg-blue-500'"
    >
        <div class="text-white px-3 sm:px-4 py-2 sm:py-3 rounded-lg shadow-xl flex items-start gap-2">
            <span class="text-lg sm:text-xl flex-shrink-0 mt-0.5">
                <span x-show="toastType === 'success'">✅</span>
                <span x-show="toastType === 'error'">❌</span>
                <span x-show="toastType === 'info'">ℹ️</span>
            </span>
            <div class="min-w-0 flex-1">
                <p class="font-semibold text-xs sm:text-sm break-words" x-text="toastMessage"></p>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function serviceHighlights() {
    return {
        // State Management
        loading: false,
        activeTab: null,
        showModal: false,
        showDeleteModal: false,
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        
        // Data from backend
        roomTypes: [],
        highlights: [],
        rooms: [],
        
        // Form & Selection
        selectedRoomType: null,
        editingHighlight: null,
        deletingHighlight: null,
        highlightForm: {
            name: '',
            description: '',
            is_active: true,
            show_in_all_services: false,
            selected_room_types: [],
            selected_rooms: []
        },

        // Initialize
        async init() {
            await this.loadInitialData();
            // Set active tab ke pertama jika ada data
            if (this.roomTypes.length > 0) {
                this.activeTab = this.roomTypes[0].id;
            }
        },

        // Load data from backend
        async loadInitialData() {
            this.loading = true;
            try {
                const response = await fetch('/highlights/api/data');
                const result = await response.json();
                
                if (result.success) {
                    this.roomTypes = result.data.roomTypes;
                    this.highlights = result.data.highlights;
                    this.rooms = result.data.rooms;
                } else {
                    this.showToastMessage('Failed to load data', 'error');
                }
            } catch (error) {
                console.error('Error loading data:', error);
                this.showToastMessage('Error loading data', 'error');
            } finally {
                this.loading = false;
            }
        },

        // Get highlights count for a room type
        getHighlightCount(roomTypeId) {
            return this.getHighlightsForService(roomTypeId).length;
        },

        // Get highlights for specific service (max 5)
        getHighlightsForService(roomTypeId) {
            const serviceHighlights = this.highlights.filter(highlight => {
                if (highlight.show_in_all_services) return true;
                
                // Check if highlight is applied to this room type
                return highlight.room_types.some(rt => rt.id === roomTypeId);
            });
            
            // Return max 5 highlights
            return serviceHighlights.slice(0, 5);
        },

        // Di Alpine.js - update method getRoomsByType
        getRoomsByType(roomTypeId) {
            const typeId = parseInt(roomTypeId);
            
            return this.rooms.filter(room => {
                // Jika room_type_id null, tampilkan di Private Office (2) dan Meeting Room (1)
                if (room.room_type_id === null) {
                    return typeId === 1 || typeId === 2; // Meeting Room atau Private Office
                }
                
                // Normal case: room punya room_type_id yang spesifik
                return room.room_type_id === typeId;
            });
        },

        // Method untuk get room type name
        getRoomTypeName(roomTypeId) {
            const roomType = this.roomTypes.find(rt => rt.id === parseInt(roomTypeId));
            return roomType ? roomType.name : 'Unknown';
        },

        // Tambahkan method untuk format room display
        getRoomDisplayName(room) {
            // Gunakan room_number sebagai nama
            return room.room_number || `Room ${room.id}`;
        },

        // Method untuk get room code/identifier
        getRoomCode(room) {
            // Jika tidak ada code khusus, bisa gunakan ID atau format dari room_number
            return room.room_number ? `#${room.room_number}` : `#${room.id}`;
        },

        // Modal Operations
        openHighlightModal(roomType = null) {
            this.selectedRoomType = roomType;
            this.editingHighlight = null;
            this.highlightForm = {
                name: '',
                description: '',
                is_active: true,
                show_in_all_services: false,
                selected_room_types: roomType ? [roomType.id] : [],
                selected_rooms: []
            };
            this.showModal = true;
        },

        async editHighlight(highlight) {
            console.log('Editing highlight:', highlight);
            console.log('Current active tab:', this.activeTab);
            
            this.editingHighlight = highlight;
            
            // Default form values
            this.highlightForm = {
                name: highlight.name,
                description: highlight.description || '',
                is_active: highlight.is_active,
                show_in_all_services: highlight.show_in_all_services,
                selected_room_types: [],
                selected_rooms: []
            };
            
            // Jika show_in_all_services true, skip room type selection
            if (!highlight.show_in_all_services) {
                // Filter room types yang relevan dengan current context
                if (this.activeTab) {
                    // Mode: Edit dari tab specific, hanya tampilkan room type yang aktif
                    this.highlightForm.selected_room_types = [this.activeTab];
                } else {
                    // Mode: Edit dari global, tampilkan semua room types yang dipilih
                    this.highlightForm.selected_room_types = highlight.room_types.map(rt => rt.id);
                }
                
                // Filter rooms yang relevan dengan selected room types
                if (highlight.rooms && highlight.rooms.length > 0) {
                    this.highlightForm.selected_rooms = highlight.rooms
                        .filter(room => {
                            // Hanya include rooms yang belong ke selected room types
                            return this.highlightForm.selected_room_types.includes(room.room_type_id);
                        })
                        .map(room => room.id);
                }
            }
            
            this.showModal = true;
        },

        // Save highlight (Create/Update)
        async saveHighlight() {
            if (!this.highlightForm.name?.trim()) {
                this.showToastMessage('Please enter highlight name', 'error');
                return;
            }

            this.loading = true;
            try {
                const url = this.editingHighlight 
                    ? `/highlights/api/${this.editingHighlight.id}` // ✅ Fix
                    : '/highlights/api'; // ✅ Fix
                
                const method = this.editingHighlight ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.highlightForm)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showToastMessage(result.message, 'success');
                    await this.loadInitialData();
                    this.showModal = false;
                } else {
                    this.showToastMessage(result.message || 'Operation failed', 'error');
                }
            } catch (error) {
                console.error('Error saving highlight:', error);
                this.showToastMessage('Error saving highlight', 'error');
            } finally {
                this.loading = false;
            }
        },

        // Delete Operations
        deleteHighlight(highlight) {
            this.deletingHighlight = highlight;
            this.showDeleteModal = true;
        },

        async confirmDelete() {
            if (!this.deletingHighlight) return;
            
            this.loading = true;
            try {
                const response = await fetch(`/highlights/api/${this.deletingHighlight.id}`, { // ✅ Tambahkan /api
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showToastMessage(result.message, 'success');
                    await this.loadInitialData();
                } else {
                    this.showToastMessage(result.message || 'Delete failed', 'error');
                }
            } catch (error) {
                console.error('Error deleting highlight:', error);
                this.showToastMessage('Error deleting highlight', 'error');
            } finally {
                this.loading = false;
                this.showDeleteModal = false;
                this.deletingHighlight = null;
            }
        },

        // Reordering
        async moveUp(roomTypeId, index) {
            const highlights = this.getHighlightsForService(roomTypeId);
            if (index === 0) return;
            
            await this.reorderHighlights(roomTypeId, index, index - 1);
        },

        async moveDown(roomTypeId, index) {
            const highlights = this.getHighlightsForService(roomTypeId);
            if (index === highlights.length - 1) return;
            
            await this.reorderHighlights(roomTypeId, index, index + 1);
        },

        async reorderHighlights(roomTypeId, fromIndex, toIndex) {
            const highlights = this.getHighlightsForService(roomTypeId);
            const highlight = highlights[fromIndex];
            
            // Update sort order locally for immediate UI feedback
            const newOrder = [...highlights];
            const [movedItem] = newOrder.splice(fromIndex, 1);
            newOrder.splice(toIndex, 0, movedItem);
            
            // Update sort orders
            const orderData = newOrder.map((item, index) => ({
                id: item.id,
                sort_order: index + 1
            }));
            
            try {
                const response = await fetch('/highlights/api/reorder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ order: orderData })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showToastMessage('Order updated successfully', 'success');
                    await this.loadInitialData(); // Reload untuk sync dengan backend
                } else {
                    this.showToastMessage('Failed to update order', 'error');
                    await this.loadInitialData(); // Rollback dengan reload data
                }
            } catch (error) {
                console.error('Error reordering:', error);
                this.showToastMessage('Error updating order', 'error');
                await this.loadInitialData(); // Rollback dengan reload data
            }
        },

        // Toggle active status
        async toggleHighlightStatus(highlight) {
            try {
                const response = await fetch(`/highlights/api/${highlight.id}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showToastMessage(result.message, 'success');
                    // Update local state
                    highlight.is_active = result.is_active;
                } else {
                    this.showToastMessage('Failed to update status', 'error');
                }
            } catch (error) {
                console.error('Error toggling status:', error);
                this.showToastMessage('Error updating status', 'error');
            }
        },

        // Utility functions
        showToastMessage(message, type = 'success') {
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;
            setTimeout(() => {
                this.showToast = false;
            }, 3000);
        },

        // Check if highlight is applied to specific room
        isHighlightAppliedToRoom(highlight, roomId) {
            return highlight.rooms.some(room => room.id === roomId);
        },

        // Get applied rooms text for display
        getAppliedRoomsText(highlight, currentRoomTypeId) {
            if (highlight.show_in_all_services) {
                return 'All Services';
            }
            
            const parts = [];
            
            // Filter room types yang relevan dengan current tab
            if (highlight.room_types && highlight.room_types.length > 0) {
                const relevantRoomTypes = highlight.room_types.filter(rt => rt.id === currentRoomTypeId);
                if (relevantRoomTypes.length > 0) {
                    const roomTypeNames = relevantRoomTypes.map(rt => rt.name).join(', ');
                    parts.push(roomTypeNames);
                }
            }
            
            // Filter rooms yang relevan dengan current room type
            if (highlight.rooms && highlight.rooms.length > 0) {
                const relevantRooms = highlight.rooms.filter(room => {
                    // Room tanpa room_type_id (null) tampilkan di Private Office (2) dan Meeting Room (1)
                    if (room.room_type_id === null) {
                        return currentRoomTypeId === 1 || currentRoomTypeId === 2;
                    }
                    // Room dengan room_type_id spesifik
                    return room.room_type_id === currentRoomTypeId;
                });
                
                if (relevantRooms.length > 0) {
                    const roomNumbers = relevantRooms.map(room => room.room_number).join(', ');
                    parts.push(`Specific: ${roomNumbers}`);
                }
            }
            
            // Jika tidak ada yang relevan
            if (parts.length === 0) {
                return 'Not assigned to this service';
            }
            
            return parts.join(' | ');
        },

        scrollToActiveTab() {
            this.$nextTick(() => {
                const activeTab = this.$el.querySelector('[x-show].flex items-center gap-2');
                if (activeTab) {
                    activeTab.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'center'
                    });
                }
            });
        },
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