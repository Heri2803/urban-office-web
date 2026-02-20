{{-- resources/views/layouts/adminpanel/components/header-profile.blade.php --}}

@php
    $user = auth()->user();
    $initial = strtoupper(substr($user->name ?? 'A', 0, 1));
@endphp

<div x-data="{ openProfileDropdown: false }" class="px-3 md:px-4 py-3 bg-white rounded-xl shadow-sm">
    <div class="flex items-center justify-between">
        
        {{-- Left: Page Title --}}
        <div class="flex items-center space-x-2">
            <div>
                <h1 class="text-base md:text-lg font-semibold text-gray-800">
                    @yield('page-title', 'Dashboard')
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">
                    @yield('page-subtitle', 'Selamat datang di Admin Panel')
                </p>
            </div>
        </div>

        {{-- Right: Profile Info & Dropdown --}}
        <div class="relative">
            <button 
                @click="openProfileDropdown = !openProfileDropdown"
                type="button"
                aria-label="Menu profil admin"
                aria-expanded="false"
                :aria-expanded="openProfileDropdown"
                class="flex items-center space-x-2 p-1.5 rounded-lg hover:bg-gray-100 transition duration-200">
                
                {{-- Profile Info (Hidden di mobile kecil) --}}
                <div class="hidden sm:block text-right">
                    <p class="text-sm font-medium text-gray-800 leading-tight">
                        {{ $user->name ?? 'Administrator' }}
                    </p>
                    <p class="text-xs text-gray-500 leading-tight">
                        Admin
                    </p>
                </div>

                {{-- Avatar --}}
                <div class="relative">
                    @if($user->profile_photo ?? false)
                        <img src="{{ asset('storage/' . $user->profile_photo) }}" 
                             alt="Profile" 
                             class="w-8 h-8 rounded-full object-cover border border-gray-200"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    @endif
                    
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-white font-medium border border-gray-200 text-sm {{ ($user->profile_photo ?? false) ? 'hidden' : '' }}">
                        {{ $initial }}
                    </div>
                    
                    {{-- Online Indicator --}}
                    <span class="absolute bottom-0 right-0 w-2 h-2 bg-green-500 border border-white rounded-full"></span>
                </div>

                {{-- Dropdown Icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" 
                     :class="{ 'rotate-180': openProfileDropdown }"
                     class="w-4 h-4 text-gray-500 transition-transform duration-200 hidden md:block" 
                     fill="none" 
                     viewBox="0 0 24 24" 
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Dropdown Menu - Semua sudut melengkung --}}
            <div x-show="openProfileDropdown"
                 x-cloak
                 @click.away="openProfileDropdown = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                 class="absolute right-0 mt-1 w-56 bg-white rounded-xl shadow-xl ring-1 ring-black/5 overflow-hidden z-50">
                
                {{-- User Info --}}
                <div class="px-3 py-2 bg-gradient-to-r from-orange-500 to-indigo-600 text-white">
                    <p class="text-sm font-semibold leading-tight">{{ $user->name ?? 'Administrator' }}</p>
                    <p class="text-xs opacity-90 leading-tight mt-0.5">{{ $user->email ?? 'admin@email.com' }}</p>
                </div>

                {{-- Menu Items --}}
                <div class="py-1">
                    <a href="{{ route('admin.settings') }}" 
                       class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition duration-150">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profil Saya
                    </a>

                    <a href="{{ route('admin.settings') }}" 
                       class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition duration-150">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </a>
                </div>

                {{-- Logout --}}
                <div class="border-t border-gray-100">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="flex items-center w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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