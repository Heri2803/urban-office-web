@extends('layouts.admin')

@section('title', 'Service Confirmation')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
        
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Service Confirmation 📑</h1>
            <p class="text-gray-600">Review and confirm bookings for Virtual Office, Coworking Space, and Event Space.</p>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-gray-100">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Filter Options</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Months</option>
                        <option value="01">January</option>
                        <option value="02">February</option>
                        <option value="03">March</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Years</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="settlement">Settlement Only</option>
                        <option value="confirmed">Confirmed Only</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 mt-6 pt-4 border-t border-gray-100">
                <button class="px-5 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                    Apply Filter
                </button>
                <button class="px-5 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    Reset Filter
                </button>
            </div>
        </div>

        <!-- Service Cards Grid - Improved Spacing -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8">
            
            <!-- Virtual Office Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow">
                
                <!-- Card Header -->
                <div class="p-5 border-b border-purple-200 bg-gradient-to-r from-purple-50 to-purple-100">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Virtual Office
                    </h2>
                    <p class="text-sm text-purple-700 mt-1">Pending: 3 | Confirmed: 2</p>
                </div>

                <!-- Card Body -->
                <div class="p-5 space-y-5">
                    
                    <!-- Action Section -->
                    <div class="pb-5 border-b border-gray-200">
                        <h3 class="text-md font-semibold text-gray-700 mb-3">Action: Confirm Booking</h3>
                        <div class="p-4 rounded-lg bg-purple-50 border border-purple-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Customer</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 mb-3 text-sm">
                                <option value="">-- Select Customer --</option>
                                <option value="1">John Doe - #VO001 - 2025-10-15</option>
                                <option value="2">Jane Smith - #VO002 - 2025-10-16</option>
                                <option value="3">Bob Wilson - #VO003 - 2025-10-17</option>
                            </select>
                            <button class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium text-sm shadow-md">
                                Confirm Booking
                            </button>
                        </div>
                    </div>

                    <!-- Booking Lists -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        
                        <!-- Waiting Confirmation -->
                        <div>
                            <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <span class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></span>
                                Waiting Confirmation (3)
                            </h3>
                            <div class="space-y-3 max-h-72 overflow-y-auto pr-2 custom-scrollbar">
                                <div class="border border-yellow-300 bg-yellow-50 rounded-lg p-3">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">#VO001</p>
                                            <p class="text-xs text-gray-600">John Doe</p>
                                        </div>
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-200 text-yellow-800">Settlement</span>
                                    </div>
                                    <div class="text-xs text-gray-600 space-y-1">
                                        <div class="flex justify-between"><span>Start Date:</span><span class="text-gray-900 font-medium">2025-10-15</span></div>
                                        <div class="flex justify-between"><span>Duration:</span><span class="text-gray-900">1 Month</span></div>
                                        <div class="flex justify-between font-bold text-sm text-purple-600 pt-2 border-t border-yellow-200 mt-2"><span>Amount:</span><span>Rp 1.500.000</span></div>
                                    </div>
                                </div>
                                <div class="border border-yellow-300 bg-yellow-50 rounded-lg p-3">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">#VO002</p>
                                            <p class="text-xs text-gray-600">Jane Smith</p>
                                        </div>
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-200 text-yellow-800">Settlement</span>
                                    </div>
                                    <div class="text-xs text-gray-600 space-y-1">
                                        <div class="flex justify-between"><span>Start Date:</span><span class="text-gray-900 font-medium">2025-10-16</span></div>
                                        <div class="flex justify-between"><span>Duration:</span><span class="text-gray-900">1 Year</span></div>
                                        <div class="flex justify-between font-bold text-sm text-purple-600 pt-2 border-t border-yellow-200 mt-2"><span>Amount:</span><span>Rp 15.000.000</span></div>
                                    </div>
                                </div>
                                <div class="border border-yellow-300 bg-yellow-50 rounded-lg p-3">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">#VO003</p>
                                            <p class="text-xs text-gray-600">Bob Wilson</p>
                                        </div>
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-200 text-yellow-800">Settlement</span>
                                    </div>
                                    <div class="text-xs text-gray-600 space-y-1">
                                        <div class="flex justify-between"><span>Start Date:</span><span class="text-gray-900 font-medium">2025-10-17</span></div>
                                        <div class="flex justify-between"><span>Duration:</span><span class="text-gray-900">6 Months</span></div>
                                        <div class="flex justify-between font-bold text-sm text-purple-600 pt-2 border-t border-yellow-200 mt-2"><span>Amount:</span><span>Rp 8.500.000</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Confirmed -->
                        <div>
                            <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                Confirmed (2)
                            </h3>
                            <div class="space-y-3 max-h-72 overflow-y-auto pr-2 custom-scrollbar">
                                <div class="border border-green-300 bg-green-50 rounded-lg p-3">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">#VO100</p>
                                            <p class="text-xs text-gray-600">Sarah Johnson</p>
                                        </div>
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-200 text-green-800">Confirmed</span>
                                    </div>
                                    <div class="text-xs text-gray-600 space-y-1 mb-3">
                                        <div class="flex justify-between"><span>Start Date:</span><span class="text-gray-900 font-medium">2025-10-10</span></div>
                                        <div class="flex justify-between"><span>Duration:</span><span class="text-gray-900">1 Month</span></div>
                                    </div>
                                    <button class="w-full px-3 py-1.5 bg-red-100 text-red-700 text-xs rounded-lg hover:bg-red-200 transition-colors font-medium">
                                        Cancel Confirmation
                                    </button>
                                </div>
                                <div class="border border-green-300 bg-green-50 rounded-lg p-3">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">#VO101</p>
                                            <p class="text-xs text-gray-600">Mike Brown</p>
                                        </div>
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-200 text-green-800">Confirmed</span>
                                    </div>
                                    <div class="text-xs text-gray-600 space-y-1 mb-3">
                                        <div class="flex justify-between"><span>Start Date:</span><span class="text-gray-900 font-medium">2025-10-12</span></div>
                                        <div class="flex justify-between"><span>Duration:</span><span class="text-gray-900">1 Year</span></div>
                                    </div>
                                    <button class="w-full px-3 py-1.5 bg-red-100 text-red-700 text-xs rounded-lg hover:bg-red-200 transition-colors font-medium">
                                        Cancel Confirmation
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Coworking Space Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow">
                
                <!-- Card Header -->
                <div class="p-5 border-b border-blue-200 bg-gradient-to-r from-blue-50 to-blue-100">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857m0 0c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Coworking Space
                    </h2>
                    <div class="mt-2">
                        <p class="text-sm text-blue-700 mb-2">Capacity: 15/20 (75% Occupied) | Pending: 2 | Confirmed: 1</p>
                        <div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div></div>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-5 space-y-5">
                    
                    <!-- Action Section with Alert -->
                    <div class="pb-5 border-b border-gray-200">
                        <h3 class="text-md font-semibold text-gray-700 mb-3">Action: Confirm Booking</h3>
                        <div class="p-3 mb-4 rounded-lg bg-yellow-100 border border-yellow-300">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <div>
                                    <p class="text-sm font-medium text-yellow-800">Near Capacity Limit</p>
                                    <p class="text-xs text-yellow-700 mt-0.5">Only 5 spots remaining for today</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 rounded-lg bg-blue-50 border border-blue-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Customer</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-3 text-sm">
                                <option value="">-- Select Customer --</option>
                                <option value="1">Alice Cooper - #CW001 - 2025-10-13</option>
                                <option value="2">David Lee - #CW002 - 2025-10-14</option>
                            </select>
                            <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm shadow-md">
                                Confirm Booking
                            </button>
                        </div>
                    </div>

                    <!-- Booking Lists -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        
                        <!-- Waiting Confirmation -->
                        <div>
                            <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <span class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></span>
                                Waiting Confirmation (2)
                            </h3>
                            <div class="space-y-3 max-h-72 overflow-y-auto pr-2 custom-scrollbar">
                                <div class="border border-yellow-300 bg-yellow-50 rounded-lg p-3">
                                    <p class="text-sm font-bold text-gray-900">#CW001 - Alice Cooper</p>
                                    <p class="text-xs text-gray-600 mt-1">Date: 2025-10-13 (1 Day)</p>
                                    <p class="text-sm font-bold text-blue-600 mt-2">Rp 150.000</p>
                                </div>
                                <div class="border border-yellow-300 bg-yellow-50 rounded-lg p-3">
                                    <p class="text-sm font-bold text-gray-900">#CW002 - David Lee</p>
                                    <p class="text-xs text-gray-600 mt-1">Date: 2025-10-14 (1 Month)</p>
                                    <p class="text-sm font-bold text-blue-600 mt-2">Rp 2.500.000</p>
                                </div>
                            </div>
                        </div>

                        <!-- Confirmed -->
                        <div>
                            <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                Confirmed (1)
                            </h3>
                            <div class="space-y-3 max-h-72 overflow-y-auto pr-2 custom-scrollbar">
                                <div class="border border-green-300 bg-green-50 rounded-lg p-3">
                                    <p class="text-sm font-bold text-gray-900">#CW100 - Emma White</p>
                                    <p class="text-xs text-gray-600 mt-1 mb-3">Date: 2025-10-13 (1 Month)</p>
                                    <button class="w-full px-3 py-1.5 bg-red-100 text-red-700 text-xs rounded-lg hover:bg-red-200 transition-colors font-medium">
                                        Cancel Confirmation
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Event Space Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow">
                
                <!-- Card Header -->
                <div class="p-5 border-b border-orange-200 bg-gradient-to-r from-orange-50 to-orange-100">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Event Space
                    </h2>
                    <p class="text-sm text-orange-700 mt-1">Pending: 2 | Confirmed: 1</p>
                </div>

                <!-- Card Body -->
                <div class="p-5 space-y-5">
                    
                    <!-- Action Section with Info -->
                    <div class="pb-5 border-b border-gray-200">
                        <h3 class="text-md font-semibold text-gray-700 mb-3">Action: Confirm Booking</h3>
                        <div class="p-3 mb-4 rounded-lg bg-blue-100 border border-blue-300">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-800">Cleanup Schedule</p>
                                    <p class="text-xs text-blue-700 mt-0.5">1 hour block time after each event</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 rounded-lg bg-orange-50 border border-orange-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Customer</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 mb-3 text-sm">
                                <option value="">-- Select Customer --</option>
                                <option value="1">Tech Corp - #ES001 - 2025-10-20</option>
                                <option value="2">Marketing Agency - #ES002 - 2025-10-22</option>
                            </select>
                            <button class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors font-medium text-sm shadow-md">
                                Confirm Booking
                            </button>
                        </div>
                    </div>

                    <!-- Booking Lists -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        
                        <!-- Waiting Confirmation -->
                        <div>
                            <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <span class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></span>
                                Waiting Confirmation (2)
                            </h3>
                            <div class="space-y-3 max-h-72 overflow-y-auto pr-2 custom-scrollbar">
                                <div class="border border-yellow-300 bg-yellow-50 rounded-lg p-3">
                                    <p class="text-sm font-bold text-gray-900">#ES001 - Tech Corp</p>
                                    <p class="text-xs text-gray-600 mt-1">Date: 2025-10-20 (09:00-17:00)</p>
                                    <p class="text-xs text-orange-700 mt-1">Cleanup: 17:00 - 18:00</p>
                                    <p class="text-sm font-bold text-orange-600 mt-2">Rp 5.000.000</p>
                                </div>
                                <div class="border border-yellow-300 bg-yellow-50 rounded-lg p-3">
                                    <p class="text-sm font-bold text-gray-900">#ES002 - Marketing Agency</p>
                                    <p class="text-xs text-gray-600 mt-1">Date: 2025-10-22 (14:00-18:00)</p>
                                    <p class="text-xs text-orange-700 mt-1">Cleanup: 18:00 - 19:00</p>
                                    <p class="text-sm font-bold text-orange-600 mt-2">Rp 2.000.000</p>
                                </div>
                            </div>
                        </div>

                        <!-- Confirmed -->
                        <div>
                            <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                Confirmed (1)
                            </h3>
                            <div class="space-y-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                                <div class="border border-green-300 bg-green-50 rounded-lg p-3">
                                    <p class="text-sm font-bold text-gray-900">#ES100 - Startup Summit</p>
                                    <p class="text-xs text-gray-600 mt-1 mb-3">Date: 2025-10-18 (08:00-17:00)</p>
                                    <button class="w-full px-3 py-1.5 bg-red-100 text-red-700 text-xs rounded-lg hover:bg-red-200 transition-colors font-medium">
                                        Cancel Confirmation
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>  

<!-- Confirmation Modal -->
<div id="confirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Confirm Booking</h3>
            <p class="text-sm text-gray-600 text-center mb-6">Are you sure you want to confirm this booking?</p>
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Booking ID:</span>
                        <span class="font-medium text-gray-900">#VO001</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Customer:</span>
                        <span class="font-medium text-gray-900">John Doe</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Service:</span>
                        <span class="font-medium text-gray-900">Virtual Office</span>
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <button class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Cancel
                </button>
                <button class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Confirmation Modal -->
<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Cancel Confirmation</h3>
            <p class="text-sm text-gray-600 text-center mb-6">Are you sure you want to cancel this confirmation? The booking will return to waiting confirmation list.</p>
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Booking ID:</span>
                        <span class="font-medium text-gray-900">#VO100</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Customer:</span>
                        <span class="font-medium text-gray-900">Sarah Johnson</span>
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <button class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    No, Keep It
                </button>
                <button class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    Yes, Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Modal handling
    document.addEventListener('DOMContentLoaded', function() {
        // You can add JavaScript for modal interactions here
        // Example modal toggle functions
    });
</script>
<style>
/* Custom scrollbar to make the overflow-y-auto look cleaner */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #d1d5db; /* gray-300 */
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: #9ca3af; /* gray-400 */
}
</style>
@endpush