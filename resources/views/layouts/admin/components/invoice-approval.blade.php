{{-- resources/views/layouts/admin/components/invoice-approval.blade.php --}}

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <h5 class="text-lg font-bold text-gray-800">Persetujuan Perubahan Status (Settlement)</h5>
</div>

{{-- Table Approval Request --}}
<div class="overflow-hidden bg-white border border-gray-200 rounded-xl shadow-sm mb-4">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                <tr>
                    <th scope="col" class="px-4 py-3 font-semibold">No</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Nomor Invoice</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Pengaju</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Catatan Pengaju</th>
                    <th scope="col" class="px-4 py-3 font-semibold text-center">Bukti Transfer</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Status Pengajuan</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Diproses Oleh</th>
                    <th scope="col" class="px-4 py-3 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($settlementApprovals as $index => $approval)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-4 py-3 text-gray-900">
                        {{ $settlementApprovals->firstItem() + $index }}
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900">
                        <div class="text-gray-900 font-semibold">{{ $approval->invoice_number }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $approval->transaction->order_id ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-900">
                        {{ $approval->requestedBy->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 max-w-xs truncate" title="{{ $approval->settlement_request_notes ?? '-' }}">
                        {{ $approval->settlement_request_notes ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($approval->settlement_payment_proof)
                            <button type="button" @click="openProofModal('{{ asset('storage/' . $approval->settlement_payment_proof) }}', false)" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 rounded-md transition-colors">
                                <i class="fas fa-image"></i> Lihat
                            </button>
                        @else
                            <span class="text-xs text-gray-400 italic">Tidak ada</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($approval->settlement_request_status === 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Approved
                            </span>
                        @elseif($approval->settlement_request_status === 'rejected')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800" title="Alasan: {{ $approval->settlement_rejection_reason }}">
                                <i class="fas fa-times-circle mr-1"></i> Rejected
                            </span>
                        @elseif($approval->settlement_request_status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 animate-pulse">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs">
                        @if($approval->processedBy)
                            <span class="font-medium text-gray-700">{{ $approval->processedBy->name }}</span>
                        @else
                            <span class="text-gray-400 italic">Belum diproses</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2">
                            <button @click="openDetailModal({{ $approval->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 rounded-md transition-colors">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                            
                            @if($approval->settlement_request_status === 'pending')
                                <button type="button" @click="triggerApprove({{ $approval->id }}, '{{ $approval->invoice_number }}', false)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 border border-green-700 rounded-md transition-colors">
                                    <i class="fas fa-check"></i> Setujui
                                </button>
                                
                                <button type="button" @click="triggerReject({{ $approval->id }}, '{{ $approval->invoice_number }}', false)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 border border-red-700 rounded-md transition-colors">
                                    <i class="fas fa-times"></i> Tolak
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-3 text-gray-400">
                                <i class="fas fa-clipboard-check text-xl"></i>
                            </div>
                            <p class="text-sm font-medium">Tidak ada pengajuan persetujuan pending</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($settlementApprovals && $settlementApprovals->hasPages())
<div class="mt-4">
    {{ $settlementApprovals->appends(request()->query())->links() }}
</div>
@endif
