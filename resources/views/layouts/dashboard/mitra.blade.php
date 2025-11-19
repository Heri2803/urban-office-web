@extends('layouts.app')

@section('content')
<div x-data="mitraPage()" x-init="setTimeout(() => isVisible = true, 100)" class="flex min-h-screen bg-gray-50">

    {{-- Modal untuk Masuk sebagai Mitra --}}
    <div x-show="showMitraModal" 
         x-cloak
         @click.away="showMitraModal = false"
         class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
        
        <div x-show="showMitraModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 space-y-5">
            
            {{-- Header Modal --}}
            <div class="flex items-center space-x-3 border-b border-gray-100 pb-4">
                <span class="text-2xl text-orange-500">🏢</span>
                <h3 class="text-lg font-semibold text-gray-900">Masuk sebagai Mitra</h3>
            </div>

            {{-- Content berdasarkan status user --}}
            <template x-if="userStatus === 'mitra'">
                <div class="space-y-4">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-green-800 font-medium">Akun Mitra Terdeteksi</span>
                        </div>
                        <p class="text-green-600 text-sm mt-2">Anda memiliki akses ke Panel Mitra Urban Office</p>
                    </div>
                    
                    <div class="flex space-x-3 pt-2">
                        <button @click="showMitraModal = false" 
                                type="button"
                                class="flex-1 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <a href="{{ route('mitrapanel.dashboard') }}" 
                           class="flex-1 px-4 py-2 bg-orange-600 text-orange text-sm font-medium rounded-lg hover:bg-orange-700 transition text-center">
                            🚀 Buka Panel Mitra
                        </a>
                    </div>
                </div>
            </template>

            <template x-if="userStatus === 'pending'">
                <div class="space-y-4">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-yellow-800 font-medium">Menunggu Approval</span>
                        </div>
                        <p class="text-yellow-600 text-sm mt-2">Pendaftaran mitra Anda sedang dalam proses review oleh tim kami</p>
                    </div>
                    
                    <button @click="showMitraModal = false" 
                            type="button"
                            class="w-full px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition">
                        Mengerti
                    </button>
                </div>
            </template>

            <template x-if="userStatus === 'customer'">
                <div class="space-y-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-blue-800 font-medium">Jadi Mitra Urban Office</span>
                        </div>
                        <p class="text-blue-600 text-sm mt-2">Daftarkan properti Anda sekarang dan mulai dapatkan penghasilan tambahan</p>
                    </div>
                    
                    <div class="flex space-x-3 pt-2">
                        <button @click="showMitraModal = false" 
                                type="button"
                                class="flex-1 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                            Nanti Saja
                        </button>
                        {{-- GUNAKAN ROUTE YANG SAMA --}}
                        <a href="{{ route('dashboard.prosesmitra') }}" 
                        class="flex-1 px-4 py-2 bg-orange-600 text-orange text-sm font-medium rounded-lg hover:bg-orange-700 transition text-center">
                            📝 Daftar Sekarang
                        </a>
                    </div>
                </div>
            </template>

            <template x-if="userStatus === 'guest'">
                <div class="space-y-4">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-800 font-medium">Login Diperlukan</span>
                        </div>
                        <p class="text-gray-600 text-sm mt-2">Silakan login terlebih dahulu untuk mengakses Panel Mitra</p>
                    </div>
                    
                    <div class="flex space-x-3 pt-2">
                        <button @click="showMitraModal = false" 
                                type="button"
                                class="flex-1 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <a href="{{ route('login') }}" 
                           class="flex-1 px-4 py-2 bg-orange-600 text-orange text-sm font-medium rounded-lg hover:bg-orange-700 transition text-center">
                            🔐 Login
                        </a>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Content (tetap sama) --}}
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

            {{-- CTA Buttons Section --}}
            <div 
                class="text-center mb-8 md:mb-16 lg:mb-20 transition-all duration-700 ease-out space-y-4 md:space-y-0 md:space-x-6 md:flex md:justify-center md:items-center"
                :class="isVisible ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 translate-y-8 scale-95'"
                style="transition-delay: 600ms">
                
            
                {{-- Button 1: Masuk sebagai Mitra (Modal Check) --}}
                <button @click="handleMitraAccess()"
                        class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold py-3 md:py-4 lg:py-5 px-6 md:px-10 lg:px-12 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 text-sm md:text-base lg:text-lg w-full md:w-auto">
                    <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 md:w-5 md:h-5 lg:w-6 lg:h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Masuk sebagai Mitra
                    </span>
                </button>

                {{-- Button 2: Daftar Jadi Mitra (Direct Link) --}}
                <a href="{{ route('dashboard.prosesmitra') }}"
                class="bg-white border-2 border-orange-500 text-orange-600 hover:bg-orange-50 font-bold py-3 md:py-4 lg:py-5 px-6 md:px-10 lg:px-12 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 text-sm md:text-base lg:text-lg w-full md:w-auto inline-block">
                    <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 md:w-5 md:h-5 lg:w-6 lg:h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Daftar Jadi Mitra
                    </span>
                </a>
            </div>

            {{-- Additional Features Section --}}
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

<!-- Di sebelum penutup </body> -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('mitraPage', () => ({
        isVisible: false,
        showMitraModal: false,
        userStatus: 'guest', // guest, mitra, pending, customer
        isChecking: false,
        
        init() {
            this.checkUserStatus();
            setTimeout(() => {
                this.isVisible = true;
            }, 100);
        },
        
        async checkUserStatus() {
            try {
                const response = await fetch('/check-mitra-status');
                const data = await response.json();
                
                // Tentukan status berdasarkan response
                if (!data.has_mitra_access) {
                    this.userStatus = 'customer';
                } else {
                    this.userStatus = 'mitra';
                }
                
                console.log('User status:', this.userStatus);
                
            } catch (error) {
                console.error('Error checking user status:', error);
                this.userStatus = 'guest';
            }
        },
        
        handleMitraAccess() {
            // Langsung buka modal untuk cek status user
            this.showMitraModal = true;
        },
        
        proceedToRegistration() {
            this.showMitraModal = false;
            window.location.href = "{{ route('dashboard.prosesmitra') }}";
        },
        
        closeModal() {
            this.showMitraModal = false;
        },
        
        goToMitraPanel() {
            this.showMitraModal = false;
            window.location.href = "{{ route('mitrapanel.dashboard') }}";
        }
    }));
});
</script>
@endsection