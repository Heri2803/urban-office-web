{{-- resources/views/layouts/admin/components/modal-invoice-detail.blade.php --}}
<style>
    /* Default Mobile Styles (Compact/Small) */
    .compact-detail-modal .modal-header-title {
        font-size: 11.5px !important;
    }
    .compact-detail-modal td {
        font-size: 9.5px !important;
        line-height: 1.25 !important;
        padding-top: 1.5px !important;
        padding-bottom: 1.5px !important;
    }
    .compact-detail-modal h6 {
        font-size: 10px !important;
    }
    .compact-detail-modal p,
    .compact-detail-modal label,
    .compact-detail-modal textarea,
    .compact-detail-modal input,
    .compact-detail-modal .creator-info,
    .compact-detail-modal span {
        font-size: 9.5px !important;
    }
    .compact-detail-modal .status-badge {
        font-size: 9px !important;
        padding: 1.5px 5px !important;
    }
    .compact-detail-modal .request-badge {
        font-size: 8.5px !important;
        padding: 1px 4px !important;
    }
    .compact-detail-modal button,
    .compact-detail-modal a {
        font-size: 9px !important;
    }

    /* Tablet & Desktop Styles (Slightly larger than mobile) */
    @media (min-width: 640px) {
        .compact-detail-modal .modal-header-title {
            font-size: 13px !important;
        }
        .compact-detail-modal td {
            font-size: 11px !important;
            line-height: 1.4 !important;
            padding-top: 3.5px !important;
            padding-bottom: 3.5px !important;
        }
        .compact-detail-modal h6 {
            font-size: 11.5px !important;
        }
        .compact-detail-modal p,
        .compact-detail-modal label,
        .compact-detail-modal textarea,
        .compact-detail-modal input,
        .compact-detail-modal .creator-info,
        .compact-detail-modal span {
            font-size: 11px !important;
        }
        .compact-detail-modal .status-badge {
            font-size: 11px !important;
            padding: 2px 7px !important;
        }
        .compact-detail-modal .request-badge {
            font-size: 10px !important;
            padding: 1.5px 6px !important;
        }
        .compact-detail-modal button,
        .compact-detail-modal a {
            font-size: 10.5px !important;
        }
    }
</style>

<div x-show="showDetailModal"
     class="fixed inset-0 z-[1050] overflow-y-auto"
     style="z-index: 1050;"
     aria-labelledby="modal-title" role="dialog" aria-modal="true"
     x-cloak>
     
    {{-- Backdrop --}}
    <div x-show="showDetailModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 transition-opacity"
         style="background-color: rgba(255, 255, 255, 0.5); backdrop-filter: blur(2px);"
         @click="showDetailModal = false"></div>

    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        {{-- Modal Panel (using sm:max-w-lg for desktop/tablet) --}}
        <div x-show="showDetailModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 compact-detail-modal"
             @keydown.escape.window="showDetailModal = false"
             @click.self="showDetailModal = false">
             
            {{-- Header --}}
            <div class="bg-gray-50 px-3 py-1.5 border-b border-gray-200 flex justify-between items-center">
                <h5 class="font-bold text-gray-900 flex items-center gap-1.5 modal-header-title" id="modal-title">
                    <i class="fas fa-file-invoice text-blue-600"></i>
                    <span x-show="!loadingDetail && currentInvoice" x-text="'Detail Invoice: ' + currentInvoice?.invoice_number"></span>
                    <span x-show="loadingDetail">Memuat Data...</span>
                </h5>
                <button type="button" @click="showDetailModal = false" :disabled="loadingDetail" class="text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-md p-0.5 transition-colors focus:outline-none disabled:opacity-50">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="px-3.5 py-2">
                {{-- Loading State --}}
                <div x-show="loadingDetail" class="flex flex-col items-center justify-center py-4" x-cloak>
                    <svg class="animate-spin -ml-1 mr-2 h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-1.5 text-gray-500 font-medium">Sedang memuat data invoice...</p>
                </div>

                {{-- Error State --}}
                <div x-show="error && !loadingDetail" class="p-2 mb-2 text-red-800 rounded-md bg-red-50 border border-red-200 flex items-center" x-cloak>
                    <i class="fas fa-exclamation-circle mr-2 text-red-600 text-xs"></i>
                    <span x-text="error"></span>
                </div>

                {{-- Content --}}
                <template x-if="currentInvoice && !loadingDetail && !error">
                    <div>
                        {{-- Status Badge --}}
                        <div class="flex flex-col items-center gap-1 mb-2.5">
                            <span class="inline-flex items-center rounded-full font-semibold uppercase tracking-wider shadow-sm status-badge"
                                  :class="{
                                      'bg-amber-100 text-amber-800 border border-amber-200': currentInvoice.status === 'pending',
                                      'bg-green-100 text-green-800 border border-green-200': currentInvoice.status === 'settlement',
                                      'bg-red-100 text-red-800 border border-red-200': currentInvoice.status === 'expired' || currentInvoice.status === 'cancel',
                                      'bg-gray-100 text-gray-800 border border-gray-200': currentInvoice.status !== 'pending' && currentInvoice.status !== 'settlement' && currentInvoice.status !== 'expired' && currentInvoice.status !== 'cancel'
                                  }"
                                  x-text="currentInvoice.status?.charAt(0).toUpperCase() + currentInvoice.status?.slice(1)">
                            </span>
                            
                            {{-- Settlement Request Badge --}}
                            <div x-show="currentInvoice.settlement_request_status" x-cloak>
                                <span class="inline-flex items-center rounded-full font-semibold uppercase tracking-wider shadow-sm border request-badge"
                                      :class="{
                                          'bg-blue-50 text-blue-700 border-blue-200': currentInvoice.settlement_request_status === 'pending',
                                          'bg-green-50 text-green-700 border-green-200': currentInvoice.settlement_request_status === 'approved',
                                          'bg-red-50 text-red-700 border-red-200': currentInvoice.settlement_request_status === 'rejected',
                                      }">
                                    <i class="fas fa-clipboard-list mr-1"></i>
                                    Request Settlement: <span x-text="currentInvoice.settlement_request_status"></span>
                                </span>
                            </div>
                        </div>

                        {{-- Details Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2.5">
                            {{-- Info Customer --}}
                            <div class="bg-white border border-gray-100 rounded-lg p-2 shadow-sm">
                                <h6 class="font-bold text-gray-900 mb-1 border-b border-gray-100 pb-1 flex items-center gap-1.5">
                                    <i class="fas fa-user text-gray-400"></i> Info Customer
                                </h6>
                                <table class="w-full text-gray-900">
                                    <tbody class="divide-y divide-gray-50">
                                        <tr>
                                            <td class="text-gray-500 w-1/3">Order ID</td>
                                            <td class="font-medium w-2/3">: <span x-text="currentInvoice.transaction?.order_id || '-'"></span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-500">Nama Lengkap</td>
                                            <td class="font-medium">: <span x-text="currentInvoice.transaction?.nama_lengkap || '-'"></span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-500">Perusahaan</td>
                                            <td class="font-medium">: <span x-text="currentInvoice.transaction?.company_name || '-'"></span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-500">Email</td>
                                            <td class="font-medium">: <span x-text="currentInvoice.transaction?.email || '-'"></span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-500">Telepon</td>
                                            <td class="font-medium">: <span x-text="currentInvoice.transaction?.phone || '-'"></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- Info Layanan --}}
                            <div class="bg-white border border-gray-100 rounded-lg p-2 shadow-sm">
                                <h6 class="font-bold text-gray-900 mb-1 border-b border-gray-100 pb-1 flex items-center gap-1.5">
                                    <i class="fas fa-building text-gray-400"></i> Detail Layanan
                                </h6>
                                <table class="w-full text-gray-900">
                                    <tbody class="divide-y divide-gray-50">
                                        <tr>
                                            <td class="text-gray-500 w-1/3">Tipe Ruangan</td>
                                            <td class="font-medium w-2/3">: <span x-text="currentInvoice.transaction?.room_type || '-'"></span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-500">Lokasi</td>
                                            <td class="font-medium">: <span x-text="currentInvoice.transaction?.location?.name || '-'"></span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-500">Tanggal Mulai</td>
                                            <td class="font-medium">: <span x-text="formatDate(currentInvoice.transaction?.booking_date)"></span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-500">Jam Mulai</td>
                                            <td class="font-medium">: <span x-text="currentInvoice.transaction?.start_time || '-'"></span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-500">Total Sewa</td>
                                            <td class="font-medium">: <strong x-text="formatCurrency(currentInvoice.transaction?.gross_amount || 0)"></strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-500">Deposit</td>
                                            <td class="font-medium">: <span x-text="formatCurrency(currentInvoice.transaction?.deposit || 0)"></span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-500">Tanggal Invoice</td>
                                            <td class="font-medium">: <span x-text="formatDate(currentInvoice.created_at)"></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Approval Info --}}
                        <div x-show="currentInvoice.settlement_request_status" class="mb-2.5 bg-blue-50/50 border border-blue-100 rounded-lg p-2 shadow-sm text-gray-900" x-cloak>
                            <h6 class="font-bold text-gray-900 mb-1 border-b border-blue-100 pb-1 flex items-center gap-1.5">
                                <i class="fas fa-clipboard-check text-blue-600"></i> Info Pengajuan Settlement
                            </h6>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <div>
                                    <p class="text-gray-500">Status Pengajuan: 
                                        <span class="font-semibold uppercase" 
                                              :class="{
                                                  'text-blue-600': currentInvoice.settlement_request_status === 'pending',
                                                  'text-green-600': currentInvoice.settlement_request_status === 'approved',
                                                  'text-red-600': currentInvoice.settlement_request_status === 'rejected',
                                              }"
                                              x-text="currentInvoice.settlement_request_status">
                                        </span>
                                    </p>
                                    <p class="text-gray-500 mt-0.5">Diproses Oleh: <span class="font-medium text-gray-900" x-text="currentInvoice.processed_by?.name || '-'"></span></p>
                                    <p class="text-gray-500 mt-0.5" x-show="currentInvoice.settlement_request_notes">Catatan Admin: <span class="font-medium text-gray-900" x-text="currentInvoice.settlement_request_notes"></span></p>
                                    <p class="text-red-600 mt-0.5 font-medium" x-show="currentInvoice.settlement_rejection_reason">Alasan Penolakan: <span x-text="currentInvoice.settlement_rejection_reason"></span></p>
                                </div>
                                <div class="flex flex-col justify-center">
                                    <p class="text-gray-500 font-medium mb-0.5">Bukti Transfer:</p>
                                    <template x-if="currentInvoice.settlement_payment_proof">
                                        <div class="mt-0.5">
                                            <button type="button" @click="openProofModal('/storage/' + currentInvoice.settlement_payment_proof, true)" class="inline-flex items-center gap-1 px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium shadow-sm transition-colors w-max">
                                                <i class="fas fa-image text-[10px]"></i> Lihat Bukti Bayar
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="!currentInvoice.settlement_payment_proof">
                                        <span class="text-gray-400 italic">Tidak ada bukti upload</span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Form Pengajuan Settlement (Toggleable) --}}
                        <div x-show="showRequestForm" class="mb-2.5 p-2 border border-blue-200 bg-blue-50 rounded-lg" x-cloak>
                            <h6 class="font-bold text-blue-900 mb-1"><i class="fas fa-paper-plane mr-1"></i> Form Pengajuan Settlement</h6>
                            <form :action="'/admin/invoices/' + currentInvoice.id + '/request-settlement'" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-1.5">
                                    <label class="block font-semibold text-gray-700 mb-0.5">Bukti Transfer (Optional):</label>
                                    <input type="file" name="payment_proof" class="w-full border-gray-300 rounded-md bg-white p-0.5">
                                </div>
                                <div class="mb-1.5">
                                    <label class="block font-semibold text-gray-700 mb-0.5">Catatan / Keterangan:</label>
                                    <textarea name="notes" rows="2" class="w-full border-gray-300 rounded-md bg-white p-1" placeholder="Catatan transaksi..."></textarea>
                                </div>
                                <div class="flex gap-1.5 justify-end">
                                    <button type="button" @click="showRequestForm = false" class="px-2 py-0.5 bg-gray-200 text-gray-700 rounded-md font-semibold hover:bg-gray-300 transition-colors">Batal</button>
                                    <button type="submit" class="px-2 py-0.5 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors">Kirim Pengajuan</button>
                                </div>
                            </form>
                        </div>



                        {{-- Notes --}}
                        <div x-show="currentInvoice.transaction?.notes" class="mb-2.5 bg-yellow-50 border border-yellow-100 rounded-lg p-2 shadow-sm" x-cloak>
                            <h6 class="font-bold text-yellow-800 mb-0.5 flex items-center gap-1.5">
                                <i class="fas fa-sticky-note text-yellow-500"></i> Catatan:
                            </h6>
                            <p class="text-yellow-900" x-text="currentInvoice.transaction?.notes"></p>
                        </div>

                        {{-- Creator Info --}}
                        <div class="mt-2 text-right creator-info">
                            <p class="text-gray-500">
                                Dibuat oleh: <span class="font-medium text-gray-700" x-text="currentInvoice.creator?.name || 'System'"></span>
                                pada <span class="font-medium text-gray-700" x-text="formatDate(currentInvoice.created_at)"></span>
                            </p>
                        </div>
                    </div>
                </template>
            </div>
            
            {{-- Footer / Actions --}}
            <div class="bg-gray-50 px-3 py-1.5 border-t border-gray-200 flex flex-col sm:flex-row justify-end items-center gap-1.5">
                <template x-if="currentInvoice && !loadingDetail">
                    <div class="flex flex-wrap gap-1.5 w-full sm:w-auto">
                        {{-- Tombol Ajukan Settlement (Admin Biasa, invoice MANUAL, status pending, request status bukan pending/approved) --}}
                        <template x-if="currentInvoice.invoice_number.startsWith('MANUAL') && currentInvoice.status === 'pending' && currentInvoice.settlement_request_status !== 'pending' && currentInvoice.settlement_request_status !== 'approved' && '{{ auth()->user()->role }}' !== 'finance'">
                            <button type="button" @click="showRequestForm = !showRequestForm" class="flex-[1] sm:flex-none inline-flex justify-center items-center gap-1 px-2 py-1 font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow-sm transition-colors">
                                <i class="fas fa-paper-plane"></i> Ajukan Settlement
                            </button>
                        </template>

                        {{-- Tombol Approve & Reject (Finance, request status pending) --}}
                        <template x-if="'{{ auth()->user()->role }}' === 'finance' && currentInvoice.settlement_request_status === 'pending'">
                            <div class="flex gap-1.5 w-full sm:w-auto">
                                <button type="button" @click="triggerApprove(currentInvoice.id, currentInvoice.invoice_number, true)" class="inline-flex justify-center items-center gap-1 px-2 py-1 font-medium text-white bg-green-600 hover:bg-green-700 rounded-md shadow-sm transition-colors">
                                    <i class="fas fa-check"></i> Setujui
                                </button>
                                <button type="button" @click="triggerReject(currentInvoice.id, currentInvoice.invoice_number, true)" class="inline-flex justify-center items-center gap-1 px-2 py-1 font-medium text-white bg-red-500 hover:bg-red-600 rounded-md shadow-sm transition-colors">
                                    <i class="fas fa-times"></i> Tolak
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
                <button type="button" @click="showDetailModal = false" :disabled="loadingDetail" class="w-full sm:w-auto px-2.5 py-1 bg-white border border-gray-300 rounded-md shadow-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>