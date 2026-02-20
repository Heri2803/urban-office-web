<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Urban Office</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/LOGO_URBAN_OFFICE.png?v=1') }}">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>

<body class="antialiased bg-gray-50 text-gray-900">

    <div class="min-h-screen flex bg-gray-50" x-data="{ sidebarOpen: false }">

        {{-- SIDEBAR (Admin Navigation) --}}
        @include('layouts.admin.components.sidebar')

        {{-- OVERLAY (mobile) --}}
        <div 
            class="fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden"
            x-show="sidebarOpen"
            x-transition.opacity
            @click="sidebarOpen = false">
        </div>

        {{-- MAIN WRAPPER --}}
        <div class="flex-1 flex flex-col md:ml-60 transition-all duration-200">

            {{-- HEADER --}}
            <header class="sticky top-0 z-40 ">
                <div class="px-4 md:px-6 bg-white rounded-xl shadow-sm border border-gray-200">
                    @include('layouts.admin.components.header-profile')
                </div>
            </header>

            {{-- MAIN CONTENT --}}
            <main class="flex-1 p-4 md:p-6 lg:p-8 bg-gray-50">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
