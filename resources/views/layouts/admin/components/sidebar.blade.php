{{-- resources/views/layouts/admin/components/sidebar.blade.php --}}

<style>[x-cloak] { display: none !important; }</style>

{{-- ===========================================
     DESKTOP & TABLET SIDEBAR
     Tampilan sidebar kiri hanya muncul di md ke atas
=========================================== --}}
<div    
    x-data="{ 
        openBooking: {{ request()->routeIs('admin.booking.*') ? 'true' : 'false' }}, 
        openVirtualOffice: {{ request()->routeIs('admin.virtual-office.*') || request()->routeIs('admin.invoices.*') || request()->routeIs('admin.contracts.*') || request()->routeIs('admin.addendums.*') ? 'true' : 'false' }},
        openContent: {{ request()->routeIs('admin.content.*') ? 'true' : 'false' }},
        openPromo: {{ request()->routeIs('banners.*') || request()->routeIs('admin.promo-usage.*') ? 'true' : 'false' }},
        openMessages: {{ request()->routeIs('admin.messages') ? 'true' : 'false' }} 
    }"
    class="hidden md:flex md:flex-col md:fixed md:inset-y-0 md:left-0 w-64 bg-white border-r border-gray-200 shadow-sm z-50"
>
    {{-- Header --}}
    <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col items-center text-center">
            {{-- Logo --}}
            <img 
                src="{{ asset('assets/LOGO_URBAN_OFFICE.png') }}" 
                alt="Logo Urban Office" 
                class="w-24 h-24 object-contain"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            >
            
            {{-- Teks di bawah Logo --}}
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Admin Panel</h2>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto text-gray-800">
        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-orange-50 text-orange-600' : 'text-gray-800 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        {{-- Booking --}}
        <div>
            <button 
                @click="openBooking = !openBooking"
                class="flex items-center justify-between w-full px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.booking.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-800 hover:bg-gray-100' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium">Booking</span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" 
                     :class="{ 'rotate-180': openBooking }" 
                     class="w-4 h-4 transition-transform duration-200" 
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div 
                x-show="openBooking" 
                x-cloak
                x-transition
                class="ml-10 mt-2 space-y-1 text-sm"
            >
                <a href="{{ route('admin.booking.all') }}" 
                   class="flex items-center justify-between px-2 py-1.5 rounded transition {{ request()->routeIs('admin.booking.all') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                    <span>All Bookings</span>
                </a>
                
                <a href="{{ route('admin.booking.room-assignment') }}" 
                   class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.booking.room-assignment') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                    Room Assignment
                </a>
                
                <a href="{{ route('admin.booking.service-confirmation') }}" 
                   class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.booking.service-confirmation') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                    Service Confirmation
                </a>
            </div>
        </div>

        {{-- Room Management --}}
        <a href="{{ route('admin.room-management') }}" 
           class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.room-management') ? 'bg-orange-50 text-orange-600' : 'text-gray-800 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span class="font-medium">Room Management</span>
        </a>

        {{-- ✅ VIRTUAL OFFICE MANAGEMENT - TAMBAHKAN DI SINI --}}
        <a href="{{ route('admin.virtual-office.index') }}" 
           class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.virtual-office.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-800 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span class="font-medium">Virtual Office</span>
            {{-- Optional: Badge untuk jumlah pending --}}
            @php
                $voPendingCount = \App\Models\Transaction::where('room_type', 'Virtual Office')
                    ->where('status', 'pending')
                    ->count();
            @endphp
            @if($voPendingCount > 0)
                <span class="ml-auto px-2 py-0.5 text-xs bg-orange-100 text-orange-600 rounded-full">
                    {{ $voPendingCount }}
                </span>
            @endif
        </a>

        {{-- ✅ INVOICE MANAGEMENT - TAMBAHKAN DI SINI (setelah Virtual Office) --}}
        <a href="{{ route('admin.invoices.index') }}" 
        class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.invoices.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-800 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="font-medium">Invoice Management</span>
            {{-- Badge untuk jumlah invoice pending atau pending approval --}}
            @php
                if (auth()->check() && auth()->user()->role === 'finance') {
                    $badgeCount = \App\Models\Invoice::where('settlement_request_status', 'pending')->count();
                    $badgeBg = 'bg-blue-100 text-blue-600';
                } else {
                    $badgeCount = \App\Models\Invoice::pending()->count();
                    $badgeBg = 'bg-orange-100 text-orange-600';
                }
            @endphp
            @if($badgeCount > 0)
                <span class="ml-auto px-2 py-0.5 text-xs {{ $badgeBg }} rounded-full">
                    {{ $badgeCount }}
                </span>
            @endif
        </a>

        {{-- ✅ CONTRACT MANAGEMENT --}}
        <a href="{{ route('admin.contracts.index') }}" 
        class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.contracts.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-800 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span class="font-medium">Contract Management</span>
        </a>

        {{-- ✅ ADDENDUM MANAGEMENT --}}
        <a href="{{ route('admin.addendums.index') }}"
        class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.addendums.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-800 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <span class="font-medium">Addendum Management</span>
            @php
                $addendumActiveCount = \App\Models\Addendum::where('status', 'active')->count();
            @endphp
            @if($addendumActiveCount > 0)
                <span class="ml-auto px-2 py-0.5 text-xs bg-orange-100 text-orange-600 rounded-full">
                    {{ $addendumActiveCount }}
                </span>
            @endif
        </a>

        {{-- ✅ SURAT MASUK MANAGEMENT --}}
        <a href="{{ route('admin.surats.index') }}"
           class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.surats.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-800 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span class="font-medium">Manajemen Surat</span>
        </a>

        {{-- Content Management --}}
        <div>
            <button 
                @click="openContent = !openContent"
                class="flex items-center justify-between w-full px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.content.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-800 hover:bg-gray-100' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="font-medium">Content Management</span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" 
                     :class="{ 'rotate-180': openContent }" 
                     class="w-4 h-4 transition-transform duration-200" 
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div 
                x-show="openContent" 
                x-cloak
                x-transition
                class="ml-10 mt-2 space-y-1 text-sm"
            >
                <a href="{{ route('admin.content.service-photos') }}" 
                   class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.content.service-photos') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                    Service Photos
                </a>
                

                
                <a href="{{ route('admin.content.highlights') }}" 
                   class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.content.highlights') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                    Service Highlights
                </a>
            </div>
        </div>

        {{-- Promo Management --}}
        <div>
            <button 
                @click="openPromo = !openPromo"
                class="flex items-center justify-between w-full px-3 py-2 rounded-lg transition {{ request()->routeIs('banners.*') || request()->routeIs('admin.promo-usage.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-800 hover:bg-gray-100' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span class="font-medium">Promo Management</span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" 
                     :class="{ 'rotate-180': openPromo }" 
                     class="w-4 h-4 transition-transform duration-200" 
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div 
                x-show="openPromo" 
                x-cloak
                x-transition
                class="ml-10 mt-2 space-y-1 text-sm"
            >
                <a href="{{ route('admin.banners.index') }}" 
                    class="block px-2 py-1.5 rounded transition {{ request()->routeIs('banners.*') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                        Banner Promo
                </a>
                
                <a href="{{ route('admin.promo-usage.index') }}" 
                    class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.promo-usage.*') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                        Promo Usage Report
                </a>
            </div>
        </div>

        {{-- Pricing Management --}}
        <a href="{{ route('admin.pricing') }}" 
           class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.pricing') ? 'bg-orange-50 text-orange-600' : 'text-gray-800 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">Pricing & Promo</span>
        </a>

        {{-- Messages --}}
        <a href="{{ route('admin.messages') }}" 
        class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.messages') ? 'bg-orange-50 text-orange-600' : 'text-gray-800 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
            <span class="font-medium">Messages</span>
        </a>

        {{-- Settings --}}
        <a href="{{ route('admin.settings') }}" 
           class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.settings') ? 'bg-orange-50 text-orange-600' : 'text-gray-800 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="font-medium">Settings</span>
        </a>

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="POST" class="pt-4 border-t border-gray-200 mt-4">
            @csrf
            <button type="submit" 
                    class="w-full flex items-center px-3 py-2 text-red-600 rounded-lg hover:bg-red-50 transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="font-medium">Logout</span>
            </button>
        </form>
    </nav>
</div>

{{-- ===========================================
     MOBILE BOTTOM NAVIGATION BAR
=========================================== --}}
<div 
    x-data="{ 
        showBookingModal: false, 
        showContentModal: false,
        showPromoModal: false,
        showVOModal: false,
        showInvoiceModal: false 
    }"
    class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50"
>
    {{-- Virtual Office Popup Modal --}}
    <div 
        x-show="showVOModal"
        @click.away="showVOModal = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 py-2"
        x-cloak
    >
        <div class="px-3 py-2 border-b border-gray-100">
            <p class="text-sm font-semibold text-gray-800">Virtual Office</p>
        </div>
        <a href="{{ route('admin.virtual-office.index') }}" 
           @click="showVOModal = false"
           class="flex items-center justify-between px-4 py-2.5 hover:bg-gray-50 transition {{ request()->routeIs('admin.virtual-office.*') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700' }}">
            <span class="text-sm">Manajemen VO</span>
            @php
                $voPendingCount = \App\Models\Transaction::where('room_type', 'Virtual Office')
                    ->where('status', 'pending')
                    ->count();
            @endphp
            @if($voPendingCount > 0)
                <span class="px-2 py-0.5 text-xs bg-orange-100 text-orange-600 rounded-full">
                    {{ $voPendingCount }}
                </span>
            @endif
        </a>
    </div>

    {{-- INVOICE MANAGEMENT POPUP MODAL --}}
    <div 
        x-show="showInvoiceModal"
        @click.away="showInvoiceModal = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 py-2"
        x-cloak
    >
        <div class="px-3 py-2 border-b border-gray-100">
            <p class="text-sm font-semibold text-gray-800">Invoice Management</p>
        </div>
        <a href="{{ route('admin.invoices.index') }}" 
        @click="showInvoiceModal = false"
        class="flex items-center justify-between px-4 py-2.5 hover:bg-gray-50 transition {{ request()->routeIs('admin.invoices.*') && request('tab') !== 'approvals' ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700' }}">
            <span class="text-sm">Daftar Invoice</span>
            @php
                if (auth()->check() && auth()->user()->role === 'finance') {
                    $invoicePendingCountMobile = \App\Models\Invoice::where('settlement_request_status', 'pending')->count();
                    $badgeBgMobile = 'bg-blue-100 text-blue-600';
                } else {
                    $invoicePendingCountMobile = \App\Models\Invoice::pending()->count();
                    $badgeBgMobile = 'bg-orange-100 text-orange-600';
                }
            @endphp
            @if($invoicePendingCountMobile > 0)
                <span class="px-2 py-0.5 text-xs {{ $badgeBgMobile }} rounded-full">
                    {{ $invoicePendingCountMobile }}
                </span>
            @endif
        </a>
        
        @if(auth()->check() && auth()->user()->role === 'finance')
        <a href="{{ route('admin.invoices.index', ['tab' => 'approvals']) }}" 
        @click="showInvoiceModal = false"
        class="flex items-center justify-between px-4 py-2.5 hover:bg-gray-50 transition {{ request('tab') === 'approvals' ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700' }}">
            <span class="text-sm">Persetujuan Settlement</span>
            @php
                $approvalsPendingCountMobile = \App\Models\Invoice::where('settlement_request_status', 'pending')->count();
            @endphp
            @if($approvalsPendingCountMobile > 0)
                <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-600 rounded-full">
                    {{ $approvalsPendingCountMobile }}
                </span>
            @endif
        </a>
        @endif
        
        <a href="{{ route('admin.invoices.create') }}" 
        @click="showInvoiceModal = false"
        class="flex items-center px-4 py-2.5 hover:bg-gray-50 transition {{ request()->routeIs('admin.invoices.create') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700' }}">
            <span class="text-sm">Generate Invoice</span>
        </a>
    </div>

    {{-- Booking Popup Modal --}}
    <div 
        x-show="showBookingModal"
        @click.away="showBookingModal = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="absolute bottom-full left-4 mb-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 py-2"
        x-cloak
    >
        <div class="px-3 py-2 border-b border-gray-100">
            <p class="text-sm font-semibold text-gray-800">Booking Menu</p>
        </div>
        <a href="{{ route('admin.booking.all') }}" 
           @click="showBookingModal = false"
           class="flex items-center px-4 py-2.5 hover:bg-gray-50 transition {{ request()->routeIs('admin.booking.all') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700' }}">
            <span class="text-sm">All Bookings</span>
        </a>
        <a href="{{ route('admin.booking.room-assignment') }}" 
           @click="showBookingModal = false"
           class="flex items-center px-4 py-2.5 hover:bg-gray-50 transition {{ request()->routeIs('admin.booking.room-assignment') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700' }}">
            <span class="text-sm">Room Assignment</span>
        </a>
        <a href="{{ route('admin.booking.service-confirmation') }}" 
           @click="showBookingModal = false"
           class="flex items-center px-4 py-2.5 hover:bg-gray-50 transition {{ request()->routeIs('admin.booking.service-confirmation') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700' }}">
            <span class="text-sm">Service Confirmation</span>
        </a>
    </div>

    {{-- Content Management Popup Modal --}}
    <div 
        x-show="showContentModal"
        @click.away="showContentModal = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 py-2"
        x-cloak
    >
        <div class="px-3 py-2 border-b border-gray-100">
            <p class="text-sm font-semibold text-gray-800">Content Menu</p>
        </div>
        <a href="{{ route('admin.content.service-photos') }}" 
           @click="showContentModal = false"
           class="flex items-center px-4 py-2.5 hover:bg-gray-50 transition {{ request()->routeIs('admin.content.service-photos') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700' }}">
            <span class="text-sm">Service Photos</span>
        </a>

        <a href="{{ route('admin.content.highlights') }}" 
           @click="showContentModal = false"
           class="flex items-center px-4 py-2.5 hover:bg-gray-50 transition {{ request()->routeIs('admin.content.highlights') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700' }}">
            <span class="text-sm">Service Highlights</span>
        </a>
    </div>

    {{-- Promo Management Popup Modal --}}
    <div 
        x-show="showPromoModal"
        @click.away="showPromoModal = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 py-2"
        x-cloak
    >
        <div class="px-3 py-2 border-b border-gray-100">
            <p class="text-sm font-semibold text-gray-800">Promo Menu</p>
        </div>
        <a href="{{ route('admin.banners.index') }}" 
           @click="showPromoModal = false"
           class="flex items-center px-4 py-2.5 hover:bg-gray-50 transition {{ request()->routeIs('banners.*') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700' }}">
            <span class="text-sm">Banner Promo</span>
        </a>
        <a href="{{ route('admin.promo-usage.index') }}" 
           @click="showPromoModal = false"
           class="flex items-center px-4 py-2.5 hover:bg-gray-50 transition {{ request()->routeIs('admin.promo-usage.*') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700' }}">
            <span class="text-sm">Promo Usage Report</span>
        </a>
    </div>

    {{-- Scrollable Navigation Container --}}
    <nav class="flex items-center gap-2 px-4 py-3 overflow-x-scroll overflow-y-hidden" style="scroll-behavior: smooth; -webkit-overflow-scrolling: touch; scrollbar-width: none; -ms-overflow-style: none;">
        
        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}" 
           class="flex flex-col items-center justify-center flex-shrink-0 min-w-[70px] px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-xs font-medium whitespace-nowrap">Dashboard</span>
        </a>

        {{-- Booking with Modal --}}
        <button 
            @click="showBookingModal = !showBookingModal"
            class="flex flex-col items-center justify-center flex-shrink-0 min-w-[70px] px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.booking.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}"
        >
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="text-xs font-medium whitespace-nowrap">Booking</span>
        </button>

        {{-- Room Management --}}
        <a href="{{ route('admin.room-management') }}" 
           class="flex flex-col items-center justify-center flex-shrink-0 min-w-[70px] px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.room-management') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span class="text-xs font-medium whitespace-nowrap">Rooms</span>
        </a>

        {{-- ✅ VIRTUAL OFFICE - TAMBAHKAN DI SINI --}}
        <button 
            @click="showVOModal = !showVOModal"
            class="flex flex-col items-center justify-center flex-shrink-0 min-w-[70px] px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.virtual-office.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}"
        >
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span class="text-xs font-medium whitespace-nowrap">Virtual</span>
            @php
                $voPendingCountMobile = \App\Models\Transaction::where('room_type', 'Virtual Office')
                    ->where('status', 'pending')
                    ->count();
            @endphp
            @if($voPendingCountMobile > 0)
                <span class="absolute top-1 right-1 w-2 h-2 bg-orange-500 rounded-full"></span>
            @endif
        </button>

        {{-- ✅ INVOICE MANAGEMENT BUTTON - TAMBAHKAN DI SINI (setelah Virtual Office) --}}
        <button 
            @click="showInvoiceModal = !showInvoiceModal"
            class="flex flex-col items-center justify-center flex-shrink-0 min-w-[70px] px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.invoices.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}"
        >
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="text-xs font-medium whitespace-nowrap">Invoice</span>
            @php
                if (auth()->check() && auth()->user()->role === 'finance') {
                    $hasBadgeMobile = \App\Models\Invoice::where('settlement_request_status', 'pending')->exists();
                } else {
                    $hasBadgeMobile = \App\Models\Invoice::pending()->exists();
                }
            @endphp
            @if($hasBadgeMobile)
                <span class="absolute top-1 right-1 w-2 h-2 bg-orange-500 rounded-full"></span>
            @endif
        </button>

        {{-- ✅ CONTRACT MANAGEMENT BUTTON - MOBILE --}}
        <a href="{{ route('admin.contracts.index') }}" 
            class="flex flex-col items-center justify-center flex-shrink-0 min-w-[70px] px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.contracts.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}"
        >
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span class="text-xs font-medium whitespace-nowrap">Contract</span>
        </a>

        {{-- ✅ ADDENDUM MANAGEMENT BUTTON - MOBILE --}}
        <a href="{{ route('admin.addendums.index') }}"
            class="flex flex-col items-center justify-center flex-shrink-0 min-w-[70px] px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.addendums.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}"
        >
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <span class="text-xs font-medium whitespace-nowrap">Addendum</span>
        </a>

        {{-- ✅ SURAT MASUK MANAGEMENT BUTTON - MOBILE --}}
        <a href="{{ route('admin.surats.index') }}" 
            class="flex flex-col items-center justify-center flex-shrink-0 min-w-[70px] px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.surats.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}"
        >
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span class="text-xs font-medium whitespace-nowrap">Surat Masuk</span>
        </a>

        {{-- Content Management with Modal --}}
        <button 
            @click="showContentModal = !showContentModal"
            class="flex flex-col items-center justify-center flex-shrink-0 min-w-[70px] px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.content.*') || request()->routeIs('banners.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}"
        >
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="text-xs font-medium whitespace-nowrap">Content</span>
        </button>

        {{-- Promo Management --}}
        <button 
            @click="showPromoModal = !showPromoModal; showBookingModal = false; showContentModal = false; showVOModal = false; showInvoiceModal = false;"
            class="flex flex-col items-center justify-center flex-shrink-0 min-w-[70px] px-3 py-2 rounded-lg transition {{ request()->routeIs('banners.*') || request()->routeIs('admin.promo-usage.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}"
        >
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span class="text-xs font-medium whitespace-nowrap">Promo</span>
        </button>

        {{-- Pricing --}}
        <a href="{{ route('admin.pricing') }}" 
           class="flex flex-col items-center justify-center flex-shrink-0 min-w-[70px] px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.pricing') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-xs font-medium whitespace-nowrap">Pricing</span>
        </a>

        {{-- Messages --}}
        <a href="{{ route('admin.messages') }}" 
           class="flex flex-col items-center justify-center flex-shrink-0 min-w-[70px] px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.messages') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
            <span class="text-xs font-medium whitespace-nowrap">Messages</span>
        </a>

        {{-- Settings --}}
        <a href="{{ route('admin.settings') }}" 
           class="flex flex-col items-center justify-center flex-shrink-0 min-w-[70px] px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.settings') ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="text-xs font-medium whitespace-nowrap">Settings</span>
        </a>

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="POST" class="inline-block flex-shrink-0">
            @csrf
            <button type="submit" 
                    class="flex flex-col items-center justify-center min-w-[70px] px-3 py-2 text-red-600 rounded-lg hover:bg-red-50 transition">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="text-xs font-medium whitespace-nowrap">Logout</span>
            </button>
        </form>

    </nav>
</div>

{{-- CSS untuk menyembunyikan scrollbar --}}
<style>
    nav::-webkit-scrollbar {
        display: none;
    }
</style>