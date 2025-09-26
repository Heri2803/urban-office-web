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

                <!-- Background Slider Wrapper -->
                <div class="absolute inset-0">
                    <div class="absolute inset-0 bg-cover bg-center hero-slide opacity-100 transition-opacity duration-1000"
                        style="background-image: url('https://images.unsplash.com/photo-1590650046871-92c887180603?ixlib=rb-4.0.3&auto=format&fit=crop&w=1926&q=80')"></div>
                    <div class="absolute inset-0 bg-cover bg-center hero-slide opacity-0 transition-opacity duration-1000"
                        style="background-image: url('https://images.unsplash.com/photo-1556761175-b413da4baf72?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80')"></div>
                    <div class="absolute inset-0 bg-cover bg-center hero-slide opacity-0 transition-opacity duration-1000"
                        style="background-image: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1926&q=80')"></div>
                </div>

                <!-- Orange Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-orange-500 via-orange-400/50"></div>

                <!-- Content -->
                <div class="relative rounded-lg mx-1 sm:mx-4 md:mx-6 lg:mx-8 mt-6 overflow-hidden">
                    <div class="relative px-4 sm:px-6 md:px-12 py-4 sm:py-6 md:py-8 lg:py-16 xl:py-20">
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
            <div id="services-grid" class="px-4 md:px-8 py-8 transition-all duration-800 ease-out opacity-0 translate-y-6">
                <div class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-4 lg:grid-cols-4 gap-2 sm:gap-1 md:gap-5 max-w-sm sm:max-w-[280px] md:max-w-md lg:max-w-2xl mx-auto">
                    <!-- Booking -->
                    <a href="{{ route('dashboard.booking') }}" class="block">
                        <div class="bg-white rounded-lg p-1.5 sm:p-1.5 md:p-4 shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer group md:aspect-square aspect-square flex flex-col items-center justify-center hover:scale-105 md:w-full md:h-full">
                            <div class="w-5 h-5 sm:w-5 sm:h-5 md:w-8 md:h-8 bg-orange-100 rounded-lg flex items-center justify-center mb-1 sm:mb-1 md:mb-2 group-hover:bg-orange-200 transition-colors">
                                <svg class="w-2.5 h-2.5 sm:w-2.5 sm:h-2.5 md:w-4 md:h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-[10px] sm:text-[10px] md:text-xs text-gray-900 text-center leading-tight px-0.5">
                                Booking
                            </h3>
                        </div>
                    </a>

                    <!-- Call -->
                    <a href="{{ route('dashboard.calls') }}" class="block">
                        <div class="bg-white rounded-lg p-1.5 sm:p-1.5 md:p-4 shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer group md:aspect-square aspect-square flex flex-col items-center justify-center hover:scale-105 md:w-full md:h-full">
                            <div class="w-5 h-5 sm:w-5 sm:h-5 md:w-8 md:h-8 bg-blue-100 rounded-lg flex items-center justify-center mb-1 sm:mb-1 md:mb-2 group-hover:bg-blue-200 transition-colors">
                                <svg class="w-2.5 h-2.5 sm:w-2.5 sm:h-2.5 md:w-4 md:h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-[10px] sm:text-[10px] md:text-xs text-gray-900 text-center leading-tight px-0.5">
                                Call
                            </h3>
                        </div>
                    </a>

                    <!-- Mails -->
                    <a href="{{ route('dashboard.mails') }}" class="block">
                        <div class="bg-white rounded-lg p-1.5 sm:p-1.5 md:p-4 shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer group md:aspect-square aspect-square flex flex-col items-center justify-center hover:scale-105 md:w-full md:h-full">
                            <div class="w-5 h-5 sm:w-5 sm:h-5 md:w-8 md:h-8 bg-green-100 rounded-lg flex items-center justify-center mb-1 sm:mb-1 md:mb-2 group-hover:bg-green-200 transition-colors">
                                <svg class="w-2.5 h-2.5 sm:w-2.5 sm:h-2.5 md:w-4 md:h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-[10px] sm:text-[10px] md:text-xs text-gray-900 text-center leading-tight px-0.5">
                                Mails
                            </h3>
                        </div>
                    </a>

                    <!-- Invoice -->
                    <a href="{{ route('dashboard.invoice') }}" class="block">
                        <div class="bg-white rounded-lg p-1.5 sm:p-1.5 md:p-4 shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer group md:aspect-square aspect-square flex flex-col items-center justify-center hover:scale-105 md:w-full md:h-full">
                            <div class="w-5 h-5 sm:w-5 sm:h-5 md:w-8 md:h-8 bg-purple-100 rounded-lg flex items-center justify-center mb-1 sm:mb-1 md:mb-2 group-hover:bg-purple-200 transition-colors">
                                <svg class="w-2.5 h-2.5 sm:w-2.5 sm:h-2.5 md:w-4 md:h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-[10px] sm:text-[10px] md:text-xs text-gray-900 text-center leading-tight px-0.5">
                                Invoice
                            </h3>
                        </div>
                    </a>

                    <!-- Pendirian Badan Usaha -->
                    <a href="{{ route('dashboard.mitra') }}" class="block col-start-2 sm:col-start-2 md:col-start-2 lg:col-start-2">
                        <div class="bg-white rounded-lg p-1.5 sm:p-1.5 md:p-4 shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer group md:aspect-square aspect-square flex flex-col items-center justify-center hover:scale-105 md:w-full md:h-full">
                            <div class="w-5 h-5 sm:w-5 sm:h-5 md:w-8 md:h-8 bg-indigo-100 rounded-lg flex items-center justify-center mb-1 sm:mb-1 md:mb-2 group-hover:bg-indigo-200 transition-colors">
                                <svg class="w-2.5 h-2.5 sm:w-2.5 sm:h-2.5 md:w-4 md:h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-[10px] sm:text-[10px] md:text-xs text-gray-900 text-center leading-tight px-0.5">
                                Pendirian Badan Usaha
                            </h3>
                        </div>
                    </a>

                    <!-- Reward -->
                    <a href="{{ route('dashboard.reward') }}" class="block col-start-3 sm:col-start-4 md:col-start-3 lg:col-start-3">
                        <div class="bg-white rounded-lg p-1.5 sm:p-1.5 md:p-4 shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer group md:aspect-square aspect-square flex flex-col items-center justify-center hover:scale-105 md:w-full md:h-full">
                            <div class="w-5 h-5 sm:w-5 sm:h-5 md:w-8 md:h-8 bg-yellow-100 rounded-lg flex items-center justify-center mb-1 sm:mb-1 md:mb-2 group-hover:bg-yellow-200 transition-colors">
                                <svg class="w-2.5 h-2.5 sm:w-2.5 sm:h-2.5 md:w-4 md:h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-[10px] sm:text-[10px] md:text-xs text-gray-900 text-center leading-tight px-0.5">
                                Reward
                            </h3>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Why Choose Urban Office Section -->
            <div id="why-choose-section" class="px-4 md:px-8 py-8 transition-all duration-800 ease-out opacity-0 translate-y-6">
                <h2 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 mb-6 md:mb-8">
                    Kenapa Pilih Urban Office?
                </h2>

                <div class="relative mb-6 md:mb-8">
                    <!-- Cards Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4">
                        <!-- Executive Boardroom -->
                        <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md group hover:scale-[1.02] transition-all duration-300 ease-out">
                            <div class="relative aspect-[4/3] overflow-hidden">
                                <img src="{{ asset('assets/SEO-URBAN-21-2.jpg') }}" alt="Executive Boardroom" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                            </div>
                            <div class="p-2 md:p-3 space-y-1.5">
                                <div class="inline-block bg-orange-500 text-white px-1.5 py-0.5 rounded text-xs font-medium shadow-sm">
                                    Meeting Room
                                </div>
                                <h3 class="font-semibold text-xs md:text-sm text-gray-900 leading-tight line-clamp-2">
                                    Executive Boardroom
                                </h3>
                                <p class="text-xs text-gray-600 leading-relaxed line-clamp-2">
                                    Ruang rapat eksklusif untuk keputusan strategis
                                </p>
                            </div>
                            <div class="absolute inset-0 border border-orange-400 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                        </div>

                        <!-- Grand Event Hall -->
                        <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md group hover:scale-[1.02] transition-all duration-300 ease-out">
                            <div class="relative aspect-[4/3] overflow-hidden">
                                <img src="{{ asset('assets/SEO-URBAN-21-2.jpg') }}" alt="Grand Event Hall" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                            </div>
                            <div class="p-2 md:p-3 space-y-1.5">
                                <div class="inline-block bg-orange-500 text-white px-1.5 py-0.5 rounded text-xs font-medium shadow-sm">
                                    Event Space
                                </div>
                                <h3 class="font-semibold text-xs md:text-sm text-gray-900 leading-tight line-clamp-2">
                                    Grand Event Hall
                                </h3>
                                <p class="text-xs text-gray-600 leading-relaxed line-clamp-2">
                                    Kapasitas besar untuk acara dan seminar
                                </p>
                            </div>
                            <div class="absolute inset-0 border border-orange-400 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                        </div>

                        <!-- Premium Private Office -->
                        <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md group hover:scale-[1.02] transition-all duration-300 ease-out">
                            <div class="relative aspect-[4/3] overflow-hidden">
                                <img src="{{ asset('assets/SEO-URBAN-21-2.jpg') }}" alt="Premium Private Office" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                            </div>
                            <div class="p-2 md:p-3 space-y-1.5">
                                <div class="inline-block bg-orange-500 text-white px-1.5 py-0.5 rounded text-xs font-medium shadow-sm">
                                    Private Office
                                </div>
                                <h3 class="font-semibold text-xs md:text-sm text-gray-900 leading-tight line-clamp-2">
                                    Premium Private Office
                                </h3>
                                <p class="text-xs text-gray-600 leading-relaxed line-clamp-2">
                                    Ruang kerja eksklusif dengan privasi maksimal
                                </p>
                            </div>
                            <div class="absolute inset-0 border border-orange-400 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                        </div>

                        <!-- Open Collaborative Space -->
                        <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md group hover:scale-[1.02] transition-all duration-300 ease-out">
                            <div class="relative aspect-[4/3] overflow-hidden">
                                <img src="{{ asset('assets/SEO-URBAN-21-2.jpg') }}" alt="Open Collaborative Space" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                            </div>
                            <div class="p-2 md:p-3 space-y-1.5">
                                <div class="inline-block bg-orange-500 text-white px-1.5 py-0.5 rounded text-xs font-medium shadow-sm">
                                    Coworking Space
                                </div>
                                <h3 class="font-semibold text-xs md:text-sm text-gray-900 leading-tight line-clamp-2">
                                    Open Collaborative Space
                                </h3>
                                <p class="text-xs text-gray-600 leading-relaxed line-clamp-2">
                                    Lingkungan kerja dinamis dan kolaboratif
                                </p>
                            </div>
                            <div class="absolute inset-0 border border-orange-400 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                        </div>
                    </div>
                </div>

                <h2 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 mb-4 md:mb-6">
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

                <!-- Slider Navigation -->
                <div class="flex justify-between items-center mb-4 sm:mb-6">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <button id="gallery-prev-btn" class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gray-200 hover:bg-orange-500 hover:text-white text-gray-600 rounded-full font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button id="gallery-next-btn" class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gray-200 hover:bg-orange-500 hover:text-white text-gray-600 rounded-full font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Slide Indicator -->
                    <div class="flex items-center gap-1 sm:gap-2">
                        <span id="gallery-current-slide" class="text-xs sm:text-sm lg:text-base text-gray-600 font-medium">1</span>
                        <span class="text-xs sm:text-sm lg:text-base text-gray-400">/</span>
                        <span id="gallery-total-slides" class="text-xs sm:text-sm lg:text-base text-gray-600 font-medium">1</span>
                    </div>
                </div>

                <!-- Image Gallery Container -->
                <div class="gallery-container relative overflow-hidden mb-6 sm:mb-8 lg:mb-10">
                    <div id="gallery-slider" class="flex transition-transform duration-300 ease-in-out">
                        
                        <!-- Slide 1 -->
                        <div class="slide flex-shrink-0 w-full">
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 sm:gap-3 lg:gap-4">
                                <!-- Meeting Room Images -->
                                <div class="room-card" data-room-type="Meeting Room">
                                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md group hover:scale-[1.02] transition-all duration-300 ease-out h-full">
                                        <div class="relative aspect-[4/3] overflow-hidden">
                                            <img src="{{ asset('assets/SEO-URBAN-21-2.jpg') }}" alt="Executive Boardroom" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
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
                                            <img src="{{ asset('assets/SEO-URBAN-21-2.jpg') }}" alt="Modern Conference Room" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
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
                                            <img src="{{ asset('assets/SEO-URBAN-21-2.jpg') }}" alt="Grand Event Hall" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
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
                                            <img src="{{ asset('assets/SEO-URBAN-21-2.jpg') }}" alt="Flexible Event Area" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
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
                                            <img src="{{ asset('assets/SEO-URBAN-21-2.jpg') }}" alt="Premium Private Office" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
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
                                            <img src="{{ asset('assets/SEO-URBAN-21-2.jpg') }}" alt="Executive Suite" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
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
                                            <img src="{{ asset('assets/SEO-URBAN-21-2.jpg') }}" alt="Open Collaborative Space" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
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
                                            <img src="{{ asset('assets/SEO-URBAN-21-2.jpg') }}" alt="Creative Work Hub" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
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

            <!-- Latest Information -->
            <div id="info-section" class="px-4 md:px-8 py-8 transition-all duration-800 ease-out opacity-100 w-full max-w-full">
                <h2 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 mb-6 md:mb-8">
                    Informasi Terkait
                </h2>

                <div class="relative w-full">
                    <div class="overflow-hidden w-full">
                        <div id="info-slider" class="flex transition-transform duration-700 ease-out w-full">
                            
                            <div class="flex-shrink-0 w-1/2 md:w-1/3 lg:w-1/4 px-1">
                                <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 hover:scale-105 cursor-pointer h-full flex flex-col"
                                    onclick="window.open('https://urbanoffice.co.id/tren-investasi-2025-apa-yang-harus-diperhatikan-pebisnis/', '_blank')">
                                    <div class="aspect-video relative">
                                        <img src="{{ asset('assets/SEO-URBAN-21-2.jpg') }}" 
                                            alt="Tren Investasi" 
                                            class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                                    </div>
                                    <div class="p-2 md:p-4 lg:p-6 flex-grow">
                                        <h3 class="font-semibold text-xs md:text-base lg:text-lg text-gray-900 mb-1 md:mb-2 leading-tight">
                                            Tren Investasi 2025
                                        </h3>
                                        <p class="text-gray-600 text-xs md:text-sm leading-relaxed hidden md:block">
                                            Investor global semakin memprioritaskan perusahaan yang berkomitmen pada praktik bisnis berkelanjutan.
                                        </p>
                                        <p class="text-gray-600 text-xs leading-tight md:hidden">
                                            Pasca-pandemi, sektor kesehatan dan bioteknologi menjadi fokus utama.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-shrink-0 w-1/2 md:w-1/3 lg:w-1/4 px-1">
                                <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 hover:scale-105 cursor-pointer h-full flex flex-col"
                                    onclick="window.open('https://urbanoffice.co.id/cash-flow-sehat-kunci-utama-manajemen-keuangan/', '_blank')">
                                    <div class="aspect-video relative">
                                        <img src="{{ asset('assets/SEO-URBAN-20.jpg') }}" 
                                            alt="Cash Flow Sehat" 
                                            class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                                    </div>
                                    <div class="p-2 md:p-4 lg:p-6 flex-grow">
                                        <h3 class="font-semibold text-xs md:text-base lg:text-lg text-gray-900 mb-1 md:mb-2 leading-tight">
                                            Cash Flow Sehat Kunci Utama Manajemen Keuangan
                                        </h3>
                                        <p class="text-gray-600 text-xs md:text-sm leading-relaxed hidden md:block">
                                            Untuk menjaga cash flow sehat, Anda harus selalu memantau angka ini secara rutin,
                                        </p>
                                        <p class="text-gray-600 text-xs leading-tight md:hidden">
                                            Buat anggaran yang terperinci untuk semua pengeluaran. Kelompokkan biaya berdasarkan prioritas...
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-shrink-0 w-1/2 md:w-1/3 lg:w-1/4 px-1">
                                <div class="bg-white rounded-lg ... h-full flex flex-col ">
                                    <div class="p-2 md:p-4 lg:p-6 flex-grow">
                                    ...
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-1/2 md:w-1/3 lg:w-1/4 px-1">
                                <div class="bg-white rounded-lg ... h-full flex flex-col">
                                    ...
                                    <div class="p-2 md:p-4 lg:p-6 flex-grow">
                                    ...
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-1/2 md:w-1/3 lg:w-1/4 px-1">
                                <div class="bg-white rounded-lg ... h-full flex flex-col">
                                    ...
                                    <div class="p-2 md:p-4 lg:p-6 flex-grow">
                                    ...
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <button id="prevBtn" class="absolute left-0 top-1/2 -translate-y-1/2 bg-orange-500 text-white px-3 py-2 rounded-full shadow hover:bg-orange-600 transition z-10">
                        ‹
                    </button>
                    <button id="nextBtn" class="absolute right-0 top-1/2 -translate-y-1/2 bg-orange-500 text-white px-3 py-2 rounded-full shadow hover:bg-orange-600 transition z-10">
                        ›
                    </button>
                </div>
            </div>
        </div>
    </div>

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
        let filteredSlides = [];

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

            // Calculate total slides and initialize
            calculateSlides();
            updateSlider();
            updateNavigationButtons();

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

            // Touch/swipe support (optional)
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
                        // Swipe left - next slide
                        currentSlide++;
                    } else if (diff < 0 && currentSlide > 0) {
                        // Swipe right - previous slide
                        currentSlide--;
                    }
                    updateSlider();
                    updateNavigationButtons();
                }
            }
        }

        function calculateSlides() {
            const slides = document.querySelectorAll('.slide');
            filteredSlides = Array.from(slides).filter(slide => {
                const visibleCards = slide.querySelectorAll('.room-card:not(.hidden)');
                return visibleCards.length > 0;
            });
            totalSlides = filteredSlides.length;
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

            if (prevBtn && nextBtn) {
                prevBtn.disabled = currentSlide === 0;
                nextBtn.disabled = currentSlide === totalSlides - 1 || totalSlides <= 1;
                
                if (prevBtn.disabled) {
                    prevBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    prevBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
                
                if (nextBtn.disabled) {
                    nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }

        // Room filtering functionality
        function initializeRoomFiltering() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const seeAllBtn = document.getElementById('see-all-btn');

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
                    filterRooms();
                    
                    // Reset slider to first slide after filtering
                    currentSlide = 0;
                    calculateSlides();
                    updateSlider();
                    updateNavigationButtons();
                });
            });

            // Initial filter setup
            filterRooms();
            calculateSlides();
        }

            function filterRooms() {
                const roomCards = document.querySelectorAll('.room-card');
                
                roomCards.forEach((card, index) => {
                    const roomType = card.getAttribute('data-room-type');
                    let shouldShow = true;
                    
                    // Filter by room type
                    if (selectedRoomType && roomType !== selectedRoomType) {
                        shouldShow = false;
                    }
                    
                    if (shouldShow) {
                        // Show the card
                        card.style.display = 'block';
                        card.classList.remove('hidden');
                    } else {
                        // Hide the card
                        card.style.display = 'none';
                        card.classList.add('hidden');
                    }
                });

                // Recalculate slides after filtering
                setTimeout(() => {
                    calculateSlides();
                    updateSlider();
                    updateNavigationButtons();
                }, 50);
            }

            // Responsive handling
            window.addEventListener('resize', () => {
                calculateSlides();
                updateSlider();
                updateNavigationButtons();
            });

            document.addEventListener("DOMContentLoaded", function () {
            const slides = document.querySelectorAll("#hero-section .hero-slide");
            let current = 0;

            setInterval(() => {
                slides[current].classList.remove("opacity-100");
                slides[current].classList.add("opacity-0");

                current = (current + 1) % slides.length;

                slides[current].classList.remove("opacity-0");
                slides[current].classList.add("opacity-100");
            }, 4500); // ganti setiap 5 detik
        });

        //slider latest info
        // Menggunakan logika yang sama dengan kode JavaScript Anda
        document.addEventListener("DOMContentLoaded", () => {
            const track = document.getElementById("info-slider"); // flex container
            const cards = track ? track.children.length : 0;

            let currentIndex = 0;

            // jumlah card per viewport - sesuaikan dengan breakpoint Tailwind
            const getVisibleCount = () => {
                if (window.innerWidth >= 1024) return 4; // desktop (lg)
                if (window.innerWidth >= 768) return 3;  // tablet (md)
                if (window.innerWidth >= 640) return 2;  // small tablet (sm)
                return 2;                                // mobile (2 cards untuk mobile)
            };

            const updateSlider = () => {
                if (!track) return;

                const visible = getVisibleCount();
                const maxIndex = Math.max(0, cards - visible); // batas akhir: card terakhir tetap pas

                if (currentIndex < 0) currentIndex = 0;
                if (currentIndex > maxIndex) currentIndex = maxIndex;

                // hitung lebar card relatif (%)
                const cardWidth = 100 / visible;
                const translate = -(cardWidth * currentIndex);

                track.style.transform = `translateX(${translate}%)`;
                
                // Update button visibility
                updateButtonVisibility();
            };
            
            const updateButtonVisibility = () => {
                const nextBtn = document.getElementById("nextBtn");
                const prevBtn = document.getElementById("prevBtn");
                const visible = getVisibleCount();
                const maxIndex = Math.max(0, cards - visible);
                
                if (prevBtn) {
                    prevBtn.style.display = currentIndex <= 0 ? 'none' : 'block';
                }
                
                if (nextBtn) {
                    nextBtn.style.display = currentIndex >= maxIndex ? 'none' : 'block';
                }
            };

            const nextBtn = document.getElementById("nextBtn");
            const prevBtn = document.getElementById("prevBtn");

            if (nextBtn) {
                nextBtn.addEventListener("click", () => {
                    const visible = getVisibleCount();
                    const maxIndex = Math.max(0, cards - visible);
                    if (currentIndex < maxIndex) {
                        currentIndex++;
                        updateSlider();
                    }
                });
            }

            if (prevBtn) {
                prevBtn.addEventListener("click", () => {
                    if (currentIndex > 0) {
                        currentIndex--;
                        updateSlider();
                    }
                });
            }

            window.addEventListener("resize", updateSlider);
            updateSlider(); // Initialize
        });
    </script>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
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
    </style>
