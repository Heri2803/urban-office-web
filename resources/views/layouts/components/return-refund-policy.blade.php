<!-- File: resources/views/components/return-refund-policy.blade.php -->
<div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] max-h-[90dvh] overflow-hidden">
    <!-- Header -->
    <div class="sticky top-0 bg-white border-b px-8 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Kebijakan Pengembalian & Refund</h2>
                <p class="text-gray-600 mt-1">Terakhir diperbarui: <?php echo date('d F Y'); ?></p>
            </div>
            <button onclick="parent.closeModal && parent.closeModal()" 
                    class="text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Content -->
    <div class="p-8 overflow-y-auto max-h-[calc(90vh-120px)] max-h-[calc(90dvh-120px)]">
        <div class="prose prose-lg max-w-none">
            
            <!-- Introduction -->
            <div class="mb-6">
                <p class="text-gray-700 leading-relaxed">
                    Kebijakan ini mengatur pengembalian dana untuk layanan WorkSpace. Dengan menggunakan layanan kami, Anda setuju dengan kebijakan ini.
                </p>
            </div>
            
            <!-- Key Points -->
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Poin Penting</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- Non-Refundable -->
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span class="font-bold text-red-700">Tidak Dapat Dikembalikan</span>
                        </div>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li>• Layanan yang sudah diakses</li>
                            <li>• Subscription yang diperpanjang otomatis</li>
                            <li>• Biaya administrasi</li>
                        </ul>
                    </div>
                    
                    <!-- Refundable -->
                    <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="font-bold text-green-700">Dapat Dikembalikan</span>
                        </div>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li>• Kesalahan sistem dari kami</li>
                            <li>• Transaksi ganda/duplikat</li>
                            <li>• Pembatalan sesuai ketentuan</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Cancellation Timeline -->
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Timeline Pembatalan Booking</h3>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">Lebih dari 24 jam sebelum booking:</span>
                            <span class="font-bold text-green-600">Refund 100%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">2-24 jam sebelum booking:</span>
                            <span class="font-bold text-yellow-600">Refund 50%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">Kurang dari 2 jam:</span>
                            <span class="font-bold text-red-600">Tidak ada refund</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Refund Process -->
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Proses Refund</h3>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <span class="font-bold text-orange-600">1</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">Ajukan permintaan melalui email</p>
                            <p class="text-sm text-gray-600">refund@workspace.id</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <span class="font-bold text-orange-600">2</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">Lampirkan bukti pembayaran</p>
                            <p class="text-sm text-gray-600">Invoice dan bukti transfer</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <span class="font-bold text-orange-600">3</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">Tunggu proses 5-14 hari kerja</p>
                            <p class="text-sm text-gray-600">Termasuk verifikasi dan proses bank</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Important Notes -->
            <div class="mb-6">
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.197 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <h4 class="font-bold text-yellow-800 mb-1">Perhatian Penting</h4>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• Permintaan refund maksimal 7 hari setelah transaksi</li>
                                <li>• Waktu dihitung berdasarkan zona waktu server (WIB)</li>
                                <li>• Refund dikembalikan ke metode pembayaran asli</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact -->
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Kontak Dukungan</h3>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-800">Email Refund</p>
                                <a href="mailto:refund@workspace.id" class="text-blue-600 hover:text-blue-800">
                                    refund@workspace.id
                                </a>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-800">Jam Operasional</p>
                                <p class="text-gray-600">Senin-Jumat: 09:00-17:00 WIB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
    <!-- Footer -->
    <div class="sticky bottom-0 bg-white border-t px-8 py-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm text-gray-600 text-center sm:text-left">
                Dengan menutup ini, Anda menyetujui Kebijakan Refund kami.
            </p>
            <button onclick="parent.closeModal && parent.closeModal()" 
                    class="px-8 py-3 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-md hover:shadow-lg w-full sm:w-auto">
                Saya Memahami & Menyetujui
            </button>
        </div>
    </div>
</div>