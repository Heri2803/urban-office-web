<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Password - Urban Office</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased min-h-screen" x-data="{ otpModal: false, email: '', countdown: 0 }">

<div class="relative w-full min-h-screen overflow-auto">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <div class="w-full h-full bg-cover bg-center"
             style="background-image: url('https://images.unsplash.com/photo-1598257006458-087169a1f08d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2126&q=80')">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-400/60 to-orange-600/70"></div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="absolute top-6 left-6 z-10">
        <a href="{{ route('beforelogin') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition-colors">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
    </div>

    <!-- Header Logo & Text -->
    <div class="absolute top-6 md:top-8 left-1/2 transform -translate-x-1/2 text-center z-30">
        <div class="mb-2 md:mb-3">
            <div class="w-10 h-10 md:w-14 md:h-14 mx-auto mb-1 md:mb-2 bg-white rounded-lg flex items-center justify-center shadow-lg">
                <img src="/assets/LOGO_URBAN_OFFICE.png" alt="Urban Office Logo" class="w-8 h-8 md:w-10 md:h-10 object-contain" />
            </div>
        </div>
        <h1 class="text-lg md:text-xl font-bold text-white mb-1">URBANOFFICE</h1>
        <p class="text-white/90 text-xs md:text-sm px-4 leading-tight font-medium">
            VIRTUAL OFFICE + MEETING ROOM = SOLUSI BISNIS<br />LENGKAP
        </p>
    </div>

    <!-- Forgot Password Form -->
    <div class="relative z-20 flex items-center justify-center min-h-screen px-4 pt-32 md:pt-40">
        <div class="bg-white/95 backdrop-blur-md shadow-2xl rounded-xl md:rounded-2xl p-6 md:p-8 w-full max-w-sm md:max-w-md mt-12 md:mt-20 mb-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto mb-4 bg-orange-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-2">Lupa Password?</h2>
                <p class="text-xs md:text-sm text-gray-600 leading-relaxed">Masukkan email Anda dan kami akan mengirimkan kode OTP untuk reset password</p>
            </div>

            <form @submit.prevent="email = $refs.emailInput.value; otpModal = true; countdown = 60; 
                  const timer = setInterval(() => { 
                      countdown--; 
                      if(countdown <= 0) clearInterval(timer); 
                  }, 1000);" class="space-y-5">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input x-ref="emailInput" type="email" name="email" placeholder="georgius.miracel@gmail.com" 
                               class="w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-colors bg-gray-50 text-gray-900 placeholder-gray-400" required>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-orange-500 text-white py-3 rounded-lg font-semibold text-sm md:text-base hover:bg-orange-600 focus:ring-4 focus:ring-orange-200 transition-all duration-200 shadow-lg">
                    Kirim Kode OTP
                </button>

                <div class="text-center pt-3">
                    <p class="text-xs md:text-sm text-gray-600">
                        Ingat password Anda? 
                        <a href="{{ route('login') }}"class="text-orange-500 hover:text-orange-600 font-semibold">Masuk di sini</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <!-- OTP Modal -->
    <div x-show="otpModal" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
         @click="otpModal = false">
        
        <div x-show="otpModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 relative"
             @click.stop>
            
            <!-- Header -->
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Kode OTP Terkirim!</h3>
                    </div>
                    <button @click="otpModal = false" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 mx-auto mb-4 bg-blue-50 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-gray-700 text-sm leading-relaxed mb-2">
                        Kami telah mengirimkan kode OTP 6 digit ke:
                    </p>
                    <p class="font-semibold text-gray-900 text-sm bg-gray-50 px-3 py-2 rounded-lg inline-block" x-text="email"></p>
                </div>

                <!-- OTP Input -->
                <form class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 text-center">Masukkan Kode OTP</label>
                        <div class="flex justify-center gap-2 mb-4">
                            <input type="text" maxlength="1" class="w-12 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none">
                            <input type="text" maxlength="1" class="w-12 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none">
                            <input type="text" maxlength="1" class="w-12 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none">
                            <input type="text" maxlength="1" class="w-12 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none">
                            <input type="text" maxlength="1" class="w-12 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none">
                            <input type="text" maxlength="1" class="w-12 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none">
                        </div>
                    </div>

                    <!-- Countdown Timer -->
                    <div class="text-center text-sm text-gray-600 mb-4">
                        <span x-show="countdown > 0">Kirim ulang kode dalam <span x-text="countdown" class="font-semibold text-orange-600"></span> detik</span>
                        <button x-show="countdown <= 0" type="button" @click="countdown = 60; const timer = setInterval(() => { countdown--; if(countdown <= 0) clearInterval(timer); }, 1000);" class="text-orange-500 hover:text-orange-600 font-semibold">
                            Kirim Ulang Kode
                        </button>
                    </div>

                    <button type="submit" class="w-full bg-orange-500 text-white py-3 rounded-lg font-semibold hover:bg-orange-600 focus:ring-4 focus:ring-orange-200 transition-all duration-200">
                        Verifikasi OTP
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl">
                <p class="text-xs text-gray-500 text-center">
                    Tidak menerima kode? Periksa folder spam atau 
                    <button @click="countdown = 60; const timer = setInterval(() => { countdown--; if(countdown <= 0) clearInterval(timer); }, 1000);" class="text-orange-500 hover:text-orange-600 font-medium" x-show="countdown <= 0">kirim ulang</button>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
// Auto-focus next input for OTP
document.addEventListener('alpine:init', () => {
    // OTP input auto-focus functionality
    document.addEventListener('input', function(e) {
        if (e.target.matches('input[maxlength="1"]')) {
            if (e.target.value.length === 1) {
                const nextInput = e.target.nextElementSibling;
                if (nextInput && nextInput.matches('input[maxlength="1"]')) {
                    nextInput.focus();
                }
            }
        }
    });

    // OTP input backspace functionality
    document.addEventListener('keydown', function(e) {
        if (e.target.matches('input[maxlength="1"]') && e.key === 'Backspace') {
            if (e.target.value.length === 0) {
                const prevInput = e.target.previousElementSibling;
                if (prevInput && prevInput.matches('input[maxlength="1"]')) {
                    prevInput.focus();
                }
            }
        }
    });
});
</script>

</body>
</html>