{{-- resources/views/layouts/admin/management-invoice.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manajemen Invoice & Kontrak')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-6 font-['DM_Sans'] text-gray-900">
    <div class="container mx-auto px-4 max-w-7xl">

        {{-- ── PAGE HEADER ── --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <h1 class="font-['Syne'] text-2xl md:text-3xl font-extrabold text-gray-900 leading-tight tracking-tight">
                    Manajemen Invoice
                    <span class="block font-['DM_Sans'] text-sm font-normal text-gray-500 mt-1 tracking-normal">Kelola invoice & kontrak sewa ruangan</span>
                </h1>
            </div>
            <nav class="flex items-center gap-2 text-sm text-gray-500">
                <a href="#" class="text-blue-600 hover:underline">Dashboard</a>
                <span class="text-gray-300">/</span>
                <span>Invoice</span>
            </nav>
        </div>

        {{-- ── MAIN CARD & ALPINE WRAPPER ── --}}
        <div x-data="invoiceManager()" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">

            {{-- Global success toast --}}
            <div x-show="successMessage"
                 x-cloak
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-x-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed top-6 right-6 z-50 flex items-center gap-3 px-5 py-3.5 bg-green-50 text-green-700 border border-green-200 rounded-xl shadow-lg font-medium text-sm max-w-sm pointer-events-none">
                <i class="fas fa-check-circle text-base"></i>
                <span x-text="successMessage"></span>
            </div>

            {{-- TAB NAVIGATION --}}
            <div class="bg-gray-50/50 border-b border-gray-200 px-6 pt-4 flex items-end gap-2 overflow-x-auto hide-scrollbar">
                <button @click="activeTab = 'list'"
                        :class="activeTab === 'list' ? 'text-blue-600 border-blue-600 bg-white font-semibold' : 'text-gray-500 border-transparent hover:text-gray-800 hover:bg-blue-50/50'"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium transition-all duration-200 border-b-2 rounded-t-lg relative outline-none whitespace-nowrap">
                    <span :class="activeTab === 'list' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100'"
                          class="w-7 h-7 flex items-center justify-center rounded-md text-xs transition-colors">
                        <i class="fas fa-file-invoice"></i>
                    </span>
                    <span class="hidden sm:inline">Daftar Invoice</span>
                </button>
                <button @click="activeTab = 'generate'"
                        :class="activeTab === 'generate' ? 'text-blue-600 border-blue-600 bg-white font-semibold' : 'text-gray-500 border-transparent hover:text-gray-800 hover:bg-blue-50/50'"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium transition-all duration-200 border-b-2 rounded-t-lg relative outline-none whitespace-nowrap">
                    <span :class="activeTab === 'generate' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100'"
                          class="w-7 h-7 flex items-center justify-center rounded-md text-xs transition-colors">
                        <i class="fas fa-plus-circle"></i>
                    </span>
                    <span class="hidden sm:inline">Generate Invoice</span>
                </button>
                @if(auth()->check() && auth()->user()->role === 'finance')
                <button @click="activeTab = 'approvals'"
                        :class="activeTab === 'approvals' ? 'text-blue-600 border-blue-600 bg-white font-semibold' : 'text-gray-500 border-transparent hover:text-gray-800 hover:bg-blue-50/50'"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium transition-all duration-200 border-b-2 rounded-t-lg relative outline-none whitespace-nowrap">
                    <span :class="activeTab === 'approvals' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100'"
                          class="w-7 h-7 flex items-center justify-center rounded-md text-xs transition-colors relative">
                        <i class="fas fa-clipboard-check"></i>
                        @if(($stats['pending_approvals'] ?? 0) > 0)
                            <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        @endif
                    </span>
                    <span class="hidden sm:inline">Persetujuan Settlement</span>
                </button>
                @endif
                @if(auth()->check() && auth()->user()->role === 'admin')
                <button @click="activeTab = 'history'"
                        :class="activeTab === 'history' ? 'text-blue-600 border-blue-600 bg-white font-semibold' : 'text-gray-500 border-transparent hover:text-gray-800 hover:bg-blue-50/50'"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium transition-all duration-200 border-b-2 rounded-t-lg relative outline-none whitespace-nowrap">
                    <span :class="activeTab === 'history' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100'"
                          class="w-7 h-7 flex items-center justify-center rounded-md text-xs transition-colors">
                        <i class="fas fa-history"></i>
                    </span>
                    <span class="hidden sm:inline">Riwayat Pengajuan</span>
                </button>
                @endif
            </div>

            {{-- TAB CONTENT --}}
            <div class="p-6 md:p-8">

                {{-- TAB 1: DAFTAR INVOICE --}}
                <div x-show="activeTab === 'list'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="space-y-6">
                    @include('layouts.admin.components.invoice-list')
                </div>

                {{-- TAB 2: GENERATE INVOICE --}}
                <div x-show="activeTab === 'generate'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-cloak
                     class="space-y-6">
                    @include('layouts.admin.components.invoice-generate')
                </div>

                {{-- TAB 3: APPROVALS (FINANCE) --}}
                @if(auth()->check() && auth()->user()->role === 'finance')
                <div x-show="activeTab === 'approvals'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-cloak
                     class="space-y-6">
                    @include('layouts.admin.components.invoice-approval')
                </div>
                @endif

                {{-- TAB 4: RIWAYAT PENGAJUAN (ALL ADMINS) --}}
                @if(auth()->check() && auth()->user()->role === 'admin')
                <div x-show="activeTab === 'history'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-cloak
                     class="space-y-6">
                    @include('layouts.admin.components.invoice-history')
                </div>
                @endif

            </div>{{-- end p-6 --}}

            {{-- MODAL BUKTI TRANSFER (POPUP KECIL DI TENGAH) --}}
            <div x-show="showProofModal"
                 class="fixed inset-0 z-[2000] overflow-y-auto"
                 style="z-index: 2000;"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 x-cloak>
                 <div class="fixed inset-0 transition-opacity" style="background-color: rgba(255, 255, 255, 0.5); backdrop-filter: blur(2px);" @click="closeProofModal()"></div>
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <div class="relative transform overflow-hidden rounded-xl bg-white p-6 text-left shadow-2xl transition-all max-w-lg w-full border border-gray-100"
                         @keydown.escape.window="closeProofModal()">
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="text-md font-bold text-gray-900"><i class="fas fa-image text-blue-600 mr-1.5"></i> Bukti Transfer</h5>
                            <button type="button" @click="closeProofModal()" class="text-gray-400 hover:text-gray-500 rounded-lg p-1 hover:bg-gray-100 transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="flex justify-center bg-gray-50 rounded-xl p-2 border border-gray-100 overflow-hidden max-h-[70vh]">
                            <img :src="proofImgUrl" alt="Bukti Transfer" class="max-w-full max-h-[60vh] object-contain rounded-lg">
                        </div>
                        <div class="mt-4 flex justify-end">
                            <a :href="proofImgUrl" target="_blank" class="px-3.5 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg font-medium text-xs hover:bg-blue-100 transition-colors mr-2">
                                <i class="fas fa-external-link-alt mr-1"></i> Buka di Tab Baru
                            </a>
                            <button type="button" @click="closeProofModal()" class="px-3.5 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium text-xs hover:bg-gray-200 transition-colors">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MODAL KONFIRMASI APPROVE --}}
            <div x-show="showApproveConfirmModal"
                 class="fixed inset-0 z-[2000] overflow-y-auto"
                 style="z-index: 2000;"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 x-cloak>
                 <div class="fixed inset-0 transition-opacity" style="background-color: rgba(255, 255, 255, 0.5); backdrop-filter: blur(2px);" @click="closeApproveConfirmModal()"></div>
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <div class="relative transform overflow-hidden rounded-xl bg-white p-5 text-left shadow-2xl transition-all max-w-sm w-full border border-gray-100"
                         @keydown.escape.window="closeApproveConfirmModal()">
                        <div class="flex items-center gap-3 mb-3 text-green-600">
                            <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-lg">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h5 class="text-sm font-bold text-gray-900">Setujui Settlement</h5>
                        </div>
                        <p class="text-xs text-gray-600 mb-4 leading-relaxed" style="font-size: 11px;">
                            Apakah Anda yakin ingin menyetujui pengajuan settlement untuk nomor invoice <strong class="text-gray-900 font-semibold" x-text="confirmInvoiceNumber"></strong>? Tindakan ini akan langsung mengubah status invoice menjadi <strong class="text-green-600">settlement</strong>.
                        </p>
                        <form :action="'/admin/invoices/approvals/' + confirmInvoiceId + '/approve'" method="POST">
                            @csrf
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="closeApproveConfirmModal()" class="px-3.5 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium text-xs hover:bg-gray-200 transition-colors" style="font-size: 11px;">
                                    Batal
                                </button>
                                <button type="submit" class="px-3.5 py-2 bg-green-600 text-white rounded-lg font-medium text-xs hover:bg-green-700 transition-colors shadow-sm" style="font-size: 11px;">
                                    Ya, Setujui
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- MODAL KONFIRMASI REJECT --}}
            <div x-show="showRejectConfirmModal"
                 class="fixed inset-0 z-[2000] overflow-y-auto"
                 style="z-index: 2000;"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 x-cloak>
                 <div class="fixed inset-0 transition-opacity" style="background-color: rgba(255, 255, 255, 0.5); backdrop-filter: blur(2px);" @click="closeRejectConfirmModal()"></div>
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <div class="relative transform overflow-hidden rounded-xl bg-white p-5 text-left shadow-2xl transition-all max-w-md w-full border border-gray-100"
                         @keydown.escape.window="closeRejectConfirmModal()">
                        <div class="flex items-center gap-3 mb-3 text-red-600">
                            <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-lg">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <h5 class="text-sm font-bold text-gray-900">Tolak Settlement</h5>
                        </div>
                        <p class="text-xs text-gray-600 mb-3 leading-relaxed" style="font-size: 11px;">
                            Silakan masukkan alasan penolakan untuk pengajuan settlement nomor invoice <strong class="text-gray-900 font-semibold" x-text="confirmInvoiceNumber"></strong>:
                        </p>
                        <form :action="'/admin/invoices/approvals/' + confirmInvoiceId + '/reject'" method="POST">
                            @csrf
                            <div class="mb-4">
                                <textarea name="rejection_reason" 
                                          required 
                                          rows="3" 
                                          class="w-full text-xs border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded-lg p-2 outline-none resize-none" 
                                          placeholder="Tuliskan alasan penolakan di sini..." style="font-size: 11px;"></textarea>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="closeRejectConfirmModal()" class="px-3.5 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium text-xs hover:bg-gray-200 transition-colors" style="font-size: 11px;">
                                    Batal
                                </button>
                                <button type="submit" class="px-3.5 py-2 bg-red-600 text-white rounded-lg font-medium text-xs hover:bg-red-700 transition-colors shadow-sm" style="font-size: 11px;">
                                    Tolak Pengajuan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @include('layouts.admin.components.modal-invoice-manual')
            @include('layouts.admin.components.modal-invoice-detail')
            @include('layouts.admin.components.modal-contract-generate')
        </div>{{-- end inv-card --}}

    </div>
</div>
@endsection

@push('scripts')
<style>
/* Hide scrollbar for Chrome, Safari and Opera */
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
/* Hide scrollbar for IE, Edge and Firefox */
.hide-scrollbar {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
[x-cloak] { display: none !important; }
</style>
<script>
document.addEventListener('alpine:init', () => {
    console.log('=== PHP ROOMS DATA ===');
    console.log(@json($rooms));
    Alpine.data('invoiceManager', () => ({
        // Tabs
        activeTab: new URLSearchParams(window.location.search).get('tab') || 'list',

        // Modal states
        showManualModal: false,
        showDetailModal: false,
        showContractModal: false,
        showRequestForm: false,
        showRejectForm: false,
        showProofModal: false,
        proofImgUrl: '',
        openedProofFromDetail: false,
        showApproveConfirmModal: false,
        showRejectConfirmModal: false,
        confirmInvoiceId: null,
        confirmInvoiceNumber: '',
        rejectionReason: '',
        openedConfirmFromDetail: false,

        // Data
        currentInvoice: null,
        contractTransactionId: null,
        contractDate: '',

        // Loading
        loading: false,
        loadingDetail: false,

        // Feedback
        error: @json($errors->first() ?: session('error')) || null,
        successMessage: @json(session('success')) || null,

        // Form
        manualForm: {
            nama_lengkap: '', company_name: '', phone: '', email: '', nik: '',
            city_id: '', location_id: '', room_id: '', room_type: '',
            booking_date: '', start_time: '',
            jumlah_orang: '',
            paket: '', jam: '', hari: '', minggu: '', bulan: '', tahun: '',
            service_category_id: '', coffee_break: '', status_pkp: '',
            lunch_option_id: '', lunch_quantity: '',
            gross_amount: '', deposit: '0', status: 'pending', notes: ''
        },

        // Dropdown Data
        cities: @json($cities ?? []),
        locations: @json($locations ?? []),
        rooms: @json($rooms ?? []),
        serviceCategories: @json($serviceCategories ?? []),
        lunchOptions: @json($lunchOptions ?? []),

        errors: {},
        todayDate: '',

        init() {
            this.todayDate = new Date().toISOString().split('T')[0];
            
            // Watchers for cascading dropdowns
            this.$watch('manualForm.city_id', value => {
                this.manualForm.location_id = '';
                this.manualForm.room_id = '';
            });
            this.$watch('manualForm.location_id', value => {
                this.manualForm.room_id = '';
            });
            this.$watch('manualForm.room_type', value => {
                // Reset fields dependent on room_type
                this.manualForm.room_id = ''
                this.manualForm.paket = '';
                this.manualForm.jam = '';
                this.manualForm.hari = '';
                this.manualForm.minggu = '';
                this.manualForm.bulan = '';
                this.manualForm.tahun = '';
                this.manualForm.service_category_id = '';
                this.manualForm.coffee_break = '';
                this.manualForm.status_pkp = '';
            });
            
            // Cek URL params untuk mempertahankan tab aktif pasca reload/submit form
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('tab')) {
                const requestedTab = urlParams.get('tab');
                if (['list', 'generate', 'approvals'].includes(requestedTab)) {
                    this.activeTab = requestedTab;
                }
            }
        },

        // Getters for cascading dropdowns
        get filteredLocations() {
            if (!this.manualForm.city_id) return [];
            return this.locations.filter(l => l.city_id == this.manualForm.city_id);
        },
        get filteredRooms() {
            if (!this.manualForm.location_id || !this.manualForm.room_type) return [];

            return this.rooms.filter(r =>
                String(r.location_id) === String(this.manualForm.location_id) &&
                r.type === this.manualForm.room_type
            );
        },

        get pdfUrl() {
            if (!this.currentInvoice) return '#';
            return '{{ route("admin.invoices.pdf", ["invoice" => "__ID__"]) }}'
                .replace('__ID__', this.currentInvoice.id);
        },
        get waUrl() {
            if (!this.currentInvoice) return '#';
            return '{{ route("admin.invoices.whatsapp", ["invoice" => "__ID__"]) }}'
                .replace('__ID__', this.currentInvoice.id);
        },

        get totalDibayar() {
            const total   = parseFloat(this.manualForm.gross_amount) || 0;
            const deposit = parseFloat(this.manualForm.deposit)      || 0;
            return Math.max(0, total + deposit);
        },
        get isDepositExceeded() {
            const total   = parseFloat(this.manualForm.gross_amount) || 0;
            const deposit = parseFloat(this.manualForm.deposit)      || 0;
            return deposit > 0 && total > 0 && deposit > total;
        },

        async openDetailModal(invoiceId) {
            this.currentInvoice  = null;
            this.error           = null;
            this.loadingDetail   = true;
            this.showDetailModal = true;
            this.showRequestForm = false;
            this.showRejectForm  = false;

            try {
                const response = await fetch(`/admin/invoices/${invoiceId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!response.ok) throw new Error('Network response was not ok');
                const result = await response.json();
                if (result.success) {
                    this.currentInvoice = result.data;
                } else {
                    this.error = result.message || 'Gagal mengambil data invoice';
                }
            } catch (error) {
                this.error = 'Terjadi kesalahan saat mengambil data';
                console.error('Error:', error);
            } finally {
                this.loadingDetail = false;
            }
        },

        openContractModal(transactionId) {
            this.contractTransactionId = transactionId;
            this.contractDate = new Date().toISOString().split('T')[0];
            this.showContractModal = true;
        },
        
        closeContractModal() {
            this.showContractModal = false;
            setTimeout(() => {
                this.contractTransactionId = null;
                this.contractDate = '';
            }, 300);
        },
        
        openProofModal(imageUrl, fromDetail = false) {
            if (fromDetail) {
                this.showDetailModal = false;
                this.openedProofFromDetail = true;
                this.proofImgUrl = imageUrl;
                setTimeout(() => {
                    this.showProofModal = true;
                }, 300);
            } else {
                this.openedProofFromDetail = false;
                this.proofImgUrl = imageUrl;
                this.showProofModal = true;
            }
        },

        closeProofModal() {
            this.showProofModal = false;
            this.proofImgUrl = '';
            if (this.openedProofFromDetail) {
                setTimeout(() => {
                    this.showDetailModal = true;
                    this.openedProofFromDetail = false;
                }, 300);
            }
        },

        triggerApprove(invoiceId, invoiceNumber, fromDetail = false) {
            this.confirmInvoiceId = invoiceId;
            this.confirmInvoiceNumber = invoiceNumber;
            this.openedConfirmFromDetail = fromDetail;
            if (fromDetail) {
                this.showDetailModal = false;
                setTimeout(() => {
                    this.showApproveConfirmModal = true;
                }, 300);
            } else {
                this.showApproveConfirmModal = true;
            }
        },

        closeApproveConfirmModal() {
            this.showApproveConfirmModal = false;
            if (this.openedConfirmFromDetail) {
                setTimeout(() => {
                    this.showDetailModal = true;
                    this.openedConfirmFromDetail = false;
                }, 300);
            }
        },

        triggerReject(invoiceId, invoiceNumber, fromDetail = false) {
            this.confirmInvoiceId = invoiceId;
            this.confirmInvoiceNumber = invoiceNumber;
            this.rejectionReason = '';
            this.openedConfirmFromDetail = fromDetail;
            if (fromDetail) {
                this.showDetailModal = false;
                setTimeout(() => {
                    this.showRejectConfirmModal = true;
                }, 300);
            } else {
                this.showRejectConfirmModal = true;
            }
        },

        closeRejectConfirmModal() {
            this.showRejectConfirmModal = false;
            if (this.openedConfirmFromDetail) {
                setTimeout(() => {
                    this.showDetailModal = true;
                    this.openedConfirmFromDetail = false;
                }, 300);
            }
        },
        
        generateContract() {
            if(!this.contractDate) {
                alert('Tanggal kontrak wajib diisi');
                return;
            }
            let url = `/admin/contracts/virtual-office/${this.contractTransactionId}/pdf?contract_date=${this.contractDate}`;
            window.open(url, '_blank');
            this.closeContractModal();
        },

        openManualModal() {
            this.resetManualForm();
            this.errors          = {};
            this.error           = null;
            this.successMessage  = null;
            this.showManualModal = true;
        },

        resetManualForm() {
            this.manualForm = {
                nama_lengkap: '', company_name: '', phone: '', email: '', nik: '',
                city_id: '', location_id: '', room_id: '', room_type: '',
                booking_date: '', start_time: '',
                jumlah_orang: '',
                paket: '', jam: '', hari: '', minggu: '', bulan: '', tahun: '',
                service_category_id: '', coffee_break: '', status_pkp: '',
                lunch_option_id: '', lunch_quantity: '',
                gross_amount: '', deposit: '0', status: 'pending', notes: ''
            };
        },

        async submitManualForm() {
            if (this.isDepositExceeded) {
                this.error = 'Deposit tidak boleh melebihi total sewa';
                return;
            }

            this.loading        = true;
            this.errors         = {};
            this.error          = null;
            this.successMessage = null;

            try {
                const formData = new FormData();
                Object.keys(this.manualForm).forEach(key => {
                    formData.append(key, this.manualForm[key] ?? '');
                });

                const response = await fetch('{{ route("admin.invoices.store-manual") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    this.successMessage  = result.message;
                    this.showManualModal = false;
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    if (result.errors) this.errors = result.errors;
                    else this.error = result.message || 'Gagal menyimpan invoice';
                }
            } catch (error) {
                this.error = 'Terjadi kesalahan saat menyimpan data';
                console.error('Error:', error);
            } finally {
                this.loading = false;
            }
        },

        formatCurrency(value) {
            if (!value && value !== 0) return 'Rp 0';
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
        },
        formatDate(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleDateString('id-ID', {
                day: 'numeric', month: 'long', year: 'numeric'
            });
        }
    }));
});
</script>
@endpush