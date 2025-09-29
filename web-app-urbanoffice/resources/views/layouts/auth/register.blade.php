<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - {{ config('app.name', 'Urban Office') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('assets/LOGO_URBAN_OFFICE.png?v=1') }}">
</head>
<body class="font-sans antialiased min-h-screen">

<div class="relative w-full min-h-screen overflow-auto">
    {{-- Background Image --}}
    <div class="absolute inset-0">
        <div class="w-full h-full bg-cover bg-center"
             style="background-image: url('https://images.unsplash.com/photo-1598257006458-087169a1f08d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2126&q=80')">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-400/60 to-orange-600/70"></div>
        </div>
    </div>

    <!-- Back Arrow -->
    <div class="absolute top-6 left-6 z-50">
        <a href="{{ route('beforelogin') }}"
        class="w-10 h-10 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition-colors">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
    </div>

    {{-- Header Logo & Text --}}
    <div class="absolute top-6 md:top-8 left-1/2 transform -translate-x-1/2 text-center z-30">
        <div class="mb-2 md:mb-3">
            <div class="w-10 h-10 md:w-14 md:h-14 mx-auto mb-1 md:mb-2 bg-white rounded-lg flex items-center justify-center shadow-lg">
                <img src="{{ asset('assets/LOGO_URBAN_OFFICE.png') }}" alt="Urban Office Logo" class="w-8 h-8 md:w-10 md:h-10 object-contain" />
            </div>
        </div>
        <h1 class="text-lg md:text-xl font-bold text-white mb-1">URBANOFFICE</h1>
        <p class="text-white/90 text-xs md:text-sm px-4 leading-tight font-medium">
            VIRTUAL OFFICE + MEETING ROOM = SOLUSI BISNIS<br />LENGKAP
        </p>
    </div>

    {{-- Register Form --}}
    <div class="relative z-20 flex items-center justify-center min-h-screen px-4 pt-32 md:pt-40">
        <div class="bg-white/95 backdrop-blur-md shadow-2xl rounded-xl md:rounded-2xl p-6 md:p-8 w-full max-w-sm md:max-w-md mt-12 md:mt-20 mb-8">
            <h2 class="text-lg md:text-xl font-bold text-center text-gray-800 mb-4 md:mb-6">Daftar Akun Baru</h2>

            <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
                    <input type="file" name="profile_photo"
                        class="w-full text-sm border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 
                                file:rounded-lg file:border-0 file:text-sm file:font-semibold 
                                file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100">
                    @error('profile_photo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="name" value="{{ old('username') }}" placeholder="Georgius Maria" 
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-colors bg-gray-50 text-gray-900 placeholder-gray-400" required>
                    @error('username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="georgius.miracel@gmail.com" 
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-colors bg-gray-50 text-gray-900 placeholder-gray-400" required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ showPassword: false }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" placeholder="••••••••" 
                               class="w-full px-4 py-2.5 pr-10 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-colors bg-gray-50 text-gray-900 placeholder-gray-400" required>
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg x-show="!showPassword" class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ showPasswordConfirmation: false }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ulangi Password</label>
                    <div class="relative">
                        <input :type="showPasswordConfirmation ? 'text' : 'password'" name="password_confirmation" placeholder="••••••••" 
                               class="w-full px-4 py-2.5 pr-10 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-colors bg-gray-50 text-gray-900 placeholder-gray-400" required>
                        <button type="button" @click="showPasswordConfirmation = !showPasswordConfirmation" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg x-show="!showPasswordConfirmation" class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPasswordConfirmation" class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-orange-500 text-white py-2.5 md:py-3 rounded-lg font-semibold text-sm md:text-base hover:bg-orange-600 focus:ring-4 focus:ring-orange-200 transition-all duration-200 shadow-lg">
                    Daftar
                </button>

                <p class="text-center text-xs md:text-sm text-gray-600 pt-2 md:pt-3">
                    Sudah punya akun? <a href="{{ route('login') }}" class="text-orange-500 hover:text-orange-600 font-semibold">Masuk di sini</a>
                </p>
            </form>
        </div>
    </div>
</div>

</body>
</html>