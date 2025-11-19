{{-- resources/views/layouts/admin/components/sidebar.blade.php --}}

<style>[x-cloak] { display: none !important; }</style>

{{-- ===========================================
     DESKTOP & TABLET SIDEBAR
     Tampilan sidebar kiri hanya muncul di md ke atas
=========================================== --}}
<div    
    x-data="{ 
        openBooking: {{ request()->routeIs('admin.booking.*') ? 'true' : 'false' }}, 
        openContent: {{ request()->routeIs('admin.content.*') ? 'true' : 'false' }}
    }"
    class="hidden md:flex md:flex-col md:fixed md:inset-y-0 md:left-0 w-64 bg-white border-r border-gray-200 shadow-sm z-50"
>
    {{-- Header --}}
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800">Admin Panel</h2>
        <p class="text-xs text-gray-500 mt-1">Urban Office Management</p>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto text-gray-800">
        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50' }}">
            <span class="text-lg mr-3">📊</span>
            <span class="font-medium">Dashboard</span>
        </a>

        {{-- Booking --}}
        <div>
            <button 
                @click="openBooking = !openBooking"
                class="flex items-center justify-between w-full px-3 py-2 rounded-lg transition text-gray-800 {{ request()->routeIs('admin.booking.*') ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50' }}">
                <div class="flex items-center">
                    <span class="text-lg mr-3">📅</span>
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
                   class="flex items-center justify-between px-2 py-1.5 rounded transition {{ request()->routeIs('admin.booking.all') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span>All Bookings</span>
                    {{-- Optional: Badge untuk pending count --}}
                    {{-- <span class="px-1.5 py-0.5 text-xs bg-red-100 text-red-600 rounded-full">3</span> --}}
                </a>
                
                <a href="{{ route('admin.booking.room-assignment') }}" 
                   class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.booking.room-assignment') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                    Room Assignment
                </a>
                
                <a href="{{ route('admin.booking.service-confirmation') }}" 
                   class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.booking.service-confirmation') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                    Service Confirmation
                </a>
                
                <a href="{{ route('admin.booking.history') }}" 
                   class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.booking.history') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                    Booking History
                </a>
            </div>
        </div>

        {{-- Room Management --}}
        <a href="{{ route('admin.room-management') }}" 
           class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.management') ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50' }}">
            <span class="text-lg mr-3">🏢</span>
            <span class="font-medium">Room Management</span>
        </a>

        {{-- Content Management --}}
        <div>
            <button 
                @click="openContent = !openContent"
                class="flex items-center justify-between w-full px-3 py-2 rounded-lg transition text-gray-800 {{ request()->routeIs('admin.content.*') ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50' }}">
                <div class="flex items-center">
                    <span class="text-lg mr-3">📝</span>
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
                   class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.content.service-photos') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                    Service Photos
                </a>
                
                <a href="{{ route('banners.index') }}" 
                    class="block px-2 py-1.5 rounded transition {{ request()->routeIs('banners.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                        Banner Promo
                </a>
                
                <a href="{{ route('admin.content.highlights') }}" 
                   class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.content.highlights') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                    Service Highlights
                </a>
            </div>
        </div>

        {{-- Pricing Management --}}
        <a href="{{ route('admin.pricing') }}" 
           class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.pricing') ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50' }}">
            <span class="text-lg mr-3">💰</span>
            <span class="font-medium">Pricing & Promo</span>
        </a>

        {{-- Settings --}}
        <a href="{{ route('admin.settings') }}" 
           class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.settings') ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50' }}">
            <span class="text-lg mr-3">⚙️</span>
            <span class="font-medium">Settings</span>
        </a>

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="POST" class="pt-4 border-t border-gray-200 mt-4">
            @csrf
            <button type="submit" 
                    class="w-full flex items-center px-3 py-2 text-red-600 rounded-lg hover:bg-red-50 transition">
                <span class="text-lg mr-3">🚪</span>
                <span class="font-medium">Logout</span>
            </button>
        </form>
    </nav>
</div>

{{-- ===========================================
     MOBILE SIDEBAR OVERLAY
     Full sidebar untuk mobile dengan hamburger
=========================================== --}}
<div 
    x-data="{ 
        openBooking: {{ request()->routeIs('admin.booking.*') ? 'true' : 'false' }}, 
        openContent: {{ request()->routeIs('admin.content.*') ? 'true' : 'false' }}
    }"
    class="md:hidden"
>
    {{-- Overlay Background --}}
    <div 
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 bg-black bg-opacity-50 z-40"
        x-cloak
    ></div>

    {{-- Sidebar Content --}}
    <div 
        x-show="sidebarOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl z-50 overflow-y-auto"
        x-cloak
    >
        {{-- Header --}}
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Admin Panel</h2>
                <p class="text-xs text-gray-500 mt-1">Urban Office</p>
            </div>
            <button @click="sidebarOpen = false" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Navigation (sama dengan desktop) --}}
        <nav class="p-4 space-y-2 text-gray-800">
            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}" 
               @click="sidebarOpen = false"
               class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50' }}">
                <span class="text-lg mr-3">📊</span>
                <span class="font-medium">Dashboard</span>
            </a>

            {{-- Booking --}}
            <div>
                <button 
                    @click="openBooking = !openBooking"
                    class="flex items-center justify-between w-full px-3 py-2 rounded-lg transition text-gray-800 {{ request()->routeIs('admin.booking.*') ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50' }}">
                    <div class="flex items-center">
                        <span class="text-lg mr-3">📅</span>
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
                       @click="sidebarOpen = false"
                       class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.booking.all') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                        All Bookings
                    </a>
                    <a href="{{ route('admin.booking.room-assignment') }}" 
                       @click="sidebarOpen = false"
                       class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.booking.room-assignment') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                        Room Assignment
                    </a>
                    <a href="{{ route('admin.booking.service-confirmation') }}" 
                       @click="sidebarOpen = false"
                       class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.booking.service-confirmation') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                        Service Confirmation
                    </a>
                    <a href="{{ route('admin.booking.history') }}" 
                       @click="sidebarOpen = false"
                       class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.booking.history') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                        Booking History
                    </a>
                </div>
            </div>

            {{-- Room Management --}}
            <a href="{{ route('admin.room-management') }}" 
               @click="sidebarOpen = false"
               class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.room-management') ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50' }}">
                <span class="text-lg mr-3">🏢</span>
                <span class="font-medium">Room Management</span>
            </a>

            {{-- Content Management --}}
            <div>
                <button 
                    @click="openContent = !openContent"
                    class="flex items-center justify-between w-full px-3 py-2 rounded-lg transition text-gray-800 {{ request()->routeIs('admin.content.*') ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50' }}">
                    <div class="flex items-center">
                        <span class="text-lg mr-3">📝</span>
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
                       @click="sidebarOpen = false"
                       class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.content.service-photos') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                        Service Photos
                    </a>
                    <a href="{{ route('banners.index') }}" 
                       @click="sidebarOpen = false"
                       class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.content.banners') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                        Banner Promo
                    </a>
                    <a href="{{ route('admin.content.highlights') }}" 
                       @click="sidebarOpen = false"
                       class="block px-2 py-1.5 rounded transition {{ request()->routeIs('admin.content.highlights') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                        Service Highlights
                    </a>
                </div>
            </div>

            {{-- Pricing --}}
            <a href="{{ route('admin.pricing') }}" 
               @click="sidebarOpen = false"
               class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.pricing') ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50' }}">
                <span class="text-lg mr-3">💰</span>
                <span class="font-medium">Pricing & Promo</span>
            </a>

            {{-- Settings --}}
            <a href="{{ route('admin.settings') }}" 
               @click="sidebarOpen = false"
               class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.settings') ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50' }}">
                <span class="text-lg mr-3">⚙️</span>
                <span class="font-medium">Settings</span>
            </a>

            {{-- Logout --}}
            <form action="{{ route('logout') }}" method="POST" class="pt-4 border-t border-gray-200 mt-4">
                @csrf
                <button type="submit" 
                        class="w-full flex items-center px-3 py-2 text-red-600 rounded-lg hover:bg-red-50 transition">
                    <span class="text-lg mr-3">🚪</span>
                    <span class="font-medium">Logout</span>
                </button>
            </form>
        </nav>
    </div>
</div>

{{-- ===========================================
     MOBILE HAMBURGER BUTTON
     Floating button di kanan bawah untuk toggle
=========================================== --}}
<button 
    @click="sidebarOpen = true"
    class="md:hidden fixed bottom-6 right-6 w-14 h-14 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition z-40 flex items-center justify-center"
>
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>