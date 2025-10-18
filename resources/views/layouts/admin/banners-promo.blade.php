@extends('layouts.admin')

@section('title', 'Banner Promo Management')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Banner Promo Management</h1>
                <p class="text-gray-600">Upload and manage promotional banners for customer pages</p>
            </div>
            <button id="uploadBannerBtn" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center gap-2">
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
                    <p class="text-2xl font-bold text-gray-900">12</p>
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
                    <p class="text-2xl font-bold text-green-600">8</p>
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
                    <p class="text-2xl font-bold text-gray-600">2</p>
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
                    <p class="text-2xl font-bold text-red-600">2</p>
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
                    <input type="text" placeholder="Search by title..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Filter by Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Banner Type</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Types</option>
                    <option value="hero">Hero Banner</option>
                    <option value="section">Section Banner</option>
                    <option value="popup">Popup Banner</option>
                </select>
            </div>

            <!-- Filter by Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
        </div>

        <!-- Filter Buttons -->
        <div class="flex flex-wrap gap-3 mt-4">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Apply Filter
            </button>
            <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                Reset Filter
            </button>
        </div>
    </div>

    <!-- Banner Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Banner Card 1 - Active -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <!-- Banner Image -->
            <div class="relative h-48 bg-gray-200">
                <img src="https://via.placeholder.com/600x300/3B82F6/ffffff?text=Summer+Promo" alt="Banner" class="w-full h-full object-cover">
                <div class="absolute top-3 right-3">
                    <span class="px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">Active</span>
                </div>
                <div class="absolute top-3 left-3">
                    <span class="px-3 py-1 bg-white text-gray-700 text-xs font-semibold rounded-full">Hero Banner</span>
                </div>
            </div>

            <!-- Banner Info -->
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Summer Discount 50%</h3>
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">Special promotion for summer season with amazing discounts</p>
                
                <div class="space-y-2 mb-4">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Homepage</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Oct 1 - Oct 31, 2025</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <button class="flex-1 px-3 py-2 bg-blue-100 text-blue-700 text-sm rounded-lg hover:bg-blue-200 transition-colors font-medium flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Preview
                    </button>
                    <button class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors font-medium flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </button>
                    <button class="px-3 py-2 bg-red-100 text-red-700 text-sm rounded-lg hover:bg-red-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Banner Card 2 - Inactive -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow opacity-75">
            <!-- Banner Image -->
            <div class="relative h-48 bg-gray-200">
                <img src="https://via.placeholder.com/600x300/10B981/ffffff?text=New+Year+Offer" alt="Banner" class="w-full h-full object-cover">
                <div class="absolute top-3 right-3">
                    <span class="px-3 py-1 bg-gray-500 text-white text-xs font-semibold rounded-full">Inactive</span>
                </div>
                <div class="absolute top-3 left-3">
                    <span class="px-3 py-1 bg-white text-gray-700 text-xs font-semibold rounded-full">Section Banner</span>
                </div>
            </div>

            <!-- Banner Info -->
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">New Year Special Offer</h3>
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">Celebrate new year with our exclusive package deals</p>
                
                <div class="space-y-2 mb-4">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Booking Page</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Dec 25 - Jan 5, 2026</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <button class="flex-1 px-3 py-2 bg-blue-100 text-blue-700 text-sm rounded-lg hover:bg-blue-200 transition-colors font-medium flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Preview
                    </button>
                    <button class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors font-medium flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </button>
                    <button class="px-3 py-2 bg-red-100 text-red-700 text-sm rounded-lg hover:bg-red-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Banner Card 3 - Expired -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow opacity-60">
            <!-- Banner Image -->
            <div class="relative h-48 bg-gray-200">
                <img src="https://via.placeholder.com/600x300/EF4444/ffffff?text=Flash+Sale" alt="Banner" class="w-full h-full object-cover">
                <div class="absolute top-3 right-3">
                    <span class="px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded-full">Expired</span>
                </div>
                <div class="absolute top-3 left-3">
                    <span class="px-3 py-1 bg-white text-gray-700 text-xs font-semibold rounded-full">Popup Banner</span>
                </div>
            </div>

            <!-- Banner Info -->
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Flash Sale Weekend</h3>
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">Limited time offer for weekend bookings only</p>
                
                <div class="space-y-2 mb-4">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>All Pages</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Sep 1 - Sep 30, 2025</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <button class="flex-1 px-3 py-2 bg-blue-100 text-blue-700 text-sm rounded-lg hover:bg-blue-200 transition-colors font-medium flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Preview
                    </button>
                    <button class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors font-medium flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </button>
                    <button class="px-3 py-2 bg-red-100 text-red-700 text-sm rounded-lg hover:bg-red-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Add more banner cards as needed -->

    </div>

    <!-- Pagination -->
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
        <p class="text-sm text-gray-600">Showing 1 to 3 of 12 banners</p>
        <div class="flex gap-2">
            <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 disabled:opacity-50" disabled>
                Previous
            </button>
            <button class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm">1</button>
            <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">2</button>
            <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">3</button>
            <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                Next
            </button>
        </div>
    </div>
</div>

<!-- Upload/Edit Banner Modal -->
<div id="bannerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 p-4 overflow-y-auto">
    <div class="min-h-full flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl max-w-xl w-full my-8">
        <div class="p-6">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Upload New Banner</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form class="space-y-4">
                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Banner Image *</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors cursor-pointer">
                        <input type="file" id="bannerImage" accept="image/*" class="hidden">
                        <label for="bannerImage" class="cursor-pointer">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-sm text-gray-600 mb-1">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-500">PNG, JPG, WebP up to 5MB</p>
                            <p class="text-xs text-gray-500 mt-1">Recommended: 1920x600px (Hero), 800x400px (Section)</p>
                        </label>
                    </div>
                    <!-- Image Preview -->
                    <div id="imagePreview" class="hidden mt-3">
                        <img src="" alt="Preview" class="w-full h-48 object-cover rounded-lg">
                        <button type="button" class="mt-2 text-sm text-red-600 hover:text-red-700">Remove Image</button>
                    </div>
                </div>

                <!-- Banner Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Banner Title *</label>
                    <input type="text" placeholder="Enter banner title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Banner Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                    <textarea rows="3" placeholder="Enter banner description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>

                <!-- Banner Type & Display Location -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Banner Type *</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Type</option>
                            <option value="hero">Hero Banner</option>
                            <option value="section">Section Banner</option>
                            <option value="popup">Popup Banner</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Display Location *</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Location</option>
                            <option value="homepage">Homepage</option>
                            <option value="booking">Booking Page</option>
                            <option value="all">All Pages</option>
                        </select>
                    </div>
                </div>

                <!-- Active Period -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Display Priority -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Display Priority</label>
                    <input type="number" min="1" max="100" value="1" placeholder="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Lower number = Higher priority</p>
                </div>

                <!-- Status Toggle -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Banner Status</p>
                        <p class="text-xs text-gray-500">Enable or disable this banner</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- Form Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="button" id="cancelBtn" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Upload Banner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
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
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-3">
                    <img src="https://via.placeholder.com/100x50/3B82F6/ffffff?text=Banner" alt="Banner" class="w-20 h-10 object-cover rounded">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Summer Discount 50%</p>
                        <p class="text-xs text-gray-600">Hero Banner • Homepage</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button id="cancelDelete" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Cancel
                </button>
                <button class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    Delete Banner
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Preview Banner Modal -->
<div id="previewModal" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4">
    <div class="relative max-w-6xl w-full">
        <!-- Close Button -->
        <button id="closePreview" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Banner Preview -->
        <div class="bg-white rounded-lg overflow-hidden">
            <img src="https://via.placeholder.com/1920x600/3B82F6/ffffff?text=Banner+Preview" alt="Banner Preview" class="w-full h-auto">
        </div>

        <!-- Banner Info -->
        <div class="bg-white rounded-lg p-4 mt-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Summer Discount 50%</h3>
            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">Hero Banner</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Homepage</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Oct 1 - Oct 31, 2025</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">Active</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Upload Banner Button
        const uploadBtn = document.getElementById('uploadBannerBtn');
        const bannerModal = document.getElementById('bannerModal');
        const closeModal = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelBtn');

        uploadBtn?.addEventListener('click', () => {
            bannerModal?.classList.remove('hidden');
        });

        closeModal?.addEventListener('click', () => {
            bannerModal?.classList.add('hidden');
        });

        cancelBtn?.addEventListener('click', () => {
            bannerModal?.classList.add('hidden');
        });

        // Delete Modal
        const deleteModal = document.getElementById('deleteModal');
        const cancelDelete = document.getElementById('cancelDelete');

        cancelDelete?.addEventListener('click', () => {
            deleteModal?.classList.add('hidden');
        });

        // Preview Modal
        const previewModal = document.getElementById('previewModal');
        const closePreview = document.getElementById('closePreview');

        closePreview?.addEventListener('click', () => {
            previewModal?.classList.add('hidden');
        });

        // Image Upload Preview
        const bannerImage = document.getElementById('bannerImage');
        const imagePreview = document.getElementById('imagePreview');

        bannerImage?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = imagePreview.querySelector('img');
                    img.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        // Close modal when clicking outside
        bannerModal?.addEventListener('click', function(e) {
            if (e.target === bannerModal) {
                bannerModal.classList.add('hidden');
            }
        });

        deleteModal?.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                deleteModal.classList.add('hidden');
            }
        });

        previewModal?.addEventListener('click', function(e) {
            if (e.target === previewModal) {
                previewModal.classList.add('hidden');
            }
        });
    });
</script>
@endpush