<!-- Modal Overlay -->
<div id="maintenanceModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] hidden flex items-center justify-center px-4 py-6">
    <!-- Modal Container -->
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full max-h-[85vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        <!-- Close Button -->
        <button onclick="closeMaintenanceModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Header dengan Icon -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-8 text-center rounded-t-xl relative">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full mb-3 shadow-lg">
                <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">Sedang Maintenance</h2>
            <p class="text-orange-100 text-sm">Kami sedang melakukan pemeliharaan sistem</p>
        </div>

        <!-- Content -->
        <div class="px-6 py-6">
            <div class="text-center mb-5">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Mohon Maaf atas Ketidaknyamanannya</h3>
                <p class="text-gray-600 leading-relaxed text-sm">
                    Website kami sedang dalam proses pemeliharaan untuk meningkatkan performa dan pengalaman pengguna yang lebih baik. Kami akan segera kembali online.
                </p>
            </div>


            <!-- Contact Info -->
            <div class="text-center">
                <p class="text-gray-600 mb-4 text-sm">Butuh bantuan? Hubungi kami:</p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="tel:+62123456789" class="inline-flex items-center px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        Telepon
                    </a>
                </div>
            </div>

            <!-- Close Button -->
            <div class="mt-6 text-center">
                <button onclick="closeMaintenanceModal()" class="px-8 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors duration-200">
                    Tutup
                </button>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-3 text-center border-t border-gray-200 rounded-b-xl">
            <p class="text-xs text-gray-500">
                © {{ date('Y') }} Your Company. Terima kasih atas kesabaran Anda.
            </p>
        </div>
    </div>
</div>

<script>
function openMaintenanceModal() {
    const modal = document.getElementById('maintenanceModal');
    const content = document.getElementById('modalContent');
    
    modal.classList.remove('hidden');
    
    // Trigger animation
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function closeMaintenanceModal() {
    const modal = document.getElementById('maintenanceModal');
    const content = document.getElementById('modalContent');
    
    // Trigger close animation
    content.classList.add('scale-95', 'opacity-0');
    content.classList.remove('scale-100', 'opacity-100');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }, 300);
}

// Close modal when clicking outside
document.getElementById('maintenanceModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeMaintenanceModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('maintenanceModal');
        if (!modal.classList.contains('hidden')) {
            closeMaintenanceModal();
        }
    }
});
</script>