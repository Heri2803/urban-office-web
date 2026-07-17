@php
    $popup = isset($banners) && $banners->count() > 0 ? $banners->first() : null;
@endphp

@if($popup)
<!-- Modal Overlay Promo -->
<div id="promoModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] flex items-center justify-center px-4 py-6" style="display: none;">
    <!-- Modal Container -->
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full transform transition-all duration-300 scale-95 opacity-0 overflow-hidden" id="promoContent">
        <!-- Close Button -->
        <button onclick="closePromoModal()" class="absolute top-4 right-4 text-white hover:text-gray-200 transition-colors z-10 bg-black/30 rounded-full p-1">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Header dengan Gambar/Banner Promo -->
        <div class="relative w-full h-48 md:h-64 bg-gray-200">
            @if($popup->image_url)
                <img src="{{ $popup->image_url }}" alt="{{ $popup->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gradient-to-br from-orange-500 via-orange-600 to-red-600 flex items-center justify-center">
                    <h2 class="text-3xl font-bold text-white mb-2">{{ $popup->name }}</h2>
                </div>
            @endif
            
            <!-- Badge "PROMO" -->
            <div class="absolute top-4 left-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-400 text-orange-900 shadow-lg animate-pulse">
                    🔥 PROMO SPESIAL
                </span>
            </div>
        </div>

        <!-- Content -->
        <div class="px-6 py-6">
            <!-- Deskripsi Promo -->
            <div class="text-center mb-5">
                <h3 class="text-xl font-bold text-gray-800 mb-3">{{ $popup->name }}</h3>
                <p class="text-gray-600 leading-relaxed text-sm mb-4">
                    {{ $popup->description ?? 'Dapatkan penawaran menarik khusus untuk Anda. Promo terbatas!' }}
                </p>
            </div>

            <!-- Highlight Features/Benefits -->
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 mb-5">
                <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Keuntungan Promo:
                </h4>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-orange-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Diskon 30% untuk Meeting Room & Event Space</span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-orange-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Gratis konsultasi pemilihan ruangan</span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-orange-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Fleksibilitas pembayaran & reschedule</span>
                    </li>
                </ul>
            </div>

            <!-- Countdown Timer (Optional) -->
            <div class="bg-red-50 border-2 border-red-200 rounded-lg p-3 mb-5 text-center">
                <p class="text-xs text-red-600 font-semibold mb-1">⏰ PROMO BERAKHIR DALAM:</p>
                <div class="flex justify-center gap-2 text-red-700">
                    <div class="bg-white px-3 py-1 rounded shadow">
                        <span class="text-lg font-bold" id="days">00</span>
                        <p class="text-xs">Hari</p>
                    </div>
                    <div class="bg-white px-3 py-1 rounded shadow">
                        <span class="text-lg font-bold" id="hours">00</span>
                        <p class="text-xs">Jam</p>
                    </div>
                    <div class="bg-white px-3 py-1 rounded shadow">
                        <span class="text-lg font-bold" id="minutes">00</span>
                        <p class="text-xs">Menit</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="/myurbanoffice/dashboard/bookingform" class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Sewa Sekarang
                </a>
                <button onclick="closePromoModal()" class="flex-1 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors duration-200">
                    Nanti Saja
                </button>
            </div>

            <!-- Checkbox "Jangan tampilkan lagi hari ini" -->
            <div class="mt-4 text-center">
                <label class="inline-flex items-center cursor-pointer text-sm text-gray-600 hover:text-gray-800">
                    <input type="checkbox" id="dontShowToday" class="rounded border-gray-300 text-orange-500 focus:ring-orange-500 mr-2">
                    <span>Jangan tampilkan lagi hari ini</span>
                </label>
            </div>
        </div>
    </div>
</div>

<script>
// ========== KONFIGURASI ==========
const PROMO_CONFIG = {
    // Interval tampil ulang (dalam menit)
    showInterval: 5, // Tampil lagi setiap 5 menit
    
    // Target halaman (path yang akan menampilkan modal)
    // ✅ Sesuaikan dengan route Laravel Anda
    targetPages: [
        '/dashboard/home',
        '/dashboard',
        // Tambahkan path lain jika perlu
    ],
    
    // Countdown end date (format: YYYY-MM-DD)
    promoEndDate: '2025-12-31',
    
    // Debug mode - set true untuk testing
    debugMode: true
};

// ========== FUNGSI UTAMA ==========
function openPromoModal() {
    const modal = document.getElementById('promoModal');
    const content = document.getElementById('promoContent');
    
    if (!modal || !content) return;
    
    // Show modal
    modal.style.display = 'flex';
    
    // Trigger animation
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
    
    // Update timestamp terakhir ditampilkan
    localStorage.setItem('promoLastShown', Date.now());
}

function closePromoModal() {
    const modal = document.getElementById('promoModal');
    const content = document.getElementById('promoContent');
    const dontShowCheckbox = document.getElementById('dontShowToday');
    
    if (!modal || !content) return;
    
    // Cek checkbox "Jangan tampilkan lagi hari ini"
    if (dontShowCheckbox && dontShowCheckbox.checked) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow.setHours(0, 0, 0, 0);
        localStorage.setItem('promoDontShowUntil', tomorrow.getTime());
    }
    
    // Trigger close animation
    content.classList.add('scale-95', 'opacity-0');
    content.classList.remove('scale-100', 'opacity-100');
    
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }, 300);
}

// Cek apakah modal harus ditampilkan
function shouldShowPromoModal() {
    const currentPath = window.location.pathname;
    
    // Debug log
    if (PROMO_CONFIG.debugMode) {
        console.log('=== PROMO MODAL DEBUG ===');
        console.log('Current Path:', currentPath);
        console.log('Target Pages:', PROMO_CONFIG.targetPages);
    }
    
    // 1. Cek apakah di halaman target
    const isTargetPage = PROMO_CONFIG.targetPages.some(page => currentPath === page || currentPath.includes(page));
    
    if (PROMO_CONFIG.debugMode) {
        console.log('Is Target Page:', isTargetPage);
    }
    
    if (!isTargetPage) {
        if (PROMO_CONFIG.debugMode) console.log('❌ Bukan halaman target');
        return false;
    }
    
    // 2. Cek "jangan tampilkan hari ini"
    const dontShowUntil = localStorage.getItem('promoDontShowUntil');
    if (dontShowUntil && Date.now() < parseInt(dontShowUntil)) {
        if (PROMO_CONFIG.debugMode) console.log('❌ User memilih "jangan tampilkan hari ini"');
        return false;
    }
    
    // 3. Cek interval waktu
    const lastShown = localStorage.getItem('promoLastShown');
    if (lastShown) {
        const minutesSinceLastShown = (Date.now() - parseInt(lastShown)) / (1000 * 60);
        if (PROMO_CONFIG.debugMode) {
            console.log('Minutes since last shown:', minutesSinceLastShown.toFixed(2));
        }
        if (minutesSinceLastShown < PROMO_CONFIG.showInterval) {
            if (PROMO_CONFIG.debugMode) console.log('❌ Belum waktunya tampil lagi');
            return false;
        }
    }
    
    if (PROMO_CONFIG.debugMode) console.log('✅ Modal akan ditampilkan!');
    return true;
}

// Countdown Timer
function updateCountdown() {
    const endDate = new Date(PROMO_CONFIG.promoEndDate).getTime();
    const now = new Date().getTime();
    const distance = endDate - now;
    
    if (distance < 0) {
        document.getElementById('days').textContent = '00';
        document.getElementById('hours').textContent = '00';
        document.getElementById('minutes').textContent = '00';
        return;
    }
    
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    
    document.getElementById('days').textContent = String(days).padStart(2, '0');
    document.getElementById('hours').textContent = String(hours).padStart(2, '0');
    document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
}

// ========== INISIALISASI ==========
document.addEventListener('DOMContentLoaded', function() {
    // Tampilkan modal jika memenuhi kondisi
    if (shouldShowPromoModal()) {
        // Delay 2 detik setelah halaman load
        setTimeout(() => {
            openPromoModal();
        }, 2000);
    }
    
    // Update countdown setiap detik
    updateCountdown();
    setInterval(updateCountdown, 1000);
});

// Close modal ketika klik di luar
document.getElementById('promoModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePromoModal();
    }
});

// Close modal dengan ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('promoModal');
        if (modal && modal.style.display === 'flex') {
            closePromoModal();
        }
    }
});

// ========== FUNGSI TAMBAHAN ==========
// Reset promo (untuk testing - hapus di production)
function resetPromoModal() {
    localStorage.removeItem('promoLastShown');
    localStorage.removeItem('promoDontShowUntil');
    console.log('Promo modal reset! Refresh halaman untuk melihat modal lagi.');
}

// Panggil di console untuk testing: resetPromoModal()
</script>

<style>
/* Animasi pulse untuk badge */
@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
@endif