@extends('layouts.superadmin')

@section('title', 'Revenue Share Management')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Revenue Share Management</h1>
                <p class="text-sm text-gray-600 mt-1">Kelola pembagian hasil dan settlement pembayaran ke mitra</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <button onclick="exportReport()" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Report
                </button>
                <button onclick="openSettlementModal()" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Process Settlement
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Revenue</p>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800">Rp 245.8M</h3>
                    <p class="text-xs text-green-600 mt-1">+12.5% vs last month</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Share to Mitra</p>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800">Rp 171.2M</h3>
                    <p class="text-xs text-gray-500 mt-1">69.7% of total revenue</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pending Settlement</p>
                    <h3 class="text-xl md:text-2xl font-bold text-orange-600">Rp 45.5M</h3>
                    <p class="text-xs text-gray-500 mt-1">5 mitra pending</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Settled This Month</p>
                    <h3 class="text-xl md:text-2xl font-bold text-green-600">Rp 125.7M</h3>
                    <p class="text-xs text-gray-500 mt-1">12 settlements completed</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option>Oktober 2025</option>
                    <option>September 2025</option>
                    <option>Agustus 2025</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option>All Status</option>
                    <option>Pending</option>
                    <option>Paid</option>
                    <option>Overdue</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mitra</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option>All Mitra</option>
                    <option>PT Workspace Indonesia</option>
                    <option>CV Ruang Kerja</option>
                    <option>PT Office Hub</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option>All Locations</option>
                    <option>Surabaya</option>
                    <option>Jakarta</option>
                    <option>Bandung</option>
                </select>
            </div>
            <div class="flex items-end">
                <button class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Revenue Share Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Mitra</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider hidden md:table-cell">Periode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Total Revenue</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider hidden lg:table-cell">Share %</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Amount to Pay</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <!-- Row 1 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold text-sm">PW</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">PT Workspace Indonesia</p>
                                    <p class="text-xs text-gray-500">3 Cabang</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900 hidden md:table-cell">Oktober 2025</td>
                        <td class="px-4 py-4">
                            <p class="text-sm font-semibold text-gray-900">Rp 85.500.000</p>
                            <p class="text-xs text-gray-500">245 transaksi</p>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900 hidden lg:table-cell">70%</td>
                        <td class="px-4 py-4">
                            <p class="text-sm font-bold text-blue-600">Rp 59.850.000</p>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                Pending
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <button onclick="viewDetail(1)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Detail
                                </button>
                                <button onclick="processPayment(1)" class="text-green-600 hover:text-green-800 text-sm font-medium">
                                    Pay
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 2 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <span class="text-green-600 font-semibold text-sm">RK</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">CV Ruang Kerja</p>
                                    <p class="text-xs text-gray-500">2 Cabang</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900 hidden md:table-cell">Oktober 2025</td>
                        <td class="px-4 py-4">
                            <p class="text-sm font-semibold text-gray-900">Rp 62.300.000</p>
                            <p class="text-xs text-gray-500">178 transaksi</p>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900 hidden lg:table-cell">65%</td>
                        <td class="px-4 py-4">
                            <p class="text-sm font-bold text-blue-600">Rp 40.495.000</p>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Paid
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <button onclick="viewDetail(2)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Detail
                                </button>
                                <button onclick="viewProof(2)" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                                    Bukti
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 3 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <span class="text-purple-600 font-semibold text-sm">OH</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">PT Office Hub</p>
                                    <p class="text-xs text-gray-500">1 Cabang</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900 hidden md:table-cell">Oktober 2025</td>
                        <td class="px-4 py-4">
                            <p class="text-sm font-semibold text-gray-900">Rp 45.200.000</p>
                            <p class="text-xs text-gray-500">132 transaksi</p>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900 hidden lg:table-cell">68%</td>
                        <td class="px-4 py-4">
                            <p class="text-sm font-bold text-blue-600">Rp 30.736.000</p>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Overdue
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <button onclick="viewDetail(3)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Detail
                                </button>
                                <button onclick="processPayment(3)" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Pay Now
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 4 -->
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <span class="text-yellow-600 font-semibold text-sm">SC</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">PT Smart Co-working</p>
                                    <p class="text-xs text-gray-500">4 Cabang</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900 hidden md:table-cell">Oktober 2025</td>
                        <td class="px-4 py-4">
                            <p class="text-sm font-semibold text-gray-900">Rp 52.800.000</p>
                            <p class="text-xs text-gray-500">189 transaksi</p>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-900 hidden lg:table-cell">72%</td>
                        <td class="px-4 py-4">
                            <p class="text-sm font-bold text-blue-600">Rp 38.016.000</p>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                Pending
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <button onclick="viewDetail(4)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Detail
                                </button>
                                <button onclick="processPayment(4)" class="text-green-600 hover:text-green-800 text-sm font-medium">
                                    Pay
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">1</span> to <span class="font-medium">4</span> of{' '}
                    <span class="font-medium">12</span> results
                </div>
                <div class="flex items-center gap-2">
                    <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50" disabled>
                        Previous
                    </button>
                    <button class="px-3 py-1 text-sm bg-blue-600 text-white rounded-lg">1</button>
                    <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">2</button>
                    <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">3</button>
                    <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Bar (Hidden by default, shown when checkboxes selected) -->
    <div id="bulkActionsBar" class="hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white rounded-lg shadow-2xl px-6 py-4 z-50">
        <div class="flex items-center gap-6">
            <span class="text-sm font-medium"><span id="selectedCount">0</span> items selected</span>
            <div class="flex items-center gap-3">
                <button onclick="bulkProcessPayment()" class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-sm font-medium transition">
                    Process Selected
                </button>
                <button onclick="bulkExport()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-medium transition">
                    Export Selected
                </button>
                <button onclick="clearSelection()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-sm font-medium transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Process Payment Modal -->
<div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Process Settlement</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Settlement Details</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Mitra:</span>
                            <span class="font-medium">PT Workspace Indonesia</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Periode:</span>
                            <span class="font-medium">Oktober 2025</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Revenue:</span>
                            <span class="font-medium">Rp 85.500.000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Share (70%):</span>
                            <span class="font-bold text-blue-600">Rp 59.850.000</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pembayaran</label>
                    <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option>Transfer Bank</option>
                        <option>Virtual Account</option>
                        <option>Cek/Giro</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Transfer</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-500 transition cursor-pointer">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-500">PNG, JPG, PDF up to 5MB</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Optional)</label>
                    <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button onclick="closePaymentModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button onclick="submitPayment()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    Process Payment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Revenue Share Details</h3>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mitra Info -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-6 text-black mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-2xl font-bold mb-1">PT Workspace Indonesia</h4>
                        <p class="text-blue-100">Periode: Oktober 2025</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-blue-100 mb-1">Amount to Pay</p>
                        <p class="text-3xl font-bold">Rp 59.8M</p>
                    </div>
                </div>
            </div>

            <!-- Revenue Breakdown by Branch -->
            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Revenue by Branch</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">Surabaya - Gubeng</span>
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Active</span>
                        </div>
                        <p class="text-xl font-bold text-gray-900 mb-1">Rp 35.2M</p>
                        <p class="text-xs text-gray-500">142 transactions</p>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 45%"></div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">Surabaya - HR Muhammad</span>
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Active</span>
                        </div>
                        <p class="text-xl font-bold text-gray-900 mb-1">Rp 28.6M</p>
                        <p class="text-xs text-gray-500">98 transactions</p>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 35%"></div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">Sidoarjo - Delta</span>
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Active</span>
                        </div>
                        <p class="text-xl font-bold text-gray-900 mb-1">Rp 21.7M</p>
                        <p class="text-xs text-gray-500">76 transactions</p>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 20%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue by Service Type -->
            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Revenue by Service Type</h4>
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Service</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Transactions</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Revenue</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Share (70%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-3 text-gray-900">Meeting Room</td>
                                <td class="px-4 py-3 text-gray-600">98</td>
                                <td class="px-4 py-3 font-medium text-gray-900">Rp 28.500.000</td>
                                <td class="px-4 py-3 font-semibold text-blue-600">Rp 19.950.000</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-gray-900">Private Office</td>
                                <td class="px-4 py-3 text-gray-600">45</td>
                                <td class="px-4 py-3 font-medium text-gray-900">Rp 22.800.000</td>
                                <td class="px-4 py-3 font-semibold text-blue-600">Rp 15.960.000</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-gray-900">Coworking Space</td>
                                <td class="px-4 py-3 text-gray-600">67</td>
                                <td class="px-4 py-3 font-medium text-gray-900">Rp 18.200.000</td>
                                <td class="px-4 py-3 font-semibold text-blue-600">Rp 12.740.000</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-gray-900">Sharing Room</td>
                                <td class="px-4 py-3 text-gray-600">23</td>
                                <td class="px-4 py-3 font-medium text-gray-900">Rp 9.500.000</td>
                                <td class="px-4 py-3 font-semibold text-blue-600">Rp 6.650.000</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-gray-900">Virtual Office</td>
                                <td class="px-4 py-3 text-gray-600">8</td>
                                <td class="px-4 py-3 font-medium text-gray-900">Rp 4.200.000</td>
                                <td class="px-4 py-3 font-semibold text-blue-600">Rp 2.940.000</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-gray-900">Event Space</td>
                                <td class="px-4 py-3 text-gray-600">4</td>
                                <td class="px-4 py-3 font-medium text-gray-900">Rp 2.300.000</td>
                                <td class="px-4 py-3 font-semibold text-blue-600">Rp 1.610.000</td>
                            </tr>
                            <tr class="bg-gray-50 font-semibold">
                                <td class="px-4 py-3 text-gray-900">TOTAL</td>
                                <td class="px-4 py-3 text-gray-900">245</td>
                                <td class="px-4 py-3 text-gray-900">Rp 85.500.000</td>
                                <td class="px-4 py-3 text-blue-600">Rp 59.850.000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Calculation Details -->
            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Calculation Details</h4>
                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total Revenue (All Branches)</span>
                        <span class="font-medium text-gray-900">Rp 85.500.000</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Platform Fee (30%)</span>
                        <span class="font-medium text-red-600">- Rp 25.650.000</span>
                    </div>
                    <div class="border-t border-gray-300 pt-2 mt-2"></div>
                    <div class="flex justify-between text-base">
                        <span class="font-semibold text-gray-900">Mitra Share (70%)</span>
                        <span class="font-bold text-blue-600 text-lg">Rp 59.850.000</span>
                    </div>
                </div>
            </div>

            <!-- Payment History (if exists) -->
            <div>
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Payment History</h4>
                <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-200">
                    <div class="p-4 text-center text-sm text-gray-500">
                        No payment history yet for this period
                    </div>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button onclick="closeDetailModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Close
                </button>
                <button onclick="printReport()" class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Report
                </button>
                <button onclick="processPaymentFromDetail()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    Process Payment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="hidden fixed top-6 right-6 bg-white rounded-lg shadow-lg border border-gray-200 p-4 z-50 max-w-sm">
    <div class="flex items-start gap-3">
        <div id="toastIcon" class="flex-shrink-0">
            <!-- Icon will be injected via JS -->
        </div>
        <div class="flex-1">
            <h4 id="toastTitle" class="text-sm font-semibold text-gray-900 mb-1"></h4>
            <p id="toastMessage" class="text-sm text-gray-600"></p>
        </div>
        <button onclick="closeToast()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<script>
// Modal Functions
function openSettlementModal() {
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

function processPayment(id) {
    openSettlementModal();
}

function viewDetail(id) {
    document.getElementById('detailModal').classList.remove('hidden');
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

function processPaymentFromDetail() {
    closeDetailModal();
    openSettlementModal();
}

function viewProof(id) {
    // Open proof document in new tab
    showToast('Info', 'Opening payment proof document...', 'info');
}

// Bulk Actions
const checkboxes = document.querySelectorAll('input[type="checkbox"]');
const bulkActionsBar = document.getElementById('bulkActionsBar');
const selectedCount = document.getElementById('selectedCount');

checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActions);
});

function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('tbody input[type="checkbox"]:checked');
    if (checkedBoxes.length > 0) {
        bulkActionsBar.classList.remove('hidden');
        selectedCount.textContent = checkedBoxes.length;
    } else {
        bulkActionsBar.classList.add('hidden');
    }
}

function bulkProcessPayment() {
    showToast('Success', 'Processing payment for selected items...', 'success');
    clearSelection();
}

function bulkExport() {
    showToast('Success', 'Exporting selected items...', 'success');
}

function clearSelection() {
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    bulkActionsBar.classList.add('hidden');
}

// Export Functions
function exportReport() {
    showToast('Success', 'Generating revenue share report...', 'success');
}

function printReport() {
    window.print();
}

// Submit Payment
function submitPayment() {
    showToast('Success', 'Payment processed successfully! Notification sent to mitra.', 'success');
    closePaymentModal();
    // Reload page or update table
    setTimeout(() => {
        location.reload();
    }, 2000);
}

// Toast Notification
function showToast(title, message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');
    
    toastTitle.textContent = title;
    toastMessage.textContent = message;
    
    // Set icon based on type
    const icons = {
        success: '<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        error: '<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        info: '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
    };
    
    toastIcon.innerHTML = icons[type] || icons.success;
    
    toast.classList.remove('hidden');
    
    setTimeout(() => {
        closeToast();
    }, 5000);
}

function closeToast() {
    document.getElementById('toast').classList.add('hidden');
}
</script>

<style>
/* Additional responsive styles */
@media print {
    .no-print {
        display: none !important;
    }
}

/* Smooth transitions */
.transition {
    transition: all 0.2s ease-in-out;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection