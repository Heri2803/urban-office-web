@extends('layouts.superadmin')

@section('title', 'Voucher Usage Report')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">🎟️ Voucher Usage Report</h1>
                <p class="text-gray-600">Monitor voucher redemption and usage across all branches</p>
            </div>
            <div class="relative">
                <button id="exportBtn" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Report
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <!-- Export Dropdown -->
                <div id="exportDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                    <button class="exportOption w-full text-left px-4 py-2 hover:bg-gray-50 text-sm text-gray-700 flex items-center gap-2" data-format="excel">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export to Excel
                    </button>
                    <button class="exportOption w-full text-left px-4 py-2 hover:bg-gray-50 text-sm text-gray-700 flex items-center gap-2" data-format="pdf">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Export to PDF
                    </button>
                    <button class="exportOption w-full text-left px-4 py-2 hover:bg-gray-50 text-sm text-gray-700 flex items-center gap-2" data-format="csv">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export to CSV
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Vouchers</p>
                    <p class="text-3xl font-bold text-gray-900">150</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Active</p>
                    <p class="text-3xl font-bold text-green-600">45</p>
                    <p class="text-xs text-gray-500 mt-1">30%</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Redeemed</p>
                    <p class="text-3xl font-bold text-blue-600">89</p>
                    <p class="text-xs text-gray-500 mt-1">59%</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Expired</p>
                    <p class="text-3xl font-bold text-red-600">16</p>
                    <p class="text-xs text-gray-500 mt-1">11%</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="date" id="startDate" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <input type="date" id="endDate" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mitra</label>
                <select id="mitraFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Mitra</option>
                    <option value="1">PT Workspace Indonesia</option>
                    <option value="2">CV Ruang Kreatif</option>
                    <option value="3">PT Office Hub</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Branch</label>
                <select id="branchFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Branches</option>
                    <option value="1">Surabaya - Gubeng</option>
                    <option value="2">Jakarta - Senayan</option>
                    <option value="3">Bandung - Dago</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Voucher Type</label>
                <select id="typeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Types</option>
                    <option value="free">Free Meeting Room 1H</option>
                    <option value="percent10">10% Discount</option>
                    <option value="percent20">20% Discount</option>
                    <option value="fixed">Fixed Amount</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="redeemed">Redeemed</option>
                    <option value="expired">Expired</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Code or customer..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button id="applyFilterBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Apply Filter
            </button>
            <button id="resetFilterBtn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                Reset Filter
            </button>
        </div>
    </div>

    <!-- Usage Per Branch Chart -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Voucher Usage Per Branch (Last 30 Days)</h2>
        <div class="h-64">
            <canvas id="usageChart"></canvas>
        </div>
    </div>

    <!-- Voucher Performance -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">🏆 Top Voucher</h3>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">FREEMR1H</span>
                    <span class="text-sm font-semibold text-gray-900">45 uses</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">DISCOUNT20</span>
                    <span class="text-sm font-semibold text-gray-900">32 uses</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">WELCOME10</span>
                    <span class="text-sm font-semibold text-gray-900">28 uses</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">🏢 Top Branch</h3>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Surabaya - Gubeng</span>
                    <span class="text-sm font-semibold text-gray-900">38 vouchers</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Jakarta - Senayan</span>
                    <span class="text-sm font-semibold text-gray-900">29 vouchers</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Bandung - Dago</span>
                    <span class="text-sm font-semibold text-gray-900">22 vouchers</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">📊 By Service</h3>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Meeting Room</span>
                    <span class="text-sm font-semibold text-gray-900">52 (58%)</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Private Office</span>
                    <span class="text-sm font-semibold text-gray-900">22 (25%)</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Event Space</span>
                    <span class="text-sm font-semibold text-gray-900">15 (17%)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Voucher Usage Grid -->
    <div class="mb-4">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Voucher Usage Details</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <!-- Voucher Card 1 - Redeemed -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="text-white text-lg font-bold">FREEMR1H001</p>
                        <p class="text-blue-100 text-xs">Free Meeting Room 1 Hour</p>
                    </div>
                    <span class="px-2 py-1 bg-white text-blue-600 text-xs font-semibold rounded-full">
                        Redeemed
                    </span>
                </div>
                <div class="flex items-center gap-2 text-white text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Budi Santoso</span>
                </div>
            </div>
            <div class="p-4">
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Branch:</span>
                        <span class="text-gray-900 font-medium">Surabaya - Gubeng</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Service:</span>
                        <span class="text-gray-900 font-medium">Meeting Room</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Original:</span>
                        <span class="text-gray-900">Rp 100,000</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Discount:</span>
                        <span class="text-green-600 font-semibold">-Rp 100,000</span>
                    </div>
                    <div class="flex justify-between text-sm pt-2 border-t">
                        <span class="text-gray-600 font-medium">Final:</span>
                        <span class="text-gray-900 font-bold">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 pt-1">
                        <span>Redeemed:</span>
                        <span>Oct 15, 2025</span>
                    </div>
                </div>
                <button class="viewVoucherBtn w-full px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium">
                    View Details
                </button>
            </div>
        </div>

        <!-- Voucher Card 2 - Redeemed -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="bg-gradient-to-r from-green-500 to-green-600 p-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="text-white text-lg font-bold">DISCOUNT20</p>
                        <p class="text-green-100 text-xs">20% Discount</p>
                    </div>
                    <span class="px-2 py-1 bg-white text-green-600 text-xs font-semibold rounded-full">
                        Redeemed
                    </span>
                </div>
                <div class="flex items-center gap-2 text-white text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Siti Rahayu</span>
                </div>
            </div>
            <div class="p-4">
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Branch:</span>
                        <span class="text-gray-900 font-medium">Jakarta - Senayan</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Service:</span>
                        <span class="text-gray-900 font-medium">Private Office</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Original:</span>
                        <span class="text-gray-900">Rp 3,500,000</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Discount:</span>
                        <span class="text-green-600 font-semibold">-Rp 700,000</span>
                    </div>
                    <div class="flex justify-between text-sm pt-2 border-t">
                        <span class="text-gray-600 font-medium">Final:</span>
                        <span class="text-gray-900 font-bold">Rp 2,800,000</span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 pt-1">
                        <span>Redeemed:</span>
                        <span>Oct 14, 2025</span>
                    </div>
                </div>
                <button class="viewVoucherBtn w-full px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium">
                    View Details
                </button>
            </div>
        </div>

        <!-- Voucher Card 3 - Active -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="text-white text-lg font-bold">WELCOME10</p>
                        <p class="text-purple-100 text-xs">10% Welcome Discount</p>
                    </div>
                    <span class="px-2 py-1 bg-white text-purple-600 text-xs font-semibold rounded-full">
                        Active
                    </span>
                </div>
                <div class="flex items-center gap-2 text-white text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Not yet redeemed</span>
                </div>
            </div>
            <div class="p-4">
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Valid Until:</span>
                        <span class="text-gray-900 font-medium">Dec 31, 2025</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Usage Limit:</span>
                        <span class="text-gray-900 font-medium">0/50 used</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Discount:</span>
                        <span class="text-purple-600 font-semibold">10%</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Applicable:</span>
                        <span class="text-gray-900 text-xs">All Services</span>
                    </div>
                    <div class="flex justify-between text-sm pt-2 border-t">
                        <span class="text-gray-600 font-medium">Status:</span>
                        <span class="text-green-600 font-bold">Available</span>
                    </div>
                </div>
                <button class="viewVoucherBtn w-full px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium">
                    View Details
                </button>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
        <p class="text-sm text-gray-600">Showing 3 of 89 vouchers</p>
        <div class="flex gap-2">
            <button class="paginationBtn px-3 py-1 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                Previous
            </button>
            <button class="paginationBtn px-3 py-1 bg-blue-600 text-white rounded-lg text-sm">1</button>
            <button class="paginationBtn px-3 py-1 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">2</button>
            <button class="paginationBtn px-3 py-1 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                Next
            </button>
        </div>
    </div>
</div>

<!-- Voucher Details Modal -->
<div id="voucherDetailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Voucher Details</h3>
                <button class="closeVoucherDetailBtn text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Voucher Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white mb-6">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="text-2xl font-bold mb-1">FREEMR1H001</p>
                        <p class="text-blue-100">Free Meeting Room 1 Hour</p>
                    </div>
                    <span class="px-3 py-1 bg-white text-blue-600 text-sm font-semibold rounded-full">
                        Redeemed
                    </span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-semibold">100% Discount - FREE</span>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="space-y-6">
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Voucher Information</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Discount Value:</span>
                            <span class="text-gray-900 font-medium">100% (FREE)</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Valid Period:</span>
                            <span class="text-gray-900 font-medium">Oct 1 - Dec 31, 2025</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Usage Limit:</span>
                            <span class="text-gray-900 font-medium">1/1 (Single Use)</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Redemption Details</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Customer:</span>
                            <span class="text-gray-900 font-medium">Budi Santoso</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Email:</span>
                            <span class="text-gray-900 font-medium">budi@example.com</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Booking ID:</span>
                            <span class="text-gray-900 font-medium">#MR-001</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Branch:</span>
                            <span class="text-gray-900 font-medium">Surabaya - Gubeng</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Transaction Details</h4>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Original Price:</span>
                            <span class="text-gray-900">Rp 100,000</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Discount:</span>
                            <span class="text-green-600 font-semibold">-Rp 100,000</span>
                        </div>
                        <div class="flex justify-between text-sm pt-2 border-t border-gray-300">
                            <span class="text-gray-900 font-semibold">Final:</span>
                            <span class="text-gray-900 font-bold text-lg">Rp 0</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Additional Info</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Redeemed:</span>
                            <span class="text-gray-900 font-medium">Oct 15, 2025 14:30</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Processed By:</span>
                            <span class="text-gray-900 font-medium">Admin Budi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Chart
        const ctx = document.getElementById('usageChart');
        let usageChart = null;

        if (ctx) {
            usageChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Day 1', 'Day 5', 'Day 10', 'Day 15', 'Day 20', 'Day 25', 'Day 30'],
                    datasets: [
                        {
                            label: 'Surabaya - Gubeng',
                            data: [12, 19, 15, 25, 22, 30, 28],
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'Jakarta - Senayan',
                            data: [8, 15, 12, 18, 20, 25, 22],
                            borderColor: 'rgb(16, 185, 129)',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'Bandung - Dago',
                            data: [5, 10, 8, 15, 12, 18, 16],
                            borderColor: 'rgb(139, 92, 246)',
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 5
                            }
                        }
                    }
                }
            });
        }

        // Export Dropdown
        const exportBtn = document.getElementById('exportBtn');
        const exportDropdown = document.getElementById('exportDropdown');

        if (exportBtn && exportDropdown) {
            exportBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                exportDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function(e) {
                if (!exportBtn.contains(e.target) && !exportDropdown.contains(e.target)) {
                    exportDropdown.classList.add('hidden');
                }
            });
        }

        // Export Options
        const exportOptions = document.querySelectorAll('.exportOption');
        exportOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                const format = this.getAttribute('data-format');
                exportDropdown.classList.add('hidden');
                showNotification(`Exporting to ${format.toUpperCase()}...`, 'info');
                setTimeout(() => {
                    showNotification(`Report exported to ${format.toUpperCase()}!`, 'success');
                }, 1500);
            });
        });

        // Dynamic Branch Filter
        const mitraFilter = document.getElementById('mitraFilter');
        const branchFilter = document.getElementById('branchFilter');

        const branchData = {
            '1': [
                { value: '1', text: 'Surabaya - Gubeng' },
                { value: '2', text: 'Surabaya - HR Muhammad' }
            ],
            '2': [
                { value: '4', text: 'Jakarta - Senayan' },
                { value: '5', text: 'Jakarta - Sudirman' }
            ],
            '3': [
                { value: '6', text: 'Bandung - Dago' },
                { value: '7', text: 'Bandung - Riau' }
            ]
        };

        if (mitraFilter && branchFilter) {
            mitraFilter.addEventListener('change', function() {
                const mitraId = this.value;
                branchFilter.innerHTML = '<option value="">All Branches</option>';
                
                if (mitraId && branchData[mitraId]) {
                    branchData[mitraId].forEach(branch => {
                        const option = document.createElement('option');
                        option.value = branch.value;
                        option.textContent = branch.text;
                        branchFilter.appendChild(option);
                    });
                }
                showNotification('Branch filter updated', 'info');
            });
        }

        // Apply Filter
        const applyFilterBtn = document.getElementById('applyFilterBtn');
        if (applyFilterBtn) {
            applyFilterBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showNotification('Applying filters...', 'info');
                setTimeout(() => {
                    showNotification('Vouchers filtered successfully', 'success');
                    if (usageChart) {
                        const newData = generateRandomData();
                        usageChart.data.datasets.forEach((dataset, i) => {
                            dataset.data = newData[i];
                        });
                        usageChart.update();
                    }
                }, 800);
            });
        }

        // Reset Filter
        const resetFilterBtn = document.getElementById('resetFilterBtn');
        if (resetFilterBtn) {
            resetFilterBtn.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('startDate').value = '';
                document.getElementById('endDate').value = '';
                mitraFilter.value = '';
                branchFilter.innerHTML = '<option value="">All Branches</option>';
                document.getElementById('typeFilter').value = '';
                document.getElementById('statusFilter').value = '';
                document.getElementById('searchInput').value = '';
                showNotification('Filters reset', 'info');
                if (usageChart) {
                    usageChart.data.datasets[0].data = [12, 19, 15, 25, 22, 30, 28];
                    usageChart.data.datasets[1].data = [8, 15, 12, 18, 20, 25, 22];
                    usageChart.data.datasets[2].data = [5, 10, 8, 15, 12, 18, 16];
                    usageChart.update();
                }
            });
        }

        // View Voucher Details
        const viewVoucherBtns = document.querySelectorAll('.viewVoucherBtn');
        const voucherDetailModal = document.getElementById('voucherDetailModal');
        const closeVoucherDetailBtns = document.querySelectorAll('.closeVoucherDetailBtn');

        viewVoucherBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                voucherDetailModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                showNotification('Loading details...', 'info');
            });
        });

        closeVoucherDetailBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                voucherDetailModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            });
        });

        voucherDetailModal?.addEventListener('click', function(e) {
            if (e.target === voucherDetailModal) {
                voucherDetailModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        });

        // Pagination
        const paginationBtns = document.querySelectorAll('.paginationBtn');
        paginationBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const pageText = this.textContent.trim();
                showNotification(`Loading page: ${pageText}`, 'info');
                setTimeout(() => {
                    showNotification('Page loaded', 'success');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }, 500);
            });
        });

        function generateRandomData() {
            const data1 = Array.from({length: 7}, () => Math.floor(Math.random() * 30) + 5);
            const data2 = Array.from({length: 7}, () => Math.floor(Math.random() * 25) + 5);
            const data3 = Array.from({length: 7}, () => Math.floor(Math.random() * 20) + 3);
            return [data1, data2, data3];
        }

        function showNotification(message, type = 'info') {
            const existingNotif = document.getElementById('notification');
            if (existingNotif) existingNotif.remove();

            const notification = document.createElement('div');
            notification.id = 'notification';
            notification.className = 'fixed top-4 right-4 z-[60] px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in';
            
            const colors = {
                success: 'bg-green-500 text-white',
                error: 'bg-red-500 text-white',
                warning: 'bg-orange-500 text-white',
                info: 'bg-blue-500 text-white'
            };
            
            notification.className += ' ' + (colors[type] || colors.info);
            
            const icons = {
                success: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
                info: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
            };
            
            notification.innerHTML = `${icons[type] || icons.info}<span>${message}</span>`;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        const style = document.createElement('style');
        style.textContent = `
            @keyframes slide-in {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            .animate-slide-in { animation: slide-in 0.3s ease-out; }
            #notification { transition: all 0.3s ease-out; }
        `;
        document.head.appendChild(style);

        console.log('Voucher Usage Report initialized!');
    });
</script>
@endpush