@extends('layouts.superadmin')

@section('content')
<main x-data="usageReports()" x-init="init()" class="p-4 sm:p-6 lg:p-10 space-y-6 bg-gray-50 min-h-screen">

    {{-- Header & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800">📊 Voucher Usage Reports</h1>
            <p class="text-sm text-gray-500 mt-1">Track and analyze voucher performance</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button @click="exportReport('pdf')" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Export PDF
            </button>
            <button @click="exportReport('excel')" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Excel
            </button>
            <button @click="scheduleReport()" class="px-4 py-2 border border-gray-300 bg-white rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hidden sm:flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Schedule Email
            </button>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">📅 Report Period & Filters</h2>
        
        <div class="space-y-4">
            {{-- Date Range --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Report Period</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input type="date" x-model="filters.startDate" 
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <input type="date" x-model="filters.endDate" 
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            {{-- Quick Select --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quick Select</label>
                <div class="flex flex-wrap gap-2">
                    <button @click="setQuickDate('today')" 
                            class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"
                            :class="quickDateActive === 'today' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-gray-300'">
                        Today
                    </button>
                    <button @click="setQuickDate('week')" 
                            class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"
                            :class="quickDateActive === 'week' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-gray-300'">
                        This Week
                    </button>
                    <button @click="setQuickDate('month')" 
                            class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"
                            :class="quickDateActive === 'month' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-gray-300'">
                        This Month
                    </button>
                    <button @click="setQuickDate('lastMonth')" 
                            class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"
                            :class="quickDateActive === 'lastMonth' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-gray-300'">
                        Last Month
                    </button>
                    <button @click="setQuickDate('quarter')" 
                            class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"
                            :class="quickDateActive === 'quarter' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-gray-300'">
                        This Quarter
                    </button>
                    <button @click="setQuickDate('year')" 
                            class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50"
                            :class="quickDateActive === 'year' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-gray-300'">
                        This Year
                    </button>
                </div>
            </div>

            {{-- Filters --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Voucher Code</label>
                    <select x-model="filters.voucherCode" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">All Vouchers</option>
                        <template x-for="voucher in availableVouchers" :key="voucher">
                            <option :value="voucher" x-text="voucher"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mitra</label>
                    <select x-model="filters.mitra" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">All Mitra</option>
                        <option value="PT SBY Office">PT SBY Office</option>
                        <option value="PT JKT Workspace">PT JKT Workspace</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                    <select x-model="filters.branch" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">All Branches</option>
                        <option value="Surabaya Center">Surabaya Center</option>
                        <option value="Jakarta Selatan">Jakarta Selatan</option>
                        <option value="Surabaya Timur">Surabaya Timur</option>
                        <option value="Jakarta Barat">Jakarta Barat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Service Type</label>
                    <select x-model="filters.service" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">All Services</option>
                        <option value="Meeting Room">Meeting Room</option>
                        <option value="Private Office">Private Office</option>
                        <option value="Coworking Space">Coworking Space</option>
                        <option value="Event Space">Event Space</option>
                    </select>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-end space-x-3 pt-2">
                <button @click="resetFilters()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Reset
                </button>
                <button @click="generateReport()" class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                    Generate Report
                </button>
            </div>
        </div>
    </div>

    {{-- Summary Dashboard --}}
    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl shadow-md border border-indigo-200 p-4 sm:p-6">
        <h2 class="text-lg font-bold text-indigo-900 mb-4">📈 Overall Performance</h2>
        <p class="text-sm text-indigo-700 mb-4" x-text="'Period: ' + filters.startDate + ' to ' + filters.endDate"></p>
        
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Total Attempts</p>
                <p class="text-2xl font-bold text-gray-900" x-text="summary.totalAttempts.toLocaleString('id-ID')"></p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Successful</p>
                <p class="text-2xl font-bold text-green-600" x-text="summary.successful.toLocaleString('id-ID')"></p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Failed/Invalid</p>
                <p class="text-2xl font-bold text-red-600" x-text="summary.failed.toLocaleString('id-ID')"></p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Total Discount</p>
                <p class="text-xl sm:text-2xl font-bold text-indigo-600" x-text="'Rp ' + summary.totalDiscount.toLocaleString('id-ID')"></p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Avg. Discount</p>
                <p class="text-xl sm:text-2xl font-bold text-purple-600" x-text="'Rp ' + summary.avgDiscount.toLocaleString('id-ID')"></p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Conversion Rate</p>
                <p class="text-2xl font-bold text-orange-600" x-text="summary.conversionRate + '%'"></p>
            </div>
        </div>

        {{-- Top Performing Voucher --}}
        <div class="mt-4 bg-white rounded-xl p-4">
            <p class="text-sm font-semibold text-gray-700 mb-2">💡 Top Performing Voucher</p>
            <div class="flex justify-between items-center">
                <div>
                    <p class="font-bold text-indigo-600" x-text="summary.topVoucher.code"></p>
                    <p class="text-xs text-gray-500" x-text="summary.topVoucher.redemptions + ' redemptions'"></p>
                </div>
                <p class="text-lg font-bold text-green-600" x-text="'Rp ' + summary.topVoucher.discount.toLocaleString('id-ID')"></p>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Redemption Trends Chart --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700">📊 Redemption Trends</h3>
                <select x-model="chartType" class="text-sm border-gray-300 rounded-lg">
                    <option value="line">Line</option>
                    <option value="bar">Bar</option>
                    <option value="area">Area</option>
                </select>
            </div>
            <div class="h-64 flex items-end justify-between space-x-1 sm:space-x-2">
                <template x-for="(day, index) in chartData.daily" :key="index">
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full bg-gradient-to-t from-indigo-500 to-indigo-400 rounded-t hover:from-indigo-600 hover:to-indigo-500 transition-all cursor-pointer relative group"
                             :style="'height: ' + (day.value / chartData.maxValue * 100) + '%'">
                            <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap">
                                <span x-text="day.value"></span> redemptions
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2" x-text="day.label"></p>
                    </div>
                </template>
            </div>
        </div>

        {{-- Voucher Performance Comparison --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">📊 Voucher Performance</h3>
            <div class="space-y-3">
                <template x-for="voucher in voucherPerformance" :key="voucher.code">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700" x-text="voucher.code"></span>
                            <span class="font-bold text-indigo-600" x-text="voucher.redemptions"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-6 relative overflow-hidden">
                            <div class="h-6 rounded-full flex items-center px-3 transition-all"
                                 :style="'width: ' + voucher.percentage + '%; background: linear-gradient(to right, ' + voucher.color + ', ' + voucher.colorDark + ')'">
                                <span class="text-white text-xs font-bold" x-show="voucher.percentage > 20" x-text="voucher.percentage + '%'"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Usage by Service Type --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">📊 Usage by Service Type</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <template x-for="service in serviceUsage" :key="service.name">
                <div class="text-center">
                    <div class="relative inline-flex items-center justify-center w-24 h-24 sm:w-28 sm:h-28">
                        <svg class="transform -rotate-90 w-24 h-24 sm:w-28 sm:h-28">
                            <circle cx="56" cy="56" r="45" stroke="#e5e7eb" stroke-width="10" fill="none"/>
                            <circle cx="56" cy="56" r="45" :stroke="service.color" stroke-width="10" fill="none"
                                    :stroke-dasharray="2 * 3.14159 * 45"
                                    :stroke-dashoffset="2 * 3.14159 * 45 * (1 - service.percentage / 100)"
                                    class="transition-all duration-1000"/>
                        </svg>
                        <span class="absolute text-lg font-bold" :style="'color: ' + service.color" x-text="service.percentage + '%'"></span>
                    </div>
                    <p class="text-sm font-medium text-gray-700 mt-2" x-text="service.name"></p>
                    <p class="text-xs text-gray-500" x-text="service.count + ' uses'"></p>
                </div>
            </template>
        </div>
    </div>

    {{-- Top Performers --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Branches --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">📍 Top Branches by Usage</h3>
            <div class="space-y-3">
                <template x-for="(branch, index) in topBranches" :key="branch.name">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-white"
                             :class="index === 0 ? 'bg-yellow-500' : index === 1 ? 'bg-gray-400' : index === 2 ? 'bg-orange-600' : 'bg-gray-300'"
                             x-text="index + 1">
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900" x-text="branch.name"></p>
                            <div class="flex items-center space-x-2 mt-1">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full" :style="'width: ' + branch.percentage + '%'"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-600" x-text="branch.count"></span>
                            </div>
                        </div>
                        <span class="text-sm text-gray-500" x-text="branch.percentage + '%'"></span>
                    </div>
                </template>
            </div>
        </div>

        {{-- Customer Insights --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">👥 Customer Insights</h3>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-xs text-blue-700 mb-1">New Customers</p>
                        <p class="text-2xl font-bold text-blue-600" x-text="customerInsights.newCustomers"></p>
                        <p class="text-xs text-gray-500 mt-1" x-text="customerInsights.newPercentage + '% of total'"></p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-xs text-green-700 mb-1">Returning</p>
                        <p class="text-2xl font-bold text-green-600" x-text="customerInsights.returning"></p>
                        <p class="text-xs text-gray-500 mt-1" x-text="customerInsights.returningPercentage + '% of total'"></p>
                    </div>
                </div>
                <div class="border-t pt-4">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Avg. Transaction with Voucher</span>
                        <span class="font-bold text-gray-900" x-text="'Rp ' + customerInsights.avgWithVoucher.toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Avg. Transaction without</span>
                        <span class="font-bold text-gray-900" x-text="'Rp ' + customerInsights.avgWithout.toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex items-center justify-between bg-green-50 rounded-lg p-3 mt-3">
                        <span class="text-sm font-medium text-green-700">Transaction Increase</span>
                        <span class="text-lg font-bold text-green-600" x-text="'↑ ' + customerInsights.increase + '%'"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Failed Redemptions --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">❌ Failed Redemption Analysis</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-4">
            <div class="bg-red-50 rounded-lg p-3 text-center">
                <p class="text-2xl font-bold text-red-600" x-text="failedReasons.expired"></p>
                <p class="text-xs text-gray-600 mt-1">Expired</p>
            </div>
            <div class="bg-orange-50 rounded-lg p-3 text-center">
                <p class="text-2xl font-bold text-orange-600" x-text="failedReasons.limitReached"></p>
                <p class="text-xs text-gray-600 mt-1">Limit Reached</p>
            </div>
            <div class="bg-yellow-50 rounded-lg p-3 text-center">
                <p class="text-2xl font-bold text-yellow-600" x-text="failedReasons.invalidCode"></p>
                <p class="text-xs text-gray-600 mt-1">Invalid Code</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-3 text-center">
                <p class="text-2xl font-bold text-purple-600" x-text="failedReasons.minNotMet"></p>
                <p class="text-xs text-gray-600 mt-1">Min. Not Met</p>
            </div>
            <div class="bg-pink-50 rounded-lg p-3 text-center">
                <p class="text-2xl font-bold text-pink-600" x-text="failedReasons.notApplicable"></p>
                <p class="text-xs text-gray-600 mt-1">Not Applicable</p>
            </div>
        </div>
        <button @click="viewDetailedLog()" class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
            View Detailed Log
        </button>
    </div>

    {{-- Detailed Usage Table --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-2 sm:space-y-0">
            <h3 class="text-lg font-semibold text-gray-700">📋 Redemption History (<span x-text="getFilteredHistory().length"></span>)</h3>
            <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-600">Show:</label>
                <select x-model="historyPerPage" @change="currentHistoryPage = 1" class="text-sm border-gray-300 rounded-lg">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

        {{-- Cards Grid for All Devices --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="history in getCurrentPageHistory()" :key="history.id">
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow hover:border-indigo-300">
                    {{-- Header: Date & Voucher Code --}}
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <p class="font-bold text-indigo-600 text-lg" x-text="history.voucherCode"></p>
                            <p class="text-xs text-gray-500 mt-1">
                                <span x-text="history.date"></span> • <span x-text="history.time"></span>
                            </p>
                        </div>
                        <div class="bg-green-50 px-3 py-1 rounded-lg">
                            <p class="text-xs text-green-700 font-medium">Success</p>
                        </div>
                    </div>

                    {{-- Booking Info --}}
                    <div class="space-y-2 mb-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Booking ID:</span>
                            <span class="font-medium text-gray-900" x-text="history.bookingId"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Customer:</span>
                            <span class="font-medium text-gray-900" x-text="history.customerName"></span>
                        </div>
                    </div>

                    {{-- Service & Branch --}}
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div class="bg-blue-50 rounded-lg p-2">
                            <p class="text-xs text-blue-700 mb-1">Service</p>
                            <p class="text-sm font-medium text-blue-900" x-text="history.service"></p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-2">
                            <p class="text-xs text-purple-700 mb-1">Branch</p>
                            <p class="text-sm font-medium text-purple-900" x-text="history.branch"></p>
                        </div>
                    </div>

                    {{-- Discount Amount --}}
                    <div class="pt-3 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Discount Given</span>
                            <span class="text-xl font-bold text-green-600" x-text="'Rp ' + history.discount.toLocaleString('id-ID')"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Empty State --}}
        <div x-show="getCurrentPageHistory().length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="mt-2 text-sm text-gray-500">No redemption history found for selected filters.</p>
        </div>

        {{-- Pagination --}}
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0 border-t pt-4">
            <div class="text-sm text-gray-700">
                Showing <span class="font-medium" x-text="getHistoryPaginationInfo().start"></span> 
                to <span class="font-medium" x-text="getHistoryPaginationInfo().end"></span> 
                of <span class="font-medium" x-text="getHistoryPaginationInfo().total"></span> records
            </div>
            <div class="flex items-center space-x-2">
                <button @click="changeHistoryPage(currentHistoryPage - 1)" 
                        :disabled="currentHistoryPage === 1"
                        class="px-3 py-2 border rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">
                    Previous
                </button>
                
                {{-- Desktop: Show page numbers --}}
                <div class="hidden sm:flex items-center space-x-1">
                    <template x-for="page in getHistoryPageNumbers()" :key="page">
                        <button @click="changeHistoryPage(page)"
                                class="px-3 py-2 border rounded-lg text-sm"
                                :class="page === currentHistoryPage ? 'bg-indigo-600 text-white border-indigo-600' : 'hover:bg-gray-100'">
                            <span x-text="page"></span>
                        </button>
                    </template>
                </div>

                {{-- Mobile: Show current page --}}
                <div class="sm:hidden px-4 py-2 text-sm font-medium">
                    <span x-text="currentHistoryPage"></span> / <span x-text="getHistoryTotalPages()"></span>
                </div>
                
                <button @click="changeHistoryPage(currentHistoryPage + 1)" 
                        :disabled="currentHistoryPage === getHistoryTotalPages()"
                        class="px-3 py-2 border rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">
                    Next
                </button>
            </div>
        </div>
    </div>

</main>

<script>
    function usageReports() {
        return {
            quickDateActive: 'month',
            chartType: 'line',
            historyPerPage: 10,
            currentHistoryPage: 1,

            filters: {
                startDate: '',
                endDate: '',
                voucherCode: '',
                mitra: '',
                branch: '',
                service: ''
            },

            availableVouchers: ['WELCOME2025', 'FREEMR1H', 'FLASH20', 'LOYALTY50', 'EARLYBIRD'],

            summary: {
                totalAttempts: 2450,
                successful: 2234,
                failed: 216,
                totalDiscount: 12500000,
                avgDiscount: 5598,
                conversionRate: 91.2,
                topVoucher: {
                    code: 'WELCOME2025',
                    redemptions: 234,
                    discount: 4200000
                }
            },

            chartData: {
                maxValue: 250,
                daily: []
            },

            voucherPerformance: [
                { code: 'WELCOME2025', redemptions: 234, percentage: 100, color: '#4f46e5', colorDark: '#4338ca' },
                { code: 'FREEMR1H', redemptions: 120, percentage: 51, color: '#0ea5e9', colorDark: '#0284c7' },
                { code: 'FLASH20', redemptions: 98, percentage: 42, color: '#8b5cf6', colorDark: '#7c3aed' },
                { code: 'LOYALTY50', redemptions: 76, percentage: 32, color: '#ec4899', colorDark: '#db2777' },
                { code: 'EARLYBIRD', redemptions: 45, percentage: 19, color: '#f59e0b', colorDark: '#d97706' }
            ],

            serviceUsage: [
                { name: 'Meeting Room', percentage: 35, count: 780, color: '#4f46e5' },
                { name: 'Private Office', percentage: 25, count: 558, color: '#0ea5e9' },
                { name: 'Coworking', percentage: 20, count: 447, color: '#10b981' },
                { name: 'Event Space', percentage: 12, count: 268, color: '#f59e0b' },
                { name: 'Sharing Room', percentage: 5, count: 112, color: '#8b5cf6' },
                { name: 'Virtual Office', percentage: 3, count: 67, color: '#ec4899' }
            ],

            topBranches: [
                { name: 'Surabaya Center', count: 345, percentage: 42 },
                { name: 'Jakarta Selatan', count: 289, percentage: 35 },
                { name: 'Surabaya Timur', count: 123, percentage: 15 },
                { name: 'Jakarta Barat', count: 67, percentage: 8 }
            ],

            customerInsights: {
                newCustomers: 456,
                newPercentage: 56,
                returning: 358,
                returningPercentage: 44,
                avgWithVoucher: 450000,
                avgWithout: 380000,
                increase: 18.4
            },

            failedReasons: {
                expired: 89,
                limitReached: 67,
                invalidCode: 34,
                minNotMet: 18,
                notApplicable: 8
            },

            redemptionHistory: [],

            init() {
                this.setQuickDate('month');
                this.generateChartData();
                this.generateRedemptionHistory();
            },

            setQuickDate(period) {
                this.quickDateActive = period;
                const today = new Date();
                const endDate = today.toISOString().split('T')[0];

                let startDate;
                switch(period) {
                    case 'today':
                        startDate = endDate;
                        break;
                    case 'week':
                        const weekAgo = new Date(today);
                        weekAgo.setDate(weekAgo.getDate() - 7);
                        startDate = weekAgo.toISOString().split('T')[0];
                        break;
                    case 'month':
                        const monthAgo = new Date(today.getFullYear(), today.getMonth(), 1);
                        startDate = monthAgo.toISOString().split('T')[0];
                        break;
                    case 'lastMonth':
                        const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                        startDate = lastMonth.toISOString().split('T')[0];
                        const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
                        this.filters.endDate = lastMonthEnd.toISOString().split('T')[0];
                        this.filters.startDate = startDate;
                        return;
                    case 'quarter':
                        const quarter = Math.floor(today.getMonth() / 3);
                        const quarterStart = new Date(today.getFullYear(), quarter * 3, 1);
                        startDate = quarterStart.toISOString().split('T')[0];
                        break;
                    case 'year':
                        const yearStart = new Date(today.getFullYear(), 0, 1);
                        startDate = yearStart.toISOString().split('T')[0];
                        break;
                }

                this.filters.startDate = startDate;
                this.filters.endDate = endDate;
            },

            resetFilters() {
                this.filters = {
                    startDate: this.filters.startDate,
                    endDate: this.filters.endDate,
                    voucherCode: '',
                    mitra: '',
                    branch: '',
                    service: ''
                };
            },

            generateReport() {
                alert('Generating report with current filters...\n\nPeriod: ' + this.filters.startDate + ' to ' + this.filters.endDate);
                // Simulate report generation
                this.generateChartData();
            },

            generateChartData() {
                this.chartData.daily = [];
                const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                days.forEach(day => {
                    this.chartData.daily.push({
                        label: day,
                        value: Math.floor(Math.random() * 200) + 50
                    });
                });
            },

            generateRedemptionHistory() {
                const vouchers = ['WELCOME2025', 'FREEMR1H', 'FLASH20', 'LOYALTY50', 'EARLYBIRD'];
                const services = ['Meeting Room', 'Private Office', 'Coworking Space', 'Event Space'];
                const branches = ['Surabaya Center', 'Jakarta Selatan', 'Surabaya Timur', 'Jakarta Barat'];
                const customers = ['John Doe', 'Jane Smith', 'Ahmad Wijaya', 'Siti Rahayu', 'Budi Santoso', 'Dewi Lestari'];

                this.redemptionHistory = [];
                for (let i = 0; i < 50; i++) {
                    const randomDate = new Date(2025, 9, Math.floor(Math.random() * 18) + 1);
                    this.redemptionHistory.push({
                        id: i + 1,
                        date: randomDate.toISOString().split('T')[0],
                        time: Math.floor(Math.random() * 24).toString().padStart(2, '0') + ':' + 
                              Math.floor(Math.random() * 60).toString().padStart(2, '0'),
                        voucherCode: vouchers[Math.floor(Math.random() * vouchers.length)],
                        bookingId: 'BK-' + (1000 + i),
                        customerName: customers[Math.floor(Math.random() * customers.length)],
                        service: services[Math.floor(Math.random() * services.length)],
                        branch: branches[Math.floor(Math.random() * branches.length)],
                        discount: Math.floor(Math.random() * 100000) + 10000
                    });
                });
            },

            getFilteredHistory() {
                let filtered = this.redemptionHistory;

                if (this.filters.voucherCode) {
                    filtered = filtered.filter(h => h.voucherCode === this.filters.voucherCode);
                }
                if (this.filters.branch) {
                    filtered = filtered.filter(h => h.branch === this.filters.branch);
                }
                if (this.filters.service) {
                    filtered = filtered.filter(h => h.service === this.filters.service);
                }

                return filtered;
            },

            getCurrentPageHistory() {
                const filtered = this.getFilteredHistory();
                const start = (this.currentHistoryPage - 1) * this.historyPerPage;
                const end = start + parseInt(this.historyPerPage);
                return filtered.slice(start, end);
            },

            getHistoryTotalPages() {
                const filtered = this.getFilteredHistory();
                return Math.ceil(filtered.length / this.historyPerPage) || 1;
            },

            getHistoryPageNumbers() {
                const total = this.getHistoryTotalPages();
                const current = this.currentHistoryPage;
                const pages = [];

                let startPage = Math.max(1, current - 2);
                let endPage = Math.min(total, current + 2);

                if (current <= 3) {
                    endPage = Math.min(5, total);
                }
                if (current >= total - 2) {
                    startPage = Math.max(1, total - 4);
                }

                for (let i = startPage; i <= endPage; i++) {
                    pages.push(i);
                }

                return pages;
            },

            getHistoryPaginationInfo() {
                const filtered = this.getFilteredHistory();
                const start = (this.currentHistoryPage - 1) * this.historyPerPage + 1;
                const end = Math.min(start + parseInt(this.historyPerPage) - 1, filtered.length);
                return {
                    start: filtered.length > 0 ? start : 0,
                    end: end,
                    total: filtered.length
                };
            },

            changeHistoryPage(page) {
                const totalPages = this.getHistoryTotalPages();
                if (page >= 1 && page <= totalPages) {
                    this.currentHistoryPage = page;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            exportReport(format) {
                alert(`Exporting report as ${format.toUpperCase()}...\n\nFilters:\nPeriod: ${this.filters.startDate} to ${this.filters.endDate}\nVoucher: ${this.filters.voucherCode || 'All'}\nBranch: ${this.filters.branch || 'All'}`);
            },

            scheduleReport() {
                alert('Schedule Email Report:\n\n• Daily at 9:00 AM\n• Weekly on Monday\n• Monthly on 1st day\n\nSelect your preference and enter email recipients.');
            },

            viewDetailedLog() {
                alert('Opening detailed failed redemption log...\n\nShowing all 216 failed attempts with:\n• Timestamp\n• Voucher code\n• Failure reason\n• Customer info\n• Attempted transaction details');
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection