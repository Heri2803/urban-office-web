{{-- resources/views/superadmin/pricing/approval-requests.blade.php --}}
@extends('layouts.superadmin')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">✅ Pricing Approval Requests</h1>
                <p class="text-gray-600 mt-1 text-sm md:text-base">Review and approve branch pricing changes</p>
            </div>
            <div class="flex gap-2">
                <button onclick="bulkApprove()" id="bulkApproveBtn" disabled class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="bulkApproveText">Approve Selected</span>
                </button>
                <button onclick="bulkReject()" id="bulkRejectBtn" disabled class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="bulkRejectText">Reject Selected</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Quick Filter Tabs --}}
    <div class="bg-white rounded-lg shadow-sm p-3 mb-6">
        <div class="flex flex-wrap gap-2">
            <button onclick="filterByStatus('all')" class="status-filter-btn px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium transition-colors">
                All (<span id="countAll">15</span>)
            </button>
            <button onclick="filterByStatus('pending')" class="status-filter-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                Pending (<span id="countPending">8</span>)
            </button>
            <button onclick="filterByStatus('approved')" class="status-filter-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                Approved (<span id="countApproved">5</span>)
            </button>
            <button onclick="filterByStatus('rejected')" class="status-filter-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                Rejected (<span id="countRejected">2</span>)
            </button>
        </div>
    </div>

    {{-- Advanced Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <select id="branchFilter" onchange="applyFilters()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Branches</option>
                <option value="surabaya">Surabaya</option>
                <option value="jakarta">Jakarta</option>
                <option value="bandung">Bandung</option>
                <option value="bali">Bali</option>
            </select>
            
            <select id="serviceFilter" onchange="applyFilters()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Services</option>
                <option value="meeting-room">Meeting Room</option>
                <option value="private-office">Private Office</option>
                <option value="sharing-room">Sharing Room</option>
                <option value="coworking-space">Coworking Space</option>
                <option value="virtual-office">Virtual Office</option>
                <option value="event-space">Event Space</option>
            </select>
            
            <select id="dateRangeFilter" onchange="applyFilters()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="all">All Time</option>
                <option value="today">Today</option>
                <option value="week" selected>This Week</option>
                <option value="month">This Month</option>
            </select>
            
            <button onclick="resetFilters()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                Reset Filters
            </button>
        </div>
    </div>

    {{-- Requests Table/Cards --}}
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">
                <span id="filterStatusTitle">Pending Approval</span> Requests (<span id="requestCount">8</span>)
            </h2>
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price Change</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Impact</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="requestsTableBody">
                    {{-- Rendered by JavaScript --}}
                </tbody>
            </table>
        </div>

        {{-- Mobile/Tablet Card View --}}
        <div class="lg:hidden space-y-4" id="requestsCardView">
            {{-- Rendered by JavaScript --}}
        </div>

        {{-- Empty State --}}
        <div id="emptyState" class="hidden text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500 text-lg font-medium">No requests found</p>
            <p class="text-gray-400 text-sm mt-2">All pricing requests have been processed</p>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Approvals/Rejections</h2>
        <div class="space-y-3" id="recentActivity">
            {{-- Rendered by JavaScript --}}
        </div>
    </div>
</div>

{{-- Review Request Modal --}}
<div id="reviewModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <div onclick="closeReviewModal()" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
        
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-3xl p-6 sm:p-8 my-8 max-h-[90vh] overflow-y-auto transform transition-all">
            <div class="flex items-center justify-between border-b pb-3 mb-6">
                <h3 class="text-xl font-bold text-gray-800">Review Pricing Request</h3>
                <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="reviewContent">
                {{-- Dynamic content loaded by JavaScript --}}
            </div>
        </div>
    </div>
</div>

{{-- Bulk Action Confirmation Modal --}}
<div id="bulkConfirmModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div onclick="closeBulkConfirmModal()" class="fixed inset-0 bg-black bg-opacity-50"></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
            <div class="text-center">
                <svg id="bulkIcon" class="w-16 h-16 mx-auto mb-4"></svg>
                <h3 class="text-lg font-bold text-gray-900 mb-2" id="bulkConfirmTitle"></h3>
                <p class="text-sm text-gray-600 mb-6" id="bulkConfirmMessage"></p>
                <div class="flex gap-3">
                    <button onclick="closeBulkConfirmModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50">
                        Cancel
                    </button>
                    <button onclick="confirmBulkAction()" id="bulkConfirmBtn" class="flex-1 px-4 py-2 rounded-lg font-medium text-white"></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentFilter = 'pending';
    let selectedRequests = [];
    let bulkActionType = '';
    
    // Sample Requests Data
    let requestsData = [
        { 
            id: 1, 
            date: '2025-10-16', 
            time: '10:30',
            branch: 'surabaya', 
            branchName: 'Surabaya',
            service: 'meeting-room',
            serviceName: 'Meeting Room',
            packageName: '1 Hour Package',
            masterPrice: 150000,
            requestedPrice: 120000,
            difference: -20,
            status: 'pending',
            reason: 'Competitor (SpaceHub) nearby offering Rp 100,000 for 1 hour meeting room. Need to stay competitive. Customer feedback shows our current pricing is too high for the area.',
            requestedBy: 'Admin Budi',
            email: 'budi@surabaya.com',
            documents: ['competitor_pricing.pdf', 'customer_feedback.xlsx'],
            effectiveStart: 'immediate',
            effectiveEnd: '2025-12-31',
            currentBookings: 45,
            estimatedRevenueLoss: -1350000,
            potentialVolumeIncrease: 25
        },
        { 
            id: 2, 
            date: '2025-10-16', 
            time: '09:15',
            branch: 'jakarta', 
            branchName: 'Jakarta',
            service: 'private-office',
            serviceName: 'Private Office',
            packageName: '1 Month',
            masterPrice: 3000000,
            requestedPrice: 3500000,
            difference: 16.7,
            status: 'pending',
            reason: 'High demand in Jakarta CBD area. Limited availability. Market research shows competitors charging Rp 3.8M - 4.2M for similar space.',
            requestedBy: 'Admin Siti',
            email: 'siti@jakarta.com',
            documents: ['market_research.pdf'],
            effectiveStart: 'immediate',
            effectiveEnd: null,
            currentBookings: 8,
            estimatedRevenueLoss: 4000000,
            potentialVolumeIncrease: 0
        },
        { 
            id: 3, 
            date: '2025-10-15', 
            time: '14:20',
            branch: 'bandung', 
            branchName: 'Bandung',
            service: 'event-space',
            serviceName: 'Event Space',
            packageName: 'Full Day (8 Hours)',
            masterPrice: 4500000,
            requestedPrice: 5000000,
            difference: 11.1,
            status: 'pending',
            reason: 'Increased operational costs. Recent facility upgrades including new sound system and LED wall. Premium positioning in Bandung market.',
            requestedBy: 'Admin Andi',
            email: 'andi@bandung.com',
            documents: [],
            effectiveStart: '2025-11-01',
            effectiveEnd: null,
            currentBookings: 12,
            estimatedRevenueLoss: 6000000,
            potentialVolumeIncrease: -10
        },
        { 
            id: 4, 
            date: '2025-10-15', 
            time: '11:45',
            branch: 'bali', 
            branchName: 'Bali',
            service: 'coworking-space',
            serviceName: 'Coworking Space',
            packageName: 'Daily Pass',
            masterPrice: 75000,
            requestedPrice: 65000,
            difference: -13.3,
            status: 'pending',
            reason: 'Tourist season low demand. Many coworking spaces offering promotional rates. Need to match market to maintain occupancy.',
            requestedBy: 'Admin Putu',
            email: 'putu@bali.com',
            documents: ['competitor_analysis.pdf'],
            effectiveStart: 'immediate',
            effectiveEnd: '2025-12-20',
            currentBookings: 30,
            estimatedRevenueLoss: -300000,
            potentialVolumeIncrease: 40
        },
        { 
            id: 5, 
            date: '2025-10-14', 
            time: '16:00',
            branch: 'surabaya', 
            branchName: 'Surabaya',
            service: 'meeting-room',
            serviceName: 'Meeting Room',
            packageName: '4 Hours Package',
            masterPrice: 500000,
            requestedPrice: 450000,
            difference: -10,
            status: 'approved',
            reason: 'Corporate client bulk booking discount',
            requestedBy: 'Admin Budi',
            approvedBy: 'Super Admin',
            approvedAt: '2025-10-15 09:30',
            comment: 'Approved for Q4 2025 promotion period'
        },
        { 
            id: 6, 
            date: '2025-10-13', 
            time: '10:20',
            branch: 'jakarta', 
            branchName: 'Jakarta',
            service: 'virtual-office',
            serviceName: 'Virtual Office',
            packageName: 'Standard (1 Year)',
            masterPrice: 2400000,
            requestedPrice: 2000000,
            difference: -16.7,
            status: 'rejected',
            reason: 'End of year promotion',
            requestedBy: 'Admin Siti',
            rejectedBy: 'Super Admin',
            rejectedAt: '2025-10-14 11:15',
            comment: 'Discount too high. Maximum allowed is 15%. Please resubmit with adjusted pricing.'
        }
    ];

    // Recent Activity (last 5)
    const recentActivity = [
        { date: '2025-10-15 09:30', branch: 'Surabaya', package: 'Meeting Room - 4 Hours', action: 'approved', by: 'Super Admin', change: '-10%' },
        { date: '2025-10-14 11:15', branch: 'Jakarta', package: 'Virtual Office - Standard', action: 'rejected', by: 'Super Admin', change: '-16.7%' },
        { date: '2025-10-13 14:45', branch: 'Bandung', package: 'Coworking Space - Monthly', action: 'approved', by: 'Super Admin', change: '-8%' },
        { date: '2025-10-12 16:20', branch: 'Bali', package: 'Event Space - Half Day', action: 'approved', by: 'Super Admin', change: '+5%' },
        { date: '2025-10-11 10:00', branch: 'Surabaya', package: 'Private Office - 3 Months', action: 'rejected', by: 'Super Admin', change: '+20%' }
    ];

    document.addEventListener('DOMContentLoaded', function() {
        renderRequests();
        renderRecentActivity();
        updateCounts();
    });

    function filterByStatus(status) {
        currentFilter = status;
        
        document.querySelectorAll('.status-filter-btn').forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white');
            btn.classList.add('bg-gray-100', 'text-gray-700');
        });
        
        event.target.classList.remove('bg-gray-100', 'text-gray-700');
        event.target.classList.add('bg-blue-600', 'text-white');
        
        const titles = {
            'all': 'All',
            'pending': 'Pending Approval',
            'approved': 'Approved',
            'rejected': 'Rejected'
        };
        document.getElementById('filterStatusTitle').textContent = titles[status];
        
        renderRequests();
    }

    function applyFilters() {
        renderRequests();
    }

    function resetFilters() {
        document.getElementById('branchFilter').value = '';
        document.getElementById('serviceFilter').value = '';
        document.getElementById('dateRangeFilter').value = 'week';
        renderRequests();
    }

    function getFilteredRequests() {
        let filtered = requestsData;
        
        if (currentFilter !== 'all') {
            filtered = filtered.filter(r => r.status === currentFilter);
        }
        
        const branchFilter = document.getElementById('branchFilter').value;
        if (branchFilter) {
            filtered = filtered.filter(r => r.branch === branchFilter);
        }
        
        const serviceFilter = document.getElementById('serviceFilter').value;
        if (serviceFilter) {
            filtered = filtered.filter(r => r.service === serviceFilter);
        }
        
        return filtered;
    }

    function renderRequests() {
        const filtered = getFilteredRequests();
        const tableBody = document.getElementById('requestsTableBody');
        const cardView = document.getElementById('requestsCardView');
        const emptyState = document.getElementById('emptyState');
        
        document.getElementById('requestCount').textContent = filtered.length;
        
        if (filtered.length === 0) {
            tableBody.innerHTML = '';
            cardView.innerHTML = '';
            emptyState.classList.remove('hidden');
            document.getElementById('selectAll').checked = false;
            return;
        }
        
        emptyState.classList.add('hidden');
        
        // Desktop Table
        tableBody.innerHTML = filtered.map(req => {
            const impactClass = Math.abs(req.difference) > 15 ? 'text-red-600' : Math.abs(req.difference) > 10 ? 'text-yellow-600' : 'text-green-600';
            const impactIcon = Math.abs(req.difference) > 15 ? '🔴' : Math.abs(req.difference) > 10 ? '🟡' : '🟢';
            const diffSign = req.difference > 0 ? '+' : '';
            
            return `
                <tr class="hover:bg-gray-50 transition-colors ${selectedRequests.includes(req.id) ? 'bg-blue-50' : ''}">
                    <td class="px-4 py-3">
                        ${req.status === 'pending' ? `
                            <input type="checkbox" class="request-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                data-id="${req.id}" 
                                ${selectedRequests.includes(req.id) ? 'checked' : ''}
                                onchange="toggleRequestSelection(${req.id})">
                        ` : '<span class="text-gray-300">—</span>'}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700">
                        ${req.date}<br>
                        <span class="text-xs text-gray-500">${req.time}</span>
                    </td>
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">${req.branchName}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">${req.serviceName}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">${req.packageName}</td>
                    <td class="px-4 py-3 text-sm">
                        <div>Rp ${req.masterPrice.toLocaleString('id-ID')} →</div>
                        <div class="font-semibold">Rp ${req.requestedPrice.toLocaleString('id-ID')}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-center">
                        <span class="${impactClass} font-semibold">${impactIcon} ${diffSign}${req.difference}%</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        ${req.status === 'pending' ? `
                            <button onclick="reviewRequest(${req.id})" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                                Review
                            </button>
                        ` : req.status === 'approved' ? `
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">✅ Approved</span>
                        ` : `
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">❌ Rejected</span>
                        `}
                    </td>
                </tr>
            `;
        }).join('');
        
        // Mobile/Tablet Cards
        cardView.innerHTML = filtered.map(req => {
            const impactClass = Math.abs(req.difference) > 15 ? 'text-red-600' : Math.abs(req.difference) > 10 ? 'text-yellow-600' : 'text-green-600';
            const impactIcon = Math.abs(req.difference) > 15 ? '🔴' : Math.abs(req.difference) > 10 ? '🟡' : '🟢';
            const diffSign = req.difference > 0 ? '+' : '';
            
            return `
                <div class="bg-white border border-gray-200 rounded-lg p-4 ${selectedRequests.includes(req.id) ? 'border-blue-500 bg-blue-50' : ''}">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-start gap-3 flex-1">
                            ${req.status === 'pending' ? `
                                <input type="checkbox" class="request-checkbox mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                    data-id="${req.id}" 
                                    ${selectedRequests.includes(req.id) ? 'checked' : ''}
                                    onchange="toggleRequestSelection(${req.id})">
                            ` : ''}
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">${req.branchName} - ${req.serviceName}</h3>
                                <p class="text-sm text-gray-600 mt-1">${req.packageName}</p>
                            </div>
                        </div>
                        <span class="${impactClass} font-bold text-lg">${impactIcon}</span>
                    </div>
                    
                    <div class="space-y-2 mb-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Date:</span>
                            <span class="text-gray-700">${req.date} ${req.time}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Current Price:</span>
                            <span class="text-gray-900">Rp ${req.masterPrice.toLocaleString('id-ID')}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Requested Price:</span>
                            <span class="font-semibold text-gray-900">Rp ${req.requestedPrice.toLocaleString('id-ID')}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Change:</span>
                            <span class="${impactClass} font-semibold">${diffSign}${req.difference}%</span>
                        </div>
                    </div>
                    
                    ${req.status === 'pending' ? `
                        <button onclick="reviewRequest(${req.id})" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                            Review Request
                        </button>
                    ` : req.status === 'approved' ? `
                        <div class="text-center">
                            <span class="px-3 py-1.5 inline-flex text-xs font-medium rounded-full bg-green-100 text-green-800">✅ Approved by ${req.approvedBy}</span>
                        </div>
                    ` : `
                        <div class="text-center">
                            <span class="px-3 py-1.5 inline-flex text-xs font-medium rounded-full bg-red-100 text-red-800">❌ Rejected by ${req.rejectedBy}</span>
                        </div>
                    `}
                </div>
            `;
        }).join('');
    }

    function updateCounts() {
        document.getElementById('countAll').textContent = requestsData.length;
        document.getElementById('countPending').textContent = requestsData.filter(r => r.status === 'pending').length;
        document.getElementById('countApproved').textContent = requestsData.filter(r => r.status === 'approved').length;
        document.getElementById('countRejected').textContent = requestsData.filter(r => r.status === 'rejected').length;
    }

    function toggleSelectAll() {
        const isChecked = document.getElementById('selectAll').checked;
        const pendingRequests = getFilteredRequests().filter(r => r.status === 'pending');
        
        if (isChecked) {
            selectedRequests = pendingRequests.map(r => r.id);
        } else {
            selectedRequests = [];
        }
        
        updateBulkButtons();
        renderRequests();
    }

    function toggleRequestSelection(id) {
        if (selectedRequests.includes(id)) {
            selectedRequests = selectedRequests.filter(rid => rid !== id);
        } else {
            selectedRequests.push(id);
        }
        
        const pendingRequests = getFilteredRequests().filter(r => r.status === 'pending');
        document.getElementById('selectAll').checked = selectedRequests.length === pendingRequests.length && pendingRequests.length > 0;
        
        updateBulkButtons();
        renderRequests();
    }

    function updateBulkButtons() {
        const hasSelection = selectedRequests.length > 0;
        document.getElementById('bulkApproveBtn').disabled = !hasSelection;
        document.getElementById('bulkRejectBtn').disabled = !hasSelection;
        document.getElementById('bulkApproveText').textContent = hasSelection ? `Approve (${selectedRequests.length})` : 'Approve Selected';
        document.getElementById('bulkRejectText').textContent = hasSelection ? `Reject (${selectedRequests.length})` : 'Reject Selected';
    }

    function reviewRequest(id) {
        const request = requestsData.find(r => r.id === id);
        if (!request) return;
        
        const diffSign = request.difference > 0 ? '+' : '';
        const diffAmount = request.requestedPrice - request.masterPrice;
        const impactClass = Math.abs(request.difference) > 15 ? 'text-red-600' : Math.abs(request.difference) > 10 ? 'text-yellow-600' : 'text-green-600';
        const impactIcon = Math.abs(request.difference) > 15 ? '🔴 High Impact' : Math.abs(request.difference) > 10 ? '🟡 Medium Impact' : '🟢 Low Impact';
        
        document.getElementById('reviewContent').innerHTML = `
            <div class="space-y-6">
                {{-- Request Details --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Request Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div><strong>ID:</strong> PR-2025-${String(request.id).padStart(3, '0')}</div>
                        <div><strong>Submitted:</strong> ${request.date} ${request.time}</div>
                        <div><strong>Branch:</strong> ${request.branchName}</div>
                        <div><strong>Admin:</strong> ${request.requestedBy}</div>
                        <div class="md:col-span-2"><strong>Email:</strong> ${request.email}</div>
                    </div>
                </div>
                
                {{-- Pricing Change --}}
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Pricing Change</h4>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Service:</span>
                            <span class="font-medium text-gray-900">${request.serviceName}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Package:</span>
                            <span class="font-medium text-gray-900">${request.packageName}</span>
                        </div>
                        <div class="flex justify-between text-sm border-t pt-2">
                            <span class="text-gray-600">Current (Master):</span>
                            <span class="text-gray-900">Rp ${request.masterPrice.toLocaleString('id-ID')}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Requested:</span>
                            <span class="font-bold text-gray-900">Rp ${request.requestedPrice.toLocaleString('id-ID')}</span>
                        </div>
                        <div class="flex justify-between text-sm border-t pt-2">
                            <span class="text-gray-600">Difference:</span>
                            <span class="${impactClass} font-bold">
                                ${diffSign}Rp ${Math.abs(diffAmount).toLocaleString('id-ID')} (${diffSign}${request.difference}%) ${impactIcon}
                            </span>
                        </div>
                    </div>
                </div>
                
                {{-- Justification --}}
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Justification</h4>
                    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700">
                        "${request.reason}"
                    </div>
                </div>
                
                ${request.documents && request.documents.length > 0 ? `
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Supporting Documents</h4>
                    <div class="space-y-2">
                        ${request.documents.map(doc => `
                            <div class="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                ${doc}
                            </div>
                        `).join('')}
                    </div>
                </div>
                ` : ''}
                
                {{-- Impact Analysis --}}
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Impact Analysis</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-lg font-bold text-gray-900">${request.currentBookings}</div>
                            <div class="text-xs text-gray-600 mt-1">Current Bookings/Month</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-lg font-bold ${request.estimatedRevenueLoss < 0 ? 'text-red-600' : 'text-green-600'}">
                                ${request.estimatedRevenueLoss < 0 ? '-' : '+'}Rp ${Math.abs(request.estimatedRevenueLoss).toLocaleString('id-ID')}
                            </div>
                            <div class="text-xs text-gray-600 mt-1">Est. Revenue Impact/Month</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-lg font-bold ${request.potentialVolumeIncrease > 0 ? 'text-green-600' : request.potentialVolumeIncrease < 0 ? 'text-red-600' : 'text-gray-600'}">
                                ${request.potentialVolumeIncrease > 0 ? '+' : ''}${request.potentialVolumeIncrease}%
                            </div>
                            <div class="text-xs text-gray-600 mt-1">Potential Volume Change</div>
                        </div>
                    </div>
                </div>
                
                {{-- Effective Period --}}
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Effective Period</h4>
                    <div class="bg-gray-50 rounded-lg p-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Start:</span>
                            <span class="font-medium text-gray-900">${request.effectiveStart === 'immediate' ? 'Immediately upon approval' : request.effectiveStart}</span>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="text-gray-600">End:</span>
                            <span class="font-medium text-gray-900">${request.effectiveEnd || 'Permanent'}</span>
                        </div>
                    </div>
                </div>
                
                {{-- Decision Section --}}
                <div class="border-t pt-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Super Admin Decision</h4>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="flex items-start p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-400 transition-colors">
                                <input type="radio" name="decision" value="approve" class="mt-1 text-green-600 focus:ring-green-500" checked>
                                <div class="ml-3">
                                    <div class="font-medium text-gray-900">Approve as requested</div>
                                    <div class="text-sm text-gray-600">Accept the proposed price change</div>
                                </div>
                            </label>
                        </div>
                        
                        <div>
                            <label class="flex items-start p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-400 transition-colors">
                                <input type="radio" name="decision" value="modify" onclick="toggleModifyPrice()" class="mt-1 text-blue-600 focus:ring-blue-500">
                                <div class="ml-3 flex-1">
                                    <div class="font-medium text-gray-900">Approve with modification</div>
                                    <div class="text-sm text-gray-600 mb-2">Set a different price</div>
                                    <input type="number" id="modifiedPrice" disabled min="10000" step="1000" placeholder="Enter modified price" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </label>
                        </div>
                        
                        <div>
                            <label class="flex items-start p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-400 transition-colors">
                                <input type="radio" name="decision" value="reject" class="mt-1 text-red-600 focus:ring-red-500">
                                <div class="ml-3">
                                    <div class="font-medium text-gray-900">Reject</div>
                                    <div class="text-sm text-gray-600">Decline this price change request</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Comments <span class="text-red-500">*</span></label>
                        <textarea id="reviewComment" required rows="3" maxlength="500" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Your approval/rejection reason..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">Required - Max 500 characters</p>
                    </div>
                </div>
                
                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t">
                    <button type="button" onclick="closeReviewModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="button" onclick="submitReview(${request.id})" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        Submit Decision
                    </button>
                </div>
            </div>
        `;
        
        document.getElementById('reviewModal').classList.remove('hidden');
    }

    function toggleModifyPrice() {
        const modifiedPriceInput = document.getElementById('modifiedPrice');
        const isModifySelected = document.querySelector('input[name="decision"][value="modify"]').checked;
        modifiedPriceInput.disabled = !isModifySelected;
        if (isModifySelected) {
            modifiedPriceInput.focus();
        }
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
    }

    function submitReview(id) {
        const decision = document.querySelector('input[name="decision"]:checked').value;
        const comment = document.getElementById('reviewComment').value.trim();
        
        if (!comment) {
            alert('Please provide a comment for your decision.');
            return;
        }
        
        const request = requestsData.find(r => r.id === id);
        if (!request) return;
        
        let finalPrice = request.requestedPrice;
        
        if (decision === 'modify') {
            const modifiedPrice = parseInt(document.getElementById('modifiedPrice').value);
            if (!modifiedPrice || modifiedPrice < 10000) {
                alert('Please enter a valid modified price (minimum Rp 10,000).');
                return;
            }
            finalPrice = modifiedPrice;
        }
        
        if (decision === 'approve' || decision === 'modify') {
            request.status = 'approved';
            request.approvedBy = 'Super Admin';
            request.approvedAt = new Date().toISOString().slice(0, 19).replace('T', ' ');
            request.comment = comment;
            if (decision === 'modify') {
                request.approvedPrice = finalPrice;
                request.comment = `${comment} (Modified to Rp ${finalPrice.toLocaleString('id-ID')})`;
            }
        } else {
            request.status = 'rejected';
            request.rejectedBy = 'Super Admin';
            request.rejectedAt = new Date().toISOString().slice(0, 19).replace('T', ' ');
            request.comment = comment;
        }
        
        // Add to recent activity
        recentActivity.unshift({
            date: new Date().toLocaleString('id-ID'),
            branch: request.branchName,
            package: `${request.serviceName} - ${request.packageName}`,
            action: decision === 'reject' ? 'rejected' : 'approved',
            by: 'Super Admin',
            change: `${request.difference > 0 ? '+' : ''}${request.difference}%`
        });
        
        if (recentActivity.length > 5) {
            recentActivity.pop();
        }
        
        closeReviewModal();
        renderRequests();
        renderRecentActivity();
        updateCounts();
        
        const actionText = decision === 'reject' ? 'rejected' : 'approved';
        alert(`Request ${actionText} successfully!\n\nBranch: ${request.branchName}\nPackage: ${request.packageName}\nDecision: ${actionText.charAt(0).toUpperCase() + actionText.slice(1)}`);
    }

    function bulkApprove() {
        if (selectedRequests.length === 0) return;
        
        bulkActionType = 'approve';
        document.getElementById('bulkIcon').innerHTML = `
            <path class="text-green-500" fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        `;
        document.getElementById('bulkConfirmTitle').textContent = 'Approve Multiple Requests?';
        document.getElementById('bulkConfirmMessage').textContent = `Are you sure you want to approve ${selectedRequests.length} pricing request(s)? This action will apply the requested prices to the selected branches.`;
        document.getElementById('bulkConfirmBtn').textContent = 'Approve All';
        document.getElementById('bulkConfirmBtn').className = 'flex-1 px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700';
        document.getElementById('bulkConfirmModal').classList.remove('hidden');
    }

    function bulkReject() {
        if (selectedRequests.length === 0) return;
        
        bulkActionType = 'reject';
        document.getElementById('bulkIcon').innerHTML = `
            <path class="text-red-500" fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        `;
        document.getElementById('bulkConfirmTitle').textContent = 'Reject Multiple Requests?';
        document.getElementById('bulkConfirmMessage').textContent = `Are you sure you want to reject ${selectedRequests.length} pricing request(s)? The branches will continue using master pricing.`;
        document.getElementById('bulkConfirmBtn').textContent = 'Reject All';
        document.getElementById('bulkConfirmBtn').className = 'flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700';
        document.getElementById('bulkConfirmModal').classList.remove('hidden');
    }

    function closeBulkConfirmModal() {
        document.getElementById('bulkConfirmModal').classList.add('hidden');
    }

    function confirmBulkAction() {
        selectedRequests.forEach(id => {
            const request = requestsData.find(r => r.id === id);
            if (request && request.status === 'pending') {
                if (bulkActionType === 'approve') {
                    request.status = 'approved';
                    request.approvedBy = 'Super Admin';
                    request.approvedAt = new Date().toISOString().slice(0, 19).replace('T', ' ');
                    request.comment = 'Bulk approved';
                } else {
                    request.status = 'rejected';
                    request.rejectedBy = 'Super Admin';
                    request.rejectedAt = new Date().toISOString().slice(0, 19).replace('T', ' ');
                    request.comment = 'Bulk rejected';
                }
                
                // Add to recent activity
                recentActivity.unshift({
                    date: new Date().toLocaleString('id-ID'),
                    branch: request.branchName,
                    package: `${request.serviceName} - ${request.packageName}`,
                    action: bulkActionType === 'approve' ? 'approved' : 'rejected',
                    by: 'Super Admin',
                    change: `${request.difference > 0 ? '+' : ''}${request.difference}%`
                });
            }
        });
        
        if (recentActivity.length > 5) {
            recentActivity.length = 5;
        }
        
        closeBulkConfirmModal();
        selectedRequests = [];
        document.getElementById('selectAll').checked = false;
        updateBulkButtons();
        renderRequests();
        renderRecentActivity();
        updateCounts();
        
        const actionText = bulkActionType === 'approve' ? 'approved' : 'rejected';
        alert(`Bulk action completed!\n\nAll selected requests have been ${actionText}.`);
    }

    function renderRecentActivity() {
        const activityContent = document.getElementById('recentActivity');
        
        activityContent.innerHTML = recentActivity.map(activity => `
            <div class="flex items-start gap-3 p-3 rounded-lg ${activity.action === 'approved' ? 'bg-green-50' : 'bg-red-50'}">
                <div class="flex-shrink-0 mt-0.5">
                    ${activity.action === 'approved' ? `
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    ` : `
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    `}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">${activity.branch} - ${activity.package}</p>
                            <p class="text-xs text-gray-600 mt-0.5">Change: ${activity.change}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${activity.action === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${activity.action === 'approved' ? '✅ Approved' : '❌ Rejected'}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">${activity.date} • by ${activity.by}</p>
                </div>
            </div>
        `).join('');
    }

    // Close modals on outside click
    document.addEventListener('click', function(event) {
        if (event.target.id === 'reviewModal') {
            closeReviewModal();
        }
        if (event.target.id === 'bulkConfirmModal') {
            closeBulkConfirmModal();
        }
    });
</script>
@endsection