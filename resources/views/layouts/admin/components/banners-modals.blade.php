<!-- Create/Edit Banner Modal -->
<div x-show="modals.createEdit.open" 
     x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 z-50 p-4 overflow-y-auto">
    <div class="min-h-full flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl max-w-xl w-full my-8">
            <div class="p-6">
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-900" 
                        x-text="modals.createEdit.isEdit ? 'Edit Banner' : 'Upload New Banner'">
                    </h3>
                    <button @click="closeCreateEditModal()" 
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="submitForm()" class="space-y-4">
                    <!-- Image Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Banner Image *</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors cursor-pointer"
                             @click="document.getElementById('bannerImage').click()">
                            <input type="file" 
                                   id="bannerImage" 
                                   @change="handleImageUpload($event)"
                                   accept="image/*" 
                                   class="hidden">
                            
                            <!-- Image Upload Placeholder - Changed from template x-if to x-show -->
                            <div x-show="!form.imagePreview">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-sm text-gray-600 mb-1">Click to upload or drag and drop</p>
                                <p class="text-xs text-gray-500">PNG, JPG, WebP up to 5MB</p>
                                <p class="text-xs text-gray-500 mt-1">Recommended: 1920x600px (Hero), 800x400px (Section)</p>
                            </div>
                            
                            <!-- Image Preview - Changed from template x-if to x-show -->
                            <div x-show="form.imagePreview">
                                <img :src="form.imagePreview || ''" alt="Preview" class="w-full h-48 object-cover rounded-lg mx-auto mb-3">
                                <button type="button" 
                                        @click.stop="removeImage()"
                                        class="text-sm text-red-600 hover:text-red-700">
                                    Remove Image
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Banner Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Banner Title *</label>
                        <input type="text" 
                               x-model="form.name"
                               placeholder="Enter banner title" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Banner Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                        <textarea rows="3" 
                                  x-model="form.description"
                                  placeholder="Enter banner description" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>

                    <!-- Promo Type Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Promo Type *</label>
                        <select x-model="form.promo_type_id" 
                                @change="loadCategories()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Promo Type</option>
                            <template x-for="type in promoTypes" :key="type.id">
                                <option :value="type.id" x-text="type.name"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Banner Type & Display Location -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Banner Type *</label>
                            <select x-model="form.promo_category_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Type</option>
                                <template x-for="category in categories" :key="category.id">
                                    <option :value="category.id" x-text="category.name"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Display Locations - GUNAKAN CHECKBOXES -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Display Locations *</label>
                            <div class="border border-gray-300 rounded-lg p-4 max-h-48 overflow-y-auto">
                                <template x-for="location in availableLocations" :key="location.id">
                                    <label class="flex items-center space-x-3 py-2 hover:bg-gray-50 px-2 rounded">
                                        <input 
                                            type="checkbox" 
                                            :value="location.id" 
                                            x-model="form.locations"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        >
                                        <span x-text="location.name" class="text-sm text-gray-700"></span>
                                    </label>
                                </template>
                            </div>
                            
                            <!-- Debug info -->
                            <div class="text-xs text-gray-500 mt-2">
                                <span x-show="form.locations.length > 0">
                                    Selected locations: <span x-text="form.locations.length"></span>
                                    (<span x-text="getSelectedLocationNames()"></span>)
                                </span>
                                <span x-show="form.locations.length === 0" class="text-red-500">
                                    No locations selected
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Active Period -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                            <input type="date" 
                                   x-model="form.start_date"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                            <input type="date" 
                                   x-model="form.end_date"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Display Priority -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Display Priority</label>
                        <input type="number" 
                               x-model="form.priority"
                               min="1" max="100" 
                               placeholder="1" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Lower number = Higher priority</p>
                    </div>

                    <!-- Service Types (Always Show) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Service Types
                            <span x-show="form.promo_type_id == 2" class="text-red-500">*</span>
                        </label>
                        <select x-model="form.service_types" 
                                multiple
                                :required="form.promo_type_id == 2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="meeting-room">Meeting Room</option>
                            <option value="co-working">Co-working Space</option>
                            <option value="virtual-office">Virtual Office</option>
                            <option value="all-services">All Services</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Hold Ctrl to select multiple services</p>
                        <p x-show="form.promo_type_id == 2" class="text-xs text-blue-500 mt-1">
                            Required for discount promotions
                        </p>
                    </div>

                    <!-- Discount Fields (Always Show) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Discount Type
                                <span x-show="form.promo_type_id == 2" class="text-red-500">*</span>
                            </label>
                            <select x-model="form.discount_type"
                                    :required="form.promo_type_id == 2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Discount Type</option>
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount</option>
                            </select>
                            <p x-show="form.promo_type_id != 2" class="text-xs text-gray-500 mt-1">Optional for banners</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Discount Amount
                                <span x-show="form.promo_type_id == 2" class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                x-model="form.discount_amount"
                                :required="form.promo_type_id == 2"
                                min="0" 
                                step="0.01"
                                placeholder="0.00"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p x-show="form.promo_type_id != 2" class="text-xs text-gray-500 mt-1">Optional for banners</p>
                        </div>
                    </div>

                    <!-- Transaction Limits (Always Show) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Minimum Transaction
                                <span x-show="form.promo_type_id == 2" class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                x-model="form.min_transaction"
                                :required="form.promo_type_id == 2"
                                min="0" 
                                step="0.01"
                                placeholder="0.00"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p x-show="form.promo_type_id != 2" class="text-xs text-gray-500 mt-1">Optional for banners</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Usage Limit
                                <span x-show="form.promo_type_id == 2" class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                x-model="form.usage_limit"
                                :required="form.promo_type_id == 2"
                                min="0" 
                                placeholder="Unlimited (0 for unlimited)"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p x-show="form.promo_type_id != 2" class="text-xs text-gray-500 mt-1">Optional for banners</p>
                        </div>
                    </div>

                    <!-- Usage Per User (Always Show) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Usage Per User
                            <span x-show="form.promo_type_id == 2" class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                            x-model="form.usage_per_user"
                            :required="form.promo_type_id == 2"
                            min="1" 
                            placeholder="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p x-show="form.promo_type_id != 2" class="text-xs text-gray-500 mt-1">Optional for banners</p>
                    </div>

                    <!-- Status Select - LEBIH SIMPLE -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select x-model="form.status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="draft">Draft</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="ended">Ended</option>
                        </select>
                        
                        <!-- Debug info -->
                        <div class="text-xs text-gray-500 mt-1">
                            Selected status: <span x-text="form.status"></span>
                        </div>
                    </div>
                    <!-- Form Buttons -->
                    <div class="flex gap-3 pt-4">
                        <button type="button" 
                                @click="closeCreateEditModal()"
                                class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                            Cancel
                        </button>
                        <button type="submit" 
                                :disabled="form.loading"
                                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            <!-- Changed from nested template x-if to x-show -->
                            <span x-show="form.loading" class="flex items-center justify-center">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                <span x-text="modals.createEdit.isEdit ? 'Updating...' : 'Uploading...'"></span>
                            </span>
                            <span x-show="!form.loading" x-text="modals.createEdit.isEdit ? 'Update Banner' : 'Upload Banner'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div x-show="modals.delete.open" 
     x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Delete Banner</h3>
            <p class="text-sm text-gray-600 text-center mb-6">Are you sure you want to delete this banner? This action cannot be undone.</p>
            
            <!-- Banner Preview in Modal -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6" x-show="modals.delete.banner">
                <div class="flex items-center gap-3">
                    <img :src="getBannerImageUrl(modals.delete.banner)" 
                            alt="Banner" 
                            class="w-20 h-10 object-cover rounded">
                    <div>
                        <p class="text-sm font-medium text-gray-900" x-text="modals.delete.banner?.name"></p>
                        <p class="text-xs text-gray-600" x-text="(modals.delete.banner?.category?.name || '') + ' • ' + formatLocations(modals.delete.banner?.locations || [])"></p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button @click="closeDeleteModal()" 
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Cancel
                </button>
                <button @click="deleteBanner()" 
                        :disabled="modals.delete.loading"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                    <!-- Changed from nested template x-if to x-show -->
                    <span x-show="modals.delete.loading" class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                        Deleting...
                    </span>
                    <span x-show="!modals.delete.loading">Delete Banner</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Preview Banner Modal -->
<div x-show="modals.preview.open" 
     x-cloak
     class="fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4">
    <div class="relative max-w-6xl w-full">
        <!-- Close Button -->
        <button @click="closePreviewModal()" 
                class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Banner Preview -->
        <div class="bg-white rounded-lg overflow-hidden">
            <img :src="modals.preview.banner?.image_url || 'https://placehold.co/1920x600/3B82F6/ffffff?text=Banner+Preview'" 
                 :alt="modals.preview.banner?.name || 'Banner Preview'" 
                 class="w-full h-auto">
        </div>

        <!-- Banner Info -->
        <div class="bg-white rounded-lg p-4 mt-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-2" x-text="modals.preview.banner?.name || 'Banner'"></h3>
            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-white text-gray-700 text-xs font-semibold rounded-full" 
                        x-text="modals.preview.banner?.category?.name || 'No Category'"> <!-- BENAR: modals.preview.banner -->
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span x-text="formatLocations(modals.preview.banner?.locations || [])"></span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span x-text="modals.preview.banner ? `${modals.preview.banner.start_date || ''} - ${modals.preview.banner.end_date || ''}` : 'N/A'"></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 rounded text-xs font-medium"
                          :class="{
                              'bg-green-100 text-green-700': modals.preview.banner?.status === 'active',
                              'bg-gray-100 text-gray-700': modals.preview.banner?.status === 'inactive',
                              'bg-red-100 text-red-700': modals.preview.banner?.status === 'ended',
                              'bg-blue-100 text-blue-700': modals.preview.banner?.status === 'upcoming'
                          }"
                          x-text="getStatusText(modals.preview.banner?.status || '')">
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>