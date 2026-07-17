@extends('layouts.app')

@section('content')

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('contractData', () => ({
        isFilterOpen: false,
        allContracts: @json($contractsData),
        filterStatus: '',

        statuses: [
            { value: 'draft',      label: 'Draft' },
            { value: 'active',     label: 'Aktif' },
            { value: 'expired',    label: 'Expired' },
            { value: 'terminated', label: 'Terminated' },
            { value: 'renewed',    label: 'Diperpanjang' },
        ],

        get filteredContracts() {
            return this.allContracts.filter(contract => {
                return this.filterStatus === '' || contract.status === this.filterStatus;
            });
        },

        resetFilter() {
            this.filterStatus = '';
        },

        statusLabel(status) {
            const map = {
                draft:      'Draft',
                active:     'Aktif',
                active_renewed: 'Aktif (Diperpanjang)',
                expired:    'Expired',
                terminated: 'Terminated',
                renewed:    'Diperpanjang',
            };
            return map[status] ?? status;
        },

        statusClass(status) {
            const map = {
                draft:      'bg-gray-100 text-gray-700',
                active:     'bg-green-100 text-green-800',
                active_renewed: 'bg-green-100 text-green-800',
                expired:    'bg-orange-100 text-orange-800',
                terminated: 'bg-red-100 text-red-800',
                renewed:    'bg-blue-100 text-blue-800',
            };
            return map[status] ?? 'bg-gray-100 text-gray-700';
        },

        init() {
            const el = document.getElementById('contract-wrapper');
            if (el) {
                el.classList.remove('opacity-0', 'translate-y-6');
                el.classList.add('opacity-100', 'translate-y-0');
            }
        }
    }));
});
</script>

<div class="flex min-h-screen bg-gray-50">
    <div class="flex-1 ml-0 md:ml-60 lg:ml-64 flex flex-col">

        <div>
            @include('layouts.components.invoice')
        </div>

        <div x-data="contractData" id="contract-wrapper"
             class="flex-1 p-4 opacity-0 translate-y-6 transition-all duration-500">

            {{-- HEADER --}}
            <div class="mb-6 flex justify-between items-center relative">
                <h1 class="text-lg font-bold">Kontrak Saya</h1>

                {{-- FILTER DROPDOWN --}}
                <div class="relative" @click.outside="isFilterOpen = false">
                    <button @click="isFilterOpen = !isFilterOpen"
                        class="flex items-center gap-2 px-3 py-2 bg-gray-100 rounded-lg text-sm hover:bg-gray-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M7 8h10M11 12h2"/>
                        </svg>
                        Filter
                        {{-- Indikator filter aktif --}}
                        @if(request('status'))
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                        @endif
                    </button>

                    <div x-show="isFilterOpen" x-transition
                        class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 p-4 rounded-xl shadow-md z-10">
                        <h4 class="font-semibold text-sm mb-3">Filter Status</h4>
                        <form method="GET" action="{{ route('customer.contracts.index') }}">
                            <div class="space-y-2">
                                @foreach([
                                    'active'     => 'Aktif',
                                    'expired'    => 'Expired',
                                    'terminated' => 'Terminated',
                                    'renewed'    => 'Diperpanjang',
                                ] as $value => $label)
                                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                                        <input type="radio" name="status" value="{{ $value }}"
                                            {{ request('status') === $value ? 'checked' : '' }}
                                            class="accent-orange-500"
                                            onchange="this.form.submit()">
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <a href="{{ route('customer.contracts.index') }}"
                            class="mt-4 block w-full text-sm text-gray-500 hover:text-gray-700 text-center">
                                Reset Filter
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            {{-- STATS SUMMARY --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6 max-w-4xl">
                <div class="bg-white rounded-xl p-3 border border-gray-100">
                    <p class="text-xs text-gray-500">Total</p>
                    <p class="text-xl font-semibold">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-green-50 rounded-xl p-3 border border-green-100">
                    <p class="text-xs text-green-600">Aktif</p>
                    <p class="text-xl font-semibold text-green-800">{{ $stats['active'] }}</p>
                </div>
                <div class="bg-orange-50 rounded-xl p-3 border border-orange-100">
                    <p class="text-xs text-orange-600">Expired</p>
                    <p class="text-xl font-semibold text-orange-800">{{ $stats['expired'] }}</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-3 border border-blue-100">
                    <p class="text-xs text-blue-600">Diperpanjang</p>
                    <p class="text-xl font-semibold text-blue-800">{{ $stats['renewed'] }}</p>
                </div>
            </div>

            {{-- LIST CONTRACT --}}
            <div class="space-y-3 max-w-4xl">

                <template x-for="contract in filteredContracts" :key="contract.id">
                    <div class="bg-white rounded-xl border border-gray-100 p-4 hover:shadow-sm transition">

                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="font-medium text-sm" x-text="contract.contract_number ?? 'Contract #' + contract.id"></p>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="contract.type"></p>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  :class="statusClass(contract.display_status)"
                                  x-text="statusLabel(contract.display_status)">
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3 text-sm">
                            <div>
                                <p class="text-xs text-gray-400">Mulai</p>
                                <p class="font-medium" x-text="contract.start_date"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Berakhir</p>
                                <p class="font-medium" x-text="contract.end_date"></p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            {{-- Sisa hari --}}
                            <span x-show="['active', 'active_renewed'].includes(contract.display_status) && contract.remaining_days !== null"
                                  class="text-xs bg-green-50 text-green-700 px-2 py-0.5 rounded-full">
                                Sisa <span x-text="contract.remaining_days"></span> hari
                            </span>
                            <span x-show="!['active', 'active_renewed'].includes(contract.display_status)" class="text-xs text-gray-400"
                                  x-text="statusLabel(contract.display_status)">
                            </span>

                            {{-- ACTION BUTTONS --}}
                                <div class="flex gap-2">
                                    {{-- Download PDF --}}
                                    <template x-if="contract.has_pdf">
                                        <a :href="contract.download_url" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-500 text-white text-xs rounded-lg hover:bg-orange-600 transition">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13 8V2H7v6H2l8 8 8-8h-5zm-7 10h8v2H6v-2z"/>
                                            </svg>
                                            PDF
                                        </a>
                                    </template>
                                    <template x-if="!contract.has_pdf">
                                        <span class="text-xs text-gray-400 italic px-3 py-1.5">PDF belum ada</span>
                                    </template>

                                    {{-- Perpanjang --}}
                                    <template x-if="['active', 'expired'].includes(contract.status) && (!contract.addendums || contract.addendums.length === 0)">
                                        <a :href="contract.renew_url" {{-- ✅ dari route, bukan hardcoded --}}
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-500 text-white text-xs rounded-lg hover:bg-green-600 transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            Perpanjang
                                        </a>
                                    </template>
                                </div>
                        </div>
                        {{-- ✅ Tampilkan alasan terminasi --}}
                        <template x-if="contract.status === 'terminated' && contract.termination_reason">
                                    <div class="mt-2 text-xs text-red-500 flex items-start gap-1">
                                        <svg class="w-3 h-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span x-text="'Alasan: ' + contract.termination_reason"></span>
                                    </div>
                        </template>

                        {{-- ADDENDUMS LIST --}}
                        <template x-if="contract.addendums && contract.addendums.length > 0">
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <p class="text-[11px] font-semibold text-gray-400 mb-2 uppercase tracking-wide">Riwayat Addendum</p>
                                <div class="space-y-2">
                                    <template x-for="addendum in contract.addendums" :key="addendum.id">
                                        <div class="flex justify-between items-center bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <p class="text-xs font-medium text-gray-800" x-text="addendum.number ?? 'Addendum #' + addendum.id"></p>
                                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-medium"
                                                          :class="statusClass(addendum.display_status)"
                                                          x-text="statusLabel(addendum.display_status)">
                                                    </span>
                                                    <span x-show="['active', 'active_renewed'].includes(addendum.display_status) && addendum.remaining_days !== null"
                                                          class="text-[10px] bg-green-50 text-green-700 px-1.5 py-0.5 rounded">
                                                        Sisa <span x-text="addendum.remaining_days"></span> hr
                                                    </span>
                                                </div>
                                                <p class="text-[11px] text-gray-500 mt-0.5">Berlaku s/d <span class="font-medium text-gray-700" x-text="contract.end_date"></span></p>
                                            </div>
                                            
                                            <template x-if="addendum.has_pdf">
                                                <a :href="addendum.download_url" target="_blank"
                                                   class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-white border border-gray-200 text-gray-700 text-[10px] sm:text-xs rounded-lg hover:bg-gray-50 transition shadow-sm">
                                                    <svg class="w-3 h-3 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M13 8V2H7v6H2l8 8 8-8h-5zm-7 10h8v2H6v-2z"/>
                                                    </svg>
                                                    PDF
                                                </a>
                                            </template>
                                            <template x-if="!addendum.has_pdf">
                                                <span class="text-[10px] text-gray-400 italic">Proses PDF</span>
                                            </template>
                                            
                                            {{-- Perpanjang Addendum --}}
                                            <template x-if="['active', 'expired'].includes(addendum.status) && addendum.is_latest">
                                                <a :href="addendum.renew_url"
                                                   class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-green-500 text-white text-[10px] sm:text-xs rounded-lg hover:bg-green-600 transition shadow-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                    </svg>
                                                    Perpanjang
                                                </a>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                    </div>
                </template>

                {{-- EMPTY STATE --}}
                <template x-if="filteredContracts.length === 0">
                    <div class="text-center py-16 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm">Tidak ada kontrak ditemukan</p>
                        <button x-show="filterStatus !== ''"
                                @click="resetFilter()"
                                class="mt-2 text-xs text-orange-500 hover:underline">
                            Reset filter
                        </button>
                    </div>
                </template>

            </div>

            {{-- PAGINATION --}}
            <div class="mt-6 max-w-4xl">
                {{ $contracts->links() }}
            </div>

        </div>
    </div>
</div>

@endsection