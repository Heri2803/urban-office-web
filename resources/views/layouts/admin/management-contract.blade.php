@extends('layouts.admin')
@section('title', 'Manajemen Kontrak')

@section('content')
<div class="space-y-6" x-data="contractManager()">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Kontrak</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola pembuatan dan pengunduhan draft Kontrak untuk layanan office</p>
        </div>
    </div>

    {{-- Tabs Configuration (Alpine) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px px-2 sm:px-6 space-x-4 sm:space-x-8 overflow-x-auto" aria-label="Tabs">
                <button 
                    @click="activeTab = 'vo'"
                    :class="{'border-orange-500 text-orange-600': activeTab === 'vo', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'vo'}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m4-6h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Virtual Office
                </button>
                <button 
                    @click="activeTab = 'po'"
                    :class="{'border-orange-500 text-orange-600': activeTab === 'po', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'po'}"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Private Office
                </button>
            </nav>
        </div>

        <div class="p-0 sm:p-2">
            {{-- Tab Virtual Office --}}
            <div x-show="activeTab === 'vo'" x-transition>
                @include('layouts.admin.components.contract-vo-list', [
                    'draftContracts' => $draftContracts,
                    'activeContracts' => $activeContracts
                ])
            </div>

            {{-- Tab Private Office --}}
            <div x-show="activeTab === 'po'" x-transition x-cloak>
                @include('layouts.admin.components.contract-po-list')
            </div>
        </div>
    </div>
    
    {{-- Include Modal Contract Generate (from the previously created component) --}}
    @include('layouts.admin.components.modal-contract-generate')
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('contractManager', () => ({
            activeTab: '{{ $activeTab ?? "vo" }}',
            showContractModal: false,
            contractTransactionId: null,
            contractDate: '',

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
            
            generateContract() {
                if(!this.contractDate) {
                    alert('Tanggal kontrak wajib diisi');
                    return;
                }
                let url = `/admin/contracts/virtual-office/${this.contractTransactionId}/pdf?contract_date=${this.contractDate}`;
                window.open(url, '_blank');
                this.closeContractModal();
            },

            /**
             * Tandai kontrak sebagai expired
             */
            async markAsExpired(contractId) {
                if (!confirm('Tandai kontrak ini sebagai EXPIRED?\n\nStatus akan berubah dan tidak bisa dikembalikan ke Aktif.')) {
                    return;
                }
                
                await this.updateContractStatus(contractId, 'expired');
            },
            
            /**
             * Perpanjang kontrak (dari expired ke renewed)
             */
            async renewContract(contractId) {
                if (!confirm('Perpanjang kontrak ini?\n\nStatus akan berubah menjadi Diperpanjang.')) {
                    return;
                }
                
                await this.updateContractStatus(contractId, 'renewed');
            },
            
            /**
             * Terminasi kontrak
             */
            async terminateContract(contractId) {
                const reason = prompt('Alasan terminasi:');
                if (reason === null) return; // user cancel
                
                if (!reason.trim()) {
                    alert('Alasan terminasi wajib diisi');
                    return;
                }
                
                await this.updateContractStatus(contractId, 'terminated', { reason });
            },

            /**
             * Terminasi Addendum khusus
             */
            async terminateAddendum(addendumId) {
                const reason = prompt('Alasan pembatalan Addendum ini:');
                if (reason === null) return; // user cancel
                
                if (!reason.trim()) {
                    alert('Alasan pembatalan wajib diisi');
                    return;
                }
                
                try {
                    const response = await fetch(`/admin/contracts/addendums/${addendumId}/terminate`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ reason }),
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal membatalkan addendum');
                    }
                    
                    this.showToast(data.message, 'success');
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                    
                } catch (error) {
                    console.error('Error terminating addendum:', error);
                    this.showToast(error.message || 'Terjadi kesalahan', 'error');
                }
            },
            
            /**
             * Core: Update status kontrak via AJAX
             */
            async updateContractStatus(contractId, status, extraData = {}) {
                const row = document.querySelector(`tr[data-contract-id="${contractId}"]`);
                
                try {
                    // Tampilkan loading di row
                    if (row) {
                        row.style.opacity = '0.5';
                        row.style.pointerEvents = 'none';
                    }
                    
                    const response = await fetch(`/admin/contracts/${contractId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ status, ...extraData }),
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal update status');
                    }
                    
                    // Tampilkan toast sukses
                    this.showToast(data.message, 'success');
                    
                    // Refresh halaman setelah 1 detik (agar user lihat perubahan)
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                    
                } catch (error) {
                    console.error('Error updating contract status:', error);
                    this.showToast(error.message || 'Terjadi kesalahan', 'error');
                    
                    // Kembalikan row ke normal
                    if (row) {
                        row.style.opacity = '1';
                        row.style.pointerEvents = 'auto';
                    }
                }
            },
            
            /**
             * Toast notification
             */
            showToast(message, type = 'success') {
                // Implementasi sederhana, bisa diganti dengan library seperti SweetAlert2
                const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
                const toast = document.createElement('div');
                toast.className = `fixed top-20 right-4 ${bgColor} text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity`;
                toast.textContent = message;
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            },
        }));
    });
</script>
@endpush