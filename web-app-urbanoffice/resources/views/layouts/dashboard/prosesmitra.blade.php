@extends('layouts.app')

@section('head')
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                screens: {
                    'xs': '475px',
                }
            }
        }
    }
</script>
@endsection

@section('content')
<div class="flex min-h-screen bg-gray-50" x-data="prosesMitraComponent()" x-init="init()">
    
    {{-- Content --}}
    <div class="flex-1 ml-0 md:ml-52 lg:ml-64 xl:ml-64">
        {{-- Header with Background --}}
        <div class="relative h-40 md:h-48 lg:h-56 overflow-hidden"
             style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); background-size: cover; background-position: center top;">
            
            {{-- Orange Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-orange-500/90 via-orange-500/70 to-orange-400/50"></div>

            {{-- Header Content --}}
            <div class="relative z-10 p-3 md:p-4 lg:p-5 h-full flex flex-col justify-center transition-all duration-700 ease-out"
                 :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
                
                {{-- Back Button --}}
                <div class="mb-2 md:mb-3">
                    <a href="{{ route('dashboard.mitra') }}" class="inline-flex items-center text-white hover:text-orange-200 transition-colors duration-200 group">
                        <svg class="w-4 h-4 md:w-5 md:h-5 mr-1.5 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="font-semibold text-xs md:text-sm">PENDIRIAN BADAN USAHA</span>
                    </a>
                </div>

                {{-- Title and Description --}}
                <div class="text-center max-w-2xl mx-auto">
                    <h1 class="text-lg md:text-xl lg:text-2xl font-bold text-white mb-2 leading-tight">
                        Dapatkan komisi dari setiap penyewaan dengan jadi Mitra Urban Office
                    </h1>
                    <p class="text-xs md:text-sm text-white/90 leading-relaxed">
                        Layanan persewaan workspace online dan offline terpercaya untuk kebutuhan bisnis modern
                    </p>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="p-4 md:p-2 lg:p-6">
            {{-- Process Flow Section --}}
            <div class="bg-white rounded-2xl shadow-xl p-3 md:p-4 lg:p-8 mb-4 md:mb-6 lg:mb-12 transition-all duration-700 ease-out"
                :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                style="transition-delay: 200ms;">
                
                {{-- Section Title --}}
                <div class="text-center mb-3 md:mb-4 lg:mb-12">
                    <h2 class="text-sm md:text-base lg:text-3xl font-bold text-gray-800 mb-1 md:mb-2">
                        Proses Alur Kerjasama Urban Office dengan Pemilik
                    </h2>
                </div>

                {{-- Process Steps Grid - Modified --}}
                <div class="w-full flex flex-col items-center max-w-full">
                    {{-- Mobile Layout - Unchanged --}}
                    <div class="md:hidden w-full flex flex-col items-center space-y-3 mb-6">
                        <div class="flex justify-center items-start gap-2 w-full">
                            <template x-for="(step, index) in processSteps.slice(0,3)" :key="index">
                                <div class="flex flex-col items-center text-center transition-all duration-500 ease-out transform hover:scale-105 w-20"
                                    :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                                    :style="`transition-delay: ${300 + index * 100}ms`">
                                    <div class="w-6 h-6 sm:w-10 sm:h-10 bg-orange-500 rounded-full flex items-center justify-center shadow-lg mb-1">
                                        <span class="text-[8px] sm:text-xs text-white" x-text="step.icon"></span>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <h3 class="font-bold text-gray-800 text-xs sm:text-[10px] leading-tight text-center mb-0.5"
                                            x-text="step.title"></h3>
                                        <p class="text-orange-500 font-medium text-xs sm:text-[7px] leading-tight text-center"
                                        x-text="step.subtitle"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <div class="flex justify-center items-start gap-2 w-full">
                            <template x-for="(step, index) in processSteps.slice(3,6)" :key="index">
                                <div class="flex flex-col items-center text-center transition-all duration-500 ease-out transform hover:scale-105 w-20"
                                    :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                                    :style="`transition-delay: ${600 + index * 100}ms`">
                                    <div class="w-6 h-6 sm:w-10 sm:h-10 bg-orange-500 rounded-full flex items-center justify-center shadow-lg mb-1">
                                        <span class="text-[8px] sm:text-xs text-white" x-text="step.icon"></span>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <h3 class="font-bold text-gray-800 text-xs sm:text-[10px] leading-tight text-center mb-0.5"
                                            x-text="step.title"></h3>
                                        <p class="text-orange-500 font-medium text-xs sm:text-[7px] leading-tight text-center"
                                        x-text="step.subtitle"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <div class="flex justify-center items-start w-full">
                            <div class="w-20"></div> 
                            <template x-for="(step, index) in processSteps.slice(6,7)" :key="index">
                                <div class="flex flex-col items-center text-center transition-all duration-500 ease-out transform hover:scale-105 w-20"
                                    :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                                    :style="`transition-delay: ${900 + index * 100}ms`">
                                    <div class="w-6 h-6 sm:w-10 sm:h-10 bg-orange-500 rounded-full flex items-center justify-center shadow-lg mb-1">
                                        <span class="text-[8px] sm:text-xs text-white" x-text="step.icon"></span>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <h3 class="font-bold text-gray-800 text-xs sm:text-[10px] leading-tight text-center mb-0.5"
                                            x-text="step.title"></h3>
                                        <p class="text-orange-500 font-medium text-xs sm:text-[7px] leading-tight text-center"
                                        x-text="step.subtitle"></p>
                                    </div>
                                </div>
                            </template>
                            <div class="w-20"></div> 
                        </div>
                    </div>
                    
                    {{-- Desktop/Tablet Layout - Modified --}}
                    <div class="hidden md:block w-full max-w-full">
                        {{-- First Row: 5 steps --}}
                        <div class="flex justify-between items-center w-full px-4 lg:px-8 mb-8">
                            <template x-for="(step, index) in processSteps.slice(0,5)" :key="index">
                                <div class="flex items-center relative transition-all duration-500 ease-out transform hover:scale-105"
                                    :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                                    :style="`transition-delay: ${300 + index * 100}ms`">
                                    
                                    {{-- Step Content --}}
                                    <div class="flex items-center space-x-1 md:space-x-2 lg:space-x-3">
                                        <div class="w-6 h-6 md:w-7 md:h-7 lg:w-12 lg:h-12 bg-orange-500 rounded-full flex items-center justify-center shadow-lg relative z-10">
                                            <span class="text-xs md:text-xs lg:text-base text-white" x-text="step.icon"></span>
                                        </div>
                                        <div class="flex flex-col items-start">
                                            <h3 class="font-bold text-gray-800 text-[10px] md:text-[9px] lg:text-sm leading-tight max-w-16 md:max-w-14 lg:max-w-24"
                                                x-text="step.title"></h3>
                                            <p class="text-orange-500 font-medium text-[9px] md:text-[8px] lg:text-xs leading-tight max-w-16 md:max-w-14 lg:max-w-24"
                                            x-text="step.subtitle"></p>
                                        </div>
                                    </div>
                                    
                                    {{-- Connecting Line - Shortened --}}
                                    <div x-show="index < 4"
                                        class="absolute left-full top-1/2 w-3 md:w-3 lg:w-6 h-0.5 bg-orange-300 -translate-y-1/2 ml-1 md:ml-1 lg:ml-2"></div>
                                </div>
                            </template>
                        </div>
                        
                        {{-- Second Row: 2 steps centered --}}
                        <div class="flex justify-center items-center gap-12 md:gap-10 lg:gap-24 w-full px-4 lg:px-8">
                            <template x-for="(step, index) in processSteps.slice(5,7)" :key="index">
                                <div class="flex items-center relative transition-all duration-500 ease-out transform hover:scale-105"
                                    :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                                    :style="`transition-delay: ${900 + index * 100}ms`">
                                    
                                    {{-- Step Content --}}
                                    <div class="flex items-center space-x-1 md:space-x-2 lg:space-x-3">
                                        <div class="w-6 h-6 md:w-7 md:h-7 lg:w-12 lg:h-12 bg-orange-500 rounded-full flex items-center justify-center shadow-lg relative z-10">
                                            <span class="text-xs md:text-xs lg:text-base text-white" x-text="step.icon"></span>
                                        </div>
                                        <div class="flex flex-col items-start">
                                            <h3 class="font-bold text-gray-800 text-[10px] md:text-[9px] lg:text-sm leading-tight max-w-16 md:max-w-14 lg:max-w-24"
                                                x-text="step.title"></h3>
                                            <p class="text-orange-500 font-medium text-[9px] md:text-[8px] lg:text-xs leading-tight max-w-16 md:max-w-14 lg:max-w-24"
                                            x-text="step.subtitle"></p>
                                        </div>
                                    </div>
                                    
                                    {{-- Connecting Line for step 6 only - Shortened --}}
                                    <div x-show="index < 1"
                                        class="absolute left-full top-1/2 w-3 md:w-3 lg:w-6 h-0.5 bg-orange-300 -translate-y-1/2 ml-1 md:ml-1 lg:ml-2"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>                 
        </div>

            {{-- Partnership Options Section --}}
            <div class="transition-all duration-700 ease-out"
                :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                style="transition-delay: 400ms;">
                
                {{-- Section Title --}}
                <div class="text-center mb-3 md:mb-6 lg:mb-8">
                    <h2 class="text-lg md:text-xl lg:text-2xl font-bold text-gray-800 mb-1">
                        PILIH DAFTAR
                    </h2>
                </div>

                {{-- Partnership Card - Responsive layout --}}
                <div class="w-full max-w-[280px] sm:max-w-sm md:max-w-xl lg:max-w-3xl xl:max-w-4xl 2xl:max-w-5xl mx-auto">
                    <div class="bg-white rounded-md md:rounded-lg lg:rounded-xl shadow-sm md:shadow-md lg:shadow-lg overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:scale-[1.01] mb-3 md:mb-6 lg:mb-8">
                        {{-- Mobile: Stacked layout, Tablet+: Side by side --}}
                        <div class="flex flex-col md:flex-row h-auto md:h-96 lg:h-[420px] xl:h-[450px]">
                            {{-- Image Section - Full width on mobile, left side on tablet+ --}}
                            <div class="relative w-full md:w-1/2 h-32 sm:h-40 md:h-full flex-shrink-0">
                                <img src="https://images.unsplash.com/photo-1497366754035-f200968a6e72?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                                    alt="Modern Office Space"
                                    class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-r from-black/20 to-transparent md:hidden"></div>
                            </div>

                            {{-- Content Section - Full width on mobile, right side on tablet+ --}}
                            <div class="w-full md:w-1/2 p-3 sm:p-4 md:p-5 lg:p-6 xl:p-8 flex flex-col justify-between md:justify-center min-h-full">
                                <div class="flex-1 flex flex-col justify-center">
                                    <h3 class="text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl font-bold text-gray-800 mb-2 sm:mb-3 md:mb-4 lg:mb-5 leading-tight">
                                        Mitra Property (Rumah, Ruko, Gedung)
                                    </h3>
                                    <p class="text-gray-600 text-xs sm:text-sm md:text-sm lg:text-base xl:text-lg leading-relaxed mb-3 sm:mb-4 md:mb-5 lg:mb-6">
                                        Jadikan properti kosong Anda produktif dengan bergabung secara
                                        transparan melalui aplikasi Urban Office. Dapatkan penghasilan pasif
                                        dari properti yang tidak terpakai.
                                    </p>

                                    {{-- Benefits List - Responsive spacing --}}
                                    <div class="space-y-1.5 sm:space-y-2 md:space-y-2.5 lg:space-y-3 mb-4 sm:mb-5 md:mb-6 lg:mb-8">
                                        <div class="flex items-start text-gray-700 text-xs sm:text-sm md:text-sm lg:text-base">
                                            <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-4 md:h-4 lg:w-5 lg:h-5 text-green-500 mr-1.5 sm:mr-2 md:mr-2 lg:mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Komisi hingga 30% dari setiap booking</span>
                                        </div>
                                        <div class="flex items-start text-gray-700 text-xs sm:text-sm md:text-sm lg:text-base">
                                            <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-4 md:h-4 lg:w-5 lg:h-5 text-green-500 mr-1.5 sm:mr-2 md:mr-2 lg:mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Pengelolaan properti secara profesional</span>
                                        </div>
                                        <div class="flex items-start text-gray-700 text-xs sm:text-sm md:text-sm lg:text-base">
                                            <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-4 md:h-4 lg:w-5 lg:h-5 text-green-500 mr-1.5 sm:mr-2 md:mr-2 lg:mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Laporan keuangan transparan</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- CTA Button - Responsive positioning --}}
                                <div class="flex justify-center sm:justify-center md:justify-start mt-auto">
                                    <a href="{{ route('dashboard.mitraform') }}">
                                        <button class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold py-2 sm:py-2.5 md:py-2.5 lg:py-3 xl:py-4 px-4 sm:px-5 md:px-5 lg:px-6 xl:px-8 rounded-md shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105 text-xs sm:text-sm md:text-sm lg:text-base xl:text-lg">
                                            <span class="flex items-center justify-center">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-4 md:h-4 lg:w-5 lg:h-5 mr-1 sm:mr-1.5 md:mr-1.5 lg:mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                ISI FORM
                                            </span>
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function prosesMitraComponent() {
    return {
        isVisible: false,
        processSteps: [
            {
                icon: "📄",
                title: "Isi Formulir",
                subtitle: "Pendaftaran",
                description: "Lengkapi formulir pendaftaran dengan data lengkap properti Anda"
            },
            {
                icon: "🔍",
                title: "Verifikasi",
                subtitle: "Screening",
                description: "Tim kami akan melakukan verifikasi data dan lokasi properti"
            },
            {
                icon: "📋",
                title: "Dokumentasi",
                subtitle: "Listing",
                description: "Proses dokumentasi dan pembuatan listing properti Anda"
            },
            {
                icon: "✍️",
                title: "Penandatanganan",
                subtitle: "Kontrak",
                description: "Penandatanganan kontrak kerjasama sebagai mitra resmi"
            },
            {
                icon: "📈",
                title: "Mulai diterima platform",
                subtitle: "Pendampingan",
                description: "Properti Anda mulai dapat dipesan oleh customer"
            },
            {
                icon: "💼",
                title: "Proses Transaksi",
                subtitle: "Komisi",
                description: "Kelola transaksi booking dan terima komisi secara otomatis"
            },
            {
                icon: "💰",
                title: "Laporan dan Pendampingan",
                subtitle: "Komisi",
                description: "Terima laporan rutin dan komisi dari setiap booking"
            },
        ],

        init() {
            // Trigger entrance animation
            this.$nextTick(() => {
                setTimeout(() => {
                    this.isVisible = true;
                }, 100);
            });
        }
    }
}
</script>

@endsection