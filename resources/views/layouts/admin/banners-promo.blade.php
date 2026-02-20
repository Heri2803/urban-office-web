@extends('layouts.admin')

@section('title', 'Banner Promo Management')

@section('content')
<div class="container-fluid px-4 py-6" x-data="bannerManagement()" x-init="init()">
    <!-- Include Modals dari file asli -->
    @include('layouts.admin.components.banners-modals')
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Banner Promo Management</h1>
                <p class="text-gray-600">Upload and manage promotional banners for customer pages</p>
            </div>
            <button @click="openCreateModal()" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Upload New Banner
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Banners</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.total"></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Active</p>
                    <p class="text-2xl font-bold text-green-600" x-text="stats.active"></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Inactive</p>
                    <p class="text-2xl font-bold text-gray-600" x-text="stats.inactive"></p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Expired</p>
                    <p class="text-2xl font-bold text-red-600" x-text="stats.expired"></p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Banner</label>
                <div class="relative">
                    <input type="text" 
                           x-model="filters.search" 
                           @input.debounce.300ms="applyFilters()"
                           placeholder="Search by title..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            <!-- Filter by Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Banner Type</label>
                <select x-model="filters.type" 
                        @change="applyFilters()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Types</option>
                    
                    <!-- GUNAKAN categories LANGSUNG, BUKAN getCategories() -->
                    <template x-for="category in categories" :key="category.id">
                        <option :value="category.id" x-text="category.name"></option>
                    </template>
                    
                    <!-- Loading state -->
                    <template x-if="loading">
                        <option disabled>Loading categories...</option>
                    </template>
                    
                    <!-- Empty state -->
                    <template x-if="!loading && (!categories || categories.length === 0)">
                        <option disabled>No categories available</option>
                    </template>
                </select>
                
                <!-- Debug info - GUNAKAN categories LANGSUNG -->
                <div class="text-xs text-gray-500 mt-1">
                    <span x-show="loading">Loading categories...</span>
                    <span x-show="!loading">
                        <span x-text="categories ? categories.length : 0"></span> categories available
                    </span>
                </div>
            </div>

            <!-- Filter by Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select x-model="filters.status" 
                        @change="applyFilters()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="ended">Expired</option>
                    <option value="upcoming">Upcoming</option>
                </select>
            </div>
        </div>

        <!-- Filter Buttons -->
        <div class="flex flex-wrap gap-3 mt-4">
            <button @click="applyFilters()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Apply Filter
            </button>
            <button @click="resetFilters()" 
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                Reset Filter
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <template x-if="loading">
        <div class="text-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
            <p class="text-gray-600 mt-4">Loading banners...</p>
        </div>
    </template>

    <!-- Banner Grid -->
    <template x-if="!loading && filteredBanners.length > 0">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="banner in filteredBanners" :key="banner.id">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow"
                 :class="{
                     'opacity-75': banner.status === 'inactive',
                     'opacity-60': banner.status === 'ended'
                 }">
                
                <!-- Banner Image -->
                <div class="relative h-48 bg-gray-200">
                    <img :src="banner.image_url ? '/storage/' + banner.image_url : '/images/placeholder.jpg'" 
                         :alt="banner.name" 
                         class="w-full h-full object-cover">
                    <div class="absolute top-3 right-3">
                        <span class="px-3 py-1 text-white text-xs font-semibold rounded-full"
                              :class="{
                                  'bg-green-500': banner.status === 'active',
                                  'bg-gray-500': banner.status === 'inactive',
                                  'bg-red-500': banner.status === 'ended',
                                  'bg-blue-500': banner.status === 'upcoming'
                              }"
                              x-text="getStatusText(banner.status)">
                        </span>
                    </div>
                    <div class="absolute top-3 left-3">
                        <span class="px-3 py-1 bg-white text-gray-700 text-xs font-semibold rounded-full"
                              x-text="banner.category.name">
                        </span>
                    </div>
                </div>

                <!-- Banner Info -->
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2" x-text="banner.name"></h3>
                    
                    <!-- Kode Banner -->
                    <div class="mb-2">
                        <span class="inline-block px-2 py-1 bg-gray-100 text-gray-800 text-xs font-mono rounded border"
                              x-text="'Kode: ' + (banner.code || '-')">
                        </span>
                    </div>
                    
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2" x-text="banner.description || 'No description'"></p>
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span x-text="formatLocations(banner.locations)"></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <!-- Format tanggal tanpa waktu -->
                            <span x-text="`${formatDateOnly(banner.start_date)} - ${formatDateOnly(banner.end_date)}`"></span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <button @click="previewBanner(banner)" 
                                class="flex-1 px-3 py-2 bg-blue-100 text-blue-700 text-sm rounded-lg hover:bg-blue-200 transition-colors font-medium flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Preview
                        </button>
                        <button @click="openEditModal(banner)" 
                                class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors font-medium flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </button>
                        <button @click="confirmDelete(banner)" 
                                class="px-3 py-2 bg-red-100 text-red-700 text-sm rounded-lg hover:bg-red-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

    <!-- Empty State -->
    <template x-if="!loading && filteredBanners.length === 0">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No banners found</h3>
            <p class="text-sm text-gray-500 mb-4" x-text="hasActiveFilters ? 'Try adjusting your filters or search terms' : 'Get started by uploading your first banner'"></p>
            <template x-if="!hasActiveFilters">
                <button @click="openCreateModal()" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Upload First Banner
                </button>
            </template>
        </div>
    </template>

    <!-- Pagination -->
    <template x-if="!loading && filteredBanners.length > 0">
        <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-sm text-gray-600" x-text="`Showing 1 to ${filteredBanners.length} of ${banners.length} banners`"></p>
            <!-- Pagination akan diimplement nanti -->
        </div>
    </template>
</div>

@push('scripts')
<script>
function bannerManagement() {
    return {
        // State
        loading: false,
        banners: [],
        
        // Data yang diperlukan untuk template
        categories: [],
        availableLocations: [],
        promoTypes: [],
        
        // Filters
        filters: {
            search: '',
            status: '',
            category: '',
            location: ''    
        },
        
        // Computed property untuk cek active filters
        get hasActiveFilters() {
            return Object.values(this.filters).some(value => 
                value !== '' && value !== null && value !== undefined
            );
        },
        
        // Stats - akan dihitung secara real-time
        get stats() {
            const total = this.filteredBanners.length;
            const active = this.filteredBanners.filter(b => b.status === 'active').length;
            const inactive = this.filteredBanners.filter(b => b.status === 'inactive').length;
            const expired = this.filteredBanners.filter(b => b.status === 'expired' || b.status === 'ended').length;
            
            return {
                total,
                active,
                inactive,
                expired
            };
        },
        
        // Filtered banners berdasarkan criteria

        get filteredBanners() {
            let filtered = this.banners;
            
            if (this.filters.search) {
                const searchTerm = this.filters.search.toLowerCase();
                filtered = filtered.filter(banner => 
                    banner.name.toLowerCase().includes(searchTerm) ||
                    (banner.description && banner.description.toLowerCase().includes(searchTerm))
                );
            }
            
            if (this.filters.status) {
                filtered = filtered.filter(banner => banner.status === this.filters.status);
            }
            
            // PERBAIKI: Gunakan filters.type (bukan filters.category)
            if (this.filters.type) {
                filtered = filtered.filter(banner => 
                    banner.promo_category_id == this.filters.type // atau banner.category_id, sesuaikan dengan struktur data
                );
            }
            
            if (this.filters.location) {
                filtered = filtered.filter(banner => 
                    banner.locations && banner.locations.includes(this.filters.location)
                );
            }
            
            return filtered;
        },
        
        // Modals
        modals: {
            createEdit: {
                open: false,
                isEdit: false,
                loading: false
            },
            delete: {
                open: false,
                banner: null,
                loading: false
            },
            preview: {
                open: false,
                banner: null
            }
        },
        
        // Form
        form: {
            name: '',
            description: '',
            promo_type_id: null,
            promo_category_id: null,
            locations: [],
            start_date: '',
            end_date: '',
            priority: 1,
            status: 'draft',
            
            // New fields
            service_types: [],
            discount_type: 'percentage',
            discount_amount: 0,
            min_transaction: 0,
            usage_limit: null,
            usage_per_user: 1,
            
            imagePreview: null,
            imageFile: null,
            loading: false
        },
        
        // Initialize
        async init() {
            console.log('✅ Banner Management Initialized');
            await this.loadInitialData();
            await this.loadBanners();
            await this.loadCategories();
        },
        
        // Load initial data (categories & locations)
        async loadInitialData() {
            try {
                // Load categories dari API
                try {
                    const typesResponse = await fetch('{{ route("admin.banners.types") }}');
                    if (typesResponse.ok) {
                        const typesData = await typesResponse.json();
                        this.promoTypes = typesData;
                        console.log('✅ Promo types loaded:', this.promoTypes.length);
                    } else {
                        throw new Error('Promo types API failed');
                    }
                } catch (error) {
                    console.warn('⚠️ Using fallback categories:', error);
                    this.promoTypes = [
                        { id: 1, name: 'Banner', slug: 'banner' },
                        { id: 2, name: 'Discount', slug: 'discount' }
                    ];
                }

                 await this.loadCategories();

                // GUNAKAN FALLBACK LOCATIONS SAJA - JANGAN PANGGIL API
                console.log('📍 Using fallback branch locations');
                this.availableLocations = [
                    { id: 1, name: 'Urban Office - Merr', code: '1' },
                    { id: 2, name: 'Urban Office - Grand Sungkono', code: '2' },
                    { id: 3, name: 'Urban Office - Gorebiz', code: '3' }
                ];

                console.log('📊 Available categories:', this.categories);
                console.log('🏢 Available branch locations:', this.availableLocations);
                
            } catch (error) {
                console.error('Error loading initial data:', error);
            }
        },

        // Load categories berdasarkan promo type
        // MODIFIKASI: loadCategories() yang bisa bekerja standalone
        async loadCategories(promoTypeId = null) {
            try {
                console.log('📥 Loading categories...', promoTypeId ? `for type: ${promoTypeId}` : 'all categories');
                
                let apiUrl = '/banners/api/categories';
                
                // Jika ada promoTypeId, filter by type, jika tidak ambil semua
                if (promoTypeId) {
                    apiUrl += `?type_id=${promoTypeId}`;
                }
                
                const response = await fetch(apiUrl);
                
                if (response.ok) {
                    const categoriesData = await response.json();
                    
                    // Process data
                    let processedCategories = [];
                    
                    if (Array.isArray(categoriesData)) {
                        processedCategories = categoriesData;
                    } else if (categoriesData.data && Array.isArray(categoriesData.data)) {
                        processedCategories = categoriesData.data;
                    } else if (categoriesData.categories && Array.isArray(categoriesData.categories)) {
                        processedCategories = categoriesData.categories;
                    }
                    
                    if (processedCategories.length > 0) {
                        this.categories = processedCategories;
                        console.log(`✅ ${this.categories.length} categories loaded`);
                        return processedCategories;
                    } else {
                        throw new Error('No categories data received');
                    }
                } else {
                    throw new Error(`API returned ${response.status}`);
                }
                
            } catch (error) {
                console.warn('⚠️ Error loading categories from API:', error);
                
                // Fallback categories berdasarkan apakah ada promoTypeId atau tidak
                const fallbackCategories = this.getFallbackCategories(promoTypeId);
                this.categories = fallbackCategories;
                console.log(`🔄 Using ${fallbackCategories.length} fallback categories`);
                
                return fallbackCategories;
            }
        },

        // Helper function untuk fallback categories
        getFallbackCategories(promoTypeId = null) {
            const allCategories = [
                { id: 1, name: 'Hero Banner', promo_type_id: 1 },
                { id: 2, name: 'Section Banner', promo_type_id: 1 },
                { id: 3, name: 'Sidebar Banner', promo_type_id: 1 },
                { id: 4, name: 'Popup Banner', promo_type_id: 1 },
                { id: 5, name: 'Percentage Discount', promo_type_id: 2 },
                { id: 6, name: 'Fixed Amount Discount', promo_type_id: 2 },
                { id: 7, name: 'Special Offer', promo_type_id: 2 },
                { id: 8, name: 'Seasonal Promotion', promo_type_id: 2 }
            ];
            
            if (promoTypeId) {
                return allCategories.filter(cat => cat.promo_type_id == promoTypeId);
            }
            
            return allCategories;
        },

        async loadBanners() {
            try {
                this.loading = true;
                const response = await fetch('{{ route("admin.banners.api") }}');
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        this.banners = result.data || [];
                        console.log('✅ Banners loaded from API:', this.banners.length);
                    } else {
                        throw new Error(result.message || 'API returned error');
                    }
                } else {
                    throw new Error('Failed to load banners: ' + response.status);
                }
            } catch (error) {
                console.error('❌ Error loading banners:', error);
                // Fallback data untuk testing
                this.banners = [
                    {
                        id: 1,
                        name: 'Summer Sale 2024',
                        status: 'active',
                        start_date: '2024-06-01',
                        end_date: '2024-06-30',
                        image_url: '/images/banners/summer-sale.jpg',
                        promo_category_id: 1,
                        category: { name: 'Hero Banner' },
                        locations: ['homepage'],
                        description: 'Special summer promotion',
                        priority: 1
                    },
                    {
                        id: 2,
                        name: 'New Year Special',
                        status: 'upcoming',
                        start_date: '2024-12-25',
                        end_date: '2025-01-05',
                        image_url: '/images/banners/new-year.jpg',
                        promo_category_id: 2,
                        category: { name: 'Section Banner' },
                        locations: ['booking', 'promotions'],
                        description: 'New year exclusive offer',
                        priority: 2
                    }
                ];
                console.log('📦 Using fallback banners:', this.banners.length);
            } finally {
                this.loading = false;
            }
        },

        // Filter Methods
        applyFilters() {
            console.log('🔍 Applying filters:', this.filters);
        },
        
        resetFilters() {
            this.filters = {
                search: '',
                status: '',
                category: '',
                location: ''
            };
            console.log('🔄 Filters reset');
        },
        
        // Modal Methods
        openCreateModal() {
            console.log('📝 Opening create modal');
            this.resetForm();
            this.modals.createEdit.isEdit = false;
            this.modals.createEdit.open = true;
        },
        
        openEditModal(banner) {
            console.log('✏️ Opening edit modal for:', banner.name);
            
            // Simpan banner ID untuk update
            this.editingBannerId = banner.id;
            
            // Isi form dengan data banner
            this.form.name = banner.name;
            this.form.description = banner.description || '';
            this.form.promo_type_id = banner.promo_type_id;
            this.form.promo_category_id = banner.promo_category_id;
            this.form.status = banner.status;
            this.form.start_date = this.formatDateForInput(banner.start_date);
            this.form.end_date = this.formatDateForInput(banner.end_date);
            this.form.priority = banner.priority || 1;
            this.form.locations = banner.locations || [];
            
            // New fields
            this.form.service_types = banner.service_types || [];
            this.form.discount_type = banner.discount_type || 'percentage';
            this.form.discount_amount = banner.discount_amount || 0;
            this.form.min_transaction = banner.min_transaction || 0;
            this.form.usage_limit = banner.usage_limit;
            this.form.usage_per_user = banner.usage_per_user || 1;
            
            // Load categories berdasarkan promo type
            this.loadCategories();
            
            this.modals.createEdit.isEdit = true;
            this.modals.createEdit.open = true;
        },
        
        closeCreateEditModal() {
            console.log('❌ Closing modal');
            this.modals.createEdit.open = false;
            this.resetForm();
        },
        
        previewBanner(banner) {
            console.log('👀 Previewing banner:', banner.name);
            this.modals.preview.banner = banner;
            this.modals.preview.open = true;
        },
        
        closePreviewModal() {
            this.modals.preview.open = false;
            this.modals.preview.banner = null;
        },
        
        confirmDelete(banner) {
            console.log('🗑️ Confirm delete:', banner.name);
            
            // Pastikan image URL benar (tanpa duplikasi /storage/)
            if (banner.image_url && !banner.image_url.startsWith('http')) {
                // Jika belum ada /storage/ dan bukan URL external, tambahkan
                if (!banner.image_url.startsWith('/storage/')) {
                    banner.image_url = '/storage/' + banner.image_url;
                }
            }
            
            this.modals.delete.banner = banner;
            this.modals.delete.open = true;
        },
        
        closeDeleteModal() {
            this.modals.delete.open = false;
            this.modals.delete.banner = null;
        },
        
        // Form Methods
        resetForm() {
            this.form = {
                name: '',
                description: '',
                promo_type_id: null,
                promo_category_id: null,
                locations: [],
                start_date: '',
                end_date: '',
                priority: 1,
                status: 'draft',
                
                // New fields
                service_types: [],
                discount_type: 'percentage',
                discount_amount: 0,
                min_transaction: 0,
                usage_limit: null,
                usage_per_user: 1,
                
                imagePreview: null,
                imageFile: null,
                loading: false
            };
        },
                
        // Di dalam submitForm(), perbaiki validation locations:
        async submitForm() {
            console.log('📤 Submitting form to backend...');
            this.form.loading = true;

            try {
                // Debug form values
                console.log('🔍 DEBUG Form values:', {
                    name: this.form.name,
                    promo_type_id: this.form.promo_type_id,
                    promo_category_id: this.form.promo_category_id,
                    locations: this.form.locations,
                    start_date: this.form.start_date,
                    end_date: this.form.end_date,
                    status: this.form.status,
                    priority: this.form.priority,
                    service_types: this.form.service_types,
                    discount_type: this.form.discount_type,
                    discount_amount: this.form.discount_amount,
                    min_transaction: this.form.min_transaction,
                    usage_limit: this.form.usage_limit,
                    usage_per_user: this.form.usage_per_user
                });

                // Validasi client-side dasar
                if (!this.form.name || this.form.name.trim() === '') {
                    this.showNotification('Banner name is required', 'error');
                    return;
                }

                if (!this.form.promo_type_id) {
                    this.showNotification('Please select a promo type', 'error');
                    return;
                }

                if (!this.form.promo_category_id) {
                    this.showNotification('Please select a category', 'error');
                    return;
                }

                // Validasi locations
                if (!this.form.locations || !Array.isArray(this.form.locations) || this.form.locations.length === 0) {
                    console.log('❌ Locations validation failed:', this.form.locations);
                    this.showNotification('Please select at least one branch location', 'error');
                    return;
                }

                // Konversi locations menjadi string
                const locationIds = this.form.locations.map(loc => loc.toString());
                console.log('📍 Processed locations:', locationIds);

                // Validasi dates
                if (!this.form.start_date) {
                    this.showNotification('Start date is required', 'error');
                    return;
                }

                if (!this.form.end_date) {
                    this.showNotification('End date is required', 'error');
                    return;
                }

                // Validasi conditional untuk discount type
                if (this.form.promo_type_id == 2) { // Jika discount type
                    if (!this.form.service_types || this.form.service_types.length === 0) {
                        this.showNotification('Service types are required for discount promotions', 'error');
                        return;
                    }

                    if (!this.form.discount_type) {
                        this.showNotification('Discount type is required for discount promotions', 'error');
                        return;
                    }

                    if (!this.form.discount_amount || this.form.discount_amount <= 0) {
                        this.showNotification('Discount amount is required for discount promotions', 'error');
                        return;
                    }

                    // Validasi percentage discount
                    if (this.form.discount_type === 'percentage' && this.form.discount_amount > 100) {
                        this.showNotification('Percentage discount cannot exceed 100%', 'error');
                        return;
                    }
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                
                console.log('🔐 CSRF Token:', csrfToken ? 'Exists' : 'Missing');

                // Tentukan endpoint dan method - GUNAKAN METHOD SPOOFING
                let endpoint, method;
                
                if (this.modals.createEdit.isEdit && this.editingBannerId) {
                    // Untuk update: POST dengan _method=PUT
                    endpoint = `/banners/${this.editingBannerId}`;
                    method = 'POST';
                    console.log('✏️ Update mode - Using POST with _method=PUT');
                } else {
                    // Untuk create: POST biasa
                    endpoint = '/banners';
                    method = 'POST';
                    console.log('🆕 Create mode');
                }

                // Siapkan FormData untuk method spoofing
                const formData = new FormData();
                
                // Field dasar
                formData.append('name', this.form.name.trim());
                formData.append('description', this.form.description || '');
                formData.append('promo_type_id', this.form.promo_type_id.toString());
                formData.append('category_id', this.form.promo_category_id.toString());
                formData.append('status', this.form.status);
                formData.append('start_date', this.form.start_date);
                formData.append('end_date', this.form.end_date);
                formData.append('priority', this.form.priority.toString());

                // Locations
                locationIds.forEach(locationId => {
                    formData.append('locations[]', locationId);
                });

                // Field opsional
                if (this.form.service_types && this.form.service_types.length > 0) {
                    this.form.service_types.forEach(serviceType => {
                        formData.append('service_types[]', serviceType);
                    });
                }

                if (this.form.discount_type) {
                    formData.append('discount_type', this.form.discount_type);
                }
                
                if (this.form.discount_amount) {
                    formData.append('discount_amount', this.form.discount_amount.toString());
                }

                if (this.form.min_transaction) {
                    formData.append('min_transaction', this.form.min_transaction.toString());
                }

                if (this.form.usage_limit !== null && this.form.usage_limit !== '') {
                    formData.append('usage_limit', this.form.usage_limit.toString());
                }

                if (this.form.usage_per_user) {
                    formData.append('usage_per_user', this.form.usage_per_user.toString());
                }

                // Method spoofing untuk update
                if (this.modals.createEdit.isEdit) {
                    formData.append('_method', 'PUT');
                }

                // Image file
                if (this.form.imageFile) {
                    formData.append('image', this.form.imageFile);
                }

                // Debug FormData
                console.log('📦 FormData contents:');
                for (let [key, value] of formData.entries()) {
                    console.log(`${key}:`, value);
                }

                console.log('🚀 Sending to:', endpoint, 'Method:', method);

                const response = await fetch(endpoint, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                        // Jangan set Content-Type untuk FormData
                    },
                    body: formData
                });

                console.log('📡 Response status:', response.status, response.statusText);
                console.log('📡 Response URL:', response.url);
                console.log('📡 Response OK:', response.ok);
                console.log('📡 Response redirected:', response.redirected);

                // Cek content type
                const contentType = response.headers.get('content-type');
                console.log('📡 Content-Type:', contentType);

                // Handle non-JSON responses (redirect, HTML, dll)
                if (!contentType || !contentType.includes('application/json')) {
                    const textResponse = await response.text();
                    console.log('📄 Non-JSON response received');
                    
                    if (response.redirected) {
                        console.log('🔄 Request was redirected to:', response.url);
                    }

                    // Jika response OK (2xx status), anggap berhasil meskipun bukan JSON
                    if (response.ok) {
                        console.log('✅ Operation successful (non-JSON response)');
                        this.showNotification(
                            this.modals.createEdit.isEdit ? 'Banner updated successfully!' : 'Banner created successfully!', 
                            'success'
                        );
                        this.closeCreateEditModal();
                        this.resetForm();
                        await this.loadBanners();
                        return;
                    }
                    
                    // Jika bukan response OK, handle error
                    if (response.status === 419) {
                        this.showNotification('Session expired. Please refresh the page.', 'error');
                    } else if (response.status === 404) {
                        this.showNotification('Endpoint not found. Please check the URL.', 'error');
                    } else if (response.status === 500) {
                        this.showNotification('Server error. Please try again later.', 'error');
                    } else {
                        this.showNotification(`Server returned ${response.status}. Please check console for details.`, 'error');
                    }
                    return;
                }

                // Process JSON response
                const result = await response.json();
                console.log('📦 JSON Response:', result);

                if (response.ok && result.success) {
                    console.log('✅ Banner operation successful:', result);
                    
                    const successMessage = this.modals.createEdit.isEdit 
                        ? 'Banner updated successfully!' 
                        : 'Banner created successfully!';
                        
                    this.showNotification(successMessage, 'success');
                    this.closeCreateEditModal();
                    this.resetForm();
                    
                    // Refresh data dari API
                    await this.loadBanners();
                } else {
                    console.error('❌ Backend Error:', result);
                    
                    let errorMessage = result.message || 'Operation failed';
                    if (result.errors) {
                        console.log('🔍 Validation errors:', result.errors);
                        const errorDetails = Object.values(result.errors).flat().join(', ');
                        errorMessage += ': ' + errorDetails;
                        
                        // Debug tambahan untuk locations
                        console.log('💡 Locations debug:', {
                            original: this.form.locations,
                            processed: locationIds,
                            type: typeof locationIds[0],
                            sample: locationIds[0]
                        });
                    }
                    
                    this.showNotification(errorMessage, 'error');
                }

            } catch (error) {
                console.error('❌ Network error:', error);
                console.error('💡 Error details:', {
                    name: error.name,
                    message: error.message
                });
                
                if (error.name === 'TypeError' && error.message.includes('Unexpected token')) {
                    this.showNotification('Server returned invalid response. Please check if the endpoint exists.', 'error');
                } else {
                    this.showNotification('Network error: ' + error.message, 'error');
                }
            } finally {
                this.form.loading = false;
            }
        },

        showNotification(message, type = 'success') {
            // Simple notification
            alert(`${type.toUpperCase()}: ${message}`);
        },

        // Update handleImageUpload untuk capture file
        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.form.imageFile = file;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.form.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        
        async deleteBanner() {
            console.log('🔥 Deleting banner:', this.modals.delete.banner?.name);
            this.modals.delete.loading = true;
            
            try {
                const bannerId = this.modals.delete.banner.id;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                
                console.log('🗑️ Deleting banner ID:', bannerId);
                
                // GUNAKAN METHOD SPOOFING: POST dengan _method=DELETE
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                
                const response = await fetch(`/banners/${bannerId}`, {
                    method: 'POST', // Gunakan POST dengan method spoofing
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                console.log('📡 Delete response status:', response.status);

                // Handle redirect responses (karena backend mengembalikan redirect)
                if (response.redirected || response.ok) {
                    console.log('✅ Delete successful');
                    
                    // Hapus dari array frontend
                    this.banners = this.banners.filter(b => b.id !== bannerId);
                    
                    this.showNotification('Banner deleted successfully!', 'success');
                    this.closeDeleteModal();
                    
                    // Refresh data dari API
                    await this.loadBanners();
                } else {
                    const result = await response.json().catch(() => null);
                    console.error('❌ Delete failed:', result);
                    this.showNotification(result?.message || 'Failed to delete banner', 'error');
                }
                
            } catch (error) {
                console.error('❌ Delete error:', error);
                this.showNotification('Error during delete: ' + error.message, 'error');
            } finally {
                this.modals.delete.loading = false;
            }
        },
        
        // Utility Methods
        getStatusText(status) {
            const statusMap = {
                'active': 'Active',
                'inactive': 'Inactive', 
                'ended': 'Expired',
                'draft': 'Draft',
                'upcoming': 'Upcoming'
            };
            return statusMap[status] || status;
        },
        
        getStatusClass(status) {
            const classMap = {
                'active': 'bg-green-100 text-green-800',
                'inactive': 'bg-gray-100 text-gray-800',
                'ended': 'bg-red-100 text-red-800',
                'draft': 'bg-yellow-100 text-yellow-800',
                'upcoming': 'bg-blue-100 text-blue-800'
            };
            return classMap[status] || 'bg-gray-100 text-gray-800';
        },
        
        formatLocations(locations) {
            if (!locations || locations.length === 0) return 'No location';
            
            return locations.map(loc => {
                const locationObj = this.availableLocations.find(al => al.code === loc || al.id === loc);
                return locationObj ? locationObj.name : loc;
            }).join(', ');
        },
        
        formatDateForInput(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toISOString().split('T')[0];
        },
        
        handleImageError(event) {
            console.log('🖼️ Image failed to load, using placeholder');
            event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+CiAgPHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkJhbm5lciBJbWFnZTwvdGV4dD4KPC9zdmc+';
        },

        // Di dalam function bannerManagement(), tambahkan:
        getSelectedLocationNames() {
            if (!this.form.locations || this.form.locations.length === 0) return 'None';
            
            return this.form.locations.map(locId => {
                const location = this.availableLocations.find(l => l.id == locId);
                return location ? location.name : `Location ${locId}`;
            }).join(', ');
        },

        // Method untuk remove image (jika belum ada)
        removeImage() {
            this.form.imagePreview = null;
            this.form.imageFile = null;
            document.getElementById('bannerImage').value = '';
        },

        // Tambahkan fungsi ini di bagian Alpine data atau methods
        formatDateOnly(dateString) {
            if (!dateString) return '-';
            
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit'
                });
            } catch (error) {
                return dateString;
            }
        },

        // Di Alpine.js data, tambahkan function
        getBannerImageUrl(banner) {
            if (!banner?.image_url) {
                return 'https://placehold.co/100x50/3B82F6/ffffff?text=Banner';
            }
            
            if (banner.image_url.startsWith('/storage/') || banner.image_url.startsWith('http')) {
                return banner.image_url;
            }
            
            return '/storage/' + banner.image_url;
        }
    }
}
</script>
@endpush
@endsection