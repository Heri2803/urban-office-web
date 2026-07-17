@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-50 mb-12">
    
    {{-- Main Content --}}
    <div class="flex-1 ml-0 md:ml-52 lg:ml-64 xl:ml-64 transition-all duration-500 ease-in-out">
        {{-- Header Section --}}
        <div 
            class="relative h-48 sm:h-42 lg:h-64 bg-cover bg-center animate-fade-in"
            style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?w=1200&h=400&fit=crop')"
        >
            {{-- Orange Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-orange-500/90 via-orange-400/70 to-transparent"></div>
            
            {{-- Header Content --}}
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between h-full p-4 sm:p-6 lg:p-8">
                <div class="flex flex-col md:flex-row items-start md:items-center space-y-3 md:space-y-0 md:space-x-4 w-full">
                    {{-- Profile Image --}}
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white rounded-full overflow-hidden border-4 border-white shadow-lg animate-slide-in-left cursor-pointer"
                         onclick="toggleUserPopup()">
                        @auth
                        <img
                            src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : 'https://via.placeholder.com/150' }}"
                            alt="Profile"
                            class="w-full h-full object-cover"
                        />
                    @else
                        <img
                            src="https://via.placeholder.com/150"
                            alt="Guest"
                            class="w-full h-full object-cover"
                        />
                    @endauth
                    </div>
                    
                    {{-- Profile Info --}}
                    <div class="text-white flex-1 animate-slide-in-up">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold">
                            Selamat Datang {{ Auth::user()->name ?? 'Guest' }}
                        </h1>
                        <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 mt-2">
                            <span class="inline-flex items-center bg-green-500 text-white text-xs sm:text-sm px-3 py-1 rounded-full font-medium w-fit">
                                {{ Auth::user()->paket ?? 'Virtual Office' }}
                            </span>
                            <span class="text-sm sm:text-base opacity-90 break-all sm:break-normal">
                                {{ Auth::user()->telephone ?? '08212345678' }} / 
                                {{ Str::limit(Auth::user()->email ?? 'example@email.com', 15) }}
                            </span>
                        </div>
                    </div>

                </div>
                
                {{-- Detail Button --}}
                <button 
                    onclick="toggleUserPopup()"
                    class="bg-white text-orange-500 px-4 py-2 sm:px-6 sm:py-3 rounded-lg font-semibold hover:bg-gray-50 transition-all duration-300 shadow-lg animate-slide-in-right hover:scale-105 absolute top-4 right-4 sm:relative sm:top-auto sm:right-auto">
                    Detail
                </button>
            </div>
        </div>

        {{-- User Info Popup --}}
        {{-- Popup User --}}
        <div id="userPopup" 
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-fade-in hidden" 
            onclick="toggleUserPopup()">
            <div 
                class="bg-white rounded-xl shadow-2xl p-4 sm:p-6 lg:p-8 m-4 w-full max-w-sm sm:max-w-md lg:max-w-lg animate-slide-in-up"
                onclick="event.stopPropagation()"
            >
                {{-- Popup Header --}}
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Detail Pengguna</h3>
                    <button 
                        onclick="toggleUserPopup()"
                        class="text-gray-400 hover:text-gray-600 transition-colors p-1"
                    >
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                {{-- Profile Image and Name --}}
                <div class="flex flex-col items-center mb-4 sm:mb-6">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 bg-gray-100 rounded-full overflow-hidden border-4 border-orange-200 mb-3">
                        @auth
                        <img
                            src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : 'https://via.placeholder.com/150' }}"
                            alt="Profile"
                            class="w-full h-full object-cover"
                        />
                    @else
                        <img
                            src="https://via.placeholder.com/150"
                            alt="Guest"
                            class="w-full h-full object-cover"
                        />
                    @endauth

                    </div>
                    @auth
                        <h4 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-800">
                            {{ Auth::user()->name }}
                        </h4>
                    @else
                        <h4 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-800">
                            Guest
                        </h4>
                    @endauth
                    <span class="inline-flex items-center bg-green-100 text-green-800 text-xs sm:text-sm px-2 sm:px-3 py-1 rounded-full font-medium mt-2">
                        Active
                    </span>
                </div>
                
                {{-- User Details --}}
                <div class="space-y-3 sm:space-y-4">
                {{-- Email --}}
                <div class="flex items-center space-x-3">
                    <span class="text-gray-400 text-sm sm:text-base">📧</span>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500">Email</p>
                        <p class="text-sm sm:text-base text-gray-800 break-all">
                            {{ optional(Auth::user())->email ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Telepon --}}
                <div class="flex items-center space-x-3">
                    <span class="text-gray-400 text-sm sm:text-base">📱</span>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500">Telepon</p>
                        <p class="text-sm sm:text-base text-gray-800">
                            {{ optional(Auth::user())->telephone ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Alamat --}}
                <div class="flex items-center space-x-3">
                    <span class="text-gray-400 text-sm sm:text-base">📍</span>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500">Alamat</p>
                        <p class="text-sm sm:text-base text-gray-800">
                            {{ optional(Auth::user())->alamat ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Paket --}}
                <div class="flex items-center space-x-3">
                    <span class="text-gray-400 text-sm sm:text-base">📦</span>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500">Paket</p>
                        <p class="text-sm sm:text-base text-gray-800">
                            {{ optional(Auth::user())->paket ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Bergabung Sejak --}}
                <div class="flex items-center space-x-3">
                    <span class="text-gray-400 text-sm sm:text-base">📅</span>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500">Bergabung Sejak</p>
                        <p class="text-sm sm:text-base text-gray-800">
                            {{ optional(optional(Auth::user())->created_at)->format('d F Y') ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Tombol Edit Profil --}}
                <div class="mt-6 flex justify-center">
                    <a href="{{ route('profile.edit', Auth::id()) }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm sm:text-base rounded-lg shadow-md transition">
                    ✏️ Edit Profil
                    </a>
                </div>
            </div>
            </div>
        </div>

        {{-- ===== INVOICE SEARCH SECTION ===== --}}
        <div class="px-4 sm:px-6 lg:px-8 pt-6 pb-2 animate-fade-in-up">
            <div class="max-w-5xl mx-auto">
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-5 sm:p-7">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-9 h-9 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-gray-800">Cek Status Invoice</h2>
                            <p class="text-xs sm:text-sm text-gray-500">Masukkan nomor invoice untuk melihat detail transaksi</p>
                        </div>
                    </div>

                    {{-- Search Input + Button --}}
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <input
                                id="invoiceSearchInput"
                                type="text"
                                placeholder="Contoh: ORDER-XXXXXX/VO/20240315/660/2026"
                                class="w-full pl-4 pr-10 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition"
                                onkeydown="if(event.key==='Enter') searchInvoice()"
                            />
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </span>
                        </div>
                        <button
                            id="invoiceSearchBtn"
                            onclick="searchInvoice()"
                            class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl font-semibold text-sm transition-all duration-300 hover:scale-105 shadow-md flex items-center justify-center gap-2 sm:w-auto w-full"
                        >
                            <svg id="searchBtnIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <svg id="searchBtnSpinner" class="w-4 h-4 animate-spin hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            <span id="searchBtnText">Cari Invoice</span>
                        </button>
                    </div>

                    {{-- Error / info message --}}
                    <div id="invoiceSearchError" class="hidden mt-3 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 110 18A9 9 0 0112 3z"/>
                        </svg>
                        <span id="invoiceSearchErrorText"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== MY SURATS SECTION ===== --}}
        <div class="px-4 sm:px-6 lg:px-8 pt-2 pb-6 animate-fade-in-up">
            <div class="max-w-5xl mx-auto">
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-5 sm:p-7">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg sm:text-xl font-bold text-gray-800">Kotak Surat Saya</h2>
                                <p class="text-xs sm:text-sm text-gray-500">Lihat dan lacak surat fisik yang diterima oleh Urban Office</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto">
                            @auth
                                @php $unreadSurats = auth()->user()->unreadSuratsCount(); @endphp
                                @if($unreadSurats > 0)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full">
                                        <span class="w-2 h-2 bg-red-500 rounded-full animate-ping"></span> {{ $unreadSurats }} Baru
                                    </span>
                                @endif
                            @endauth
                            <a href="{{ route('dashboard.surats.index') }}"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition-all duration-300 hover:scale-105 shadow-md flex items-center justify-center gap-2 sm:w-auto w-full"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                </svg>
                                Buka Kotak Surat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== INVOICE RESULT MODAL ===== --}}
        <div id="invoiceResultModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-fade-in hidden"
            onclick="closeInvoiceModal()">
            <div
                class="bg-white rounded-2xl shadow-2xl m-4 w-full max-w-lg animate-slide-in-up overflow-hidden"
                onclick="event.stopPropagation()"
            >
                {{-- Modal Header --}}
                <div class="bg-gradient-to-r from-orange-500 to-orange-400 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-white font-bold text-lg">Detail Invoice</h3>
                    </div>
                    <button onclick="closeInvoiceModal()" class="text-white/70 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-5 sm:p-6 max-h-[70vh] overflow-y-auto">

                    {{-- Invoice Summary --}}
                    <div class="bg-orange-50 border border-orange-100 rounded-xl p-4 mb-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-500 mb-1">Nomor Invoice</p>
                                <p id="modal_invoice_number" class="text-sm font-bold text-gray-800 break-all"></p>
                            </div>
                            <span id="modal_status_badge" class="flex-shrink-0 text-xs font-semibold px-3 py-1 rounded-full"></span>
                        </div>
                        <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-xs text-gray-500">Tanggal Invoice</p>
                                <p id="modal_formatted_date" class="font-medium text-gray-800"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Total</p>
                                <p id="modal_formatted_total" class="font-semibold text-orange-600"></p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-gray-500">Dibuat Oleh</p>
                                <p id="modal_created_by" class="font-medium text-gray-800"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-xs text-gray-400 font-medium uppercase tracking-wide">Detail Transaksi</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>

                    {{-- Transaction Details Grid --}}
                    <div class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-500 mb-1">👤 Nama Lengkap</p>
                                <p id="modal_nama_lengkap" class="text-sm font-medium text-gray-800"></p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-500 mb-1">🏢 Perusahaan</p>
                                <p id="modal_company_name" class="text-sm font-medium text-gray-800"></p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-500 mb-1">📧 Email</p>
                                <p id="modal_email" class="text-sm font-medium text-gray-800 break-all"></p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-500 mb-1">📱 Telepon</p>
                                <p id="modal_phone" class="text-sm font-medium text-gray-800"></p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-500 mb-1">🏠 Tipe Ruangan</p>
                                <p id="modal_room_type" class="text-sm font-medium text-gray-800"></p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-500 mb-1">📍 Lokasi</p>
                                <p id="modal_location_name" class="text-sm font-medium text-gray-800"></p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-500 mb-1">📅 Tanggal Booking</p>
                                <p id="modal_booking_date" class="text-sm font-medium text-gray-800"></p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-500 mb-1">⏱️ Durasi</p>
                                <p id="modal_duration_text" class="text-sm font-medium text-gray-800"></p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-500 mb-1">👥 Peserta</p>
                                <p id="modal_participants_text" class="text-sm font-medium text-gray-800"></p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-500 mb-1">📦 Paket</p>
                                <p id="modal_paket" class="text-sm font-medium text-gray-800"></p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 col-span-1 sm:col-span-2">
                                <p class="text-xs text-gray-500 mb-1">🔖 Order ID</p>
                                <p id="modal_order_id" class="text-sm font-medium text-gray-800 break-all"></p>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button onclick="closeInvoiceModal()" class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-xl transition-all duration-300">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        {{-- FAQ Section --}}
        <div class="p-4 sm:p-6 lg:p-8 animate-fade-in-up">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6 sm:mb-8 animate-slide-in-left">
                    Frequently Asked Questions
                </h2>
                
                <div class="space-y-3">
                    @php
                        $faqItems = [
                            [
                                'id' => 1,
                                'icon' => '🔒',
                                'title' => 'Keamanan Akun',
                                'content' => 'Informasi mengenai keamanan akun Anda termasuk pengaturan password, two-factor authentication, dan riwayat login terbaru.'
                            ],
                            [
                                'id' => 2,
                                'icon' => '❓',
                                'title' => 'Hubungi Bantuan',
                                'content' => 'Tim support kami siap membantu Anda 24/7. Hubungi kami melalui email support@urbanoffice.com atau telepon 021-1234-5678.'
                            ],
                            [
                                'id' => 3,
                                'icon' => '❓',
                                'title' => 'FAQ',
                                'content' => 'Pertanyaan yang sering diajukan mengenai layanan Urban Office, pembayaran, booking ruangan, dan fitur-fitur lainnya.'
                            ],
                            [
                                'id' => 4,
                                'icon' => '📋',
                                'title' => 'Syarat dan Ketentuan',
                                'content' => 'Syarat dan ketentuan penggunaan layanan Urban Office yang berlaku untuk semua pengguna platform kami.'
                            ],
                            [
                                'id' => 5,
                                'icon' => '🔐',
                                'title' => 'Kebijakan Privasi',
                                'content' => 'Kebijakan privasi mengenai bagaimana kami mengumpulkan, menggunakan, dan melindungi data pribadi Anda.'
                            ]
                        ];
                    @endphp

                    @foreach($faqItems as $index => $item)
                        {{-- FAQ Title Card --}}
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all duration-300 animate-fade-in-card" style="animation-delay: {{ $index * 100 }}ms">
                            <button
                                onclick="toggleDropdown({{ $item['id'] }})"
                                class="w-full flex items-center justify-between p-4 sm:p-5 hover:bg-gray-50 transition-colors text-left"
                            >
                                <div class="flex items-center space-x-3 sm:space-x-4">
                                    <span class="text-lg sm:text-xl animate-bounce-light">{{ $item['icon'] }}</span>
                                    <span class="text-base sm:text-lg font-medium text-gray-800">
                                        {{ $item['title'] }}
                                    </span>
                                </div>
                                <svg
                                    id="arrow-{{ $item['id'] }}"
                                    class="w-5 h-5 text-gray-400 transition-transform duration-300"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                        
                        {{-- FAQ Content Card (Initially Hidden) --}}
                        <div id="dropdown-{{ $item['id'] }}" class="hidden">
                            <div class="bg-orange-50 rounded-lg shadow-sm border border-orange-200 ml-4 sm:ml-6 animate-slide-in-down">
                                <div class="p-4 sm:p-5">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-1 h-6 bg-orange-400 rounded-full flex-shrink-0 mt-1"></div>
                                        <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
                                            {{ $item['content'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center py-6 sm:py-8 text-gray-500 text-sm animate-fade-in">
            My Office by Urban Office
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slide-in-left {
    from { transform: translateX(-20px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slide-in-right {
    from { transform: translateX(20px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slide-in-up {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes fade-in-up {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes fade-in-card {
    from { transform: translateY(10px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes bounce-light {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-2px); }
}

@keyframes slide-in-down {
    from { 
        transform: translateY(-10px); 
        opacity: 0; 
    }
    to { 
        transform: translateY(0); 
        opacity: 1; 
    }
}

.animate-fade-in {
    animation: fade-in 0.8s ease-out;
}

.animate-slide-in-left {
    animation: slide-in-left 0.6s ease-out;
}

.animate-slide-in-right {
    animation: slide-in-right 0.6s ease-out;
}

.animate-slide-in-up {
    animation: slide-in-up 0.7s ease-out;
}

.animate-fade-in-up {
    animation: fade-in-up 0.9s ease-out;
}

.animate-fade-in-card {
    animation: fade-in-card 0.5s ease-out forwards;
    opacity: 0;
}

.animate-bounce-light {
    animation: bounce-light 2s infinite;
}

.animate-slide-in-down {
    animation: slide-in-down 0.3s ease-out;
}

svg {
    transition: transform 0.3s ease-in-out;
}

.rotate-180 {
    transform: rotate(180deg);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let openDropdown = null;
    let showUserPopup = false;

    // Toggle dropdown function - MODIFIED
    window.toggleDropdown = function(id) {
        const dropdown = document.getElementById(`dropdown-${id}`);
        const arrow = document.getElementById(`arrow-${id}`);
         
        
        if (openDropdown === id) {
            // Close current dropdown with slide up animation
            dropdown.style.transform = 'translateY(-10px)';
            dropdown.style.opacity = '0';
            
            setTimeout(() => {
                dropdown.classList.add('hidden');
                dropdown.style.transform = 'translateY(0)';
                dropdown.style.opacity = '1';
            }, 200);
            
            arrow.classList.remove('rotate-180');
            openDropdown = null;
        } else {
            // Close previously opened dropdown
            if (openDropdown !== null) {
                const prevDropdown = document.getElementById(`dropdown-${openDropdown}`);
                const prevArrow = document.getElementById(`arrow-${openDropdown}`);
                
                prevDropdown.style.transform = 'translateY(-10px)';
                prevDropdown.style.opacity = '0';
                
                setTimeout(() => {
                    prevDropdown.classList.add('hidden');
                    prevDropdown.style.transform = 'translateY(0)';
                    prevDropdown.style.opacity = '1';
                }, 200);
                
                prevArrow.classList.remove('rotate-180');
            }
            
            // Open new dropdown with slide down animation
            dropdown.classList.remove('hidden');
            dropdown.style.transform = 'translateY(-10px)';
            dropdown.style.opacity = '0';
            
            setTimeout(() => {
                dropdown.style.transform = 'translateY(0)';
                dropdown.style.opacity = '1';
            }, 10);
            
            arrow.classList.add('rotate-180');
            openDropdown = id;
        }
    };

    // ADD THIS FUNCTION - Toggle user popup function
    window.toggleUserPopup = function() {
        const popup = document.getElementById('userPopup');
        showUserPopup = !showUserPopup;
        
        if (showUserPopup) {
            popup.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        } else {
            popup.classList.add('hidden');
            document.body.style.overflow = 'auto'; // Re-enable scrolling
        }
    };

    // Close popup when pressing Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (showUserPopup) toggleUserPopup();
            closeInvoiceModal();
        }
    });

    // ========== INVOICE SEARCH ==========
    window.searchInvoice = function() {
        const input   = document.getElementById('invoiceSearchInput');
        const errBox  = document.getElementById('invoiceSearchError');
        const errText = document.getElementById('invoiceSearchErrorText');
        const btnText = document.getElementById('searchBtnText');
        const btnIcon = document.getElementById('searchBtnIcon');
        const spinner = document.getElementById('searchBtnSpinner');
        const btn     = document.getElementById('invoiceSearchBtn');

        const invoiceNumber = input.value.trim();
        if (!invoiceNumber) {
            showSearchError('Nomor invoice tidak boleh kosong.');
            return;
        }

        // --- Loading state ---
        errBox.classList.add('hidden');
        btnText.textContent = 'Mencari...';
        btnIcon.classList.add('hidden');
        spinner.classList.remove('hidden');
        btn.disabled = true;

        fetch(`{{ route('dashboard.invoice.search') }}?invoice_number=${encodeURIComponent(invoiceNumber)}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(json => {
            if (json.found) {
                populateInvoiceModal(json.data);
                openInvoiceModal();
            } else {
                showSearchError(json.message || 'Invoice tidak ditemukan.');
            }
        })
        .catch(() => {
            showSearchError('Terjadi kesalahan. Silakan coba lagi.');
        })
        .finally(() => {
            btnText.textContent = 'Cari Invoice';
            btnIcon.classList.remove('hidden');
            spinner.classList.add('hidden');
            btn.disabled = false;
        });
    };

    function showSearchError(msg) {
        const errBox  = document.getElementById('invoiceSearchError');
        const errText = document.getElementById('invoiceSearchErrorText');
        errText.textContent = msg;
        errBox.classList.remove('hidden');
    }

    function populateInvoiceModal(d) {
        document.getElementById('modal_invoice_number').textContent  = d.invoice_number;
        document.getElementById('modal_formatted_date').textContent  = d.formatted_date;
        document.getElementById('modal_formatted_total').textContent = d.formatted_total;
        document.getElementById('modal_created_by').textContent      = d.created_by_name;
        document.getElementById('modal_nama_lengkap').textContent    = d.nama_lengkap;
        document.getElementById('modal_company_name').textContent    = d.company_name;
        document.getElementById('modal_email').textContent           = d.email;
        document.getElementById('modal_phone').textContent           = d.phone;
        document.getElementById('modal_room_type').textContent       = d.room_type;
        document.getElementById('modal_location_name').textContent   = d.location_name;
        document.getElementById('modal_booking_date').textContent    = d.booking_date;
        document.getElementById('modal_duration_text').textContent   = d.duration_text;
        document.getElementById('modal_participants_text').textContent = d.participants_text;
        document.getElementById('modal_paket').textContent           = d.paket;
        document.getElementById('modal_order_id').textContent        = d.order_id;

        // Status badge
        const badge = document.getElementById('modal_status_badge');
        const statusMap = {
            'settlement': { label: '✅ Lunas',   cls: 'bg-green-100 text-green-700' },
            'pending'   : { label: '⏳ Pending', cls: 'bg-yellow-100 text-yellow-700' },
            'expired'   : { label: '❌ Expired', cls: 'bg-red-100 text-red-700' },
            'expire'    : { label: '❌ Expired', cls: 'bg-red-100 text-red-700' },
        };
        const s = statusMap[d.status] || { label: d.status, cls: 'bg-gray-100 text-gray-700' };
        badge.textContent  = s.label;
        badge.className    = `flex-shrink-0 text-xs font-semibold px-3 py-1 rounded-full ${s.cls}`;
    }

    window.openInvoiceModal = function() {
        const modal = document.getElementById('invoiceResultModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    window.closeInvoiceModal = function() {
        const modal = document.getElementById('invoiceResultModal');
        if (!modal.classList.contains('hidden')) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    };
});
</script>
@endsection