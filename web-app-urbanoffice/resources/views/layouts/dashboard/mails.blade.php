@extends('layouts.app') {{-- layout utama --}}

@section('content')
<div class="flex min-h-screen bg-gray-50">

    {{-- Main Content --}}
    <div class="flex-1 ml-0 md:ml-60 lg:ml-64 xl:ml-64 flex flex-col">

        {{-- Mails Header Bar --}}
        <div class="transition-all duration-700 ease-out opacity-100 translate-y-0">
            @include('layouts.components.mailsbar')
        </div>

        {{-- Mail Content --}}
        <div class="flex-1 p-4 md:p-6">
    {{-- Mail Header --}}
    <div class="mb-4 md:mb-6 transition-all duration-500 ease-out opacity-100 translate-y-0 delay-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-2">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-2 sm:mb-0">
                Kontak Pesan Masuk
            </h2>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                @if($transactions->first()?->status === 'settlement') bg-green-100 text-green-800 
                @elseif($transactions->first()?->status === 'pending') bg-yellow-100 text-yellow-800 
                @else bg-red-100 text-red-800 @endif w-fit">
                {{ ucfirst($transactions->first()?->status ?? 'Belum ada') }}
            </span>
        </div>
        <p class="text-sm text-gray-600">
            {{ $transactions->first()?->order_id ?? '-' }} / 
            {{ $transactions->first()?->booking_date?->format('d/m/Y') ?? '-' }}
        </p>
    </div>

    @foreach($transactions as $transaction)
    {{-- Message Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 transition-all duration-500 ease-out opacity-100 translate-y-0 delay-{{ 300 + $loop->index * 100 }}">
        {{-- Message Header --}}
        <div class="p-4 md:p-6 border-b border-gray-200">
            <div class="flex items-start space-x-3">
                <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <div class="w-5 h-5 bg-white rounded"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-800 text-sm md:text-base">
                        Dari Customer - {{ $transaction->nama_lengkap }}
                    </h3>
                    <p class="text-xs md:text-sm text-gray-600 mt-1">
                        No Telp. {{ $transaction->telephone }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Message Content --}}
        <div class="p-4 md:p-6">
            <div class="space-y-4">
                {{-- Customer Message --}}
                <div class="bg-gray-50 rounded-lg p-3 md:p-4">
                    <p class="text-gray-700 text-sm md:text-base">
                        Terima kasih atas transaksi Anda.
                    </p>
                </div>

                {{-- Payment Information --}}
                <div class="border-l-4 border-blue-400 bg-blue-50 pl-4 py-3 rounded-r-lg">
                    <p class="text-blue-700 text-sm md:text-base font-medium">
                        Untuk pelunasan berikut nanti Rp. {{ number_format($transaction->gross_amount, 0, ',', '.') }}
                    </p>
                </div>

                {{-- Payment Instructions --}}
                <div class="border-l-4 border-yellow-400 bg-yellow-50 pl-4 py-3 rounded-r-lg">
                    <p class="text-yellow-700 text-sm md:text-base">
                        <span class="font-medium">Instruksi:</span> Jika ingin tf bisa ke rekening BCA 8023666667 a.n. Master Penerjemah Indonesia
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

    </div>
</div>

{{-- JS untuk preview nama file --}}
<script>
    function previewFileName(input) {
        const file = input.files[0];
        if (file) {
            document.getElementById('fileNamePreview').textContent = "File dipilih: " + file.name;
        }
    }
</script>
@endsection
