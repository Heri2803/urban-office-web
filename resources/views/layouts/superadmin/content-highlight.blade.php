{{-- resources/views/superadmin/content/highlights.blade.php --}}
@extends('layouts.superadmin')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">✨ Service Highlights Management</h1>
                <p class="text-gray-600 mt-1 text-sm md:text-base">Manage key features and highlights for each service</p>
            </div>
            <div class="flex gap-2">
                <button onclick="openAddModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Highlight
                </button>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
            <div class="text-2xl font-bold text-gray-900" id="totalHighlights">24</div>
            <div class="text-sm text-gray-600 mt-1">Total Highlights</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
            <div class="text-2xl font-bold text-green-600" id="activeHighlights">21</div>
            <div class="text-sm text-gray-600 mt-1">Active Highlights</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
            <div class="text-2xl font-bold text-purple-600" id="mostPopular">Meeting Room</div>
            <div class="text-sm text-gray-600 mt-1">Most Popular</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-orange-500">
            <div class="text-2xl font-bold text-orange-600" id="lastUpdated">2 hours ago</div>
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

    {{-- Filter & View Options --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-3 flex-1">
                <select id="statusFilter" onchange="applyFilters()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <select id="branchFilter" onchange="applyFilters()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Branches</option>
                    <option value="all">All Branches</option>
                    <option value="surabaya">Surabaya</option>
                    <option value="jakarta">Jakarta</option>
                    <option value="bandung">Bandung</option>
                </select>
                <input type="text" id="searchInput" onkeyup="applyFilters()" placeholder="Search highlights..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 flex-1">
            </div>
            <div class="flex gap-2">
                <button onclick="setViewMode('grid')" id="gridViewBtn" class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
                <button onclick="setViewMode('list')" id="listViewBtn" class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Highlights Grid/List --}}
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">
                <span id="serviceTitle">Meeting Room</span> Highlights (<span id="highlightCount">8</span>)
            </h2>
            <span class="text-sm text-gray-500" id="viewModeLabel">Grid View</span>
        </div>

        {{-- Grid View --}}
        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            {{-- Cards will be rendered by JavaScript --}}
        </div>

        {{-- List View --}}
        <div id="listView" class="hidden space-y-3">
            {{-- List items will be rendered by JavaScript --}}
        </div>

        {{-- Empty State --}}
        <div id="emptyState" class="hidden text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <p class="text-gray-500 text-lg font-medium">No highlights found</p>
            <p class="text-gray-400 text-sm mt-2">Try adjusting your filters or add a new highlight</p>
            <button onclick="openAddModal()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                Add First Highlight
            </button>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-sm text-gray-600" id="paginationInfo">Showing 1-8 of 8 highlights</p>
            <div class="flex gap-2" id="paginationButtons">
                <button class="px-3 py-1.5 border border-gray-300 rounded text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>Previous</button>
                <button class="px-3 py-1.5 bg-blue-600 text-white rounded text-sm font-medium">1</button>
                <button class="px-3 py-1.5 border border-gray-300 rounded text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>Next</button>
            </div>
        </div>
    </div>
</div>

{{-- Add/Edit Modal --}}
<div id="highlightModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <div onclick="closeModal()" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
        
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6 sm:p-8 my-8 max-h-[90vh] overflow-y-auto transform transition-all">
            <div class="flex items-center justify-between border-b pb-3 mb-6">
                <h3 class="text-xl font-bold text-gray-800" id="modalTitle">Add New Highlight</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="highlightForm" onsubmit="saveHighlight(event)">
                <input type="hidden" id="highlightId" value="">
                
                {{-- Service Type --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Type</label>
                    <select id="modalServiceType" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="meeting-room">Meeting Room</option>
                        <option value="private-office">Private Office</option>
                        <option value="sharing-room">Sharing Room</option>
                        <option value="coworking-space">Coworking Space</option>
                        <option value="virtual-office">Virtual Office</option>
                        <option value="event-space">Event Space</option>
                    </select>
                </div>

                {{-- Icon/Emoji --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Icon/Emoji</label>
                    <div class="flex gap-2">
                        <button type="button" onclick="toggleEmojiPicker()" class="px-4 py-2 border border-gray-300 rounded-lg text-2xl hover:bg-gray-50" id="selectedEmoji">📹</button>
                        <div class="flex-1">
                            <div id="emojiPicker" class="hidden absolute z-10 bg-white border border-gray-300 rounded-lg shadow-lg p-3 grid grid-cols-8 gap-2 max-w-sm">
                                <button type="button" onclick="selectEmoji('📹')" class="text-2xl hover:bg-gray-100 rounded p-1">📹</button>
                                <button type="button" onclick="selectEmoji('🖥️')" class="text-2xl hover:bg-gray-100 rounded p-1">🖥️</button>
                                <button type="button" onclick="selectEmoji('💻')" class="text-2xl hover:bg-gray-100 rounded p-1">💻</button>
                                <button type="button" onclick="selectEmoji('🌐')" class="text-2xl hover:bg-gray-100 rounded p-1">🌐</button>
                                <button type="button" onclick="selectEmoji('☕')" class="text-2xl hover:bg-gray-100 rounded p-1">☕</button>
                                <button type="button" onclick="selectEmoji('❄️')" class="text-2xl hover:bg-gray-100 rounded p-1">❄️</button>
                                <button type="button" onclick="selectEmoji('🎨')" class="text-2xl hover:bg-gray-100 rounded p-1">🎨</button>
                                <button type="button" onclick="selectEmoji('📝')" class="text-2xl hover:bg-gray-100 rounded p-1">📝</button>
                                <button type="button" onclick="selectEmoji('🚗')" class="text-2xl hover:bg-gray-100 rounded p-1">🚗</button>
                                <button type="button" onclick="selectEmoji('🔒')" class="text-2xl hover:bg-gray-100 rounded p-1">🔒</button>
                                <button type="button" onclick="selectEmoji('🧹')" class="text-2xl hover:bg-gray-100 rounded p-1">🧹</button>
                                <button type="button" onclick="selectEmoji('📞')" class="text-2xl hover:bg-gray-100 rounded p-1">📞</button>
                                <button type="button" onclick="selectEmoji('🏢')" class="text-2xl hover:bg-gray-100 rounded p-1">🏢</button>
                                <button type="button" onclick="selectEmoji('🚇')" class="text-2xl hover:bg-gray-100 rounded p-1">🚇</button>
                                <button type="button" onclick="selectEmoji('🍔')" class="text-2xl hover:bg-gray-100 rounded p-1">🍔</button>
                                <button type="button" onclick="selectEmoji('🌳')" class="text-2xl hover:bg-gray-100 rounded p-1">🌳</button>
                            </div>
                            <input type="hidden" id="iconInput" value="📹">
                            <p class="text-xs text-gray-500 mt-1">Click the icon to choose an emoji</p>
                        </div>
                    </div>
                </div>

                {{-- Title --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Highlight Title <span class="text-red-500">*</span></label>
                    <input type="text" id="titleInput" maxlength="50" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., HD Video Conferencing System">
                    <p class="text-xs text-gray-500 mt-1">Max 50 characters - <span id="titleCount">0</span>/50</p>
                </div>

                {{-- Description --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description <span class="text-red-500">*</span></label>
                    <textarea id="descriptionInput" maxlength="200" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Detailed description of the highlight..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Max 200 characters - <span id="descCount">0</span>/200</p>
                </div>

                {{-- Priority --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority Order</label>
                    <select id="priorityInput" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="1">1 - Highest (shown first)</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10 - Lowest (shown last)</option>
                    </select>
                </div>

                {{-- Status --}}
                <div class="mb-4">
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

                {{-- Branch Scope --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Apply to</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="branchScope" value="all" checked onclick="toggleBranchList()" class="text-blue-600 focus:ring-blue-500 mr-2">
                            <span class="text-sm text-gray-700">All Branches</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="branchScope" value="specific" onclick="toggleBranchList()" class="text-blue-600 focus:ring-blue-500 mr-2">
                            <span class="text-sm text-gray-700">Specific Branches:</span>
                        </label>
                        <div id="branchList" class="hidden ml-6 space-y-1">
                            <label class="flex items-center">
                                <input type="checkbox" value="surabaya" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                                <span class="text-sm text-gray-700">Surabaya</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" value="jakarta" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                                <span class="text-sm text-gray-700">Jakarta</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" value="bandung" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                                <span class="text-sm text-gray-700">Bandung</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" value="bali" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                                <span class="text-sm text-gray-700">Bali</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t">
                    <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        Save Highlight
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
                <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Highlight?</h3>
                <p class="text-sm text-gray-600 mb-6">Are you sure you want to delete this highlight? This action cannot be undone.</p>
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
    // Data State
    let currentService = 'meeting-room';
    let viewMode = 'grid';
    let deleteTargetId = null;
    let highlights = [
        { id: 1, service: 'meeting-room', icon: '📹', title: 'HD Video Conferencing', description: 'Professional HD video conferencing system with 4K resolution and AI auto-framing for seamless meetings', priority: 1, status: 'active', branch: 'all' },
        { id: 2, service: 'meeting-room', icon: '🖥️', title: 'Large Screen Display', description: '65" 4K display perfect for presentations and collaborative work sessions', priority: 2, status: 'active', branch: 'all' },
        { id: 3, service: 'meeting-room', icon: '🌐', title: 'Fast WiFi Connection', description: '1Gbps dedicated internet connection for smooth video calls and file sharing', priority: 3, status: 'active', branch: 'all' },
        { id: 4, service: 'meeting-room', icon: '☕', title: 'Free Coffee & Snacks', description: 'Complimentary beverages and light snacks throughout your booking', priority: 4, status: 'inactive', branch: 'surabaya' },
        { id: 5, service: 'meeting-room', icon: '🎨', title: 'Whiteboard & Markers', description: 'Large whiteboard with premium markers for brainstorming sessions', priority: 5, status: 'active', branch: 'all' },
        { id: 6, service: 'meeting-room', icon: '❄️', title: 'Climate Control AC', description: 'Individual AC control for optimal comfort during meetings', priority: 6, status: 'active', branch: 'all' },
        { id: 7, service: 'meeting-room', icon: '🔒', title: 'Secure & Private', description: 'Soundproof walls and secure access for confidential discussions', priority: 7, status: 'active', branch: 'all' },
        { id: 8, service: 'meeting-room', icon: '🚗', title: 'Free Parking', description: 'Complimentary parking space for meeting participants', priority: 8, status: 'active', branch: 'jakarta' },
        { id: 9, service: 'private-office', icon: '💻', title: 'Ergonomic Workstation', description: 'Adjustable desk and ergonomic chair for maximum productivity', priority: 1, status: 'active', branch: 'all' },
        { id: 10, service: 'private-office', icon: '🔒', title: '24/7 Secure Access', description: 'Round-the-clock access with advanced security system', priority: 2, status: 'active', branch: 'all' }
    ];

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        renderHighlights();
        updateStats();
        setupCharCounters();
    });

    // Service Tab Switching
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
        
        renderHighlights();
    }

    // View Mode Toggle
    function setViewMode(mode) {
        viewMode = mode;
        document.getElementById('gridViewBtn').classList.toggle('bg-blue-600', mode === 'grid');
        document.getElementById('gridViewBtn').classList.toggle('text-white', mode === 'grid');
        document.getElementById('gridViewBtn').classList.toggle('border', mode === 'list');
        document.getElementById('gridViewBtn').classList.toggle('border-gray-300', mode === 'list');
        document.getElementById('gridViewBtn').classList.toggle('text-gray-700', mode === 'list');
        
        document.getElementById('listViewBtn').classList.toggle('bg-blue-600', mode === 'list');
        document.getElementById('listViewBtn').classList.toggle('text-white', mode === 'list');
        document.getElementById('listViewBtn').classList.toggle('border', mode === 'grid');
        document.getElementById('listViewBtn').classList.toggle('border-gray-300', mode === 'grid');
        document.getElementById('listViewBtn').classList.toggle('text-gray-700', mode === 'grid');
        
        document.getElementById('gridView').classList.toggle('hidden', mode === 'list');
        document.getElementById('listView').classList.toggle('hidden', mode === 'grid');
        document.getElementById('viewModeLabel').textContent = mode === 'grid' ? 'Grid View' : 'List View';
        
        renderHighlights();
    }

    // Filter Highlights
    function applyFilters() {
        renderHighlights();
    }

    function getFilteredHighlights() {
        let filtered = highlights.filter(h => h.service === currentService);
        
        const statusFilter = document.getElementById('statusFilter').value;
        if (statusFilter) {
            filtered = filtered.filter(h => h.status === statusFilter);
        }
        
        const branchFilter = document.getElementById('branchFilter').value;
        if (branchFilter) {
            filtered = filtered.filter(h => h.branch === branchFilter || h.branch === 'all');
        }
        
        const search = document.getElementById('searchInput').value.toLowerCase();
        if (search) {
            filtered = filtered.filter(h => 
                h.title.toLowerCase().includes(search) || 
                h.description.toLowerCase().includes(search)
            );
        }
        
        return filtered.sort((a, b) => a.priority - b.priority);
    }

    // Render Highlights
    function renderHighlights() {
        const filtered = getFilteredHighlights();
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');
        const emptyState = document.getElementById('emptyState');
        
        document.getElementById('highlightCount').textContent = filtered.length;
        
        if (filtered.length === 0) {
            gridView.innerHTML = '';
            listView.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }
        
        emptyState.classList.add('hidden');
        
        if (viewMode === 'grid') {
            gridView.innerHTML = filtered.map(h => `
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <div class="text-4xl">${h.icon}</div>
                        <div class="flex gap-1">
                            <button onclick="moveUp(${h.id})" class="p-1 text-gray-400 hover:text-blue-600" title="Move Up">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                </svg>
                            </button>
                            <button onclick="moveDown(${h.id})" class="p-1 text-gray-400 hover:text-blue-600" title="Move Down">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2 line-clamp-1">${h.title}</h3>
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">${h.description}</p>
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                        <span class="font-medium">Priority: ${h.priority}</span>
                        <span class="px-2 py-1 rounded-full ${h.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">${h.status === 'active' ? '✅ Active' : '⏸️ Inactive'}</span>
                    </div>
                    <div class="text-xs text-gray-500 mb-3">
                        <span class="font-medium">Branch:</span> ${h.branch === 'all' ? 'All Branches' : h.branch.charAt(0).toUpperCase() + h.branch.slice(1)}
                    </div>
                    <div class="flex gap-2">
                        <button onclick="editHighlight(${h.id})" class="flex-1 px-3 py-1.5 bg-blue-50 text-blue-600 rounded text-sm font-medium hover:bg-blue-100 transition-colors">
                            Edit
                        </button>
                        <button onclick="toggleStatus(${h.id})" class="px-3 py-1.5 ${h.status === 'active' ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100' : 'bg-green-50 text-green-600 hover:bg-green-100'} rounded text-sm font-medium transition-colors">
                            ${h.status === 'active' ? '⏸️' : '✅'}
                        </button>
                        <button onclick="openDeleteModal(${h.id})" class="px-3 py-1.5 bg-red-50 text-red-600 rounded text-sm font-medium hover:bg-red-100 transition-colors">
                            🗑️
                        </button>
                    </div>
                </div>
            `).join('');
        } else {
            listView.innerHTML = filtered.map(h => `
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4">
                        <div class="text-3xl flex-shrink-0">${h.icon}</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4 mb-2">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 mb-1">${h.title}</h3>
                                    <p class="text-sm text-gray-600 line-clamp-2">${h.description}</p>
                                </div>
                                <div class="flex gap-2 flex-shrink-0">
                                    <button onclick="moveUp(${h.id})" class="p-1 text-gray-400 hover:text-blue-600" title="Move Up">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    </button>
                                    <button onclick="moveDown(${h.id})" class="p-1 text-gray-400 hover:text-blue-600" title="Move Down">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                <span class="font-medium">Priority: ${h.priority}</span>
                                <span class="px-2 py-1 rounded-full ${h.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">${h.status === 'active' ? '✅ Active' : '⏸️ Inactive'}</span>
                                <span>${h.branch === 'all' ? 'All Branches' : h.branch.charAt(0).toUpperCase() + h.branch.slice(1)}</span>
                            </div>
                        </div>
                        <div class="flex gap-2 flex-shrink-0">
                            <button onclick="editHighlight(${h.id})" class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded text-sm font-medium hover:bg-blue-100">
                                Edit
                            </button>
                            <button onclick="toggleStatus(${h.id})" class="px-3 py-1.5 ${h.status === 'active' ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100' : 'bg-green-50 text-green-600 hover:bg-green-100'} rounded text-sm font-medium">
                                ${h.status === 'active' ? '⏸️' : '✅'}
                            </button>
                            <button onclick="openDeleteModal(${h.id})" class="px-3 py-1.5 bg-red-50 text-red-600 rounded text-sm font-medium hover:bg-red-100">
                                🗑️
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }
    }

    // Update Stats
    function updateStats() {
        document.getElementById('totalHighlights').textContent = highlights.length;
        document.getElementById('activeHighlights').textContent = highlights.filter(h => h.status === 'active').length;
        
        const serviceCount = highlights.reduce((acc, h) => {
            acc[h.service] = (acc[h.service] || 0) + 1;
            return acc;
        }, {});
        const mostPopular = Object.keys(serviceCount).reduce((a, b) => serviceCount[a] > serviceCount[b] ? a : b);
        const titles = {
            'meeting-room': 'Meeting Room',
            'private-office': 'Private Office',
            'sharing-room': 'Sharing Room',
            'coworking-space': 'Coworking',
            'virtual-office': 'Virtual Office',
            'event-space': 'Event Space'
        };
        document.getElementById('mostPopular').textContent = titles[mostPopular] || 'N/A';
    }

    // Modal Functions
    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'Add New Highlight';
        document.getElementById('highlightForm').reset();
        document.getElementById('highlightId').value = '';
        document.getElementById('modalServiceType').value = currentService;
        document.getElementById('selectedEmoji').textContent = '📹';
        document.getElementById('iconInput').value = '📹';
        document.querySelector('input[name="status"][value="active"]').checked = true;
        document.querySelector('input[name="branchScope"][value="all"]').checked = true;
        document.getElementById('branchList').classList.add('hidden');
        document.getElementById('highlightModal').classList.remove('hidden');
        updateCharCounters();
    }

    function editHighlight(id) {
        const highlight = highlights.find(h => h.id === id);
        if (!highlight) return;
        
        document.getElementById('modalTitle').textContent = 'Edit Highlight';
        document.getElementById('highlightId').value = highlight.id;
        document.getElementById('modalServiceType').value = highlight.service;
        document.getElementById('selectedEmoji').textContent = highlight.icon;
        document.getElementById('iconInput').value = highlight.icon;
        document.getElementById('titleInput').value = highlight.title;
        document.getElementById('descriptionInput').value = highlight.description;
        document.getElementById('priorityInput').value = highlight.priority;
        document.querySelector(`input[name="status"][value="${highlight.status}"]`).checked = true;
        
        if (highlight.branch === 'all') {
            document.querySelector('input[name="branchScope"][value="all"]').checked = true;
            document.getElementById('branchList').classList.add('hidden');
        } else {
            document.querySelector('input[name="branchScope"][value="specific"]').checked = true;
            document.getElementById('branchList').classList.remove('hidden');
            document.querySelectorAll('#branchList input[type="checkbox"]').forEach(cb => {
                cb.checked = cb.value === highlight.branch;
            });
        }
        
        document.getElementById('highlightModal').classList.remove('hidden');
        updateCharCounters();
    }

    function closeModal() {
        document.getElementById('highlightModal').classList.add('hidden');
        document.getElementById('emojiPicker').classList.add('hidden');
    }

    function saveHighlight(event) {
        event.preventDefault();
        
        const id = document.getElementById('highlightId').value;
        const branchScope = document.querySelector('input[name="branchScope"]:checked').value;
        let branch = 'all';
        
        if (branchScope === 'specific') {
            const checkedBranches = Array.from(document.querySelectorAll('#branchList input[type="checkbox"]:checked')).map(cb => cb.value);
            if (checkedBranches.length === 0) {
                alert('Please select at least one branch');
                return;
            }
            branch = checkedBranches[0]; // Simplified: take first checked
        }
        
        const highlightData = {
            service: document.getElementById('modalServiceType').value,
            icon: document.getElementById('iconInput').value,
            title: document.getElementById('titleInput').value,
            description: document.getElementById('descriptionInput').value,
            priority: parseInt(document.getElementById('priorityInput').value),
            status: document.querySelector('input[name="status"]:checked').value,
            branch: branch
        };
        
        if (id) {
            const index = highlights.findIndex(h => h.id == id);
            highlights[index] = { ...highlights[index], ...highlightData };
        } else {
            highlightData.id = highlights.length > 0 ? Math.max(...highlights.map(h => h.id)) + 1 : 1;
            highlights.push(highlightData);
        }
        
        closeModal();
        renderHighlights();
        updateStats();
        
        // Show success message
        alert(id ? 'Highlight updated successfully!' : 'Highlight added successfully!');
    }

    // Delete Functions
    function openDeleteModal(id) {
        deleteTargetId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        deleteTargetId = null;
        document.getElementById('deleteModal').classList.add('hidden');
    }

    function confirmDelete() {
        if (deleteTargetId) {
            highlights = highlights.filter(h => h.id !== deleteTargetId);
            closeDeleteModal();
            renderHighlights();
            updateStats();
            alert('Highlight deleted successfully!');
        }
    }

    // Priority Functions
    function moveUp(id) {
        const filtered = getFilteredHighlights();
        const index = filtered.findIndex(h => h.id === id);
        if (index > 0) {
            const current = highlights.find(h => h.id === id);
            const above = filtered[index - 1];
            const temp = current.priority;
            current.priority = above.priority;
            above.priority = temp;
            renderHighlights();
        }
    }

    function moveDown(id) {
        const filtered = getFilteredHighlights();
        const index = filtered.findIndex(h => h.id === id);
        if (index < filtered.length - 1) {
            const current = highlights.find(h => h.id === id);
            const below = filtered[index + 1];
            const temp = current.priority;
            current.priority = below.priority;
            below.priority = temp;
            renderHighlights();
        }
    }

    // Toggle Status
    function toggleStatus(id) {
        const highlight = highlights.find(h => h.id === id);
        if (highlight) {
            highlight.status = highlight.status === 'active' ? 'inactive' : 'active';
            renderHighlights();
            updateStats();
        }
    }

    // Emoji Picker
    function toggleEmojiPicker() {
        document.getElementById('emojiPicker').classList.toggle('hidden');
    }

    function selectEmoji(emoji) {
        document.getElementById('selectedEmoji').textContent = emoji;
        document.getElementById('iconInput').value = emoji;
        document.getElementById('emojiPicker').classList.add('hidden');
    }

    // Branch Scope Toggle
    function toggleBranchList() {
        const isSpecific = document.querySelector('input[name="branchScope"][value="specific"]').checked;
        document.getElementById('branchList').classList.toggle('hidden', !isSpecific);
    }

    // Character Counters
    function setupCharCounters() {
        document.getElementById('titleInput').addEventListener('input', function() {
            document.getElementById('titleCount').textContent = this.value.length;
        });
        
        document.getElementById('descriptionInput').addEventListener('input', function() {
            document.getElementById('descCount').textContent = this.value.length;
        });
    }

    function updateCharCounters() {
        document.getElementById('titleCount').textContent = document.getElementById('titleInput').value.length;
        document.getElementById('descCount').textContent = document.getElementById('descriptionInput').value.length;
    }

    // Close modals on outside click
    document.addEventListener('click', function(event) {
        if (event.target.id === 'highlightModal') {
            closeModal();
        }
        if (event.target.id === 'deleteModal') {
            closeDeleteModal();
        }
    });
</script>

<style>
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection