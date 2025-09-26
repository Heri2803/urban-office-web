@extends('layouts.app') {{-- layout utama --}}

@section('content')
<div class="flex min-h-screen bg-gray-50">

    {{-- Main Content --}}
    <div class="flex-1 ml-0 md:ml-60 lg:ml-64 xl:ml-64 flex flex-col">

        {{-- Invoice Header Bar --}}
        <div class="transition-all duration-700 ease-out opacity-100 translate-y-0">
            @include('layouts.components.invoice')
        </div>

        {{-- Invoice Content dengan container yang diperkecil untuk tablet --}}
        <div class="flex-1 p-3 md:p-4 lg:p-6">
            {{-- Page Header with Back Button --}}
            <div class="mb-4 md:mb-6 transition-all duration-500 ease-out opacity-100 translate-y-0 delay-200">
                <div class="flex items-center space-x-3 mb-4">
                    <a href="{{ route('dashboard.home') }}" class="block">
                        <button class="flex items-center text-gray-700 hover:text-orange-500 transition-colors duration-200 group">
                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span class="font-medium text-xs md:text-sm lg:text-base">INVOICE</span>
                        </button>
                    </a>
                </div>
            </div>

            {{-- Documents Section dengan max-width untuk tablet --}}
            <div class="space-y-3 md:space-y-4 max-w-full md:max-w-3xl lg:max-w-4xl xl:max-w-5xl">
                

                @foreach($transactions as $index => $transaction)
                    {{-- Category Header --}}
                    <div class="mb-2 md:mb-3 transition-all duration-500 ease-out opacity-100 translate-x-0 delay-{{ 300 + $index * 100 }}">
                        <h3 class="text-xs md:text-sm lg:text-base font-medium text-gray-700 mb-1 md:mb-2">
                            Invoice {{ $transaction->room_type }}
                        </h3>
                    </div>

                    {{-- Document Card --}}
                    <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-200 p-3 md:p-4 lg:p-6 transition-all duration-500 ease-out hover:shadow-md opacity-100 translate-y-0 delay-{{ 400 + $index * 100 }}">
                        <div class="flex items-start space-x-2 md:space-x-3 mb-3 md:mb-4">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-red-500 rounded flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-bold text-xs">PDF</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-800 text-xs md:text-sm lg:text-base mb-1 truncate">
                                    {{ $transaction->order_id }}. Invoice {{ $transaction->room_type }} - {{ $transaction->nama_lengkap }}
                                </h4>
                                <p class="text-xs text-gray-500">
                                    {{ $transaction->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>

                        
                        {{-- Download button --}}
                        <a href="{{ route('invoice.generate', $transaction->order_id) }}"
                        class="inline-block px-3 py-1 text-xs md:text-sm bg-orange-500 text-white rounded hover:bg-orange-600">
                            Download PDF
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection