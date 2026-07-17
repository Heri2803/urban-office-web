{{-- resources/views/layouts/admin/components/invoice-generate.blade.php --}}

{{-- 
    FIX: Satu form tunggal di luar table untuk menghindari N form di dalam loop.
    Transaction ID akan di-set via JavaScript sebelum form di-submit.
--}}
<form id="generateInvoiceForm"
      method="POST"
      action="{{ route('admin.invoices.store') }}"
      style="display: none;">
    @csrf
    <input type="hidden" name="transaction_id" id="selectedTransactionId">
</form>

<div class="generate-invoice-section">
    <h5 class="text-lg font-bold text-gray-800 mb-6">Pilih Transaksi untuk Generate Invoice</h5>

    {{-- Search Form --}}
    <div class="mb-6 bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('admin.invoices.index') }}" class="flex flex-col md:flex-row gap-3">
            <input type="hidden" name="tab" value="generate">
            <div class="flex-grow">
                <input type="text" name="search_transaction" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       value="{{ request('search_transaction') }}"
                       placeholder="Cari Order ID, Nama Pembeli, atau Perusahaan...">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex justify-center items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm whitespace-nowrap">
                    <i class="fas fa-search"></i> Cari
                </button>
                <a href="{{ route('admin.invoices.index', ['tab' => 'generate']) }}" class="inline-flex justify-center items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors whitespace-nowrap">
                    <i class="fas fa-sync"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Table Transaksi --}}
    <div class="overflow-hidden bg-white border border-gray-200 rounded-xl shadow-sm mb-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-4 py-3 font-semibold">No</th>
                        <th scope="col" class="px-4 py-3 font-semibold">Order ID</th>
                        <th scope="col" class="px-4 py-3 font-semibold">Pembeli</th>
                        <th scope="col" class="px-4 py-3 font-semibold">Perusahaan</th>
                        <th scope="col" class="px-4 py-3 font-semibold">Tipe Ruangan</th>
                        <th scope="col" class="px-4 py-3 font-semibold text-right">Total</th>
                        <th scope="col" class="px-4 py-3 font-semibold">Status</th>
                        <th scope="col" class="px-4 py-3 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transactions as $index => $transaction)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-3 text-gray-900">{{ $transactions->firstItem() + $index }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $transaction->order_id }}</td>
                        <td class="px-4 py-3 text-gray-900">{{ $transaction->nama_lengkap }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $transaction->company_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $transaction->room_type }}</td>
                        <td class="px-4 py-3 text-right text-gray-900 font-medium">Rp {{ number_format($transaction->gross_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            @if($transaction->status == 'settlement')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Settlement</span>
                            @elseif($transaction->status == 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Pending</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ $transaction->status }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            {{--
                                FIX: Cek apakah invoice sudah pernah di-generate untuk transaksi ini.
                                Pastikan controller sudah eager load: Transaction::with('invoice')->paginate()
                            --}}
                            @if($transaction->invoice)
                                {{-- Invoice sudah ada: tampilkan tombol lihat saja --}}
                                <a href="{{ route('admin.invoices.show', $transaction->invoice) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 text-xs font-medium rounded-md transition-colors whitespace-nowrap">
                                    <i class="fas fa-eye"></i> Lihat Invoice
                                </a>
                            @else
                                {{-- FIX: Gunakan onclick ke fungsi JS, bukan form per-row --}}
                                <button type="button"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-600 border border-green-200 text-xs font-medium rounded-md transition-colors whitespace-nowrap"
                                        onclick="confirmGenerate({{ $transaction->id }}, '{{ $transaction->order_id }}')">
                                    <i class="fas fa-file-invoice"></i> Generate
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center mb-3">
                                    <i class="fas fa-info-circle text-xl"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-600">Tidak ada transaksi yang tersedia.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $transactions->appends(request()->query())->links() }}
    </div>
</div>

<script>
    /**
     * FIX: Satu fungsi JS terpusat menggantikan N buah onclick="return confirm()" di tiap row.
     * Form tunggal #generateInvoiceForm di-submit setelah user konfirmasi.
     */
    function confirmGenerate(transactionId, orderId) {
        if (!confirm(`Generate invoice untuk transaksi ${orderId}?`)) return;
        document.getElementById('selectedTransactionId').value = transactionId;
        document.getElementById('generateInvoiceForm').submit();
    }
</script>