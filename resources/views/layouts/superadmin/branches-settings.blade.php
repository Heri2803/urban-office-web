@extends('layouts.superadmin')

@section('title', 'Branch Settings')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('superadmin.dashboard') }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Branch Settings</h1>
                </div>
                <p class="text-sm text-gray-600 ml-9">Configure branch details and operational settings</p>
            </div>
            <div class="flex gap-2">
                <button onclick="cancelChanges()" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button onclick="saveSettings()" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    <!-- Branch Info Card -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-sm p-6 mb-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="bg-white bg-opacity-20 p-4 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold mb-1">Surabaya - Gubeng Branch</h2>
                    <p class="text-blue-100 text-sm">PT Workspace Indonesia • 15 Active Rooms • 245 Bookings This Month</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-400 bg-opacity-30 text-white border border-white border-opacity-30">
                    <span class="w-2 h-2 bg-green-200 rounded-full mr-2"></span>
                    Active
                </span>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-t-lg shadow-sm border border-b-0 border-gray-200 overflow-x-auto">
        <div class="flex min-w-max md:min-w-0">
            <button onclick="switchTab('general')" id="tab-general" class="tab-button active px-6 py-4 text-sm font-medium border-b-2 border-blue-600 text-blue-600 hover:bg-gray-50 transition whitespace-nowrap">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                General Settings
            </button>
            <button onclick="switchTab('services')" id="tab-services" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition whitespace-nowrap">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                Services & Pricing
            </button>
            <button onclick="switchTab('booking')" id="tab-booking" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition whitespace-nowrap">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Booking Configuration
            </button>
            <button onclick="switchTab('admin')" id="tab-admin" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition whitespace-nowrap">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Admin Access
            </button>
        </div>
    </div>

    <!-- Tab Content Container -->
    <div class="bg-white rounded-b-lg shadow-sm border border-gray-200 p-6">
        
        <!-- TAB 1: General Settings -->
        <div id="content-general" class="tab-content">
            <div class="max-w-4xl">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">General Settings</h3>
                
                <!-- Basic Information -->
                <div class="mb-8">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Basic Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Branch Name <span class="text-red-500">*</span></label>
                            <input type="text" value="Surabaya - Gubeng" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Display Name (Public) <span class="text-red-500">*</span></label>
                            <input type="text" value="Workspace Gubeng" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mitra <span class="text-red-500">*</span></label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50" disabled>
                                <option>PT Workspace Indonesia</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Mitra cannot be changed. Contact system admin if needed.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">City <span class="text-red-500">*</span></label>
                            <input type="text" value="Surabaya" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address <span class="text-red-500">*</span></label>
                        <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">Jl. Gubeng Kertajaya No. 125, Gubeng, Surabaya, Jawa Timur 60281</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone <span class="text-red-500">*</span></label>
                            <input type="tel" value="+62 31 1234567" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                            <input type="email" value="gubeng@workspace.id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Operating Hours -->
                <div class="mb-8">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Operating Hours
                    </h4>
                    <div class="space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="w-32">
                                <span class="text-sm font-medium text-gray-700">Monday - Friday</span>
                            </div>
                            <div class="flex items-center gap-2 flex-1">
                                <input type="time" value="08:00" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <span class="text-gray-500">-</span>
                                <input type="time" value="17:00" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="w-32">
                                <span class="text-sm font-medium text-gray-700">Saturday</span>
                            </div>
                            <div class="flex items-center gap-2 flex-1">
                                <input type="time" value="09:00" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <span class="text-gray-500">-</span>
                                <input type="time" value="15:00" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="w-32">
                                <span class="text-sm font-medium text-gray-700">Sunday</span>
                            </div>
                            <div class="flex items-center gap-2 flex-1">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-600">Closed</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Branch Status -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Branch Status
                    </h4>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Branch Status</p>
                            <p class="text-xs text-gray-500 mt-1">Enable or disable this branch for public booking</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: Services & Pricing -->
        <div id="content-services" class="tab-content hidden">
            <div class="max-w-4xl">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Services & Pricing</h3>

                <!-- Available Services -->
                <div class="mb-8">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Available Services
                    </h4>
                    <p class="text-sm text-gray-600 mb-4">Select which services are available at this branch</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-center p-4 bg-white border-2 border-blue-200 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                            <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Meeting Room</p>
                                <p class="text-xs text-gray-500">Hourly-based meeting spaces</p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 bg-white border-2 border-blue-200 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                            <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Private Office</p>
                                <p class="text-xs text-gray-500">Dedicated private workspaces</p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 bg-white border-2 border-blue-200 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                            <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Sharing Room</p>
                                <p class="text-xs text-gray-500">Shared workspace areas</p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 bg-gray-100 border-2 border-gray-300 rounded-lg cursor-not-allowed">
                            <input type="checkbox" disabled class="rounded border-gray-300 text-gray-400">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-500">Virtual Office</p>
                                <p class="text-xs text-gray-400">Not available at this branch</p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 bg-white border-2 border-blue-200 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                            <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Coworking Space</p>
                                <p class="text-xs text-gray-500">Flexible desk spaces</p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 bg-white border-2 border-blue-200 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                            <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Event Space</p>
                                <p class="text-xs text-gray-500">Large event venues</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Pricing Mode -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Pricing Mode
                    </h4>

                    <div class="space-y-4">
                        <label class="flex items-start p-4 bg-white border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="pricing_mode" class="mt-1 text-blue-600 focus:ring-blue-500">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Use Master Pricing</p>
                                <p class="text-xs text-gray-500 mt-1">Use default pricing from Global Pricing Management</p>
                            </div>
                        </label>

                        <label class="flex items-start p-4 bg-white border-2 border-blue-600 rounded-lg cursor-pointer">
                            <input type="radio" name="pricing_mode" checked class="mt-1 text-blue-600 focus:ring-blue-500">
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900">Branch Override Pricing</p>
                                <p class="text-xs text-gray-500 mt-1 mb-4">Set custom pricing for this branch (requires approval)</p>
                                
                                <!-- Quick Price Preview -->
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <p class="text-xs font-semibold text-gray-700 mb-3">Quick Price Preview</p>
                                    <div class="space-y-2 text-xs">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Meeting Room (1 hour)</span>
                                            <span class="font-medium text-gray-900">Rp 100.000</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Private Office (monthly)</span>
                                            <span class="font-medium text-gray-900">Rp 3.500.000</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Coworking (daily)</span>
                                            <span class="font-medium text-gray-900">Rp 75.000</span>
                                        </div>
                                    </div>
                                    <a href="" class="inline-flex items-center text-xs text-blue-600 hover:text-blue-700 font-medium mt-3">
                                        Edit Full Pricing Table
                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-amber-900">Price Override Requires Approval</p>
                                <p class="text-xs text-amber-700 mt-1">Any changes to pricing will be submitted for approval by Super Admin before taking effect.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: Booking Configuration -->
        <div id="content-booking" class="tab-content hidden">
            <div class="max-w-4xl">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Booking Configuration</h3>

                <!-- Booking Rules -->
                <div class="mb-8">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Booking Rules
                    </h4>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Advance Booking</label>
                                <div class="flex items-center gap-2">
                                    <input type="number" value="60" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <span class="text-sm text-gray-600">days</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">How far in advance customers can book</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Advance Booking</label>
                                <div class="flex items-center gap-2">
                                    <input type="number" value="1" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <span class="text-sm text-gray-600">hour</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Minimum time before booking starts</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Allow Same-Day Booking</p>
                                <p class="text-xs text-gray-500 mt-1">Customers can book for today</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" checked class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Cancellation Policy -->
                <div class="mb-8">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Cancellation Policy
                    </h4>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Free Cancellation Period</label>
                            <div class="flex items-center gap-2">
                                <input type="number" value="24" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <span class="text-sm text-gray-600">hours before booking</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">100% refund if cancelled within this period</p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-sm font-medium text-gray-700 mb-3">Refund Policy</p>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-24">
                                            <span class="text-sm text-gray-600">> 7 days</span>
                                        </div>
                                        <input type="number" value="100" class="w-20 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <span class="text-sm text-gray-600">% refund</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-24">
                                            <span class="text-sm text-gray-600">3-7 days</span>
                                        </div>
                                        <input type="number" value="50" class="w-20 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <span class="text-sm text-gray-600">% refund</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-24">
                                            <span class="text-sm text-gray-600">< 3 days</span>
                                        </div>
                                        <input type="number" value="0" class="w-20 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <span class="text-sm text-gray-600">% refund</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Auto-Confirmation -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Auto-Confirmation Settings
                    </h4>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-green-900">Auto-confirm After Payment Settlement</p>
                                    <p class="text-xs text-green-700 mt-1">Booking will be automatically confirmed once payment is verified by Midtrans</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" checked class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600"></div>
                            </label>
                        </div>

                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-900">Recommended Setting</p>
                                    <p class="text-xs text-blue-700 mt-1">Keep this enabled for better customer experience and reduce admin workload. Payment verification by Midtrans is instant and secure.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 4: Admin Access -->
        <div id="content-admin" class="tab-content hidden">
            <div class="max-w-4xl">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Admin Access Management</h3>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Assigned Admins (2)
                            </h4>
                            <p class="text-xs text-gray-500 mt-1">Admins who can manage bookings and operations for this branch</p>
                        </div>
                        <button onclick="openAssignAdminModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Assign New Admin
                        </button>
                    </div>

                    <!-- Admin List -->
                    <div class="space-y-3">
                        <!-- Admin 1 -->
                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-semibold text-lg">AB</span>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-semibold text-gray-900">Admin Budi Santoso</h5>
                                        <p class="text-xs text-gray-500">budi.santoso@workspace.id</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                Branch Admin
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span>
                                                Active
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button onclick="viewAdminDetail(1)" class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button onclick="editAdminAccess(1)" class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Access">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button onclick="removeAdmin(1)" class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Remove">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                                    <div>
                                        <p class="text-gray-500">Assigned Date</p>
                                        <p class="font-medium text-gray-900">01 Jan 2025</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Last Login</p>
                                        <p class="font-medium text-gray-900">2 hours ago</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Bookings Processed</p>
                                        <p class="font-medium text-gray-900">245 this month</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Phone</p>
                                        <p class="font-medium text-gray-900">+62 812-3456-7890</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Admin 2 -->
                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 bg-purple-100 rounded-full flex items-center justify-center">
                                        <span class="text-purple-600 font-semibold text-lg">SR</span>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-semibold text-gray-900">Siti Rahayu</h5>
                                        <p class="text-xs text-gray-500">siti.rahayu@workspace.id</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                Branch Admin
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span>
                                                Active
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button onclick="viewAdminDetail(2)" class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button onclick="editAdminAccess(2)" class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Access">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button onclick="removeAdmin(2)" class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Remove">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                                    <div>
                                        <p class="text-gray-500">Assigned Date</p>
                                        <p class="font-medium text-gray-900">15 Feb 2025</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Last Login</p>
                                        <p class="font-medium text-gray-900">1 day ago</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Bookings Processed</p>
                                        <p class="font-medium text-gray-900">178 this month</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Phone</p>
                                        <p class="font-medium text-gray-900">+62 821-9876-5432</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-blue-900">Need More Control?</p>
                            <p class="text-xs text-blue-700 mt-1">For full admin management including creating new admins, permissions, and detailed settings, visit the <a href="{{ route('superadmin.users') }}" class="underline font-medium hover:text-blue-900">User Management</a> page.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Assign Admin Modal -->
<div id="assignAdminModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Assign New Admin</h3>
                <button onclick="closeAssignAdminModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Admin</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option>Choose an admin...</option>
                        <option>Ahmad Wijaya (ahmad@workspace.id)</option>
                        <option>Dewi Sartika (dewi@workspace.id)</option>
                        <option>Rizki Pratama (rizki@workspace.id)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Only shows admins not yet assigned to any branch</p>
                </div>

                <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <p class="text-xs text-amber-800">
                        <strong>Note:</strong> Each admin can only be assigned to ONE branch. If you need to create a new admin account, go to User Management page first.
                    </p>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button onclick="closeAssignAdminModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button onclick="confirmAssignAdmin()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    Assign Admin
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-sm w-full p-6">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Remove Admin Access?</h3>
            <p class="text-sm text-gray-600 mb-6">This will remove admin access to this branch. The admin account will remain active but won't be able to manage this branch anymore.</p>
            <div class="flex gap-3">
                <button onclick="closeConfirmModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button onclick="confirmRemoveAdmin()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition">
                    Remove
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="hidden fixed top-6 right-6 bg-white rounded-lg shadow-lg border border-gray-200 p-4 z-50 max-w-sm">
    <div class="flex items-start gap-3">
        <div id="toastIcon" class="flex-shrink-0">
            <!-- Icon will be injected via JS -->
        </div>
        <div class="flex-1">
            <h4 id="toastTitle" class="text-sm font-semibold text-gray-900 mb-1"></h4>
            <p id="toastMessage" class="text-sm text-gray-600"></p>
        </div>
        <button onclick="closeToast()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<script>
// Tab Switching
function switchTab(tabName) {
    // Hide all tab contents
    const contents = document.querySelectorAll('.tab-content');
    contents.forEach(content => content.classList.add('hidden'));
    
    // Remove active class from all tabs
    const tabs = document.querySelectorAll('.tab-button');
    tabs.forEach(tab => {
        tab.classList.remove('active', 'border-blue-600', 'text-blue-600');
        tab.classList.add('border-transparent', 'text-gray-600');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Add active class to selected tab
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.add('active', 'border-blue-600', 'text-blue-600');
    activeTab.classList.remove('border-transparent', 'text-gray-600');
}

// Save Settings
function saveSettings() {
    showToast('Success', 'Branch settings saved successfully!', 'success');
    setTimeout(() => {
        // Optionally redirect or reload
        // window.location.href = "{{ route('superadmin.branches.all') }}";
    }, 1500);
}

function cancelChanges() {
    if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
        window.location.href = "{{ route('superadmin.branches.all') }}";
    }
}

// Modal Functions
function openAssignAdminModal() {
    document.getElementById('assignAdminModal').classList.remove('hidden');
}

function closeAssignAdminModal() {
    document.getElementById('assignAdminModal').classList.add('hidden');
}

function confirmAssignAdmin() {
    showToast('Success', 'Admin assigned successfully!', 'success');
    closeAssignAdminModal();
    // Reload or update UI
    setTimeout(() => {
        location.reload();
    }, 1500);
}

// Admin Actions
function viewAdminDetail(id) {
    // Redirect to user management with specific admin
    window.location.href = "{{ route('superadmin.users') }}/" + id;
}

function editAdminAccess(id) {
    showToast('Info', 'Opening admin edit form...', 'info');
    // Open edit modal or redirect
}

let adminToRemove = null;

function removeAdmin(id) {
    adminToRemove = id;
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    adminToRemove = null;
}

function confirmRemoveAdmin() {
    if (adminToRemove) {
        showToast('Success', 'Admin access removed successfully!', 'success');
        closeConfirmModal();
        // Remove admin from UI or reload
        setTimeout(() => {
            location.reload();
        }, 1500);
    }
}

// Toast Notification
function showToast(title, message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');
    
    toastTitle.textContent = title;
    toastMessage.textContent = message;
    
    const icons = {
        success: '<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        error: '<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        info: '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
    };
    
    toastIcon.innerHTML = icons[type] || icons.success;
    toast.classList.remove('hidden');
    
    setTimeout(() => {
        closeToast();
    }, 5000);
}

function closeToast() {
    document.getElementById('toast').classList.add('hidden');
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    // Add any form validation logic here
    console.log('Branch Settings page loaded');
});
</script>

<style>
/* Tab transition */
.tab-content {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Toggle Switch Custom Styles */
input[type="checkbox"]:checked + div {
    background-color: #2563eb;
}

/* Hover effects */
.tab-button:hover {
    background-color: #f9fafb;
}

/* Smooth transitions */
.transition {
    transition: all 0.2s ease-in-out;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Responsive table on mobile */
@media (max-width: 768px) {
    .tab-button {
        font-size: 0.875rem;
        padding: 0.75rem 1rem;
    }
}

/* Print styles */
@media print {
    .no-print,
    button,
    .tab-button {
        display: none !important;
    }
    
    .tab-content {
        display: block !important;
    }
}
</style>
@endsection 