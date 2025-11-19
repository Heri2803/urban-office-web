{{-- resources/views/layouts/admin/service-photos.blade.php --}}
@extends('layouts.admin')

@section('title', 'Service Photos')

@section('content')
<div x-data="servicePhotos()" x-init="init()" class="space-y-6 pb-20 md:pb-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">📸 Service Photos</h1>
            <p class="text-sm text-gray-500 mt-1">Upload and manage photos for each room type</p>
        </div>
        <button 
            @click="openUploadModal()" 
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium flex items-center gap-2"
        >
            <span>➕</span>
            <span>Upload New Photo</span>
        </button>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <template x-for="roomType in roomTypes" :key="roomType.id">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xl" x-text="getRoomIcon(roomType.name)"></span>
                    <span class="text-xs font-semibold text-gray-500" x-text="getPhotoCount(roomType.id)"></span>
                </div>
                <p class="text-xs font-medium text-gray-700" x-text="roomType.name"></p>
            </div>
        </template>
    </div>

    {{-- Room Type Tabs --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 ">
        {{-- Tab Headers --}}
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="flex min-w-max md:min-w-0">
                <template x-for="roomType in roomTypes" :key="roomType.id">
                    <button 
                        @click="activeTab = roomType.id"
                        :class="activeTab === roomType.id ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-1 md:px-6 py-3 md:py-4 border-b-2 font-medium text-sm whitespace-nowrap transition"
                    >
                        <span x-text="getRoomIcon(roomType.name)"></span>
                        <span x-text="roomType.name"></span>
                        <span 
                            class="px-1.5 py-0.5 text-xs rounded-full"
                            :class="activeTab === roomType.id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'"
                            x-text="getPhotoCount(roomType.id)"
                        ></span>
                    </button>
                </template>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="p-4 md:p-6">
            <template x-for="roomType in roomTypes" :key="roomType.id">
                <div x-show="activeTab === roomType.id" x-transition>
                    {{-- Photo Gallery --}}
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <span x-text="getRoomIcon(roomType.name)"></span>
                                <span x-text="roomType.name + ' Gallery'"></span>
                                <span class="text-sm text-gray-500 ml-2" x-text="'(' + getRoomTypePhotos(roomType.id).length + ' photos)'"></span>
                            </h3>
                        </div>

                        {{-- Photo Grid --}}
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <template x-for="photo in getRoomTypePhotos(roomType.id)" :key="photo.id">
                                <div class="group relative bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300">
                                    {{-- Primary Badge --}}
                                    <div 
                                        x-show="photo.is_primary"
                                        class="absolute top-2 left-2 z-10 px-2 py-1 bg-yellow-400 text-yellow-900 text-xs font-bold rounded-full flex items-center gap-1"
                                    >
                                        <span>⭐</span>
                                        <span>Primary</span>
                                    </div>

                                    {{-- Image --}}
                                    <div class="aspect-video bg-gray-100 overflow-hidden">
                                        <img 
                                            :src="photo.url" 
                                            :alt="photo.filename"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                        >
                                    </div>

                                    {{-- Info & Actions --}}
                                    <div class="p-3">
                                        <p class="text-sm font-medium text-gray-800 truncate mb-1" x-text="photo.filename"></p>
                                        <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                            <span x-text="formatFileSize(photo.size)"></span>
                                            <span x-text="photo.uploaded_at"></span>
                                        </div>

                                        {{-- Action Buttons --}}
                                        <div class="flex gap-2">
                                            <button 
                                                @click="setPrimaryPhoto(photo)"
                                                :class="photo.is_primary ? 'bg-yellow-100 text-yellow-700 cursor-default' : 'bg-gray-100 text-gray-700 hover:bg-yellow-100 hover:text-yellow-700'"
                                                class="flex-1 px-3 py-2 rounded-lg text-xs font-medium transition"
                                                :disabled="photo.is_primary"
                                            >
                                                <span x-text="photo.is_primary ? '⭐ Primary' : '☆ Set Primary'"></span>
                                            </button>
                                        </div>

                                        <div class="flex gap-2 mt-2">
                                            <button 
                                                @click="editPhoto(photo)"
                                                class="flex-1 px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 text-xs font-medium transition"
                                            >
                                                ✏️ Edit
                                            </button>
                                            <button 
                                                @click="deletePhoto(photo)"
                                                class="flex-1 px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 text-xs font-medium transition"
                                            >
                                                🗑️ Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </template>
        </div>
    </div>

    {{-- Upload Modal --}}
    <div x-show="showUploadModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showUploadModal = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Upload Photos</h3>
                    <button @click="showUploadModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Room Type Selection --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Room Type</label>
                    <select x-model="uploadRoomTypeId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Choose Room Type --</option>
                        <template x-for="roomType in roomTypes" :key="roomType.id">
                            <option :value="roomType.id" x-text="roomType.name"></option>
                        </template>
                    </select>
                </div>

                {{-- File Input --}}
                <div class="mb-4">
                    <input 
                        type="file" 
                        x-ref="modalFileInput"
                        @change="handleModalFileSelect($event)" 
                        accept="image/jpeg,image/png,image/jpg"
                        multiple
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                    <p class="text-xs text-gray-500 mt-2">JPG, PNG (Max 2MB per file). You can select multiple files.</p>
                </div>

                {{-- Preview Grid --}}
                <div x-show="selectedFiles.length > 0" class="mb-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Selected Files (<span x-text="selectedFiles.length"></span>)</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 max-h-64 overflow-y-auto">
                        <template x-for="(file, index) in selectedFiles" :key="index">
                            <div class="relative bg-gray-100 rounded-lg p-2 border border-gray-200">
                                <button 
                                    @click="removeSelectedFile(index)"
                                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full hover:bg-red-600 transition text-xs"
                                >
                                    ✕
                                </button>
                                <div class="aspect-square bg-gray-200 rounded mb-2 overflow-hidden">
                                    <img :src="file.preview" class="w-full h-full object-cover">
                                </div>
                                <p class="text-xs text-gray-600 truncate" x-text="file.name"></p>
                                <p class="text-xs text-gray-500" x-text="formatFileSize(file.size)"></p>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Upload Button --}}
                <div class="flex gap-3">
                    <button 
                        @click="uploadPhotos()"
                        :disabled="!uploadRoomTypeId || selectedFiles.length === 0 || uploading"
                        :class="(!uploadRoomTypeId || selectedFiles.length === 0 || uploading) ? 'bg-gray-300 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                        class="flex-1 px-6 py-3 text-white rounded-lg font-semibold transition"
                    >
                        <span x-show="!uploading">Upload Photos</span>
                        <span x-show="uploading" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Uploading...
                        </span>
                    </button>
                    <button 
                        @click="showUploadModal = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showEditModal = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Edit Photo</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <template x-if="editingPhoto">
                    <div class="space-y-4">
                        {{-- Photo Preview --}}
                        <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden relative">
                            <img :src="editingPhoto.url" :alt="editingPhoto.filename" class="w-full h-full object-cover">
                            
                            {{-- ✅ UPDATE PHOTO BUTTON --}}
                            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                <button 
                                    @click="$refs.editFileInput.click()"
                                    class="px-4 py-2 bg-white text-gray-800 rounded-lg font-medium hover:bg-gray-100 transition flex items-center gap-2"
                                >
                                    <span>🔄</span>
                                    <span>Change Photo</span>
                                </button>
                            </div>
                        </div>

                        {{-- ✅ HIDDEN FILE INPUT FOR PHOTO UPDATE --}}
                        <input 
                            type="file" 
                            x-ref="editFileInput"
                            @change="handlePhotoUpdate($event)"
                            accept="image/jpeg,image/png,image/jpg"
                            class="hidden"
                        >

                        {{-- Current Photo Info --}}
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-600 mb-1">Current Photo</p>
                            <p class="text-sm font-medium text-gray-800" x-text="editingPhoto.filename"></p>
                            <p class="text-xs text-gray-500" x-text="formatFileSize(editingPhoto.size)"></p>
                        </div>

                        {{-- New Photo Preview (jika ada) --}}
                        <template x-if="newPhotoFile">
                            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                <p class="text-sm font-medium text-blue-800 mb-2">New Photo Preview</p>
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 bg-blue-100 rounded-lg overflow-hidden">
                                        <img :src="newPhotoFile.preview" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800" x-text="newPhotoFile.name"></p>
                                        <p class="text-xs text-gray-500" x-text="formatFileSize(newPhotoFile.size)"></p>
                                    </div>
                                    <button 
                                        @click="newPhotoFile = null"
                                        class="text-red-500 hover:text-red-700"
                                    >
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- Filename --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filename</label>
                            <input 
                                type="text" 
                                x-model="editingPhoto.filename"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>

                        {{-- Caption --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Caption (Optional)</label>
                            <textarea 
                                x-model="editingPhoto.caption"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Add a description for this photo..."
                            ></textarea>
                        </div>

                        {{-- Primary Toggle --}}
                        <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                            <div>
                                <p class="text-sm font-medium text-gray-800">Set as Primary Photo</p>
                                <p class="text-xs text-gray-600">This photo will be featured first</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="editingPhoto.is_primary" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-400"></div>
                            </label>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-3">
                            <button 
                                @click="savePhotoEdit()"
                                :disabled="updatingPhoto"
                                :class="updatingPhoto ? 'bg-blue-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                                class="flex-1 px-6 py-3 text-white rounded-lg font-semibold transition flex items-center justify-center gap-2"
                            >
                                <span x-show="!updatingPhoto">Save Changes</span>
                                <span x-show="updatingPhoto" class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Saving...
                                </span>
                            </button>
                            <button 
                                @click="showEditModal = false"
                                class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition"
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
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showDeleteModal = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">⚠️</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Delete Photo</h3>
                    <p class="text-gray-600 mb-6">
                        Are you sure you want to delete this photo?<br>
                        <span class="text-sm text-gray-500">This action cannot be undone.</span>
                    </p>

                    <template x-if="deletingPhoto">
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm font-medium text-gray-800" x-text="deletingPhoto.filename"></p>
                        </div>
                    </template>

                    <div class="flex gap-3">
                        <button 
                            @click="confirmDelete()"
                            class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition"
                        >
                            Yes, Delete
                        </button>
                        <button 
                            @click="showDeleteModal = false"
                            class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition"
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
        class="fixed top-4 right-4 z-50 max-w-sm"
        :class="toastType === 'success' ? 'bg-green-500' : toastType === 'error' ? 'bg-red-500' : 'bg-blue-500'"
    >
        <div class="text-white px-6 py-4 rounded-lg shadow-xl flex items-start gap-3">
            <span class="text-2xl">
                <span x-show="toastType === 'success'">✅</span>
                <span x-show="toastType === 'error'">❌</span>
                <span x-show="toastType === 'info'">ℹ️</span>
            </span>
            <div>
                <p class="font-semibold" x-text="toastMessage"></p>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function servicePhotos() {
    return {
        // State management
        activeTab: null,
        showUploadModal: false,
        showEditModal: false,
        showDeleteModal: false,
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        uploading: false,
        uploadRoomTypeId: '',
        selectedFiles: [],
        editingPhoto: null,
        deletingPhoto: null,

        // Edit modal properties
        newPhotoFile: null,
        updatingPhoto: false,

        roomTypes: [],
        photos: [],

        // API endpoints
        api: {
            roomTypes: '{{ route('service-photos.api.room-types') }}',
            photos: '{{ route('service-photos.api.photos') }}',
            upload: '{{ route('service-photos.api.upload') }}',
        },

        async init() {
            try {
                await this.loadRoomTypes();
                await this.loadPhotos();
                
                if (this.roomTypes.length > 0 && !this.activeTab) {
                    this.activeTab = this.roomTypes[0].id;
                }
            } catch (error) {
                console.error('Initialization error:', error);
                await this.loadDummyData();
            }
        },

        async loadRoomTypes() {
            try {
                const response = await fetch(this.api.roomTypes);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (Array.isArray(data)) {
                    this.roomTypes = data;
                } else if (data.roomTypes) {
                    this.roomTypes = data.roomTypes;
                } else {
                    this.roomTypes = [];
                }
                
                if (this.roomTypes.length === 0) {
                    this.roomTypes = this.getFallbackRoomTypes();
                }
                
            } catch (error) {
                console.error('Error loading room types:', error);
                this.roomTypes = this.getFallbackRoomTypes();
            }
        },

        getRoomIcon(roomTypeName) {
            const icons = {
                'Meeting Room': '🏢',
                'Private Office': '🚪',
                'Sharing Room': '👥',
                'Virtual Office': '💼',
                'Coworking Space': '🖥️',
                'Event Space': '🎉'
            };
            return icons[roomTypeName] || '📷';
        },

        getFallbackRoomTypes() {
            return [
                { id: 1, name: 'Meeting Room', description: 'Professional meeting rooms' },
                { id: 2, name: 'Private Office', description: 'Dedicated private offices' },
                { id: 3, name: 'Sharing Room', description: 'Shared workspace environments' },
                { id: 4, name: 'Virtual Office', description: 'Virtual office solutions' },
                { id: 5, name: 'Coworking Space', description: 'Flexible coworking spaces' },
                { id: 6, name: 'Event Space', description: 'Event and conference spaces' }
            ];
        },

        async loadPhotos() {
            try {
                const response = await fetch(this.api.photos);
                if (!response.ok) throw new Error('Failed to load photos');
                
                const photosData = await response.json();
                
                this.photos = photosData.map(photo => {
                    let imageUrl;
                    
                    if (photo.file_path) {
                        imageUrl = '/storage/' + photo.file_path;
                    } else if (photo.file_url) {
                        imageUrl = photo.file_url;
                    } else {
                        imageUrl = 'https://via.placeholder.com/400x300/f3f4f6/9ca3af?text=No+Image';
                    }
                    
                    return {
                        id: photo.id,
                        room_type_id: photo.room_type_id,
                        filename: photo.filename || photo.original_name,
                        url: imageUrl,
                        size: photo.file_size || photo.size,
                        caption: photo.caption || '',
                        is_primary: photo.is_primary || false,
                        uploaded_at: this.formatUploadTime(photo.created_at || photo.uploaded_at),
                        original_name: photo.original_name,
                        file_path: photo.file_path
                    };
                });
                
            } catch (error) {
                console.error('Error loading photos:', error);
                this.loadDummyPhotos();
            }
        },

        async loadDummyData() {
            this.roomTypes = this.getFallbackRoomTypes();
            this.loadDummyPhotos();
            
            if (this.roomTypes.length > 0) {
                this.activeTab = this.roomTypes[0].id;
            }
        },

        loadDummyPhotos() {
            this.photos = [];
            let photoId = 1;
            
            this.roomTypes.forEach((roomType, index) => {
                const photoCount = Math.floor(Math.random() * 3) + 2;
                for (let i = 0; i < photoCount; i++) {
                    this.photos.push({
                        id: photoId++,
                        room_type_id: roomType.id,
                        filename: `${roomType.name.toLowerCase().replace(' ', '-')}-photo-${i + 1}.jpg`,
                        url: `https://picsum.photos/seed/${roomType.name}${i}/400/300`,
                        size: Math.floor(Math.random() * 3000000) + 1000000,
                        caption: i === 0 ? `Beautiful ${roomType.name.toLowerCase()} space` : '',
                        is_primary: i === 0,
                        uploaded_at: this.getRandomDate(),
                    });
                }
            });
        },

        getRandomDate() {
            const days = Math.floor(Math.random() * 30);
            const date = new Date();
            date.setDate(date.getDate() - days);
            return days === 0 ? 'Today' : days === 1 ? 'Yesterday' : `${days} days ago`;
        },

        getPhotoCount(roomTypeId) {
            return this.photos.filter(p => p.room_type_id === roomTypeId).length;
        },

        getRoomTypePhotos(roomTypeId) {
            return this.photos.filter(p => p.room_type_id === roomTypeId);
        },

        openUploadModal() {
            this.uploadRoomTypeId = this.activeTab;
            this.selectedFiles = [];
            this.showUploadModal = true;
        },

        handleFileSelect(event, roomTypeId) {
            this.uploadRoomTypeId = roomTypeId;
            this.selectedFiles = [];
            this.processFiles(event.target.files);
        },

        handleModalFileSelect(event) {
            this.selectedFiles = [];
            this.processFiles(event.target.files);
        },

        processFiles(files) {
            Array.from(files).forEach(file => {
                if (!file.type.match('image/(jpeg|jpg|png)')) {
                    this.showToastMessage('Invalid file type. Only JPG and PNG allowed.', 'error');
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    this.showToastMessage(`${file.name} is too large. Max 2MB allowed.`, 'error');
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.selectedFiles.push({
                        file: file,
                        name: file.name,
                        size: file.size,
                        preview: e.target.result
                    });
                };
                reader.readAsDataURL(file);
            });
        },

        removeSelectedFile(index) {
            this.selectedFiles.splice(index, 1);
        },

        async uploadPhotos() {
            if (!this.uploadRoomTypeId || this.selectedFiles.length === 0) return;

            this.uploading = true;

            try {
                for (const fileData of this.selectedFiles) {
                    const formData = new FormData();
                    formData.append('room_type_id', this.uploadRoomTypeId);
                    formData.append('photo', fileData.file);
                    formData.append('caption', '');

                    const response = await fetch(this.api.upload, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const result = await response.json();
                    
                    if (!response.ok || !result.success) {
                        throw new Error(result.message || `Upload failed for ${fileData.name}`);
                    }
                }

                await this.loadPhotos();
                this.showUploadModal = false;
                this.selectedFiles = [];
                this.showToastMessage('Photos uploaded successfully!', 'success');
                this.activeTab = this.uploadRoomTypeId;
                
            } catch (error) {
                console.error('Upload error:', error);
                this.showToastMessage('Upload failed: ' + error.message, 'error');
            } finally {
                this.uploading = false;
            }
        },

        editPhoto(photo) {
            console.log('🖼️ Editing photo:', photo);
            
            this.editingPhoto = { 
                ...photo,
                // Pastikan filename selalu ada
                filename: photo.filename || photo.original_name || `photo-${photo.id}.jpg`
            };
            
            this.newPhotoFile = null;
            this.showEditModal = true;
            
            console.log('📝 Editing photo data:', this.editingPhoto);
        },

        handlePhotoUpdate(event) {
            const file = event.target.files[0];
            if (!file) {
                this.newPhotoFile = null; // Pastikan di-set null jika tidak ada file
                return;
            }

            if (!file.type.match('image/(jpeg|jpg|png)')) {
                this.showToastMessage('Invalid file type. Only JPG and PNG allowed.', 'error');
                this.newPhotoFile = null; // Reset ke null
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                this.showToastMessage('File is too large. Max 2MB allowed.', 'error');
                this.newPhotoFile = null; // Reset ke null
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                this.newPhotoFile = {
                    file: file,
                    name: file.name,
                    size: file.size,
                    preview: e.target.result
                };
            };
            reader.readAsDataURL(file);

            event.target.value = '';
        },

        async savePhotoEdit() {
            this.updatingPhoto = true;

            try {
                // Siapkan data dengan type yang benar
                const requestData = {
                    filename: this.editingPhoto.filename?.trim(),
                    caption: this.editingPhoto.caption || '',
                    is_primary: Boolean(this.editingPhoto.is_primary)
                };

                console.log('📤 Data untuk update:', requestData);

                // Validasi manual sebelum kirim
                if (!requestData.filename || requestData.filename.trim() === '') {
                    throw new Error('Filename is required');
                }

                const formData = new FormData();
                
                // ✅ TAMBAHKAN INI - Method spoofing untuk Laravel
                formData.append('_method', 'PUT');
                
                // Append data sebagai string
                formData.append('filename', requestData.filename);
                formData.append('caption', requestData.caption);
                formData.append('is_primary', requestData.is_primary ? '1' : '0');

                // Handle file upload jika ada
                if (this.newPhotoFile && this.newPhotoFile.file) {
                    formData.append('new_photo', this.newPhotoFile.file);
                    console.log('📎 File included:', this.newPhotoFile.name);
                }

                // Debug formData
                console.log('📦 FormData contents:');
                for (let [key, value] of formData.entries()) {
                    if (value instanceof File) {
                        console.log(`${key}:`, `File - ${value.name} (${value.size} bytes)`);
                    } else {
                        console.log(`${key}:`, `"${value}"`);
                    }
                }

                // ✅ UBAH METHOD JADI POST
                const response = await fetch(`/service-photos/api/${this.editingPhoto.id}`, {
                    method: 'POST', // ← GANTI DARI PUT KE POST
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                        // ❌ JANGAN tambahkan Content-Type, biar browser yang handle
                    },
                    body: formData
                });

                // Handle response
                if (!response.ok) {
                    const errorData = await response.json();
                    console.error('❌ Server error response:', errorData);
                    
                    if (response.status === 422) {
                        throw new Error(errorData.message || 'Validation failed');
                    }
                    throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.message || 'Failed to update photo');
                }

                console.log('✅ Update berhasil:', result);
                
                await this.loadPhotos();
                this.showEditModal = false;
                this.newPhotoFile = null;
                this.showToastMessage('Photo updated successfully!', 'success');
                
            } catch (error) {
                console.error('❌ Update error:', error);
                this.showToastMessage('Failed to update photo: ' + error.message, 'error');
            } finally {
                this.updatingPhoto = false;
            }
        },

        cancelEdit() {
            this.showEditModal = false;
            this.newPhotoFile = null;
            this.editingPhoto = null;
        },

        async setPrimaryPhoto(photo) {
            if (photo.is_primary) return;

            try {
                const response = await fetch(`{{ route('service-photos.api.set-primary', '') }}/${photo.id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();
                
                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Failed to set primary photo');
                }

                await this.loadPhotos();
                this.showToastMessage('Primary photo updated!', 'success');
                
            } catch (error) {
                console.error('Set primary error:', error);
                this.showToastMessage('Failed to set primary photo: ' + error.message, 'error');
            }
        },

        deletePhoto(photo) {
            this.deletingPhoto = photo;
            this.showDeleteModal = true;
        },

        async confirmDelete() {
            try {
                const response = await fetch(`{{ route('service-photos.api.destroy', '') }}/${this.deletingPhoto.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();
                
                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Failed to delete photo');
                }

                await this.loadPhotos();
                this.showDeleteModal = false;
                this.showToastMessage('Photo deleted successfully!', 'success');
                
            } catch (error) {
                console.error('Delete error:', error);
                this.showToastMessage('Failed to delete photo: ' + error.message, 'error');
            }
        },

        formatFileSize(bytes) {
            if (!bytes || bytes === 0) return '0 Bytes'; // Tambahkan pengecekan null
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        },

        formatUploadTime(timestamp) {
            if (!timestamp) return 'Recently';
            
            const date = new Date(timestamp);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins} minutes ago`;
            if (diffHours < 24) return `${diffHours} hours ago`;
            if (diffDays === 1) return 'Yesterday';
            if (diffDays < 7) return `${diffDays} days ago`;
            
            return date.toLocaleDateString();
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

@push('styles')
<style>
[x-cloak] { display: none !important; }
</style>
@endpush

@endsection