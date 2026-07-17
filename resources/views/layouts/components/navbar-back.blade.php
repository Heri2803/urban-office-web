{{-- ============================================================
     navbar-back.blade.php
     Tombol navigasi: Back to Home + Back to Previous Page
     Hanya muncul jika BUKAN halaman home (/dashboard/home)
     ============================================================ --}}
@php
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    $isHomePage   = $currentRoute === 'dashboard.home';
@endphp

@if(!$isHomePage)
<div id="navbar-back" class="navbar-back-wrapper flex items-center gap-2 px-4 md:px-6 pt-4 pb-1">

    {{-- Tombol Back to Previous Page --}}
    <button
        onclick="window.history.back()"
        title="Kembali ke halaman sebelumnya"
        class="nav-back-btn group inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
               bg-white border border-gray-200 shadow-sm
               text-gray-600 text-xs font-medium
               hover:bg-orange-50 hover:border-orange-300 hover:text-orange-600
               active:scale-95 transition-all duration-200"
    >
        {{-- Arrow Left Icon --}}
        <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover:-translate-x-0.5"
             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        <span>Kembali</span>
    </button>

    {{-- Separator --}}
    <span class="text-gray-300 text-xs select-none">/</span>

    {{-- Tombol Back to Home --}}
    <a
        href="{{ route('dashboard.home') }}"
        title="Kembali ke halaman utama"
        class="nav-home-btn group inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
               bg-orange-500 border border-orange-500 shadow-sm
               text-white text-xs font-medium
               hover:bg-orange-600 hover:border-orange-600 hover:shadow-md
               active:scale-95 transition-all duration-200"
    >
        {{-- Home Icon --}}
        <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover:scale-110"
             fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2L3 9v12h6v-7h6v7h6V9z"/>
        </svg>
        <span>Beranda</span>
    </a>

</div>
@endif
