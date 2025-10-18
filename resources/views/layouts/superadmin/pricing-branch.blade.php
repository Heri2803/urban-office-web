{{-- resources/views/superadmin/pricing/branch-override.blade.php --}}
@extends('layouts.superadmin')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">🏢 Branch Pricing Override</h1>
                <p class="text-gray-600 mt-1 text-sm md:text-base">Request custom pricing for your branch</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="openRequestModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Request Price Change
                </button>
                <button onclick="viewHistory()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    View History
                </button>
            </div>
        </div>
    </div>

    {{-- Branch Selector --}}
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-3">Select Branch to View/Manage:</label>
        <div class="flex flex-col sm:flex-row gap-3">
            <select id="branchSelector" onchange="switchBranch()" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="surabaya">🏢 Surabaya</option>
                <option value="jakarta">🏢 Jakarta</option>
                <option value="bandung">🏢 Bandung</option>
                <option value="bali">🏢 Bali</option>
            </select>
            <div class="flex gap-2 overflow-x-auto sm:overflow-visible">
                <button onclick="selectBranch('surabaya')" class="branch-btn px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium whitespace-nowrap">Surabaya</button>
                <button onclick="selectBranch('jakarta')" class="branch-btn px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 whitespace-nowrap">Jakarta</button>
                <button onclick="selectBranch('bandung')" class="branch-btn px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 whitespace-nowrap">Bandung</button>
                <button onclick="selectBranch('bali')" class="branch-btn px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 whitespace-nowrap">Bali</button>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
            <div class="text-2xl font-bold text-green-600" id="activeOverrides">12</div>
            <div class="text-sm text-gray-600 mt-1">Active Overrides</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
            <div class="text-2xl font-bold text-yellow-600" id="pendingRequests">3</div>
            <div class="text-sm text-gray-600 mt-1">Pending Requests</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
            <div class="text-2xl font-bold text-blue-600" id="approvedMonth">8</div>
            <div class="text-sm text-gray-600 mt-1">Approved This Month</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
            <div class="text-2xl font-bold text-red-600" id="rejectedMonth">2</div>
            <div class="text-sm text-gray-600 mt-1">Rejected This Month</div>
        </div>
    </div>

    {{-- Service Type Tabs --}}
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="overflow-x-auto">
            <div class="flex border-b">
                <button onclick="switchService('meeting-room')" class="service-tab whitespace-nowrap px-4 md:px-6 py-3 font-medium text-sm border-b-2 border-blue-500 text-blue-600 bg-blue-50">
                    Meeting Room
                </button>
                <button onclick="switchService('private-office')" class="service-tab whitespace-nowrap px-4 md:px-6 py-3 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                    Private Office
                </button>
                <button onclick="switchService('sharing-room')" class="service-tab whitespace-nowrap px-4 md:px-6 py-3 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                    Sharing Room
                </button>
                <button onclick="switchService('coworking-space')" class="service-tab whitespace-nowrap px-4 md:px-6 py-3 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                    Coworking Space
                </button>
                <button onclick="switchService('virtual-office')" class="service-tab whitespace-nowrap px-4 md:px-6 py-3 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                    Virtual Office
                </button>
                <button onclick="switchService('event-space')" class="service-tab whitespace-nowrap px-4 md:px-6 py-3 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                    Event Space
                </button>
            </div>
        </div>
    </div>

    {{-- Comparison Table --}}
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
            <span id="branchName">Surabaya</span> Branch - <span id="serviceTitle">Meeting Room</span> Pricing Overview
        </h2>

        {{-- Desktop Table --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Master Price</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch Override</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difference</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="comparisonTableBody">
                    {{-- Rendered by JavaScript --}}
                </tbody>
            </table>
        </div>

        {{-- Mobile/Tablet Card View --}}
        <div class="lg:hidden space-y-4" id="comparisonCardView">
            {{-- Rendered by JavaScript --}}
        </div>

        {{-- Empty State --}}
        <div id="emptyState" class="hidden text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500 text-lg font-medium">No pricing data available</p>
        </div>
    </div>
</div>

{{-- Request Price Change Modal --}}
<div id="requestModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <div onclick="closeRequestModal()" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
        
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6 sm:p-8 my-8 max-h-[90vh] overflow-y-auto transform transition-all">
            <div class="flex items-center justify-between border-b pb-3 mb-6">
                <h3 class="text-xl font-bold text-gray-800">Request Price Change</h3>
                <button onclick="closeRequestModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="requestForm" onsubmit="submitRequest(event)">
                <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <strong>Branch:</strong> <span id="modalBranchName">Surabaya</span> (Current Admin's Branch)
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Service Type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Service Type <span class="text-red-500">*</span></label>
                        <select id="reqServiceType" required onchange="loadPackages()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Service</option>
                            <option value="meeting-room">Meeting Room</option>
                            <option value="private-office">Private Office</option>
                            <option value="sharing-room">Sharing Room</option>
                            <option value="coworking-space">Coworking Space</option>
                            <option value="virtual-office">Virtual Office</option>
                            <option value="event-space">Event Space</option>
                        </select>
                    </div>

                    {{-- Package --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Package <span class="text-red-500">*</span></label>
                        <select id="reqPackage" required onchange="updatePriceInfo()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" disabled>
                            <option value="">Select Package</option>
                        </select>
                    </div>
                </div>

                {{-- Current Master Price --}}
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <div class="text-sm text-gray-600 mb-1">Current Master Price:</div>
                    <div class="text-2xl font-bold text-gray-900" id="currentMasterPrice">-</div>
                </div>

                {{-- Requested New Price --}}
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Requested New Price (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" id="reqNewPrice" required min="10000" step="1000" oninput="calculateDifference()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="120000">
                    <div id="priceDifference" class="text-sm mt-2"></div>
                </div>

                {{-- Reason --}}
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Change <span class="text-red-500">*</span></label>
                    <textarea id="reqReason" required rows="3" minlength="20" maxlength="500" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Explain why this price change is needed..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Min 20 characters - <span id="reasonCount">0</span>/500</p>
                </div>

                {{-- Effective Period --}}
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Effective Start Date</label>
                        <input type="date" id="reqStartDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <label class="flex items-center mt-2">
                            <input type="checkbox" id="immediateEffect" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                            <span class="text-sm text-gray-700">Immediate if approved</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="duration" value="permanent" checked class="text-blue-600 focus:ring-blue-500 mr-2">
                                <span class="text-sm text-gray-700">Permanent</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="duration" value="temporary" onclick="toggleEndDate()" class="text-blue-600 focus:ring-blue-500 mr-2">
                                <span class="text-sm text-gray-700">Temporary</span>
                            </label>
                            <input type="date" id="reqEndDate" disabled class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Supporting Documents --}}
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supporting Documents (Optional)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors cursor-pointer">
                        <input type="file" id="reqDocuments" multiple accept=".pdf,.jpg,.jpeg,.png,.xlsx" class="hidden" onchange="displayFiles()">
                        <label for="reqDocuments" class="cursor-pointer">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-sm text-gray-600">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-500 mt-1">PDF, Images, Excel (Max 5MB each)</p>
                        </label>
                    </div>
                    <div id="fileList" class="mt-2 text-sm text-gray-600"></div>
                </div>

                {{-- Warning Note --}}
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="text-sm text-yellow-800">
                            <strong>Note:</strong> Price changes greater than 10% require Super Admin approval. Changes will not take effect until approved.
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t mt-6">
                    <button type="button" onclick="closeRequestModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- History Modal --}}
<div id="historyModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <div onclick="closeHistoryModal()" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
        
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-4xl p-6 sm:p-8 my-8 max-h-[90vh] overflow-y-auto transform transition-all">
            <div class="flex items-center justify-between border-b pb-3 mb-6">
                <h3 class="text-xl font-bold text-gray-800">Price Override History - <span id="historyBranchName">Surabaya</span></h3>
                <button onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="space-y-4" id="historyContent">
                {{-- Rendered by JavaScript --}}
            </div>
        </div>
    </div>
</div>

<script>
    let currentBranch = 'surabaya';
    let currentService = 'meeting-room';
    
    // Sample Master Pricing Data
    const masterPricing = {
        'meeting-room': [
            { id: 1, name: '1 Hour Package', price: 150000 },
            { id: 2, name: '2 Hours Package', price: 280000 },
            { id: 3, name: '4 Hours Package', price: 500000 },
            { id: 4, name: 'Full Day (8 Hours)', price: 900000 }
        ],
        'private-office': [
            { id: 5, name: '1 Month', price: 3000000 },
            { id: 6, name: '3 Months', price: 8500000 },
            { id: 7, name: '6 Months', price: 16000000 },
            { id: 8, name: '1 Year', price: 30000000 }
        ],
        'virtual-office': [
            { id: 9, name: 'Basic (1 Year)', price: 1200000 },
            { id: 10, name: 'Standard (1 Year)', price: 2400000 }
        ],
        'event-space': [
            { id: 11, name: 'Half Day (4 Hours)', price: 2500000 },
            { id: 12, name: 'Full Day (8 Hours)', price: 4500000 }
        ]
    };

    // Sample Override Data
    let overrideData = [
        { id: 1, branch: 'surabaya', service: 'meeting-room', packageId: 1, packageName: '1 Hour Package', masterPrice: 150000, overridePrice: 120000, status: 'active', reason: 'Competitor pricing', effectiveStart: '2025-10-16', effectiveEnd: '2025-12-31' },
        { id: 2, branch: 'surabaya', service: 'meeting-room', packageId: 2, packageName: '2 Hours Package', masterPrice: 280000, overridePrice: null, status: 'master', reason: null, effectiveStart: null, effectiveEnd: null },
        { id: 3, branch: 'surabaya', service: 'meeting-room', packageId: 4, packageName: 'Full Day (8 Hours)', masterPrice: 900000, overridePrice: 950000, status: 'pending', reason: 'High demand', effectiveStart: null, effectiveEnd: null },
        { id: 4, branch: 'jakarta', service: 'meeting-room', packageId: 1, packageName: '1 Hour Package', masterPrice: 150000, overridePrice: 180000, status: 'active', reason: 'Premium location', effectiveStart: '2025-10-01', effectiveEnd: null }
    ];

    // Sample History Data
    const historyData = [
        { date: '2025-10-15', package: 'Meeting Room - 1 Hour', change: '150k → 120k (-20%)', reason: 'Competitor pricing', status: 'approved', effective: '16/10/2025 - 31/12/2025', approvedBy: 'Super Admin' },
        { date: '2025-10-10', package: 'Private Office - 1 Month', change: '3M → 3.5M (+16.7%)', reason: 'High demand, limited availability', status: 'rejected', effective: null, rejectedBy: 'Super Admin', comment: 'Exceeded 15% limit' },
        { date: '2025-09-20', package: 'Event Space - Half Day', change: '2.5M → 2.3M (-8%)', reason: 'Seasonal promotion', status: 'approved', effective: '20/09/2025 - 30/09/2025', approvedBy: 'Super Admin' }
    ];

    document.addEventListener('DOMContentLoaded', function() {
        renderComparison();
        updateStats();
        setupEventListeners();
        setMinDate();
    });

    function setupEventListeners() {
        document.getElementById('reqReason').addEventListener('input', function() {
            document.getElementById('reasonCount').textContent = this.value.length;
        });
        document.getElementById('immediateEffect').addEventListener('change', function() {
            document.getElementById('reqStartDate').disabled = this.checked;
        });
    }

    function setMinDate() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('reqStartDate').min = today;
        document.getElementById('reqEndDate').min = today;
    }

    function switchBranch() {
        currentBranch = document.getElementById('branchSelector').value;
        updateBranchButtons();
        updateBranchName();
        renderComparison();
        updateStats();
    }

    function selectBranch(branch) {
        currentBranch = branch;
        document.getElementById('branchSelector').value = branch;
        updateBranchButtons();
        updateBranchName();
        renderComparison();
        updateStats();
    }

    function updateBranchButtons() {
        document.querySelectorAll('.branch-btn').forEach((btn, index) => {
            const branches = ['surabaya', 'jakarta', 'bandung', 'bali'];
            if (branches[index] === currentBranch) {
                btn.classList.remove('border', 'border-gray-300', 'text-gray-700', 'hover:bg-gray-50');
                btn.classList.add('bg-blue-600', 'text-white');
            } else {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('border', 'border-gray-300', 'text-gray-700', 'hover:bg-gray-50');
            }
        });
    }

    function updateBranchName() {
        const names = { 'surabaya': 'Surabaya', 'jakarta': 'Jakarta', 'bandung': 'Bandung', 'bali': 'Bali' };
        document.getElementById('branchName').textContent = names[currentBranch];
        document.getElementById('modalBranchName').textContent = names[currentBranch];
        document.getElementById('historyBranchName').textContent = names[currentBranch];
    }

    function switchService(service) {
        currentService = service;
        document.querySelectorAll('.service-tab').forEach(tab => {
            tab.classList.remove('border-blue-500', 'text-blue-600', 'bg-blue-50');
            tab.classList.add('border-transparent', 'text-gray-500');
        });
        event.target.classList.remove('border-transparent', 'text-gray-500');
        event.target.classList.add('border-blue-500', 'text-blue-600', 'bg-blue-50');
        
        const titles = {
            'meeting-room': 'Meeting Room',
            'private-office': 'Private Office',
            'sharing-room': 'Sharing Room',
            'coworking-space': 'Coworking Space',
            'virtual-office': 'Virtual Office',
            'event-space': 'Event Space'
        };
        document.getElementById('serviceTitle').textContent = titles[service];
        renderComparison();
    }

    function renderComparison() {
        const packages = masterPricing[currentService] || [];
        const tableBody = document.getElementById('comparisonTableBody');
        const cardView = document.getElementById('comparisonCardView');
        const emptyState = document.getElementById('emptyState');
        
        if (packages.length === 0) {
            tableBody.innerHTML = '';
            cardView.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }
        
        emptyState.classList.add('hidden');
        
        // Desktop Table
        tableBody.innerHTML = packages.map(pkg => {
            const override = overrideData.find(o => o.branch === currentBranch && o.service === currentService && o.packageId === pkg.id);
            const overridePrice = override?.overridePrice || null;
            const status = override?.status || 'master';
            
            let difference = '';
            let diffClass = '';
            if (overridePrice) {
                const diff = overridePrice - pkg.price;
                const diffPercent = ((diff / pkg.price) * 100).toFixed(1);
                if (diff < 0) {
                    difference = `-Rp ${Math.abs(diff).toLocaleString('id-ID')} (${diffPercent}%)`;
                    diffClass = 'text-green-600';
                } else if (diff > 0) {
                    difference = `+Rp ${diff.toLocaleString('id-ID')} (+${diffPercent}%)`;
                    diffClass = 'text-red-600';
                } else {
                    difference = '0%';
                    diffClass = 'text-gray-600';
                }
            }
            
            const statusBadge = {
                'active': '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">🟢 Active</span>',
                'pending': '<span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">🟡 Pending</span>',
                'master': '<span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">🔵 Master</span>',
                'rejected': '<span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">🔴 Rejected</span>'
            };
            
            return `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">${pkg.name}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">Rp ${pkg.price.toLocaleString('id-ID')}</td>
                    <td class="px-4 py-3 text-sm font-semibold ${overridePrice ? 'text-gray-900' : 'text-gray-400'}">
                        ${overridePrice ? 'Rp ' + overridePrice.toLocaleString('id-ID') : '= Master'}
                    </td>
                    <td class="px-4 py-3 text-sm font-medium ${diffClass}">
                        ${difference || '-'}
                    </td>
                    <td class="px-4 py-3">
                        ${statusBadge[status]}
                    </td>
                    <td class="px-4 py-3 text-center">
                        ${status === 'master' ? `
                            <button onclick="quickRequest(${pkg.id}, '${pkg.name}', ${pkg.price})" class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100">
                                Request Override
                            </button>
                        ` : status === 'pending' ? `
                            <button onclick="viewRequestDetail(${override.id})" class="px-3 py-1.5 bg-yellow-50 text-yellow-600 rounded-lg text-sm font-medium hover:bg-yellow-100">
                                View Request
                            </button>
                        ` : status === 'active' ? `
                            <div class="flex gap-2 justify-center">
                                <button onclick="editOverride(${override.id})" class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100">
                                    Edit
                                </button>
                                <button onclick="removeOverride(${override.id})" class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100">
                                    Remove
                                </button>
                            </div>
                        ` : ''}
                    </td>
                </tr>
            `;
        }).join('');
        
        // Mobile/Tablet Cards
        cardView.innerHTML = packages.map(pkg => {
            const override = overrideData.find(o => o.branch === currentBranch && o.service === currentService && o.packageId === pkg.id);
            const overridePrice = override?.overridePrice || null;
            const status = override?.status || 'master';
            
            let difference = '';
            let diffClass = '';
            if (overridePrice) {
                const diff = overridePrice - pkg.price;
                const diffPercent = ((diff / pkg.price) * 100).toFixed(1);
                if (diff < 0) {
                    difference = `-Rp ${Math.abs(diff).toLocaleString('id-ID')} (${diffPercent}%)`;
                    diffClass = 'text-green-600';
                } else if (diff > 0) {
                    difference = `+Rp ${diff.toLocaleString('id-ID')} (+${diffPercent}%)`;
                    diffClass = 'text-red-600';
                }
            }
            
            const statusBadge = {
                'active': 'bg-green-100 text-green-800',
                'pending': 'bg-yellow-100 text-yellow-800',
                'master': 'bg-blue-100 text-blue-800',
                'rejected': 'bg-red-100 text-red-800'
            };
            
            const statusText = {
                'active': '🟢 Active Override',
                'pending': '🟡 Pending Approval',
                'master': '🔵 Using Master',
                'rejected': '🔴 Rejected'
            };
            
            return `
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="font-semibold text-gray-900">${pkg.name}</h3>
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${statusBadge[status]}">
                            ${statusText[status]}
                        </span>
                    </div>
                    
                    <div class="space-y-2 mb-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Master Price:</span>
                            <span class="text-gray-900">Rp ${pkg.price.toLocaleString('id-ID')}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Branch Override:</span>
                            <span class="${overridePrice ? 'text-gray-900 font-semibold' : 'text-gray-400'}">
                                ${overridePrice ? 'Rp ' + overridePrice.toLocaleString('id-ID') : '= Master'}
                            </span>
                        </div>
                        ${difference ? `
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Difference:</span>
                            <span class="font-medium ${diffClass}">${difference}</span>
                        </div>
                        ` : ''}
                    </div>
                    
                    <div class="flex gap-2">
                        ${status === 'master' ? `
                            <button onclick="quickRequest(${pkg.id}, '${pkg.name}', ${pkg.price})" class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                                Request Override
                            </button>
                        ` : status === 'pending' ? `
                            <button onclick="viewRequestDetail(${override.id})" class="flex-1 px-3 py-2 bg-yellow-50 text-yellow-600 rounded-lg text-sm font-medium hover:bg-yellow-100">
                                View Request
                            </button>
                        ` : status === 'active' ? `
                            <button onclick="editOverride(${override.id})" class="flex-1 px-3 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100">
                                Edit
                            </button>
                            <button onclick="removeOverride(${override.id})" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100">
                                Remove
                            </button>
                        ` : ''}
                    </div>
                </div>
            `;
        }).join('');
    }

    function updateStats() {
        const branchOverrides = overrideData.filter(o => o.branch === currentBranch);
        document.getElementById('activeOverrides').textContent = branchOverrides.filter(o => o.status === 'active').length;
        document.getElementById('pendingRequests').textContent = branchOverrides.filter(o => o.status === 'pending').length;
        document.getElementById('approvedMonth').textContent = '8'; // Simulated
        document.getElementById('rejectedMonth').textContent = '2'; // Simulated
    }

    function openRequestModal() {
        document.getElementById('requestForm').reset();
        document.getElementById('reqPackage').disabled = true;
        document.getElementById('reqPackage').innerHTML = '<option value="">Select Service First</option>';
        document.getElementById('currentMasterPrice').textContent = '-';
        document.getElementById('priceDifference').innerHTML = '';
        document.getElementById('reasonCount').textContent = '0';
        document.getElementById('fileList').textContent = '';
        document.getElementById('reqEndDate').disabled = true;
        document.getElementById('requestModal').classList.remove('hidden');
    }

    function closeRequestModal() {
        document.getElementById('requestModal').classList.add('hidden');
    }

    function quickRequest(packageId, packageName, masterPrice) {
        openRequestModal();
        const serviceSelect = document.getElementById('reqServiceType');
        serviceSelect.value = currentService;
        loadPackages();
        setTimeout(() => {
            document.getElementById('reqPackage').value = packageId;
            updatePriceInfo();
        }, 100);
    }

    function loadPackages() {
        const service = document.getElementById('reqServiceType').value;
        const packageSelect = document.getElementById('reqPackage');
        
        if (!service) {
            packageSelect.disabled = true;
            packageSelect.innerHTML = '<option value="">Select Service First</option>';
            return;
        }
        
        packageSelect.disabled = false;
        const packages = masterPricing[service] || [];
        packageSelect.innerHTML = '<option value="">Select Package</option>' + 
            packages.map(p => `<option value="${p.id}" data-price="${p.price}">${p.name}</option>`).join('');
    }

    function updatePriceInfo() {
        const packageSelect = document.getElementById('reqPackage');
        const selectedOption = packageSelect.options[packageSelect.selectedIndex];
        
        if (!selectedOption || !selectedOption.value) {
            document.getElementById('currentMasterPrice').textContent = '-';
            return;
        }
        
        const masterPrice = parseInt(selectedOption.getAttribute('data-price'));
        document.getElementById('currentMasterPrice').textContent = 'Rp ' + masterPrice.toLocaleString('id-ID');
        calculateDifference();
    }

    function calculateDifference() {
        const packageSelect = document.getElementById('reqPackage');
        const selectedOption = packageSelect.options[packageSelect.selectedIndex];
        
        if (!selectedOption || !selectedOption.value) return;
        
        const masterPrice = parseInt(selectedOption.getAttribute('data-price'));
        const newPrice = parseInt(document.getElementById('reqNewPrice').value) || 0;
        
        if (newPrice === 0) {
            document.getElementById('priceDifference').innerHTML = '';
            return;
        }
        
        const diff = newPrice - masterPrice;
        const diffPercent = ((diff / masterPrice) * 100).toFixed(1);
        
        let html = '';
        let colorClass = '';
        
        if (diff < 0) {
            colorClass = 'text-green-600';
            html = `<strong>Difference:</strong> -Rp ${Math.abs(diff).toLocaleString('id-ID')} (${diffPercent}%) <span class="text-green-700">✓ Lower price</span>`;
        } else if (diff > 0) {
            colorClass = 'text-red-600';
            html = `<strong>Difference:</strong> +Rp ${diff.toLocaleString('id-ID')} (+${diffPercent}%) <span class="text-red-700">⚠ Higher price</span>`;
            
            if (Math.abs(parseFloat(diffPercent)) > 10) {
                html += '<br><span class="text-yellow-600 text-xs">⚠ Requires Super Admin approval (>10% change)</span>';
            }
        } else {
            colorClass = 'text-gray-600';
            html = '<strong>Difference:</strong> No change';
        }
        
        document.getElementById('priceDifference').innerHTML = `<span class="${colorClass}">${html}</span>`;
    }

    function toggleEndDate() {
        const isTemporary = document.querySelector('input[name="duration"][value="temporary"]').checked;
        document.getElementById('reqEndDate').disabled = !isTemporary;
    }

    function displayFiles() {
        const files = document.getElementById('reqDocuments').files;
        const fileList = document.getElementById('fileList');
        
        if (files.length === 0) {
            fileList.textContent = '';
            return;
        }
        
        const fileNames = Array.from(files).map(f => f.name).join(', ');
        fileList.textContent = `Selected files: ${fileNames}`;
    }

    function submitRequest(event) {
        event.preventDefault();
        
        const service = document.getElementById('reqServiceType').value;
        const packageId = parseInt(document.getElementById('reqPackage').value);
        const packageName = document.getElementById('reqPackage').options[document.getElementById('reqPackage').selectedIndex].text;
        const masterPrice = parseInt(document.getElementById('reqPackage').options[document.getElementById('reqPackage').selectedIndex].getAttribute('data-price'));
        const newPrice = parseInt(document.getElementById('reqNewPrice').value);
        const reason = document.getElementById('reqReason').value;
        
        const newRequest = {
            id: overrideData.length + 1,
            branch: currentBranch,
            service: service,
            packageId: packageId,
            packageName: packageName,
            masterPrice: masterPrice,
            overridePrice: newPrice,
            status: 'pending',
            reason: reason,
            effectiveStart: document.getElementById('immediateEffect').checked ? null : document.getElementById('reqStartDate').value,
            effectiveEnd: document.querySelector('input[name="duration"][value="temporary"]').checked ? document.getElementById('reqEndDate').value : null
        };
        
        overrideData.push(newRequest);
        
        closeRequestModal();
        renderComparison();
        updateStats();
        
        alert('Price change request submitted successfully!\nYour request will be reviewed by Super Admin.');
    }

    function viewRequestDetail(id) {
        const override = overrideData.find(o => o.id === id);
        if (!override) return;
        
        const diff = override.overridePrice - override.masterPrice;
        const diffPercent = ((diff / override.masterPrice) * 100).toFixed(1);
        
        alert(`Request Details:\n\nPackage: ${override.packageName}\nMaster Price: Rp ${override.masterPrice.toLocaleString('id-ID')}\nRequested Price: Rp ${override.overridePrice.toLocaleString('id-ID')}\nDifference: ${diff > 0 ? '+' : ''}${diffPercent}%\n\nReason: ${override.reason}\n\nStatus: Pending Super Admin approval`);
    }

    function editOverride(id) {
        const override = overrideData.find(o => o.id === id);
        if (!override) return;
        
        openRequestModal();
        document.getElementById('reqServiceType').value = override.service;
        loadPackages();
        setTimeout(() => {
            document.getElementById('reqPackage').value = override.packageId;
            updatePriceInfo();
            document.getElementById('reqNewPrice').value = override.overridePrice;
            document.getElementById('reqReason').value = override.reason;
            calculateDifference();
        }, 100);
    }

    function removeOverride(id) {
        if (!confirm('Are you sure you want to remove this price override?\n\nThe pricing will revert to master pricing.')) {
            return;
        }
        
        const index = overrideData.findIndex(o => o.id === id);
        if (index > -1) {
            overrideData[index].status = 'master';
            overrideData[index].overridePrice = null;
            renderComparison();
            updateStats();
            alert('Price override removed successfully!\nNow using master pricing.');
        }
    }

    function viewHistory() {
        document.getElementById('historyModal').classList.remove('hidden');
        renderHistory();
    }

    function closeHistoryModal() {
        document.getElementById('historyModal').classList.add('hidden');
    }

    function renderHistory() {
        const historyContent = document.getElementById('historyContent');
        
        historyContent.innerHTML = historyData.map(h => `
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">${h.package}</div>
                        <div class="text-sm text-gray-600 mt-1">Date: ${h.date}</div>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium rounded-full ${h.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${h.status === 'approved' ? '✅ Approved' : '❌ Rejected'}
                    </span>
                </div>
                <div class="space-y-1 text-sm">
                    <div><strong>Change:</strong> <span class="font-medium">${h.change}</span></div>
                    <div><strong>Reason:</strong> ${h.reason}</div>
                    ${h.status === 'approved' ? `
                        <div><strong>Effective Period:</strong> ${h.effective}</div>
                        <div class="text-green-700"><strong>Approved by:</strong> ${h.approvedBy}</div>
                    ` : `
                        <div class="text-red-700"><strong>Rejected by:</strong> ${h.rejectedBy}</div>
                        <div class="text-red-600"><strong>Comment:</strong> ${h.comment}</div>
                    `}
                </div>
            </div>
        `).join('');
    }

    // Close modals on outside click
    document.addEventListener('click', function(event) {
        if (event.target.id === 'requestModal') {
            closeRequestModal();
        }
        if (event.target.id === 'historyModal') {
            closeHistoryModal();
        }
    });
</script>
@endsection