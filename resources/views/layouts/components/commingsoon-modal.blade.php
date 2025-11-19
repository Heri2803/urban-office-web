<!-- Modal Overlay -->
<div id="comingSoonModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] flex items-center justify-center px-4 py-6" style="display: none;">
    <!-- Modal Container - Lebih Kecil -->
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0 overflow-hidden" id="comingSoonContent">
        <!-- Close Button -->
        <button onclick="closeComingSoonModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition-colors z-10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Header dengan Icon - Kompak -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-6 text-center rounded-t-xl relative">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-white rounded-full mb-2 shadow-lg">
                <svg class="w-7 h-7 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-white mb-1">Coming Soon</h2>
            <p class="text-orange-100 text-xs">Fitur ini segera hadir</p>
        </div>

        <!-- Content - Singkat -->
        <div class="px-6 py-5">
            <div class="text-center mb-4">
                <p class="text-gray-600 leading-relaxed text-sm">
                    Kami sedang mengembangkan fitur ini untuk memberikan pengalaman yang lebih baik. Nantikan update selanjutnya!
                </p>
            </div>

            <!-- Features Preview - Ringkas -->
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-3 mb-4">
                <ul class="space-y-1.5 text-xs text-gray-700">
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-orange-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Fitur yang lebih canggih</span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-orange-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Pengalaman pengguna lebih baik</span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-orange-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Performa yang lebih cepat</span>
                    </li>
                </ul>
            </div>

            <!-- Close Button -->
            <div class="text-center">
                <button onclick="closeComingSoonModal()" class="w-full px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
                    Mengerti
                </button>
            </div>
        </div>

        <!-- Footer - Minimal -->
        <div class="bg-orange-50 px-4 py-2.5 text-center border-t border-orange-100 rounded-b-xl">
            <p class="text-xs text-gray-600">
                Terima kasih atas kesabaran Anda 🙏
            </p>
        </div>
    </div>
</div>

<script>
function openComingSoonModal() {
    const modal = document.getElementById('comingSoonModal');
    const content = document.getElementById('comingSoonContent');
    
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
}

function closeComingSoonModal() {
    const modal = document.getElementById('comingSoonModal');
    const content = document.getElementById('comingSoonContent');
    
    if (!modal || !content) return;
    
    // Trigger close animation
    content.classList.add('scale-95', 'opacity-0');
    content.classList.remove('scale-100', 'opacity-100');
    
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }, 300);
}

// Close modal when clicking outside
document.getElementById('comingSoonModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeComingSoonModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('comingSoonModal');
        if (modal && modal.style.display === 'flex') {
            closeComingSoonModal();
        }
    }
});
</script>

