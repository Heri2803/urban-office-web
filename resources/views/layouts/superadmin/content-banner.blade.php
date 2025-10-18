@extends('layouts.superadmin')

@section('content')
<div class="min-h-screen bg-gray-50 p-4 md:p-6 lg:p-8">
    
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">🎨 Banner Promo</h1>
                <p class="text-sm md:text-base text-gray-600 mt-1">Manage promotional banners for all branches</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 md:gap-3">
                <button onclick="refreshPage()" class="flex items-center justify-center gap-2 px-4 py-2 md:py-2.5 bg-gray-200 text-gray-800 rounded-lg text-sm font-medium hover:bg-gray-300 transition">
                    🔄 Refresh
                </button>
                <button onclick="openCreateModal()" class="flex items-center justify-center gap-2 px-4 py-2 md:py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    ➕ Create New Banner
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-5 border-l-4 border-blue-500">
            <div class="text-gray-600 text-xs md:text-sm font-medium">Total Banners</div>
            <div class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">24</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-5 border-l-4 border-green-500">
            <div class="text-gray-600 text-xs md:text-sm font-medium">Active</div>
            <div class="text-2xl md:text-3xl font-bold text-green-600 mt-2">8</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-5 border-l-4 border-yellow-500">
            <div class="text-gray-600 text-xs md:text-sm font-medium">Scheduled</div>
            <div class="text-2xl md:text-3xl font-bold text-yellow-600 mt-2">5</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-5 border-l-4 border-gray-500">
            <div class="text-gray-600 text-xs md:text-sm font-medium">Archived</div>
            <div class="text-2xl md:text-3xl font-bold text-gray-600 mt-2">11</div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-6">
        <h2 class="text-base md:text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <span>🔎</span> Search & Filter
        </h2>
        
        <form id="filterForm" class="space-y-4" onsubmit="event.preventDefault(); applyFilters();">
            
            <!-- Search Box -->
            <div>
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">Search Banner</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input 
                        type="text" 
                        id="searchInput" 
                        placeholder="Search by banner name..." 
                        class="w-full pl-10 pr-4 py-2 md:py-2.5 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                </div>
            </div>

            <!-- Filter Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
                
                <!-- Status Filter -->
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="statusFilter" class="w-full px-3 md:px-4 py-2 md:py-2.5 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="draft">Draft</option>
                        <option value="archived">Archived</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>

                <!-- Branch Filter -->
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">Branch</label>
                    <select id="branchFilter" class="w-full px-3 md:px-4 py-2 md:py-2.5 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="">All Branches</option>
                        <option value="surabaya">Surabaya</option>
                        <option value="jakarta">Jakarta</option>
                        <option value="bandung">Bandung</option>
                        <option value="medan">Medan</option>
                    </select>
                </div>

                <!-- Sort Filter -->
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select id="sortFilter" class="w-full px-3 md:px-4 py-2 md:py-2.5 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="latest">Latest</option>
                        <option value="oldest">Oldest</option>
                        <option value="name-asc">Name (A-Z)</option>
                        <option value="name-desc">Name (Z-A)</option>
                        <option value="most-viewed">Most Viewed</option>
                    </select>
                </div>

            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-2 md:gap-3 pt-4 border-t">
                <button 
                    type="reset" 
                    class="flex-1 sm:flex-auto px-4 py-2 md:py-2.5 bg-gray-200 text-gray-800 rounded-lg text-xs md:text-sm font-medium hover:bg-gray-300 transition duration-200"
                >
                    🔄 Reset
                </button>
                <button 
                    type="submit" 
                    class="flex-1 sm:flex-auto px-4 py-2 md:py-2.5 bg-blue-600 text-white rounded-lg text-xs md:text-sm font-medium hover:bg-blue-700 transition duration-200"
                >
                    🔍 Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Banner Grid Cards View -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        
        @php
            $banners = [
                [
                    'id' => 1,
                    'name' => 'Diskon Khusus Meeting Room 50%',
                    'description' => 'Diskon hingga 50% untuk booking meeting room',
                    'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1920&h=480&fit=crop',
                    'status' => 'active',
                    'status_label' => 'Active',
                    'start_date' => '5 Jan 2024',
                    'end_date' => '5 Feb 2024',
                    'branches' => 'All Branches',
                    'views' => 2456,
                    'clicks' => 345
                ],
                [
                    'id' => 2,
                    'name' => 'WiFi Gratis Untuk Semua',
                    'description' => 'WiFi gratis sepanjang waktu untuk semua layanan',
                    'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1920&h=480&fit=crop',
                    'status' => 'scheduled',
                    'status_label' => 'Scheduled',
                    'start_date' => '10 Jan 2024',
                    'end_date' => '28 Feb 2024',
                    'branches' => 'Jakarta, Bandung',
                    'views' => 0,
                    'clicks' => 0
                ],
                [
                    'id' => 3,
                    'name' => 'Promo Bundle Meeting Room + Catering',
                    'description' => 'Paket hemat meeting room dengan catering included',
                    'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1920&h=480&fit=crop',
                    'status' => 'draft',
                    'status_label' => 'Draft',
                    'start_date' => '1 Feb 2024',
                    'end_date' => '28 Feb 2024',
                    'branches' => 'All Branches',
                ],
                [
                    'id' => 4,
                    'name' => 'Private Office Special Discount',
                    'description' => 'Harga spesial untuk private office 3 bulan',
                    'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1920&h=480&fit=crop',
                    'status' => 'active',
                    'status_label' => 'Active',
                    'start_date' => '1 Jan 2024',
                    'end_date' => '31 Jan 2024',
                    'branches' => 'Surabaya',
                ],
                [
                    'id' => 5,
                    'name' => 'Coworking Space Flash Sale',
                    'description' => 'Flash sale coworking space hanya hari ini',
                    'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1920&h=480&fit=crop',
                    'status' => 'expired',
                    'status_label' => 'Expired',
                    'start_date' => '15 Dec 2023',
                    'end_date' => '20 Dec 2023',
                    'branches' => 'All Branches',
                ],
                [
                    'id' => 6,
                    'name' => 'Event Space Promo Tahun Baru',
                    'description' => 'Diskon spesial untuk event space tahun baru',
                    'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1920&h=480&fit=crop',
                    'status' => 'archived',
                    'status_label' => 'Archived',
                    'start_date' => '1 Dec 2023',
                    'end_date' => '31 Dec 2023',
                    'branches' => 'Medan',
                ],
            ];
        @endphp

        @foreach($banners as $banner)
        <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition duration-200 group">
            
            <!-- Banner Image -->
            <div class="relative overflow-hidden bg-gray-200 h-40 md:h-48">
                <img 
                    src="{{ $banner['image'] }}" 
                    alt="{{ $banner['name'] }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                />
                
                <!-- Status Badge -->
                <div class="absolute top-3 right-3">
                    @if($banner['status'] === 'active')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">✅ Active</span>
                    @elseif($banner['status'] === 'scheduled')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">🔔 Scheduled</span>
                    @elseif($banner['status'] === 'draft')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">📝 Draft</span>
                    @elseif($banner['status'] === 'expired')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">⏰ Expired</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">📦 Archived</span>
                    @endif
                </div>
            </div>

            <!-- Banner Content -->
            <div class="p-4 md:p-5">
                
                <!-- Banner Title -->
                <h3 class="text-sm md:text-base font-semibold text-gray-900 line-clamp-2 mb-2">
                    {{ $banner['name'] }}
                </h3>

                <!-- Banner Description -->
                <p class="text-xs md:text-sm text-gray-600 line-clamp-2 mb-3">
                    {{ $banner['description'] }}
                </p>

                <!-- Display Period -->
                <div class="flex items-center gap-1 text-xs md:text-sm text-gray-700 mb-2">
                    <span>📅</span>
                    <span class="font-medium">{{ $banner['start_date'] }} - {{ $banner['end_date'] }}</span>
                </div>

                <!-- Branches -->
                <div class="flex items-center gap-1 text-xs md:text-sm text-gray-700 mb-3">
                    <span>📍</span>
                    <span>{{ $banner['branches'] }}</span>
                </div>

                <!-- Quick Actions -->
                <div class="flex gap-2 flex-col sm:flex-row">
                    <button 
                        onclick="previewBanner({{ $banner['id'] }})"
                        class="flex-1 px-3 py-2 text-xs md:text-sm bg-blue-50 text-blue-600 rounded-lg font-medium hover:bg-blue-100 transition"
                    >
                        👁️ Preview
                    </button>
                    <button 
                        onclick="editBanner({{ $banner['id'] }})"
                        class="flex-1 px-3 py-2 text-xs md:text-sm bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition"
                    >
                        ✏️ Edit
                    </button>
                    <button 
                        onclick="deleteBanner({{ $banner['id'] }})"
                        class="flex-1 px-3 py-2 text-xs md:text-sm bg-red-50 text-red-600 rounded-lg font-medium hover:bg-red-100 transition"
                    >
                        🗑️ Delete
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-center">
        <nav class="flex gap-2">
            <button class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition disabled:opacity-50" disabled>&lt; Previous</button>
            <button class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">1</button>
            <button class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition">2</button>
            <button class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition">3</button>
            <button class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition">Next &gt;</button>
        </nav>
    </div>

</div>

<!-- Create/Edit Banner Modal -->
<div id="bannerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg md:text-xl font-bold text-gray-900" id="modalTitle">Create New Banner</h2>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>

        <!-- Modal Content -->
        <div class="p-4 md:p-6 space-y-6">
            
            <!-- Banner Name -->
            <div>
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">Banner Name/Title *</label>
                <input 
                    type="text" 
                    id="bannerName"
                    placeholder="e.g., Diskon Khusus Meeting Room 50%"
                    class="w-full px-3 md:px-4 py-2 md:py-2.5 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <!-- Banner Description -->
            <div>
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea 
                    id="bannerDescription"
                    placeholder="Enter banner description..."
                    rows="3"
                    class="w-full px-3 md:px-4 py-2 md:py-2.5 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                ></textarea>
            </div>

            <!-- Image Upload -->
            <div>
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">Upload Banner Image *</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition cursor-pointer" onclick="document.getElementById('imageInput').click()">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-12L28 20m0 0l-4-4m4 4l4-4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <p class="mt-2 text-xs md:text-sm text-gray-600"><span class="font-medium text-blue-600">Click to upload</span> or drag and drop</p>
                    <p class="text-xs text-gray-500 mt-1">JPG, PNG, WebP up to 5MB (Recommended: 1920x480px)</p>
                </div>
                <input type="file" id="imageInput" class="hidden" accept="image/*" onchange="previewImage(event)">
                <div id="imagePreview" class="mt-4 hidden">
                    <img id="previewImg" class="w-full rounded-lg max-h-48 object-cover" alt="Preview">
                    <button type="button" onclick="removeImage()" class="mt-2 text-xs text-red-600 hover:text-red-800 font-medium">Remove Image</button>
                </div>
            </div>

            <!-- Date & Time -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                    <input 
                        type="date" 
                        id="startDate"
                        class="w-full px-3 md:px-4 py-2 md:py-2.5 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">End Date *</label>
                    <input 
                        type="date" 
                        id="endDate"
                        class="w-full px-3 md:px-4 py-2 md:py-2.5 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-3">Status</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="status" value="active" class="w-4 h-4 text-blue-600">
                        <span class="text-sm text-gray-700">Active (Publish sekarang)</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="status" value="scheduled" class="w-4 h-4 text-blue-600">
                        <span class="text-sm text-gray-700">Scheduled (Publish otomatis saat start date)</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="status" value="draft" class="w-4 h-4 text-blue-600" checked>
                        <span class="text-sm text-gray-700">Draft (Simpan tapi jangan publish)</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="sticky bottom-0 bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-t border-gray-200 flex flex-col sm:flex-row gap-2 sm:gap-3 justify-end">
            <button 
                onclick="closeModal()"
                class="flex-1 sm:flex-auto px-4 py-2 md:py-2.5 bg-gray-300 text-gray-800 rounded-lg text-xs md:text-sm font-medium hover:bg-gray-400 transition"
            >
                Cancel
            </button>
            <button 
                onclick="saveBanner()"
                class="flex-1 sm:flex-auto px-4 py-2 md:py-2.5 bg-blue-600 text-white rounded-lg text-xs md:text-sm font-medium hover:bg-blue-700 transition"
            >
                Publish Banner
            </button>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg md:text-xl font-bold text-gray-900">Banner Preview</h2>
            <button onclick="closePreviewModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>

        <!-- Modal Content -->
        <div class="p-4 md:p-6 space-y-6">
            
            <!-- Desktop Preview -->
            <div>
                <h3 class="text-sm md:text-base font-semibold text-gray-900 mb-3">Desktop Preview</h3>
                <div class="bg-gray-100 rounded-lg overflow-hidden">
                    <img id="previewImageDesktop" src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=1920&h=480&fit=crop" class="w-full" alt="Banner">
                </div>
            </div>

            <!-- Tablet Preview -->
            <div>
                <h3 class="text-sm md:text-base font-semibold text-gray-900 mb-3">Tablet Preview</h3>
                <div class="bg-gray-100 rounded-lg overflow-hidden max-w-md">
                    <img id="previewImageTablet" src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=1024&h=256&fit=crop" class="w-full" alt="Banner">
                </div>
            </div>

            <!-- Mobile Preview -->
            <div>
                <h3 class="text-sm md:text-base font-semibold text-gray-900 mb-3">Mobile Preview</h3>
                <div class="bg-gray-100 rounded-lg overflow-hidden max-w-xs">
                    <img id="previewImageMobile" src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=360&h=180&fit=crop" class="w-full" alt="Banner">
                </div>
            </div>

            <!-- Banner Info -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Banner Information</h4>
                <div class="space-y-2 text-xs md:text-sm text-gray-700">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span id="previewStatus" class="font-semibold">Active</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Period:</span>
                        <span id="previewPeriod" class="font-semibold">5 Jan 2024 - 5 Feb 2024</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Applies to:</span>
                        <span id="previewBranches" class="font-semibold">All Branches</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="sticky bottom-0 bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-t border-gray-200 flex justify-end">
            <button 
                onclick="closePreviewModal()"
                class="px-4 py-2 md:py-2.5 bg-gray-300 text-gray-800 rounded-lg text-xs md:text-sm font-medium hover:bg-gray-400 transition"
            >
                Close
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full">
        
        <!-- Modal Header -->
        <div class="bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
            <h2 class="text-lg md:text-xl font-bold text-gray-900">⚠️ Delete Banner</h2>
        </div>

        <!-- Modal Content -->
        <div class="p-4 md:p-6">
            <p class="text-sm md:text-base text-gray-700 mb-4">
                Are you sure you want to delete this banner?
            </p>
            <p id="deleteBannerName" class="text-sm md:text-base font-semibold text-gray-900 mb-4 p-3 bg-gray-100 rounded-lg">
                
            </p>
            <p class="text-xs md:text-sm text-gray-600">
                This action cannot be undone. The banner will be moved to archive.
            </p>
        </div>

        <!-- Modal Footer -->
        <div class="bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-t border-gray-200 flex gap-3 justify-end">
            <button 
                onclick="closeDeleteModal()"
                class="px-4 py-2 md:py-2.5 bg-gray-300 text-gray-800 rounded-lg text-xs md:text-sm font-medium hover:bg-gray-400 transition"
            >
                Cancel
            </button>
            <button 
                onclick="confirmDelete()"
                class="px-4 py-2 md:py-2.5 bg-red-600 text-white rounded-lg text-xs md:text-sm font-medium hover:bg-red-700 transition"
            >
                Delete
            </button>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    let currentBannerId = null;

    // Modal Functions
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Create New Banner';
        document.getElementById('bannerName').value = '';
        document.getElementById('bannerDescription').value = '';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.querySelector('input[name="status"][value="draft"]').checked = true;
        document.getElementById('imagePreview').classList.add('hidden');
        document.getElementById('bannerModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function editBanner(id) {
        currentBannerId = id;
        document.getElementById('modalTitle').textContent = 'Edit Banner';
        // In real app, fetch banner data and populate form
        document.getElementById('bannerModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('bannerModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function previewBanner(id) {
        // In real app, fetch banner data
        document.getElementById('previewModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closePreviewModal() {
        document.getElementById('previewModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function deleteBanner(id) {
        currentBannerId = id;
        document.getElementById('deleteBannerName').textContent = '"Diskon Khusus Meeting Room 50%"';
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function confirmDelete() {
        alert('Banner deleted successfully!');
        closeDeleteModal();
        // In real app, make API call to delete
    }

    // Image Upload Functions
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('imagePreview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    function removeImage() {
        document.getElementById('imageInput').value = '';
        document.getElementById('imagePreview').classList.add('hidden');
    }

    // Form Functions
    function saveBanner() {
        const name = document.getElementById('bannerName').value;
        const description = document.getElementById('bannerDescription').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const status = document.querySelector('input[name="status"]:checked').value;

        if (!name || !startDate || !endDate) {
            alert('Please fill in all required fields!');
            return;
        }

        alert(`Banner ${currentBannerId ? 'updated' : 'created'} successfully!\n\nName: ${name}\nStatus: ${status}`);
        closeModal();
        // In real app, make API call to save
    }

    // Filter Functions
    function applyFilters() {
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;
        const branch = document.getElementById('branchFilter').value;
        const sort = document.getElementById('sortFilter').value;

        console.log('Filters Applied:', { search, status, branch, sort });
        // In real app, make API call with filters
    }

    // Utility Functions
    function refreshPage() {
        alert('Page refreshed!');
        // In real app, reload data
    }

    // Close modal when clicking outside
    document.getElementById('bannerModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    document.getElementById('previewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePreviewModal();
        }
    });

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
            closePreviewModal();
            closeDeleteModal();
        }
    });

    // Real-time search
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            console.log('Searching for:', this.value);
        }, 500);
    });
</script>
@endsection