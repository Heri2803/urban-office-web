<!-- File: resources/views/components/delivery-policy.blade.php -->
<div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] max-h-[90dvh] overflow-hidden">
    <!-- Header -->
    <div class="sticky top-0 bg-white border-b px-8 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Delivery Policy</h2>
                <p class="text-gray-600 mt-1">Kebijakan Pengiriman Layanan</p>
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
            <div class="mb-8">
                <p class="text-gray-700 leading-relaxed">
                    Kebijakan Delivery ini menjelaskan bagaimana layanan Urban Office / WorkSpace 
                    disampaikan kepada Anda setelah pemesanan dan pembayaran berhasil.
                </p>
                <p class="text-gray-700 leading-relaxed mt-4 font-medium bg-blue-50 p-4 rounded-lg border-l-4 border-blue-400">
                    Karena kami menyediakan layanan digital, "delivery" mengacu pada akses dan aktivasi layanan dalam sistem kami.
                </p>
            </div>
            
            <!-- Key Points -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Aktivasi Layanan</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Instant Access -->
                    <div class="bg-green-50 border-l-4 border-green-400 p-5 rounded-r-lg">
                        <div class="flex items-center mb-3">
                            <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <h4 class="font-bold text-green-800">Akses Instan</h4>
                        </div>
                        <ul class="text-gray-700 space-y-2">
                            <li class="flex items-start">
                                <div class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3"></div>
                                <span>Akses langsung setelah pembayaran berhasil</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3"></div>
                                <span>Login ke dashboard My Urban Office</span>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Scheduled -->
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-5 rounded-r-lg">
                        <div class="flex items-center mb-3">
                            <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <h4 class="font-bold text-blue-800">Terjadwal</h4>
                        </div>
                        <ul class="text-gray-700 space-y-2">
                            <li class="flex items-start">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3"></div>
                                <span>Aktivasi sesuai tanggal booking</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3"></div>
                                <span>Notifikasi email sebelum layanan dimulai</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Service Types -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Jenis Layanan & Delivery</h3>
                
                <div class="space-y-4">
                    <!-- Meeting Room -->
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 mb-1">Meeting Room</h4>
                            <p class="text-gray-600 text-sm">Akses ruangan sesuai jadwal booking. QR code/akses dikirim via email.</p>
                        </div>
                    </div>
                    
                    <!-- Virtual Office -->
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 mb-1">Virtual Office</h4>
                            <p class="text-gray-600 text-sm">Aktivasi akun dalam 1-2 jam kerja. Akses dashboard untuk manajemen.</p>
                        </div>
                    </div>
                    
                    <!-- Coworking Space -->
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 mb-1">Coworking Space</h4>
                            <p class="text-gray-600 text-sm">Akses harian/bulanan sesuai paket. Member card/akses diberikan di lokasi.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Delivery Time -->
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Waktu Delivery</h3>
                
                <div class="bg-yellow-50 p-5 rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-yellow-100">
                                    <th class="py-3 px-4 text-left font-bold text-yellow-800">Jenis Layanan</th>
                                    <th class="py-3 px-4 text-left font-bold text-yellow-800">Delivery Time</th>
                                    <th class="py-3 px-4 text-left font-bold text-yellow-800">Metode</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-yellow-200">
                                <tr class="bg-white">
                                    <td class="py-3 px-4">Meeting Room Booking</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">
                                            Instan
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">Email Confirmation + QR Code</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="py-3 px-4">Virtual Office</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                            1-2 jam kerja
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">Email Activation Link</td>
                                </tr>
                                <tr class="bg-white">
                                    <td class="py-3 px-4">Coworking Space (Harian)</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">
                                            Instan
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">Email Ticket + Check-in di Lokasi</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Support -->
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Dukungan Delivery</h3>
                
                <div class="bg-gray-50 p-5 rounded-lg">
                    <p class="text-gray-700 mb-4">Jika mengalami masalah dengan delivery layanan:</p>
                    
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-800">Email Support</p>
                                <a href="mailto:support@urbanoffice.co.id" class="text-blue-600 hover:text-blue-800">
                                    support@urbanoffice.co.id
                                </a>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-800">Response Time</p>
                                <p class="text-gray-600">Maksimal 2 jam kerja untuk urgent issues</p>
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
                Kebijakan ini bagian dari Terms of Service Urban Office / WorkSpace.
            </p>
            <button onclick="parent.closeModal && parent.closeModal()" 
                    class="px-8 py-3 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-md hover:shadow-lg w-full sm:w-auto">
                Mengerti
            </button>
        </div>
    </div>
</div>