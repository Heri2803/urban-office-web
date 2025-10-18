{{-- resources/views/superadmin/users/all-admins.blade.php --}}

@extends('layouts.superadmin')

@section('title', 'All Superadmin')

@section('content')
<div x-data="adminManagement()" x-init="init()" class="space-y-4 md:space-y-6 pb-20 md:pb-6 max-w-full overflow-hidden">
    
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="min-w-0">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 truncate">👥 All Admins</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Manage admin accounts across all branches</p>
        </div>
        <button 
            @click="openAddModal()"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium flex items-center justify-center gap-2 whitespace-nowrap"
        >
            <span>➕</span>
            <span>Add Admin</span>
        </button>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4">
            <p class="text-xs text-gray-500 mb-1">Total Admins</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-800" x-text="stats.total"></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4">
            <p class="text-xs text-gray-500 mb-1">Active</p>
            <p class="text-xl sm:text-2xl font-bold text-green-600" x-text="stats.active"></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4">
            <p class="text-xs text-gray-500 mb-1">Inactive</p>
            <p class="text-xl sm:text-2xl font-bold text-red-600" x-text="stats.inactive"></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4">
            <p class="text-xs text-gray-500 mb-1">Pending</p>
            <p class="text-xl sm:text-2xl font-bold text-yellow-600" x-text="stats.pending"></p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Mitra</label>
                <select x-model="filters.mitra" @change="onMitraChange()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="">All Mitra</option>
                    <template x-for="mitra in mitras" :key="mitra.id">
                        <option :value="mitra.id" x-text="mitra.name"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Branch</label>
                <select x-model="filters.branch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="">All Branches</option>
                    <template x-for="branch in getFilteredBranches()" :key="branch.id">
                        <option :value="branch.id" x-text="branch.name"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select x-model="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                <input type="text" x-model="filters.search" placeholder="Name or email..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
            </div>
        </div>
        <button @click="resetFilters()" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Reset Filters</button>
    </div>

    {{-- Admin Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        <template x-for="admin in getFilteredAdmins()" :key="admin.id">
            <div class="bg-white rounded-lg border border-gray-200 hover:shadow-lg transition p-4">
                {{-- Header --}}
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-sm flex-shrink-0">
                            <span x-text="admin.initial"></span>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-gray-800 text-sm truncate" x-text="admin.name"></h3>
                            <p class="text-xs text-gray-500" x-text="admin.role"></p>
                        </div>
                    </div>
                    <button @click="toggleActions(admin.id)" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                        </svg>
                    </button>
                </div>

                {{-- Info --}}
                <div class="space-y-2 mb-3">
                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-gray-500">📧</span>
                        <span class="text-gray-700 truncate" x-text="admin.email"></span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-gray-500">📱</span>
                        <span class="text-gray-700" x-text="admin.phone"></span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-gray-500">🏢</span>
                        <span class="text-gray-700 truncate" x-text="admin.branchName"></span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-gray-500">🤝</span>
                        <span class="text-gray-700 truncate" x-text="admin.mitraName"></span>
                    </div>
                </div>

                {{-- Status & Actions --}}
                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <span 
                        class="px-2 py-1 rounded-full text-xs font-semibold"
                        :class="{
                            'bg-green-100 text-green-700': admin.status === 'active',
                            'bg-red-100 text-red-700': admin.status === 'inactive',
                            'bg-yellow-100 text-yellow-700': admin.status === 'pending'
                        }"
                        x-text="admin.status.charAt(0).toUpperCase() + admin.status.slice(1)"
                    ></span>
                    <div class="flex gap-1">
                        <button @click="viewAdmin(admin)" class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium hover:bg-blue-200 transition">
                            View
                        </button>
                        <button @click="editAdmin(admin)" class="px-3 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium hover:bg-gray-200 transition">
                            Edit
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Empty State --}}
    <div x-show="getFilteredAdmins().length === 0" class="text-center py-12 bg-white rounded-lg border-2 border-dashed border-gray-300">
        <div class="text-gray-400">
            <div class="text-5xl mb-3">👥</div>
            <p class="text-base font-medium text-gray-600 mb-2">No admins found</p>
            <p class="text-sm text-gray-500 mb-4">Try adjusting your filters</p>
        </div>
    </div>

    {{-- Pagination --}}
    <div x-show="getFilteredAdmins().length > 0" class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-white rounded-lg border border-gray-200 p-3">
        <div class="text-sm text-gray-600">
            Showing <span x-text="getFilteredAdmins().length"></span> admins
        </div>
        <div class="text-xs text-gray-500">
            Per page: <span x-text="perPage"></span>
        </div>
    </div>

    {{-- Add Admin Modal --}}
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-3 sm:px-4">
            <div @click="showAddModal = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-4 sm:p-6 my-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Add New Admin</h3>
                    <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" x-model="adminForm.name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" x-model="adminForm.email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                        <input type="text" x-model="adminForm.phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mitra *</label>
                        <select x-model="adminForm.mitra" @change="onFormMitraChange()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">Select Mitra</option>
                            <template x-for="mitra in mitras" :key="mitra.id">
                                <option :value="mitra.id" x-text="mitra.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Branch *</label>
                        <select x-model="adminForm.branch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">Select Branch</option>
                            <template x-for="branch in getFormBranches()" :key="branch.id">
                                <option :value="branch.id" x-text="branch.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select x-model="adminForm.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 pt-4">
                        <button 
                            @click="saveAdmin()"
                            :disabled="!adminForm.name || !adminForm.email || !adminForm.branch"
                            :class="(!adminForm.name || !adminForm.email || !adminForm.branch) ? 'bg-gray-300 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                            class="flex-1 px-4 py-2 text-white rounded-lg font-semibold transition text-sm"
                        >
                            Create Admin
                        </button>
                        <button @click="showAddModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- View Admin Modal --}}
    <div x-show="showViewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-3 sm:px-4">
            <div @click="showViewModal = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-4 sm:p-6 my-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Admin Details</h3>
                    <button @click="showViewModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <template x-if="selectedAdmin">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 pb-4 border-b">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-xl">
                                <span x-text="selectedAdmin.initial"></span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800" x-text="selectedAdmin.name"></h4>
                                <p class="text-sm text-gray-500" x-text="selectedAdmin.role"></p>
                                <span 
                                    class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                                    :class="{
                                        'bg-green-100 text-green-700': selectedAdmin.status === 'active',
                                        'bg-red-100 text-red-700': selectedAdmin.status === 'inactive',
                                        'bg-yellow-100 text-yellow-700': selectedAdmin.status === 'pending'
                                    }"
                                    x-text="selectedAdmin.status.charAt(0).toUpperCase() + selectedAdmin.status.slice(1)"
                                ></span>
                            </div>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Email</p>
                                <p class="text-gray-800 font-medium" x-text="selectedAdmin.email"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Phone</p>
                                <p class="text-gray-800 font-medium" x-text="selectedAdmin.phone"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Branch</p>
                                <p class="text-gray-800 font-medium" x-text="selectedAdmin.branchName"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Mitra</p>
                                <p class="text-gray-800 font-medium" x-text="selectedAdmin.mitraName"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Last Login</p>
                                <p class="text-gray-800 font-medium" x-text="selectedAdmin.lastLogin"></p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2 pt-4 border-t">
                            <button @click="editAdmin(selectedAdmin); showViewModal = false" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition text-sm">
                                Edit Admin
                            </button>
                            <button @click="resetPassword(selectedAdmin)" class="flex-1 px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 font-semibold transition text-sm">
                                Reset Password
                            </button>
                        </div>
                        <button 
                            @click="deleteAdmin(selectedAdmin)"
                            :class="selectedAdmin.status === 'active' ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200'"
                            class="w-full px-4 py-2 rounded-lg font-semibold transition text-sm"
                        >
                            <span x-text="selectedAdmin.status === 'active' ? 'Deactivate Admin' : 'Activate Admin'"></span>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Edit Admin Modal --}}
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-3 sm:px-4">
            <div @click="showEditModal = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-4 sm:p-6 my-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Edit Admin</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" x-model="adminForm.name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" x-model="adminForm.email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" x-model="adminForm.phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transfer to Branch</label>
                        <select x-model="adminForm.branch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <template x-for="branch in branches" :key="branch.id">
                                <option :value="branch.id" x-text="branch.name + ' (' + branch.mitraName + ')'"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select x-model="adminForm.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 pt-4">
                        <button @click="updateAdmin()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition text-sm">
                            Save Changes
                        </button>
                        <button @click="showEditModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div 
        x-show="showToast" 
        x-transition
        class="fixed top-4 right-4 z-50 max-w-xs sm:max-w-sm bg-green-500 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-lg shadow-xl"
    >
        <p class="font-semibold text-sm" x-text="toastMessage"></p>
    </div>

</div>
<script>
    function adminManagement() {
        return {
            // --- DATA STATE ---
            showAddModal: false,
            showEditModal: false,
            showViewModal: false,
            showToast: false,
            toastMessage: '',
            
            selectedAdmin: null,
            perPage: 50,
            
            stats: {
                total: 0,
                active: 0,
                inactive: 0,
                pending: 0
            },
            
            filters: {
                mitra: '',
                branch: '',
                status: '',
                search: ''
            },
            
            adminForm: {
                id: null, // Tambahkan ID untuk edit
                name: '',
                email: '',
                phone: '',
                mitra: '',
                branch: '',
                status: 'active',
                isEditing: false // Flag untuk membedakan mode edit
            },
            
            mitras: [],
            branches: [],
            admins: [],

            // --- INIT & UTILITY ---
            init() {
                this.generateDummyData();
                this.calculateStats();
            },
            
            generateDummyData() {
                // Dummy Mitras
                this.mitras = [
                    { id: 1, name: 'PT Workspace Indonesia' },
                    { id: 2, name: 'CV Ruang Kerja' },
                    { id: 3, name: 'PT Office Hub' }
                ];
                
                // Dummy Branches
                this.branches = [
                    { id: 1, mitraId: 1, name: 'Surabaya - Gubeng', mitraName: 'PT Workspace Indonesia' },
                    { id: 2, mitraId: 1, name: 'Surabaya - HR Muhammad', mitraName: 'PT Workspace Indonesia' },
                    { id: 3, mitraId: 2, name: 'Jakarta - Senayan', mitraName: 'CV Ruang Kerja' },
                    { id: 4, mitraId: 2, name: 'Jakarta - Sudirman', mitraName: 'CV Ruang Kerja' },
                    { id: 5, mitraId: 3, name: 'Bandung - Dago', mitraName: 'PT Office Hub' }
                ];
                
                // Dummy Admins
                const names = ['Budi Santoso', 'Siti Rahayu', 'Ahmad Wijaya', 'Dewi Lestari', 'Eko Prasetyo', 'Fitri Handayani'];
                this.admins = [];
                names.forEach((name, index) => {
                    const branch = this.branches[index % this.branches.length];
                    const statuses = ['active', 'active', 'active', 'inactive', 'pending'];
                    this.admins.push({
                        id: index + 1,
                        name: name,
                        initial: name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase(),
                        email: name.toLowerCase().replace(' ', '.') + '@urbanoffice.com',
                        phone: `+628${Math.floor(Math.random() * 900000000 + 100000000)}`,
                        branchId: branch.id,
                        branchName: branch.name,
                        mitraId: branch.mitraId,
                        mitraName: branch.mitraName,
                        role: 'Branch Admin',
                        status: statuses[index % statuses.length],
                        lastLogin: ['2 hours ago', '1 day ago', '3 days ago'][Math.floor(Math.random() * 3)]
                    });
                });
            },
            
            calculateStats() {
                this.stats.total = this.admins.length;
                this.stats.active = this.admins.filter(a => a.status === 'active').length;
                this.stats.inactive = this.admins.filter(a => a.status === 'inactive').length;
                this.stats.pending = this.admins.filter(a => a.status === 'pending').length;
            },
            
            showNotification(message) {
                this.toastMessage = message;
                this.showToast = true;
                setTimeout(() => {
                    this.showToast = false;
                }, 3000);
            },

            // --- FILTER LOGIC ---
            getFilteredAdmins() {
                let filtered = this.admins;

                // 1. Filter by Mitra
                if (this.filters.mitra) {
                    filtered = filtered.filter(admin => admin.mitraId == this.filters.mitra);
                }
                
                // 2. Filter by Branch
                if (this.filters.branch) {
                    filtered = filtered.filter(admin => admin.branchId == this.filters.branch);
                }

                // 3. Filter by Status
                if (this.filters.status) {
                    filtered = filtered.filter(admin => admin.status === this.filters.status);
                }

                // 4. Filter by Search
                if (this.filters.search) {
                    const search = this.filters.search.toLowerCase();
                    filtered = filtered.filter(admin => 
                        admin.name.toLowerCase().includes(search) || 
                        admin.email.toLowerCase().includes(search)
                    );
                }

                return filtered;
            },

            getFilteredBranches() {
                if (!this.filters.mitra) return this.branches;
                return this.branches.filter(b => b.mitraId == this.filters.mitra);
            },

            onMitraChange() {
                // Reset branch filter saat mitra diubah
                this.filters.branch = '';
            },

            resetFilters() {
                this.filters.mitra = '';
                this.filters.branch = '';
                this.filters.status = '';
                this.filters.search = '';
            },

            // --- FORM/MODAL LOGIC ---
            
            getFormBranches() {
                if (!this.adminForm.mitra) return [];
                return this.branches.filter(b => b.mitraId == this.adminForm.mitra);
            },

            onFormMitraChange() {
                // Reset branch saat mitra di form diubah
                this.adminForm.branch = '';
            },

            resetForm() {
                this.adminForm = {
                    id: null,
                    name: '',
                    email: '',
                    phone: '',
                    mitra: '',
                    branch: '',
                    status: 'active',
                    isEditing: false
                };
            },
            
            // Open Modals
            openAddModal() {
                this.resetForm();
                this.showAddModal = true;
            },

            viewAdmin(admin) {
                this.selectedAdmin = admin;
                this.showViewModal = true;
            },

            editAdmin(admin) {
                this.resetForm();
                // Map data admin ke form
                this.adminForm = {
                    id: admin.id,
                    name: admin.name,
                    email: admin.email,
                    phone: admin.phone,
                    mitra: admin.mitraId,
                    branch: admin.branchId,
                    status: admin.status,
                    isEditing: true
                };
                this.showEditModal = true;
            },

            // --- CRUD ACTIONS (SIMULASI) ---
            saveAdmin() {
                // Dapatkan detail Branch dan Mitra untuk data admin baru
                const branch = this.branches.find(b => b.id == this.adminForm.branch);
                const mitra = this.mitras.find(m => m.id == this.adminForm.mitra);

                if (!branch || !mitra) {
                    this.showNotification('Error: Mitra atau Cabang tidak valid!');
                    return;
                }

                const newAdmin = {
                    id: this.admins.length + 1,
                    name: this.adminForm.name,
                    initial: this.adminForm.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase(),
                    email: this.adminForm.email,
                    phone: this.adminForm.phone,
                    branchId: branch.id,
                    branchName: branch.name,
                    mitraId: mitra.id,
                    mitraName: mitra.name,
                    role: 'Branch Admin', // Role default
                    status: this.adminForm.status,
                    lastLogin: 'N/A'
                };

                // Tambahkan admin baru
                this.admins.push(newAdmin);
                this.calculateStats();
                this.showAddModal = false;
                this.showNotification(`Admin ${newAdmin.name} berhasil ditambahkan!`);
            },

            updateAdmin() {
                // Cari index admin yang diedit
                const index = this.admins.findIndex(a => a.id === this.adminForm.id);
                if (index === -1) {
                    this.showNotification('Error: Admin tidak ditemukan!');
                    return;
                }

                // Dapatkan detail Branch dan Mitra yang baru
                const branch = this.branches.find(b => b.id == this.adminForm.branch);
                const mitra = this.mitras.find(m => m.id == this.adminForm.mitra);

                if (!branch || !mitra) {
                     this.showNotification('Error: Mitra atau Cabang tidak valid!');
                    return;
                }

                // Update data admin
                this.admins[index] = {
                    ...this.admins[index], // Pertahankan properti lama (initial, role, dll.)
                    name: this.adminForm.name,
                    email: this.adminForm.email,
                    phone: this.adminForm.phone,
                    branchId: branch.id,
                    branchName: branch.name,
                    mitraId: mitra.id,
                    mitraName: mitra.name,
                    status: this.adminForm.status,
                };

                this.calculateStats();
                this.showEditModal = false;
                this.showNotification(`Data Admin ${this.admins[index].name} berhasil diperbarui!`);
            },
            
            resetPassword(admin) {
                // Simulasi aksi Reset Password
                const confirmReset = confirm(`Anda yakin ingin me-reset password untuk ${admin.name}?`);
                if (confirmReset) {
                    this.showNotification(`Password untuk ${admin.name} berhasil di-reset (simulasi).`);
                }
            },
            
            deleteAdmin(admin) {
                // Aksi Deactivate/Activate
                const newStatus = admin.status === 'active' ? 'inactive' : 'active';
                const action = admin.status === 'active' ? 'Deaktifkan' : 'Aktifkan';
                
                const confirmAction = confirm(`Anda yakin ingin ${action} admin ${admin.name}?`);

                if (confirmAction) {
                    const index = this.admins.findIndex(a => a.id === admin.id);
                    if (index !== -1) {
                        this.admins[index].status = newStatus;
                        this.selectedAdmin.status = newStatus; // Update status di modal view juga
                        this.calculateStats();
                        this.showNotification(`Admin ${admin.name} berhasil di${newStatus}kan.`);
                    }
                }
            },
        }
    }
</script>
@endsection