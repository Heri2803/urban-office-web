<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name', 'Urban Office') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="h-screen overflow-hidden">

<div class="h-screen relative overflow-hidden">

    <!-- Background Image -->
    <div class="absolute inset-0">
        <div class="w-full h-full bg-cover bg-center"
            style="background-image: url('https://images.unsplash.com/photo-1598257006458-087169a1f08d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2126&q=80')">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-400/40 to-orange-600/50"></div>
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

    <!-- Logo & Tagline -->
    <div class="absolute top-6 md:top-8 left-1/2 transform -translate-x-1/2 text-center z-30">
        <div class="mb-2 md:mb-3">
            <div class="w-10 h-10 md:w-14 md:h-14 mx-auto mb-1 md:mb-2 bg-white rounded-lg flex items-center justify-center shadow-lg">
                <img src="/assets/LOGO_URBAN_OFFICE.png"
                    alt="Urban Office Logo"
                    class="w-8 h-8 md:w-10 md:h-10 object-contain"/>
            </div>
        </div>
        <p class="text-white/90 text-xs md:text-sm px-4 leading-tight font-medium">
            VIRTUAL OFFICE + MEETING ROOM = SOLUSI BISNIS<br/>
            LENGKAP
        </p>
    </div>

    <!-- Login Form -->
    <div class="relative z-20 h-screen flex items-center justify-center px-4 pt-20 md:pt-24">
        <div class="bg-white rounded-xl md:rounded-2xl shadow-2xl w-full max-w-sm md:max-w-md p-5 md:p-7 mt-4 md:mt-8">
            <div class="text-center mb-4 md:mb-5">
                <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-1 md:mb-2">Masuk ke Akun</h2>
                <p class="text-xs md:text-sm text-gray-600">Kelola bisnis Anda dengan mudah</p>
            </div>

            {{-- Alert Messages --}}
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p class="text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4 md:space-y-5">
                @csrf
                
                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Email:</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 md:w-5 md:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input type="email" 
                               name="email"
                               value="{{ old('email') }}"
                               class="w-full pl-9 md:pl-10 pr-4 py-2.5 md:py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-colors bg-gray-50 text-gray-900 placeholder-gray-400 @error('email') border-red-500 @enderror"
                               placeholder="nama@email.com" 
                               required 
                               autofocus>
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Sandi:</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 md:w-5 md:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password" 
                               name="password"
                               class="w-full pl-9 md:pl-10 pr-4 py-2.5 md:py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-colors bg-gray-50 text-gray-900 placeholder-gray-400 @error('password') border-red-500 @enderror"
                               placeholder="Masukkan sandi Anda" 
                               required>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="remember" 
                               id="remember"
                               class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-xs md:text-sm text-gray-700">
                            Ingat saya
                        </label>
                    </div>
                    <div class="text-right">
                        <a href="{{ route('password.request') }}" class="text-xs md:text-sm text-orange-500 hover:text-orange-600 font-medium">
                            Lupa Sandi? <span class="underline">Reset here</span>
                        </a>
                    </div>
                </div>

                <!-- Login Button -->
                <button type="submit"
                        class="w-full bg-orange-500 text-white py-2.5 md:py-3 rounded-lg font-semibold text-sm md:text-base hover:bg-orange-600 focus:ring-4 focus:ring-orange-200 transition-all duration-200 shadow-lg">
                    Masuk
                </button>

                <!-- Register link -->
                <div class="text-center pt-2 md:pt-3 text-xs md:text-sm text-gray-600">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-orange-500 hover:text-orange-600 font-semibold">Daftar sekarang</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>