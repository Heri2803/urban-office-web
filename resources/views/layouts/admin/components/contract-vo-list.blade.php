{{--
    contract-vo-list.blade.php
    Komponen daftar kontrak Virtual Office (aktif & draft)
    - Search fix: setiap form menyimpan state form lain sebagai hidden input
    - Teks dipendekkan + truncated, detail bisa dibuka via popup
    - Section addendum dihapus (ada di halaman Addendum Management)
--}}

{{-- ============================================================
     POPUP MODAL — Detail Kontrak / Customer
     Dipanggil dari tombol info di tiap baris tabel
============================================================ --}}
<div
    x-data="contractDetailPopup()"
    @open-contract-detail.window="open($event.detail)"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-[60] flex items-center justify-center p-4"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="close()"></div>

    {{-- Panel --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg border border-gray-100 z-10"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800" x-text="title"></h3>
            <button @click="close()" class="p-1.5 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5 space-y-3 max-h-[70vh] overflow-y-auto">
            <template x-for="item in rows" :key="item.label">
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-400 w-36 shrink-0 pt-0.5" x-text="item.label"></span>
                    <span class="text-sm text-gray-800 break-all" x-text="item.value"></span>
                </div>
            </template>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-3 border-t border-gray-100 flex justify-end">
            <button @click="close()"
                    class="px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition font-medium">
                Tutup
            </button>
        </div>
    </div>
</div>

<div class="px-4 sm:px-6 py-4 space-y-8">

    {{-- ============================================================
         SECTION 1: KONTRAK AKTIF / TERARSIP
    ============================================================ --}}
    <div>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
            <h2 class="text-base font-semibold text-gray-800">Kontrak Aktif / Terarsip</h2>

            {{-- Search Form — Aktif --}}
            <form action="{{ route('admin.contracts.index') }}" method="GET" class="flex flex-wrap gap-2">
                {{-- Preserve state --}}
                <input type="hidden" name="tab"          value="vo">
                <input type="hidden" name="search_draft" value="{{ request('search_draft') }}">
                <input type="hidden" name="draft_page"   value="{{ request('draft_page') }}">

                <select name="status_active"
                        class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none bg-white"
                        onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="active"     {{ request('status_active') == 'active'     ? 'selected' : '' }}>Active</option>
                    <option value="expired"    {{ request('status_active') == 'expired'    ? 'selected' : '' }}>Expired</option>
                    <option value="terminated" {{ request('status_active') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                    <option value="renewed"    {{ request('status_active') == 'renewed'    ? 'selected' : '' }}>Renewed</option>
                </select>

                <div class="flex gap-1">
                    <input type="text" name="search_active" value="{{ request('search_active') }}"
                           placeholder="No. kontrak, invoice, nama, order ID..."
                           class="text-sm border border-gray-200 rounded-lg px-3 py-2 w-60 focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none">
                    <button type="submit"
                            class="px-3 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                    @if(request('search_active') || request('status_active'))
                    <a href="{{ route('admin.contracts.index', ['tab' => 'vo']) }}"
                       class="px-3 py-2 bg-gray-100 text-gray-500 rounded-lg hover:bg-gray-200 transition text-xs flex items-center">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto border border-gray-200 rounded-xl">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-600 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3">No. Kontrak</th>
                        <th class="px-4 py-3">Pelanggan</th>
                        <th class="px-4 py-3">Lokasi</th>
                        <th class="px-4 py-3">Tgl Kontrak</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($activeContracts as $contract)
                    @php
                        $contractNo  = $contract->contract_number ?? '-';
                        $orderId     = $contract->transaction->order_id ?? '-';
                        $namaLengkap = $contract->transaction->nama_lengkap ?? '-';
                        $company     = $contract->transaction->company_name ?? '-';
                        $lokasi      = $contract->transaction->location->name ?? '-';
                        $alamat      = $contract->transaction->location->address ?? '-';
                    @endphp
                    <tr class="bg-white hover:bg-gray-50 transition" data-contract-id="{{ $contract->id }}">

                        {{-- No. Kontrak --}}
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900 text-xs leading-tight max-w-[160px]">
                                <span class="block truncate" title="{{ $contractNo }}">
                                    {{ Str::limit($contractNo, 28, '…') }}
                                </span>
                            </div>
                            <div class="text-[11px] text-gray-400 font-mono mt-0.5 truncate max-w-[140px]" title="{{ $orderId }}">
                                {{ Str::limit($orderId, 22, '…') }}
                            </div>
                        </td>

                        {{-- Pelanggan --}}
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900 text-xs truncate max-w-[120px]" title="{{ $namaLengkap }}">
                                {{ Str::limit($namaLengkap, 18, '…') }}
                            </div>
                            <div class="text-[11px] text-gray-400 truncate max-w-[120px]" title="{{ $company }}">
                                {{ Str::limit($company, 18, '…') }}
                            </div>
                        </td>

                        {{-- Lokasi --}}
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800 text-xs truncate max-w-[100px]" title="{{ $lokasi }}">
                                {{ Str::limit($lokasi, 14, '…') }}
                            </div>
                            <div class="text-[11px] text-gray-400 truncate max-w-[100px]" title="{{ $alamat }}">
                                {{ Str::limit($alamat, 16, '…') }}
                            </div>
                        </td>

                        {{-- Tanggal --}}
                        <td class="px-4 py-3 text-xs text-gray-600 whitespace-nowrap">
                            {{ $contract->formatted_contract_date }}
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3">
                            {!! $contract->status_badge !!}
                            @if($contract->status == 'active' && $contract->end_date)
                                <div class="text-[11px] text-blue-500 mt-1">
                                    Sisa {{ $contract->remaining_days ?? 0 }}h
                                </div>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">

                                {{-- Info Detail Popup --}}
                                <button
                                    @click="$dispatch('open-contract-detail', {
                                        title: 'Detail Kontrak',
                                        rows: [
                                            { label: 'No. Kontrak',   value: {{ json_encode($contractNo) }} },
                                            { label: 'Order ID',       value: {{ json_encode($orderId) }} },
                                            { label: 'Pelanggan',      value: {{ json_encode($namaLengkap) }} },
                                            { label: 'Perusahaan',     value: {{ json_encode($company) }} },
                                            { label: 'Lokasi',         value: {{ json_encode($lokasi) }} },
                                            { label: 'Alamat',         value: {{ json_encode($alamat) }} },
                                            { label: 'Tgl Kontrak',    value: {{ json_encode($contract->formatted_contract_date) }} },
                                            { label: 'Berlaku s/d',    value: {{ json_encode($contract->end_date?->format('d/m/Y') ?? '-') }} },
                                            { label: 'Status',         value: {{ json_encode(ucfirst($contract->status)) }} },
                                            @if($contract->termination_reason)
                                            { label: 'Alasan Terminasi', value: {{ json_encode($contract->termination_reason) }} },
                                            @endif
                                        ]
                                    })"
                                    class="p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-700 rounded-lg transition"
                                    title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>

                                {{-- Kirim WA Perpanjangan --}}
                                @if($contract->wa_link !== '#')
                                <a href="{{ $contract->wa_link }}" target="_blank"
                                   class="p-1.5 text-green-500 hover:bg-green-50 hover:text-green-600 rounded-lg transition" 
                                   title="Kirim Notifikasi WA">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.031 0C5.385 0 0 5.388 0 12.036c0 2.124.553 4.195 1.604 6.015L.17 24l6.096-1.597C8.016 23.36 10.007 23.94 12.025 23.94h.006C18.667 23.94 24 18.549 24 11.905 24 8.683 22.753 5.65 20.475 3.375 18.201 1.099 15.192 0 12.031 0zm.006 21.942c-1.785 0-3.535-.48-5.066-1.388l-.364-.215-3.766.986.997-3.667-.236-.376C2.65 15.753 2.016 13.882 2.016 11.904c0-5.541 4.512-10.057 10.052-10.057 2.684 0 5.205 1.047 7.1 2.943 1.895 1.896 2.939 4.417 2.939 7.102 0 5.543-4.512 10.05-10.063 10.05H12.03zm5.518-7.551c-.302-.153-1.792-.888-2.069-.99-.276-.102-.477-.153-.679.153-.201.305-.78 1.002-.955 1.206-.176.204-.352.229-.654.076-.302-.153-1.28-.472-2.438-1.506-.902-.806-1.509-1.802-1.686-2.107-.176-.305-.019-.47.133-.622.136-.135.302-.354.453-.531.151-.178.201-.305.302-.508.101-.203.051-.382-.025-.535-.075-.153-.679-1.637-.93-2.243-.243-.591-.491-.512-.678-.521-.176-.008-.377-.008-.579-.008s-.528.076-.805.381c-.276.305-1.055 1.03-1.055 2.511 0 1.482 1.08 2.915 1.231 3.118.151.204 2.127 3.245 5.15 4.549.718.31 1.28.495 1.716.634.721.23 1.378.197 1.894.12.576-.086 1.792-.733 2.043-1.442.251-.709.251-1.317.176-1.442-.075-.127-.276-.204-.579-.356z" />
                                    </svg>
                                </a>
                                @endif

                                {{-- Download PDF --}}
                                @if($contract->hasPdf())
                                <a href="{{ route('admin.contracts.download', $contract) }}" target="_blank"
                                   class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Download PDF">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
                                @endif

                                {{-- Tandai Expired —hanya active --}}
                                @if($contract->status === 'active')
                                <button @click="markAsExpired({{ $contract->id }})"
                                        class="p-1.5 text-orange-500 hover:bg-orange-50 rounded-lg transition" title="Tandai Expired">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>

                                {{-- Terminasi —hanya active --}}
                                <button @click="terminateContract({{ $contract->id }})"
                                        class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="Terminasi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                @endif

                                {{-- Perpanjang —hanya expired --}}
                                @if($contract->status === 'expired')
                                <button @click="renewContract({{ $contract->id }})"
                                        class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition" title="Perpanjang">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400 text-sm">
                            Belum ada kontrak Virtual Office aktif / terarsip.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $activeContracts->appends([
                'tab'           => 'vo',
                'status_active' => request('status_active'),
                'search_active' => request('search_active'),
                'search_draft'  => request('search_draft'),
                'draft_page'    => request('draft_page'),
            ])->links() }}
        </div>
    </div>

    {{-- ============================================================
         SECTION 2: MENUNGGU DIGENERATE (DRAFT)
    ============================================================ --}}
    <div class="pt-6 border-t border-gray-200">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
            <h2 class="text-base font-semibold text-gray-800">Menunggu Generate Kontrak</h2>

            {{-- Search Draft --}}
            <form action="{{ route('admin.contracts.index') }}" method="GET" class="flex gap-1">
                <input type="hidden" name="tab"           value="vo">
                <input type="hidden" name="status_active" value="{{ request('status_active') }}">
                <input type="hidden" name="search_active" value="{{ request('search_active') }}">
                <input type="hidden" name="active_page"   value="{{ request('active_page') }}">

                <input type="text" name="search_draft" value="{{ request('search_draft') }}"
                       placeholder="Cari draft kontrak..."
                       class="text-sm border border-gray-200 rounded-lg px-3 py-2 w-52 focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none">
                <button type="submit"
                        class="px-3 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
                @if(request('search_draft'))
                <a href="{{ route('admin.contracts.index', ['tab' => 'vo']) }}"
                   class="px-3 py-2 bg-gray-100 text-gray-500 rounded-lg hover:bg-gray-200 transition text-xs flex items-center">
                    Reset
                </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto border border-gray-200 rounded-xl">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-600 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3">Order ID</th>
                        <th class="px-4 py-3">Pelanggan</th>
                        <th class="px-4 py-3">Lokasi</th>
                        <th class="px-4 py-3">Invoice</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($draftContracts as $trx)
                    @php
                        $dNama    = $trx->nama_lengkap    ?? '-';
                        $dCompany = $trx->company_name    ?? '-';
                        $dLokasi  = $trx->location->name  ?? '-';
                        $dAlamat  = $trx->location->address ?? '-';
                        $dOrder   = $trx->order_id        ?? '-';
                    @endphp
                    <tr class="bg-white hover:bg-gray-50 transition">

                        {{-- Order ID --}}
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-gray-700 truncate block max-w-[110px]" title="{{ $dOrder }}">
                                {{ Str::limit($dOrder, 18, '…') }}
                            </span>
                        </td>

                        {{-- Pelanggan --}}
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900 text-xs truncate max-w-[120px]" title="{{ $dNama }}">
                                {{ Str::limit($dNama, 18, '…') }}
                            </div>
                            <div class="text-[11px] text-gray-400 truncate max-w-[120px]" title="{{ $dCompany }}">
                                {{ Str::limit($dCompany, 20, '…') }}
                            </div>
                        </td>

                        {{-- Lokasi --}}
                        <td class="px-4 py-3">
                            <div class="text-xs text-gray-700 font-medium truncate max-w-[100px]" title="{{ $dLokasi }}">
                                {{ Str::limit($dLokasi, 14, '…') }}
                            </div>
                            <div class="text-[11px] text-gray-400 truncate max-w-[100px]" title="{{ $dAlamat }}">
                                {{ Str::limit($dAlamat, 16, '…') }}
                            </div>
                        </td>

                        {{-- Invoice --}}
                        <td class="px-4 py-3">
                            @if($trx->invoice)
                                <span class="text-blue-600 font-medium text-xs">
                                    {{ Str::limit(str_replace('INV-', '', $trx->invoice->invoice_number), 16, '…') }}
                                </span>
                            @else
                                <span class="bg-red-100 text-red-700 text-[11px] font-medium px-2 py-0.5 rounded">No Invoice</span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                {{-- Info Popup --}}
                                <button
                                    @click="$dispatch('open-contract-detail', {
                                        title: 'Detail Draft Kontrak',
                                        rows: [
                                            { label: 'Order ID',     value: {{ json_encode($dOrder) }} },
                                            { label: 'Pelanggan',    value: {{ json_encode($dNama) }} },
                                            { label: 'Perusahaan',   value: {{ json_encode($dCompany) }} },
                                            { label: 'Lokasi',       value: {{ json_encode($dLokasi) }} },
                                            { label: 'Alamat',       value: {{ json_encode($dAlamat) }} },
                                            { label: 'Invoice',      value: {{ json_encode($trx->invoice?->invoice_number ?? 'Belum ada') }} },
                                        ]
                                    })"
                                    class="p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-700 rounded-lg transition"
                                    title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>

                                {{-- Generate Kontrak --}}
                                <button @click="openContractModal({{ $trx->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-purple-700 bg-purple-50 border border-purple-200 hover:bg-purple-100 rounded-md transition"
                                        title="Generate Kontrak">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Generate
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-sm">
                            Tidak ada draft kontrak yang menunggu generate.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $draftContracts->appends([
                'tab'           => 'vo',
                'search_draft'  => request('search_draft'),
                'status_active' => request('status_active'),
                'search_active' => request('search_active'),
                'active_page'   => request('active_page'),
            ])->links() }}
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('contractDetailPopup', () => ({
            show: false,
            title: '',
            rows: [],

            open(detail) {
                this.title = detail.title ?? 'Detail';
                this.rows  = detail.rows  ?? [];
                this.show  = true;
                document.body.style.overflow = 'hidden';
            },

            close() {
                this.show = false;
                document.body.style.overflow = '';
            },
        }));
    });
</script>
@endpush