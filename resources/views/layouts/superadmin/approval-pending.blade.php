{{-- resources/views/superadmin/approval/approval-pending.blade.php --}}

@extends('layouts.superadmin')

@section('title', 'Pending Approvals')

@section('content')
{{-- START DUMMY DATA SETUP UNTUK FRONTEND SIMULATION --}}
@php
    // --- Data DUMMY untuk $branches ---
    if (!isset($branches)) {
        $branches = [
            (object)['id' => 1, 'name' => 'Surabaya Center'],
            (object)['id' => 2, 'name' => 'Jakarta Selatan'],
            (object)['id' => 3, 'name' => 'Bandung Utara'],
        ];
    }
    
    // --- Kelas Dummy Request untuk mengatasi Error Undefined Method ---
    // Di Laravel, kita tidak bisa langsung mengakses method pada objek dummy
    // Namun, kita bisa membuat array of objects yang memiliki properti yang dibutuhkan.
    
    // Data dummy yang lebih detail
    $dummyRequestsData = [
        [
            'id' => 101, 'request_id' => 'PR-2025001', 'type' => 'price_change', 'type_name' => 'Price Change',
            'urgent' => true, 'created_at' => now()->subHours(2), 'admin' => (object)['name' => 'Admin SBY'],
            'branch' => (object)['name' => 'Surabaya Center'], 'current_price' => 5000000, 'new_price' => 4500000,
            'service_name' => 'Private Office A', 'reason' => 'Permintaan penyesuaian harga untuk mempertahankan klien besar...',
            'documents' => ['doc1.pdf'],
        ],
        [
            'id' => 102, 'request_id' => 'DR-2025002', 'type' => 'discount', 'type_name' => 'Discount Request',
            'urgent' => false, 'created_at' => now()->subHours(5), 'admin' => (object)['name' => 'Admin JKT'],
            'branch' => (object)['name' => 'Jakarta Selatan'], 'discount_percentage' => 15, 'max_discount' => 20,
            'customer_name' => 'PT Jaya Abadi', 'service_name' => 'Meeting Room',
            'reason' => 'Pengajuan diskon 15% untuk booking Meeting Room selama 3 hari berturut-turut...',
        ],
        [
            'id' => 103, 'request_id' => 'RF-2025003', 'type' => 'refund', 'type_name' => 'Refund Request',
            'urgent' => true, 'created_at' => now()->subHours(10), 'admin' => (object)['name' => 'Admin BDG'],
            'branch' => (object)['name' => 'Bandung Utara'], 'booking_id' => 'BKG-998877',
            'customer_name' => 'Dian Kusuma', 'refund_amount' => 750000,
            'reason' => 'Pembatalan booking Event Space karena acara ditunda mendadak...',
        ],
        [
            'id' => 104, 'request_id' => 'SF-2025004', 'type' => 'special_facility', 'type_name' => 'Special Facility',
            'urgent' => false, 'created_at' => now()->subDays(1), 'admin' => (object)['name' => 'Admin JKT'],
            'branch' => (object)['name' => 'Jakarta Selatan'], 'service_name' => 'Coworking Space',
            'customer_name' => 'Risa Putri', 'facility_description' => 'Permintaan monitor 34 inch Ultrawide',
            'additional_cost' => 0, 'documents' => [],
            'reason' => 'Kebutuhan khusus untuk pekerjaan desain grafis yang intensif.',
        ],
        [
            'id' => 105, 'request_id' => 'RS-2025005', 'type' => 'reschedule', 'type_name' => 'Reschedule',
            'urgent' => false, 'created_at' => now()->subDays(2), 'admin' => (object)['name' => 'Admin SBY'],
            'branch' => (object)['name' => 'Surabaya Center'], 'booking_id' => 'BKG-554433',
            'customer_name' => 'Agung Pratama', 'current_date' => '2025-11-10', 'new_date' => '2025-12-01',
            'reason' => 'Perubahan jadwal meeting dari klien luar kota.',
        ],
    ];

    // Fungsi helper dummy untuk kebutuhan Blade (Biasanya ada di Model atau Helper)
    $formatCurrency = function($amount) { return 'Rp ' . number_format($amount, 0, ',', '.'); };
    $getTypeColorClass = function($type) {
        $map = [
            'price_change' => 'bg-yellow-100 text-yellow-800',
            'discount' => 'bg-purple-100 text-purple-800',
            'refund' => 'bg-orange-100 text-orange-800',
            'special_facility' => 'bg-blue-100 text-blue-800',
            'reschedule' => 'bg-indigo-100 text-indigo-800',
        ];
        return $map[$type] ?? 'bg-gray-100 text-gray-800';
    };
    $getPriceChangePercentage = function($current, $new) {
        return round(abs($current - $new) / $current * 100);
    };

    // Mapping array dummy data ke objek dengan helper functions (Simulasi Collection Pagination)
    $requestsCollection = collect($dummyRequestsData)->map(function($data) use ($formatCurrency, $getTypeColorClass, $getPriceChangePercentage) {
        $object = (object)$data;
        $object->formatCurrency = $formatCurrency;
        $object->getTypeColorClass = $getTypeColorClass;
        $object->getPriceChangePercentage = $getPriceChangePercentage;
        $object->created_at = $object->created_at; // now() object
        return $object;
    });

    // --- Data DUMMY untuk $requests (Collection yang bisa di-paginate) ---
    if (!isset($requests)) {
        // Objek dummy untuk Pagination dan total()
        $requests = new class($requestsCollection) {
            private $items;
            public function __construct($items) { $this->items = $items; }
            public function total() { return $this->items->count(); }
            public function hasPages() { return false; } // Simplified for dummy
            public function links() { return ''; } // Dummy links
            // Implementasi forelse: memungkinkan loop menggunakan items
            public function getIterator() { return $this->items->getIterator(); } 
        };
    }
@endphp
{{-- END DUMMY DATA SETUP --}}

<div class="container-fluid px-4 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Pending Approvals</h1>
        <p class="text-gray-600">Review and approve requests from all branches</p>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('superadmin.approval.pending') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                
                {{-- Request Type Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Request Type</label>
                    <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
                        <option value="price_change" {{ request('type') == 'price_change' ? 'selected' : '' }}>Price Change</option>
                        <option value="discount" {{ request('type') == 'discount' ? 'selected' : '' }}>Discount Request</option>
                        <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Refund Request</option>
                        <option value="special_facility" {{ request('type') == 'special_facility' ? 'selected' : '' }}>Special Facility</option>
                        <option value="reschedule" {{ request('type') == 'reschedule' ? 'selected' : '' }}>Reschedule</option>
                    </select>
                </div>

                {{-- BRANCH FILTER (Menggunakan $branches dari dummy data) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Branch</label>
                    <select name="branch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" {{ request('branch') == 'all' ? 'selected' : '' }}>All Branches</option>
                        
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date Range Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <input type="date" name="date" value="{{ request('date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            
            {{-- Action Buttons --}}
            <div class="mt-4 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Apply Filter
                </button>
                {{-- PERBAIKAN TYPO ROUTE: 'super-admin.approval.pending' --}}
                <a href="{{ route('superadmin.approval.pending') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Count Badge --}}
    <div class="mb-4">
        <span class="inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-800 text-sm font-medium">
            {{ $requests->total() }} Pending Request{{ $requests->total() != 1 ? 's' : '' }}
        </span>
    </div>

    {{-- Requests List --}}
    <div class="space-y-4 mb-6">
        {{-- Menggunakan $requests yang kini adalah objek dummy yang bisa di-loop --}}
        @forelse($requests as $request)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 hover:shadow-md transition-shadow">
                {{-- Header --}}
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-2">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $request->getTypeColorClass($request->type) }}">
                                {{ $request->type_name }}
                            </span>
                            @if(isset($request->urgent) && $request->urgent)
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-500 text-white flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Urgent
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500">Request ID: {{ $request->request_id }}</p>
                    </div>
                </div>

                {{-- Content --}}
                <div class="space-y-3 mb-4">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <div class="text-sm">
                            <span class="text-gray-500">From:</span>
                            <span class="font-medium ml-1">{{ $request->admin->name ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <div class="text-sm">
                            <span class="text-gray-500">Branch:</span>
                            <span class="font-medium ml-1">{{ $request->branch->name ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm text-gray-600">{{ $request->created_at->format('d M Y, H:i') }}</div>
                    </div>

                    {{-- Type-specific content --}}
                    @if($request->type === 'price_change')
                        <div class="bg-gray-50 rounded-lg p-3 mt-3">
                            <p class="text-sm font-medium mb-2">{{ $request->service_name }}</p>
                            <div class="flex items-center gap-3 text-sm">
                                <span class="text-gray-600">{{ $request->formatCurrency($request->current_price) }}</span>
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                </svg>
                                <span class="font-semibold text-green-600">{{ $request->formatCurrency($request->new_price) }}</span>
                                <span class="text-xs text-gray-500">(-{{ $request->getPriceChangePercentage($request->current_price, $request->new_price) }}%)</span>
                            </div>
                        </div>
                    @endif

                    @if($request->type === 'discount')
                        <div class="bg-gray-50 rounded-lg p-3 mt-3">
                            <p class="text-sm font-medium mb-2">{{ $request->service_name }}</p>
                            <p class="text-sm text-gray-600 mb-1">Customer: {{ $request->customer_name }}</p>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span class="text-sm">
                                    <span class="font-semibold text-purple-600">{{ $request->discount_percentage }}%</span>
                                    <span class="text-gray-500 text-xs ml-1">(Max: {{ $request->max_discount }}%)</span>
                                </span>
                            </div>
                        </div>
                    @endif

                    @if($request->type === 'refund')
                        <div class="bg-gray-50 rounded-lg p-3 mt-3">
                            <p class="text-sm text-gray-600 mb-1">Booking: {{ $request->booking_id }}</p>
                            <p class="text-sm text-gray-600 mb-1">Customer: {{ $request->customer_name }}</p>
                            <p class="text-sm font-semibold text-red-600">Amount: {{ $request->formatCurrency($request->refund_amount) }}</p>
                        </div>
                    @endif

                    @if($request->type === 'special_facility')
                        <div class="bg-gray-50 rounded-lg p-3 mt-3">
                            <p class="text-sm font-medium mb-1">{{ $request->service_name }}</p>
                            <p class="text-sm text-gray-600 mb-1">Customer: {{ $request->customer_name }}</p>
                            <p class="text-sm text-gray-600 mb-2">Facility: {{ $request->facility_description }}</p>
                            <p class="text-sm font-semibold text-green-600">Additional Cost: {{ $request->formatCurrency($request->additional_cost) }}</p>
                        </div>
                    @endif

                    @if($request->type === 'reschedule')
                        <div class="bg-gray-50 rounded-lg p-3 mt-3">
                            <p class="text-sm text-gray-600 mb-1">Booking: {{ $request->booking_id }}</p>
                            <p class="text-sm text-gray-600 mb-2">Customer: {{ $request->customer_name }}</p>
                            <div class="flex items-center gap-2 text-sm">
                                <span class="text-gray-600">{{ $request->current_date }}</span>
                                <span>→</span>
                                <span class="font-medium text-blue-600">{{ $request->new_date }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="pt-2">
                        <p class="text-sm text-gray-500 mb-1">Reason:</p>
                        <p class="text-sm text-gray-700">{{ Str::limit($request->reason, 150) }}</p>
                    </div>

                    @if(isset($request->documents) && count($request->documents) > 0)
                        <div class="flex items-center gap-2 text-sm text-blue-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>{{ count($request->documents) }} document(s) attached</span>
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-2 pt-4 border-t">
                    <button onclick="viewDetails({{ $request->id }})" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View Details
                    </button>
                    <button onclick="showActionModal({{ $request->id }}, 'approve')" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Approve
                    </button>
                    <button onclick="showActionModal({{ $request->id }}, 'reject')" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Reject
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <div class="text-gray-400 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Pending Requests</h3>
                <p class="text-gray-600">All requests have been processed</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination (Akan menampilkan link dummy) --}}
    @if($requests->hasPages())
        <div class="flex justify-center">
            {{ $requests->links() }}
        </div>
    @endif
</div>

{{-- Detail Modal --}}
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold">Request Details</h2>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="detailModalContent" class="px-6 py-4">
            {{-- Content will be loaded via AJAX --}}
        </div>
    </div>
</div>

{{-- Action Modal --}}
<div id="actionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 id="actionModalTitle" class="text-xl font-bold"></h2>
        </div>

        <form id="actionForm" method="POST">
            @csrf
            <div class="px-6 py-4">
                <p id="actionModalText" class="text-gray-600 mb-4"></p>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Notes <span id="requiredStar" class="text-red-500 hidden">*</span>
                    </label>
                    <textarea 
                        name="notes" 
                        id="actionNotes"
                        placeholder="Add notes..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        rows="4"
                    ></textarea>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex gap-3">
                <button type="button" onclick="closeActionModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100">
                    Cancel
                </button>
                <button type="submit" id="actionSubmitBtn" class="flex-1 px-4 py-2 rounded-lg text-white">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Logic ini tetap dipertahankan karena sudah benar untuk frontend interaction
let currentRequestId = null;
let currentAction = null;

function viewDetails(requestId) {
    currentRequestId = requestId;
    document.getElementById('detailModal').classList.remove('hidden');
    
    // AJAX call for details (will fail in pure frontend, but logic is correct)
    document.getElementById('detailModalContent').innerHTML = 
        '<div class="text-center py-8 text-gray-500">Loading details for Request ID: ' + requestId + '... (Simulasi: Integrasi Backend Diperlukan)</div>';
    
    // Simulating AJAX delay and content fill
    setTimeout(() => {
        document.getElementById('detailModalContent').innerHTML = `
            <div class="space-y-4">
                <p class="text-sm"><span class="font-medium text-gray-700">Request ID:</span> ${requestId}</p>
                <p class="text-sm"><span class="font-medium text-gray-700">Customer:</span> PT Contoh Abadi</p>
                <p class="text-sm"><span class="font-medium text-gray-700">Branch:</span> Surabaya Center</p>
                <p class="text-sm"><span class="font-medium text-gray-700">Full Reason:</span> Ini adalah simulasi alasan lengkap dari permintaan ini. Ketika backend sudah terpasang, data ini akan dimuat melalui AJAX.</p>
                <p class="text-sm text-green-600 font-medium">Status Simulasi: Pending</p>
            </div>
        `;
    }, 500); 
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
    currentRequestId = null;
}

function showActionModal(requestId, action) {
    currentRequestId = requestId;
    currentAction = action;
    
    const modal = document.getElementById('actionModal');
    const title = document.getElementById('actionModalTitle');
    const text = document.getElementById('actionModalText');
    const submitBtn = document.getElementById('actionSubmitBtn');
    const form = document.getElementById('actionForm');
    const requiredStar = document.getElementById('requiredStar');
    const notesField = document.getElementById('actionNotes');
    
    if (action === 'approve') {
        title.textContent = 'Approve Request';
        text.textContent = `Are you sure you want to approve Request ID ${requestId}?`;
        submitBtn.className = 'flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-white';
        submitBtn.textContent = 'Approve';
        requiredStar.classList.add('hidden');
        notesField.placeholder = 'Add optional notes...';
        notesField.required = false;
    } else {
        title.textContent = 'Reject Request';
        text.textContent = `Are you sure you want to reject Request ID ${requestId}?`;
        submitBtn.className = 'flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-white';
        submitBtn.textContent = 'Reject';
        requiredStar.classList.remove('hidden');
        notesField.placeholder = 'Provide reason for rejection...';
        notesField.required = true;
    }
    
    // Simulasi action form: akan diarahkan ke URL dummy
    form.action = `/super-admin/approval/${requestId}/${action}`;
    modal.classList.remove('hidden');
}

function closeActionModal() {
    document.getElementById('actionModal').classList.add('hidden');
    document.getElementById('actionNotes').value = '';
    currentRequestId = null;
    currentAction = null;
}

// Close modals when clicking outside
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) closeDetailModal();
});

document.getElementById('actionModal').addEventListener('click', function(e) {
    if (e.target === this) closeActionModal();
});
</script>
@endpush

@push('styles')
<style>
    /* Responsive utilities */
    @media (max-width: 640px) {
        .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }
</style>
@endpush