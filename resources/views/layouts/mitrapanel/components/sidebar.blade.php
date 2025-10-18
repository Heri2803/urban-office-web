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

        {{-- Lokasi Mitra (Dropdown) --}}
        <div class="relative">
            <button 
                @click="openLokasiDropdown = !openLokasiDropdown" 
                type="button"
                class="flex items-center w-full px-3 py-2 rounded-lg hover:bg-gray-50 transition group text-gray-700">
                <span class="text-lg mr-3">📍</span>
                <span class="flex-1 text-left font-medium">Lokasi Mitra</span>
                <svg xmlns="http://www.w3.org/2000/svg" 
                     :class="{ 'rotate-180': openLokasiDropdown }" 
                     class="w-4 h-4 transition-transform duration-200" 
                     fill="none" 
                     viewBox="0 0 24 24" 
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Dropdown List --}}
            <div x-show="openLokasiDropdown" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 class="ml-8 mt-2 space-y-1 max-h-60 overflow-y-auto">
                <template x-for="location in mitraLocations" :key="location.id">
                    <a :href="`/mitrapanel/lokasi/${location.slug}`" 
                       class="block px-3 py-2 rounded-md hover:bg-gray-50 text-sm transition text-gray-600">
                        <span x-text="location.name"></span>
                    </a>
                </template>
            </div>
        </div>

        {{-- history promo --}}
        <a href="{{ route('mitrapanel.history-promo') }}" 
           :class="isActive('mitrapanel.history-promo') ? 'bg-orange-100 text-orange-600' : 'text-gray-700'"
           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-50 transition group">
            <span class="text-lg mr-3">📸</span>
            <span class="font-medium">history promo</span>
        </a>

        {{-- Download Faktur Pajak --}}
        <a href="{{ route('mitrapanel.faktur') }}" 
           :class="isActive('mitrapanel.faktur') ? 'bg-orange-100 text-orange-600' : 'text-gray-700'"
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
     Menggunakan pola yang sama dengan referensi
============================================ --}}
<div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50">
    <nav class="grid grid-cols-4 divide-x divide-gray-200">
        
        {{-- Home --}}
        <a href="{{ route('mitrapanel.dashboard') }}" 
           :class="isActive('mitrapanel.dashboard') ? 'text-orange-600' : 'text-gray-600'"
           class="flex flex-col items-center justify-center py-3 hover:bg-gray-50 transition active:scale-95">
            <span class="text-xl mb-1">🏠</span>
            <span class="text-xs font-medium">Home</span>
        </a>

        {{-- Lokasi Mitra (Popup) --}}
        <div class="relative flex flex-col items-center justify-center">
            <button type="button"
                    @click="openLokasiDropdown = !openLokasiDropdown"
                    :class="openLokasiDropdown ? 'bg-gray-50 text-orange-600' : 'text-gray-600'"
                    class="w-full h-full flex flex-col items-center justify-center py-3 hover:bg-gray-50 transition active:scale-95">
                <span class="text-xl mb-1">📍</span>
                <span class="text-xs font-medium">Lokasi</span>
            </button>

            {{-- Popup Modal untuk Mobile --}}
            <div x-show="openLokasiDropdown"
                 x-cloak
                 @click.away="openLokasiDropdown = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                 class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 w-72 bg-white text-slate-900 rounded-lg shadow-2xl ring-1 ring-black/10 overflow-hidden z-50">
                
                {{-- Header Popup --}}
                <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-700">Pilih Lokasi Mitra</h3>
                    <button @click="openLokasiDropdown = false" 
                            type="button"
                            class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- List Lokasi (Scrollable) --}}
                <div class="max-h-64 overflow-y-auto">
                    <template x-for="location in mitraLocations" :key="location.id">
                        <a :href="`/mitrapanel/lokasi/${location.slug}`"
                           @click="openLokasiDropdown = false"
                           class="flex items-center px-4 py-3 hover:bg-slate-50 transition border-b border-slate-100 last:border-b-0">
                            <span class="text-lg mr-3">📍</span>
                            <span class="text-sm font-medium text-slate-700" x-text="location.name"></span>
                        </a>
                    </template>
                </div>
            </div>
        </div>

        {{-- history promo --}}
        <a href="{{ route('mitrapanel.history-promo') }}" 
           :class="isActive('mitrapanel.history-promo') ? 'text-orange-600' : 'text-gray-600'"
           class="flex flex-col items-center justify-center py-3 hover:bg-gray-50 transition active:scale-95">
            <span class="text-xl mb-1">📸</span>
            <span class="text-xs font-medium">History Promo</span>
        </a>

        {{-- Settings --}}
        <a href="{{ route('mitrapanel.settings') }}" 
           :class="isActive('mitrapanel.settings') ? 'text-orange-600' : 'text-gray-600'"
           class="flex flex-col items-center justify-center py-3 hover:bg-gray-50 transition active:scale-95">
            <span class="text-xl mb-1">⚙️</span>
            <span class="text-xs font-medium">Settings</span>
        </a>

    </nav>
</div>