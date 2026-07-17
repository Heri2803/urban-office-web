@extends('layouts.admin')
@section('title', 'Manajemen Addendum')

@section('content')
<div class="space-y-6" x-data="addendumManager()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Addendum</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola seluruh riwayat addendum / perpanjangan kontrak Virtual Office</p>
        </div>
        <a href="{{ route('admin.contracts.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Ke Manajemen Kontrak
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Total</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 shadow-sm p-4">
            <p class="text-xs text-green-500 font-medium uppercase tracking-wide">Aktif</p>
            <p class="text-2xl font-bold text-green-700 mt-1">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-yellow-100 shadow-sm p-4">
            <p class="text-xs text-yellow-500 font-medium uppercase tracking-wide">Draft</p>
            <p class="text-2xl font-bold text-yellow-700 mt-1">{{ $stats['draft'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-red-100 shadow-sm p-4">
            <p class="text-xs text-red-400 font-medium uppercase tracking-wide">Terminated</p>
            <p class="text-2xl font-bold text-red-700 mt-1">{{ $stats['terminated'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.addendums.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nomor addendum, nama, order ID..."
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none">
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none bg-white">
                    <option value="">Semua Status</option>
                    <option value="draft"      {{ request('status') === 'draft'      ? 'selected' : '' }}>Draft</option>
                    <option value="active"     {{ request('status') === 'active'     ? 'selected' : '' }}>Active</option>
                    <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
            </div>
            <button type="submit"
                    class="px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition">
                Cari
            </button>
            @if(request()->anyFilled(['search','status']))
            <a href="{{ route('admin.addendums.index') }}"
               class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition text-center">
                Reset
            </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800">Daftar Addendum</h2>
            <span class="text-xs text-gray-400">{{ $addendums->total() }} data ditemukan</span>
        </div>

        @if($addendums->isEmpty())
        <div class="text-center py-16">
            <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-400 text-sm">Belum ada data addendum.</p>
        </div>
        @else
        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Nomor Addendum</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left">Kontrak Induk</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Berlaku s/d</th>
                        <th class="px-4 py-3 text-right">Nilai</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($addendums as $addendum)
                    @php
                        $contract    = $addendum->contract;
                        $transaction = $contract?->transaction ?? $addendum->transaction;
                        $customer    = $transaction?->user ?? null;
                        $customerName = $transaction?->nama_lengkap ?? $customer?->name ?? '-';
                        $companyName  = $transaction?->company_name ?? '-';
                    @endphp
                    <tr class="hover:bg-gray-50 transition" data-addendum-id="{{ $addendum->id }}">
                        {{-- Urutan --}}
                        <td class="px-4 py-3 text-gray-400 font-mono text-xs">
                            {{ $addendum->roman_order }}
                        </td>

                        {{-- Nomor Addendum --}}
                        <td class="px-4 py-3">
                            @if($addendum->addendum_number)
                                <p class="font-medium text-gray-800 text-xs leading-tight">{{ $addendum->addendum_number }}</p>
                            @else
                                <span class="text-gray-400 italic text-xs">Belum digenerate</span>
                            @endif
                        </td>

                        {{-- Customer --}}
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $customerName }}</p>
                            @if($companyName !== '-')
                                <p class="text-xs text-gray-400">{{ $companyName }}</p>
                            @endif
                            @if($transaction?->order_id)
                                <p class="text-xs text-gray-400 font-mono">{{ $transaction->order_id }}</p>
                            @endif
                        </td>

                        {{-- Kontrak Induk --}}
                        <td class="px-4 py-3">
                            @if($contract)
                                <p class="text-xs font-medium text-gray-700">{{ $contract->contract_number ?? 'Kontrak #' . $contract->id }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $contract->status === 'active'     ? 'bg-green-100 text-green-700'  : '' }}
                                    {{ $contract->status === 'expired'    ? 'bg-orange-100 text-orange-700' : '' }}
                                    {{ $contract->status === 'renewed'    ? 'bg-blue-100 text-blue-700'    : '' }}
                                    {{ $contract->status === 'terminated' ? 'bg-red-100 text-red-700'      : '' }}
                                ">
                                    {{ ucfirst($contract->status) }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>

                        {{-- Tanggal Addendum --}}
                        <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">
                            {{ $addendum->formatted_addendum_date }}
                        </td>

                        {{-- End Date --}}
                        <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">
                            {{ $addendum->formatted_end_date }}
                        </td>

                        {{-- Nilai --}}
                        <td class="px-4 py-3 text-right">
                            <span class="font-medium text-gray-800 text-xs whitespace-nowrap">
                                Rp {{ number_format($addendum->gross_amount ?? 0, 0, ',', '.') }}
                            </span>
                        </td>

                        {{-- Status Badge --}}
                        <td class="px-4 py-3 text-center">
                            {!! $addendum->status_badge !!}
                        </td>

                        {{-- Aksi --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Kirim WA Perpanjangan --}}
                                @if($addendum->wa_link !== '#')
                                <a href="{{ $addendum->wa_link }}" target="_blank"
                                   class="p-1.5 text-green-500 hover:bg-green-50 hover:text-green-600 rounded-lg transition" 
                                   title="Kirim Notifikasi WA">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.031 0C5.385 0 0 5.388 0 12.036c0 2.124.553 4.195 1.604 6.015L.17 24l6.096-1.597C8.016 23.36 10.007 23.94 12.025 23.94h.006C18.667 23.94 24 18.549 24 11.905 24 8.683 22.753 5.65 20.475 3.375 18.201 1.099 15.192 0 12.031 0zm.006 21.942c-1.785 0-3.535-.48-5.066-1.388l-.364-.215-3.766.986.997-3.667-.236-.376C2.65 15.753 2.016 13.882 2.016 11.904c0-5.541 4.512-10.057 10.052-10.057 2.684 0 5.205 1.047 7.1 2.943 1.895 1.896 2.939 4.417 2.939 7.102 0 5.543-4.512 10.05-10.063 10.05H12.03zm5.518-7.551c-.302-.153-1.792-.888-2.069-.99-.276-.102-.477-.153-.679.153-.201.305-.78 1.002-.955 1.206-.176.204-.352.229-.654.076-.302-.153-1.28-.472-2.438-1.506-.902-.806-1.509-1.802-1.686-2.107-.176-.305-.019-.47.133-.622.136-.135.302-.354.453-.531.151-.178.201-.305.302-.508.101-.203.051-.382-.025-.535-.075-.153-.679-1.637-.93-2.243-.243-.591-.491-.512-.678-.521-.176-.008-.377-.008-.579-.008s-.528.076-.805.381c-.276.305-1.055 1.03-1.055 2.511 0 1.482 1.08 2.915 1.231 3.118.151.204 2.127 3.245 5.15 4.549.718.31 1.28.495 1.716.634.721.23 1.378.197 1.894.12.576-.086 1.792-.733 2.043-1.442.251-.709.251-1.317.176-1.442-.075-.127-.276-.204-.579-.356z" />
                                    </svg>
                                </a>
                                @endif

                                {{-- Download PDF --}}
                                @if($addendum->file_path || $addendum->status === 'active')
                                    <a href="{{ route('admin.addendums.download', $addendum->id) }}"
                                       target="_blank"
                                       class="p-1.5 text-orange-500 hover:bg-orange-50 rounded-lg transition"
                                       title="Download PDF Addendum">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13 8V2H7v6H2l8 8 8-8h-5zm-7 10h8v2H6v-2z"/>
                                        </svg>
                                    </a>
                                @endif

                                {{-- Terminate (hanya active) --}}
                                @if($addendum->status === 'active')
                                    <button @click="terminateAddendum({{ $addendum->id }})"
                                            class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition"
                                            title="Batalkan Addendum">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                @endif

                                {{-- Link ke kontrak induk --}}
                                @if($contract)
                                    <a href="{{ route('admin.contracts.index') }}?search_active={{ urlencode($contract->contract_number ?? '') }}&tab=vo"
                                       class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition"
                                       title="Lihat Kontrak Induk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-gray-100">
            @foreach($addendums as $addendum)
            @php
                $contract    = $addendum->contract;
                $transaction = $contract?->transaction ?? $addendum->transaction;
                $customer    = $transaction?->user ?? null;
                $customerName = $transaction?->nama_lengkap ?? $customer?->name ?? '-';
                $companyName  = $transaction?->company_name ?? '-';
            @endphp
            <div class="p-4 space-y-2">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">{{ $customerName }}</p>
                        @if($companyName !== '-')
                            <p class="text-xs text-gray-400">{{ $companyName }}</p>
                        @endif
                    </div>
                    {!! $addendum->status_badge !!}
                </div>
                <p class="text-xs text-gray-500 font-mono">{{ $addendum->addendum_number ?? 'Draft Addendum #'.$addendum->id }}</p>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <span>Berlaku s/d: <strong class="text-gray-700">{{ $addendum->formatted_end_date }}</strong></span>
                    <span>·</span>
                    <span class="font-medium text-gray-700">Rp {{ number_format($addendum->gross_amount ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-2 pt-1">
                    @if($addendum->wa_link !== '#')
                        <a href="{{ $addendum->wa_link }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-600 text-xs rounded-lg hover:bg-green-100 transition font-medium">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.388 0 12.036c0 2.124.553 4.195 1.604 6.015L.17 24l6.096-1.597C8.016 23.36 10.007 23.94 12.025 23.94h.006C18.667 23.94 24 18.549 24 11.905 24 8.683 22.753 5.65 20.475 3.375 18.201 1.099 15.192 0 12.031 0zm.006 21.942c-1.785 0-3.535-.48-5.066-1.388l-.364-.215-3.766.986.997-3.667-.236-.376C2.65 15.753 2.016 13.882 2.016 11.904c0-5.541 4.512-10.057 10.052-10.057 2.684 0 5.205 1.047 7.1 2.943 1.895 1.896 2.939 4.417 2.939 7.102 0 5.543-4.512 10.05-10.063 10.05H12.03zm5.518-7.551c-.302-.153-1.792-.888-2.069-.99-.276-.102-.477-.153-.679.153-.201.305-.78 1.002-.955 1.206-.176.204-.352.229-.654.076-.302-.153-1.28-.472-2.438-1.506-.902-.806-1.509-1.802-1.686-2.107-.176-.305-.019-.47.133-.622.136-.135.302-.354.453-.531.151-.178.201-.305.302-.508.101-.203.051-.382-.025-.535-.075-.153-.679-1.637-.93-2.243-.243-.591-.491-.512-.678-.521-.176-.008-.377-.008-.579-.008s-.528.076-.805.381c-.276.305-1.055 1.03-1.055 2.511 0 1.482 1.08 2.915 1.231 3.118.151.204 2.127 3.245 5.15 4.549.718.31 1.28.495 1.716.634.721.23 1.378.197 1.894.12.576-.086 1.792-.733 2.043-1.442.251-.709.251-1.317.176-1.442-.075-.127-.276-.204-.579-.356z" /></svg>
                            WA
                        </a>
                    @endif
                    @if($addendum->file_path || $addendum->status === 'active')
                        <a href="{{ route('admin.addendums.download', $addendum->id) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 text-orange-600 text-xs rounded-lg hover:bg-orange-100 transition font-medium">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M13 8V2H7v6H2l8 8 8-8h-5zm-7 10h8v2H6v-2z"/></svg>
                            PDF
                        </a>
                    @endif
                    @if($addendum->status === 'active')
                        <button @click="terminateAddendum({{ $addendum->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 text-xs rounded-lg hover:bg-red-100 transition font-medium">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batalkan
                        </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($addendums->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $addendums->links() }}
        </div>
        @endif
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('addendumManager', () => ({
            async terminateAddendum(addendumId) {
                const reason = prompt('Alasan pembatalan Addendum ini:');
                if (reason === null) return;

                if (!reason.trim()) {
                    alert('Alasan pembatalan wajib diisi');
                    return;
                }

                try {
                    const response = await fetch(`/admin/addendums/${addendumId}/terminate`, {
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
                    setTimeout(() => window.location.reload(), 1000);

                } catch (error) {
                    console.error('Error terminating addendum:', error);
                    this.showToast(error.message || 'Terjadi kesalahan', 'error');
                }
            },

            showToast(message, type = 'success') {
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
