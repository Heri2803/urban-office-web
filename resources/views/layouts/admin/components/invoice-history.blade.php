{{-- resources/views/layouts/admin/components/invoice-history.blade.php --}}

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <h5 class="text-lg font-bold text-gray-800">Riwayat Pengajuan Settlement</h5>
</div>

{{-- Table History Request --}}
<div class="overflow-hidden bg-white border border-gray-200 rounded-xl shadow-sm mb-4">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                <tr>
                    <th scope="col" class="px-4 py-3 font-semibold">No</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Nomor Invoice</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Pengaju</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Catatan</th>
                    <th scope="col" class="px-4 py-3 font-semibold text-center">Bukti Transfer</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Status Pengajuan</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Diproses Oleh</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Alasan Penolakan</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Tanggal Update</th>
                    <th scope="col" class="px-4 py-3 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($settlementHistory as $index => $history)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-4 py-3 text-gray-900">
                        {{ $settlementHistory->firstItem() + $index }}
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900">
                        <div class="text-gray-900 font-semibold">{{ $history->invoice_number }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $history->transaction->order_id ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-900">
                        {{ $history->requestedBy->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 max-w-xs truncate" title="{{ $history->settlement_request_notes ?? '-' }}">
                        {{ $history->settlement_request_notes ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($history->settlement_payment_proof)
                            <button type="button" @click="openProofModal('{{ asset('storage/' . $history->settlement_payment_proof) }}', false)" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 rounded-md transition-colors">
                                <i class="fas fa-image"></i> Lihat
                            </button>
                        @else
                            <span class="text-xs text-gray-400 italic">Tidak ada</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($history->settlement_request_status === 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Approved
                            </span>
                        @elseif($history->settlement_request_status === 'rejected')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i> Rejected
                            </span>
                        @elseif($history->settlement_request_status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 animate-pulse">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs">
                        @if($history->processedBy)
                            <span class="font-medium text-gray-700">{{ $history->processedBy->name }}</span>
                        @else
                            <span class="text-gray-400 italic">Belum diproses</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-red-600 font-medium text-xs max-w-xs truncate" title="{{ $history->settlement_rejection_reason ?? '' }}">
                        {{ $history->settlement_rejection_reason ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        {{ $history->updated_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button @click="openDetailModal({{ $history->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 rounded-md transition-colors">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-4 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-3 text-gray-400">
                                <i class="fas fa-history text-xl"></i>
                            </div>
                            <p class="text-sm font-medium">Tidak ada data riwayat pengajuan</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($settlementHistory && $settlementHistory->hasPages())
<div class="mt-4">
    {{ $settlementHistory->appends(request()->query())->links() }}
</div>
@endif
