{{-- resources/views/superadmin/pricing/master.blade.php --}}
@extends('layouts.superadmin')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">💰 Master Pricing Management</h1>
                <p class="text-gray-600 mt-1 text-sm md:text-base">Set base pricing for all services across branches</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="openAddModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Package
                </button>
                <button onclick="exportPricing()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export
                </button>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
            <div class="text-2xl font-bold text-gray-900" id="totalPackages">45</div>
            <div class="text-sm text-gray-600 mt-1">Total Packages</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
            <div class="text-lg font-bold text-green-600" id="priceRange">50k - 20M</div>
            <div class="text-sm text-gray-600 mt-1">Price Range</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
            <div class="text-2xl font-bold text-purple-600" id="recentUpdates">12</div>
            <div class="text-sm text-gray-600 mt-1">Recent Updates</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-orange-500">
            <div class="text-lg font-bold text-orange-600" id="lastUpdated">2 hours ago</div>
            <div class="text-sm text-gray-600 mt-1">Last Updated</div>
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

    {{-- Filter & Search --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-3">
            <select id="statusFilter" onchange="applyFilters()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <input type="text" id="searchInput" onkeyup="applyFilters()" placeholder="Search packages..." class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <button onclick="resetFilters()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                Reset
            </button>
        </div>
    </div>

    {{-- Pricing Table/Cards --}}
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">
                <span id="serviceTitle">Meeting Room</span> - Standard Pricing (<span id="packageCount">4</span>)
            </h2>
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base Price</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Discount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Additional</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="tableBody">
                    {{-- Rendered by JavaScript --}}
                </tbody>
            </table>
        </div>

        {{-- Mobile/Tablet Card View --}}
        <div class="lg:hidden space-y-4" id="cardView">
            {{-- Rendered by JavaScript --}}
        </div>

        {{-- Empty State --}}
        <div id="emptyState" class="hidden text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500 text-lg font-medium">No pricing packages found</p>
            <p class="text-gray-400 text-sm mt-2">Add a new package to get started</p>
            <button onclick="openAddModal()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                Add First Package
            </button>
        </div>
    </div>
</div>

{{-- Add/Edit Modal --}}
<div id="pricingModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <div onclick="closeModal()" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
        
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-3xl p-6 sm:p-8 my-8 max-h-[90vh] overflow-y-auto transform transition-all">
            <div class="flex items-center justify-between border-b pb-3 mb-6">
                <h3 class="text-xl font-bold text-gray-800" id="modalTitle">Add New Pricing Package</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="pricingForm" onsubmit="savePricing(event)">
                <input type="hidden" id="pricingId" value="">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Service Type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Service Type <span class="text-red-500">*</span></label>
                        <select id="modalServiceType" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="meeting-room">Meeting Room</option>
                            <option value="private-office">Private Office</option>
                            <option value="sharing-room">Sharing Room</option>
                            <option value="coworking-space">Coworking Space</option>
                            <option value="virtual-office">Virtual Office</option>
                            <option value="event-space">Event Space</option>
                        </select>
                    </div>

                    {{-- Package Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Package Name <span class="text-red-500">*</span></label>
                        <input type="text" id="packageName" required maxlength="50" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., 1 Hour Package">
                    </div>

                    {{-- Duration Value --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duration <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <input type="number" id="durationValue" required min="1" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="1">
                            <select id="durationUnit" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="minutes">Minutes</option>
                                <option value="hours">Hours</option>
                                <option value="days">Days</option>
                                <option value="months">Months</option>
                                <option value="years">Years</option>
                            </select>
                        </div>
                    </div>

                    {{-- Base Price --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Base Price (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" id="basePrice" required min="10000" step="1000" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="150000">
                        <p class="text-xs text-gray-500 mt-1">Min: Rp 10,000</p>
                    </div>

                    {{-- Max Discount --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Discount (%)</label>
                        <input type="number" id="maxDiscount" min="0" max="50" step="1" value="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="10" oninput="calculateMaxDiscountAmount()">
                        <p class="text-xs text-gray-500 mt-1">Max: <span id="maxDiscountAmount">Rp 0</span></p>
                    </div>
                </div>

                {{-- Additional Charges --}}
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Additional Charges (Optional)</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="weekendSurcharge" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="weekendSurcharge" class="text-sm text-gray-700">Weekend Surcharge</label>
                            <input type="number" id="weekendPercent" min="0" max="100" placeholder="20" class="w-20 px-2 py-1 border border-gray-300 rounded text-sm" disabled>
                            <span class="text-sm text-gray-500">%</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="overtimeCharge" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="overtimeCharge" class="text-sm text-gray-700">Overtime Rate</label>
                            <input type="number" id="overtimeRate" min="0" placeholder="50000" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm" disabled>
                        </div>
                    </div>
                </div>

                {{-- Features Included --}}
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Features Included</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        <label class="flex items-center text-sm">
                            <input type="checkbox" value="wifi" class="feature-check rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2" checked>
                            <span class="text-gray-700">WiFi</span>
                        </label>
                        <label class="flex items-center text-sm">
                            <input type="checkbox" value="ac" class="feature-check rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2" checked>
                            <span class="text-gray-700">AC</span>
                        </label>
                        <label class="flex items-center text-sm">
                            <input type="checkbox" value="coffee" class="feature-check rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                            <span class="text-gray-700">Coffee/Tea</span>
                        </label>
                        <label class="flex items-center text-sm">
                            <input type="checkbox" value="parking" class="feature-check rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                            <span class="text-gray-700">Parking</span>
                        </label>
                        <label class="flex items-center text-sm">
                            <input type="checkbox" value="projector" class="feature-check rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                            <span class="text-gray-700">Projector</span>
                        </label>
                        <label class="flex items-center text-sm">
                            <input type="checkbox" value="whiteboard" class="feature-check rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                            <span class="text-gray-700">Whiteboard</span>
                        </label>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea id="notes" rows="2" maxlength="200" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Additional information..."></textarea>
                </div>

                {{-- Status --}}
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="status" value="active" checked class="text-blue-600 focus:ring-blue-500 mr-2">
                            <span class="text-sm text-gray-700">Active</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="inactive" class="text-blue-600 focus:ring-blue-500 mr-2">
                            <span class="text-sm text-gray-700">Inactive</span>
                        </label>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t mt-6">
                    <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        Save Package
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div onclick="closeDeleteModal()" class="fixed inset-0 bg-black bg-opacity-50"></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
            <div class="text-center">
                <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Pricing Package?</h3>
                <p class="text-sm text-gray-600 mb-6">Are you sure you want to delete this package? This will affect all branches using master pricing.</p>
                <div class="flex gap-3">
                    <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50">
                        Cancel
                    </button>
                    <button onclick="confirmDelete()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentService = 'meeting-room';
    let deleteTargetId = null;
    let pricingData = [
        { id: 1, service: 'meeting-room', name: '1 Hour Package', duration: 1, unit: 'hours', price: 150000, discount: 10, weekend: 20, overtime: 50000, features: ['wifi', 'ac'], status: 'active', notes: '' },
        { id: 2, service: 'meeting-room', name: '2 Hours Package', duration: 2, unit: 'hours', price: 280000, discount: 15, weekend: 20, overtime: 50000, features: ['wifi', 'ac', 'coffee'], status: 'active', notes: '' },
        { id: 3, service: 'meeting-room', name: '4 Hours Package', duration: 4, unit: 'hours', price: 500000, discount: 20, weekend: 20, overtime: 50000, features: ['wifi', 'ac', 'coffee', 'parking'], status: 'active', notes: '' },
        { id: 4, service: 'meeting-room', name: 'Full Day (8 Hours)', duration: 8, unit: 'hours', price: 900000, discount: 25, weekend: 20, overtime: 50000, features: ['wifi', 'ac', 'coffee', 'parking', 'projector', 'whiteboard'], status: 'active', notes: '' },
        { id: 5, service: 'private-office', name: '1 Month', duration: 1, unit: 'months', price: 3000000, discount: 5, weekend: 0, overtime: 0, features: ['wifi', 'ac', 'parking'], status: 'active', notes: '' },
        { id: 6, service: 'private-office', name: '3 Months', duration: 3, unit: 'months', price: 8500000, discount: 10, weekend: 0, overtime: 0, features: ['wifi', 'ac', 'parking'], status: 'active', notes: '6% discount' },
        { id: 7, service: 'private-office', name: '6 Months', duration: 6, unit: 'months', price: 16000000, discount: 12, weekend: 0, overtime: 0, features: ['wifi', 'ac', 'parking'], status: 'active', notes: '11% discount' },
        { id: 8, service: 'private-office', name: '1 Year', duration: 12, unit: 'months', price: 30000000, discount: 15, weekend: 0, overtime: 0, features: ['wifi', 'ac', 'parking'], status: 'active', notes: '17% discount' },
        { id: 9, service: 'virtual-office', name: 'Basic (1 Year)', duration: 1, unit: 'years', price: 1200000, discount: 10, weekend: 0, overtime: 0, features: ['wifi'], status: 'active', notes: 'Setup fee: Rp 500k' },
        { id: 10, service: 'virtual-office', name: 'Standard (1 Year)', duration: 1, unit: 'years', price: 2400000, discount: 10, weekend: 0, overtime: 0, features: ['wifi'], status: 'active', notes: '' },
        { id: 11, service: 'event-space', name: 'Half Day (4 Hours)', duration: 4, unit: 'hours', price: 2500000, discount: 15, weekend: 30, overtime: 0, features: ['wifi', 'ac', 'projector'], status: 'active', notes: '' },
        { id: 12, service: 'event-space', name: 'Full Day (8 Hours)', duration: 8, unit: 'hours', price: 4500000, discount: 15, weekend: 30, overtime: 0, features: ['wifi', 'ac', 'projector', 'whiteboard'], status: 'active', notes: '' }
    ];

    document.addEventListener('DOMContentLoaded', function() {
        renderPricing();
        updateStats();
        setupEventListeners();
    });

    function setupEventListeners() {
        document.getElementById('weekendSurcharge').addEventListener('change', function() {
            document.getElementById('weekendPercent').disabled = !this.checked;
        });
        document.getElementById('overtimeCharge').addEventListener('change', function() {
            document.getElementById('overtimeRate').disabled = !this.checked;
        });
        document.getElementById('basePrice').addEventListener('input', calculateMaxDiscountAmount);
        document.getElementById('maxDiscount').addEventListener('input', calculateMaxDiscountAmount);
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
        document.getElementById('modalServiceType').value = service;
        
        renderPricing();
    }

    function applyFilters() {
        renderPricing();
    }

    function resetFilters() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('searchInput').value = '';
        renderPricing();
    }

    function getFilteredPricing() {
        let filtered = pricingData.filter(p => p.service === currentService);
        
        const statusFilter = document.getElementById('statusFilter').value;
        if (statusFilter) {
            filtered = filtered.filter(p => p.status === statusFilter);
        }
        
        const search = document.getElementById('searchInput').value.toLowerCase();
        if (search) {
            filtered = filtered.filter(p => 
                p.name.toLowerCase().includes(search) ||
                p.price.toString().includes(search)
            );
        }
        
        return filtered;
    }

    function renderPricing() {
        const filtered = getFilteredPricing();
        const tableBody = document.getElementById('tableBody');
        const cardView = document.getElementById('cardView');
        const emptyState = document.getElementById('emptyState');
        
        document.getElementById('packageCount').textContent = filtered.length;
        
        if (filtered.length === 0) {
            tableBody.innerHTML = '';
            cardView.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }
        
        emptyState.classList.add('hidden');
        
        // Desktop Table View
        tableBody.innerHTML = filtered.map(p => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 text-sm font-medium text-gray-900">${p.name}</td>
                <td class="px-4 py-3 text-sm text-gray-700">${p.duration} ${p.unit}</td>
                <td class="px-4 py-3 text-sm font-semibold text-gray-900">Rp ${p.price.toLocaleString('id-ID')}</td>
                <td class="px-4 py-3 text-sm text-gray-700">
                    ${p.discount}%<br>
                    <span class="text-xs text-gray-500">(Rp ${(p.price * p.discount / 100).toLocaleString('id-ID')})</span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">
                    ${p.weekend > 0 ? `Weekend: +${p.weekend}%<br>` : ''}
                    ${p.overtime > 0 ? `Overtime: Rp ${(p.overtime/1000).toFixed(0)}k/hr` : '-'}
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs font-medium rounded-full ${p.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                        ${p.status === 'active' ? '✅ Active' : '⏸️ Inactive'}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="relative inline-block">
                        <button onclick="toggleActionMenu(${p.id})" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                            </svg>
                        </button>
                        <div id="menu-${p.id}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                            <button onclick="editPricing(${p.id})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Package
                            </button>
                            <button onclick="duplicatePricing(${p.id})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Duplicate
                            </button>
                            <button onclick="viewUsageStats(${p.id})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                View Stats
                            </button>
                            <button onclick="toggleStatus(${p.id})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                ${p.status === 'active' ? 'Deactivate' : 'Activate'}
                            </button>
                            <button onclick="openDeleteModal(${p.id})" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 border-t">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
        `).join('');
        
        // Mobile/Tablet Card View
        cardView.innerHTML = filtered.map(p => `
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 mb-1">${p.name}</h3>
                        <p class="text-sm text-gray-600">${p.duration} ${p.unit}</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium rounded-full ${p.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                        ${p.status === 'active' ? '✅ Active' : '⏸️ Inactive'}
                    </span>
                </div>
                
                <div class="space-y-2 mb-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Base Price:</span>
                        <span class="font-semibold text-gray-900">Rp ${p.price.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Max Discount:</span>
                        <span class="text-gray-700">${p.discount}% (Rp ${(p.price * p.discount / 100).toLocaleString('id-ID')})</span>
                    </div>
                    ${p.weekend > 0 ? `
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Weekend Surcharge:</span>
                        <span class="text-gray-700">+${p.weekend}%</span>
                    </div>
                    ` : ''}
                    ${p.overtime > 0 ? `
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Overtime Rate:</span>
                        <span class="text-gray-700">Rp ${(p.overtime/1000).toFixed(0)}k/hr</span>
                    </div>
                    ` : ''}
                </div>
                
                <div class="flex gap-2">
                    <button onclick="editPricing(${p.id})" class="flex-1 px-3 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100">
                        Edit
                    </button>
                    <button onclick="duplicatePricing(${p.id})" class="px-3 py-2 bg-gray-50 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-100">
                        📋
                    </button>
                    <button onclick="toggleStatus(${p.id})" class="px-3 py-2 ${p.status === 'active' ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100' : 'bg-green-50 text-green-600 hover:bg-green-100'} rounded-lg text-sm font-medium">
                        ${p.status === 'active' ? '⏸️' : '✅'}
                    </button>
                    <button onclick="openDeleteModal(${p.id})" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100">
                        🗑️
                    </button>
                </div>
            </div>
        `).join('');
    }

    function updateStats() {
        document.getElementById('totalPackages').textContent = pricingData.length;
        
        const prices = pricingData.map(p => p.price);
        const minPrice = Math.min(...prices);
        const maxPrice = Math.max(...prices);
        document.getElementById('priceRange').textContent = `${(minPrice/1000).toFixed(0)}k - ${(maxPrice/1000000).toFixed(0)}M`;
        
        const recentCount = pricingData.filter(p => p.status === 'active').length;
        document.getElementById('recentUpdates').textContent = recentCount;
    }

    function toggleActionMenu(id) {
        document.querySelectorAll('[id^="menu-"]').forEach(menu => {
            if (menu.id !== `menu-${id}`) {
                menu.classList.add('hidden');
            }
        });
        document.getElementById(`menu-${id}`).classList.toggle('hidden');
    }

    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'Add New Pricing Package';
        document.getElementById('pricingForm').reset();
        document.getElementById('pricingId').value = '';
        document.getElementById('modalServiceType').value = currentService;
        document.querySelector('input[name="status"][value="active"]').checked = true;
        document.getElementById('weekendPercent').disabled = true;
        document.getElementById('overtimeRate').disabled = true;
        calculateMaxDiscountAmount();
        document.getElementById('pricingModal').classList.remove('hidden');
    }

    function editPricing(id) {
        const pricing = pricingData.find(p => p.id === id);
        if (!pricing) return;
        
        document.getElementById('modalTitle').textContent = 'Edit Pricing Package';
        document.getElementById('pricingId').value = pricing.id;
        document.getElementById('modalServiceType').value = pricing.service;
        document.getElementById('packageName').value = pricing.name;
        document.getElementById('durationValue').value = pricing.duration;
        document.getElementById('durationUnit').value = pricing.unit;
        document.getElementById('basePrice').value = pricing.price;
        document.getElementById('maxDiscount').value = pricing.discount;
        
        document.getElementById('weekendSurcharge').checked = pricing.weekend > 0;
        document.getElementById('weekendPercent').value = pricing.weekend;
        document.getElementById('weekendPercent').disabled = pricing.weekend === 0;
        
        document.getElementById('overtimeCharge').checked = pricing.overtime > 0;
        document.getElementById('overtimeRate').value = pricing.overtime;
        document.getElementById('overtimeRate').disabled = pricing.overtime === 0;
        
        document.querySelectorAll('.feature-check').forEach(cb => {
            cb.checked = pricing.features.includes(cb.value);
        });
        
        document.getElementById('notes').value = pricing.notes;
        document.querySelector(`input[name="status"][value="${pricing.status}"]`).checked = true;
        
        calculateMaxDiscountAmount();
        document.getElementById('pricingModal').classList.remove('hidden');
        toggleActionMenu(id);
    }

    function closeModal() {
        document.getElementById('pricingModal').classList.add('hidden');
    }

    function savePricing(event) {
        event.preventDefault();
        
        const id = document.getElementById('pricingId').value;
        const features = Array.from(document.querySelectorAll('.feature-check:checked')).map(cb => cb.value);
        
        const pricingObj = {
            service: document.getElementById('modalServiceType').value,
            name: document.getElementById('packageName').value,
            duration: parseInt(document.getElementById('durationValue').value),
            unit: document.getElementById('durationUnit').value,
            price: parseInt(document.getElementById('basePrice').value),
            discount: parseInt(document.getElementById('maxDiscount').value),
            weekend: document.getElementById('weekendSurcharge').checked ? parseInt(document.getElementById('weekendPercent').value) : 0,
            overtime: document.getElementById('overtimeCharge').checked ? parseInt(document.getElementById('overtimeRate').value) : 0,
            features: features,
            status: document.querySelector('input[name="status"]:checked').value,
            notes: document.getElementById('notes').value
        };
        
        if (id) {
            const index = pricingData.findIndex(p => p.id == id);
            pricingData[index] = { ...pricingData[index], ...pricingObj };
        } else {
            pricingObj.id = pricingData.length > 0 ? Math.max(...pricingData.map(p => p.id)) + 1 : 1;
            pricingData.push(pricingObj);
        }
        
        closeModal();
        renderPricing();
        updateStats();
        alert(id ? 'Pricing package updated successfully!' : 'Pricing package added successfully!');
    }

    function duplicatePricing(id) {
        const pricing = pricingData.find(p => p.id === id);
        if (!pricing) return;
        
        const newPricing = { 
            ...pricing, 
            id: Math.max(...pricingData.map(p => p.id)) + 1,
            name: pricing.name + ' (Copy)'
        };
        pricingData.push(newPricing);
        
        renderPricing();
        updateStats();
        toggleActionMenu(id);
        alert('Pricing package duplicated successfully!');
    }

    function toggleStatus(id) {
        const pricing = pricingData.find(p => p.id === id);
        if (pricing) {
            pricing.status = pricing.status === 'active' ? 'inactive' : 'active';
            renderPricing();
            updateStats();
            const menu = document.getElementById(`menu-${id}`);
            if (menu) menu.classList.add('hidden');
        }
    }

    function viewUsageStats(id) {
        const pricing = pricingData.find(p => p.id === id);
        if (!pricing) return;
        
        toggleActionMenu(id);
        alert(`Usage Statistics for ${pricing.name}:\n\nTotal Bookings: 145\nTotal Revenue: Rp ${(pricing.price * 145).toLocaleString('id-ID')}\nAverage per Month: 12 bookings\nMost Popular Branch: Surabaya`);
    }

    function openDeleteModal(id) {
        deleteTargetId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
        const menu = document.getElementById(`menu-${id}`);
        if (menu) menu.classList.add('hidden');
    }

    function closeDeleteModal() {
        deleteTargetId = null;
        document.getElementById('deleteModal').classList.add('hidden');
    }

    function confirmDelete() {
        if (deleteTargetId) {
            pricingData = pricingData.filter(p => p.id !== deleteTargetId);
            closeDeleteModal();
            renderPricing();
            updateStats();
            alert('Pricing package deleted successfully!');
        }
    }

    function calculateMaxDiscountAmount() {
        const basePrice = parseInt(document.getElementById('basePrice').value) || 0;
        const maxDiscount = parseInt(document.getElementById('maxDiscount').value) || 0;
        const maxAmount = basePrice * maxDiscount / 100;
        document.getElementById('maxDiscountAmount').textContent = `Rp ${maxAmount.toLocaleString('id-ID')}`;
    }

    function exportPricing() {
        const filtered = getFilteredPricing();
        const titles = {
            'meeting-room': 'Meeting Room',
            'private-office': 'Private Office',
            'sharing-room': 'Sharing Room',
            'coworking-space': 'Coworking Space',
            'virtual-office': 'Virtual Office',
            'event-space': 'Event Space'
        };
        
        let csvContent = `Service Type,Package Name,Duration,Unit,Base Price,Max Discount %,Weekend Surcharge %,Overtime Rate,Status,Notes\n`;
        
        filtered.forEach(p => {
            csvContent += `${titles[p.service]},${p.name},${p.duration},${p.unit},${p.price},${p.discount},${p.weekend},${p.overtime},${p.status},"${p.notes}"\n`;
        });
        
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `master-pricing-${currentService}-${new Date().toISOString().split('T')[0]}.csv`;
        a.click();
        window.URL.revokeObjectURL(url);
        
        alert('Pricing data exported successfully!');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('button')) {
            document.querySelectorAll('[id^="menu-"]').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
        if (event.target.id === 'pricingModal') {
            closeModal();
        }
        if (event.target.id === 'deleteModal') {
            closeDeleteModal();
        }
    });
</script>
@endsection