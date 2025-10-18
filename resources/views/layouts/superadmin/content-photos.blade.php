@extends('layouts.superadmin')

@section('title', 'Service Photos')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                    <span>Content Management</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-gray-900 font-medium">Service Photos</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Service Photos (Global)</h1>
                <p class="text-sm text-gray-600 mt-1">Manage service photos for all branches</p>
            </div>
            <div>
                <button onclick="openUploadModal()" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition w-full lg:w-auto">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Upload Photos
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Total Photos</p>
                    <h3 class="text-2xl font-bold text-gray-800">156</h3>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Active</p>
                    <h3 class="text-2xl font-bold text-green-600">142</h3>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Archived</p>
                    <h3 class="text-2xl font-bold text-gray-600">14</h3>
                </div>
                <div class="bg-gray-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Used by Branches</p>
                    <h3 class="text-2xl font-bold text-blue-600">12/15</h3>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Type Tabs -->
    <div class="bg-white rounded-t-lg shadow-sm border border-b-0 border-gray-200 overflow-x-auto">
        <div class="flex min-w-max md:min-w-0">
            <button onclick="switchServiceTab('meeting')" id="tab-meeting" class="service-tab active flex-1 md:flex-none px-4 md:px-6 py-4 text-sm font-medium border-b-2 border-blue-600 text-blue-600 hover:bg-gray-50 transition whitespace-nowrap">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Meeting Room
                <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full text-xs">42</span>
            </button>
            <button onclick="switchServiceTab('private')" id="tab-private" class="service-tab flex-1 md:flex-none px-4 md:px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition whitespace-nowrap">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Private Office
                <span class="ml-2 px-2 py-0.5 bg-gray-200 text-gray-800 rounded-full text-xs">28</span>
            </button>
            <button onclick="switchServiceTab('sharing')" id="tab-sharing" class="service-tab flex-1 md:flex-none px-4 md:px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition whitespace-nowrap">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Sharing Room
                <span class="ml-2 px-2 py-0.5 bg-gray-200 text-gray-800 rounded-full text-xs">18</span>
            </button>
            <button onclick="switchServiceTab('coworking')" id="tab-coworking" class="service-tab flex-1 md:flex-none px-4 md:px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition whitespace-nowrap">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Coworking
                <span class="ml-2 px-2 py-0.5 bg-gray-200 text-gray-800 rounded-full text-xs">35</span>
            </button>
            <button onclick="switchServiceTab('virtual')" id="tab-virtual" class="service-tab flex-1 md:flex-none px-4 md:px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition whitespace-nowrap">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Virtual Office
                <span class="ml-2 px-2 py-0.5 bg-gray-200 text-gray-800 rounded-full text-xs">11</span>
            </button>
            <button onclick="switchServiceTab('event')" id="tab-event" class="service-tab flex-1 md:flex-none px-4 md:px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition whitespace-nowrap">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
                Event Space
                <span class="ml-2 px-2 py-0.5 bg-gray-200 text-gray-800 rounded-full text-xs">22</span>
            </button>
        </div>
    </div>

    <!-- Filters & View Toggle -->
    <div class="bg-white border-x border-gray-200 p-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <div class="inline-flex rounded-lg border border-gray-300 p-1 bg-gray-50">
                    <button onclick="switchView('grid')" id="view-grid" class="view-btn active px-3 py-1.5 text-sm font-medium rounded-md transition">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        Grid
                    </button>
                    <button onclick="switchView('list')" id="view-list" class="view-btn px-3 py-1.5 text-sm font-medium rounded-md transition">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        List
                    </button>
                </div>

                <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option>All Status</option>
                    <option>Active</option>
                    <option>Archived</option>
                </select>

                <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option>All Branches</option>
                    <option>Used</option>
                    <option>Unused</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option>Latest Upload</option>
                    <option>Oldest Upload</option>
                    <option>Most Used</option>
                    <option>File Size</option>
                    <option>Filename A-Z</option>
                </select>
            </div>
        </div>

        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" placeholder="Search photos by filename..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>

    <!-- Photo Grid View -->
    <div id="grid-view" class="bg-white rounded-b-lg border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Photo Card 1 -->
            <div class="group bg-white border-2 border-gray-200 rounded-lg overflow-hidden hover:border-blue-500 hover:shadow-lg transition">
                <div class="relative aspect-video bg-gray-100 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=800" alt="Meeting Room" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <div class="absolute top-2 left-2">
                        <input type="checkbox" class="photo-checkbox w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 bg-white shadow-lg">
                    </div>
                    <div class="absolute top-2 right-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-500 text-white shadow-lg">
                            Active
                        </span>
                    </div>
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <button onclick="viewPhoto(1)" class="p-2 bg-white rounded-full hover:bg-gray-100 transition">
                            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-gray-900 mb-1 truncate">meeting-room-001.jpg</h3>
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                        <span>1920 × 1080 px</span>
                        <span>2.4 MB</span>
                    </div>
                    <div class="mb-3">
                        <p class="text-xs text-gray-600 mb-1">Used by 8 branches:</p>
                        <div class="flex flex-wrap gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-800">Surabaya</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-800">Jakarta</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">+6 more</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="viewPhoto(1)" class="flex-1 inline-flex items-center justify-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-200 transition">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View
                        </button>
                        <button onclick="editPhoto(1)" class="flex-1 inline-flex items-center justify-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-xs font-medium hover:bg-blue-200 transition">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                        <button onclick="pushPhoto(1)" class="inline-flex items-center justify-center px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-xs font-medium hover:bg-green-200 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </button>
                        <button onclick="deletePhoto(1)" class="inline-flex items-center justify-center px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-xs font-medium hover:bg-red-200 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Photo Card 2 -->
            <div class="group bg-white border-2 border-gray-200 rounded-lg overflow-hidden hover:border-blue-500 hover:shadow-lg transition">
                <div class="relative aspect-video bg-gray-100 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1497366811353-6870744d04b2?w=800" alt="Meeting Room" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <div class="absolute top-2 left-2">
                        <input type="checkbox" class="photo-checkbox w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 bg-white shadow-lg">
                    </div>
                    <div class="absolute top-2 right-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-500 text-white shadow-lg">
                            Active
                        </span>
                    </div>
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <button onclick="viewPhoto(2)" class="p-2 bg-white rounded-full hover:bg-gray-100 transition">
                            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-gray-900 mb-1 truncate">meeting-room-002.jpg</h3>
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                        <span>1920 × 1080 px</span>
                        <span>3.1 MB</span>
                    </div>
                    <div class="mb-3">
                        <p class="text-xs text-gray-600 mb-1">Used by 5 branches:</p>
                        <div class="flex flex-wrap gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-800">Jakarta</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-800">Bandung</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">+3 more</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="viewPhoto(2)" class="flex-1 inline-flex items-center justify-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-200 transition">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View
                        </button>
                        <button onclick="editPhoto(2)" class="flex-1 inline-flex items-center justify-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-xs font-medium hover:bg-blue-200 transition">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                        <button onclick="pushPhoto(2)" class="inline-flex items-center justify-center px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-xs font-medium hover:bg-green-200 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </button>
                        <button onclick="deletePhoto(2)" class="inline-flex items-center justify-center px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-xs font-medium hover:bg-red-200 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Photo Card 3 -->
            <div class="group bg-white border-2 border-gray-200 rounded-lg overflow-hidden hover:border-blue-500 hover:shadow-lg transition">
                <div class="relative aspect-video bg-gray-100 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1497366754035-f200968a6e72?w=800" alt="Meeting Room" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <div class="absolute top-2 left-2">
                        <input type="checkbox" class="photo-checkbox w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 bg-white shadow-lg">
                    </div>
                    <div class="absolute top-2 right-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-500 text-white shadow-lg">
                            Unused
                        </span>
                    </div>
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <button onclick="viewPhoto(3)" class="p-2 bg-white rounded-full hover:bg-gray-100 transition">
                            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-gray-900 mb-1 truncate">meeting-room-003.jpg</h3>
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                        <span>1920 × 1080 px</span>
                        <span>1.8 MB</span>
                    </div>
                    <div class="mb-3">
                        <p class="text-xs text-gray-500 italic">Not used by any branch</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="viewPhoto(3)" class="flex-1 inline-flex items-center justify-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-200 transition">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View
                        </button>
                        <button onclick="editPhoto(3)" class="flex-1 inline-flex items-center justify-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-xs font-medium hover:bg-blue-200 transition">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                        <button onclick="pushPhoto(3)" class="inline-flex items-center justify-center px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-xs font-medium hover:bg-green-200 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </button>
                        <button onclick="deletePhoto(3)" class="inline-flex items-center justify-center px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-xs font-medium hover:bg-red-200 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Pagination -->
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-700">
                Showing <span class="font-medium">1</span> to <span class="font-medium">3</span> of{' '}
                <span class="font-medium">42</span> photos
            </div>
            <div class="flex items-center gap-2">
                <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50" disabled>
                    Previous
                </button>
                <button class="px-3 py-1 text-sm bg-blue-600 text-white rounded-lg">1</button>
                <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">2</button>
                <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">3</button>
                <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                    Next
                </button>
            </div>
        </div>
    </div>

    <!-- List View (Hidden by default) -->
    <div id="list-view" class="hidden bg-white rounded-b-lg border border-gray-200">
        <div class="divide-y divide-gray-200">
            <!-- List Item 1 -->
            <div class="flex items-center gap-4 p-4 hover:bg-gray-50 transition">
                <input type="checkbox" class="photo-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=200" alt="Meeting Room" class="w-20 h-14 object-cover rounded-lg">
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-gray-900 truncate">meeting-room-001.jpg</h3>
                    <p class="text-xs text-gray-500">1920 × 1080 px • 2.4 MB</p>
                    <p class="text-xs text-gray-600 mt-1">Used by 8 branches</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Active
                </span>
                <div class="flex items-center gap-2">
                    <button onclick="viewPhoto(1)" class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                    <button onclick="editPhoto(1)" class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button onclick="pushPhoto(1)" class="p-2 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </button>
                    <button onclick="deletePhoto(1)" class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Bar (Hidden by default) -->
    <div id="bulk-actions" class="hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white rounded-lg shadow-2xl px-6 py-4 z-50">
        <div class="flex flex-col sm:flex-row items-center gap-4">
            <span class="text-sm font-medium"><span id="selected-count">0</span> photos selected</span>
            <div class="flex items-center gap-3">
                <button onclick="bulkPush()" class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-sm font-medium transition">
                    Push to Branches
                </button>
                <button onclick="bulkArchive()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-sm font-medium transition">
                    Archive
                </button>
                <button onclick="bulkDelete()" class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-sm font-medium transition">
                    Delete
                </button>
                <button onclick="clearSelection()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-sm font-medium transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Upload Photos Modal -->
<div id="upload-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Upload Service Photos</h3>
                <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Type</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center p-3 border-2 border-blue-500 bg-blue-50 rounded-lg cursor-pointer">
                            <input type="radio" name="service_type" checked class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm font-medium text-gray-900">Meeting Room</span>
                        </label>
                        <label class="flex items-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-gray-400">
                            <input type="radio" name="service_type" class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm font-medium text-gray-900">Private Office</span>
                        </label>
                        <label class="flex items-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-gray-400">
                            <input type="radio" name="service_type" class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm font-medium text-gray-900">Sharing Room</span>
                        </label>
                        <label class="flex items-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-gray-400">
                            <input type="radio" name="service_type" class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm font-medium text-gray-900">Coworking</span>
                        </label>
                        <label class="flex items-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-gray-400">
                            <input type="radio" name="service_type" class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm font-medium text-gray-900">Virtual Office</span>
                        </label>
                        <label class="flex items-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-gray-400">
                            <input type="radio" name="service_type" class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm font-medium text-gray-900">Event Space</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Photos</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition cursor-pointer">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Drag & Drop files here</p>
                        <p class="text-sm text-gray-600">or click to browse</p>
                        <p class="mt-2 text-xs text-gray-500">
                            Supported: JPG, PNG, WEBP • Max: 5MB per file • Max: 10 files
                        </p>
                        <input type="file" class="hidden" multiple accept="image/jpeg,image/png,image/webp">
                    </div>
                </div>

                <div id="selected-files" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Selected Files (3)</label>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">meeting-001.jpg</p>
                                    <p class="text-xs text-gray-500">2.4 MB</p>
                                </div>
                            </div>
                            <button class="text-red-600 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Options</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Auto-resize to optimal size (1920px width)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Compress images (recommended)</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Push to Branches (Optional)</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Push to all branches immediately</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Select specific branches</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button onclick="closeUploadModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button onclick="uploadPhotos()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    Upload Photos
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Photo Modal (Lightbox) -->
<div id="view-modal" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4">
    <button onclick="closeViewModal()" class="absolute top-4 right-4 text-white hover:text-gray-300">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <button onclick="prevPhoto()" class="absolute left-4 text-white hover:text-gray-300">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </button>

    <button onclick="nextPhoto()" class="absolute right-4 text-white hover:text-gray-300">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <div class="max-w-6xl w-full">
        <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=1920" alt="Photo" class="w-full h-auto rounded-lg">
        <div class="mt-4 text-center text-white">
            <h3 class="text-lg font-semibold">meeting-room-001.jpg</h3>
            <p class="text-sm text-gray-300">1920 × 1080 px • 2.4 MB • Used by 8 branches • Active</p>
            <div class="mt-4 flex items-center justify-center gap-4">
                <button onclick="downloadPhoto()" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg text-sm font-medium transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download
                </button>
                <button onclick="deleteFromView()" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Photo Modal -->
<div id="edit-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Edit Photo Details</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                    <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=800" alt="Preview" class="w-full h-full object-cover">
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Photo Information</h4>
                    <div class="space-y-1 text-sm text-gray-600">
                        <p>• Filename: meeting-room-001.jpg</p>
                        <p>• Size: 1920 × 1080 px (2.4 MB)</p>
                        <p>• Uploaded: 15 Oct 2025</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Caption / Alt Text</label>
                    <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Modern meeting room with projector and whiteboard...">Modern meeting room with projector and whiteboard</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Type</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option>Meeting Room</option>
                        <option>Private Office</option>
                        <option>Sharing Room</option>
                        <option>Coworking Space</option>
                        <option>Virtual Office</option>
                        <option>Event Space</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="status" checked class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Archived</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Currently Used By</label>
                    <div class="bg-gray-50 rounded-lg p-4 max-h-40 overflow-y-auto">
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Surabaya - Gubeng
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Jakarta - Senayan
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Bandung - Dago
                            </li>
                            <li class="text-gray-500">... +5 more branches</li>
                        </ul>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                        Replace Photo
                    </button>
                    <button class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                        Download Original
                    </button>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button onclick="closeEditModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button onclick="savePhotoEdit()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Push to Branches Modal -->
<div id="push-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Push Photos to Branches</h3>
                <button onclick="closePushModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">Selected Photos (1)</h4>
                    <ul class="text-sm text-blue-800 list-disc list-inside">
                        <li>meeting-room-001.jpg</li>
                    </ul>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Target Branches</label>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        <!-- Mitra Group 1 -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <input type="checkbox" id="mitra-1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="mitra-1" class="ml-3 flex items-center text-sm font-semibold text-gray-900">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    PT Workspace Indonesia (All 3 branches)
                                </label>
                            </div>
                            <div class="ml-6 space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Surabaya - Gubeng</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Surabaya - HR Muhammad</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Sidoarjo - Delta</span>
                                </label>
                            </div>
                        </div>

                        <!-- Mitra Group 2 -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <input type="checkbox" id="mitra-2" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="mitra-2" class="ml-3 flex items-center text-sm font-semibold text-gray-900">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    CV Ruang Kerja (All 2 branches)
                                </label>
                            </div>
                            <div class="ml-6 space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Jakarta - Senayan</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Jakarta - Sudirman</span>
                                </label>
                            </div>
                        </div>

                        <!-- Mitra Group 3 -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <input type="checkbox" id="mitra-3" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="mitra-3" class="ml-3 flex items-center text-sm font-semibold text-gray-900">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    PT Office Hub (All 1 branch)
                                </label>
                            </div>
                            <div class="ml-6 space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Bandung - Dago</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Selected: <span class="text-blue-600 font-semibold">4 branches</span></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Options</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="push_option" class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Replace existing photos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="push_option" checked class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Add to existing photos</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Notify branch admins</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button onclick="closePushModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button onclick="confirmPush()" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">
                    Push to Branches
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Photo?</h3>
            <p class="text-sm text-gray-600 mb-4">This photo is currently used by 8 branches. Deleting will remove it from:</p>
            <div class="bg-gray-50 rounded-lg p-3 mb-6 max-h-32 overflow-y-auto text-left">
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Surabaya - Gubeng</li>
                    <li>• Jakarta - Senayan</li>
                    <li>• Bandung - Dago</li>
                    <li class="text-gray-500">... +5 more branches</li>
                </ul>
            </div>
            <p class="text-sm text-red-600 font-medium mb-6">This action cannot be undone!</p>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button onclick="confirmDelete()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="hidden fixed top-6 right-6 bg-white rounded-lg shadow-lg border border-gray-200 p-4 z-50 max-w-sm">
    <div class="flex items-start gap-3">
        <div id="toast-icon" class="flex-shrink-0"></div>
        <div class="flex-1">
            <h4 id="toast-title" class="text-sm font-semibold text-gray-900 mb-1"></h4>
            <p id="toast-message" class="text-sm text-gray-600"></p>
        </div>
        <button onclick="closeToast()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<script>
// Service Tab Switching
function switchServiceTab(service) {
    const tabs = document.querySelectorAll('.service-tab');
    tabs.forEach(tab => {
        tab.classList.remove('active', 'border-blue-600', 'text-blue-600');
        tab.classList.add('border-transparent', 'text-gray-600');
    });
    
    const activeTab = document.getElementById('tab-' + service);
    activeTab.classList.add('active', 'border-blue-600', 'text-blue-600');
    activeTab.classList.remove('border-transparent', 'text-gray-600');
    
    showToast('Info', 'Loading ' + service + ' photos...', 'info');
}

// View Switching
function switchView(view) {
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');
    const gridBtn = document.getElementById('view-grid');
    const listBtn = document.getElementById('view-list');
    
    if (view === 'grid') {
        gridView.classList.remove('hidden');
        listView.classList.add('hidden');
        gridBtn.classList.add('active', 'bg-blue-600', 'text-white');
        gridBtn.classList.remove('text-gray-600');
        listBtn.classList.remove('active', 'bg-blue-600', 'text-white');
        listBtn.classList.add('text-gray-600');
    } else {
        gridView.classList.add('hidden');
        listView.classList.remove('hidden');
        listBtn.classList.add('active', 'bg-blue-600', 'text-white');
        listBtn.classList.remove('text-gray-600');
        gridBtn.classList.remove('active', 'bg-blue-600', 'text-white');
        gridBtn.classList.add('text-gray-600');
    }
}

// Checkbox Selection
const checkboxes = document.querySelectorAll('.photo-checkbox');
const bulkActions = document.getElementById('bulk-actions');
const selectedCount = document.getElementById('selected-count');

checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActions);
});

function updateBulkActions() {
    const checked = document.querySelectorAll('.photo-checkbox:checked');
    if (checked.length > 0) {
        bulkActions.classList.remove('hidden');
        selectedCount.textContent = checked.length;
    } else {
        bulkActions.classList.add('hidden');
    }
}

function clearSelection() {
    checkboxes.forEach(checkbox => checkbox.checked = false);
    bulkActions.classList.add('hidden');
}

// Modal Functions
function openUploadModal() {
    document.getElementById('upload-modal').classList.remove('hidden');
}

function closeUploadModal() {
    document.getElementById('upload-modal').classList.add('hidden');
}

function uploadPhotos() {
    showToast('Success', 'Photos uploaded successfully!', 'success');
    closeUploadModal();
    setTimeout(() => location.reload(), 1500);
}

function viewPhoto(id) {
    document.getElementById('view-modal').classList.remove('hidden');
}

function closeViewModal() {
    document.getElementById('view-modal').classList.add('hidden');
}

function prevPhoto() {
    showToast('Info', 'Loading previous photo...', 'info');
}

function nextPhoto() {
    showToast('Info', 'Loading next photo...', 'info');
}

function editPhoto(id) {
    document.getElementById('edit-modal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}

function savePhotoEdit() {
    showToast('Success', 'Photo details updated successfully!', 'success');
    closeEditModal();
}

function pushPhoto(id) {
    document.getElementById('push-modal').classList.remove('hidden');
}

function closePushModal() {
    document.getElementById('push-modal').classList.add('hidden');
}

function confirmPush() {
    showToast('Success', 'Photos pushed to 4 branches successfully!', 'success');
    closePushModal();
}

function deletePhoto(id) {
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
}

function confirmDelete() {
    showToast('Success', 'Photo deleted successfully!', 'success');
    closeDeleteModal();
    setTimeout(() => location.reload(), 1500);
}

function downloadPhoto() {
    showToast('Success', 'Download started...', 'success');
}

function deleteFromView() {
    closeViewModal();
    deletePhoto(1);
}

// Bulk Actions
function bulkPush() {
    document.getElementById('push-modal').classList.remove('hidden');
    bulkActions.classList.add('hidden');
}

function bulkArchive() {
    showToast('Success', 'Selected photos archived!', 'success');
    clearSelection();
}

function bulkDelete() {
    document.getElementById('delete-modal').classList.remove('hidden');
    bulkActions.classList.add('hidden');
}

// Toast Notification
function showToast(title, message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastTitle = document.getElementById('toast-title');
    const toastMessage = document.getElementById('toast-message');
    const toastIcon = document.getElementById('toast-icon');
    
    toastTitle.textContent = title;
    toastMessage.textContent = message;
    
    const icons = {
        success: '<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        error: '<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        info: '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
    };
    
    toastIcon.innerHTML = icons[type] || icons.success;
    toast.classList.remove('hidden');
    
    setTimeout(() => closeToast(), 5000);
}

function closeToast() {
    document.getElementById('toast').classList.add('hidden');
}
</script>

<style>
.view-btn.active {
    background-color: #2563eb;
    color: white;
}

.service-tab.active {
    border-bottom-color: #2563eb;
    color: #2563eb;
}

.transition {
    transition: all 0.2s ease-in-out;
}
</style>
@endsection