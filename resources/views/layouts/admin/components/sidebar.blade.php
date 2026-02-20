{{-- resources/views/layouts/admin/components/sidebar.blade.php --}}

<style>[x-cloak] { display: none !important; }</style>

{{-- ===========================================
     DESKTOP & TABLET SIDEBAR
     Tampilan sidebar kiri hanya muncul di md ke atas
=========================================== --}}
<div    
    x-data="{ 
        openBooking: {{ request()->routeIs('admin.booking.*') ? 'true' : 'false' }}, 
        openContent: {{ request()->routeIs('admin.content.*') ? 'true' : 'false' }},
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
                    {{-- Optional: Badge untuk pending count --}}
                    {{-- <span class="px-1.5 py-0.5 text-xs bg-red-100 text-red-600 rounded-full">3</span> --}}
                </a>
                
                <a href="{{ route('admin.booking.room-assignment') }}" 
                   class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.booking.room-assignment') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                    Room Assignment
                </a>
                
                <a href="{{ route('admin.booking.service-confirmation') }}" 
                   class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.booking.service-confirmation') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                    Service Confirmation
                </a>
                
                <!-- <a href="{{ route('admin.booking.history') }}" 
                   class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.booking.history') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                    Booking History
                </a> -->
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
                
                <a href="{{ route('admin.banners.index') }}" 
                    class="block px-2 py-1.5 rounded transition {{ request()->routeIs('banners.*') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                        Banner Promo
                </a>
                
                <a href="{{ route('admin.content.highlights') }}" 
                   class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.content.highlights') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                    Service Highlights
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
            {{-- Optional: Badge untuk unread messages --}}
            {{-- <span class="ml-auto px-2 py-0.5 text-xs bg-red-100 text-red-600 rounded-full">3</span> --}}
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
     Horizontal scrollable navigation di bottom
=========================================== --}}
<div 
    x-data="{ 
        showBookingModal: false, 
        showContentModal: false 
    }"
    class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50"
>
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
        <!-- <a href="{{ route('admin.booking.history') }}" 
           @click="showBookingModal = false"
           class="flex items-center px-4 py-2.5 hover:bg-gray-50 transition {{ request()->routeIs('admin.booking.history') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700' }}">
            <span class="text-sm">Booking History</span>
        </a> -->
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
        <a href="{{ route('admin.banners.index') }}" 
           @click="showContentModal = false"
           class="flex items-center px-4 py-2.5 hover:bg-gray-50 transition {{ request()->routeIs('banners.*') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700' }}">
            <span class="text-sm">Banner Promo</span>
        </a>
        <a href="{{ route('admin.content.highlights') }}" 
           @click="showContentModal = false"
           class="flex items-center px-4 py-2.5 hover:bg-gray-50 transition {{ request()->routeIs('admin.content.highlights') ? 'bg-orange-50 text-orange-600 font-medium' : 'text-gray-700' }}">
            <span class="text-sm">Service Highlights</span>
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