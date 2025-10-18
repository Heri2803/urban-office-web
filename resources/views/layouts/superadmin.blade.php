<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Superadmin Panel') - Urban Office</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/LOGO_URBAN_OFFICE.png?v=1') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="//unpkg.com/alpinejs" defer></script>
    @stack('styles')
</head>
<body class="antialiased bg-gray-50 text-gray-900">

    {{-- ========================================================== --}}
    {{-- WRAPPER UTAMA: Sidebar + Content --}}
    {{-- ========================================================== --}}
    <div class="flex min-h-screen bg-gray-50">

        {{-- === SIDEBAR === --}}
        {{-- Sidebar fixed di kiri, responsive di mobile --}}
        <aside class="fixed md:static inset-y-0 left-0 w-64 md:w-64 z-50">
            @include('layouts.superadmin.components.sidebar')
        </aside>

        {{-- === AREA KONTEN (HEADER + MAIN) === --}}
        <div class="flex-1 flex flex-col md:ml-60">

            {{-- === HEADER (Sticky) === --}}
            <header class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
                <div class="px-4 md:px-6 py-3 flex justify-between items-center">
                    <h1 class="text-lg font-semibold">@yield('page_title', 'Dashboard')</h1>
                    <div>
                        {{-- Area user dropdown atau notifikasi --}}
                        @yield('header_right')
                    </div>
                </div>
            </header>

            {{-- === AREA UTAMA === --}}
            <main class="flex-1 bg-gray-50 p-4 md:p-6 lg:p-8 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
