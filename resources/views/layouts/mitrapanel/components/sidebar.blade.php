{{-- resources/views/layouts/mitrapanel/components/sidebar.blade.php --}}

{{-- Alpine.js x-cloak helper --}}
<style>[x-cloak] { display: none !important; }</style>

{{-- ============================================
     DESKTOP & TABLET SIDEBAR
     Sidebar width: md:w-64 (256px) untuk semua ukuran md ke atas
============================================ --}}
<div class="hidden md:flex md:flex-col md:fixed md:inset-y-0 md:left-0 w-64 md:bg-white md:border-r md:border-gray-200 md:shadow-sm z-50">
    
    {{-- Header / Logo --}}
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-center">
            <img src="{{ asset('assets/LOGO_URBAN_OFFICE.png') }}" 
                 alt="Urban Office Logo" 
                 class="h-24 w-auto">
        </div>
    </div>

    {{-- Navigation Menu --}}
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        
        {{-- Home --}}
        <a href="{{ route('mitrapanel.dashboard') }}" 
           :class="isActive('mitrapanel.dashboard') ? 'bg-orange-100 text-orange-600' : 'text-gray-700'"
           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-50 transition group">
            <svg class="w-5 h-5 mr-3" :class="isActive('mitrapanel.dashboard') ? 'text-orange-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="font-medium">Home</span>
        </a>

        {{-- Lokasi Mitra (Direct Link) --}}
        <a href="{{ route('mitrapanel.lokasi.detail', ['slug' => 'urban-office-merr']) }}" 
           :class="currentPage.includes('mitrapanel.lokasi') ? 'bg-orange-100 text-orange-600' : 'text-gray-700'"
           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-50 transition group">
            <svg class="w-5 h-5 mr-3" :class="currentPage.includes('mitrapanel.lokasi') ? 'text-orange-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="font-medium">Lokasi Mitra</span>
        </a>

        {{-- History Promo --}}
        <a href="{{ route('mitrapanel.history-promo') }}" 
           :class="isActive('mitrapanel.history-promo') ? 'bg-orange-100 text-orange-600' : 'text-gray-700'"
           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-50 transition group">
            <svg class="w-5 h-5 mr-3" :class="isActive('mitrapanel.history-promo') ? 'text-orange-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="font-medium">History Promo</span>
        </a>

        {{-- Download Faktur Pajak --}}
        <a href="{{ route('mitrapanel.faktur-pajak') }}" 
           :class="isActive('mitrapanel.faktur-pajak') ? 'bg-orange-100 text-orange-600' : 'text-gray-700'"
           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-50 transition group">
            <svg class="w-5 h-5 mr-3" :class="isActive('mitrapanel.faktur-pajak') ? 'text-orange-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="font-medium">Faktur Pajak</span>
        </a>

        {{-- Settings --}}
        <a href="{{ route('mitrapanel.settings') }}" 
           :class="isActive('mitrapanel.settings') ? 'bg-orange-100 text-orange-600' : 'text-gray-700'"
           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-50 transition group">
            <svg class="w-5 h-5 mr-3" :class="isActive('mitrapanel.settings') ? 'text-orange-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="font-medium">Settings</span>
        </a>

    </nav>

    {{-- Footer --}}
    <div class="p-4 border-t border-gray-200">
        <p class="text-xs text-gray-400 text-center">© 2025 Urban Office</p>
    </div>

</div>

{{-- ============================================
     MOBILE BOTTOM NAVIGATION
============================================ --}}
<div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50">
    <nav class="flex justify-between items-center px-2 py-1">
        
        {{-- Bagian Kiri: Lokasi dan Promo --}}
        <div class="flex space-x-4">
            {{-- Lokasi Mitra --}}
            <a href="{{ route('mitrapanel.lokasi.detail', ['slug' => 'urban-office-merr']) }}"
               :class="currentPage.includes('mitrapanel.lokasi') ? 'text-orange-600' : 'text-gray-600'"
               class="flex flex-col items-center justify-center px-2 py-1 hover:bg-gray-50 transition active:scale-95">
                <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-[10px] font-medium">Lokasi</span>
            </a>

            {{-- History Promo --}}
            <a href="{{ route('mitrapanel.history-promo') }}" 
               :class="isActive('mitrapanel.history-promo') ? 'text-orange-600' : 'text-gray-600'"
               class="flex flex-col items-center justify-center px-2 py-1 hover:bg-gray-50 transition active:scale-95">
                <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-[10px] font-medium">Promo</span>
            </a>
        </div>

        {{-- Bagian Tengah: Home dengan Logo Bulat --}}
        <div class="flex -mt-8">
            <a href="{{ route('mitrapanel.dashboard') }}" 
               :class="isActive('mitrapanel.dashboard') ? 'ring-4 ring-orange-200' : 'ring-4 ring-white'"
               class="flex flex-col items-center justify-center bg-white rounded-full p-3 shadow-lg hover:shadow-xl transition-all duration-300 active:scale-95">
                <img src="{{ asset('assets/LOGO_URBAN_OFFICE.png') }}" 
                     alt="Home" 
                     class="w-10 h-10 object-contain">
            </a>
        </div>

        {{-- Bagian Kanan: Faktur dan Settings --}}
        <div class="flex space-x-4">
            {{-- Download Faktur Pajak --}}
            <a href="{{ route('mitrapanel.faktur-pajak') }}" 
               :class="isActive('mitrapanel.faktur-pajak') ? 'text-orange-600' : 'text-gray-600'"
               class="flex flex-col items-center justify-center px-2 py-1 hover:bg-gray-50 transition active:scale-95">
                <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="text-[10px] font-medium">Faktur</span>
            </a>

            {{-- Settings --}}
            <a href="{{ route('mitrapanel.settings') }}" 
               :class="isActive('mitrapanel.settings') ? 'text-orange-600' : 'text-gray-600'"
               class="flex flex-col items-center justify-center px-2 py-1 hover:bg-gray-50 transition active:scale-95">
                <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-[10px] font-medium">Settings</span>
            </a>
        </div>

    </nav>
</div>