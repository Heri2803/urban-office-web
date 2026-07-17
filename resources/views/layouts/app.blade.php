<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel App') }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('/assets/LOGO_URBAN_OFFICE.png?v=1') }}">

    {{-- ✅ TAMBAHAN: PWA Meta Tags --}}
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    <meta name="theme-color" content="#4A90E2">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('/assets/LOGO_URBAN_OFFICE.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('/assets/LOGO_URBAN_OFFICE.png') }}">

    {{-- Tailwind CSS & Vite --}}
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- AlpineJS --}}
    <script src="//unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    {{-- STYLE GLOBAL - TETAP SAMA PERSIS --}}
    <style>
        html, body {
            overflow-x: hidden !important;
            width: 100% !important;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        
        *, *::before, *::after {
            box-sizing: border-box;
        }
        
        #info-section .info-card {
            min-width: 0;
            flex-shrink: 0;
        }
    
        @media (max-width: 767px) {
            .flex-1.ml-0 {
                margin-bottom: 2.5rem !important;
            }
            
            .md\:ml-60, .lg\:ml-64, .xl\:ml-64 {
                margin-left: 0 !important;
            }
        }
        
        .sidebar-wrapper > div.md\:hidden {
            z-index: 9999 !important;
        }

        @media (min-width: 768px) {
            .sidebar-wrapper > div:first-child {
                display: none !important;
            }

            [class*="md:ml-60"],
            [class*="lg:ml-64"],
            [class*="xl:ml-64"] {
                margin-left: 0 !important;
            }
        }
    </style>

    @yield('head')

    {{-- Inisialisasi Alpine Data - TETAP SAMA PERSIS --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('layout', () => ({
                isSidebarHidden: false,
                activeItem: 'Beranda',
                isSidebarOpen: true,

                menuItems: [
                    {
                        name: 'Beranda',
                        href: '/dashboard/home',
                        isAction: false,
                        icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L3 9v12h6v-7h6v7h6V9z"/>
                                </svg>`
                    },
                    {
                        name: 'Sewa',
                        href: '/dashboard/bookingform',
                        isAction: false,
                        icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 4h-2V2h-2v2H9V2H7v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.89-2-2-2m0 16H5V8h14z"/>
                                    <path d="M12 11h-2v2h2v-2zm3 0h-2v2h2v-2zm-6 3H7v2h2v-2z"/>
                                </svg>`
                    },
                    {
                        name: 'Mitra',
                        href: '/dashboard/mitra',
                        isAction: false,
                        icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 17v-1c0-1.33-2.67-2-4-2s-4 .67-4 2v1h8m3 2H5v-2c0-2.67 5.33-4 8-4s8 1.33 8 4v2m-4-7a4 4 0 1 0 0-8 4 4 0 0 0 0 8m-7-9a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>
                                </svg>`
                    },
                    {
                        name: 'Akun',
                        href: '/dashboard/profile',
                        isAction: false,
                        icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>`
                    },
                    {
                        name: 'Logout',
                        isAction: true,
                        icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 9v-2c0-2.76-2.24-5-5-5H7c-2.76 0-5 2.24-5 5v10c0 2.76 2.24 5 5 5h4c2.76 0 5-2.24 5-5v-2h-2v2c0 1.66-1.34 3-3 3H7c-1.66 0-3-1.34-3-3V7c0-1.66 1.34-3 3-3h4c1.66 0 3 1.34 3 3v2h2zm-4-4h2v4h-2V5zm4 7h-6v2h6v-2z"/>
                                </svg>`
                    }
                ],

                init() {
                    this.menuItems = this.menuItems.map(item => {
                        if (item.isAction && item.name === 'Logout') {
                            item.action = () => this.handleLogout();
                        }
                        return item;
                    });

                    const currentPath = window.location.pathname;
                    const active = this.menuItems.find(item => item.href === currentPath);
                    if (active) this.activeItem = active.name;

                    const savedState = localStorage.getItem('sidebarOpen');
                    if (savedState !== null) {
                        this.isSidebarOpen = JSON.parse(savedState);
                    }
                },

                setActiveItem(itemName) {
                    this.activeItem = itemName;
                    localStorage.setItem('activeMenuItem', itemName);
                },

                toggleSidebar() {
                    this.isSidebarOpen = !this.isSidebarOpen;
                    localStorage.setItem('sidebarOpen', JSON.stringify(this.isSidebarOpen));
                },

                handleLogout() {
                    this.setActiveItem('Logout');
                    sessionStorage.removeItem('unreadToastShown');
                    
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/logout';

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;

                    form.appendChild(csrfInput);
                    document.body.appendChild(form);
                    form.submit();
                },

                getDesktopButtonClass(name) {
                    return this.activeItem === name
                        ? 'bg-orange-100 text-orange-600'
                        : 'text-gray-700 hover:bg-gray-50';
                },

                getIconClass(name) {
                    return this.activeItem === name
                        ? 'text-orange-600'
                        : 'text-gray-400 group-hover:text-gray-500';
                },

                getTextClass(name) {
                    return this.activeItem === name
                        ? 'text-orange-600'
                        : 'text-gray-900 group-hover:text-gray-700';
                },

                getMobileButtonClass(name) {
                    return this.activeItem === name
                        ? 'text-orange-600'
                        : 'text-gray-500 hover:text-orange-500';
                },
            }));
        });
    </script>
</head>

<body class="antialiased bg-gray-50 text-gray-900" x-data="layout">

    {{-- ✅ TAMBAHAN: Notification Manager (1 baris saja) --}}
    @include('layouts.components.notification-manager')

    {{-- Sidebar --}}
    <div class="sidebar-wrapper">
        @include('layouts.components.sidebar')
    </div>

    {{-- Content --}}
    <div class="content-wrapper">
        {{-- Tombol Back Navigation --}}
        @include('layouts.components.navbar-back')
        @yield('content')
    </div>

    {{-- Footer & komponen tambahan --}}
    @yield('footer')
    @include('layouts.components.maintanance-modal')
    @include('layouts.components.commingsoon-modal')
    @include('layouts.components.promo-modal')
    
    @stack('scripts')

</body>
</html>