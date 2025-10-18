@extends('layouts.superadmin')

@section('content')
<div class="min-h-screen bg-gray-50 p-4 md:p-6 lg:p-8">
    
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Activity Log</h1>
                <p class="text-sm md:text-base text-gray-600 mt-1">Track all admin activities across branches</p>
            </div>
            <div class="text-xs md:text-sm text-gray-500 bg-white px-3 md:px-4 py-2 rounded-lg">
                Last Updated: <span id="lastUpdate">{{ now()->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-5 border-l-4 border-blue-500">
            <div class="text-gray-600 text-xs md:text-sm font-medium">Total Actions</div>
            <div class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">487</div>
            <div class="text-xs text-gray-500 mt-1">Last 24 hours</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-5 border-l-4 border-green-500">
            <div class="text-gray-600 text-xs md:text-sm font-medium">Successful</div>
            <div class="text-2xl md:text-3xl font-bold text-green-600 mt-2">484</div>
            <div class="text-xs text-gray-500 mt-1">99.4% success rate</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-5 border-l-4 border-red-500">
            <div class="text-gray-600 text-xs md:text-sm font-medium">Failed</div>
            <div class="text-2xl md:text-3xl font-bold text-red-600 mt-2">3</div>
            <div class="text-xs text-gray-500 mt-1">0.6% failed rate</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-5 border-l-4 border-purple-500">
            <div class="text-gray-600 text-xs md:text-sm font-medium">Active Admins</div>
            <div class="text-2xl md:text-3xl font-bold text-purple-600 mt-2">12</div>
            <div class="text-xs text-gray-500 mt-1">All branches</div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-6">
        <h2 class="text-base md:text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <span>🔎</span> Filters & Search
        </h2>
        
        <form id="filterForm" class="space-y-4" onsubmit="event.preventDefault(); applyFilters();">
            
            <!-- Search Box -->
            <div>
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">Search</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input 
                        type="text" 
                        id="searchInput" 
                        placeholder="Search by admin name, action, booking ID, email..." 
                        class="w-full pl-10 pr-4 py-2 md:py-2.5 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                </div>
            </div>

            <!-- Filter Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                
                <!-- Admin Filter -->
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">Admin</label>
                    <select id="adminFilter" class="w-full px-3 md:px-4 py-2 md:py-2.5 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="">All Admins</option>
                        <option value="budi">Budi Santoso</option>
                        <option value="siti">Siti Nurhalim</option>
                        <option value="andi">Admin Andi</option>
                        <option value="rini">Rini Wijaya</option>
                        <option value="doni">Doni Permana</option>
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

                <!-- Action Type Filter -->
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">Action Type</label>
                    <select id="actionFilter" class="w-full px-3 md:px-4 py-2 md:py-2.5 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="">All Actions</option>
                        <option value="booking_created">Booking Created</option>
                        <option value="booking_cancelled">Booking Cancelled</option>
                        <option value="booking_modified">Booking Modified</option>
                        <option value="payment_changed">Payment Status Changed</option>
                        <option value="room_updated">Room Status Updated</option>
                        <option value="admin_login">Admin Login</option>
                        <option value="admin_logout">Admin Logout</option>
                        <option value="pricing_changed">Pricing Changed</option>
                        <option value="report_download">Report Downloaded</option>
                        <option value="settings_changed">Settings Changed</option>
                    </select>
                </div>

            </div>

            <!-- Date & Time Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input 
                        type="date" 
                        id="fromDate" 
                        class="w-full px-3 md:px-4 py-2 md:py-2.5 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        value="{{ date('Y-m-d', strtotime('-30 days')) }}"
                    />
                </div>
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input 
                        type="date" 
                        id="toDate" 
                        class="w-full px-3 md:px-4 py-2 md:py-2.5 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        value="{{ date('Y-m-d') }}"
                    />
                </div>
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">From Time</label>
                    <input 
                        type="time" 
                        id="fromTime" 
                        class="w-full px-3 md:px-4 py-2 md:py-2.5 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        value="00:00"
                    />
                </div>
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">To Time</label>
                    <input 
                        type="time" 
                        id="toTime" 
                        class="w-full px-3 md:px-4 py-2 md:py-2.5 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        value="23:59"
                    />
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="statusFilter" class="w-full px-3 md:px-4 py-2 md:py-2.5 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                    <option value="">All Status</option>
                    <option value="success">✅ Success</option>
                    <option value="failed">❌ Failed</option>
                    <option value="pending">⏳ Pending</option>
                    <option value="warning">⚠️ Warning</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-2 md:gap-3 pt-4 border-t">
                <button 
                    type="reset" 
                    class="flex-1 sm:flex-auto px-4 py-2 md:py-2.5 bg-gray-200 text-gray-800 rounded-lg text-xs md:text-sm font-medium hover:bg-gray-300 transition duration-200"
                >
                    🔄 Reset Filters
                </button>
                <button 
                    type="submit" 
                    class="flex-1 sm:flex-auto px-4 py-2 md:py-2.5 bg-blue-600 text-white rounded-lg text-xs md:text-sm font-medium hover:bg-blue-700 transition duration-200"
                >
                    🔍 Apply Filters
                </button>
                <div class="flex gap-2 md:gap-3">
                    <button 
                        type="button" 
                        onclick="exportData('csv')"
                        class="flex-1 sm:flex-auto px-3 md:px-4 py-2 md:py-2.5 bg-green-600 text-white rounded-lg text-xs md:text-sm font-medium hover:bg-green-700 transition duration-200"
                    >
                        ⬇️ CSV
                    </button>
                    <button 
                        type="button" 
                        onclick="exportData('pdf')"
                        class="flex-1 sm:flex-auto px-3 md:px-4 py-2 md:py-2.5 bg-red-600 text-white rounded-lg text-xs md:text-sm font-medium hover:bg-red-700 transition duration-200"
                    >
                        ⬇️ PDF
                    </button>
                    <button 
                        type="button" 
                        onclick="exportData('excel')"
                        class="flex-1 sm:flex-auto px-3 md:px-4 py-2 md:py-2.5 bg-blue-700 text-white rounded-lg text-xs md:text-sm font-medium hover:bg-blue-800 transition duration-200"
                    >
                        ⬇️ Excel
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Activity Log Table (Desktop View) -->
    <div class="hidden lg:block bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-semibold text-gray-700 cursor-pointer hover:bg-gray-100">
                            Time ↕
                        </th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-semibold text-gray-700">Admin</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-semibold text-gray-700">Branch</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-semibold text-gray-700">Action</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-semibold text-gray-700">Details</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-semibold text-gray-700">IP Address</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-center text-xs md:text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-center text-xs md:text-sm font-semibold text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php
                        $activities = [
                            ['time' => '14:35:22', 'admin' => 'Budi Santoso', 'branch' => 'Surabaya', 'action' => 'Booking Created', 'details' => 'Meeting Room #MR-089', 'ip' => '192.168.1.5', 'status' => 'success'],
                            ['time' => '14:20:15', 'admin' => 'Siti Nurhalim', 'branch' => 'Jakarta', 'action' => 'Payment Status Changed', 'details' => '#BK-456 Pending → Paid', 'ip' => '192.168.2.10', 'status' => 'success'],
                            ['time' => '13:45:08', 'admin' => 'Budi Santoso', 'branch' => 'Surabaya', 'action' => 'Booking Cancelled', 'details' => '#MR-088 (Customer request)', 'ip' => '192.168.1.5', 'status' => 'success'],
                            ['time' => '13:32:47', 'admin' => 'Admin Andi', 'branch' => 'Bandung', 'action' => 'Room Status Updated', 'details' => 'Room 201: Available → Maintenance', 'ip' => '192.168.3.8', 'status' => 'success'],
                            ['time' => '12:15:30', 'admin' => 'Siti Nurhalim', 'branch' => 'Jakarta', 'action' => 'Admin Login', 'details' => 'Login successful', 'ip' => '192.168.2.10', 'status' => 'success'],
                            ['time' => '11:48:22', 'admin' => 'Budi Santoso', 'branch' => 'Surabaya', 'action' => 'Booking Modified', 'details' => '#MR-085 Time changed', 'ip' => '192.168.1.5', 'status' => 'success'],
                            ['time' => '10:20:15', 'admin' => 'Admin Andi', 'branch' => 'Bandung', 'action' => 'Pricing Changed', 'details' => 'Attempt failed - unauthorized', 'ip' => '192.168.3.8', 'status' => 'failed'],
                            ['time' => '09:15:45', 'admin' => 'Siti Nurhalim', 'branch' => 'Jakarta', 'action' => 'Report Downloaded', 'details' => 'Revenue Report - Sept 2024', 'ip' => '192.168.2.10', 'status' => 'success'],
                        ];
                    @endphp
                    
                    @foreach($activities as $activity)
                    <tr class="hover:bg-gray-50 transition duration-150 group">
                        <td class="px-4 md:px-6 py-3 md:py-4 text-xs md:text-sm text-gray-900 font-medium">{{ $activity['time'] }}</td>
                        <td class="px-4 md:px-6 py-3 md:py-4 text-xs md:text-sm">
                            <div class="font-medium text-gray-900">{{ $activity['admin'] }}</div>
                        </td>
                        <td class="px-4 md:px-6 py-3 md:py-4 text-xs md:text-sm">
                            <span class="inline-block px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full">{{ $activity['branch'] }}</span>
                        </td>
                        <td class="px-4 md:px-6 py-3 md:py-4 text-xs md:text-sm">
                            <div class="text-gray-900 font-medium">{{ $activity['action'] }}</div>
                        </td>
                        <td class="px-4 md:px-6 py-3 md:py-4 text-xs md:text-sm text-gray-600">{{ $activity['details'] }}</td>
                        <td class="px-4 md:px-6 py-3 md:py-4 text-xs md:text-sm font-mono text-gray-500">{{ $activity['ip'] }}</td>
                        <td class="px-4 md:px-6 py-3 md:py-4 text-center">
                            @if($activity['status'] === 'success')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✅ Success</span>
                            @elseif($activity['status'] === 'failed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">❌ Failed</span>
                            @elseif($activity['status'] === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">⏳ Pending</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">⚠️ Warning</span>
                            @endif
                        </td>
                        <td class="px-4 md:px-6 py-3 md:py-4 text-center">
                            <div class="flex justify-center gap-2">
                                <button 
                                    onclick="viewDetail(this)"
                                    class="p-1.5 md:p-2 text-blue-600 hover:bg-blue-50 rounded transition duration-150"
                                    title="View Details"
                                >
                                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Table Pagination -->
        <div class="bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs md:text-sm">
            <div class="text-gray-600">
                Showing <span class="font-semibold">1-8</span> of <span class="font-semibold">2,547</span> records
            </div>
            <div class="flex items-center gap-2">
                <button class="px-2 md:px-3 py-1.5 md:py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition disabled:opacity-50" disabled>&lt; Previous</button>
                <button class="px-2.5 md:px-3 py-1.5 md:py-2 bg-blue-600 text-white rounded-lg">1</button>
                <button class="px-2.5 md:px-3 py-1.5 md:py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">2</button>
                <button class="px-2.5 md:px-3 py-1.5 md:py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">3</button>
                <span class="text-gray-500">...</span>
                <button class="px-2.5 md:px-3 py-1.5 md:py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">320</button>
                <button class="px-2 md:px-3 py-1.5 md:py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">Next &gt;</button>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-gray-600">Rows per page:</label>
                <select class="px-2 md:px-3 py-1.5 md:py-2 border border-gray-300 rounded-lg text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                    <option>100</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Activity Log Card View (Mobile & Tablet) -->
    <div class="lg:hidden space-y-3 md:space-y-4">
        @foreach($activities as $activity)
        <div class="bg-white rounded-lg shadow-sm p-3 md:p-4 border-l-4 @if($activity['status'] === 'success') border-green-500 @elseif($activity['status'] === 'failed') border-red-500 @else border-yellow-500 @endif">
            
            <!-- Header Row -->
            <div class="flex justify-between items-start gap-2 mb-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs md:text-sm font-bold text-gray-900">{{ $activity['admin'] }}</span>
                        <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full">{{ $activity['branch'] }}</span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ $activity['time'] }}</div>
                </div>
                <button 
                    onclick="viewDetail(this)"
                    class="p-2 text-blue-600 hover:bg-blue-50 rounded transition"
                    title="View Details"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            <!-- Action & Status Row -->
            <div class="mb-3">
                <div class="text-xs md:text-sm font-semibold text-gray-900 mb-1">{{ $activity['action'] }}</div>
                <div class="text-xs md:text-sm text-gray-600">{{ $activity['details'] }}</div>
            </div>

            <!-- Footer Row -->
            <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                <div class="text-xs text-gray-500 font-mono">{{ $activity['ip'] }}</div>
                @if($activity['status'] === 'success')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✅ Success</span>
                @elseif($activity['status'] === 'failed')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">❌ Failed</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">⏳ Pending</span>
                @endif
            </div>
        </div>
        @endforeach

        <!-- Mobile Pagination -->
        <div class="bg-white rounded-lg shadow-sm p-3 md:p-4 flex justify-center gap-2">
            <button class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm hover:bg-gray-100 transition disabled:opacity-50" disabled>&lt; Prev</button>
            <button class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm">1</button>
            <button class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm hover:bg-gray-100 transition">2</button>
            <button class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm hover:bg-gray-100 transition">Next &gt;</button>
        </div>
    </div>

</div>

<!-- Detail Modal -->
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg md:text-xl font-bold text-gray-900">Activity Detail</h2>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>

        <!-- Modal Content -->
        <div class="p-4 md:p-6 space-y-6">
            
            <!-- Basic Info -->
            <div>
                <h3 class="text-sm md:text-base font-semibold text-gray-900 mb-3">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4 bg-gray-50 p-3 md:p-4 rounded-lg">
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Activity ID</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">ACT-2024-10-16-14-35-22-001</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Timestamp</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">2024-10-16 14:35:22</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Admin</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">Budi Santoso</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Branch</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">Surabaya</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Action Type</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">Booking Created</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Status</div>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✅ Success</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">IP Address</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">192.168.1.5</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Device/Browser</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">Chrome 129.0 / Windows 10</div>
                    </div>
                </div>
            </div>

            <!-- Action Details -->
            <div>
                <h3 class="text-sm md:text-base font-semibold text-gray-900 mb-3">Action Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4 bg-blue-50 p-3 md:p-4 rounded-lg border border-blue-200">
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Booking ID</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">#MR-089</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Service Type</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">Meeting Room</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Customer Name</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">PT Maju Jaya</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Customer Email</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">contact@majujaya.com</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Customer Phone</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">08123456789</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Room Number</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">Meeting Room 2</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Check-in Date</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">2024-10-20</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Check-in Time</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">09:00</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Duration</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">2 hours</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Total Price</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">Rp 200.000</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Discount Applied</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">None</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Payment Method</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">Online (Midtrans)</div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-xs text-gray-600 font-medium">Special Request</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">"Perlu proyektor dan catering"</div>
                    </div>
                </div>
            </div>

            <!-- System Details -->
            <div>
                <h3 class="text-sm md:text-base font-semibold text-gray-900 mb-3">System Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4 bg-gray-50 p-3 md:p-4 rounded-lg">
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Request Method</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">POST</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">API Endpoint</div>
                        <div class="text-sm md:text-base text-gray-900 font-mono font-semibold mt-1">/api/bookings</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">HTTP Status</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">201 Created</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Response Time</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">234 ms</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Database Query Time</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">145 ms</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium">Cache Hit</div>
                        <div class="text-sm md:text-base text-gray-900 font-semibold mt-1">No</div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-xs text-gray-600 font-medium">User Agent</div>
                        <div class="text-xs md:text-sm text-gray-900 font-mono break-words mt-1">Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36</div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="sticky bottom-0 bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-t border-gray-200 flex flex-col sm:flex-row gap-2 sm:gap-3 justify-end">
            <button 
                onclick="printDetail()"
                class="flex-1 sm:flex-auto px-4 py-2 md:py-2.5 bg-gray-600 text-white rounded-lg text-xs md:text-sm font-medium hover:bg-gray-700 transition"
            >
                🖨️ Print
            </button>
            <button 
                onclick="exportDetail()"
                class="flex-1 sm:flex-auto px-4 py-2 md:py-2.5 bg-blue-600 text-white rounded-lg text-xs md:text-sm font-medium hover:bg-blue-700 transition"
            >
                📥 Export
            </button>
            <button 
                onclick="closeModal()"
                class="flex-1 sm:flex-auto px-4 py-2 md:py-2.5 bg-gray-300 text-gray-800 rounded-lg text-xs md:text-sm font-medium hover:bg-gray-400 transition"
            >
                Close
            </button>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    // Modal Functions
    function viewDetail(button) {
        const modal = document.getElementById('detailModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('detailModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    // Filter Functions
    function applyFilters() {
        const search = document.getElementById('searchInput').value;
        const admin = document.getElementById('adminFilter').value;
        const branch = document.getElementById('branchFilter').value;
        const action = document.getElementById('actionFilter').value;
        const fromDate = document.getElementById('fromDate').value;
        const toDate = document.getElementById('toDate').value;
        const fromTime = document.getElementById('fromTime').value;
        const toTime = document.getElementById('toTime').value;
        const status = document.getElementById('statusFilter').value;

        console.log('Filters Applied:', {
            search, admin, branch, action, fromDate, toDate, fromTime, toTime, status
        });

        // Update last updated time
        const now = new Date();
        document.getElementById('lastUpdate').textContent = now.toLocaleString('id-ID');

        // Here you would typically send these filters to your backend
        // Example: window.location.href = `/activity-log?search=${search}&admin=${admin}&branch=${branch}&action=${action}&fromDate=${fromDate}&toDate=${toDate}&status=${status}`;
    }

    // Export Functions
    function exportData(format) {
        const timestamp = new Date().toLocaleString('id-ID');
        alert(`Exporting Activity Log as ${format.toUpperCase()}...\n\nTimestamp: ${timestamp}\n\nNote: This will trigger your backend export endpoint.`);
        
        // You would typically call your backend here
        // Example: window.location.href = `/activity-log/export?format=${format}`;
    }

    function printDetail() {
        window.print();
    }

    function exportDetail() {
        alert('Exporting detail as PDF...');
        // window.location.href = '/activity-log/export-detail';
    }

    // Real-time search (optional debounce)
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            console.log('Searching for:', this.value);
        }, 500);
    });

    // Sort table columns (for desktop)
    let sortDirection = 'asc';
    document.querySelectorAll('thead th').forEach(header => {
        header.addEventListener('click', function() {
            if (this.textContent.includes('↕')) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                console.log('Sort by:', this.textContent, sortDirection);
            }
        });
    });

    // Update last modified time every minute
    setInterval(() => {
        const now = new Date();
        document.getElementById('lastUpdate').textContent = now.toLocaleString('id-ID');
    }, 60000);
</script>

<style>
    @media print {
        .hidden { display: none; }
        body { background: white; }
        .bg-gray-50 { background: white; }
        .shadow-sm { box-shadow: none; border: 1px solid #e5e7eb; }
    }
</style>
@endsection