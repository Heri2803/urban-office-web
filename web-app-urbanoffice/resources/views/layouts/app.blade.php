<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel App') }}</title>

    {{-- Tailwind CSS --}}
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')

    {{-- CSRF Token (biar logout aman) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="antialiased bg-gray-50 text-gray-900">

    {{-- Sidebar --}}
    <div x-data="sidebarData()" x-init="initSidebar()" class="sidebar-wrapper">
        @include('layouts.components.sidebar')
    </div>

    {{-- Content --}}
    <div class="content-wrapper">
        @yield('content')
    </div>

    {{-- Footer --}}
    @yield('footer')

    {{-- Script Alpine + SidebarData --}}
    <script>
        function sidebarData() {
            return {
                activeItem: 'Beranda',
                menuItems: [
                    {
                        name: 'Beranda',
                        href: '/dashboard/home',
                        isAction: false,
                        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m-4 4h12a2 2 0
                                    002-2v-8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>`
                    },
                    {
                        name: 'Sewa',
                        href: '/dashboard/bookingform',
                        isAction: false,
                        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 7h18M3 12h18M3 17h18"/></svg>`
                    },
                    {
                        name: 'Mitra',
                        href: '/dashboard/mitra',
                        isAction: false,
                        icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a6 6 0 100 12 6 6 0 000-12zM2 18a8 8 0 0116 0H2z"/></svg>`
                    },
                    {
                        name: 'Akun',
                        href: '/dashboard/profile',
                        isAction: false,
                        icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 10a4 4 0 100-8 4 4 0 000 8zM4 16a6 6 0 1112 0H4z"/></svg>`
                    },
                    {
                        name: 'Logout',
                        isAction: true,
                        action() { this.handleLogout() },
                        icon: `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-10V5"/></svg>`
                    }
                ],
                initSidebar() {
                    this.menuItems = this.menuItems.map(item => {
                        if (item.isAction && item.name === 'Logout') {
                            item.action = () => this.handleLogout(); // bind ke Alpine
                        }
                        return item;
                    });

                    const currentPath = window.location.pathname;
                    const active = this.menuItems.find(item => item.href === currentPath);
                    if (active) this.activeItem = active.name;
                },
                setActiveItem(name) {
                    this.activeItem = name;
                },
                handleLogout() {
                // Buat form logout secara dinamis
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/logout';
                
                // Tambahkan CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
                
                // Submit form
                document.body.appendChild(form);
                form.submit();
                },
                getDesktopButtonClass(name) {
                    return this.activeItem === name
                        ? 'bg-orange-100 text-orange-600'
                        : 'text-gray-700 hover:bg-gray-50';
                },
                getMobileButtonClass(name) {
                    return this.activeItem === name
                        ? 'bg-orange-50 border-orange-500 text-orange-700'
                        : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800';
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
                }
            }
        }
    </script>

    <!-- Alpine.js harus terakhir -->
    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
