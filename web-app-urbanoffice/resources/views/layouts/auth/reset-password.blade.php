<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - Urban Office</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased min-h-screen">

<div class="relative w-full min-h-screen overflow-auto">
    <div class="absolute inset-0">
        <div class="w-full h-full bg-cover bg-center"
             style="background-image: url('https://images.unsplash.com/photo-1598257006458-087169a1f08d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2126&q=80')">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-400/60 to-orange-600/70"></div>
        </div>
    </div>

    <div class="absolute top-6 left-6 z-10">
        <a href="{{ route('password.request') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition-colors">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
    </div>

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

    <div class="relative z-20 flex items-center justify-center min-h-screen px-4 pt-32 md:pt-40">
        <div class="bg-white/95 backdrop-blur-md shadow-2xl rounded-xl md:rounded-2xl p-6 md:p-8 w-full max-w-sm md:max-w-md mt-12 md:mt-20 mb-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto mb-4 bg-orange-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7h3a5 5 0 015 5v2a5 5 0 01-5 5h-3m-6 0H6a5 5 0 01-5-5v-2a5 5 0 015-5h3m-1 12l2-2m-2 2l2 2m-2-2l-2-2m2 2l-2 2m-2-2h4a2 2 0 002-2V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-2">Atur Ulang Password</h2>
                <p class="text-xs md:text-sm text-gray-600 leading-relaxed">Masukkan password baru Anda di bawah untuk menyelesaikan proses reset.</p>
                <p class="text-xs md:text-sm text-gray-800 font-semibold mt-2">Email: {{ $email ?? 'Email tidak ditemukan' }}</p>
            </div>

            {{-- Pesan Status Global (Success/Error) --}}
            @if (session('status'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Form Tahap 3: Input Password Baru --}}
            {{-- Action mengarah ke route standar Laravel untuk reset password --}}
            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                
                {{-- Token dan Email (Hidden) --}}
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password" placeholder="Masukkan password baru (min. 8 karakter)" 
                               class="w-full pl-4 pr-4 py-3 text-sm border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-colors bg-gray-50 text-gray-900 placeholder-gray-400" required>
                    </div>
                    @error('password')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru" 
                               class="w-full pl-4 pr-4 py-3 text-sm border {{ $errors->has('password_confirmation') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-colors bg-gray-50 text-gray-900 placeholder-gray-400" required>
                    </div>
                    @error('password_confirmation')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" 
                        class="w-full bg-orange-500 text-white py-3 rounded-lg font-semibold text-sm md:text-base hover:bg-orange-600 focus:ring-4 focus:ring-orange-200 transition-all duration-200 shadow-lg">
                    Reset Password
                </button>

                <div class="text-center pt-3">
                    <p class="text-xs md:text-sm text-gray-600">
                        Password berhasil diubah? 
                        <a href="{{ route('login') }}" class="text-orange-500 hover:text-orange-600 font-semibold">Masuk sekarang</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>