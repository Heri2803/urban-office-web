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
        <h2 class="text-xl font-semibold text-gray-800">Mitra Panel</h2>
    </div>

    {{-- Navigation Menu --}}
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        
        {{-- Home --}}
        <a href="{{ route('mitrapanel.dashboard') }}" 
           :class="isActive('mitrapanel.dashboard') ? 'bg-orange-100 text-orange-600' : 'text-gray-700'"
           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-50 transition group">
            <span class="text-lg mr-3">🏠</span>
            <span class="font-medium">Home</span>
        </a>

        {{-- Lokasi Mitra (Direct Link) --}}
        <a href="{{ route('mitrapanel.lokasi.detail', ['slug' => 'urban-office-merr']) }}" 
           :class="isActive('mitrapanel.lokasi.*') ? 'bg-orange-100 text-orange-600' : 'text-gray-700'"
           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-50 transition group">
            <span class="text-lg mr-3">📍</span>
            <span class="font-medium">Lokasi Mitra</span>
        </a>

        {{-- History Promo --}}
        <a href="{{ route('mitrapanel.history-promo') }}" 
           :class="isActive('mitrapanel.history-promo') ? 'bg-orange-100 text-orange-600' : 'text-gray-700'"
           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-50 transition group">
            <span class="text-lg mr-3">📸</span>
            <span class="font-medium">History Promo</span>
        </a>

        {{-- Download Faktur Pajak --}}
        <a href="{{ route('mitrapanel.faktur-pajak') }}" 
           :class="isActive('mitrapanel.faktur-pajak') ? 'bg-orange-100 text-orange-600' : 'text-gray-700'"
           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-50 transition group">
            <span class="text-lg mr-3">📄</span>
            <span class="font-medium">Faktur Pajak</span>
        </a>

        {{-- Settings --}}
        <a href="{{ route('mitrapanel.settings') }}" 
           :class="isActive('mitrapanel.settings') ? 'bg-orange-100 text-orange-600' : 'text-gray-700'"
           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-50 transition group">
            <span class="text-lg mr-3">⚙️</span>
            <span class="font-medium">Settings</span>
        </a>

    </nav>

    {{-- Footer --}}
    <div class="p-4 border-t border-gray-200">
        <p class="text-xs text-gray-400 text-center">© 2024 Urban Office</p>
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
               :class="isActive('mitrapanel.lokasi.*') ? 'text-orange-600' : 'text-gray-600'"
               class="flex flex-col items-center justify-center px-2 py-1 hover:bg-gray-50 transition active:scale-95">
                <span class="text-lg mb-0.5">📍</span>
                <span class="text-[10px] font-medium">Lokasi</span>
            </a>

            {{-- History Promo --}}
            <a href="{{ route('mitrapanel.history-promo') }}" 
               :class="isActive('mitrapanel.history-promo') ? 'text-orange-600' : 'text-gray-600'"
               class="flex flex-col items-center justify-center px-2 py-1 hover:bg-gray-50 transition active:scale-95">
                <span class="text-lg mb-0.5">📸</span>
                <span class="text-[10px] font-medium">Promo</span>
            </a>
        </div>

        {{-- Bagian Tengah: Home --}}
        <div class="flex">
            <a href="{{ route('mitrapanel.dashboard') }}" 
               :class="isActive('mitrapanel.dashboard') ? 'text-orange-600' : 'text-gray-600'"
               class="flex flex-col items-center justify-center px-3 py-1 hover:bg-gray-50 transition active:scale-95">
                <span class="text-lg mb-0.5">🏠</span>
                <span class="text-[10px] font-medium">Home</span>
            </a>
        </div>

        {{-- Bagian Kanan: Faktur dan Settings --}}
        <div class="flex space-x-4">
            {{-- Download Faktur Pajak --}}
            <a href="{{ route('mitrapanel.faktur-pajak') }}" 
               :class="isActive('mitrapanel.faktur-pajak') ? 'text-orange-600' : 'text-gray-600'"
               class="flex flex-col items-center justify-center px-2 py-1 hover:bg-gray-50 transition active:scale-95">
                <span class="text-lg mb-0.5">📄</span>
                <span class="text-[10px] font-medium">Faktur</span>
            </a>

            {{-- Settings --}}
            <a href="{{ route('mitrapanel.settings') }}" 
               :class="isActive('mitrapanel.settings') ? 'text-orange-600' : 'text-gray-600'"
               class="flex flex-col items-center justify-center px-2 py-1 hover:bg-gray-50 transition active:scale-95">
                <span class="text-lg mb-0.5">⚙️</span>
                <span class="text-[10px] font-medium">Settings</span>
            </a>
        </div>

    </nav>
</div>