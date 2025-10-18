@extends('layouts.superadmin')

@section('title', 'All Mitra')

@section('content')
<div x-data="mitraManagementData()" x-init="init()" class="space-y-4 md:space-y-6">
    
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">Mitra Management</h1>
            <p class="text-sm text-gray-600 mt-1">Manage all partners and their branches</p>
        </div>
        <div class="flex items-center gap-2 sm:gap-3">
            <button @click="exportData()" class="px-3 sm:px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="hidden sm:inline">Export</span>
            </button>
            <button @click="showAddMitraModal = true" class="px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="hidden sm:inline">Add New Mitra</span>
                <span class="sm:hidden">Add</span>
            </button>
        </div>
    </div>

    {{-- Summary Statistics Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-600 truncate">Total Mitra</p>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800" x-text="stats.totalMitra">0</h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-600 truncate">Active Mitra</p>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800" x-text="stats.activeMitra">0</h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-600 truncate">Total Branches</p>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800" x-text="stats.totalBranches">0</h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-600 truncate">Total Revenue</p>
                    <h3 class="text-base md:text-xl font-bold text-gray-800" x-text="formatCurrency(stats.totalRevenue)">Rp 0</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Section --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base md:text-lg font-semibold text-gray-800">Filters</h2>
            <button @click="resetFilters()" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                Reset All
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select x-model="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="pending">Pending</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                <select x-model="filters.sortBy" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="name">Name (A-Z)</option>
                    <option value="revenue">Revenue (High to Low)</option>
                    <option value="branches">Branches (Most to Least)</option>
                    <option value="newest">Newest First</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <div class="relative">
                    <input type="text" x-model="filters.search" placeholder="Search by company name or contact..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                Showing <span class="font-semibold" x-text="filteredMitra.length"></span> of <span class="font-semibold" x-text="allMitra.length"></span> mitra
            </p>
        </div>
    </div>

    {{-- Desktop Table View --}}
    <div class="hidden lg:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mitra</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contact Person</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Branches</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Revenue/Month</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="mitra in filteredMitra" :key="mitra.id">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800" x-text="mitra.companyName"></p>
                                    <p class="text-xs text-gray-500" x-text="'Since ' + mitra.joinDate"></p>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-800" x-text="mitra.picName"></p>
                                    <p class="text-xs text-gray-500" x-text="mitra.picPhone"></p>
                                    <p class="text-xs text-gray-500" x-text="mitra.picEmail"></p>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800" x-text="mitra.branches.length + ' Branches'"></p>
                                    <p class="text-xs text-gray-500" x-text="mitra.branches.map(b => b.city).join(', ')"></p>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm font-semibold text-gray-800" x-text="formatCurrency(mitra.revenue)"></p>
                            </td>
                            <td class="px-4 py-4">
                                <span x-show="mitra.status === 'active'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                                <span x-show="mitra.status === 'inactive'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Inactive
                                </span>
                                <span x-show="mitra.status === 'pending'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <button @click="viewMitraDetail(mitra)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button @click="editMitra(mitra)" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button @click="manageBranches(mitra)" class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition" title="Manage Branches">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile & Tablet Card View --}}
    <div class="lg:hidden space-y-3">
        <template x-for="mitra in filteredMitra" :key="mitra.id">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-start justify-between mb-3 pb-3 border-b border-gray-200">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-gray-800 truncate" x-text="mitra.companyName"></h3>
                        <p class="text-xs text-gray-500 mt-1" x-text="'Since ' + mitra.joinDate"></p>
                    </div>
                    <div class="ml-3">
                        <span x-show="mitra.status === 'active'" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Active
                        </span>
                        <span x-show="mitra.status === 'inactive'" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Inactive
                        </span>
                        <span x-show="mitra.status === 'pending'" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Pending
                        </span>
                    </div>
                </div>

                <div class="space-y-2 text-sm mb-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Contact Person:</span>
                        <div class="text-right">
                            <p class="font-medium text-gray-800" x-text="mitra.picName"></p>
                            <p class="text-xs text-gray-500" x-text="mitra.picPhone"></p>
                        </div>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Branches:</span>
                        <div class="text-right">
                            <p class="font-medium text-gray-800" x-text="mitra.branches.length + ' Branches'"></p>
                            <p class="text-xs text-gray-500" x-text="mitra.branches.map(b => b.city).join(', ')"></p>
                        </div>
                    </div>

                    <div class="flex justify-between pt-2 border-t border-gray-200">
                        <span class="text-gray-600 font-medium">Revenue/Month:</span>
                        <span class="font-bold text-gray-800" x-text="formatCurrency(mitra.revenue)"></span>
                    </div>
                </div>

                <div class="flex gap-2 pt-3 border-t border-gray-200">
                    <button @click="viewMitraDetail(mitra)" class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-xs font-medium">
                        View Detail
                    </button>
                    <button @click="editMitra(mitra)" class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-xs font-medium">
                        Edit
                    </button>
                    <button @click="manageBranches(mitra)" class="px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-xs font-medium">
                        Branches
                    </button>
                </div>
            </div>
        </template>
    </div>

    {{-- Add/Edit Mitra Modal --}}
    <div x-show="showAddMitraModal" x-cloak @click.away="showAddMitraModal = false" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-black opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 transform transition-all max-h-screen overflow-y-auto">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <span x-show="!editMode">Add New Mitra</span>
                    <span x-show="editMode">Edit Mitra</span>
                </h3>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Company Name <span class="text-red-500">*</span></label>
                            <input type="text" x-model="mitraForm.companyName" placeholder="e.g., PT Maju Jaya" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">PIC Name <span class="text-red-500">*</span></label>
                            <input type="text" x-model="mitraForm.picName" placeholder="Person in charge name" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">PIC Phone <span class="text-red-500">*</span></label>
                            <input type="tel" x-model="mitraForm.picPhone" placeholder="08123456789" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">PIC Email <span class="text-red-500">*</span></label>
                            <input type="email" x-model="mitraForm.picEmail" placeholder="pic@company.com" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea x-model="mitraForm.address" rows="3" placeholder="Company address..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">NPWP</label>
                            <input type="text" x-model="mitraForm.npwp" placeholder="XX.XXX.XXX.X-XXX.XXX" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                            <select x-model="mitraForm.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Revenue Share Agreement</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Super Admin (%)</label>
                                <input type="number" x-model="mitraForm.revenueShareAdmin" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Mitra (%)</label>
                                <input type="number" x-model="mitraForm.revenueShareMitra" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">Total must equal 100%</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contract Start Date</label>
                            <input type="date" x-model="mitraForm.contractStart" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contract End Date</label>
                            <input type="date" x-model="mitraForm.contractEnd" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button @click="closeAddMitraModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        Cancel
                    </button>
                    <button @click="submitMitraForm()" :disabled="!canSubmitMitra" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed font-medium">
                        <span x-show="!editMode">Save Mitra</span>
                        <span x-show="editMode">Update Mitra</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Mitra Detail Modal --}}
    <div x-show="showDetailModal" x-cloak @click.away="showDetailModal = false" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-black opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full p-4 md:p-6 transform transition-all max-h-screen overflow-y-auto">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                    <div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-800" x-text="selectedMitra?.companyName"></h3>
                        <p class="text-sm text-gray-600 mt-1">Mitra Details & Information</p>
                    </div>
                    <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-6">
                    {{-- Company Information --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Company Information</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-gray-50 rounded-lg p-4">
                            <div>
                                <p class="text-xs text-gray-600">Company Name</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedMitra?.companyName"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">NPWP</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedMitra?.npwp || '-'"></p>
                            </div>
                            <div class="sm:col-span-2">
                                <p class="text-xs text-gray-600">Address</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedMitra?.address || '-'"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Join Date</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedMitra?.joinDate"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Status</p>
                                <span x-show="selectedMitra?.status === 'active'" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                                <span x-show="selectedMitra?.status === 'inactive'" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Inactive
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Person --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Contact Person</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-gray-50 rounded-lg p-4">
                            <div>
                                <p class="text-xs text-gray-600">Name</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedMitra?.picName"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Phone</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedMitra?.picPhone"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Email</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedMitra?.picEmail"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Revenue Share --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Revenue Share Agreement</h4>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">Super Admin</p>
                                    <p class="text-2xl font-bold text-blue-600" x-text="selectedMitra?.revenueShareAdmin + '%'"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">Mitra</p>
                                    <p class="text-2xl font-bold text-green-600" x-text="selectedMitra?.revenueShareMitra + '%'"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Branches List --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">
                            Branches (<span x-text="selectedMitra?.branches?.length"></span>)
                        </h4>
                        <div class="space-y-2">
                            <template x-for="branch in selectedMitra?.branches" :key="branch.id">
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800" x-text="branch.name"></p>
                                        <p class="text-xs text-gray-500" x-text="branch.city + ' - ' + branch.rooms + ' rooms'"></p>
                                    </div>
                                    <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded font-medium">Active</span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Revenue Summary --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Revenue Summary (This Month)</h4>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total Revenue:</span>
                                <span class="font-semibold text-gray-800" x-text="formatCurrency(selectedMitra?.revenue)"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Super Admin Share (<span x-text="selectedMitra?.revenueShareAdmin + '%'"></span>):</span>
                                <span class="font-semibold text-blue-600" x-text="formatCurrency(calculateShare(selectedMitra?.revenue, selectedMitra?.revenueShareAdmin))"></span>
                            </div>
                            <div class="flex justify-between text-sm pt-2 border-t border-gray-300">
                                <span class="text-gray-600">Mitra Share (<span x-text="selectedMitra?.revenueShareMitra + '%'"></span>):</span>
                                <span class="font-semibold text-green-600" x-text="formatCurrency(calculateShare(selectedMitra?.revenue, selectedMitra?.revenueShareMitra))"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-6 border-t border-gray-200">
                    <button @click="editMitra(selectedMitra)" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                        Edit Mitra
                    </button>
                    <button @click="manageBranches(selectedMitra)" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                        Manage Branches
                    </button>
                    <button @click="showDetailModal = false" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Alpine.js Data & Logic --}}
@push('scripts')
<script>
function mitraManagementData() {
    return {
        // Stats
        stats: {
            totalMitra: 5,
            activeMitra: 4,
            totalBranches: 8,
            totalRevenue: 450000000
        },

        // Filters
        filters: {
            status: '',
            sortBy: 'name',
            search: ''
        },

        // Modals
        showAddMitraModal: false,
        showDetailModal: false,
        editMode: false,

        // Selected Mitra
        selectedMitra: null,

        // Mitra Form
        mitraForm: {
            companyName: '',
            picName: '',
            picPhone: '',
            picEmail: '',
            address: '',
            npwp: '',
            status: 'active',
            revenueShareAdmin: 60,
            revenueShareMitra: 40,
            contractStart: '',
            contractEnd: ''
        },

        // All Mitra Data
        allMitra: [
            {
                id: 1,
                companyName: 'PT XYZ Indonesia',
                picName: 'John Doe',
                picPhone: '0812-3456-7890',
                picEmail: 'john@xyz.com',
                address: 'Jl. Sudirman No. 123, Jakarta',
                npwp: '01.234.567.8-901.000',
                status: 'active',
                joinDate: 'Jan 2024',
                revenue: 150000000,
                revenueShareAdmin: 60,
                revenueShareMitra: 40,
                branches: [
                    { id: 1, name: 'Surabaya 1', city: 'Surabaya', rooms: 15 },
                    { id: 2, name: 'Jakarta 1', city: 'Jakarta', rooms: 12 }
                ]
            },
            {
                id: 2,
                companyName: 'CV ABC Jaya',
                picName: 'Jane Smith',
                picPhone: '0813-5678-9012',
                picEmail: 'jane@abc.com',
                address: 'Jl. Asia Afrika No. 45, Bandung',
                npwp: '02.345.678.9-012.000',
                status: 'active',
                joinDate: 'Mar 2024',
                revenue: 80000000,
                revenueShareAdmin: 60,
                revenueShareMitra: 40,
                branches: [
                    { id: 3, name: 'Bandung 1', city: 'Bandung', rooms: 10 }
                ]
            },
            {
                id: 3,
                companyName: 'PT DEF Makmur',
                picName: 'Mike Lee',
                picPhone: '0815-7890-1234',
                picEmail: 'mike@def.com',
                address: 'Jl. Veteran No. 67, Malang',
                npwp: '03.456.789.0-123.000',
                status: 'active',
                joinDate: 'May 2024',
                revenue: 120000000,
                revenueShareAdmin: 60,
                revenueShareMitra: 40,
                branches: [
                    { id: 4, name: 'Malang 1', city: 'Malang', rooms: 8 },
                    { id: 5, name: 'Surabaya 2', city: 'Surabaya', rooms: 10 }
                ]
            },
            {
                id: 4,
                companyName: 'PT GHI Sejahtera',
                picName: 'Sarah Johnson',
                picPhone: '0816-8901-2345',
                picEmail: 'sarah@ghi.com',
                address: 'Jl. Gajah Mada No. 89, Semarang',
                npwp: '04.567.890.1-234.000',
                status: 'active',
                joinDate: 'Jul 2024',
                revenue: 100000000,
                revenueShareAdmin: 60,
                revenueShareMitra: 40,
                branches: [
                    { id: 6, name: 'Semarang 1', city: 'Semarang', rooms: 12 }
                ]
            },
            {
                id: 5,
                companyName: 'CV JKL Utama',
                picName: 'David Chen',
                picPhone: '0817-9012-3456',
                picEmail: 'david@jkl.com',
                address: 'Jl. Diponegoro No. 12, Yogyakarta',
                npwp: '05.678.901.2-345.000',
                status: 'pending',
                joinDate: 'Sep 2024',
                revenue: 0,
                revenueShareAdmin: 60,
                revenueShareMitra: 40,
                branches: [
                    { id: 7, name: 'Yogyakarta 1', city: 'Yogyakarta', rooms: 8 }
                ]
            }
        ],

        // Initialize
        init() {
            this.calculateStats();
        },

        // Computed
        get filteredMitra() {
            let filtered = this.allMitra;

            // Filter by status
            if (this.filters.status) {
                filtered = filtered.filter(m => m.status === this.filters.status);
            }

            // Search
            if (this.filters.search) {
                const search = this.filters.search.toLowerCase();
                filtered = filtered.filter(m => 
                    m.companyName.toLowerCase().includes(search) ||
                    m.picName.toLowerCase().includes(search) ||
                    m.picPhone.includes(search) ||
                    m.picEmail.toLowerCase().includes(search)
                );
            }

            // Sort
            if (this.filters.sortBy === 'name') {
                filtered.sort((a, b) => a.companyName.localeCompare(b.companyName));
            } else if (this.filters.sortBy === 'revenue') {
                filtered.sort((a, b) => b.revenue - a.revenue);
            } else if (this.filters.sortBy === 'branches') {
                filtered.sort((a, b) => b.branches.length - a.branches.length);
            } else if (this.filters.sortBy === 'newest') {
                filtered.sort((a, b) => new Date(b.joinDate) - new Date(a.joinDate));
            }

            return filtered;
        },

        get canSubmitMitra() {
            return this.mitraForm.companyName && 
                   this.mitraForm.picName && 
                   this.mitraForm.picPhone && 
                   this.mitraForm.picEmail &&
                   (parseInt(this.mitraForm.revenueShareAdmin) + parseInt(this.mitraForm.revenueShareMitra) === 100);
        },

        // Methods
        calculateStats() {
            this.stats.totalMitra = this.allMitra.length;
            this.stats.activeMitra = this.allMitra.filter(m => m.status === 'active').length;
            this.stats.totalBranches = this.allMitra.reduce((sum, m) => sum + m.branches.length, 0);
            this.stats.totalRevenue = this.allMitra.reduce((sum, m) => sum + m.revenue, 0);
        },

        formatCurrency(value) {
            return 'Rp ' + parseInt(value).toLocaleString('id-ID');
        },

        calculateShare(revenue, percentage) {
            return (revenue * percentage) / 100;
        },

        resetFilters() {
            this.filters = {
                status: '',
                sortBy: 'name',
                search: ''
            };
        },

        viewMitraDetail(mitra) {
            this.selectedMitra = mitra;
            this.showDetailModal = true;
        },

        editMitra(mitra) {
            this.editMode = true;
            this.selectedMitra = mitra;
            this.mitraForm = {
                companyName: mitra.companyName,
                picName: mitra.picName,
                picPhone: mitra.picPhone,
                picEmail: mitra.picEmail,
                address: mitra.address || '',
                npwp: mitra.npwp || '',
                status: mitra.status,
                revenueShareAdmin: mitra.revenueShareAdmin,
                revenueShareMitra: mitra.revenueShareMitra,
                contractStart: '',
                contractEnd: ''
            };
            this.showDetailModal = false;
            this.showAddMitraModal = true;
        },

        closeAddMitraModal() {
            this.showAddMitraModal = false;
            this.editMode = false;
            this.selectedMitra = null;
            this.mitraForm = {
                companyName: '',
                picName: '',
                picPhone: '',
                picEmail: '',
                address: '',
                npwp: '',
                status: 'active',
                revenueShareAdmin: 60,
                revenueShareMitra: 40,
                contractStart: '',
                contractEnd: ''
            };
        },

        submitMitraForm() {
            if (this.editMode) {
                alert('Updating mitra: ' + this.mitraForm.companyName);
                // Update logic here
            } else {
                alert('Adding new mitra: ' + this.mitraForm.companyName);
                // Add logic here
            }
            this.closeAddMitraModal();
        },

        manageBranches(mitra) {
            alert('Redirecting to branch management for: ' + mitra.companyName);
            // Redirect to branch management page
            // window.location.href = '/superadmin/mitra/' + mitra.id + '/branches';
        },

        exportData() {
            alert('Exporting mitra data to Excel...');
            // Export logic here
        }
    }
}
</script>
@endpush

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
@endsection