@extends('layouts.superadmin')

@section('content')
<main x-data="revenueReport()" x-init="init()" class="p-4 sm:p-6 lg:p-10 space-y-6 bg-gray-50 min-h-screen">

    {{-- Header & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800">💰 Revenue Report</h1>
            <p class="text-sm text-gray-500 mt-1">Track and analyze revenue performance</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button @click="exportReport('pdf')" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                PDF
            </button>
            <button @click="exportReport('excel')" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Excel
            </button>
            <button @click="printReport()" class="px-4 py-2 border border-gray-300 bg-white rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hidden sm:flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </button>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">🔍 Filters</h2>
        
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                    <select x-model="filters.paymentStatus" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">All Status</option>
                        <option value="settlement">Settlement</option>
                        <option value="pending">Pending</option>
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

    {{-- Revenue Metrics Dashboard --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
            <p class="text-xs text-gray-500 mb-1">Total Revenue</p>
            <p class="text-2xl font-bold text-gray-900" x-text="'Rp ' + metrics.totalRevenue.toLocaleString('id-ID')"></p>
            <p class="text-xs text-gray-500 mt-25" x-text="'(' + filters.startDate + ' to ' + filters.endDate + ')'"></p>
        </div>
        
        <div class="bg-white rounded-xl p-4 shadow-md border border-gray-200">
            <p class="text-xs text-gray-500 mb-1">Avg. Transaction</p>
            <p class="text-2xl font-bold text-gray-900" x-text="'Rp ' + metrics.avgTransaction.toLocaleString('id-ID')"></p>
            <p class="text-xs text-gray-500 mt-2" x-text="metrics.totalTransactions + ' transactions'"></p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-md border border-gray-200">
            <p class="text-xs text-gray-500 mb-1">Revenue Growth</p>
            <p class="text-2xl font-bold" :class="metrics.growth >= 0 ? 'text-green-600' : 'text-red-600'" x-text="(metrics.growth >= 0 ? '+' : '') + metrics.growth + '%'"></p>
            <p class="text-xs text-gray-500 mt-2">vs previous period</p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-md border border-gray-200">
            <p class="text-xs text-gray-500 mb-1">Mitra Revenue</p>
            <p class="text-xl font-bold text-indigo-600" x-text="'Rp ' + metrics.mitraRevenue.toLocaleString('id-ID')"></p>
            <p class="text-xs text-gray-500 mt-2">Share to mitra</p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-md border border-gray-200">
            <p class="text-xs text-gray-500 mb-1">Tax Paid</p>
            <p class="text-xl font-bold text-orange-600" x-text="'Rp ' + metrics.taxPaid.toLocaleString('id-ID')"></p>
            <p class="text-xs text-gray-500 mt-2" x-text="metrics.taxRate + '% tax'"></p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-md border border-gray-200">
            <p class="text-xs text-gray-500 mb-1">Net Revenue</p>
            <p class="text-xl font-bold text-purple-600" x-text="'Rp ' + metrics.netRevenue.toLocaleString('id-ID')"></p>
            <p class="text-xs text-gray-500 mt-2">After tax & share</p>
        </div>
    </div>

    {{-- Revenue Comparison Chart --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-700">📊 Revenue Comparison: This Month vs Last Month</h3>
            <div class="flex items-center space-x-2">
                <button @click="chartView = 'bar'" 
                        class="px-3 py-1 text-sm rounded-lg"
                        :class="chartView === 'bar' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'">
                    Bar
                </button>
                <button @click="chartView = 'line'" 
                        class="px-3 py-1 text-sm rounded-lg"
                        :class="chartView === 'line' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'">
                    Line
                </button>
            </div>
        </div>

        {{-- Comparison Summary --}}
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-sm text-blue-700 font-medium mb-2">This Month</p>
                <p class="text-3xl font-bold text-blue-600" x-text="'Rp ' + comparison.thisMonth.toLocaleString('id-ID')"></p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-700 font-medium mb-2">Last Month</p>
                <p class="text-3xl font-bold text-gray-600" x-text="'Rp ' + comparison.lastMonth.toLocaleString('id-ID')"></p>
            </div>
        </div>

        {{-- Chart --}}
        <div class="h-80 flex items-end justify-between space-x-2 sm:space-x-4 px-2">
            <template x-for="(week, index) in comparison.weeklyData" :key="index">
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full flex justify-center space-x-1" :style="'height: ' + Math.max(week.thisMonth, week.lastMonth) / comparison.maxValue * 280 + 'px'">
                        {{-- This Month Bar --}}
                        <div class="flex-1 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t hover:from-blue-600 hover:to-blue-500 transition-all cursor-pointer relative group"
                             :style="'height: ' + (week.thisMonth / comparison.maxValue * 100) + '%'">
                            <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 bg-blue-600 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap z-10">
                                This: Rp <span x-text="week.thisMonth.toLocaleString('id-ID')"></span>
                            </div>
                        </div>
                        {{-- Last Month Bar --}}
                        <div class="flex-1 bg-gradient-to-t from-gray-400 to-gray-300 rounded-t hover:from-gray-500 hover:to-gray-400 transition-all cursor-pointer relative group"
                             :style="'height: ' + (week.lastMonth / comparison.maxValue * 100) + '%'">
                            <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 bg-gray-600 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap z-10">
                                Last: Rp <span x-text="week.lastMonth.toLocaleString('id-ID')"></span>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-3 text-center" x-text="week.label"></p>
                </div>
            </template>
        </div>

        {{-- Legend --}}
        <div class="flex justify-center space-x-6 mt-6 pt-4 border-t">
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-blue-500 rounded"></div>
                <span class="text-sm text-gray-700">This Month</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-gray-400 rounded"></div>
                <span class="text-sm text-gray-700">Last Month</span>
            </div>
        </div>
    </div>

    {{-- Revenue by Service & Branch --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Revenue by Service --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">💼 Revenue by Service Type</h3>
            <div class="space-y-3">
                <template x-for="service in revenueByService" :key="service.name">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700" x-text="service.name"></span>
                            <span class="font-bold text-green-600" x-text="'Rp ' + service.revenue.toLocaleString('id-ID')"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-6 relative overflow-hidden">
                            <div class="h-6 rounded-full flex items-center px-3 transition-all"
                                 :style="'width: ' + service.percentage + '%; background: linear-gradient(to right, ' + service.color + ', ' + service.colorDark + ')'">
                                <span class="text-white text-xs font-bold" x-show="service.percentage > 15" x-text="service.percentage + '%'"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Revenue by Branch --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">🏢 Revenue by Branch</h3>
            <div class="space-y-3">
                <template x-for="(branch, index) in revenueByBranch" :key="branch.name">
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
                                <span class="text-sm font-bold text-green-600" x-text="'Rp ' + branch.revenue.toLocaleString('id-ID')"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Revenue by Mitra (if filtered) --}}
    <div x-show="filters.mitra" class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl shadow-md border border-purple-200 p-4 sm:p-6">
        <h3 class="text-lg font-semibold text-purple-900 mb-4">🤝 Revenue Breakdown for Selected Mitra</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg p-4">
                <p class="text-xs text-gray-500 mb-1">Total Revenue</p>
                <p class="text-2xl font-bold text-purple-600" x-text="'Rp ' + mitraBreakdown.total.toLocaleString('id-ID')"></p>
            </div>
            <div class="bg-white rounded-lg p-4">
                <p class="text-xs text-gray-500 mb-1">Mitra Share (70%)</p>
                <p class="text-2xl font-bold text-indigo-600" x-text="'Rp ' + mitraBreakdown.mitraShare.toLocaleString('id-ID')"></p>
            </div>
            <div class="bg-white rounded-lg p-4">
                <p class="text-xs text-gray-500 mb-1">Company Share (30%)</p>
                <p class="text-2xl font-bold text-green-600" x-text="'Rp ' + mitraBreakdown.companyShare.toLocaleString('id-ID')"></p>
            </div>
        </div>
    </div>

    {{-- Detailed Transaction Table --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-2 sm:space-y-0">
            <h3 class="text-lg font-semibold text-gray-700">📋 Transaction Details (<span x-text="getFilteredTransactions().length"></span>)</h3>
            <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-600">Show:</label>
                <select x-model="transactionsPerPage" @change="currentPage = 1" class="text-sm border-gray-300 rounded-lg">
                    <option :value="10">10</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                </select>
            </div>
        </div>

        {{-- Transaction Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="transaction in getCurrentPageTransactions()" :key="transaction.id">
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow hover:border-indigo-300">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="font-bold text-indigo-600" x-text="transaction.id"></p>
                            <p class="text-xs text-gray-500" x-text="transaction.date + ' ' + transaction.time"></p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full"
                              :class="transaction.status === 'settlement' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                              x-text="transaction.status">
                        </span>
                    </div>

                    <div class="space-y-2 text-sm mb-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Customer:</span>
                            <span class="font-medium text-gray-900" x-text="transaction.customer"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Service:</span>
                            <span class="font-medium text-gray-900" x-text="transaction.service"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Branch:</span>
                            <span class="font-medium text-gray-900" x-text="transaction.branch"></span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Amount</span>
                            <span class="text-xl font-bold text-green-600" x-text="'Rp ' + transaction.amount.toLocaleString('id-ID')"></span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1" x-text="'via ' + transaction.paymentMethod"></p>
                    </div>
                </div>
            </template>
        </div>

        {{-- Empty State --}}
        <div x-show="getCurrentPageTransactions().length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="mt-2 text-sm text-gray-500">No transactions found for selected filters.</p>
        </div>

        {{-- Pagination --}}
        <div x-show="getCurrentPageTransactions().length > 0" class="mt-6 flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0 border-t pt-4">
            <div class="text-sm text-gray-700">
                Showing <span class="font-medium" x-text="getPaginationInfo().start"></span> 
                to <span class="font-medium" x-text="getPaginationInfo().end"></span> 
                of <span class="font-medium" x-text="getPaginationInfo().total"></span> transactions
            </div>
            <div class="flex items-center space-x-2">
                <button @click="changePage(currentPage - 1)" 
                        :disabled="currentPage === 1"
                        class="px-3 py-2 border rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">
                    Previous
                </button>
                
                <div class="hidden sm:flex items-center space-x-1">
                    <template x-for="page in getPageNumbers()" :key="page">
                        <button @click="changePage(page)"
                                class="px-3 py-2 border rounded-lg text-sm"
                                :class="page === currentPage ? 'bg-indigo-600 text-white border-indigo-600' : 'hover:bg-gray-100'">
                            <span x-text="page"></span>
                        </button>
                    </template>
                </div>

                <div class="sm:hidden px-4 py-2 text-sm font-medium">
                    <span x-text="currentPage"></span> / <span x-text="getTotalPages()"></span>
                </div>
                
                <button @click="changePage(currentPage + 1)" 
                        :disabled="currentPage === getTotalPages()"
                        class="px-3 py-2 border rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">
                    Next
                </button>
            </div>
        </div>
    </div>

</main>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('revenueReport', () => ({
        // --- DATA STATE ---
        filters: {
            startDate: new Date(new Date().setDate(1)).toISOString().split('T')[0], // Awal bulan ini
            endDate: new Date().toISOString().split('T')[0], // Hari ini
            mitra: '',
            branch: '',
            service: '',
            paymentStatus: '',
        },
        quickDateActive: 'month',
        
        metrics: {
            totalRevenue: 0,
            avgTransaction: 0,
            totalTransactions: 0,
            growth: 0,
            mitraRevenue: 0,
            taxPaid: 0,
            taxRate: 10, // Simulasi PPN 10%
            netRevenue: 0,
        },

        comparison: {
            thisMonth: 0,
            lastMonth: 0,
            maxValue: 1,
            weeklyData: [], // { label: 'Week 1', thisMonth: 1000, lastMonth: 900 }
        },
        chartView: 'bar', // 'bar' or 'line'

        revenueByService: [],
        revenueByBranch: [],
        
        mitraBreakdown: {
            total: 0,
            mitraRate: 70, // Simulasi 70%
            companyRate: 30, // Simulasi 30%
            mitraShare: 0,
            companyShare: 0,
        },

        // Data transaksi simulasi (akan difilter dan dipaginasi)
        allTransactions: [],
        
        // State Pagination
        currentPage: 1,
        transactionsPerPage: 10,
        
        // --- INITIALIZATION ---
        init() {
            this.generateDummyData();
            this.generateReport(); // Generate report on initial load
        },

        // --- DUMMY DATA GENERATOR ---
        generateDummyData() {
            // Generasi Data Transaksi Simulasi
            const services = ['Meeting Room', 'Private Office', 'Coworking Space', 'Event Space'];
            const branches = ['Surabaya Center', 'Jakarta Selatan', 'Surabaya Timur', 'Jakarta Barat'];
            const paymentMethods = ['Midtrans', 'Transfer Bank', 'Cash'];
            const customers = ['Ahmad Budi', 'Citra Dewi', 'Eka Fajar', 'Gita Haris', 'Indra Jaya'];
            const statuses = ['settlement', 'pending'];
            const today = new Date();
            const last60Days = new Date(today.getTime() - (60 * 24 * 60 * 60 * 1000));

            for (let i = 1; i <= 100; i++) {
                const randomDate = new Date(last60Days.getTime() + Math.random() * (today.getTime() - last60Days.getTime()));
                const dateString = randomDate.toISOString().split('T')[0];
                const timeString = randomDate.toTimeString().split(' ')[0].substring(0, 5);
                const randomAmount = Math.floor(Math.random() * 900000) + 100000;

                this.allTransactions.push({
                    id: 'TRX-' + Math.floor(Math.random() * 900000 + 100000),
                    date: dateString,
                    time: timeString,
                    amount: randomAmount,
                    customer: customers[Math.floor(Math.random() * customers.length)],
                    service: services[Math.floor(Math.random() * services.length)],
                    branch: branches[Math.floor(Math.random() * branches.length)],
                    status: statuses[Math.floor(Math.random() * statuses.length)],
                    paymentMethod: paymentMethods[Math.floor(Math.random() * paymentMethods.length)],
                    mitra: Math.random() < 0.5 ? 'PT SBY Office' : 'PT JKT Workspace',
                });
            }
        },

        // --- FILTER LOGIC ---
        setQuickDate(period) {
            const today = new Date();
            let start = new Date();
            this.quickDateActive = period;

            if (period === 'today') {
                start = new Date(today);
            } else if (period === 'week') {
                // Senin di minggu ini
                const day = today.getDay();
                start = new Date(today.setDate(today.getDate() - day + (day === 0 ? -6 : 1))); 
            } else if (period === 'month') {
                start = new Date(today.getFullYear(), today.getMonth(), 1);
            } else if (period === 'lastMonth') {
                start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                this.filters.endDate = new Date(today.getFullYear(), today.getMonth(), 0).toISOString().split('T')[0];
            } else if (period === 'year') {
                start = new Date(today.getFullYear(), 0, 1);
            }

            if (period !== 'lastMonth') {
                this.filters.endDate = new Date().toISOString().split('T')[0];
            }
            this.filters.startDate = start.toISOString().split('T')[0];
            this.generateReport();
        },

        resetFilters() {
            this.filters = {
                startDate: new Date(new Date().setDate(1)).toISOString().split('T')[0],
                endDate: new Date().toISOString().split('T')[0],
                mitra: '',
                branch: '',
                service: '',
                paymentStatus: '',
            };
            this.quickDateActive = 'month';
            this.currentPage = 1;
            this.generateReport();
        },

        // --- REPORT GENERATION (CORE LOGIC) ---
        generateReport() {
            const filtered = this.getFilteredTransactions();
            const totalRevenue = filtered.reduce((sum, t) => sum + t.amount, 0);
            
            // 1. Calculate Metrics
            this.metrics.totalRevenue = totalRevenue;
            this.metrics.totalTransactions = filtered.length;
            this.metrics.avgTransaction = filtered.length > 0 ? Math.round(totalRevenue / filtered.length) : 0;
            this.metrics.taxPaid = Math.round(totalRevenue * (this.metrics.taxRate / 100));
            this.metrics.mitraRevenue = Math.round(totalRevenue * (this.mitraBreakdown.mitraRate / 100)); // Simulasi
            this.metrics.netRevenue = totalRevenue - this.metrics.taxPaid - this.metrics.mitraRevenue;
            
            // Simulasi Growth (contoh: pertumbuhan 5% dari periode sebelumnya)
            this.metrics.growth = Math.round(((totalRevenue / 1.05) / totalRevenue - 1) * 100 * 100) / 100;
            if(Math.random() < 0.5) this.metrics.growth *= -1; // Random positif/negatif

            // 2. Generate Comparison Data (Simulasi)
            this.generateComparisonData(filtered);

            // 3. Generate Service & Branch Breakdown
            this.generateBreakdown(filtered);

            // 4. Mitra Breakdown (jika filter mitra aktif)
            if (this.filters.mitra) {
                this.mitraBreakdown.total = totalRevenue;
                this.mitraBreakdown.mitraShare = Math.round(totalRevenue * (this.mitraBreakdown.mitraRate / 100));
                this.mitraBreakdown.companyShare = totalRevenue - this.mitraBreakdown.mitraShare;
            } else {
                 this.mitraBreakdown.total = 0;
            }
            
            this.currentPage = 1; // Reset page after new report
        },

        getFilteredTransactions() {
            const start = new Date(this.filters.startDate).getTime();
            const end = new Date(this.filters.endDate).getTime();
            
            return this.allTransactions.filter(t => {
                const tDate = new Date(t.date).getTime();
                const dateMatch = tDate >= start && tDate <= end;
                const mitraMatch = !this.filters.mitra || t.mitra === this.filters.mitra;
                const branchMatch = !this.filters.branch || t.branch === this.filters.branch;
                const serviceMatch = !this.filters.service || t.service === this.filters.service;
                const statusMatch = !this.filters.paymentStatus || t.status === this.filters.paymentStatus;

                return dateMatch && mitraMatch && branchMatch && serviceMatch && statusMatch;
            }).sort((a, b) => new Date(b.date) - new Date(a.date)); // Sort by date descending
        },
        
        generateComparisonData(currentPeriodTransactions) {
            // Simulasi data untuk "This Month" (sebenarnya data yang difilter)
            this.comparison.thisMonth = currentPeriodTransactions.reduce((sum, t) => sum + t.amount, 0);

            // Simulasi data untuk "Last Month" (contoh: 5% lebih rendah)
            this.comparison.lastMonth = Math.round(this.comparison.thisMonth / 1.05 * (Math.random() * 0.2 + 0.9));

            // Simulasi Data Mingguan
            const weeklyData = [];
            const weeks = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            let maxVal = 0;

            for(let i=0; i < weeks.length; i++) {
                const thisW = Math.round(this.comparison.thisMonth * (0.2 + Math.random() * 0.05));
                const lastW = Math.round(this.comparison.lastMonth * (0.2 + Math.random() * 0.05));
                
                weeklyData.push({
                    label: weeks[i],
                    thisMonth: thisW,
                    lastMonth: lastW
                });
                maxVal = Math.max(maxVal, thisW, lastW);
            }

            this.comparison.weeklyData = weeklyData;
            this.comparison.maxValue = maxVal * 1.1; // Tambah 10% agar batang tidak menyentuh puncak
        },

        generateBreakdown(filtered) {
            const serviceMap = {};
            const branchMap = {};
            let total = this.metrics.totalRevenue;

            // Agregasi
            filtered.forEach(t => {
                serviceMap[t.service] = (serviceMap[t.service] || 0) + t.amount;
                branchMap[t.branch] = (branchMap[t.branch] || 0) + t.amount;
            });

            // Service Breakdown
            const serviceColors = {
                'Meeting Room': { color: 'rgb(59, 130, 246)', colorDark: 'rgb(37, 99, 235)' }, // Blue
                'Private Office': { color: 'rgb(168, 85, 247)', colorDark: 'rgb(109, 40, 217)' }, // Purple
                'Coworking Space': { color: 'rgb(34, 197, 94)', colorDark: 'rgb(22, 163, 74)' }, // Green
                'Event Space': { color: 'rgb(249, 115, 22)', colorDark: 'rgb(234, 88, 12)' }, // Orange
            };
            this.revenueByService = Object.keys(serviceMap).map(name => ({
                name,
                revenue: serviceMap[name],
                percentage: total > 0 ? Math.round((serviceMap[name] / total) * 100) : 0,
                color: serviceColors[name]?.color || 'rgb(107, 114, 128)',
                colorDark: serviceColors[name]?.colorDark || 'rgb(75, 85, 99)',
            })).sort((a, b) => b.revenue - a.revenue);

            // Branch Breakdown
            this.revenueByBranch = Object.keys(branchMap).map(name => ({
                name,
                revenue: branchMap[name],
                percentage: total > 0 ? Math.round((branchMap[name] / total) * 100) : 0,
            })).sort((a, b) => b.revenue - a.revenue);
        },

        // --- EXPORT & PRINT LOGIC ---
        exportReport(type) {
            alert(`Simulasi: Laporan Pendapatan untuk periode ${this.filters.startDate} hingga ${this.filters.endDate} akan diekspor sebagai ${type.toUpperCase()}.`);
        },

        printReport() {
            alert("Simulasi: Membuka dialog cetak untuk Laporan Pendapatan...");
            // window.print(); // Anda dapat menggunakan ini untuk cetak nyata
        },

        // --- PAGINATION LOGIC ---
        getTotalPages() {
            const total = this.getFilteredTransactions().length;
            return Math.ceil(total / this.transactionsPerPage);
        },

        getCurrentPageTransactions() {
            const filtered = this.getFilteredTransactions();
            const start = (this.currentPage - 1) * this.transactionsPerPage;
            const end = start + parseInt(this.transactionsPerPage); // Pastikan transactionsPerPage adalah angka
            return filtered.slice(start, end);
        },

        changePage(page) {
            if (page >= 1 && page <= this.getTotalPages()) {
                this.currentPage = page;
            }
        },

        getPaginationInfo() {
            const total = this.getFilteredTransactions().length;
            const start = (this.currentPage - 1) * this.transactionsPerPage + 1;
            const end = Math.min(this.currentPage * this.transactionsPerPage, total);
            
            return {
                start,
                end,
                total
            };
        },

        getPageNumbers() {
            const totalPages = this.getTotalPages();
            const currentPage = this.currentPage;
            const pages = [];

            // Tampilkan 5 halaman di tengah
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);

            if (currentPage <= 3) {
                endPage = Math.min(totalPages, 5);
            }
            if (currentPage > totalPages - 2) {
                startPage = Math.max(1, totalPages - 4);
            }

            for (let i = startPage; i <= endPage; i++) {
                pages.push(i);
            }
            return pages;
        }
    }));
});
</script>
@endsection