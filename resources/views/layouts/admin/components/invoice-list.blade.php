{{-- resources/views/layouts/admin/components/invoice-list.blade.php --}}

{{-- Header dengan tombol input manual --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <h5 class="text-lg font-bold text-gray-800">Daftar Invoice</h5>
    <button @click="openManualModal" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
        <i class="fas fa-hand-pointer"></i> Input Invoice Manual
    </button>
</div>

{{-- Filter Section --}}
<div class="mb-6 bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
    <form method="GET" action="{{ route('admin.invoices.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3">
        <div class="md:col-span-2">
            <select name="status" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="pending"    {{ request('status') == 'pending'    ? 'selected' : '' }}>Pending</option>
                <option value="settlement" {{ request('status') == 'settlement' ? 'selected' : '' }}>Settlement</option>
                <option value="expired"    {{ request('status') == 'expired'    ? 'selected' : '' }}>Expired</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <input type="date" name="date_from" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ request('date_from') }}" placeholder="Dari Tanggal">
        </div>
        <div class="md:col-span-2">
            <input type="date" name="date_to" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ request('date_to') }}" placeholder="Sampai Tanggal">
        </div>
        <div class="md:col-span-3">
            <input type="text" name="search" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   value="{{ request('search') }}"
                   placeholder="Cari invoice, order ID, nama...">
        </div>
        <div class="md:col-span-3 flex gap-2">
            <button type="submit" class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="{{ route('admin.invoices.index') }}" class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-sync"></i> Reset
            </a>
        </div>
    </form>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    {{-- Total Invoice --}}
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 text-white shadow-md relative overflow-hidden group">
        <div class="relative z-10">
            <h3 class="text-3xl font-bold mb-1">{{ $stats['total'] ?? 0 }}</h3>
            <p class="text-blue-100 text-sm font-medium">Total Invoice</p>
        </div>
        <div class="absolute -right-2 -top-2 text-blue-400 opacity-50 group-hover:scale-110 group-hover:opacity-70 transition-all duration-300">
            <i class="fas fa-file-invoice text-7xl"></i>
        </div>
    </div>

    {{-- Pending --}}
    <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-5 text-white shadow-md relative overflow-hidden group">
        <div class="relative z-10">
            <h3 class="text-3xl font-bold mb-1">{{ $stats['pending'] ?? 0 }}</h3>
            <p class="text-amber-100 text-sm font-medium">Pending</p>
        </div>
        <div class="absolute -right-2 -top-2 text-amber-400 opacity-50 group-hover:scale-110 group-hover:opacity-70 transition-all duration-300">
            <i class="fas fa-clock text-7xl"></i>
        </div>
    </div>

    {{-- Settlement --}}
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-5 text-white shadow-md relative overflow-hidden group">
        <div class="relative z-10">
            <h3 class="text-3xl font-bold mb-1">{{ $stats['settlement'] ?? 0 }}</h3>
            <p class="text-green-100 text-sm font-medium">Settlement</p>
        </div>
        <div class="absolute -right-2 -top-2 text-green-400 opacity-50 group-hover:scale-110 group-hover:opacity-70 transition-all duration-300">
            <i class="fas fa-check-circle text-7xl"></i>
        </div>
    </div>

    {{-- Expired --}}
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-5 text-white shadow-md relative overflow-hidden group">
        <div class="relative z-10">
            <h3 class="text-3xl font-bold mb-1">{{ $stats['expired'] ?? 0 }}</h3>
            <p class="text-red-100 text-sm font-medium">Expired</p>
        </div>
        <div class="absolute -right-2 -top-2 text-red-400 opacity-50 group-hover:scale-110 group-hover:opacity-70 transition-all duration-300">
            <i class="fas fa-times-circle text-7xl"></i>
        </div>
    </div>
</div>

{{-- Table Invoice --}}
<div class="overflow-hidden bg-white border border-gray-200 rounded-xl shadow-sm mb-4">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                <tr>
                    <th scope="col" class="px-4 py-3 font-semibold">No</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Nomor Invoice</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Order ID</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Pembeli</th>
                    <th scope="col" class="px-4 py-3 font-semibold text-right">Total</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Status</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Tanggal</th>
                    <th scope="col" class="px-4 py-3 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($invoices as $index => $invoice)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-4 py-3 text-gray-900">{{ $invoices->firstItem() + $index }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $invoice->invoice_number }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $invoice->transaction->order_id ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="text-gray-900 font-medium">{{ $invoice->transaction->nama_lengkap ?? '-' }}</div>
                        @if($invoice->transaction?->company_name)
                            <div class="text-xs text-gray-500 mt-0.5">{{ $invoice->transaction->company_name }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right text-gray-900 font-medium">
                        Rp {{ number_format($invoice->transaction->gross_amount ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3">
                        {{-- Make sure $invoice->status_badge output uses Tailwind classes if generated from backend! 
                             Assuming we might need to overwrite these in UI or if backend generates bootstrap badges 
                        --}}
                        @if($invoice->status === 'settlement')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Settlement</span>
                        @elseif($invoice->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Pending</span>
                        @elseif($invoice->status === 'expired')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Expired</span>
                        @else
                            {!! $invoice->status_badge !!}
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $invoice->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            <button @click="openDetailModal({{ $invoice->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 rounded-md transition-colors" title="Lihat">
                                <i class="fas fa-eye"></i> Lihat
                            </button>
                            <a href="{{ route('admin.invoices.pdf', $invoice) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-200 hover:bg-red-100 rounded-md transition-colors" title="PDF">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                            <a href="{{ route('admin.invoices.whatsapp', $invoice) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-200 hover:bg-green-100 rounded-md transition-colors" title="WhatsApp">
                                <i class="fab fa-whatsapp"></i> WA
                            </a>
                            @if($invoice->transaction?->room_type === 'Virtual Office' && $invoice->transaction?->status === 'settlement')
                            <button @click="openContractModal({{ $invoice->transaction->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-purple-700 bg-purple-50 border border-purple-200 hover:bg-purple-100 rounded-md transition-colors" title="Buat Kontrak">
                                <i class="fas fa-file-signature"></i> Kontrak
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
                                <i class="fas fa-file-invoice text-xl"></i>
                            </div>
                            <p class="text-sm font-medium">Tidak ada data invoice</p>
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
    {{ $invoices->appends(request()->query())->links() }}
</div>
