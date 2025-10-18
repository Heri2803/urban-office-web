@extends('layouts.superadmin')

@section('title', 'All Branches')

@section('content')
<div x-data="branchManagementData()" x-init="init()" class="space-y-4 md:space-y-6">
    
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">Branch Management</h1>
            <p class="text-sm text-gray-600 mt-1">Manage all branches grouped by mitra</p>
        </div>
        <div class="flex items-center gap-2 sm:gap-3">
            <button @click="exportData()" class="px-3 sm:px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="hidden sm:inline">Export</span>
            </button>
            <button @click="showAddBranchModal = true" class="px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="hidden sm:inline">Add New Branch</span>
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
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-600 truncate">Active Branches</p>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800" x-text="stats.activeBranches">0</h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-600 truncate">Total Rooms</p>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800" x-text="stats.totalRooms">0</h3>
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Mitra</label>
                <select x-model="filters.mitra" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Mitra</option>
                    <template x-for="mitra in uniqueMitra" :key="mitra">
                        <option :value="mitra" x-text="mitra"></option>
                    </template>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                <select x-model="filters.city" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Cities</option>
                    <template x-for="city in uniqueCities" :key="city">
                        <option :value="city" x-text="city"></option>
                    </template>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select x-model="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <div class="relative">
                    <input type="text" x-model="filters.search" placeholder="Search branch name..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                Showing <span class="font-semibold" x-text="filteredBranches.length"></span> of <span class="font-semibold" x-text="allBranches.length"></span> branches
            </p>
        </div>
    </div>

    {{-- Grouped by Mitra View --}}
    <div class="space-y-4">
        <template x-for="mitraGroup in groupedBranches" :key="mitraGroup.mitra">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                {{-- Mitra Header --}}
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200 p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-800" x-text="mitraGroup.mitra"></h3>
                            <p class="text-sm text-gray-600 mt-1">
                                <span x-text="mitraGroup.branches.length + ' Branches'"></span> | 
                                <span x-text="mitraGroup.totalRooms + ' Total Rooms'"></span> | 
                                <span x-text="'Revenue: ' + formatCurrency(mitraGroup.totalRevenue)"></span>
                            </p>
                        </div>
                        <button @click="toggleMitraGroup(mitraGroup.mitra)" class="p-2 hover:bg-blue-200 rounded-lg transition">
                            <svg class="w-5 h-5 text-gray-600 transition-transform" :class="expandedMitra.includes(mitraGroup.mitra) ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Branches Table/Cards --}}
                <div x-show="expandedMitra.includes(mitraGroup.mitra)" x-collapse>
                    {{-- Desktop Table --}}
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Branch Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">City</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Address</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Rooms</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Admins</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Revenue</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="branch in mitraGroup.branches" :key="branch.id">
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-4">
                                            <p class="text-sm font-semibold text-gray-800" x-text="branch.name"></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-sm text-gray-800" x-text="branch.city"></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-sm text-gray-600 max-w-xs truncate" x-text="branch.address"></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-sm font-medium text-gray-800" x-text="branch.rooms + ' rooms'"></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-sm text-gray-800" x-text="branch.admins + ' admins'"></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="text-sm font-semibold text-gray-800" x-text="formatCurrency(branch.revenue)"></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span x-show="branch.status === 'active'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                            <span x-show="branch.status === 'inactive'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Inactive
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-2">
                                                <button @click="viewBranchDetail(branch)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Detail">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </button>
                                                <button @click="editBranch(branch)" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="lg:hidden p-4 space-y-3">
                        <template x-for="branch in mitraGroup.branches" :key="branch.id">
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-gray-800" x-text="branch.name"></h4>
                                        <p class="text-xs text-gray-500 mt-1" x-text="branch.city"></p>
                                    </div>
                                    <span x-show="branch.status === 'active'" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                    <span x-show="branch.status === 'inactive'" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                </div>

                                <div class="space-y-2 text-sm mb-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Address:</span>
                                        <span class="font-medium text-gray-800 text-right max-w-[60%] truncate" x-text="branch.address"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Rooms:</span>
                                        <span class="font-medium text-gray-800" x-text="branch.rooms + ' rooms'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Admins:</span>
                                        <span class="font-medium text-gray-800" x-text="branch.admins + ' admins'"></span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t border-gray-300">
                                        <span class="text-gray-600 font-medium">Revenue:</span>
                                        <span class="font-bold text-gray-800" x-text="formatCurrency(branch.revenue)"></span>
                                    </div>
                                </div>

                                <div class="flex gap-2 pt-3 border-t border-gray-300">
                                    <button @click="viewBranchDetail(branch)" class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-xs font-medium">
                                        View Detail
                                    </button>
                                    <button @click="editBranch(branch)" class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-xs font-medium">
                                        Edit
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Add/Edit Branch Modal --}}
    <div x-show="showAddBranchModal" x-cloak @click.away="showAddBranchModal = false" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-black opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 transform transition-all max-h-screen overflow-y-auto">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <span x-show="!editMode">Add New Branch</span>
                    <span x-show="editMode">Edit Branch</span>
                </h3>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mitra <span class="text-red-500">*</span></label>
                            <select x-model="branchForm.mitra" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select mitra...</option>
                                <option value="PT XYZ Indonesia">PT XYZ Indonesia</option>
                                <option value="CV ABC Jaya">CV ABC Jaya</option>
                                <option value="PT DEF Makmur">PT DEF Makmur</option>
                                <option value="PT GHI Sejahtera">PT GHI Sejahtera</option>
                                <option value="CV JKL Utama">CV JKL Utama</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Branch Name <span class="text-red-500">*</span></label>
                            <input type="text" x-model="branchForm.name" placeholder="e.g., Surabaya 2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">City <span class="text-red-500">*</span></label>
                            <select x-model="branchForm.city" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select city...</option>
                                <option value="Surabaya">Surabaya</option>
                                <option value="Jakarta">Jakarta</option>
                                <option value="Bandung">Bandung</option>
                                <option value="Semarang">Semarang</option>
                                <option value="Yogyakarta">Yogyakarta</option>
                                <option value="Malang">Malang</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address <span class="text-red-500">*</span></label>
                            <textarea x-model="branchForm.address" rows="3" placeholder="Full branch address..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Rooms</label>
                            <input type="number" x-model="branchForm.rooms" min="0" placeholder="Number of rooms" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Operating Hours</label>
                            <div class="flex items-center gap-2">
                                <input type="time" x-model="branchForm.openTime" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <span class="text-gray-600">-</span>
                                <input type="time" x-model="branchForm.closeTime" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                            <select x-model="branchForm.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                            <input type="tel" x-model="branchForm.phone" placeholder="Branch phone number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button @click="closeAddBranchModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        Cancel
                    </button>
                    <button @click="submitBranchForm()" :disabled="!canSubmitBranch" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed font-medium">
                        <span x-show="!editMode">Save Branch</span>
                        <span x-show="editMode">Update Branch</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Branch Detail Modal --}}
    <div x-show="showDetailModal" x-cloak @click.away="showDetailModal = false" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-black opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full p-4 md:p-6 transform transition-all max-h-screen overflow-y-auto">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                    <div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-800" x-text="selectedBranch?.name"></h3>
                        <p class="text-sm text-gray-600 mt-1" x-text="'Mitra: ' + selectedBranch?.mitra"></p>
                    </div>
                    <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-6">
                    {{-- Branch Information --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Branch Information</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-gray-50 rounded-lg p-4">
                            <div>
                                <p class="text-xs text-gray-600">Branch Name</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedBranch?.name"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">City</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedBranch?.city"></p>
                            </div>
                            <div class="sm:col-span-2">
                                <p class="text-xs text-gray-600">Address</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedBranch?.address"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Phone</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedBranch?.phone || '-'"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Operating Hours</p>
                                <p class="text-sm font-medium text-gray-800" x-text="selectedBranch?.openTime + ' - ' + selectedBranch?.closeTime"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Status</p>
                                <span x-show="selectedBranch?.status === 'active'" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                                <span x-show="selectedBranch?.status === 'inactive'" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Inactive
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Statistics --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Statistics</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-xs text-gray-600 mb-1">Total Rooms</p>
                                <p class="text-2xl font-bold text-blue-600" x-text="selectedBranch?.rooms"></p>
                            </div>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <p class="text-xs text-gray-600 mb-1">Total Admins</p>
                                <p class="text-2xl font-bold text-green-600" x-text="selectedBranch?.admins"></p>
                            </div>
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                <p class="text-xs text-gray-600 mb-1">Revenue/Month</p>
                                <p class="text-lg font-bold text-purple-600" x-text="formatCurrency(selectedBranch?.revenue)"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Assigned Admins --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">
                            Assigned Admins (<span x-text="selectedBranch?.adminList?.length || 0"></span>)
                        </h4>
                        <div x-show="selectedBranch?.adminList && selectedBranch.adminList.length > 0" class="space-y-2">
                            <template x-for="admin in selectedBranch?.adminList" :key="admin.id">
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800" x-text="admin.name"></p>
                                        <p class="text-xs text-gray-500" x-text="admin.email"></p>
                                    </div>
                                    <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded font-medium">Active</span>
                                </div>
                            </template>
                        </div>
                        <div x-show="!selectedBranch?.adminList || selectedBranch.adminList.length === 0" class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500">No admins assigned yet</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-6 border-t border-gray-200">
                    <button @click="editBranch(selectedBranch)" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                        Edit Branch
                    </button>
                    <button @click="manageAdmins(selectedBranch)" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                        Manage Admins
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
function branchManagementData() {
    return {
        // Stats
        stats: {
            totalBranches: 8,
            activeBranches: 7,
            totalRooms: 85,
            totalRevenue: 450000000
        },

        // Filters
        filters: {
            mitra: '',
            city: '',
            status: '',
            search: ''
        },

        // Expanded Mitra Groups
        expandedMitra: ['PT XYZ Indonesia', 'CV ABC Jaya', 'PT DEF Makmur', 'PT GHI Sejahtera'],

        // Modals
        showAddBranchModal: false,
        showDetailModal: false,
        editMode: false,

        // Selected Branch
        selectedBranch: null,

        // Branch Form
        branchForm: {
            mitra: '',
            name: '',
            city: '',
            address: '',
            rooms: 0,
            openTime: '08:00',
            closeTime: '17:00',
            status: 'active',
            phone: ''
        },

        // All Branches Data
        allBranches: [
            {
                id: 1,
                mitra: 'PT XYZ Indonesia',
                name: 'Surabaya 1',
                city: 'Surabaya',
                address: 'Jl. HR Muhammad No. 123, Surabaya',
                phone: '031-1234567',
                rooms: 15,
                admins: 2,
                revenue: 75000000,
                status: 'active',
                openTime: '08:00',
                closeTime: '17:00',
                adminList: [
                    { id: 1, name: 'Admin Budi', email: 'budi@xyz.com' },
                    { id: 2, name: 'Admin Sari', email: 'sari@xyz.com' }
                ]
            },
            {
                id: 2,
                mitra: 'PT XYZ Indonesia',
                name: 'Jakarta 1',
                city: 'Jakarta',
                address: 'Jl. Sudirman No. 456, Jakarta Selatan',
                phone: '021-9876543',
                rooms: 12,
                admins: 2,
                revenue: 75000000,
                status: 'active',
                openTime: '08:00',
                closeTime: '17:00',
                adminList: [
                    { id: 3, name: 'Admin Siti', email: 'siti@xyz.com' }
                ]
            },
            {
                id: 3,
                mitra: 'CV ABC Jaya',
                name: 'Bandung 1',
                city: 'Bandung',
                address: 'Jl. Asia Afrika No. 789, Bandung',
                phone: '022-5555555',
                rooms: 10,
                admins: 1,
                revenue: 80000000,
                status: 'active',
                openTime: '08:00',
                closeTime: '17:00',
                adminList: [
                    { id: 4, name: 'Admin Andi', email: 'andi@abc.com' }
                ]
            },
            {
                id: 4,
                mitra: 'PT DEF Makmur',
                name: 'Malang 1',
                city: 'Malang',
                address: 'Jl. Veteran No. 321, Malang',
                phone: '0341-777888',
                rooms: 8,
                admins: 1,
                revenue: 60000000,
                status: 'active',
                openTime: '08:00',
                closeTime: '17:00',
                adminList: [
                    { id: 5, name: 'Admin Rudi', email: 'rudi@def.com' }
                ]
            },
            {
                id: 5,
                mitra: 'PT DEF Makmur',
                name: 'Surabaya 2',
                city: 'Surabaya',
                address: 'Jl. Raya Darmo No. 555, Surabaya',
                phone: '031-8889999',
                rooms: 10,
                admins: 1,
                revenue: 60000000,
                status: 'active',
                openTime: '08:00',
                closeTime: '17:00',
                adminList: []
            },
            {
                id: 6,
                mitra: 'PT GHI Sejahtera',
                name: 'Semarang 1',
                city: 'Semarang',
                address: 'Jl. Pemuda No. 888, Semarang',
                phone: '024-3334444',
                rooms: 12,
                admins: 2,
                revenue: 100000000,
                status: 'active',
                openTime: '08:00',
                closeTime: '17:00',
                adminList: [
                    { id: 6, name: 'Admin Lisa', email: 'lisa@ghi.com' },
                    { id: 7, name: 'Admin Tono', email: 'tono@ghi.com' }
                ]
            },
            {
                id: 7,
                mitra: 'CV JKL Utama',
                name: 'Yogyakarta 1',
                city: 'Yogyakarta',
                address: 'Jl. Malioboro No. 111, Yogyakarta',
                phone: '0274-555666',
                rooms: 8,
                admins: 1,
                revenue: 0,
                status: 'inactive',
                openTime: '08:00',
                closeTime: '17:00',
                adminList: []
            },
            {
                id: 8,
                mitra: 'PT DEF Makmur',
                name: 'Jakarta 2',
                city: 'Jakarta',
                address: 'Jl. Gatot Subroto No. 999, Jakarta Pusat',
                phone: '021-4445555',
                rooms: 10,
                admins: 1,
                revenue: 0,
                status: 'inactive',
                openTime: '08:00',
                closeTime: '17:00',
                adminList: []
            }
        ],

        // Initialize
        init() {
            this.calculateStats();
        },

        // Computed
        get filteredBranches() {
            let filtered = this.allBranches;

            if (this.filters.mitra) {
                filtered = filtered.filter(b => b.mitra === this.filters.mitra);
            }

            if (this.filters.city) {
                filtered = filtered.filter(b => b.city === this.filters.city);
            }

            if (this.filters.status) {
                filtered = filtered.filter(b => b.status === this.filters.status);
            }

            if (this.filters.search) {
                const search = this.filters.search.toLowerCase();
                filtered = filtered.filter(b => 
                    b.name.toLowerCase().includes(search) ||
                    b.address.toLowerCase().includes(search)
                );
            }

            return filtered;
        },

        get groupedBranches() {
            const groups = {};
            
            this.filteredBranches.forEach(branch => {
                if (!groups[branch.mitra]) {
                    groups[branch.mitra] = {
                        mitra: branch.mitra,
                        branches: [],
                        totalRooms: 0,
                        totalRevenue: 0
                    };
                }
                groups[branch.mitra].branches.push(branch);
                groups[branch.mitra].totalRooms += branch.rooms;
                groups[branch.mitra].totalRevenue += branch.revenue;
            });

            return Object.values(groups);
        },

        get uniqueMitra() {
            return [...new Set(this.allBranches.map(b => b.mitra))];
        },

        get uniqueCities() {
            return [...new Set(this.allBranches.map(b => b.city))];
        },

        get canSubmitBranch() {
            return this.branchForm.mitra && 
                   this.branchForm.name && 
                   this.branchForm.city && 
                   this.branchForm.address;
        },

        // Methods
        calculateStats() {
            this.stats.totalBranches = this.allBranches.length;
            this.stats.activeBranches = this.allBranches.filter(b => b.status === 'active').length;
            this.stats.totalRooms = this.allBranches.reduce((sum, b) => sum + b.rooms, 0);
            this.stats.totalRevenue = this.allBranches.reduce((sum, b) => sum + b.revenue, 0);
        },

        formatCurrency(value) {
            return 'Rp ' + parseInt(value).toLocaleString('id-ID');
        },

        resetFilters() {
            this.filters = {
                mitra: '',
                city: '',
                status: '',
                search: ''
            };
        },

        toggleMitraGroup(mitra) {
            const index = this.expandedMitra.indexOf(mitra);
            if (index > -1) {
                this.expandedMitra.splice(index, 1);
            } else {
                this.expandedMitra.push(mitra);
            }
        },

        viewBranchDetail(branch) {
            this.selectedBranch = branch;
            this.showDetailModal = true;
        },

        editBranch(branch) {
            this.editMode = true;
            this.selectedBranch = branch;
            this.branchForm = {
                mitra: branch.mitra,
                name: branch.name,
                city: branch.city,
                address: branch.address,
                rooms: branch.rooms,
                openTime: branch.openTime,
                closeTime: branch.closeTime,
                status: branch.status,
                phone: branch.phone || ''
            };
            this.showDetailModal = false;
            this.showAddBranchModal = true;
        },

        closeAddBranchModal() {
            this.showAddBranchModal = false;
            this.editMode = false;
            this.selectedBranch = null;
            this.branchForm = {
                mitra: '',
                name: '',
                city: '',
                address: '',
                rooms: 0,
                openTime: '08:00',
                closeTime: '17:00',
                status: 'active',
                phone: ''
            };
        },

        submitBranchForm() {
            if (this.editMode) {
                alert('Updating branch: ' + this.branchForm.name);
            } else {
                alert('Adding new branch: ' + this.branchForm.name);
            }
            this.closeAddBranchModal();
        },

        manageAdmins(branch) {
            alert('Redirecting to admin management for: ' + branch.name);
            // window.location.href = '/superadmin/branch/' + branch.id + '/admins';
        },

        exportData() {
            alert('Exporting branch data to Excel...');
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