{{-- resources/views/admin/content/service-photos.blade.php --}}

@extends('layouts.admin')

@section('title', 'Service Photos')

@section('content')
<div x-data="servicePhotos()" x-init="init()" class="space-y-6 pb-20 md:pb-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">📸 Service Photos</h1>
            <p class="text-sm text-gray-500 mt-1">Upload and manage photos for each service</p>
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
        <template x-for="service in services" :key="service.id">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xl" x-text="service.icon"></span>
                    <span class="text-xs font-semibold text-gray-500" x-text="getPhotoCount(service.id)"></span>
                </div>
                <p class="text-xs font-medium text-gray-700" x-text="service.name"></p>
            </div>
        </template>
    </div>

    {{-- Service Tabs --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 ">
        {{-- Tab Headers --}}
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="flex min-w-max md:min-w-0">
                <template x-for="service in services" :key="service.id">
                    <button 
                        @click="activeTab = service.id"
                        :class="activeTab === service.id ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-1 md:px-6 py-3 md:py-4 border-b-2 font-medium text-sm whitespace-nowrap transition"
                    >
                        <span x-text="service.icon"></span>
                        <span x-text="service.name"></span>
                        <span 
                            class="px-1.5 py-0.5 text-xs rounded-full"
                            :class="activeTab === service.id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'"
                            x-text="getPhotoCount(service.id)"
                        ></span>
                    </button>
                </template>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="p-4 md:p-6">
            <template x-for="service in services" :key="service.id">
                <div x-show="activeTab === service.id" x-transition>
                    
                    {{-- Upload Section --}}
                    <div class="mb-6 bg-gradient-to-r from-blue-50 to-purple-50 border-2 border-dashed border-blue-300 rounded-xl p-6 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center text-3xl">
                                📤
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-1">Upload Photos</h3>
                                <p class="text-sm text-gray-600 mb-3">
                                    Drag & drop or click to browse<br>
                                    <span class="text-xs text-gray-500">JPG, PNG (Max 5MB per file)</span>
                                </p>
                            </div>
                            <input 
                                type="file" 
                                x-ref="fileInput"
                                @change="handleFileSelect($event, service.id)" 
                                accept="image/jpeg,image/png,image/jpg"
                                multiple
                                class="hidden"
                            >
                            <button 
                                @click="$refs.fileInput.click()"
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                            >
                                Browse Files
                            </button>
                        </div>
                    </div>

                    {{-- Photo Gallery --}}
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <span x-text="service.icon"></span>
                                <span x-text="service.name + ' Gallery'"></span>
                                <span class="text-sm text-gray-500 ml-2" x-text="'(' + getServicePhotos(service.id).length + ' photos)'"></span>
                            </h3>
                        </div>

                        {{-- Photo Grid --}}
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <template x-for="photo in getServicePhotos(service.id)" :key="photo.id">
                                <div class="group relative bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300">
                                    {{-- Primary Badge --}}
                                    <div 
                                        x-show="photo.isPrimary"
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
                                            <span x-text="photo.uploadedAt"></span>
                                        </div>

                                        {{-- Action Buttons --}}
                                        <div class="flex gap-2">
                                            <button 
                                                @click="setPrimaryPhoto(photo)"
                                                :class="photo.isPrimary ? 'bg-yellow-100 text-yellow-700 cursor-default' : 'bg-gray-100 text-gray-700 hover:bg-yellow-100 hover:text-yellow-700'"
                                                class="flex-1 px-3 py-2 rounded-lg text-xs font-medium transition"
                                                :disabled="photo.isPrimary"
                                            >
                                                <span x-text="photo.isPrimary ? '⭐ Primary' : '☆ Set Primary'"></span>
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

                        {{-- Empty State --}}
                        <div 
                            x-show="getServicePhotos(service.id).length === 0"
                            class="text-center py-16 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300"
                        >
                            <div class="text-gray-400">
                                <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center text-5xl">
                                    📷
                                </div>
                                <p class="text-lg font-medium text-gray-600 mb-2">No photos uploaded yet</p>
                                <p class="text-sm text-gray-500 mb-4">Upload your first photo to get started</p>
                                <button 
                                    @click="$refs.fileInput.click()"
                                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium inline-flex items-center gap-2"
                                >
                                    <span>➕</span>
                                    <span>Upload Photo</span>
                                </button>
                            </div>
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

                {{-- Service Selection --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Service</label>
                    <select x-model="uploadServiceId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Choose Service --</option>
                        <template x-for="service in services" :key="service.id">
                            <option :value="service.id" x-text="service.icon + ' ' + service.name"></option>
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
                    <p class="text-xs text-gray-500 mt-2">JPG, PNG (Max 5MB per file). You can select multiple files.</p>
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
                        :disabled="!uploadServiceId || selectedFiles.length === 0 || uploading"
                        :class="(!uploadServiceId || selectedFiles.length === 0 || uploading) ? 'bg-gray-300 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
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
                        <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden">
                            <img :src="editingPhoto.url" :alt="editingPhoto.filename" class="w-full h-full object-cover">
                        </div>

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
                                <input type="checkbox" x-model="editingPhoto.isPrimary" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-400"></div>
                            </label>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-3">
                            <button 
                                @click="savePhotoEdit()"
                                class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition"
                            >
                                Save Changes
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
        activeTab: 'meeting-room',
        showUploadModal: false,
        showEditModal: false,
        showDeleteModal: false,
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        uploading: false,
        uploadServiceId: '',
        selectedFiles: [],
        editingPhoto: null,
        deletingPhoto: null,

        services: [
            { id: 'meeting-room', name: 'Meeting Room', icon: '🏢' },
            { id: 'private-office', name: 'Private Office', icon: '🚪' },
            { id: 'sharing-room', name: 'Sharing Room', icon: '👥' },
            { id: 'virtual-office', name: 'Virtual Office', icon: '💼' },
            { id: 'coworking-space', name: 'Coworking Space', icon: '🖥️' },
            { id: 'event-space', name: 'Event Space', icon: '🎉' }
        ],

        photos: [],
        photoIdCounter: 1,

        init() {
            this.generateDummyPhotos();
        },

        generateDummyPhotos() {
            // Generate 3-5 dummy photos for each service
            this.services.forEach((service, serviceIndex) => {
                const photoCount = Math.floor(Math.random() * 3) + 3; // 3-5 photos
                for (let i = 0; i < photoCount; i++) {
                    this.photos.push({
                        id: this.photoIdCounter++,
                        serviceId: service.id,
                        filename: `${service.id}-photo-${i + 1}.jpg`,
                        url: `https://picsum.photos/seed/${serviceIndex}${i}/400/300`,
                        size: Math.floor(Math.random() * 4000000) + 1000000, // 1-5MB
                        caption: i === 0 ? `Beautiful ${service.name.toLowerCase()} space` : '',
                        isPrimary: i === 0,
                        uploadedAt: this.getRandomDate(),
                        uploadedBy: 'Admin User'
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

        getPhotoCount(serviceId) {
            return this.photos.filter(p => p.serviceId === serviceId).length;
        },

        getServicePhotos(serviceId) {
            return this.photos.filter(p => p.serviceId === serviceId);
        },

        openUploadModal() {
            this.uploadServiceId = this.activeTab;
            this.selectedFiles = [];
            this.showUploadModal = true;
        },

        handleFileSelect(event, serviceId) {
            this.uploadServiceId = serviceId;
            this.selectedFiles = [];
            this.processFiles(event.target.files);
        },

        handleModalFileSelect(event) {
            this.selectedFiles = [];
            this.processFiles(event.target.files);
        },

        processFiles(files) {
            Array.from(files).forEach(file => {
                // Validate file type
                if (!file.type.match('image/(jpeg|jpg|png)')) {
                    this.showToastMessage('Invalid file type. Only JPG and PNG allowed.', 'error');
                    return;
                }

                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    this.showToastMessage(`${file.name} is too large. Max 5MB allowed.`, 'error');
                    return;
                }

                // Create preview
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

        uploadPhotos() {
            if (!this.uploadServiceId || this.selectedFiles.length === 0) return;

            this.uploading = true;

            // Simulate upload with timeout
            setTimeout(() => {
                this.selectedFiles.forEach(file => {
                    this.photos.push({
                        id: this.photoIdCounter++,
                        serviceId: this.uploadServiceId,
                        filename: file.name,
                        url: file.preview,
                        size: file.size,
                        caption: '',
                        isPrimary: this.getPhotoCount(this.uploadServiceId) === 0,
                        uploadedAt: 'Just now',
                        uploadedBy: 'Admin User'
                    });
                });

                this.uploading = false;
                this.showUploadModal = false;
                this.selectedFiles = [];
                this.activeTab = this.uploadServiceId;
                this.showToastMessage(`${this.selectedFiles.length || 'Photos'} uploaded successfully!`, 'success');
            }, 2000);
        },

        editPhoto(photo) {
            this.editingPhoto = { ...photo };
            this.showEditModal = true;
        },

        savePhotoEdit() {
            const index = this.photos.findIndex(p => p.id === this.editingPhoto.id);
            if (index !== -1) {
                // If setting as primary, unset previous primary
                if (this.editingPhoto.isPrimary) {
                    this.photos.forEach(p => {
                        if (p.serviceId === this.editingPhoto.serviceId && p.id !== this.editingPhoto.id) {
                            p.isPrimary = false;
                        }
                    });
                }

                this.photos[index] = { ...this.editingPhoto };
                this.showEditModal = false;
                this.showToastMessage('Photo updated successfully!', 'success');
            }
        },

        setPrimaryPhoto(photo) {
            if (photo.isPrimary) return;

            // Unset previous primary
            this.photos.forEach(p => {
                if (p.serviceId === photo.serviceId) {
                    p.isPrimary = false;
                }
            });

            // Set new primary
            const index = this.photos.findIndex(p => p.id === photo.id);
            if (index !== -1) {
                this.photos[index].isPrimary = true;
                this.showToastMessage('Primary photo updated!', 'success');
            }
        },

        deletePhoto(photo) {
            this.deletingPhoto = photo;
            this.showDeleteModal = true;
        },

        confirmDelete() {
            const index = this.photos.findIndex(p => p.id === this.deletingPhoto.id);
            if (index !== -1) {
                const wasPrimary = this.photos[index].isPrimary;
                const serviceId = this.photos[index].serviceId;
                
                this.photos.splice(index, 1);

                // If deleted photo was primary, set first remaining photo as primary
                if (wassPrimary) {
                    const remainingPhotos = this.photos.filter(p => p.serviceId === serviceId);
                    if (remainingPhotos.length > 0) {
                        remainingPhotos[0].isPrimary = true;
                    }
                }

                this.showDeleteModal = false;
                this.showToastMessage('Photo deleted successfully!', 'success');
            }
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
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