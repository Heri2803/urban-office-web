{{-- resources/views/layouts/dashboard/calls.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-50">

    {{-- Main Content --}}
    <div class="flex-1 ml-0 md:ml-60 lg:ml-64 xl:ml-64 flex flex-col mb-14">
        
        {{-- Calls Header Bar --}}
        @include('layouts.components.callsbar')

        {{-- Calls Content --}}
        <div class="flex-1 p-4 md:p-6">
            
            {{-- Page Header with Back Button --}}
            <div class="mb-6">
                <a href="{{ route('dashboard.home') }}" class="block">
                    <div class="flex items-center space-x-3 mb-4">
                        <button class="flex items-center text-gray-700 hover:text-orange-500 transition-colors duration-200 group">
                            <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span class="font-medium text-sm md:text-base">CALLS</span>
                        </button>
                    </div>
                </a>
            </div>

            {{-- Call History --}}
            <div class="space-y-4">
                @php
                    $callHistory = [
                        [
                            'id' => 1,
                            'date' => '01/02/2026',
                            'title' => 'Telephone Masuk',
                            'code' => '549/VO/VII/25',
                            'status' => 'Terjawab',
                            'customer' => 'Dari Customer - Angga',
                            'phone' => 'No Telp. 081243599921',
                            'location' => 'Amaris Hotel Surabaya',
                        ],
                        [
                            'id' => 2,
                            'date' => '01/02/2026',
                            'title' => 'Telephone Masuk',
                            'code' => '549/VO/VII/25',
                            'status' => 'Terjawab',
                            'customer' => 'Dari Customer - Kevin',
                            'phone' => 'No Telp. 082190742678',
                            'location' => 'Amaris Hotel Surabaya',
                        ]
                    ];
                @endphp

                @foreach ($callHistory as $call)
                    <div class="space-y-4">
                        {{-- Date Header --}}
                        <div class="bg-orange-500 text-white px-4 py-2 rounded-t-lg">
                            <span class="font-medium text-sm md:text-base">{{ $call['date'] }}</span>
                        </div>

                        {{-- Call Card --}}
                        <div class="bg-white rounded-b-lg shadow-sm border border-gray-200 hover:shadow-md">

                            {{-- Call Header --}}
                            <div class="p-4 md:p-6 border-b border-gray-200">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-2">
                                    <h3 class="font-semibold text-gray-800 text-sm md:text-base mb-2 sm:mb-0">
                                        {{ $call['title'] }}
                                    </h3>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 w-fit">
                                        {{ $call['status'] }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">{{ $call['code'] }}</p>
                            </div>

                            {{-- Customer Info --}}
                            <div class="p-4 md:p-6">
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-800 text-sm md:text-base mb-1">
                                            {{ $call['customer'] }}
                                        </h4>
                                        <p class="text-xs md:text-sm text-gray-600 mb-1">
                                            {{ $call['phone'] }}
                                        </p>
                                        <p class="text-xs md:text-sm text-gray-600">
                                            {{ $call['location'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Quick Actions - Mobile Style --}}
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <button class="flex items-center justify-center space-x-3 p-4 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span class="font-medium">Panggil Sekarang</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
