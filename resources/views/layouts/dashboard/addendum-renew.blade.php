@extends('layouts.app')

@section('title', 'Perpanjang Addendum')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4" x-data="renewForm()" x-init="init()">

    {{-- Breadcrumb --}}
    <nav class="flex mb-6 text-sm text-gray-500">
        <a href="{{ route('customer.contracts.index') }}" class="hover:text-orange-500">Kontrak Saya</a>
        <span class="mx-2">/</span>
        <span class="text-gray-700">Perpanjang Addendum</span>
    </nav>

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Perpanjang Addendum</h1>

    {{-- Detail Addendum Saat Ini --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6 shadow-sm">
        <h2 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Detail Addendum Saat Ini
        </h2>

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500 text-xs uppercase tracking-wider">No. Addendum</dt>
                <dd class="font-medium text-gray-800">{{ $addendum->addendum_number ?? 'Addendum #' . $addendum->id }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs uppercase tracking-wider">Kontrak Induk</dt>
                <dd class="font-medium text-gray-800">{{ $addendum->contract->contract_number ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs uppercase tracking-wider">Lokasi</dt>
                <dd class="font-medium text-gray-800">{{ $addendum->contract->transaction->location->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs uppercase tracking-wider">Status</dt>
                <dd>{!! $addendum->status_badge !!}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs uppercase tracking-wider">Tanggal Mulai</dt>
                <dd class="font-medium text-gray-800">{{ $addendum->formatted_start_date ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs uppercase tracking-wider">Tanggal Berakhir</dt>
                <dd class="font-medium {{ $addendum->status === 'expired' ? 'text-red-600' : 'text-gray-800' }}">
                    {{ $addendum->formatted_end_date ?? '-' }}
                </dd>
            </div>
        </dl>
    </div>

    {{-- Form Pilih Durasi Perpanjangan --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <h2 class="font-semibold text-gray-700 mb-4">Pilih Durasi Perpanjangan</h2>

        {{-- Loading State --}}
        <div x-show="isLoading" class="text-center py-8">
            <svg class="w-8 h-8 mx-auto animate-spin text-orange-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="mt-3 text-gray-500">Memuat data harga...</p>
        </div>

        {{-- Error State --}}
        <div x-show="hasError" class="bg-red-50 text-red-600 p-4 rounded-lg text-center">
            <p x-text="errorMessage"></p>
            <button @click="fetchPackages" class="mt-2 text-sm underline">Coba Lagi</button>
        </div>

        {{-- Form Content --}}
        <div x-show="!isLoading && !hasError">

            {{-- Info Harga Dasar --}}
            <div class="bg-blue-50 p-4 rounded-lg mb-6">
                <p class="text-sm text-blue-800">
                    <span class="font-medium">Harga per Bulan:</span>
                    <span x-text="formatRupiah(pricePerMonth)"></span>
                </p>
                <p class="text-xs text-blue-600 mt-1">* Harga diambil dari paket yang tersedia</p>
            </div>

            <form @submit.prevent="submitForm">
                @csrf

                <div class="space-y-6">

                    {{-- Pilihan Paket Tersedia --}}
                    <div x-show="availablePackages.length > 0">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Paket Tersedia</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <template x-for="pkg in availablePackages" :key="pkg.id">
                                <div class="relative flex cursor-pointer rounded-lg border p-4 transition-all"
                                     :class="selectedPackageId === pkg.id ? 'border-orange-500 bg-orange-50 ring-2 ring-orange-500' : 'border-gray-300 hover:border-gray-400'"
                                     @click="selectPackage(pkg)">

                                    <div class="flex items-center mr-3">
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                             :class="selectedPackageId === pkg.id ? 'border-orange-500' : 'border-gray-400'">
                                            <div class="w-2.5 h-2.5 rounded-full bg-orange-500"
                                                 x-show="selectedPackageId === pkg.id"></div>
                                        </div>
                                    </div>

                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900" x-text="pkg.name"></span>
                                            <span class="mt-1 text-sm text-gray-500">
                                                <span x-text="pkg.duration + ' ' + (pkg.duration_type === 'month' ? 'Bulan' : 'Tahun')"></span>
                                            </span>
                                            <span class="mt-2 text-lg font-semibold text-gray-900"
                                                  x-text="formatRupiah(pkg.base_price)">
                                            </span>
                                        </span>
                                    </span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- ATAU Pilih Durasi Kustom --}}
                    <div class="border-t pt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Atau Pilih Durasi Kustom
                        </label>

                        <div class="flex gap-4 items-end">
                            <div class="flex-1">
                                <label class="block text-xs text-gray-500 mb-1">Jumlah</label>
                                <input type="number"
                                       x-model.number="customDurationValue"
                                       min="1"
                                       max="60"
                                       class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                                       @input="useCustomDuration">
                            </div>
                            <div class="w-32">
                                <label class="block text-xs text-gray-500 mb-1">Satuan</label>
                                <select x-model="customDurationType"
                                        class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                                        @change="useCustomDuration">
                                    <option value="month">Bulan</option>
                                    <option value="year">Tahun</option>
                                </select>
                            </div>
                        </div>

                        <p class="text-xs text-gray-500 mt-2">Maksimal 60 bulan (5 tahun)</p>
                    </div>

                    {{-- ✅ TAMBAHAN: Input Tanggal Addendum --}}
                    <div class="border-t pt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Addendum <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-gray-500 mb-3">
                            Tanggal ini akan tercetak di dokumen Addendum sebagai tanggal penerbitan dan tanggal mulai kontrak baru.
                        </p>
                        <input type="date"
                               x-model="addendumDate"
                               class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 p-2.5"
                               :class="addendumDateError ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''">
                        <p x-show="addendumDateError"
                           x-text="addendumDateError"
                           class="mt-1 text-xs text-red-500"></p>
                    </div>

                    {{-- Ringkasan Durasi yang Dipilih --}}
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Durasi yang dipilih:</span>
                            <span class="font-medium" x-text="selectedDurationText"></span>
                        </div>
                        {{-- ✅ Tampilkan estimasi end date jika addendum_date sudah diisi --}}
                        <div x-show="addendumDate" class="mt-2 pt-2 border-t border-gray-200">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">Mulai:</span>
                                <span class="font-medium text-gray-700" x-text="formatDate(addendumDate)"></span>
                            </div>
                            <div class="flex justify-between items-center text-sm mt-1">
                                <span class="text-gray-500">Berakhir (estimasi):</span>
                                <span class="font-medium text-gray-700" x-text="estimatedEndDate"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Estimasi Total --}}
                    <div class="bg-orange-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Total Pembayaran</p>
                        <p class="text-3xl font-bold text-orange-700" x-text="formatRupiah(estimatedTotal)"></p>
                        <p class="text-xs text-gray-500 mt-2" x-text="calculationDetail"></p>
                    </div>

                    {{-- Loading Submit --}}
                    <div x-show="isSubmitting" class="flex items-center gap-2 text-orange-600">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span class="text-sm">Memproses...</span>
                    </div>

                    {{-- Error Submit --}}
                    <div x-show="submitError"
                         class="bg-red-50 text-red-600 p-3 rounded-lg text-sm"
                         x-text="submitError"></div>

                    {{-- Buttons --}}
                    <div class="flex gap-3 pt-4">
                        <button type="submit"
                                :disabled="isSubmitting || estimatedTotal <= 0 || !addendumDate"
                                class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed transition">
                            Lanjut ke Pembayaran
                        </button>
                        <a href="{{ route('customer.contracts.index') }}"
                           class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Batal
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

{{-- Include Modal Component --}}
@include('layouts.components.midtrans-payment-modal')

@endsection

@push('scripts')
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('renewForm', () => ({
            // State
            isLoading: true,
            hasError: false,
            errorMessage: '',
            availablePackages: [],
            _selectedPackageId: null,
            customDurationValue: 1,
            customDurationType: 'month',
            isSubmitting: false,
            submitError: '',
            pricePerMonthOverride: 0,

            // ✅ TAMBAHAN: state untuk addendum date
            addendumDate: '',
            addendumDateError: '',

            // Route dari Blade — hindari hardcode di JS
            packagesUrl: '{{ route("virtual-office-packages") ?? "/virtual-office-packages" }}',

            // Getter/Setter
            get selectedPackageId() {
                return this._selectedPackageId;
            },

            set selectedPackageId(value) {
                this._selectedPackageId = value;
            },

            // Init
            async init() {
                await this.fetchPackages();
            },

            // Fetch packages
            async fetchPackages() {
                this.isLoading = true;
                this.hasError  = false;

                try {
                    const response = await fetch('/virtual-office-packages');

                    if (!response.ok) {
                        throw new Error('Gagal memuat daftar paket');
                    }

                    const data = await response.json();
                    this.availablePackages = data;

                    const defaultPackage = this.availablePackages.find(p =>
                        p.duration_type === 'year' && p.duration === 1
                    ) || this.availablePackages[0];

                    if (defaultPackage) {
                        this.selectPackage(defaultPackage);
                    }

                } catch (error) {
                    console.error('Fetch error:', error);
                    this.hasError     = true;
                    this.errorMessage = error.message || 'Terjadi kesalahan saat memuat paket.';
                } finally {
                    this.isLoading = false;
                }
            },

            // Pilih paket
            selectPackage(pkg) {
                this._selectedPackageId = pkg.id;

                // ✅ FIX: reset override saat pilih paket
                this.pricePerMonthOverride = 0;
            },

            // Gunakan durasi kustom
            useCustomDuration() {
                this._selectedPackageId = null;

                // ✅ FIX: set override dari paket terakhir yang dipilih jika ada
                if (this.availablePackages.length > 0) {
                    const monthlyPkg = this.availablePackages.find(p =>
                        p.duration_type === 'month' && p.duration === 1
                    );
                    if (monthlyPkg) {
                        this.pricePerMonthOverride = monthlyPkg.base_price;
                    } else {
                        const yearlyPkg = this.availablePackages.find(p =>
                            p.duration_type === 'year' && p.duration === 1
                        );
                        if (yearlyPkg) {
                            this.pricePerMonthOverride = yearlyPkg.base_price / 12;
                        }
                    }
                }
            },

            // Computed: paket yang dipilih
            get selectedPackage() {
                return this.availablePackages.find(p => p.id === this._selectedPackageId);
            },

            // Computed: Harga per bulan
            get pricePerMonth() {
                if (this.selectedPackage) {
                    const pkg = this.selectedPackage;
                    return pkg.duration_type === 'month'
                        ? pkg.base_price / pkg.duration
                        : pkg.base_price / (pkg.duration * 12);
                }

                if (this.pricePerMonthOverride > 0) {
                    return this.pricePerMonthOverride;
                }

                if (this.availablePackages.length > 0) {
                    const monthlyPkg = this.availablePackages.find(p =>
                        p.duration_type === 'month' && p.duration === 1
                    );
                    if (monthlyPkg) return monthlyPkg.base_price;

                    const yearlyPkg = this.availablePackages.find(p =>
                        p.duration_type === 'year' && p.duration === 1
                    );
                    if (yearlyPkg) return yearlyPkg.base_price / 12;
                }

                return 0;
            },

            // Computed: durasi yang dipilih
            get selectedDuration() {
                if (this.selectedPackage) {
                    return {
                        type: this.selectedPackage.duration_type,
                        value: this.selectedPackage.duration,
                    };
                }
                return {
                    type: this.customDurationType,
                    // ✅ FIX: validasi min 1
                    value: Math.max(1, Math.min(this.customDurationValue, 60)),
                };
            },

            // Computed: teks durasi
            get selectedDurationText() {
                const dur     = this.selectedDuration;
                const satuan  = dur.type === 'month' ? 'Bulan' : 'Tahun';
                const custom  = this.selectedPackage ? '' : ' (Custom)';
                return `${dur.value} ${satuan}${custom}`;
            },

            // Computed: estimasi total
            get estimatedTotal() {
                if (this.selectedPackage) {
                    return this.selectedPackage.base_price;
                }

                // ✅ FIX: validasi min 1
                if (this.customDurationValue < 1 || this.customDurationValue > 60) {
                    return 0;
                }

                const price = this.pricePerMonth;
                if (price <= 0) return 0;

                const dur = this.selectedDuration;
                return dur.type === 'month'
                    ? price * dur.value
                    : price * 12 * dur.value;
            },

            // Computed: detail kalkulasi
            get calculationDetail() {
                if (this.selectedPackage) {
                    const pkg    = this.selectedPackage;
                    const satuan = pkg.duration_type === 'month' ? 'Bulan' : 'Tahun';
                    return `Paket ${pkg.duration} ${satuan} - ${pkg.name}`;
                }

                const price = this.pricePerMonth;
                if (price <= 0) return '';

                if (this.customDurationValue < 1) {
                    return 'Minimal durasi adalah 1 bulan';
                }

                if (this.customDurationValue > 60) {
                    return 'Maksimal 60 bulan (5 tahun)';
                }

                const dur          = this.selectedDuration;
                const hargaPerBulan = this.formatRupiah(price);

                return dur.type === 'month'
                    ? `${dur.value} Bulan × ${hargaPerBulan}/bulan`
                    : `${dur.value} Tahun (${dur.value * 12} bulan) × ${hargaPerBulan}/bulan`;
            },

            // ✅ TAMBAHAN: Computed estimasi end date
            get estimatedEndDate() {
                if (!this.addendumDate) return '-';

                const date = new Date(this.addendumDate);
                const dur  = this.selectedDuration;

                if (dur.type === 'month') {
                    date.setMonth(date.getMonth() + dur.value);
                } else {
                    date.setFullYear(date.getFullYear() + dur.value);
                }

                // Kurangi 1 hari agar end date = hari terakhir periode
                date.setDate(date.getDate() - 1);

                return this.formatDate(date.toISOString().split('T')[0]);
            },

            // ✅ TAMBAHAN: Format tanggal untuk display
            formatDate(dateStr) {
                if (!dateStr) return '-';
                const date = new Date(dateStr);
                return date.toLocaleDateString('id-ID', {
                    day:   'numeric',
                    month: 'long',
                    year:  'numeric',
                });
            },

            formatRupiah(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style:                 'currency',
                    currency:              'IDR',
                    minimumFractionDigits: 0,
                }).format(amount);
            },

            // ✅ TAMBAHAN: Validasi addendum date
            validateAddendumDate() {
                this.addendumDateError = '';

                if (!this.addendumDate) {
                    this.addendumDateError = 'Tanggal addendum wajib diisi';
                    return false;
                }

                return true;
            },

            async submitForm() {
                // Validasi total
                if (this.estimatedTotal <= 0) {
                    this.submitError = 'Total pembayaran tidak valid';
                    return;
                }

                // ✅ FIX: validasi min 1
                if (!this.selectedPackage && this.customDurationValue < 1) {
                    this.submitError = 'Minimal durasi adalah 1 bulan';
                    return;
                }

                if (!this.selectedPackage && this.customDurationValue > 60) {
                    this.submitError = 'Maksimal durasi adalah 60 bulan (5 tahun)';
                    return;
                }

                // ✅ TAMBAHAN: Validasi addendum date
                if (!this.validateAddendumDate()) {
                    return;
                }

                this.isSubmitting = true;
                this.submitError  = '';

                const dur = this.selectedDuration;

                const formData = new FormData();
                formData.append('duration_type',  dur.type);
                formData.append('duration_value', dur.value);
                formData.append('total_amount',   this.estimatedTotal);
                formData.append('addendum_date',  this.addendumDate); // ✅ TAMBAHAN
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                if (this.selectedPackage) {
                    formData.append('package_id', this.selectedPackage.id);
                }

                try {
                    const response = await fetch('{{ route("customer.addendums.renew.process", $addendum->id) }}', {
                        method: 'POST',
                        headers: {
                            'Accept':       'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: formData,
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal memproses perpanjangan');
                    }

                    // Buka modal Midtrans
                    if (window.MidtransModal && window.MidtransModal.open) {
                        window.MidtransModal.open({
                            order_id:     data.order_id,
                            total_amount: data.total_amount,
                            snap_token:   data.snap_token,
                        });
                    } else {
                        // ✅ FIX: Hapus fallback __x.$data yang deprecated
                        this.submitError = 'Modal pembayaran tidak tersedia. Silakan refresh halaman.';
                    }

                } catch (error) {
                    this.submitError = error.message || 'Terjadi kesalahan. Silakan coba lagi.';
                } finally {
                    this.isSubmitting = false;
                }
            },
        }));
    });
</script>
@endpush