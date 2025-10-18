@extends('layouts.app')

@section('content')
<div x-data="{ isVisible: false }" x-init="setTimeout(() => isVisible = true, 100)" class="flex min-h-screen bg-gray-50">

    {{-- Content --}}
    <div class="flex-1 ml-0 md:ml-52 lg:ml-64 xl:ml-64 mb-18 pb-14 md:pb-0">

        {{-- Header with Background - Reduced Size by 30% --}}
        <div 
            class="relative h-32 md:h-44 lg:h-56 xl:h-68 overflow-hidden"
            style="background-image: url('https://images.unsplash.com/photo-1556761175-b413da4baf72?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); background-size: cover; background-position: center;"
        >
            {{-- Orange Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-orange-500/90 via-orange-500/70 to-orange-400/50"></div>

            {{-- Header Content --}}
            <div 
                class="relative z-10 p-3 md:p-4 lg:p-6 xl:p-8 h-full flex flex-col justify-center items-center text-center transition-all duration-700 ease-out"
                :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
                <h1 class="text-lg md:text-2xl lg:text-3xl xl:text-4xl font-bold text-white mb-2 md:mb-3 lg:mb-4">
                    Mulai Jadi Mitra Sekarang!
                </h1>
                <p class="text-xs md:text-base lg:text-lg xl:text-xl text-white/90 max-w-xl lg:max-w-3xl leading-relaxed">
                    Pemilik properti yang ingin menyewakan ruang kosong dan mendapat penghasilan tambahan Office
                </p>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="p-3 md:p-6 lg:p-8 xl:p-12">

            {{-- Section Title --}}
            <div 
                class="text-center mb-6 md:mb-12 lg:mb-16 transition-all duration-700 ease-out"
                :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                style="transition-delay: 200ms">
                <h2 class="text-xl md:text-3xl lg:text-4xl font-bold text-gray-800 mb-2">
                    PROGRAM SUKSES BERSAMA
                </h2>
            </div>

            {{-- Program Cards - Improved Layout for Tablet & Mobile --}}
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-2 md:gap-4 lg:gap-10 mb-4 md:mb-12 lg:mb-16 px-8 md:px-0">

                {{-- Card 1 for Tablet: Fixed height for consistency --}}
                <div 
                    class="bg-white rounded-md lg:rounded-2xl border-2 border-orange-200 hover:border-orange-400 
                        p-2 md:p-3 lg:p-10 text-center shadow-sm lg:shadow-lg hover:shadow-xl 
                        transition-all duration-500 ease-out transform hover:scale-105 
                        w-full h-32 md:h-56 lg:h-auto flex flex-col justify-center max-w-xs mx-auto md:max-w-none"
                    :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    style="transition-delay: 300ms">
                    <div class="flex flex-col items-center justify-center h-full">
                        <div class="w-6 h-6 md:w-12 md:h-12 lg:w-24 lg:h-24 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-1 md:mb-3 lg:mb-8">
                            <svg class="w-3 h-3 md:w-6 md:h-6 lg:w-12 lg:h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L3 7l9 5 9-5-9-5zM3 17l9 5 9-5M3 12l9 5 9-5" />
                            </svg>
                        </div>
                        <h3 class="text-xs md:text-base lg:text-2xl font-bold text-gray-800 mb-1 md:mb-2 lg:mb-4">
                            Mulai Jadi Mitra Sekarang!
                        </h3>
                        <p class="text-gray-600 text-xs md:text-xs lg:text-lg leading-tight px-1">
                            Mulai langsung jalan dengan sistem yang sudah terintegrasi
                        </p>
                    </div>
                </div>

                {{-- Card 2 for Tablet: Fixed height for consistency --}}
                <div 
                    class="bg-white rounded-md lg:rounded-2xl border-2 border-orange-200 hover:border-orange-400 
                        p-2 md:p-3 lg:p-10 text-center shadow-sm lg:shadow-lg hover:shadow-xl 
                        transition-all duration-500 ease-out transform hover:scale-105 
                        w-full h-32 md:h-56 lg:h-auto flex flex-col justify-center max-w-xs mx-auto md:max-w-none"
                    :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    style="transition-delay: 400ms">
                    <div class="flex flex-col items-center justify-center h-full">
                        <div class="w-6 h-6 md:w-12 md:h-12 lg:w-24 lg:h-24 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-1 md:mb-3 lg:mb-8">
                            <svg class="w-3 h-3 md:w-6 md:h-6 lg:w-12 lg:h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
                            </svg>
                        </div>
                        <h3 class="text-xs md:text-base lg:text-2xl font-bold text-gray-800 mb-1 md:mb-2 lg:mb-4">
                            Dukungan Khusus Penuh
                        </h3>
                        <p class="text-gray-600 text-xs md:text-xs lg:text-lg leading-tight px-1">
                            Dapat dukungan khusus untuk meningkatkan penjualan properti
                        </p>
                    </div>
                </div>

                {{-- Card 3 for Tablet: Fixed height for consistency --}}
                <div 
                    class="bg-white rounded-md lg:rounded-2xl border-2 border-orange-200 hover:border-orange-400 
                        p-2 md:p-3 lg:p-10 text-center shadow-sm lg:shadow-lg hover:shadow-xl 
                        transition-all duration-500 ease-out transform hover:scale-105 
                        w-full h-32 md:h-56 lg:h-auto flex flex-col justify-center max-w-xs mx-auto md:max-w-none"
                    :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    style="transition-delay: 500ms">
                    <div class="flex flex-col items-center justify-center h-full">
                        <div class="w-6 h-6 md:w-12 md:h-12 lg:w-24 lg:h-24 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-1 md:mb-3 lg:mb-8">
                            <svg class="w-3 h-3 md:w-6 md:h-6 lg:w-12 lg:h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z" />
                            </svg>
                        </div>
                        <h3 class="text-xs md:text-base lg:text-2xl font-bold text-gray-800 mb-1 md:mb-2 lg:mb-4">
                            Jangkauan Luas
                        </h3>
                        <p class="text-gray-600 text-xs md:text-xs lg:text-lg leading-tight px-1">
                            Perluas jangkauan anda secara luas kepada calon customer potensial
                        </p>
                    </div>
                </div>
            </div>

            {{-- Alternative layout for tablet: 2+1 arrangement --}}
            <div class="hidden">
                {{-- Top row: 2 cards side by side --}}
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    {{-- Card 1 & 2 content here --}}
                </div>
                {{-- Bottom row: 1 card full width --}}
                <div class="grid md:grid-cols-1 gap-6 mb-8">
                    {{-- Card 3 content here --}}
                </div>
            </div>

            {{-- CTA Button --}}
            <div 
                class="text-center mb-8 md:mb-16 lg:mb-20 transition-all duration-700 ease-out"
                :class="isVisible ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 translate-y-8 scale-95'"
                style="transition-delay: 600ms">
                <a href="{{ route('dashboard.prosesmitra') }}">
                    <button class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold py-3 md:py-5 lg:py-6 px-6 md:px-12 lg:px-16 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 text-sm md:text-lg lg:text-xl">
                        <span class="flex items-center justify-center">
                            <svg class="w-4 h-4 md:w-6 md:h-6 lg:w-7 lg:h-7 mr-2 md:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Masuk sebagai Mitra
                        </span>
                    </button>
                </a>
            </div>

            {{-- Additional Features Section - Enhanced --}}
            <div 
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-2 md:gap-4 lg:gap-10 mt-8 transition-all duration-700 ease-out px-4 md:px-0"
                :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                style="transition-delay: 700ms">

                {{-- Feature 1 --}}
                <div 
                    class="bg-white rounded-md lg:rounded-2xl border-2 border-orange-200 hover:border-orange-400 
                        p-2 md:p-3 lg:p-8 text-center shadow-sm lg:shadow-lg hover:shadow-xl 
                        transition-all duration-500 ease-out transform hover:scale-105 
                        w-full h-32 md:h-56 lg:h-auto flex flex-col justify-center 
                        max-w-xs mx-auto md:max-w-none">
                    <div class="flex flex-col items-center justify-center h-full">
                        <div class="w-6 h-6 md:w-12 md:h-12 lg:w-20 lg:h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-1 md:mb-3 lg:mb-6">
                            <svg class="w-3 h-3 md:w-6 md:h-6 lg:w-10 lg:h-10 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                            </svg>
                        </div>
                        <h4 class="text-xs md:text-base lg:text-xl font-bold text-gray-800 mb-1 md:mb-2 lg:mb-4">
                            Keuntungan Maksimal
                        </h4>
                        <p class="text-gray-600 text-xs md:text-sm lg:text-lg leading-tight px-1">
                            Dapatkan revenue sharing yang menguntungkan dari setiap transaksi booking yang berhasil dengan sistem pembayaran yang transparan
                        </p>
                    </div>
                </div>

                {{-- Feature 2 --}}
                <div 
                    class="bg-white rounded-md lg:rounded-2xl border-2 border-orange-200 hover:border-orange-400 
                        p-2 md:p-3 lg:p-8 text-center shadow-sm lg:shadow-lg hover:shadow-xl 
                        transition-all duration-500 ease-out transform hover:scale-105 
                        w-full h-32 md:h-56 lg:h-auto flex flex-col justify-center 
                        max-w-xs mx-auto md:max-w-none">
                    <div class="flex flex-col items-center justify-center h-full">
                        <div class="w-6 h-6 md:w-12 md:h-12 lg:w-20 lg:h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-1 md:mb-3 lg:mb-6">
                            <svg class="w-3 h-3 md:w-6 md:h-6 lg:w-10 lg:h-10 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="text-xs md:text-base lg:text-xl font-bold text-gray-800 mb-1 md:mb-2 lg:mb-4">
                            Proses Mudah
                        </h4>
                        <p class="text-gray-600 text-xs md:text-sm lg:text-lg leading-tight px-1">
                            Registrasi simpel dan cepat, langsung bisa mulai menerima booking dari customer dengan dukungan teknologi terdepan
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection