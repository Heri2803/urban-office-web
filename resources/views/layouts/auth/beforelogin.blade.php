<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Before Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('assets/LOGO_URBAN_OFFICE.png?v=1') }}">
</head>
<body class="min-h-screen bg-gradient-to-br from-orange-400 via-orange-500 to-orange-600 relative">

<div x-data="{ isOpen: false }" :class="{ 'overflow-hidden': isOpen }" class="min-h-screen relative">

    <!-- Background -->
    <div class="absolute inset-0">
        <div class="absolute right-0 top-0 w-full h-full md:w-1/2 bg-gradient-to-l from-black/20 to-transparent">
            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2"
                 alt="Professional Woman"
                 class="w-full h-full object-cover opacity-30 md:opacity-60">
        </div>
        <div class="absolute inset-0 bg-orange-500/40"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 flex min-h-screen flex-col md:flex-row overflow-y-auto">

        <!-- Left Section -->
        <div class="flex flex-1 flex-col justify-center items-start p-6 md:p-16 max-w-2xl">
            
            <!-- Logo -->
            <div class="flex items-center gap-3 mb-8">
                <div class="w-14 h-14 bg-white rounded-lg flex items-center justify-center shadow">
                    <img src="/assets/LOGO_URBAN_OFFICE.png" alt="Urban Office Logo" class="w-14 h-14 object-contain">
                </div>
            </div>

            <!-- Headline -->
            <h1 class="text-4xl md:text-6xl font-bold leading-tight mb-8 text-white drop-shadow-lg">
                Bekerja dari mana saja, seperti anda berada di kantor
            </h1>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 mb-8 w-full max-w-lg">
                <a href="{{ route('login') }}"
                   class="flex-1 bg-white text-orange-600 font-semibold px-8 py-4 rounded-xl shadow-xl text-center hover:bg-gray-50 hover:shadow-2xl transition">
                    Masuk untuk memuat halaman!
                </a>

                <a href="{{ route('register') }}"
                   class="flex-1 bg-gray-900 text-white font-semibold px-8 py-4 rounded-xl shadow-xl text-center hover:bg-black hover:shadow-2xl transition">
                    Masuk untuk daftar buat akun baru!
                </a>
            </div>

            <!-- Quick Links -->
            <div class="flex gap-6 text-sm mb-8">
                <a href="{{ route('password.request') }}" class="text-white/90 hover:text-white underline">Lupa Password?</a>
                <a href="#" class="text-white/90 hover:text-white underline">Mode tamu!</a>
            </div>

            <!-- Divider -->
            <div class="flex items-center gap-4 w-full max-w-lg mb-8">
                <div class="flex-grow h-px bg-white/30"></div>
                <span class="text-white/90 font-medium">atau</span>
                <div class="flex-grow h-px bg-white/30"></div>
            </div>

            <!-- Google Button -->
            <a href="http://127.0.0.1:8000/auth/google"
                class="group flex items-center justify-center gap-3 bg-white/95 text-gray-800 font-semibold px-8 py-4 rounded-xl shadow-xl hover:shadow-2xl w-full max-w-lg border border-white/50 transition-all duration-200">
                    <div class="w-6 h-6 bg-gradient-to-br from-blue-500 via-red-500 to-yellow-500 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                    </div>
                    <span>Continue with Google</span>
            </a>

            <!-- Footer -->
            <div class="mt-12 text-sm text-white/80 max-w-lg">
                <p>© 2024 WorkSpace. All rights reserved.</p>
                <div class="flex gap-4 mt-2">
                    <a href="#" class="hover:text-white underline">Privacy Policy</a>
                    <a href="#" class="hover:text-white underline">Terms of Service</a>
                </div>
            </div>
        </div>

        <!-- Right Section -->
        <div class="hidden md:flex flex-1 justify-center items-center p-8 relative">
            <div class="absolute top-20 right-20 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
            <div class="absolute bottom-20 right-32 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
        </div>
    </div>

    <!-- Modal Google -->
    <div x-show="isOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
         @click="isOpen = false">
        <div x-show="isOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="bg-white rounded-xl shadow-2xl max-w-sm w-full relative"
             @click.stop>
            
            <!-- Header -->
            <div class="p-6 pb-4 border-b">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" viewBox="0 0 24 24">
                            <path fill="#4285f4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34a853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#fbbc04" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#ea4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <h2 class="text-lg font-medium text-gray-900">Pilih akun</h2>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600 p-1" @click="isOpen = false">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-600 mt-2">untuk melanjutkan ke WorkSpace</p>
            </div>

            <!-- Account List -->
            <div class="p-4">
                <!-- Account 1 -->
                <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors duration-150 border border-transparent hover:border-gray-200">
                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium">
                        JD
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">John Doe</p>
                        <p class="text-xs text-gray-500 truncate">john.doe@gmail.com</p>
                    </div>
                </a>

                <!-- Account 2 -->
                <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors duration-150 border border-transparent hover:border-gray-200 mt-2">
                    <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white font-medium">
                        AS
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">Ahmad Setiawan</p>
                        <p class="text-xs text-gray-500 truncate">ahmad.setiawan@gmail.com</p>
                    </div>
                </a>

                <!-- Add Another Account -->
                <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors duration-150 border border-transparent hover:border-gray-200 mt-4">
                    <div class="w-10 h-10 rounded-full border-2 border-gray-300 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-700">Gunakan akun lain</p>
                    </div>
                </a>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t bg-gray-50 rounded-b-xl">
                <div class="flex justify-between text-xs text-gray-500">
                    <a href="#" class="hover:text-gray-700">Kebijakan Privasi</a>
                    <a href="#" class="hover:text-gray-700">Persyaratan Layanan</a>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    [x-cloak] { display: none !important; }
</style>

</body>
</html>