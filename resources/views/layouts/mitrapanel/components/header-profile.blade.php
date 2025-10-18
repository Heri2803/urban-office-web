{{-- resources/views/layouts/mitrapanel/components/header-profile.blade.php --}}

<div x-data="{ openProfileDropdown: false }" class="px-4 md:px-6 py-4">
    <div class="flex items-center justify-between">
        
        {{-- Left: Page Title (Mobile/Tablet) atau Breadcrumb --}}
        <div class="flex items-center space-x-3">

            <div>
                <h1 class="text-lg md:text-xl font-semibold text-gray-800">
                    @yield('page-title', 'Dashboard')
                </h1>
                <p class="text-xs md:text-sm text-gray-500 mt-0.5">
                    @yield('page-subtitle', 'Selamat datang di panel mitra')
                </p>
            </div>
        </div>

        {{-- Right: Profile Info & Dropdown --}}
        <div class="relative">
            <button 
                @click="openProfileDropdown = !openProfileDropdown"
                type="button"
                class="flex items-center space-x-2 md:space-x-3 p-2 rounded-lg hover:bg-gray-100 transition">
                
                {{-- Profile Info (Hidden di mobile kecil) --}}
                <div class="hidden sm:block text-right">
                    <p class="text-sm font-medium text-gray-800">
                        {{ auth()->user()->name ?? 'Nama Mitra' }}
                    </p>
                    <p class="text-xs text-gray-500">
                        Mitra Partner
                    </p>
                </div>

                {{-- Avatar --}}
                <div class="relative">
                    @if(auth()->user()->profile_photo ?? false)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" 
                             alt="Profile" 
                             class="w-10 h-10 rounded-full object-cover border-2 border-gray-200">
                    @else
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold border-2 border-gray-200">
                            {{ strtoupper(substr(auth()->user()->name ?? 'M', 0, 1)) }}
                        </div>
                    @endif
                    
                    {{-- Online Indicator --}}
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                </div>

                {{-- Dropdown Icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" 
                     :class="{ 'rotate-180': openProfileDropdown }"
                     class="w-4 h-4 text-gray-600 transition-transform duration-200 hidden md:block" 
                     fill="none" 
                     viewBox="0 0 24 24" 
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Dropdown Menu --}}
            <div x-show="openProfileDropdown"
                 x-cloak
                 @click.away="openProfileDropdown = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                 class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl ring-1 ring-black/5 overflow-hidden z-50">
                
                {{-- User Info --}}
                <div class="px-4 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white">
                    <p class="text-sm font-semibold">{{ auth()->user()->name ?? 'Nama Mitra' }}</p>
                    <p class="text-xs opacity-90">{{ auth()->user()->email ?? 'email@mitra.com' }}</p>
                </div>

                {{-- Menu Items --}}
                <div class="py-2">
                    <a href="{{ route('mitrapanel.settings') }}" 
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profil Saya
                    </a>

                    <a href="{{ route('mitrapanel.settings') }}" 
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Pengaturan
                    </a>

                    <a href="{{ route('mitrapanel.faktur') }}" 
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Faktur Pajak
                    </a>
                </div>

                {{-- Logout --}}
                <div class="border-t border-gray-100">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>

            </div>

        </div>

    </div>
</div>