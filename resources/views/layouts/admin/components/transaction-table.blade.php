{{-- resources/views/admin/components/transaction-table.blade.php (MODIFIED - Tailwind CSS) --}}

<div class="bg-white shadow-lg rounded-lg p-4 md:p-6">
    @php
        // Asumsi nilai per_page diambil dari request. Ganti '5' jika ada default lain.
        $selectedRowsPerPage = request()->get('per_page', 5);
        $transactionsCount = count($transactions ?? []);

        // DUMMY DATA FOR PAGINATION (HARUS DIGANTI DENGAN DATA NYATA DARI PAGINATOR)
        $currentPage = 1;
        $totalItems = 50;
        $itemsPerPage = $selectedRowsPerPage;
        $totalPages = max(1, ceil($totalItems / $itemsPerPage));
        $startItem = (($currentPage - 1) * $itemsPerPage) + 1;
        $endItem = min($currentPage * $itemsPerPage, $totalItems);
        // END DUMMY DATA
    @endphp

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 space-y-2 md:space-y-0">
        <div>
            <h6 class="text-lg font-semibold text-gray-700">Transaksi {{ $title }}</h6>
        </div>
        <div class="flex items-center space-x-2 text-sm text-gray-600">
            <label for="rowsPerPageSelect">Tampilkan:</label>
            <select id="rowsPerPageSelect"
                    class="form-select bg-white border border-gray-300 rounded-md py-1 px-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                    style="width: auto;"
                    onchange="changeRowsPerPage('{{ $service }}', this.value)">
                <option value="5" @if($selectedRowsPerPage == 5) selected @endif>5</option>
                <option value="10" @if($selectedRowsPerPage == 10) selected @endif>10</option>
                <option value="25" @if($selectedRowsPerPage == 25) selected @endif>25</option>
                <option value="50" @if($selectedRowsPerPage == 50) selected @endif>50</option>
            </select>
            <span class="text-gray-600">per halaman</span>
        </div>
    </div>

    <div class="overflow-x-auto border border-gray-200 rounded-lg">
        <table class="min-w-full divide-y divide-gray-200" id="table-{{ $service }}">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap w-1/12">No</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap w-2/12">Nama Customer</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap w-2/12">Layanan</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap w-2/12">Tanggal Booking</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap w-2/12">Waktu Booking</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap w-2/12">Status Pembayaran</th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap w-2/12">Nominal</th>
                </tr>
            </thead>
            <tbody id="tbody-{{ $service }}" class="bg-white divide-y divide-gray-200">
                @forelse($transactions ?? [] as $index => $transaction)
                <tr class="hover:bg-blue-50 transition duration-150 ease-in-out">
                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                        <div class="flex items-center">
                            {{-- Tailwind equivalent of avatar-initial --}}
                            <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold text-sm mr-2 flex-shrink-0">
                                {{ strtoupper(substr($transaction['name'] ?? 'U', 0, 1)) }}
                            </div>
                            <span>{{ $transaction['name'] ?? 'Unknown' }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $transaction['service'] ?? $title }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $transaction['booking_date'] ?? date('Y-m-d') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $transaction['booking_time'] ?? '09:00 - 17:00' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                        @php
                            $status = $transaction['payment_status'] ?? 'pending';
                            $badgeClasses = match($status) {
                                'settlement' => 'bg-green-100 text-green-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'expired' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                            $statusText = ucfirst($status);
                        @endphp
                        <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full {{ $badgeClasses }}">
                            {{ $statusText }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 whitespace-nowrap text-right">
                        Rp {{ number_format($transaction['amount'] ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            {{-- Menggunakan ikon placeholder, Anda bisa ganti dengan ikon Blade/Heroicons --}}
                            <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0V6m0 7v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4"></path></svg>
                            <p class="mb-0 text-sm">Tidak ada transaksi untuk hari ini</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 border-t border-gray-200">
                <tr>
                    <td colspan="6" class="px-4 py-3 text-right text-sm font-bold text-gray-900">Total:</td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-gray-900">
                        @php
                            $total = collect($transactions ?? [])->sum('amount');
                        @endphp
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center mt-4 space-y-2 md:space-y-0">
        {{-- Informasi Data --}}
        <div class="text-sm text-gray-700 order-2 md:order-1">
            Menampilkan <span class="font-medium">{{ $transactionsCount > 0 ? $startItem : 0 }}</span> hingga <span class="font-medium">{{ $endItem }}</span> dari <span class="font-medium">{{ $totalItems }}</span> data
        </div>

        {{-- Navigasi Pagination --}}
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px order-1 md:order-2" aria-label="Pagination">
            {{-- Previous Button --}}
            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 {{ $currentPage <= 1 ? 'pointer-events-none opacity-50' : '' }}">
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
            </a>

            {{-- DUMMY PAGE LINKS - Ganti dengan perulangan pagination yang sebenarnya --}}
            @for ($i = 1; $i <= $totalPages; $i++)
                @if ($i <= 3 || $i == $totalPages || ($i >= $currentPage - 1 && $i <= $currentPage + 1))
                    <a href="#"
                        class="relative inline-flex items-center px-4 py-2 border text-sm font-medium
                        {{ $i == $currentPage ? 'z-10 bg-blue-600 text-white border-blue-600' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                        {{ $i }}
                    </a>
                @elseif ($i == 4 && $currentPage > 3)
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>
                @endif
            @endfor
            {{-- END DUMMY PAGE LINKS --}}

            {{-- Next Button --}}
            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 {{ $currentPage >= $totalPages ? 'pointer-events-none opacity-50' : '' }}">
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10l-3.293-3.293a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
            </a>
        </nav>
    </div>
</div>