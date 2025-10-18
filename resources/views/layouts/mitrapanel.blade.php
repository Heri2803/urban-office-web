<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mitra Panel') - Urban Office</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/LOGO_URBAN_OFFICE.png?v=1') }}">
    
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    
    {{-- Alpine.js (jika belum ada di app.js) --}}
    <script src="//unpkg.com/alpinejs" defer></script>
    
    @stack('styles')
</head>
<body class="antialiased bg-gray-50 text-gray-900">

    {{-- ========================================================== --}}
    {{-- Alpine.js Data Store untuk Sidebar --}}
    {{-- ========================================================== --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('mitraSidebar', () => ({
                openLokasiDropdown: false,
                currentPage: '{{ request()->route()->getName() ?? "home" }}',
                
                mitraLocations: [
                    { id: 1, name: 'Urban Office Jakarta Pusat', slug: 'jakarta-pusat' },
                    { id: 2, name: 'Urban Office Surabaya', slug: 'surabaya' },
                    { id: 3, name: 'Urban Office Bandung', slug: 'bandung' },
                    { id: 4, name: 'Urban Office Yogyakarta', slug: 'yogyakarta' },
                    { id: 5, name: 'Urban Office Bali', slug: 'bali' }
                ],
                
                isActive(page) {
                    return this.currentPage === page;
                }
            }))
        });
    </script>

    {{-- ========================================================== --}}
    {{-- SIDEBAR WRAPPER dengan Alpine x-data --}}
    {{-- ========================================================== --}}
    <div x-data="mitraSidebar" class="sidebar-wrapper">
        @include('layouts.mitrapanel.components.sidebar')
    </div>

    {{-- ========================================================== --}}
    {{-- CONTENT WRAPPER --}}
    {{-- md:ml-64: Margin kiri HARUS SAMA dengan lebar sidebar (w-64) --}}
    {{-- ========================================================== --}}
    <div class="content-wrapper md:ml-60 pb-20 md:pb-0">
        
        {{-- HEADER PROFIL (sticky di atas) --}}
        <header class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
            <div class="px-4 md:px-6">
                @include('layouts.mitrapanel.components.header-profile')
            </div>
        </header>

        {{-- === AREA KONTEN (MAIN) === --}}
        <main class="flex-1 bg-gray-50 p-6 md:p-8 min-h-screen">
            
            {{-- Alert / Flash Messages --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     x-init="setTimeout(() => show = false, 3000)"
                     class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" 
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" 
                              clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     x-init="setTimeout(() => show = false, 3000)"
                     class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" 
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" 
                              clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Konten halaman --}}
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>