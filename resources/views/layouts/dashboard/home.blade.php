@extends('layouts.app')

@section('content')
    <div class="flex min-h-screen bg-gray-50">
        <!-- Sidebar Component -->
        @include('layouts.components.sidebar')

        <!-- Main Content -->
        <div class="flex-1 ml-0 md:ml-60 lg:ml-64 xl:ml-64 p-4 md:p-6 mb-8">
            <!-- User Status -->
            @include('layouts.components.userstatus', ['name' => 'Georgius Mario', 'status' => 'Virtual Office'])
            <!-- Hero Section -->
            <div id="hero-section" 
                class="relative rounded-lg mx-2 sm:mx-4 md:mx-6 lg:mx-8 mt-6 overflow-hidden transition-all duration-800 ease-out opacity-0 translate-y-6">
                <div class="absolute inset-0">
                    @php
                        $hasHeroBanners = isset($heroBanners) && $heroBanners->count() > 0;
                        $totalHeroBanners = $hasHeroBanners ? $heroBanners->count() : 0;
                        $slideIndex = 0;
                    @endphp
                    
                    @if($hasHeroBanners)
                        @foreach($heroBanners as $banner)
                            <div class="absolute inset-0 bg-cover bg-center hero-slide transition-opacity duration-1000 {{ $slideIndex === 0 ? 'opacity-100' : 'opacity-0' }}"
                                style="background-image: url('{{ $banner->image_url }}')">
                            </div>
                            @php $slideIndex++; @endphp
                        @endforeach
                    @endif

                    {{-- Selalu tambahkan static fallback agar ada animasi slide jika dynamic banner kurang dari 2 --}}
                    @if($totalHeroBanners < 2)
                        <div class="absolute inset-0 bg-cover bg-center hero-slide transition-opacity duration-1000 {{ $slideIndex === 0 ? 'opacity-100' : 'opacity-0' }}"
                            style="background-image: url('{{ asset('assets/banner.jpg') }}')"></div>
                        @php $slideIndex++; @endphp
                        
                        @if($totalHeroBanners === 0)
                            <div class="absolute inset-0 bg-cover bg-center hero-slide transition-opacity duration-1000 opacity-0"
                                style="background-image: url('https://images.unsplash.com/photo-1556761175-b413da4baf72?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80')"></div>
                        @endif
                    @endif
                </div>
                <!-- Content -->
                <div class="relative rounded-lg mx-1 sm:mx-4 md:mx-6 lg:mx-8 mt-6 overflow-hidden h-48 sm:h-56 md:h-72 lg:h-96 xl:h-[28rem] 2xl:h-[32rem]">
                    <div class="relative px-4 sm:px-6 md:px-12 py-4 sm:py-6 md:py-8 lg:py-16 xl:py-20 hidden">
                        <div class="w-full sm:w-4/5 max-w-6xl">
                            <h1 class="text-lg sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold text-white mb-2 sm:mb-4 drop-shadow-lg">
                                STARTUP START
                            </h1>
                            <p class="text-sm sm:text-lg md:text-xl text-white mb-3 sm:mb-6 opacity-90 drop-shadow">
                                Menu Virtual Office FREE PENDIRIAN PT Perorangan
                            </p>
                        </div>
                        <div class="flex justify-end pr-12">
                            <div class="inline-flex items-center justify-center bg-red-500 text-white w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 lg:w-28 lg:h-28 rounded-full shadow-lg hover:bg-red-600 transition-all duration-300 hover:scale-105">
                                <span class="text-[8px] sm:text-[9px] md:text-[10px] lg:text-xs font-medium text-center leading-tight px-1">
                                    BEBAS AKSES RUANG MEETING
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Grid -->
            <div id="services-grid" class="w-full py-8 transition-all duration-800 ease-out">
                <!-- Slider Container -->
                <div class="relative w-full max-w-7xl mx-auto px-0 md:px-4 lg:px-8">
                    <!-- Services Slider -->
                    <div id="slider" class="overflow-x-auto snap-x scroll-smooth hide-scrollbar touch-pan-x px-4 md:px-0">
                        <div class="inline-flex gap-3 md:gap-3 lg:gap-5 lg:justify-center min-w-full lg:w-full">
                            <!-- Booking -->
                            <div class="snap-start">
                                <a href="{{ route('dashboard.booking') }}" class="block">
                                    <div class="service-card-container rounded-lg p-2 md:p-3 lg:p-5 shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer group flex flex-col items-center justify-center hover:scale-105" style="background-color: #FF9D23;">
                                        <div style="background-color: rgba(193, 70, 0, 0.4);" class="w-8 h-8 md:w-10 md:h-10 lg:w-14 lg:h-14 rounded-lg flex items-center justify-center mb-1.5 md:mb-2 lg:mb-3 group-hover:opacity-80 transition-all flex-shrink-0">
                                            <svg class="w-4 h-4 md:w-5 md:h-5 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <h3 class="font-semibold text-xs md:text-sm lg:text-base text-white text-center leading-tight px-1">
                                            Booking
                                        </h3>
                                    </div>
                                </a>
                            </div>
            
                            <!-- Call -->
                            <div class="snap-start">
                                <!-- Ganti <a> dengan <div> dan tambahkan onclick -->
                                <div onclick="openComingSoonModal()" class="block">
                                    <div class="service-card-container rounded-lg p-2 md:p-3 lg:p-5 shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer group flex flex-col items-center justify-center hover:scale-105" style="background-color: #FF9D23;">
                                        <div style="background-color: rgba(193, 70, 0, 0.4);" class="w-8 h-8 md:w-10 md:h-10 lg:w-14 lg:h-14 rounded-lg flex items-center justify-center mb-1.5 md:mb-2 lg:mb-3 group-hover:opacity-80 transition-all flex-shrink-0">
                                            <svg class="w-4 h-4 md:w-5 md:h-5 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                        </div>
                                        <h3 class="font-semibold text-xs md:text-sm lg:text-base text-white text-center leading-tight px-1">
                                            Call
                                        </h3>
                                    </div>
                                </div>
                            </div>
            
                            <!-- Mails -->
                            <div>
                            
                                {{-- DEBUG INFO (hapus setelah selesai) --}}
                                <div class="absolute -top-10 left-0 bg-yellow-200 px-2 py-1 text-xs rounded z-50"></div>
                            
                                <!-- ✅ Perbaikan: Ganti <a> menjadi <div> untuk menghindari konflik click -->
                                <div 
                                    class="block relative cursor-pointer"
                                    @click="window.location.href='{{ route('dashboard.mails') }}'"
                                >
                            
                                    {{-- Badge dengan styling lebih visible --}}
                                    @if(isset($unreadTransactions) && $unreadTransactions > 0)
                                        <div class="absolute -top-3 -right-3 z-[100] bg-red-600 text-white text-base font-bold 
                                                    min-w-[32px] h-8 px-3 rounded-full flex items-center justify-center 
                                                    shadow-2xl border-4 border-white animate-bounce">
                                            {{ $unreadTransactions > 99 ? '99+' : $unreadTransactions }}
                                        </div>
                                    @endif
                            
                                    <div class="service-card-container rounded-lg p-2 md:p-3 lg:p-5 shadow-sm 
                                                hover:shadow-md transition-all duration-300 cursor-pointer group 
                                                flex flex-col items-center justify-center hover:scale-105"
                                         style="background-color: #FF9D23;">
                            
                                        <div style="background-color: rgba(193, 70, 0, 0.4);"
                                             class="w-8 h-8 md:w-10 md:h-10 lg:w-14 lg:h-14 rounded-lg flex items-center 
                                                    justify-center mb-1.5 md:mb-2 lg:mb-3 group-hover:opacity-80 transition-all flex-shrink-0">
                                            <svg class="w-4 h-4 md:w-5 md:h-5 lg:w-7 lg:h-7 text-white"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                            
                                        <h3 class="font-semibold text-xs md:text-sm lg:text-base text-white text-center leading-tight px-1">
                                            Mails
                                        </h3>
                                    </div>
                                </div>
                            </div>
            
                            <!-- Invoice -->
                            <div class="snap-start">
                                <a href="{{ route('dashboard.invoice') }}" class="block">
                                    <div class="service-card-container rounded-lg p-2 md:p-3 lg:p-5 shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer group flex flex-col items-center justify-center hover:scale-105" style="background-color: #FF9D23;">
                                        <div style="background-color: rgba(193, 70, 0, 0.4);" class="w-8 h-8 md:w-10 md:h-10 lg:w-14 lg:h-14 rounded-lg flex items-center justify-center mb-1.5 md:mb-2 lg:mb-3 group-hover:opacity-80 transition-all flex-shrink-0">
                                            <svg class="w-4 h-4 md:w-5 md:h-5 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <h3 class="font-semibold text-xs md:text-sm lg:text-base text-white text-center leading-tight px-1">
                                            Invoice
                                        </h3>
                                    </div>
                                </a>
                            </div>
            
                            <!-- Pendirian Badan Usaha -->
                            <div class="snap-start">
                                <a href="{{ route('dashboard.mitra') }}" class="block">
                                    <div class="service-card-container rounded-lg p-2 md:p-3 lg:p-5 shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer group flex flex-col items-center justify-center hover:scale-105" style="background-color: #FF9D23;">
                                        <div style="background-color: rgba(193, 70, 0, 0.4);" class="w-8 h-8 md:w-10 md:h-10 lg:w-14 lg:h-14 rounded-lg flex items-center justify-center mb-1.5 md:mb-2 lg:mb-3 group-hover:opacity-80 transition-all flex-shrink-0">
                                            <svg class="w-4 h-4 md:w-5 md:h-5 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <h3 class="font-semibold text-[9px] md:text-[10px] lg:text-sm text-white text-center leading-tight px-1">
                                            Pendirian<br>Badan<br>Usaha
                                        </h3>
                                    </div>
                                </a>
                            </div>
            
                            <!-- My Contract -->
                            <div class="snap-start">
                                <a href="{{ route('customer.contracts.index') }}" class="block">
                                    <div class="service-card-container rounded-lg p-2 md:p-3 lg:p-5 shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer group flex flex-col items-center justify-center hover:scale-105" style="background-color: #FF9D23;">
                                        <div style="background-color: rgba(193, 70, 0, 0.4);" class="w-8 h-8 md:w-10 md:h-10 lg:w-14 lg:h-14 rounded-lg flex items-center justify-center mb-1.5 md:mb-2 lg:mb-3 group-hover:opacity-80 transition-all flex-shrink-0">
                                            <svg class="w-4 h-4 md:w-5 md:h-5 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <h3 class="font-semibold text-xs md:text-sm lg:text-base text-white text-center leading-tight px-1">
                                            Kontrak
                                        </h3>
                                    </div>
                                </a>
                            </div>

                            <!-- Deals & Voucher -->
                            <div class="snap-start">
                                <a href="{{ route('deals') }}" class="block">
                                    <div class="service-card-container rounded-lg p-2 md:p-3 lg:p-5 shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer group flex flex-col items-center justify-center hover:scale-105" style="background-color: #FF9D23;">
                                        <div style="background-color: rgba(193, 70, 0, 0.4);" class="w-8 h-8 md:w-10 md:h-10 lg:w-14 lg:h-14 rounded-lg flex items-center justify-center mb-1.5 md:mb-2 lg:mb-3 group-hover:opacity-80 transition-all flex-shrink-0">
                                            <svg class="w-4 h-4 md:w-5 md:h-5 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                            </svg>
                                        </div>
                                        <h3 class="font-semibold text-xs md:text-sm lg:text-base text-white text-center leading-tight px-1">
                                            Voucher
                                        </h3>
                                    </div>
                                </a>
                            </div>
                            
                            <!-- Reward -->
                            <div class="snap-start">
                                <a href="{{ route('dashboard.reward') }}" class="block">
                                    <div class="service-card-container rounded-lg p-2 md:p-3 lg:p-5 shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer group flex flex-col items-center justify-center hover:scale-105" style="background-color: #FF9D23;">
                                        <div style="background-color: rgba(193, 70, 0, 0.4);" class="w-8 h-8 md:w-10 md:h-10 lg:w-14 lg:h-14 rounded-lg flex items-center justify-center mb-1.5 md:mb-2 lg:mb-3 group-hover:opacity-80 transition-all flex-shrink-0">
                                            <svg class="w-4 h-4 md:w-5 md:h-5 lg:w-7 lg:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                            </svg>
                                        </div>
                                        <h3 class="font-semibold text-xs md:text-sm lg:text-base text-white text-center leading-tight px-1">
                                            Reward
                                        </h3>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
            
                    <!-- Navigation Dots -->
                    <div id="dots" class="flex justify-center gap-2 mt-4 lg:hidden">
                        <button class="dot w-2 h-2 rounded-full bg-gray-400 transition-all duration-300" data-index="0"></button>
                        <button class="dot w-2 h-2 rounded-full bg-gray-400 transition-all duration-300" data-index="1"></button>
                    </div>
                </div>
            </div>


            <!-- Why Choose Urban Office Section -->
            <div id="why-choose-section" class="px-4 md:px-8 pt-8 pb-2">
              <h2 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 mb-4 md:mb-4">
                Kenapa Pilih Urban Office?
              </h2>
            
              <div class="relative flex items-center">
                <!-- Tombol Kiri -->
                <button id="why-prev-btn" 
                  class="group absolute left-0 top-1/2 -translate-y-1/2 translate-x-1/4 z-10 inline-flex items-center justify-center w-10 h-10 md:w-11 md:h-11 bg-orange-500 hover:bg-orange-600 hover:scale-110 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 ease-out">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                  </svg>
                </button>
            
                <!-- Area Slider -->
                <div id="why-slider" class="w-full transition-opacity duration-300"></div>
            
                <!-- Tombol Kanan -->
                <button id="why-next-btn" 
                  class="group absolute right-0 top-1/2 -translate-y-1/2 translate-x-1/2 z-10 inline-flex items-center justify-center w-10 h-10 md:w-11 md:h-11 bg-orange-500 hover:bg-orange-600 hover:scale-110 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 ease-out">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </div>


            <!-- YouTube Popup Modal -->
            <div id="youtube-popup"
                    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4"
                    aria-hidden="true" style="display: none;">
                <div id="popup-content"
                    class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-200 scale-90 opacity-0">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-6 rounded-t-2xl">
                    <div class="flex items-center justify-center mb-2">
                        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </div>

                    </div>

                    <!-- Body -->
                    <div class="p-6 space-y-4">
                    <p class="text-gray-600 text-center text-sm md:text-base leading-relaxed">
                        Ingin melihat lebih detail tentang ruangan ini? Tonton video tour lengkap kami di YouTube!
                    </p>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button" id="youtube-open-btn"
                                class="flex-1 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10 16.5l6-4.5-6-4.5v9zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                        </svg>
                        Tonton di YouTube
                        </button>

                        <button type="button" id="youtube-close-btn"
                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105">
                        Tutup
                        </button>
                    </div>
                    </div>
                </div>
                </div>      

                <h2 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 mb-4 md:mb-4 mt-4 md:mt-4">
                    Pilih Ruang Kerja Anda
                </h2>

                <!-- Room Type Buttons -->
                <div class="flex flex-wrap gap-2 sm:gap-3 lg:gap-4 mb-4 sm:mb-6">
                    <button class="filter-btn active px-3 sm:px-4 lg:px-6 py-2 sm:py-2.5 lg:py-3 rounded-lg text-xs sm:text-sm lg:text-base font-medium transition-all duration-300 hover:scale-105 transform bg-orange-500 text-white hover:bg-orange-600" data-filter="all">
                        All Rooms
                    </button>
                    <button class="filter-btn px-3 sm:px-4 lg:px-6 py-2 sm:py-2.5 lg:py-3 rounded-lg text-xs sm:text-sm lg:text-base font-medium transition-all duration-300 hover:scale-105 transform bg-gray-200 text-gray-700 hover:bg-orange-500 hover:text-white" data-filter="Meeting Room">
                        Meeting Room
                    </button>
                    <button class="filter-btn px-3 sm:px-4 lg:px-6 py-2 sm:py-2.5 lg:py-3 rounded-lg text-xs sm:text-sm lg:text-base font-medium transition-all duration-300 hover:scale-105 transform bg-gray-200 text-gray-700 hover:bg-orange-500 hover:text-white" data-filter="Event Space">
                        Event Space
                    </button>
                    <button class="filter-btn px-3 sm:px-4 lg:px-6 py-2 sm:py-2.5 lg:py-3 rounded-lg text-xs sm:text-sm lg:text-base font-medium transition-all duration-300 hover:scale-105 transform bg-gray-200 text-gray-700 hover:bg-orange-500 hover:text-white" data-filter="Private Office">
                        Private Office
                    </button>
                    <button class="filter-btn px-3 sm:px-4 lg:px-6 py-2 sm:py-2.5 lg:py-3 rounded-lg text-xs sm:text-sm lg:text-base font-medium transition-all duration-300 hover:scale-105 transform bg-gray-200 text-gray-700 hover:bg-orange-500 hover:text-white" data-filter="Coworking Space">
                        Coworking Space
                    </button>
                </div>


                <!-- Image Gallery Container -->
                <div class="gallery-container relative overflow-hidden">
                    <!-- Navigation Buttons (Inside Image Area) -->
                <div class="absolute inset-0 flex items-center justify-between px-2 sm:px-3 md:px-4 z-20">
                    <!-- Previous Button -->
                    <button id="gallery-prev-btn" 
                        class="group inline-flex items-center justify-center w-10 h-10 sm:w-11 sm:h-11 
                               bg-orange-500 hover:bg-orange-600 hover:scale-110 text-white rounded-full 
                               shadow-lg hover:shadow-xl transition-all duration-300 ease-out">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 transition-transform duration-300 group-hover:-translate-x-0.5" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                
                    <!-- Next Button -->
                    <button id="gallery-next-btn" 
                        class="group inline-flex items-center justify-center w-10 h-10 sm:w-11 sm:h-11 
                               bg-orange-500 hover:bg-orange-600 hover:scale-110 text-white rounded-full 
                               shadow-lg hover:shadow-xl transition-all duration-300 ease-out">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 transition-transform duration-300 group-hover:translate-x-0.5" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <!-- Hidden counters (biarkan tetap) -->
                                    <div class="hidden">
                                        <span id="gallery-current-slide">1</span>
                                        <span id="gallery-total-slides">1</span>
                                    </div>
                </div>

                
                    <!-- Gallery Slides -->
                    <div id="gallery-slider" class="flex transition-transform duration-300 ease-in-out">
                        
                        <!-- Slide 1 -->
                        <div class="slide flex-shrink-0 w-full">
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 sm:gap-3 lg:gap-4">
                                <!-- Meeting Room Images -->
                                <div class="room-card" data-room-type="Meeting Room">
                                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md group hover:scale-[1.02] transition-all duration-300 ease-out h-full">
                                        <div class="relative aspect-[4/3] overflow-hidden">
                                            <img src="https://images.unsplash.com/photo-1556761175-b413da4baf72?w=800&h=600&fit=crop&auto=format" alt="Executive Boardroom" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                                            <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                                        </div>
                                        <div class="p-2 sm:p-3 lg:p-4 space-y-1.5 sm:space-y-2">
                                            <div class="inline-block bg-orange-500 text-white px-1.5 sm:px-2 py-0.5 sm:py-1 rounded text-xs font-medium shadow-sm">
                                                Meeting Room
                                            </div>
                                            <h3 class="font-semibold text-xs sm:text-sm lg:text-base text-gray-900 leading-tight line-clamp-2">
                                                Executive Boardroom
                                            </h3>
                                            <p class="text-xs sm:text-sm lg:text-base text-gray-600 leading-relaxed line-clamp-2">
                                                Ruang rapat eksklusif untuk keputusan strategis
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="room-card" data-room-type="Meeting Room">
                                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md group hover:scale-[1.02] transition-all duration-300 ease-out h-full">
                                        <div class="relative aspect-[4/3] overflow-hidden">
                                            <img src="https://images.unsplash.com/photo-1556761175-4b46a572b786?w=800&h=600&fit=crop&auto=format" alt="Modern Conference Room" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                                            <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                                        </div>
                                        <div class="p-2 sm:p-3 lg:p-4 space-y-1.5 sm:space-y-2">
                                            <div class="inline-block bg-orange-500 text-white px-1.5 sm:px-2 py-0.5 sm:py-1 rounded text-xs font-medium shadow-sm">
                                                Meeting Room
                                            </div>
                                            <h3 class="font-semibold text-xs sm:text-sm lg:text-base text-gray-900 leading-tight line-clamp-2">
                                                Modern Conference Room
                                            </h3>
                                            <p class="text-xs sm:text-sm lg:text-base text-gray-600 leading-relaxed line-clamp-2">
                                                Teknologi terdepan untuk presentasi profesional
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Event Space Images -->
                                <div class="room-card" data-room-type="Event Space">
                                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md group hover:scale-[1.02] transition-all duration-300 ease-out h-full">
                                        <div class="relative aspect-[4/3] overflow-hidden">
                                            <img src="https://images.unsplash.com/photo-1505236858219-8359eb29e329?w=800&h=600&fit=crop&auto=format" alt="Grand Event Hall" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                                            <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                                        </div>
                                        <div class="p-2 sm:p-3 lg:p-4 space-y-1.5 sm:space-y-2">
                                            <div class="inline-block bg-orange-500 text-white px-1.5 sm:px-2 py-0.5 sm:py-1 rounded text-xs font-medium shadow-sm">
                                                Event Space
                                            </div>
                                            <h3 class="font-semibold text-xs sm:text-sm lg:text-base text-gray-900 leading-tight line-clamp-2">
                                                Grand Event Hall
                                            </h3>
                                            <p class="text-xs sm:text-sm lg:text-base text-gray-600 leading-relaxed line-clamp-2">
                                                Kapasitas besar untuk acara dan seminar
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="room-card" data-room-type="Event Space">
                                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md group hover:scale-[1.02] transition-all duration-300 ease-out h-full">
                                        <div class="relative aspect-[4/3] overflow-hidden">
                                            <img src="https://images.unsplash.com/photo-1511578314322-379afb476865?w=800&h=600&fit=crop&auto=format" alt="Flexible Event Area" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                                            <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                                        </div>
                                        <div class="p-2 sm:p-3 lg:p-4 space-y-1.5 sm:space-y-2">
                                            <div class="inline-block bg-orange-500 text-white px-1.5 sm:px-2 py-0.5 sm:py-1 rounded text-xs font-medium shadow-sm">
                                                Event Space
                                            </div>
                                            <h3 class="font-semibold text-xs sm:text-sm lg:text-base text-gray-900 leading-tight line-clamp-2">
                                                Flexible Event Area
                                            </h3>
                                            <p class="text-xs sm:text-sm lg:text-base text-gray-600 leading-relaxed line-clamp-2">
                                                Ruang serbaguna dengan setup fleksibel
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="room-card" data-room-type="Private Office">
                                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md group hover:scale-[1.02] transition-all duration-300 ease-out h-full">
                                        <div class="relative aspect-[4/3] overflow-hidden">
                                            <img src="https://images.unsplash.com/photo-1497366811353-6870744d04b2?w=800&h=600&fit=crop&auto=format" alt="Premium Private Office" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                                            <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                                        </div>
                                        <div class="p-2 sm:p-3 lg:p-4 space-y-1.5 sm:space-y-2">
                                            <div class="inline-block bg-orange-500 text-white px-1.5 sm:px-2 py-0.5 sm:py-1 rounded text-xs font-medium shadow-sm">
                                                Private Office
                                            </div>
                                            <h3 class="font-semibold text-xs sm:text-sm lg:text-base text-gray-900 leading-tight line-clamp-2">
                                                Premium Private Office
                                            </h3>
                                            <p class="text-xs sm:text-sm lg:text-base text-gray-600 leading-relaxed line-clamp-2">
                                                Ruang kerja eksklusif dengan privasi maksimal
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Slide 2 -->
                        <div class="slide flex-shrink-0 w-full">
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 sm:gap-3 lg:gap-4">
                                <!-- Private Office Images -->
                                <div class="room-card" data-room-type="Private Office">
                                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md group hover:scale-[1.02] transition-all duration-300 ease-out h-full">
                                        <div class="relative aspect-[4/3] overflow-hidden">
                                            <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&h=600&fit=crop&auto=format" alt="Executive Suite" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                                            <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                                        </div>
                                        <div class="p-2 sm:p-3 lg:p-4 space-y-1.5 sm:space-y-2">
                                            <div class="inline-block bg-orange-500 text-white px-1.5 sm:px-2 py-0.5 sm:py-1 rounded text-xs font-medium shadow-sm">
                                                Private Office
                                            </div>
                                            <h3 class="font-semibold text-xs sm:text-sm lg:text-base text-gray-900 leading-tight line-clamp-2">
                                                Executive Suite
                                            </h3>
                                            <p class="text-xs sm:text-sm lg:text-base text-gray-600 leading-relaxed line-clamp-2">
                                                Kantor bergengsi untuk kebutuhan bisnis premium
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Coworking Space Images -->
                                <div class="room-card" data-room-type="Coworking Space">
                                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md group hover:scale-[1.02] transition-all duration-300 ease-out h-full">
                                        <div class="relative aspect-[4/3] overflow-hidden">
                                            <img src="https://images.unsplash.com/photo-1497366754035-f200968a6e72?w=800&h=600&fit=crop&auto=format" alt="Open Collaborative Space" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                                            <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                                        </div>
                                        <div class="p-2 sm:p-3 lg:p-4 space-y-1.5 sm:space-y-2">
                                            <div class="inline-block bg-orange-500 text-white px-1.5 sm:px-2 py-0.5 sm:py-1 rounded text-xs font-medium shadow-sm">
                                                Coworking Space
                                            </div>
                                            <h3 class="font-semibold text-xs sm:text-sm lg:text-base text-gray-900 leading-tight line-clamp-2">
                                                Open Collaborative Space
                                            </h3>
                                            <p class="text-xs sm:text-sm lg:text-base text-gray-600 leading-relaxed line-clamp-2">
                                                Lingkungan kerja dinamis dan kolaboratif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="room-card" data-room-type="Coworking Space">
                                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md group hover:scale-[1.02] transition-all duration-300 ease-out h-full">
                                        <div class="relative aspect-[4/3] overflow-hidden">
                                            <img src="https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=800&h=600&fit=crop&auto=format" alt="Creative Work Hub" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                                            <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                                        </div>
                                        <div class="p-2 sm:p-3 lg:p-4 space-y-1.5 sm:space-y-2">
                                            <div class="inline-block bg-orange-500 text-white px-1.5 sm:px-2 py-0.5 sm:py-1 rounded text-xs font-medium shadow-sm">
                                                Coworking Space
                                            </div>
                                            <h3 class="font-semibold text-xs sm:text-sm lg:text-base text-gray-900 leading-tight line-clamp-2">
                                                Creative Work Hub
                                            </h3>
                                            <p class="text-xs sm:text-sm lg:text-base text-gray-600 leading-relaxed line-clamp-2">
                                                Inspirasi dan networking dalam satu tempat
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Promo Section Banner -->
            @if(isset($sectionBanners) && $sectionBanners->count() > 0)
                <div class="w-full pt-2 md:pt-4 pb-2">
                    <div class="max-w-full mx-auto px-4">
                        <h2 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 mb-4 md:mb-6">
                            Promo Terbatas
                        </h2>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 lg:gap-4">
                            @foreach($sectionBanners as $banner)
                                <div class="w-full bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md group cursor-pointer transition-all duration-300 hover:scale-[1.02]">
                                    <div class="relative w-full overflow-hidden" style="padding-bottom: 56.25%;">
                                        <img src="{{ $banner->image_url }}" alt="{{ $banner->name }}" class="absolute inset-0 w-full h-full object-cover">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Latest Information -->
            <div class="w-full pt-2 md:pt-4 pb-8">
                <div class="max-w-full mx-auto px-4">
                    
                    <!-- Title -->
                    <h2 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 mb-4 md:mb-6">
                        Informasi Terkait
                    </h2>
                </div>
            
                <!-- Info Slider Container with Buttons -->
                <div class="relative px-4">
                    <!-- Cards Container -->
                    <div class="info-container relative overflow-hidden">
                        <div id="info-slider" class="transition-opacity duration-500 ease-in-out">
                            <!-- Slides will be generated by JavaScript -->
                        </div>
                    </div>
            
                    <!-- Previous Button - Left Side (Outside Container) -->
                    <button id="info-prev-btn" class="group absolute left-0 top-1/2 -translate-y-1/2 translate-x-1/4 z-10 inline-flex items-center justify-center w-10 h-10 md:w-11 md:h-11 bg-orange-500 hover:bg-orange-600 hover:scale-110 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 ease-out">
                        <svg class="w-5 h-5 md:w-6 md:h-6 transition-transform duration-300 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
            
                    <!-- Next Button - Right Side (Outside Container) -->
                    <button id="info-next-btn" class="group absolute right-0 top-1/2 -translate-y-1/2 translate-x-1/2 z-10 inline-flex items-center justify-center w-10 h-10 md:w-11 md:h-11 bg-orange-500 hover:bg-orange-600 hover:scale-110 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 ease-out">
                        <svg class="w-5 h-5 md:w-6 md:h-6 transition-transform duration-300 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

    @if(isset($popupBanners) && $popupBanners->count() > 0)
        @include('layouts.components.promo-modal', ['banners' => $popupBanners])
    @endif

    <script>
        // Animation timers
        let isVisible = false;
        let heroVisible = false;
        let servicesVisible = false;
        let whyChooseVisible = false;
        let infoVisible = false;
        let showAll = false;
        let selectedRoomType = null;

        // Slider variables
        let currentSlide = 0;
        let totalSlides = 0;

        // Store original room cards data
        let originalRoomCards = [];

        let currentYouTubeURL = '';

        // Initialize animations
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                isVisible = true;
                const userStatus = document.getElementById('user-status');
                if (userStatus) {
                    userStatus.classList.remove('opacity-0', '-translate-y-4');
                    userStatus.classList.add('opacity-100', 'translate-y-0');
                }
            }, 100);

            setTimeout(() => {
                heroVisible = true;
                const heroSection = document.getElementById('hero-section');
                if (heroSection) {
                    heroSection.classList.remove('opacity-0', 'translate-y-6');
                    heroSection.classList.add('opacity-100', 'translate-y-0');
                }
            }, 300);

            setTimeout(() => {
                servicesVisible = true;
                const servicesGrid = document.getElementById('services-grid');
                if (servicesGrid) {
                    servicesGrid.classList.remove('opacity-0', 'translate-y-6');
                    servicesGrid.classList.add('opacity-100', 'translate-y-0');
                }
            }, 500);

            setTimeout(() => {
                whyChooseVisible = true;
                const whyChooseSection = document.getElementById('why-choose-section');
                if (whyChooseSection) {
                    whyChooseSection.classList.remove('opacity-0', 'translate-y-6');
                    whyChooseSection.classList.add('opacity-100', 'translate-y-0');
                }
            }, 700);

            setTimeout(() => {
                infoVisible = true;
                const infoSection = document.getElementById('info-section');
                if (infoSection) {
                    infoSection.classList.remove('opacity-0', 'translate-y-6');
                    infoSection.classList.add('opacity-100', 'translate-y-0');
                }
            }, 900);

            // Initialize room filtering and slider
            initializeRoomFiltering();
            initializeSlider();
        });

        // Slider functionality
        function initializeSlider() {
            const slider = document.getElementById('gallery-slider');
            const prevBtn = document.getElementById('gallery-prev-btn');
            const nextBtn = document.getElementById('gallery-next-btn');
            const currentSlideSpan = document.getElementById('gallery-current-slide');
            const totalSlidesSpan = document.getElementById('gallery-total-slides');

            if (!slider || !prevBtn || !nextBtn || !currentSlideSpan || !totalSlidesSpan) {
                return;
            }

            // Event listeners for navigation buttons
            prevBtn.addEventListener('click', () => {
                if (currentSlide > 0) {
                    currentSlide--;
                    updateSlider();
                    updateNavigationButtons();
                }
            });

            nextBtn.addEventListener('click', () => {
                if (currentSlide < totalSlides - 1) {
                    currentSlide++;
                    updateSlider();
                    updateNavigationButtons();
                }
            });

            // Touch/swipe support
            let startX = 0;
            let endX = 0;

            slider.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
            });

            slider.addEventListener('touchend', (e) => {
                endX = e.changedTouches[0].clientX;
                handleSwipe();
            });

            function handleSwipe() {
                const swipeThreshold = 50;
                const diff = startX - endX;

                if (Math.abs(diff) > swipeThreshold) {
                    if (diff > 0 && currentSlide < totalSlides - 1) {
                        currentSlide++;
                    } else if (diff < 0 && currentSlide > 0) {
                        currentSlide--;
                    }
                    updateSlider();
                    updateNavigationButtons();
                }
            }
        }

        function calculateSlides() {
            const slider = document.getElementById('gallery-slider');
            if (!slider) return;

            // Get all room cards from original stored data or from DOM
            if (originalRoomCards.length === 0) {
                const allCards = Array.from(document.querySelectorAll('.room-card'));
                // Store original cards with their HTML and data
                originalRoomCards = allCards.map(card => ({
                    html: card.outerHTML,
                    roomType: card.getAttribute('data-room-type')
                }));
            }

            // Filter cards based on selected room type
            let visibleCards = originalRoomCards.filter(cardData => {
                if (!selectedRoomType) {
                    return true; // Show all
                }
                return cardData.roomType === selectedRoomType;
            });

            // Clear existing slides
            slider.innerHTML = '';

            if (visibleCards.length === 0) {
                totalSlides = 1;
                const emptySlide = document.createElement('div');
                emptySlide.className = 'slide flex-shrink-0 w-full';
                emptySlide.innerHTML = `
                    <div class="flex items-center justify-center h-64 text-gray-500">
                        <p class="text-sm md:text-base">Tidak ada ruangan yang tersedia untuk kategori ini.</p>
                    </div>
                `;
                slider.appendChild(emptySlide);
                return;
            }

            // Calculate items per slide based on screen width
            const getItemsPerSlide = () => {
                if (window.innerWidth >= 1280) return 5; // xl
                if (window.innerWidth >= 1024) return 4; // lg
                if (window.innerWidth >= 640) return 3;  // sm
                return 2; // mobile
            };

            const itemsPerSlide = getItemsPerSlide();
            totalSlides = Math.ceil(visibleCards.length / itemsPerSlide);

            // Create slides
            for (let i = 0; i < totalSlides; i++) {
                const slideDiv = document.createElement('div');
                slideDiv.className = 'slide flex-shrink-0 w-full';
                
                const gridDiv = document.createElement('div');
                gridDiv.className = 'grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 sm:gap-3 lg:gap-4';
                
                // Get cards for this slide
                const startIdx = i * itemsPerSlide;
                const endIdx = Math.min(startIdx + itemsPerSlide, visibleCards.length);
                const slideCards = visibleCards.slice(startIdx, endIdx);
                
                // Create cards from stored HTML
                slideCards.forEach(cardData => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = cardData.html;
                    const card = tempDiv.firstElementChild;
                    card.style.display = 'block';
                    card.classList.remove('hidden');
                    gridDiv.appendChild(card);
                });
                
                slideDiv.appendChild(gridDiv);
                slider.appendChild(slideDiv);
            }

            // Reset to first slide
            currentSlide = 0;
        }

        function updateSlider() {
            const slider = document.getElementById('gallery-slider');
            const currentSlideSpan = document.getElementById('gallery-current-slide');
            const totalSlidesSpan = document.getElementById('gallery-total-slides');

            if (slider && currentSlideSpan && totalSlidesSpan) {
                const translateX = -currentSlide * 100;
                slider.style.transform = `translateX(${translateX}%)`;
                
                currentSlideSpan.textContent = currentSlide + 1;
                totalSlidesSpan.textContent = totalSlides;
            }
        }

        function updateNavigationButtons() {
          const prevBtn = document.getElementById('gallery-prev-btn');
          const nextBtn = document.getElementById('gallery-next-btn');
        
          if (!prevBtn || !nextBtn) return;
        
          // Sembunyikan tombol prev di slide pertama
          if (currentSlide === 0) {
            prevBtn.classList.add('opacity-0', 'pointer-events-none');
          } else {
            prevBtn.classList.remove('opacity-0', 'pointer-events-none');
          }
        
          // Sembunyikan tombol next di slide terakhir
          if (currentSlide === totalSlides - 1) {
            nextBtn.classList.add('opacity-0', 'pointer-events-none');
          } else {
            nextBtn.classList.remove('opacity-0', 'pointer-events-none');
          }
        }


        // Room filtering functionality
        function initializeRoomFiltering() {
            const filterButtons = document.querySelectorAll('.filter-btn');

            // Filter button functionality
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    
                    // Update button states
                    filterButtons.forEach(btn => {
                        btn.classList.remove('bg-orange-500', 'text-white', 'active');
                        btn.classList.add('bg-gray-200', 'text-gray-700');
                        
                        // Remove pulse indicator
                        const pulse = btn.querySelector('.animate-pulse');
                        if (pulse) {
                            pulse.remove();
                        }
                    });
                    
                    // Set active button
                    this.classList.remove('bg-gray-200', 'text-gray-700');
                    this.classList.add('bg-orange-500', 'text-white', 'active');
                    
                    // Add pulse indicator for specific filters
                    if (filter !== 'all') {
                        const pulse = document.createElement('span');
                        pulse.className = 'ml-2 inline-block w-2 h-2 bg-white rounded-full animate-pulse';
                        this.appendChild(pulse);
                    }
                    
                    selectedRoomType = filter === 'all' ? null : filter;
                    
                    // Rebuild slides based on filter
                    calculateSlides();
                    updateSlider();
                    updateNavigationButtons();
                });
            });

            // Initial setup
            calculateSlides();
            updateSlider();
            updateNavigationButtons();
        }

        // Responsive handling
        window.addEventListener('resize', () => {
            calculateSlides();
            updateSlider();
            updateNavigationButtons();
        });

        // Hero slider
        document.addEventListener("DOMContentLoaded", function () {
            const slides = document.querySelectorAll("#hero-section .hero-slide");
            if (slides.length === 0) return;
            
            let current = 0;

            setInterval(() => {
                slides[current].classList.remove("opacity-100");
                slides[current].classList.add("opacity-0");

                current = (current + 1) % slides.length;

                slides[current].classList.remove("opacity-0");
                slides[current].classList.add("opacity-100");
            }, 4500);
        });

        //slider latest info
        // Menggunakan logika yang sama dengan kode JavaScript Anda
        document.addEventListener("DOMContentLoaded", () => {
        const slider = document.getElementById("info-slider");
        const prevBtn = document.getElementById("info-prev-btn");
        const nextBtn = document.getElementById("info-next-btn");
        
        if (!slider || !prevBtn || !nextBtn) return;
        
        // Data cards
        const cardsData = [
            {
                title: "Tren Investasi 2025",
                description: "Investor global semakin memprioritaskan perusahaan yang berkomitmen pada praktik bisnis berkelanjutan.",
                image: "{{ asset('assets/SEO-URBAN-21-2.jpg') }}",
                url: "https://urbanoffice.co.id/tren-investasi-2025-apa-yang-harus-diperhatikan-pebisnis/"
            },
            {
                title: "Cash Flow Sehat Kunci Utama Manajemen Keuangan",
                description: "Untuk menjaga cash flow sehat, Anda harus selalu memantau angka ini secara rutin.",
                image: "{{ asset('assets/SEO-URBAN-20.jpg') }}",
                url: "https://urbanoffice.co.id/cash-flow-sehat-kunci-utama-manajemen-keuangan/"
            },
            {
                title: "Cash Flow Sehat Kunci Utama Manajemen Keuangan",
                description: "Untuk menjaga cash flow sehat, Anda harus selalu memantau angka ini secara rutin.",
                image: "{{ asset('assets/SEO-URBAN-19.jpg') }}",
                url: "https://urbanoffice.co.id/cash-flow-sehat-kunci-utama-manajemen-keuangan/"
            },
            {
                title: "Perbandingan Sistem Kerja",
                description: "Pelajari sistem kerja terbaru untuk membantu produktivitas pekerjaan Anda",
                image: "{{ asset('assets/SEO-URBAN-18.jpg') }}",
                url: "https://urbanoffice.co.id/perbandingan-sistem-kerja-remote-vs-hybrid-vs-kantor/"
            },
            {
                title: "Tips Manajemen Keuangan",
                description: "Kelola keuangan bisnis dengan lebih efektif dan efisien.",
                image: "{{ asset('assets/SEO-URBAN-16.jpg') }}",
                url: "https://urbanoffice.co.id/sri-mulyani-mundur-dan-ihsg-anjlok-apa-dampaknya/"
            }
        ];
    
        let currentIndex = 0;
        
        function getCardsPerView() {
            const w = window.innerWidth;
            if (w >= 1024) return 4;
            if (w >= 768) return 3;
            return 2;
        }
        
        function createCardHTML(card) {
            return `
                <div class="w-full">
                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 hover:scale-105 cursor-pointer h-full flex flex-col"
                        onclick="window.open('${card.url}', '_blank')">
                        <div class="relative w-full overflow-hidden" style="padding-bottom: 75%;">
                            <img src="${card.image}" alt="${card.title}" class="absolute inset-0 w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                        </div>
                        <div class="p-3 flex-grow">
                            <h3 class="font-semibold text-sm text-gray-900 mb-2 overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                ${card.title}
                            </h3>
                            <p class="text-xs text-gray-600 hidden md:block overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                ${card.description}
                            </p>
                        </div>
                    </div>
                </div>
            `;
        }
        
        function renderCards() {
            const cardsPerView = getCardsPerView();
            const visibleCards = cardsData.slice(currentIndex, currentIndex + cardsPerView);
            
            // Add fade out effect
            slider.style.opacity = '0.5';
            
            setTimeout(() => {
                slider.innerHTML = '';
                const gridDiv = document.createElement('div');
                
                gridDiv.className = 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 md:gap-3 lg:gap-4';
                
                visibleCards.forEach(card => {
                    gridDiv.insertAdjacentHTML('beforeend', createCardHTML(card));
                });
                
                slider.appendChild(gridDiv);
                
                // Fade back in
                requestAnimationFrame(() => {
                    slider.style.opacity = '1';
                });
                
                updateButtons();
            }, 200);
        }
        
        function updateButtons() {
            const cardsPerView = getCardsPerView();
            const isAtStart = currentIndex === 0;
            const isAtEnd = currentIndex + cardsPerView >= cardsData.length;
            
            // Hide all buttons first if all cards fit in one view
            if (cardsData.length <= cardsPerView) {
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
                return;
            }
            
            // Show/hide buttons based on position
            // At start: show only next button
            prevBtn.style.display = isAtStart ? 'none' : 'flex';
            
            // At end: show only prev button
            nextBtn.style.display = isAtEnd ? 'none' : 'flex';
        }
        
        function nextSlide() {
            const cardsPerView = getCardsPerView();
            if (currentIndex + cardsPerView < cardsData.length) {
                currentIndex++;
                renderCards();
            }
        }
        
        function prevSlide() {
            if (currentIndex > 0) {
                currentIndex--;
                renderCards();
            }
        }
        
        nextBtn.addEventListener("click", nextSlide);
        prevBtn.addEventListener("click", prevSlide);
        
        let resizeTimer;
        window.addEventListener("resize", () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                const cardsPerView = getCardsPerView();
                if (currentIndex + cardsPerView > cardsData.length) {
                    currentIndex = Math.max(0, cardsData.length - cardsPerView);
                }
                renderCards();
            }, 150);
        });
        
        renderCards();
    });
    
    //why choose urban section slider
    document.addEventListener("DOMContentLoaded", () => {
      const slider = document.getElementById("why-slider");
      const prevBtn = document.getElementById("why-prev-btn");
      const nextBtn = document.getElementById("why-next-btn");
    
      if (!slider || !prevBtn || !nextBtn) return;
    
      const cardsData = [
        {
          title: "Fleksibel dan Eksklusif",
          description: "Sekarang makin banyak bisnis beralih ke Virtual Office. Alasannya sederhana fleksibel, efisien, dan tetap terlihat profesional. Urban Office hadir sebagai solusi lengkap dengan cabang di Jakarta, Surabaya, Gresik, Jogja, dan kota lainnya.",
          image: "{{ asset('assets/thumbnail_new.jpg') }}",
          label: "Beralih ke Virtual Office",
          videoUrl: "https://youtube.com/shorts/cYG2XRJ1T_w?si=oiSn9_tT0h67kqBL"
        },
        {
          title: "Member Blip",
          description: "Temui Tri, Talent Development @blip dan anggota Urban Office!",
          image: "{{ asset('assets/thumbnail-5.jpg') }}",
          label: "Testimoni Member",
          videoUrl: "https://youtu.be/hrQ_nhjL0F4?si=dHSPcMTYdbxGhJcz"
        },
        {
          title: "Lokasi Strategis",
          description: "Meeting aman, rahasia terjaga, lebih enjoy dengan coffee break.",
          image: "{{ asset('assets/thumbnail-3.jpg') }}",
          label: "Privasi Terjamin",
          videoUrl: "https://youtu.be/me5pVm_R34o?si=OhNMcMQMFX47Xvyz"
        },
        {
          title: "Investasi Cuma Rp 800.000",
          description: "Dengan fasilitas lengkap, Urban Office terasa seperti kantor pribadi.",
          image: "{{ asset('assets/thumbnail-4.jpg') }}",
          label: "Paling Murah",
          videoUrl: "https://youtu.be/GObyygmSjYc?si=IkWR2tj0Woe0ds1z"
        }
      ];
    
      let currentIndex = 0;
    
      function getCardsPerView() {
        const w = window.innerWidth;
        if (w >= 1024) return 4;
        if (w >= 768) return 3;
        return 2;
      }
    
      function createCardHTML(card) {
        return `
          <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md group hover:scale-[1.02] transition-all duration-300 ease-out cursor-pointer"
               onclick="openYouTubePopup('${card.label}', '${card.videoUrl}')">
            <div class="relative aspect-[4/3] overflow-hidden">
              <img src="${card.image}" alt="${card.title}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
              <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
            </div>
            <div class="p-2 md:p-3 space-y-1.5">
              <div class="inline-block bg-orange-500 text-white px-1.5 py-0.5 rounded text-xs font-medium shadow-sm">${card.label}</div>
              <h3 class="font-semibold text-xs md:text-sm text-gray-900 leading-tight line-clamp-2">${card.title}</h3>
              <p class="text-xs text-gray-600 leading-relaxed line-clamp-2">${card.description}</p>
            </div>
            <div class="absolute inset-0 border border-orange-400 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
          </div>
        `;
      }
    
      function renderCards() {
        const cardsPerView = getCardsPerView();
        const visibleCards = cardsData.slice(currentIndex, currentIndex + cardsPerView);
    
        slider.style.opacity = "0.5";
    
        setTimeout(() => {
          slider.innerHTML = "";
          const gridDiv = document.createElement("div");
          gridDiv.className = "grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 md:gap-3 lg:gap-4";
          visibleCards.forEach(card => gridDiv.insertAdjacentHTML("beforeend", createCardHTML(card)));
          slider.appendChild(gridDiv);
          requestAnimationFrame(() => (slider.style.opacity = "1"));
          updateButtons();
        }, 200);
      }
    
      function updateButtons() {
        const cardsPerView = getCardsPerView();
        const atStart = currentIndex === 0;
        const atEnd = currentIndex + cardsPerView >= cardsData.length;
    
        // Kondisi tombol
        if (cardsData.length <= cardsPerView) {
          prevBtn.classList.add("hidden");
          nextBtn.classList.add("hidden");
        } else if (atStart) {
          prevBtn.classList.add("hidden");
          nextBtn.classList.remove("hidden");
        } else if (atEnd) {
          nextBtn.classList.add("hidden");
          prevBtn.classList.remove("hidden");
        } else {
          prevBtn.classList.remove("hidden");
          nextBtn.classList.remove("hidden");
        }
      }
    
      function nextSlide() {
        const cardsPerView = getCardsPerView();
        if (currentIndex + cardsPerView < cardsData.length) {
          currentIndex++;
          renderCards();
        }
      }
    
      function prevSlide() {
        if (currentIndex > 0) {
          currentIndex--;
          renderCards();
        }
      }
    
      nextBtn.addEventListener("click", nextSlide);
      prevBtn.addEventListener("click", prevSlide);
    
      window.addEventListener("resize", () => {
        clearTimeout(window._resizeTimer);
        window._resizeTimer = setTimeout(() => {
          const cardsPerView = getCardsPerView();
          if (currentIndex + cardsPerView > cardsData.length) {
            currentIndex = Math.max(0, cardsData.length - cardsPerView);
          }
          renderCards();
        }, 200);
      });
    
      renderCards();
    });

    //why chose urban section
    (function () {
    // simpan url yg akan dibuka
    window._youtubePopupUrl = null;
    window._youtubePopupTitle = null; // TAMBAHAN: simpan title juga
    
    function ensureInBody(popupEl) {
      if (!popupEl) return;
      if (popupEl.parentElement !== document.body) {
        document.body.appendChild(popupEl);
      }
    }
    
    window.openYouTubePopup = function(title, url) {
      const popup = document.getElementById('youtube-popup');
      const content = document.getElementById('popup-content');
      
      if (!popup || !content) return console.warn('Popup elements missing');
      
      // PENTING: Simpan URL saja (title tidak dipakai)
      window._youtubePopupUrl = url || null;
      
      // pindahkan popup ke body supaya fixed berfungsi ke viewport
      ensureInBody(popup);
      
      // RESET state sebelum buka popup baru (penting untuk mobile)
      content.classList.remove('scale-100', 'opacity-100');
      content.classList.add('scale-90', 'opacity-0');
      popup.classList.add('hidden');
      popup.style.display = 'none';
      
      // Tampilkan popup dengan animasi
      requestAnimationFrame(() => {
        // Tampilkan overlay
        popup.classList.remove('hidden');
        popup.style.display = 'flex';
        
        // Animasi masuk di frame berikutnya
        requestAnimationFrame(() => {
          content.classList.remove('scale-90', 'opacity-0');
          content.classList.add('scale-100', 'opacity-100');
        });
      });
      
      // lock scroll halaman
      document.documentElement.style.overflow = 'hidden';
      document.body.style.overflow = 'hidden';
    };
    
    window.closeYouTubePopup = function() {
      const popup = document.getElementById('youtube-popup');
      const content = document.getElementById('popup-content');
      if (!popup || !content) return;
      
      // reverse animation
      content.classList.remove('scale-100', 'opacity-100');
      content.classList.add('scale-90', 'opacity-0');
      
      // after animation hide overlay
      setTimeout(() => {
        popup.classList.add('hidden');
        popup.style.display = 'none';
        
        // RESET data setelah popup ditutup
        window._youtubePopupUrl = null;
        window._youtubePopupTitle = null;
        
        // unlock scroll
        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
      }, 200); // cocokkan dengan duration-200
    };
    
    // tombol "Tonton di YouTube"
    document.addEventListener('click', function (e) {
      if (e.target && (e.target.id === 'youtube-open-btn' || e.target.closest('#youtube-open-btn'))) {
        if (window._youtubePopupUrl) {
          window.open(window._youtubePopupUrl, '_blank');
        }
        closeYouTubePopup();
      }
      if (e.target && (e.target.id === 'youtube-close-btn' || e.target.closest('#youtube-close-btn'))) {
        closeYouTubePopup();
      }
    });
    
    // close when click on overlay background
    document.addEventListener('click', function (e) {
      const popup = document.getElementById('youtube-popup');
      if (!popup || popup.classList.contains('hidden')) return;
      // if clicked directly on overlay (not the content)
      if (e.target === popup) closeYouTubePopup();
    });
    
    // close on ESC
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        const popup = document.getElementById('youtube-popup');
        if (popup && !popup.classList.contains('hidden')) closeYouTubePopup();
      }
    });
})();

    //slider services grid
    document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('slider');
    const dots = document.querySelectorAll('.dot');
    
    if (!slider || dots.length === 0) {
        return;
    }
    
    let currentPage = 0;
    const totalPages = dots.length;
    
    // Update active dot
    function updateDots(pageIndex) {
        
        dots.forEach((dot, index) => {
            if (index === pageIndex) {
                dot.classList.remove('bg-gray-400', 'w-2');
                dot.classList.add('bg-orange-500', 'w-4');
            } else {
                dot.classList.remove('bg-orange-500', 'w-4');
                dot.classList.add('bg-gray-400', 'w-2');
            }
        });
    }
    
    // Get items in slider
    function getSliderItems() {
        return slider.querySelectorAll('.snap-start');
    }
    
    // Calculate items per page based on viewport
    function getItemsPerPage() {
        const sliderWidth = slider.offsetWidth;
        const items = getSliderItems();
        
        if (items.length === 0) return 1;
        
        const itemWidth = items[0].offsetWidth;
        const gap = 12; // gap-3 = 12px
        
        // Hitung berapa item yang muat per halaman
        let itemsPerPage = Math.floor(sliderWidth / (itemWidth + gap));
        return Math.max(1, itemsPerPage);
    }
    
    // Go to specific page
    function goToPage(pageIndex) {
        if (pageIndex < 0 || pageIndex >= totalPages) return;
        const items = getSliderItems();
        const itemsPerPage = getItemsPerPage();
        
        // Hitung item pertama pada halaman ini
        const firstItemIndex = pageIndex * itemsPerPage;
        
        if (firstItemIndex < items.length) {
            const targetItem = items[firstItemIndex];
            
            // Scroll ke item
            const scrollLeft = targetItem.offsetLeft - slider.offsetLeft;
            slider.scrollTo({
                left: scrollLeft,
                behavior: 'smooth'
            });
            
            currentPage = pageIndex;
            updateDots(pageIndex);
        }
    }
    
    // Touch/Swipe handling
    let touchStartX = 0;
    let touchEndX = 0;
    let scrollStart = 0;
    
    slider.addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX;
        scrollStart = slider.scrollLeft;
        
    });
    
    slider.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].clientX;
        const diff = touchStartX - touchEndX;
        // Threshold untuk swipe (30% dari lebar slider)
        const threshold = slider.offsetWidth * 0.3;
        
        if (Math.abs(diff) > threshold) {
            if (diff > 0) {
                // Swipe left - next
                
                if (currentPage < totalPages - 1) {
                    goToPage(currentPage + 1);
                } else {
                    // Sudah di halaman terakhir, snap kembali
                    goToPage(currentPage);
                }
            } else {
                // Swipe right - prev
                
                if (currentPage > 0) {
                    goToPage(currentPage - 1);
                } else {
                    // Sudah di halaman pertama, snap kembali
                    goToPage(currentPage);
                }
            }
        } else {
            // Kembali ke posisi semula jika swipe terlalu pendek
            
            goToPage(currentPage);
        }
    });
    
    // Click dots navigation
    dots.forEach((dot, index) => {
        dot.addEventListener('click', function() {
            
            goToPage(index);
        });
    });
    
    // Monitor scroll untuk auto-detect page
    let scrollTimeout;
    slider.addEventListener('scroll', function() {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function() {
            const items = getSliderItems();
            const itemsPerPage = getItemsPerPage();
            
            // Cari item yang paling dekat dengan posisi scroll
            let closestPage = 0;
            let closestDistance = Infinity;
            
            for (let i = 0; i < totalPages; i++) {
                const firstItemIndex = i * itemsPerPage;
                if (firstItemIndex < items.length) {
                    const item = items[firstItemIndex];
                    const distance = Math.abs(slider.scrollLeft - (item.offsetLeft - slider.offsetLeft));
                    
                    if (distance < closestDistance) {
                        closestDistance = distance;
                        closestPage = i;
                    }
                }
            }
            
            if (closestPage !== currentPage) {
                
                currentPage = closestPage;
                updateDots(closestPage);
            }
        }, 150);
    });
    
    // Handle resize
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            
            goToPage(currentPage);
        }, 250);
    });
    
    // Initialize
    updateDots(0);
    // Debug info setelah load
    setTimeout(function() {
    }, 100);
});
 
    </script>

    <style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .duration-300 {
        transition-duration: 300ms;
    }
    
    .duration-800 {
        transition-duration: 800ms;
    }
    
    .ease-out {
        transition-timing-function: cubic-bezier(0, 0, 0.2, 1);
    }
    
    /* Base styles */
    #services-grid {
        overflow-x: hidden;
    }
    
    #slider {
        scrollbar-width: none;
        -ms-overflow-style: none;
        max-width: 100%;
    }
    
    #slider::-webkit-scrollbar {
        display: none;
    }
    
    /* Fixed card size - MOBILE FIRST (default) */
    .service-card-container {
        width: 70px !important;
        height: 100px !important;
    }
    
    /* MOBILE ONLY (max-width: 767px) */
    @media (max-width: 767px) {
        #services-grid {
            max-width: 90vw;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        
        #services-grid > div {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        #slider {
            overflow-x: auto;
            overflow-y: hidden;
            max-width: 100%;
        }
        
        #slider > div {
            width: max-content;
        }
    }
    
    /* Tablet specific fixes */
        @media (min-width: 768px) and (max-width: 1023px) {
            .service-card-container {
                width: 115px;
                height: 135px;
            }
            
            /* Fix overflow di tablet */
            #services-grid {
                max-width: 100vw;
                overflow: hidden;
            }
            
            #services-grid > .relative {
                max-width: 100%;
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            #slider {
                margin-left: 0;
                margin-right: 0;
                padding-left: 0;
                padding-right: 0;
            }
            
            #slider > div {
                gap: 0.75rem;
            }
        }
    /* DESKTOP (1024px ke atas) */
    @media (min-width: 1024px) {
        .service-card-container {
            width: 125px !important;
            height: 140px !important;
        }
        
        #services-grid {
            max-width: 100vw;
        }
        
        #slider {
            overflow-x: visible;
        }
        
        #slider > div {
            width: 100%;
            display: flex;
            justify-content: center;
        }
    }
</style>

@if(isset($popupBanners) && $popupBanners->count() > 0)
    @include('layouts.components.promo-modal', ['banners' => $popupBanners])
@endif
@endsection