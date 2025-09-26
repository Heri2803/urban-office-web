@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-50 mb-12">
    
    {{-- Main Content --}}
    <div class="flex-1 ml-0 md:ml-52 lg:ml-64 xl:ml-64 transition-all duration-500 ease-in-out">
        {{-- Header Section --}}
        <div 
            class="relative h-48 sm:h-42 lg:h-64 bg-cover bg-center animate-fade-in"
            style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?w=1200&h=400&fit=crop')"
        >
            {{-- Orange Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-orange-500/90 via-orange-400/70 to-transparent"></div>
            
            {{-- Header Content --}}
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between h-full p-4 sm:p-6 lg:p-8">
                <div class="flex flex-col md:flex-row items-start md:items-center space-y-3 md:space-y-0 md:space-x-4 w-full">
                    {{-- Profile Image --}}
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white rounded-full overflow-hidden border-4 border-white shadow-lg animate-slide-in-left cursor-pointer"
                         onclick="toggleUserPopup()">
                        <img 
                            src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face"
                            alt="Profile"
                            class="w-full h-full object-cover hover:scale-110 transition-transform duration-300"
                        />
                    </div>
                    
                    {{-- Profile Info --}}
                    <div class="text-white flex-1 animate-slide-in-up">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold">
                            Selamat Datang {{ $user->name ?? 'Georgius Mario' }}
                        </h1>
                        <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 mt-2">
                            <span class="inline-flex items-center bg-green-500 text-white text-xs sm:text-sm px-3 py-1 rounded-full font-medium w-fit">
                                {{ $user->package ?? 'Virtual Office' }}
                            </span>
                            <span class="text-sm sm:text-base opacity-90 break-all sm:break-normal">
                                {{ $user->phone ?? '08212345678' }} / {{ Str::limit($user->email ?? 'Georgius.mirac...', 15) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                {{-- Detail Button --}}
                <button 
                    onclick="toggleUserPopup()"
                    class="bg-white text-orange-500 px-4 py-2 sm:px-6 sm:py-3 rounded-lg font-semibold hover:bg-gray-50 transition-all duration-300 shadow-lg animate-slide-in-right hover:scale-105 absolute top-4 right-4 sm:relative sm:top-auto sm:right-auto">
                    Detail
                </button>
            </div>
        </div>

        {{-- User Info Popup --}}
        {{-- Popup User --}}
        <div id="userPopup" 
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-fade-in hidden" 
            onclick="toggleUserPopup()">
            <div 
                class="bg-white rounded-xl shadow-2xl p-4 sm:p-6 lg:p-8 m-4 w-full max-w-sm sm:max-w-md lg:max-w-lg animate-slide-in-up"
                onclick="event.stopPropagation()"
            >
                {{-- Popup Header --}}
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Detail Pengguna</h3>
                    <button 
                        onclick="toggleUserPopup()"
                        class="text-gray-400 hover:text-gray-600 transition-colors p-1"
                    >
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                {{-- Profile Image and Name --}}
                <div class="flex flex-col items-center mb-4 sm:mb-6">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 bg-gray-100 rounded-full overflow-hidden border-4 border-orange-200 mb-3">
                        @auth
                        <img
                            src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : 'https://via.placeholder.com/150' }}"
                            alt="Profile"
                            class="w-full h-full object-cover"
                        />
                    @else
                        <img
                            src="https://via.placeholder.com/150"
                            alt="Guest"
                            class="w-full h-full object-cover"
                        />
                    @endauth

                    </div>
                    @auth
                        <h4 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-800">
                            {{ Auth::user()->name }}
                        </h4>
                    @else
                        <h4 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-800">
                            Guest
                        </h4>
                    @endauth
                    <span class="inline-flex items-center bg-green-100 text-green-800 text-xs sm:text-sm px-2 sm:px-3 py-1 rounded-full font-medium mt-2">
                        Active
                    </span>
                </div>
                
                {{-- User Details --}}
                <div class="space-y-3 sm:space-y-4">
                {{-- Email --}}
                <div class="flex items-center space-x-3">
                    <span class="text-gray-400 text-sm sm:text-base">📧</span>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500">Email</p>
                        <p class="text-sm sm:text-base text-gray-800 break-all">
                            {{ optional(Auth::user())->email ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Telepon --}}
                <div class="flex items-center space-x-3">
                    <span class="text-gray-400 text-sm sm:text-base">📱</span>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500">Telepon</p>
                        <p class="text-sm sm:text-base text-gray-800">
                            {{ optional(Auth::user())->phone ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Alamat --}}
                <div class="flex items-center space-x-3">
                    <span class="text-gray-400 text-sm sm:text-base">📍</span>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500">Alamat</p>
                        <p class="text-sm sm:text-base text-gray-800">
                            {{ optional(Auth::user())->address ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Paket --}}
                <div class="flex items-center space-x-3">
                    <span class="text-gray-400 text-sm sm:text-base">📦</span>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500">Paket</p>
                        <p class="text-sm sm:text-base text-gray-800">
                            {{ optional(Auth::user())->package ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Bergabung Sejak --}}
                <div class="flex items-center space-x-3">
                    <span class="text-gray-400 text-sm sm:text-base">📅</span>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500">Bergabung Sejak</p>
                        <p class="text-sm sm:text-base text-gray-800">
                            {{ optional(optional(Auth::user())->created_at)->format('d F Y') ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

                
                {{-- Action Buttons --}}
                
            </div>
        </div>


        {{-- FAQ Section --}}
        <div class="p-4 sm:p-6 lg:p-8 animate-fade-in-up">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6 sm:mb-8 animate-slide-in-left">
                    Frequently Asked Questions
                </h2>
                
                <div class="space-y-3">
                    @php
                        $faqItems = [
                            [
                                'id' => 1,
                                'icon' => '🔒',
                                'title' => 'Keamanan Akun',
                                'content' => 'Informasi mengenai keamanan akun Anda termasuk pengaturan password, two-factor authentication, dan riwayat login terbaru.'
                            ],
                            [
                                'id' => 2,
                                'icon' => '❓',
                                'title' => 'Hubungi Bantuan',
                                'content' => 'Tim support kami siap membantu Anda 24/7. Hubungi kami melalui email support@urbanoffice.com atau telepon 021-1234-5678.'
                            ],
                            [
                                'id' => 3,
                                'icon' => '❓',
                                'title' => 'FAQ',
                                'content' => 'Pertanyaan yang sering diajukan mengenai layanan Urban Office, pembayaran, booking ruangan, dan fitur-fitur lainnya.'
                            ],
                            [
                                'id' => 4,
                                'icon' => '📋',
                                'title' => 'Syarat dan Ketentuan',
                                'content' => 'Syarat dan ketentuan penggunaan layanan Urban Office yang berlaku untuk semua pengguna platform kami.'
                            ],
                            [
                                'id' => 5,
                                'icon' => '🔐',
                                'title' => 'Kebijakan Privasi',
                                'content' => 'Kebijakan privasi mengenai bagaimana kami mengumpulkan, menggunakan, dan melindungi data pribadi Anda.'
                            ]
                        ];
                    @endphp

                    @foreach($faqItems as $index => $item)
                        {{-- FAQ Title Card --}}
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all duration-300 animate-fade-in-card" style="animation-delay: {{ $index * 100 }}ms">
                            <button
                                onclick="toggleDropdown({{ $item['id'] }})"
                                class="w-full flex items-center justify-between p-4 sm:p-5 hover:bg-gray-50 transition-colors text-left"
                            >
                                <div class="flex items-center space-x-3 sm:space-x-4">
                                    <span class="text-lg sm:text-xl animate-bounce-light">{{ $item['icon'] }}</span>
                                    <span class="text-base sm:text-lg font-medium text-gray-800">
                                        {{ $item['title'] }}
                                    </span>
                                </div>
                                <svg
                                    id="arrow-{{ $item['id'] }}"
                                    class="w-5 h-5 text-gray-400 transition-transform duration-300"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                        
                        {{-- FAQ Content Card (Initially Hidden) --}}
                        <div id="dropdown-{{ $item['id'] }}" class="hidden">
                            <div class="bg-orange-50 rounded-lg shadow-sm border border-orange-200 ml-4 sm:ml-6 animate-slide-in-down">
                                <div class="p-4 sm:p-5">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-1 h-6 bg-orange-400 rounded-full flex-shrink-0 mt-1"></div>
                                        <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
                                            {{ $item['content'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center py-6 sm:py-8 text-gray-500 text-sm animate-fade-in">
            My Office by Urban Office
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slide-in-left {
    from { transform: translateX(-20px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slide-in-right {
    from { transform: translateX(20px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slide-in-up {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes fade-in-up {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes fade-in-card {
    from { transform: translateY(10px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes bounce-light {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-2px); }
}

@keyframes slide-in-down {
    from { 
        transform: translateY(-10px); 
        opacity: 0; 
    }
    to { 
        transform: translateY(0); 
        opacity: 1; 
    }
}

.animate-fade-in {
    animation: fade-in 0.8s ease-out;
}

.animate-slide-in-left {
    animation: slide-in-left 0.6s ease-out;
}

.animate-slide-in-right {
    animation: slide-in-right 0.6s ease-out;
}

.animate-slide-in-up {
    animation: slide-in-up 0.7s ease-out;
}

.animate-fade-in-up {
    animation: fade-in-up 0.9s ease-out;
}

.animate-fade-in-card {
    animation: fade-in-card 0.5s ease-out forwards;
    opacity: 0;
}

.animate-bounce-light {
    animation: bounce-light 2s infinite;
}

.animate-slide-in-down {
    animation: slide-in-down 0.3s ease-out;
}

svg {
    transition: transform 0.3s ease-in-out;
}

.rotate-180 {
    transform: rotate(180deg);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let openDropdown = null;
    let showUserPopup = false;

    // Toggle dropdown function - MODIFIED
    window.toggleDropdown = function(id) {
        const dropdown = document.getElementById(`dropdown-${id}`);
        const arrow = document.getElementById(`arrow-${id}`);
         
        
        if (openDropdown === id) {
            // Close current dropdown with slide up animation
            dropdown.style.transform = 'translateY(-10px)';
            dropdown.style.opacity = '0';
            
            setTimeout(() => {
                dropdown.classList.add('hidden');
                dropdown.style.transform = 'translateY(0)';
                dropdown.style.opacity = '1';
            }, 200);
            
            arrow.classList.remove('rotate-180');
            openDropdown = null;
        } else {
            // Close previously opened dropdown
            if (openDropdown !== null) {
                const prevDropdown = document.getElementById(`dropdown-${openDropdown}`);
                const prevArrow = document.getElementById(`arrow-${openDropdown}`);
                
                prevDropdown.style.transform = 'translateY(-10px)';
                prevDropdown.style.opacity = '0';
                
                setTimeout(() => {
                    prevDropdown.classList.add('hidden');
                    prevDropdown.style.transform = 'translateY(0)';
                    prevDropdown.style.opacity = '1';
                }, 200);
                
                prevArrow.classList.remove('rotate-180');
            }
            
            // Open new dropdown with slide down animation
            dropdown.classList.remove('hidden');
            dropdown.style.transform = 'translateY(-10px)';
            dropdown.style.opacity = '0';
            
            setTimeout(() => {
                dropdown.style.transform = 'translateY(0)';
                dropdown.style.opacity = '1';
            }, 10);
            
            arrow.classList.add('rotate-180');
            openDropdown = id;
        }
    };

    // ADD THIS FUNCTION - Toggle user popup function
    window.toggleUserPopup = function() {
        const popup = document.getElementById('userPopup');
        showUserPopup = !showUserPopup;
        
        if (showUserPopup) {
            popup.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        } else {
            popup.classList.add('hidden');
            document.body.style.overflow = 'auto'; // Re-enable scrolling
        }
    };

    // Close popup when pressing Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && showUserPopup) {
            toggleUserPopup();
        }
    });
});
</script>
@endsection