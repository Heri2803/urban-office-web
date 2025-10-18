{{-- resources/views/superadmin/reports/export.blade.php --}}
@extends('layouts.superadmin')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Export Reports</h1>
        <p class="text-gray-600 mt-1 text-sm md:text-base">Generate and download reports for analysis</p>
    </div>

    {{-- Main Content --}}
    <div class="bg-white rounded-lg shadow-sm">
        {{-- Filter Section --}}
        <div class="border-b border-gray-200 p-4 md:p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Report Configuration</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Report Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                    <select id="reportType" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="booking">Booking Report</option>
                        <option value="revenue">Revenue Report</option>
                        <option value="occupancy">Occupancy Report</option>
                        <option value="customer">Customer Report</option>
                        <option value="voucher">Voucher Report</option>
                    </select>
                </div>

                {{-- Date Range From --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input type="date" id="dateFrom" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="2025-10-01">
                </div>

                {{-- Date Range To --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input type="date" id="dateTo" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="2025-10-17">
                </div>

                {{-- Mitra Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mitra</label>
                    <select id="mitraFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Mitra</option>
                        <option value="mitra-a">Mitra A - PT. ABC Corp</option>
                        <option value="mitra-b">Mitra B - CV. XYZ Ltd</option>
                        <option value="mitra-c">Mitra C - PT. DEF Inc</option>
                    </select>
                </div>

                {{-- Branch Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Branch</label>
                    <select id="branchFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Branches</option>
                        <option value="surabaya">Surabaya</option>
                        <option value="jakarta">Jakarta</option>
                        <option value="bandung">Bandung</option>
                        <option value="bali">Bali</option>
                    </select>
                </div>

                {{-- Service Type Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Type</label>
                    <select id="serviceFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Services</option>
                        <option value="meeting-room">Meeting Room</option>
                        <option value="private-office">Private Office</option>
                        <option value="sharing-room">Sharing Room</option>
                        <option value="coworking-space">Coworking Space</option>
                        <option value="virtual-office">Virtual Office</option>
                        <option value="event-space">Event Space</option>
                    </select>
                </div>

                {{-- Status Filter (for Booking Report) --}}
                <div id="statusFilter">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="statusSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="settlement">Settlement</option>
                        <option value="pending">Pending</option>
                        <option value="expired">Expired</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                {{-- Payment Method Filter (for Revenue Report) --}}
                <div id="paymentFilter" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select id="paymentSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Methods</option>
                        <option value="va">Virtual Account</option>
                        <option value="cc">Credit Card</option>
                        <option value="qris">QRIS</option>
                        <option value="ewallet">E-Wallet</option>
                    </select>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 mt-6">
                <button id="resetBtn" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    Reset Filters
                </button>
                <button id="generateBtn" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    Generate Report
                </button>
            </div>
        </div>

        {{-- Preview Section --}}
        <div class="p-4 md:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-2 sm:mb-0">
                    <span id="reportTitle">Booking Report</span> Preview
                </h2>
                <div class="flex items-center gap-4">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium text-blue-600" id="totalRecords">156</span> records found
                    </div>
                    <select id="perPageSelect" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="5">5 per page</option>
                        <option value="10" selected>10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div id="summaryCards" class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-gray-900" id="stat1Value">156</div>
                    <div class="text-sm text-gray-600 mt-1" id="stat1Label">Total Bookings</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600" id="stat2Value">142</div>
                    <div class="text-sm text-gray-600 mt-1" id="stat2Label">Completed</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-600" id="stat3Value">Rp 42.3M</div>
                    <div class="text-sm text-gray-600 mt-1" id="stat3Label">Total Revenue</div>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-purple-600" id="stat4Value">68%</div>
                    <div class="text-sm text-gray-600 mt-1" id="stat4Label">Occupancy Rate</div>
                </div>
            </div>

            {{-- Data Table --}}
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr id="tableHeader">
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">Booking ID</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">Customer</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[12%]">Mitra</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">Branch</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">Service</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">Date</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[13%]">Amount</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="tableBody">
                            {{-- Data will be loaded via JavaScript --}}
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-sm text-gray-600" id="paginationInfo">Showing 1-10 of 156 records</p>
                    <div class="flex gap-2" id="paginationButtons">
                        <button class="px-3 py-1.5 border border-gray-300 rounded text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed" id="prevBtn" disabled>Previous</button>
                        <div class="flex gap-1" id="pageNumbers"></div>
                        <button class="px-3 py-1.5 border border-gray-300 rounded text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed" id="nextBtn">Next</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Download Section --}}
        <div class="border-t border-gray-200 p-4 md:p-6 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Download Options</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <button class="flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Excel
                </button>
                
                <button class="flex items-center justify-center px-4 py-3 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Download PDF
                </button>
                
                <button class="flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download CSV
                </button>
                
                <button class="flex items-center justify-center px-4 py-3 bg-gray-600 text-white rounded-lg text-sm font-medium hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                    </svg>
                    Share Report
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Sample data untuk simulasi
    const sampleData = [
        { id: 'BK-001', customer: 'John Doe', branch: 'surabaya', service: 'meeting-room', date: '2025-10-15', amount: 'Rp 180,000', status: 'completed', mitra: 'mitra-a' },
        { id: 'BK-002', customer: 'Jane Smith', branch: 'jakarta', service: 'private-office', date: '2025-10-15', amount: 'Rp 1,500,000', status: 'settlement', mitra: 'mitra-a' },
        { id: 'BK-003', customer: 'Mike Johnson', branch: 'bandung', service: 'event-space', date: '2025-10-14', amount: 'Rp 850,000', status: 'pending', mitra: 'mitra-b' },
        { id: 'BK-004', customer: 'Sarah Lee', branch: 'surabaya', service: 'coworking-space', date: '2025-10-14', amount: 'Rp 300,000', status: 'completed', mitra: 'mitra-a' },
        { id: 'BK-005', customer: 'David Chen', branch: 'jakarta', service: 'virtual-office', date: '2025-10-13', amount: 'Rp 2,000,000', status: 'completed', mitra: 'mitra-c' },
        { id: 'BK-006', customer: 'Lisa Wang', branch: 'bali', service: 'meeting-room', date: '2025-10-13', amount: 'Rp 200,000', status: 'completed', mitra: 'mitra-b' },
        { id: 'BK-007', customer: 'Tom Brown', branch: 'surabaya', service: 'sharing-room', date: '2025-10-12', amount: 'Rp 500,000', status: 'settlement', mitra: 'mitra-a' },
        { id: 'BK-008', customer: 'Anna Garcia', branch: 'jakarta', service: 'event-space', date: '2025-10-12', amount: 'Rp 1,200,000', status: 'cancelled', mitra: 'mitra-c' },
        { id: 'BK-009', customer: 'Chris Wilson', branch: 'bandung', service: 'meeting-room', date: '2025-10-11', amount: 'Rp 150,000', status: 'completed', mitra: 'mitra-b' },
        { id: 'BK-010', customer: 'Emma Davis', branch: 'bali', service: 'private-office', date: '2025-10-11', amount: 'Rp 1,800,000', status: 'completed', mitra: 'mitra-b' },
        { id: 'BK-011', customer: 'Robert Taylor', branch: 'surabaya', service: 'coworking-space', date: '2025-10-10', amount: 'Rp 350,000', status: 'pending', mitra: 'mitra-a' },
        { id: 'BK-012', customer: 'Maria Martinez', branch: 'jakarta', service: 'virtual-office', date: '2025-10-10', amount: 'Rp 2,200,000', status: 'settlement', mitra: 'mitra-c' },
        { id: 'BK-013', customer: 'James Anderson', branch: 'bandung', service: 'meeting-room', date: '2025-10-09', amount: 'Rp 180,000', status: 'expired', mitra: 'mitra-b' },
        { id: 'BK-014', customer: 'Linda Thomas', branch: 'bali', service: 'event-space', date: '2025-10-09', amount: 'Rp 900,000', status: 'completed', mitra: 'mitra-b' },
        { id: 'BK-015', customer: 'Kevin White', branch: 'surabaya', service: 'sharing-room', date: '2025-10-08', amount: 'Rp 600,000', status: 'completed', mitra: 'mitra-a' }
    ];

    // State management
    let currentPage = 1;
    let perPage = 10;
    let filteredData = [...sampleData];

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        renderTable();
        setupEventListeners();
    });

    function setupEventListeners() {
        // Report type change
        document.getElementById('reportType').addEventListener('change', handleReportTypeChange);
        
        // All filters
        ['dateFrom', 'dateTo', 'mitraFilter', 'branchFilter', 'serviceFilter', 'statusSelect', 'paymentSelect'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('change', applyFilters);
            }
        });

        // Per page change
        document.getElementById('perPageSelect').addEventListener('change', function() {
            perPage = parseInt(this.value);
            currentPage = 1;
            renderTable();
        });

        // Reset button
        document.getElementById('resetBtn').addEventListener('click', resetFilters);

        // Generate button
        document.getElementById('generateBtn').addEventListener('click', applyFilters);

        // Pagination
        document.getElementById('prevBtn').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });

        document.getElementById('nextBtn').addEventListener('click', () => {
            const totalPages = Math.ceil(filteredData.length / perPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        });
    }

    function handleReportTypeChange() {
        const reportType = this.value;
        const reportTitle = document.getElementById('reportTitle');
        const statusFilter = document.getElementById('statusFilter');
        const paymentFilter = document.getElementById('paymentFilter');
        
        const titles = {
            'booking': 'Booking Report',
            'revenue': 'Revenue Report',
            'occupancy': 'Occupancy Report',
            'customer': 'Customer Report',
            'voucher': 'Voucher Report'
        };
        reportTitle.textContent = titles[reportType];
        
        if (reportType === 'revenue') {
            statusFilter.classList.add('hidden');
            paymentFilter.classList.remove('hidden');
        } else {
            statusFilter.classList.remove('hidden');
            paymentFilter.classList.add('hidden');
        }
        
        updateTableHeaders(reportType);
        applyFilters();
    }

    function applyFilters() {
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        const mitra = document.getElementById('mitraFilter').value;
        const branch = document.getElementById('branchFilter').value;
        const service = document.getElementById('serviceFilter').value;
        const status = document.getElementById('statusSelect').value;

        filteredData = sampleData.filter(item => {
            let match = true;

            if (dateFrom && item.date < dateFrom) match = false;
            if (dateTo && item.date > dateTo) match = false;
            if (mitra && item.mitra !== mitra) match = false;
            if (branch && item.branch !== branch) match = false;
            if (service && item.service !== service) match = false;
            if (status && item.status !== status) match = false;

            return match;
        });

        currentPage = 1;
        updateStats();
        renderTable();
    }

    function resetFilters() {
        document.getElementById('dateFrom').value = '2025-10-01';
        document.getElementById('dateTo').value = '2025-10-17';
        document.getElementById('mitraFilter').value = '';
        document.getElementById('branchFilter').value = '';
        document.getElementById('serviceFilter').value = '';
        document.getElementById('statusSelect').value = '';
        document.getElementById('paymentSelect').value = '';
        document.getElementById('reportType').value = 'booking';
        
        filteredData = [...sampleData];
        currentPage = 1;
        updateStats();
        renderTable();
        handleReportTypeChange.call(document.getElementById('reportType'));
    }

    function updateStats() {
        const completed = filteredData.filter(d => d.status === 'completed').length;
        const totalAmount = filteredData.length * 500000; // Simulasi

        document.getElementById('stat1Value').textContent = filteredData.length;
        document.getElementById('stat2Value').textContent = completed;
        document.getElementById('stat3Value').textContent = `Rp ${(totalAmount / 1000000).toFixed(1)}M`;
        document.getElementById('totalRecords').textContent = filteredData.length;
    }

    function renderTable() {
        const tableBody = document.getElementById('tableBody');
        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const pageData = filteredData.slice(start, end);

        const statusColors = {
            'completed': 'bg-green-100 text-green-800',
            'settlement': 'bg-blue-100 text-blue-800',
            'pending': 'bg-yellow-100 text-yellow-800',
            'cancelled': 'bg-red-100 text-red-800',
            'expired': 'bg-gray-100 text-gray-800'
        };

        const branchNames = {
            'surabaya': 'Surabaya',
            'jakarta': 'Jakarta',
            'bandung': 'Bandung',
            'bali': 'Bali'
        };

        const serviceNames = {
            'meeting-room': 'Meeting Room',
            'private-office': 'Private Office',
            'sharing-room': 'Sharing Room',
            'coworking-space': 'Coworking Space',
            'virtual-office': 'Virtual Office',
            'event-space': 'Event Space'
        };

        const mitraNames = {
            'mitra-a': 'Mitra A - PT. ABC',
            'mitra-b': 'Mitra B - CV. XYZ',
            'mitra-c': 'Mitra C - PT. DEF'
        };

        tableBody.innerHTML = pageData.map(item => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-3 py-3 text-sm font-medium text-gray-900">${item.id}</td>
                <td class="px-3 py-3 text-sm text-gray-700">${item.customer}</td>
                <td class="px-3 py-3 text-sm text-gray-700">${mitraNames[item.mitra]}</td>
                <td class="px-3 py-3 text-sm text-gray-700">${branchNames[item.branch]}</td>
                <td class="px-3 py-3 text-sm text-gray-700">${serviceNames[item.service]}</td>
                <td class="px-3 py-3 text-sm text-gray-700">${item.date}</td>
                <td class="px-3 py-3 text-sm text-gray-700">${item.amount}</td>
                <td class="px-3 py-3">
                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full ${statusColors[item.status]}">
                        ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
                    </span>
                </td>
            </tr>
        `).join('');

        renderPagination();
    }

    function renderPagination() {
        const totalPages = Math.ceil(filteredData.length / perPage);
        const start = (currentPage - 1) * perPage + 1;
        const end = Math.min(start + perPage - 1, filteredData.length);

        document.getElementById('paginationInfo').textContent = 
            `Showing ${start}-${end} of ${filteredData.length} records`;

        document.getElementById('prevBtn').disabled = currentPage === 1;
        document.getElementById('nextBtn').disabled = currentPage === totalPages;

        const pageNumbers = document.getElementById('pageNumbers');
        let pages = [];

        if (totalPages <= 5) {
            pages = Array.from({ length: totalPages }, (_, i) => i + 1);
        } else {
            if (currentPage <= 3) {
                pages = [1, 2, 3, 4, '...', totalPages];
            } else if (currentPage >= totalPages - 2) {
                pages = [1, '...', totalPages - 3, totalPages - 2, totalPages - 1, totalPages];
            } else {
                pages = [1, '...', currentPage - 1, currentPage, currentPage + 1, '...', totalPages];
            }
        }

        pageNumbers.innerHTML = pages.map(page => {
            if (page === '...') {
                return '<span class="px-3 py-1.5 text-sm text-gray-500">...</span>';
            }
            const isActive = page === currentPage;
            return `
                <button 
                    class="px-3 py-1.5 border rounded text-sm font-medium transition-colors ${
                        isActive 
                            ? 'bg-blue-600 text-white border-blue-600' 
                            : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                    }"
                    onclick="goToPage(${page})"
                >
                    ${page}
                </button>
            `;
        }).join('');
    }

    function goToPage(page) {
        currentPage = page;
        renderTable();
    }

    function updateTableHeaders(reportType) {
        const tableHeader = document.getElementById('tableHeader');
        const headers = {
            'booking': [
                { name: 'Booking ID', width: '10%' },
                { name: 'Customer', width: '15%' },
                { name: 'Mitra', width: '12%' },
                { name: 'Branch', width: '10%' },
                { name: 'Service', width: '15%' },
                { name: 'Date', width: '10%' },
                { name: 'Amount', width: '13%' },
                { name: 'Status', width: '15%' }
            ],
            'revenue': [
                { name: 'Date', width: '12%' },
                { name: 'Mitra', width: '15%' },
                { name: 'Branch', width: '12%' },
                { name: 'Service', width: '15%' },
                { name: 'Bookings', width: '10%' },
                { name: 'Gross Revenue', width: '12%' },
                { name: 'Discount', width: '12%' },
                { name: 'Net Revenue', width: '12%' }
            ],
            'occupancy': [
                { name: 'Room No', width: '10%' },
                { name: 'Service', width: '15%' },
                { name: 'Mitra', width: '12%' },
                { name: 'Branch', width: '10%' },
                { name: 'Occupancy %', width: '12%' },
                { name: 'Bookings', width: '12%' },
                { name: 'Revenue', width: '15%' },
                { name: 'Avg Duration', width: '14%' }
            ],
            'customer': [
                { name: 'Customer Name', width: '15%' },
                { name: 'Email', width: '18%' },
                { name: 'Mitra', width: '12%' },
                { name: 'Branch', width: '10%' },
                { name: 'Total Bookings', width: '12%' },
                { name: 'Total Spent', width: '12%' },
                { name: 'Last Booking', width: '11%' },
                { name: 'Type', width: '10%' }
            ],
            'voucher': [
                { name: 'Voucher Code', width: '15%' },
                { name: 'Type', width: '12%' },
                { name: 'Discount', width: '12%' },
                { name: 'Used', width: '10%' },
                { name: 'Limit', width: '10%' },
                { name: 'Revenue Impact', width: '15%' },
                { name: 'Branch', width: '12%' },
                { name: 'Status', width: '14%' }
            ]
        };
        
        const headerHTML = headers[reportType].map(h => 
            `<th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: ${h.width}">${h.name}</th>`
        ).join('');
        
        tableHeader.innerHTML = headerHTML;
    }
</script>
@endsection