{{-- resources/views/reward.blade.php --}}
@extends('layouts.app')

@section('content')
<div x-data="{ isLoaded: false }" x-init="setTimeout(() => isLoaded = true, 200)" class="flex min-h-screen bg-gray-50">

    {{-- Main Content --}}
    <div class="flex-1 w-full md:ml-52 lg:ml-64">
        {{-- Header Section --}}
        <div 
            class="relative h-64 sm:h-72 md:h-80 lg:h-96 bg-cover bg-center transition-all duration-1000 overflow-hidden"
            :class="isLoaded ? 'opacity-100 scale-100' : 'opacity-0 scale-95'"
            style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2069&q=80')">
            
            {{-- Orange Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-orange-500 via-orange-400/90 to-orange-300/70"></div>

            {{-- Header Content --}}
            <div class="relative z-10 p-4 sm:p-6 text-white h-full flex flex-col">
                {{-- Back Button --}}
                <a href="{{ route('dashboard.home') }}"
                    class="inline-flex items-center mb-4 sm:mb-6 hover:bg-white/20 p-2 rounded-lg transition-all duration-700 delay-200 transform hover:scale-105 hover:shadow-lg"
                    :class="isLoaded ? 'translate-x-0 opacity-100' : '-translate-x-10 opacity-0'">
                        <span class="font-medium text-sm sm:text-base">REWARD</span>
                </a>
                {{-- Earning Info --}}
                <div class="flex-1 flex flex-col justify-center items-center text-center">
                    {{-- Main Earning Card --}}
                    <div class="inline-block bg-black/30 backdrop-blur-sm rounded-xl sm:rounded-2xl 
                                px-3 sm:px-4 lg:px-6 py-2 sm:py-3 lg:py-4 mb-8 sm:mb-4 lg:mb-12 
                                transition-all duration-1000 delay-300 transform 
                                hover:bg-black/40 hover:scale-105 
                                w-full max-w-[180px] sm:max-w-[240px] md:max-w-[280px]"
                         :class="isLoaded ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                        <div class="flex items-center justify-center space-x-2 mb-2">
                            <span class="text-sm sm:text-lg lg:text-xl animate-bounce">💰</span>
                            <span class="text-[10px] sm:text-sm font-medium opacity-90">PENDAPATAN</span>
                        </div>
                        <div class="text-sm sm:text-xl md:text-2xl lg:text-3xl font-bold break-words">
                            RP. 100.000.000
                        </div>
                    </div>

                    {{-- Service Cards --}}
                    <div class="flex flex-row flex-wrap justify-center gap-2 sm:gap-3 md:gap-4 px-2 sm:px-4 w-full max-w-6xl sm:mb-10 lg:mb-12">
                        {{-- Card 1 --}}
                        <div class="bg-white/20 backdrop-blur-sm rounded-lg sm:rounded-xl md:rounded-2xl 
                                    px-2 sm:px-3 md:px-4 lg:px-6 py-2 sm:py-3 md:py-4 
                                    transition-all duration-1000 delay-500 transform 
                                    hover:bg-white/30 hover:scale-105 hover:shadow-xl 
                                    w-[75px] sm:w-[90px] md:w-[110px] lg:w-[130px] xl:w-[150px]
                                    h-[75px] sm:h-[90px] md:h-[110px] lg:h-[130px] xl:h-[150px]
                                    flex flex-col justify-center"
                             :class="isLoaded ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                            <div class="flex flex-col items-center space-y-1 mb-1 sm:mb-2">
                                <span class="text-xs sm:text-sm md:text-base lg:text-lg">💰</span>
                                <span class="text-[8px] sm:text-[10px] md:text-xs lg:text-sm font-medium">Meeting</span>
                            </div>
                            <div class="text-center text-[10px] sm:text-xs md:text-sm lg:text-base xl:text-lg font-bold">
                                RP. 70.000.000
                            </div>
                        </div>

                        {{-- Card 2 --}}
                        <div class="bg-white/20 backdrop-blur-sm rounded-lg sm:rounded-xl md:rounded-2xl 
                                    px-2 sm:px-3 md:px-4 lg:px-6 py-2 sm:py-3 md:py-4 
                                    transition-all duration-1000 delay-700 transform 
                                    hover:bg-white/30 hover:scale-105 hover:shadow-xl 
                                    w-[75px] sm:w-[90px] md:w-[110px] lg:w-[130px] xl:w-[150px]
                                    h-[75px] sm:h-[90px] md:h-[110px] lg:h-[130px] xl:h-[150px]
                                    flex flex-col justify-center"
                             :class="isLoaded ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                            <div class="flex flex-col items-center space-y-1 mb-1 sm:mb-2">
                                <span class="text-xs sm:text-sm md:text-base lg:text-lg">💰</span>
                                <span class="text-[8px] sm:text-[10px] md:text-xs lg:text-sm font-medium">Virtual Office</span>
                            </div>
                            <div class="text-center text-[10px] sm:text-xs md:text-sm lg:text-base xl:text-lg font-bold">
                                RP. 20.000.000
                            </div>
                        </div>

                        {{-- Card 3 --}}
                        <div class="bg-white/20 backdrop-blur-sm rounded-lg sm:rounded-xl md:rounded-2xl 
                                    px-2 sm:px-3 md:px-4 lg:px-6 py-2 sm:py-3 md:py-4 
                                    transition-all duration-1000 delay-900 transform 
                                    hover:bg-white/30 hover:scale-105 hover:shadow-xl 
                                    w-[75px] sm:w-[90px] md:w-[110px] lg:w-[130px] xl:w-[150px]
                                    h-[75px] sm:h-[90px] md:h-[110px] lg:h-[130px] xl:h-[150px]
                                    flex flex-col justify-center"
                             :class="isLoaded ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                            <div class="flex flex-col items-center space-y-1 mb-1 sm:mb-2">
                                <span class="text-xs sm:text-sm md:text-base lg:text-lg">💰</span>
                                <span class="text-[8px] sm:text-[10px] md:text-xs lg:text-sm font-medium">Event Space</span>
                            </div>
                            <div class="text-center text-[10px] sm:text-xs md:text-sm lg:text-base xl:text-lg font-bold">
                                RP. 10.000.000
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transaction History Section --}}
        <div class="p-4 sm:p-6 mb-9">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 transition-all duration-1000 delay-1100 transform"
                :class="isLoaded ? 'translate-x-0 opacity-100' : '-translate-x-10 opacity-0'">
                RIWAYAT TRANSAKSI
            </h2>

            {{-- Example Transaction Card --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-1000 delay-1300 transform hover:shadow-xl hover:-translate-y-1 hover:scale-[1.02] mb-6"
                 :class="isLoaded ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-orange-500 to-orange-400 p-3 sm:p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <div class="text-white">
                        <h3 class="font-semibold text-base sm:text-lg">Meeting 549/MT/23/06/25</h3>
                    </div>
                    <div class="bg-green-500 text-white px-3 sm:px-4 py-1 sm:py-2 rounded-full text-xs sm:text-sm font-medium animate-pulse">
                        Lunas
                    </div>
                </div>

                {{-- Details --}}
                <div class="p-4 sm:p-6">
                    <div class="flex flex-col lg:flex-row lg:items-start space-y-4 lg:space-y-0 lg:space-x-4">
                        {{-- Info Section --}}
                        <div class="flex-1 space-y-4">
                            <h4 class="text-lg sm:text-xl font-bold text-gray-800">Amaris Hotel Surabaya</h4>

                            <div class="space-y-4 sm:space-y-0 sm:grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 sm:gap-4 lg:gap-6">
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <h5 class="text-sm font-semibold text-gray-600 mb-2">Service</h5>
                                    <p class="text-gray-800 font-medium">Meeting</p>
                                </div>

                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <h5 class="text-sm font-semibold text-gray-600 mb-2">Pax</h5>
                                    <p class="text-gray-800 font-medium">6 pax/ cb</p>
                                </div>

                                <div class="bg-gray-50 p-3 rounded-lg md:col-span-2 lg:col-span-1">
                                    <h5 class="text-sm font-semibold text-gray-600 mb-2">Durasi</h5>
                                    <p class="text-gray-800 font-medium text-sm">8 jam (09.00-17.00)/ full day</p>
                                </div>
                            </div>

                            <div class="bg-orange-50 p-3 rounded-lg">
                                <p class="text-gray-700 text-sm sm:text-base">Meeting room 6 pax include CB</p>
                            </div>
                        </div>

                        <div class="text-center lg:text-right bg-orange-50 p-4 rounded-lg lg:bg-transparent lg:p-0">
                            <div class="text-2xl sm:text-3xl font-bold text-orange-500 animate-pulse">
                                Rp. 350.000
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
