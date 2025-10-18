{{-- resources/views/admin/content/highlights.blade.php --}}

@extends('layouts.admin')

@section('title', 'Service Highlights')

@section('content')
<div x-data="serviceHighlights()" x-init="init()" class="space-y-4 md:space-y-6 pb-20 md:pb-6 max-w-full overflow-hidden">
    
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="min-w-0">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 truncate">⭐ Service Highlights</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Manage features and facilities for each service</p>
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
                        class="px-3 sm:px-4 md:px-6 py-2 sm:py-3 md:py-4 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap transition flex items-center gap-1 sm:gap-2 flex-shrink-0"
                    >
                        <span x-text="service.icon"></span>
                        <span class="hidden sm:inline" x-text="service.name"></span>
                        <span class="sm:hidden" x-text="service.name.split(' ')[0]"></span>
                        <span 
                            class="px-1 sm:px-1.5 py-0.5 text-xs rounded-full"
                            :class="activeTab === service.id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'"
                            x-text="getHighlightCount(service.id)"
                        ></span>
                    </button>
                </template>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="p-3 sm:p-4 md:p-6">
            <template x-for="service in services" :key="service.id">
                <div x-show="activeTab === service.id" x-transition>
                    
                    {{-- Header with Add Button --}}
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800">
                            <span x-text="service.icon"></span>
                            <span x-text="service.name + ' Features'"></span>
                        </h3>
                        <button 
                            @click="openHighlightModal(service)"
                            class="px-3 sm:px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-xs sm:text-sm flex items-center gap-1 sm:gap-2 whitespace-nowrap"
                        >
                            <span>➕</span>
                            <span class="hidden sm:inline">Add Highlight</span>
                            <span class="sm:hidden">Add</span>
                        </button>
                    </div>

                    {{-- Highlights List --}}
                    <div class="space-y-3">
                        <template x-for="(highlight, index) in getServiceHighlights(service.id)" :key="highlight.id">
                            <div class="bg-white rounded-lg border-2 border-gray-200 hover:border-blue-300 transition p-3 sm:p-4">
                                <div class="flex items-start gap-3">
                                    {{-- Icon --}}
                                    <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-bold text-sm sm:text-base">
                                        <span x-text="highlight.icon"></span>
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2 mb-2">
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm sm:text-base font-semibold text-gray-800 mb-1" x-text="highlight.text"></h4>
                                                <p x-show="highlight.description" class="text-xs sm:text-sm text-gray-600" x-text="highlight.description"></p>
                                            </div>
                                            <span 
                                                :class="highlight.active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'"
                                                class="px-2 py-1 rounded-full text-xs font-semibold flex-shrink-0"
                                                x-text="highlight.active ? 'Active' : 'Inactive'"
                                            ></span>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="flex flex-wrap gap-2">
                                            <button 
                                                @click="editHighlight(highlight)"
                                                class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 text-xs sm:text-sm font-medium transition"
                                            >
                                                ✏️ Edit
                                            </button>
                                            <button 
                                                @click="deleteHighlight(highlight)"
                                                class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 text-xs sm:text-sm font-medium transition"
                                            >
                                                🗑️ Delete
                                            </button>
                                            <div class="flex gap-1">
                                                <button 
                                                    @click="moveUp(service.id, index)"
                                                    :disabled="index === 0"
                                                    :class="index === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                                    class="px-2 sm:px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs sm:text-sm font-medium transition"
                                                >
                                                    ↑
                                                </button>
                                                <button 
                                                    @click="moveDown(service.id, index)"
                                                    :disabled="index === getServiceHighlights(service.id).length - 1"
                                                    :class="index === getServiceHighlights(service.id).length - 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                                    class="px-2 sm:px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs sm:text-sm font-medium transition"
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
                        x-show="getServiceHighlights(service.id).length === 0"
                        class="text-center py-12 sm:py-16 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300"
                    >
                        <div class="text-gray-400">
                            <div class="text-4xl sm:text-5xl mb-3">⭐</div>
                            <p class="text-base sm:text-lg font-medium text-gray-600 mb-2">No highlights yet</p>
                            <p class="text-xs sm:text-sm text-gray-500 mb-4 px-4">Add features and facilities for this service</p>
                            <button 
                                @click="openHighlightModal(service)"
                                class="px-4 sm:px-6 py-2 sm:py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium inline-flex items-center gap-2 text-sm"
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
        <div class="flex items-center justify-center min-h-screen px-3 sm:px-4">
            <div @click="showModal = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-4 sm:p-6 my-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800" x-text="editingHighlight ? 'Edit Highlight' : 'Add New Highlight'"></h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 flex-shrink-0 ml-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-3 sm:space-y-4">
                    {{-- Service Display --}}
                    <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                        <p class="text-xs text-blue-700 mb-1">Service</p>
                        <p class="text-sm font-semibold text-blue-900" x-text="selectedService?.icon + ' ' + selectedService?.name"></p>
                    </div>

                    {{-- Icon --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Icon/Emoji</label>
                        <input 
                            type="text" 
                            x-model="highlightForm.icon"
                            placeholder="e.g., ✓ or 📶"
                            maxlength="2"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                        >
                        <p class="text-xs text-gray-500 mt-1">Single emoji or character (e.g., ✓, ✔, 🌐, 📶)</p>
                    </div>

                    {{-- Highlight Text --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Highlight Text <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            x-model="highlightForm.text"
                            placeholder="e.g., Free Wi-Fi"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                        >
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                        <textarea 
                            x-model="highlightForm.description"
                            rows="3"
                            placeholder="Add more details about this feature..."
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                        ></textarea>
                    </div>

                    {{-- Active Toggle --}}
                    <div class="flex items-center justify-between p-3 sm:p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="min-w-0 mr-3">
                            <p class="text-sm font-medium text-gray-800">Active Status</p>
                            <p class="text-xs text-gray-600">Show this highlight to customers</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input type="checkbox" x-model="highlightForm.active" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                        </label>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-4">
                        <button 
                            @click="saveHighlight()"
                            :disabled="!highlightForm.text"
                            :class="!highlightForm.text ? 'bg-gray-300 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                            class="flex-1 px-4 sm:px-6 py-2 sm:py-3 text-white rounded-lg font-semibold transition text-sm"
                        >
                            <span x-text="editingHighlight ? 'Update Highlight' : 'Add Highlight'"></span>
                        </button>
                        <button 
                            @click="showModal = false"
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
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-2">Delete Highlight</h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">
                        Are you sure you want to delete this highlight?<br>
                        <span class="text-xs sm:text-sm text-gray-500">This action cannot be undone.</span>
                    </p>

                    <template x-if="deletingHighlight">
                        <div class="mb-3 sm:mb-4 p-2 sm:p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs sm:text-sm font-medium text-gray-800" x-text="deletingHighlight.text"></p>
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
function serviceHighlights() {
    return {
        activeTab: 'meeting-room',
        showModal: false,
        showDeleteModal: false,
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        selectedService: null,
        editingHighlight: null,
        deletingHighlight: null,

        services: [
            { id: 'meeting-room', name: 'Meeting Room', icon: '🏢' },
            { id: 'private-office', name: 'Private Office', icon: '🚪' },
            { id: 'sharing-room', name: 'Sharing Room', icon: '👥' },
            { id: 'virtual-office', name: 'Virtual Office', icon: '💼' },
            { id: 'coworking-space', name: 'Coworking Space', icon: '🖥️' },
            { id: 'event-space', name: 'Event Space', icon: '🎉' }
        ],

        highlights: {},

        highlightForm: {
            icon: '✓',
            text: '',
            description: '',
            active: true
        },

        init() {
            this.generateDummyHighlights();
        },

        generateDummyHighlights() {
            const highlightsData = {
                'meeting-room': [
                    { icon: '✓', text: 'Free Wi-Fi', description: 'High-speed internet connection', active: true },
                    { icon: '✓', text: 'Projector & Screen', description: 'HD projector with large screen', active: true },
                    { icon: '✓', text: 'Air Conditioned', description: 'Comfortable temperature control', active: true },
                    { icon: '✓', text: 'Whiteboard', description: 'Large whiteboard with markers', active: true },
                    { icon: '✓', text: 'Video Conference Ready', description: 'Camera and microphone setup', active: true }
                ],
                'private-office': [
                    { icon: '✓', text: 'Private Workspace', description: 'Dedicated office space', active: true },
                    { icon: '✓', text: 'Lockable Door', description: 'Secure and private', active: true },
                    { icon: '✓', text: 'Ergonomic Furniture', description: 'Comfortable desk and chair', active: true },
                    { icon: '✓', text: 'Storage Cabinet', description: 'Personal storage space', active: true }
                ],
                'sharing-room': [
                    { icon: '✓', text: 'Shared Workspace', description: 'Collaborative environment', active: true },
                    { icon: '✓', text: 'Hot Desking', description: 'Flexible seating arrangement', active: true },
                    { icon: '✓', text: 'Meeting Area', description: 'Shared discussion space', active: true }
                ],
                'virtual-office': [
                    { icon: '✓', text: 'Business Address', description: 'Professional mailing address', active: true },
                    { icon: '✓', text: 'Mail Handling', description: 'Receive and forward mail', active: true },
                    { icon: '✓', text: 'Phone Answering', description: 'Professional call handling', active: true },
                    { icon: '✓', text: 'Meeting Room Access', description: 'Book meeting rooms when needed', active: true }
                ],
                'coworking-space': [
                    { icon: '✓', text: '24/7 Access', description: 'Work anytime you want', active: true },
                    { icon: '✓', text: 'Free Coffee & Tea', description: 'Unlimited beverages', active: true },
                    { icon: '✓', text: 'Printing Facilities', description: 'Free printing and scanning', active: true },
                    { icon: '✓', text: 'Locker Storage', description: 'Secure personal storage', active: true },
                    { icon: '✓', text: 'Community Events', description: 'Networking opportunities', active: true }
                ],
                'event-space': [
                    { icon: '✓', text: 'Spacious Venue', description: 'Large event area', active: true },
                    { icon: '✓', text: 'Audio System', description: 'Professional sound equipment', active: true },
                    { icon: '✓', text: 'Stage Setup', description: 'Presentation stage available', active: true },
                    { icon: '✓', text: 'Catering Support', description: 'Food and beverage options', active: true }
                ]
            };

            let idCounter = 1;
            this.services.forEach(service => {
                this.highlights[service.id] = (highlightsData[service.id] || []).map((h, index) => ({
                    id: idCounter++,
                    serviceId: service.id,
                    icon: h.icon,
                    text: h.text,
                    description: h.description,
                    order: index + 1,
                    active: h.active
                }));
            });
        },

        getHighlightCount(serviceId) {
            return this.highlights[serviceId]?.length || 0;
        },

        getServiceHighlights(serviceId) {
            return this.highlights[serviceId] || [];
        },

        openHighlightModal(service) {
            this.selectedService = service;
            this.editingHighlight = null;
            this.highlightForm = {
                icon: '✓',
                text: '',
                description: '',
                active: true
            };
            this.showModal = true;
        },

        editHighlight(highlight) {
            this.selectedService = this.services.find(s => s.id === highlight.serviceId);
            this.editingHighlight = highlight;
            this.highlightForm = {
                icon: highlight.icon,
                text: highlight.text,
                description: highlight.description,
                active: highlight.active
            };
            this.showModal = true;
        },

        saveHighlight() {
            if (!this.highlightForm.text) {
                this.showToastMessage('Please enter highlight text', 'error');
                return;
            }

            if (this.editingHighlight) {
                // Update existing highlight
                const index = this.highlights[this.selectedService.id].findIndex(h => h.id === this.editingHighlight.id);
                if (index !== -1) {
                    this.highlights[this.selectedService.id][index] = {
                        ...this.highlights[this.selectedService.id][index],
                        icon: this.highlightForm.icon,
                        text: this.highlightForm.text,
                        description: this.highlightForm.description,
                        active: this.highlightForm.active
                    };
                }
                this.showToastMessage('Highlight updated successfully!', 'success');
            } else {
                // Add new highlight
                if (!this.highlights[this.selectedService.id]) {
                    this.highlights[this.selectedService.id] = [];
                }
                
                const newHighlight = {
                    id: Date.now(),
                    serviceId: this.selectedService.id,
                    icon: this.highlightForm.icon,
                    text: this.highlightForm.text,
                    description: this.highlightForm.description,
                    order: this.highlights[this.selectedService.id].length + 1,
                    active: this.highlightForm.active
                };
                
                this.highlights[this.selectedService.id].push(newHighlight);
                this.showToastMessage('Highlight added successfully!', 'success');
            }

            this.showModal = false;
        },

        deleteHighlight(highlight) {
            this.deletingHighlight = highlight;
            this.showDeleteModal = true;
        },

        confirmDelete() {
            const serviceId = this.deletingHighlight.serviceId;
            const index = this.highlights[serviceId].findIndex(h => h.id === this.deletingHighlight.id);
            
            if (index !== -1) {
                this.highlights[serviceId].splice(index, 1);
                // Reorder remaining highlights
                this.highlights[serviceId].forEach((h, idx) => {
                    h.order = idx + 1;
                });
                this.showToastMessage('Highlight deleted successfully!', 'success');
            }

            this.showDeleteModal = false;
        },

        moveUp(serviceId, index) {
            if (index === 0) return;
            
            const temp = this.highlights[serviceId][index];
            this.highlights[serviceId][index] = this.highlights[serviceId][index - 1];
            this.highlights[serviceId][index - 1] = temp;
            
            // Update order numbers
            this.highlights[serviceId].forEach((h, idx) => {
                h.order = idx + 1;
            });
            
            this.showToastMessage('Highlight moved up!', 'success');
        },

        moveDown(serviceId, index) {
            if (index === this.highlights[serviceId].length - 1) return;
            
            const temp = this.highlights[serviceId][index];
            this.highlights[serviceId][index] = this.highlights[serviceId][index + 1];
            this.highlights[serviceId][index + 1] = temp;
            
            // Update order numbers
            this.highlights[serviceId].forEach((h, idx) => {
                h.order = idx + 1;
            });
            
            this.showToastMessage('Highlight moved down!', 'success');
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