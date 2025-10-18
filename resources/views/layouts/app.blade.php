<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel App') }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/LOGO_URBAN_OFFICE.png?v=1') }}">

    {{-- Tailwind CSS & Vite --}}
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- PENTING: AlpineJS perlu dimuat di sini (jika tidak dimuat via app.js) --}}
    {{-- CATATAN: Jika Anda sudah mengimpor Alpine di resources/js/app.js, hapus baris di bawah ini. --}}
    <script src="//unpkg.com/alpinejs" defer></script> 

    @yield('head')
</head>
<body class="antialiased bg-gray-50 text-gray-900">

    {{-- SidebarData dengan Alpine.data() HARUS BERADA DI ATAS KOMPONEN x-data --}}
    {{-- Ini adalah perbaikan utama untuk urutan loading --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sidebar', () => ({
                activeItem: 'Beranda',
                // Variabel menuItems didefinisikan dengan benar di sini
                menuItems: [
                {
                    name: 'Beranda',
                    href: '/dashboard/home',
                    isAction: false,
                    // ICON BARU: Rumah/Dashboard (Solid & Modern)
                    icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L3 9v12h6v-7h6v7h6V9z"/>
                            </svg>`
                },
                {
                    name: 'Sewa',
                    href: '/dashboard/bookingform',
                    isAction: false,
                    // ICON BARU: Kalender dengan Marker (Representasi Booking/Jadwal)
                    icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 4h-2V2h-2v2H9V2H7v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.89-2-2-2m0 16H5V8h14z"/>
                                <path d="M12 11h-2v2h2v-2zm3 0h-2v2h2v-2zm-6 3H7v2h2v-2z"/>
                            </svg>`
                },
                {
                    name: 'Mitra',
                    href: '/dashboard/mitra',
                    isAction: false,
                    // ICON BARU: People / Network (Simbol Kemitraan)
                    icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 17v-1c0-1.33-2.67-2-4-2s-4 .67-4 2v1h8m3 2H5v-2c0-2.67 5.33-4 8-4s8 1.33 8 4v2m-4-7a4 4 0 1 0 0-8 4 4 0 0 0 0 8m-7-9a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>
                            </svg>`
                },
                {
                    name: 'Akun',
                    href: '/dashboard/profile',
                    isAction: false,
                    // ICON BARU: User (Simbol Pengguna Solid)
                    icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>`
                },
                {
                    name: 'Logout',
                    isAction: true,
                    // ICON BARU: Power / Exit (Tombol Keluar)
                    icon: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 9v-2c0-2.76-2.24-5-5-5H7c-2.76 0-5 2.24-5 5v10c0 2.76 2.24 5 5 5h4c2.76 0 5-2.24 5-5v-2h-2v2c0 1.66-1.34 3-3 3H7c-1.66 0-3-1.34-3-3V7c0-1.66 1.34-3 3-3h4c1.66 0 3 1.34 3 3v2h2zm-4-4h2v4h-2V5zm4 7h-6v2h6v-2z"/>
                            </svg>`
                }
            ],
                // Fungsi init() menggantikan x-init="initSidebar()"
                init() {
                    // Loop untuk memastikan aksi logout didefinisikan
                    this.menuItems = this.menuItems.map(item => {
                        if (item.isAction && item.name === 'Logout') {
                            item.action = () => this.handleLogout();
                        }
                        return item;
                    });

                    // Logika untuk menentukan item aktif
                    const currentPath = window.location.pathname;
                    const active = this.menuItems.find(item => item.href === currentPath);
                    if (active) this.activeItem = active.name;
                },
                setActiveItem(name) {
                    this.activeItem = name;
                },
                handleLogout() {
                    // Logika Logout (dibiarkan tidak berubah karena sudah benar)
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
                // Getter methods (dibiarkan tidak berubah)
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
            }))
        });
    </script>
    
    {{-- Sidebar (ELEMEN x-data HARUS ADA SETELAH SCRIPT DEFINISINYA) --}}
    <div x-data="sidebar" class="sidebar-wrapper">
        @include('layouts.components.sidebar')
    </div>

    {{-- Content --}}
    <div class="content-wrapper">
        @yield('content')
    </div>

    {{-- Footer --}}
    @yield('footer')

</body>
</html>