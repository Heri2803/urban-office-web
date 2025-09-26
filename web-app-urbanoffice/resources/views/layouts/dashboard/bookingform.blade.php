@extends('layouts.app') {{-- layout utama --}}

@section('head')
<script src="https://cdn.tailwindcss.com"></script>
@endsection

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">

    {{-- Content --}}
    <div class="flex-1 ml-0 md:ml-60 lg:ml-64 xl:ml-64 p-4 md:p-6 mb-12">

        {{-- User Status --}}
        <div class="mb-6 transition-all duration-700 ease-out opacity-100 translate-y-0">
            @include('layouts.components.userstatus', [
                'name' => 'Georgius Mario',
                'status' => 'Virtual Office'
            ])
        </div>

        {{-- Main Content Container - Horizontal Layout --}}
        <div class="flex flex-col lg:flex-row gap-6">
            
            {{-- Room Card - Desktop: Left side (1/3 width) --}}
            <div class="w-full lg:w-1/3 order-1 lg:order-1 mb-6 lg:mb-0">
                @include('layouts.components.roomcard', [
                    'roomType' => 'Virtual Office',
                    'allRoomData' => [
                        'Virtual Office' => [
                            'images' => [
                                'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&h=600&fit=crop&auto=format',
                                'https://images.unsplash.com/photo-1497366811353-6870744d04b2?w=800&h=600&fit=crop&auto=format'
                            ],
                            'benefits' => [
                                'Alamat bisnis prestisius untuk perusahaan Anda',
                                'Layanan resepsionis profesional dan penerimaan surat',
                                'Akses ke meeting room dan fasilitas kantor premium'
                            ],
                        ],
                        'Meeting Room' => [
                            'images' => [
                                'https://images.unsplash.com/photo-1556761175-b413da4baf72?w=800&h=600&fit=crop&auto=format',
                                'https://images.unsplash.com/photo-1556761175-4b46a572b786?w=800&h=600&fit=crop&auto=format'
                            ],
                            'benefits' => [
                                'Ruang meeting modern dengan kapasitas 8-12 orang',
                                'Dilengkapi proyektor, whiteboard, dan sistem audio visual',
                                'Wi-Fi berkecepatan tinggi dan layanan catering tersedia'
                            ],
                        ],
                        'Coworking Space' => [
                            'images' => [
                                'https://images.unsplash.com/photo-1497366754035-f200968a6e72?w=800&h=600&fit=crop&auto=format',
                                'https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=800&h=600&fit=crop&auto=format'
                            ],
                            'benefits' => [
                                'Workspace fleksibel dengan suasana kerja yang produktif',
                                'Akses 24/7 dengan keamanan terjamin dan cleaning service',
                                'Komunitas profesional dan networking opportunities'
                            ],
                        ],
                        'Event Space' => [
                            'images' => [
                                'https://images.unsplash.com/photo-1505236858219-8359eb29e329?w=800&h=600&fit=crop&auto=format',
                                'https://images.unsplash.com/photo-1511578314322-379afb476865?w=800&h=600&fit=crop&auto=format'
                            ],
                            'benefits' => [
                                'Ruang event luas untuk seminar, workshop, dan acara besar',
                                'Kapasitas hingga 100 orang dengan tata letak fleksibel',
                                'Sound system profesional, lighting, dan dukungan teknis'
                            ],
                        ]
                    ]
                ])
            </div>

            {{-- Booking Form - Desktop: Right side (2/3 width) --}}
            <div class="w-full lg:w-2/3 order-2 lg:order-2"
                 x-data="bookingFormComponent()" 
                 x-init="init()">
                <div class="bg-white shadow-lg hover:shadow-xl rounded-xl p-4 md:p-6">
                    
                    {{-- Header --}}
                    <div class="mb-6 transition-all duration-500 ease-out opacity-100 translate-x-0" 
                         style="transition-delay: 400ms;">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Form Booking</h2>
                        <p class="text-gray-600">Isi form di bawah untuk melakukan pemesanan ruang kerja</p>
                    </div>

                    {{-- Form --}}
                    {{-- resources/views/layouts/dashboard/bookingform.blade.php --}}

                    <div class="space-y-6" x-data="bookingForm()" x-init="init()">

                        {{-- Pilihan Kota dan Ruang --}}
                        <div class="flex flex-col md:flex-row md:space-x-4 mb-6 space-y-2 md:space-y-0 transition-all duration-500 ease-out opacity-100 translate-x-0" 
                            style="transition-delay: 500ms;">
                            <div class="flex-1 w-full">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Pilih Kota</label>
                                <select x-model="city"  
                                    class="border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 
                                        rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 
                                        hover:border-gray-400 bg-white">
                                    <option>Surabaya</option>
                                    <option>Jakarta</option>
                                    <option>Bandung</option>
                                </select>
                            </div>

                            <div class="flex-1 w-full">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Jenis Ruang</label>
                                <input type="text" 
                                    x-model="selectedRoomType"
                                    class="border-2 border-gray-300 bg-gray-50 rounded-lg p-3 w-full text-gray-800 font-medium"
                                    readonly>
                            </div>
                        </div>

                        {{-- Tabs Pilih Jenis Ruang --}}
                        <div class="mb-6 transition-all duration-500 ease-out opacity-100 translate-y-0" 
                            style="transition-delay: 600ms;">
                            <label class="block text-sm font-semibold text-gray-800 mb-3">Pilih Jenis Ruang</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-2 md:gap-3">
                                <template x-for="(roomType, index) in roomTypes" :key="roomType">
                                    <button type="button"
                                            @click="selectRoomType(roomType)"
                                            class="px-3 md:px-4 py-3 rounded-lg font-semibold text-sm md:text-base 
                                                transition-all duration-300 transform hover:scale-105"
                                            :class="selectedRoomType === roomType 
                                                ? 'bg-orange-500 hover:bg-orange-600 text-white shadow-lg' 
                                                : 'bg-gray-100 hover:bg-gray-200 text-gray-800 hover:shadow-md'"
                                            :style="`transition-delay: ${700 + index * 100}ms`">
                                        <span x-text="roomType"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Informasi Tambahan --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 transition-all duration-500 ease-out opacity-100 translate-y-0" 
                            style="transition-delay: 800ms;">
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Jumlah Orang</label>
                                <input type="number" placeholder="Masukkan jumlah orang" x-model="jumlahOrang"
                                    class="border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 
                                        rounded-lg p-3 w-full text-gray-800 font-medium placeholder-gray-500 
                                        transition-all duration-200 hover:border-gray-400">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Tanggal Booking</label>
                                <input type="date" x-model="bookingDate"
                                    class="border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 
                                        rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 
                                        hover:border-gray-400">
                            </div>
                        </div>

                        {{-- Tambahan Field Khusus --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            
                            {{-- Virtual Office: Paket Bulanan / Tahunan --}}
                            <div x-show="selectedRoomType === 'Virtual Office'" class="transition-all duration-300">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Paket</label>
                                <select x-model="paket"
                                    class="border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 
                                        rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 bg-white">
                                    <option value="">Pilih Paket</option>
                                    <option value="monthly">Bulanan</option>
                                    <option value="yearly">Tahunan</option>
                                </select>
                            </div>

                            {{-- Virtual Office: Input Bulan --}}
                            <div x-show="selectedRoomType === 'Virtual Office' && paket === 'monthly'" class="transition-all duration-300">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Jumlah Bulan</label>
                                <input type="number" x-model="bulan" min="1" placeholder="Masukkan jumlah bulan"
                                    class="border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 
                                        rounded-lg p-3 w-full text-gray-800 font-medium placeholder-gray-500">
                            </div>

                            {{-- Virtual Office: Input Tahun --}}
                            <div x-show="selectedRoomType === 'Virtual Office' && paket === 'yearly'" class="transition-all duration-300">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Jumlah Tahun</label>
                                <input type="number" x-model="tahun" min="1" placeholder="Masukkan jumlah tahun"
                                    class="border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 
                                        rounded-lg p-3 w-full text-gray-800 font-medium placeholder-gray-500">
                            </div>

                            {{-- Meeting Room: Input Jam --}}
                            <div x-show="selectedRoomType === 'Meeting Room'" class="transition-all duration-300">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Durasi (Jam)</label>
                                <input type="number" x-model="jam" min="1" placeholder="Masukkan durasi jam"
                                    class="border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 
                                        rounded-lg p-3 w-full text-gray-800 font-medium placeholder-gray-500">
                            </div>
                        </div>

                        {{-- Status PKP & Telepon --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 transition-all duration-500 ease-out opacity-100 translate-y-0" 
                            style="transition-delay: 900ms;">
                            <div x-model="statusPkp" x-show="selectedRoomType === 'Virtual Office'">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Status PKP</label>
                                <select
                                    class="border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 
                                        rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 
                                        hover:border-gray-400 bg-white">
                                    <option>Non PKP</option>
                                    <option>PKP</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Nomor Telepon</label>
                                <input type="text" placeholder="Nomor Telepon" x-model="phone"
                                    class="border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 
                                        rounded-lg p-3 w-full text-gray-800 font-medium placeholder-gray-500 
                                        transition-all duration-200 hover:border-gray-400">
                            </div>
                        </div>

                        {{-- Nama & Email --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 transition-all duration-500 ease-out opacity-100 translate-y-0" 
                            style="transition-delay: 1000ms;">
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Nama Lengkap</label>
                                <input type="text" placeholder="Nama Lengkap" x-model="namaLengkap"
                                    class="border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 
                                        rounded-lg p-3 w-full text-gray-800 font-medium placeholder-gray-500 
                                        transition-all duration-200 hover:border-gray-400">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Email</label>
                                <input type="email" placeholder="Email" x-model="email"
                                    class="border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 
                                        rounded-lg p-3 w-full text-gray-800 font-medium placeholder-gray-500 
                                        transition-all duration-200 hover:border-gray-400">
                            </div>
                        </div>

                        {{-- Button --}}
                        <div class="transition-all duration-500 ease-out opacity-100 translate-y-0" 
                            style="transition-delay: 1100ms;">
                            <button @click="submitBooking"
                                    class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700
                                        text-white font-bold py-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300
                                        transform hover:scale-105 hover:-translate-y-1 text-center">
                                <span class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z" />
                                    </svg>
                                    Mulai Sewa
                                </span>
                            </button>
                        </div>

                        <div class="text-center mt-4 transition-all duration-500 ease-out opacity-100 translate-y-0" 
                            style="transition-delay: 1200ms;">
                            <p class="text-gray-600 text-sm px-2 leading-relaxed">
                                Anda bisa memesan ruang ini untuk penggunaan per jam, hari, maupun
                                jangka waktu yang panjang
                            </p>
                        </div>
                    </div>


                </div>
            </div>

        </div>
    </div>
</div>

<script>
function bookingFormComponent() {
    return {
        selectedRoomType: 'Virtual Office',
        roomTypes: ['Virtual Office', 'Event Space', 'Coworking Space', 'Meeting Room'],

        init() {
            console.log('Booking form initialized');
            // Panggil selectRoomType untuk inisialisasi awal
            this.selectRoomType(this.selectedRoomType);
        },

        selectRoomType(roomType) {
            console.log('Selecting room type:', roomType);
            this.selectedRoomType = roomType;
            
            // Method 1: Update roomcard component jika ada (Alpine.js way)
            const roomCard = document.querySelector('[x-data*="roomCardComponent"]');
            if (roomCard && roomCard._x_dataStack && roomCard._x_dataStack[0].changeRoomType) {
                console.log('Updating roomcard via direct call');
                roomCard._x_dataStack[0].changeRoomType(roomType);
            }

            // Method 2: Dispatch custom event (Recommended)
            console.log('Dispatching room-type-selected event');
            this.$dispatch('room-type-selected', { 
                roomType: roomType,
                timestamp: new Date().getTime()
            });

            // Method 3: Update via Alpine store jika Anda menggunakan Alpine store
            if (Alpine.store && Alpine.store('booking')) {
                Alpine.store('booking').setRoomType(roomType);
            }

            // Method 4: Trigger manual update untuk semua component roomcard
            document.querySelectorAll('[x-data*="roomCardComponent"]').forEach(card => {
                if (card._x_dataStack && card._x_dataStack[0]) {
                    const component = card._x_dataStack[0];
                    if (component.changeRoomType) {
                        component.changeRoomType(roomType);
                    }
                    if (component.selectedRoomType !== undefined) {
                        component.selectedRoomType = roomType;
                    }
                }
            });
        }
    }
}
</script>

{{-- Pastikan sudah include Alpine.js --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

{{-- Midtrans Snap JS --}}
<script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
function bookingForm() {
    return {
        selectedRoomType: '',
        paket: '',
        bulan: '', // Ubah dari default 1 ke empty string
        tahun: '', // Ubah dari default 1 ke empty string
        jam: '', // Ubah dari default 1 ke empty string
        jumlahOrang: '', // Ubah dari default 1 ke empty string
        city: '', // Ubah dari default 'Surabaya' ke empty string
        bookingDate: '',
        namaLengkap: '',
        email: '',
        phone: '',
        statusPkp: '', // Ubah dari default 'Non PKP' ke empty string
        isSubmitting: false,
        
        // Room types for the buttons
        roomTypes: ['Virtual Office', 'Meeting Room', 'Event Space', 'Coworking Space'],
        
        // Options untuk dropdown city
        cityOptions: [
            { value: 'Surabaya', label: 'Surabaya' },
            { value: 'Bandung', label: 'Bandung' },
            { value: 'Jakarta', label: 'Jakarta' }
        ],
        
        // Options untuk dropdown status PKP
        statusPkpOptions: [
            { value: 'Non PKP', label: 'Non PKP' },
            { value: 'PKP', label: 'PKP' }
        ],

        init() {
            // Inisialisasi default jika perlu
            console.log('Booking form initialized');
        },

        async submitBooking() {
            if (this.isSubmitting) {
                return; // Prevent double submission
            }

            try {
                this.isSubmitting = true;
                
                // Validasi frontend
                if (!this.validateForm()) {
                    return;
                }

                // Ambil data dari form - pastikan nilai numerik di-convert dengan benar
                const data = {
                    city: this.city,
                    room_type: this.selectedRoomType,
                    jumlah_orang: this.jumlahOrang ? parseInt(this.jumlahOrang) : null,
                    booking_date: this.bookingDate,
                    paket: this.paket,
                    bulan: this.bulan ? parseInt(this.bulan) : null,
                    tahun: this.tahun ? parseInt(this.tahun) : null,
                    jam: this.jam ? parseInt(this.jam) : null,
                    status_pkp: this.statusPkp,
                    phone: this.phone,
                    nama_lengkap: this.namaLengkap,
                    email: this.email
                };

                console.log('Submitting booking data:', data);

                // Get CSRF token from meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    throw new Error('CSRF token not found. Add <meta name="csrf-token" content="{{ csrf_token() }}"> to your HTML head.');
                }

                // Kirim request ke Laravel untuk generate Snap Token
                const response = await fetch('/transactions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(data)
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers.get('content-type'));

                // Cek apakah response OK
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Response error text:', errorText);
                    
                    // Coba parse sebagai JSON jika possible
                    try {
                        const errorJson = JSON.parse(errorText);
                        throw new Error(errorJson.message || `HTTP error! status: ${response.status}`);
                    } catch (parseError) {
                        throw new Error(`Server error (${response.status}). Please check the console for details.`);
                    }
                }

                // Cek content type
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const responseText = await response.text();
                    console.error('Expected JSON but got:', responseText.substring(0, 200));
                    throw new Error('Server returned invalid response format');
                }

                const result = await response.json();
                console.log('Success response:', result);

                if (result.success && result.snap_token) {
                    // Cek apakah Snap sudah di-load
                    if (typeof snap === 'undefined') {
                        throw new Error('Midtrans Snap belum di-load. Pastikan script Snap sudah dimuat.');
                    }

                    // Panggil Snap pop-up
                    snap.pay(result.snap_token, {
                        onSuccess: (response) => {
                            console.log('Payment success:', response);
                            alert('Pembayaran berhasil!');
                            // PERBAIKAN: Redirect ke halaman invoice dengan order_id
                            window.location.href = `/dashboard/invoice/`;
                        },
                        onPending: (response) => {
                            console.log('Payment pending:', response);
                            alert('Pembayaran pending! Silakan selesaikan pembayaran Anda.');
                            // Optional: redirect ke halaman invoice juga untuk pending
                            window.location.href = `/dashboard/invoice/`;
                        },
                        onError: (response) => {
                            console.error('Payment error:', response);
                            alert('Terjadi kesalahan pembayaran! Silakan coba lagi.');
                        },
                        onClose: () => {
                            console.log('Payment popup closed');
                            alert('Anda menutup popup pembayaran tanpa menyelesaikan transaksi.');
                        }
                    });
                } else {
                    throw new Error(result.message || 'Gagal generate Snap Token');
                }

            } catch (error) {
                console.error('Booking error:', error);
                
                // Show user-friendly error message
                let errorMessage = 'Terjadi kesalahan saat memproses booking. ';
                if (error.message.includes('CSRF')) {
                    errorMessage += 'Silakan refresh halaman dan coba lagi.';
                } else if (error.message.includes('Network')) {
                    errorMessage += 'Periksa koneksi internet Anda.';
                } else {
                    errorMessage += error.message || 'Silakan coba lagi.';
                }
                
                alert(errorMessage);
            } finally {
                this.isSubmitting = false;
            }
        },

        validateForm() {
            console.log('Validating form with data:', {
                city: this.city,
                selectedRoomType: this.selectedRoomType,
                namaLengkap: this.namaLengkap,
                email: this.email,
                jumlahOrang: this.jumlahOrang,
                statusPkp: this.statusPkp,
                jam: this.jam
            });

            // Validasi field wajib dasar
            const basicRequired = [
                { field: 'city', name: 'Kota', value: this.city },
                { field: 'selectedRoomType', name: 'Jenis Ruang', value: this.selectedRoomType },
                { field: 'namaLengkap', name: 'Nama Lengkap', value: this.namaLengkap },
                { field: 'email', name: 'Email', value: this.email }
            ];

            for (const item of basicRequired) {
                console.log(`Checking ${item.name}:`, `"${item.value}"`, 'Type:', typeof item.value);
                
                // PERBAIKAN: Cek dengan lebih teliti untuk city
                if (item.field === 'city') {
                    if (!item.value || item.value === '' || item.value === null || item.value === undefined) {
                        console.error(`Validation failed for ${item.name} - value is empty`);
                        alert(`${item.name} wajib diisi!`);
                        return false;
                    }
                } else {
                    if (!item.value || item.value.toString().trim() === '') {
                        console.error(`Validation failed for ${item.name}`);
                        alert(`${item.name} wajib diisi!`);
                        return false;
                    }
                }
            }

            // Validasi email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(this.email)) {
                alert('Format email tidak valid!');
                return false;
            }

            // Validasi khusus berdasarkan room type
            if (this.selectedRoomType === 'Virtual Office') {
                if (!this.paket) {
                    alert('Pilih paket untuk Virtual Office!');
                    return false;
                }
                
                // Validasi status PKP untuk Virtual Office
                if (!this.statusPkp) {
                    alert('Status PKP wajib dipilih untuk Virtual Office!');
                    return false;
                }
                
                // Validasi bulan atau tahun untuk Virtual Office
                if (!this.bulan && !this.tahun) {
                    alert('Pilih durasi bulan atau tahun untuk Virtual Office!');
                    return false;
                }
                
                // Validasi nilai bulan/tahun harus positif
                if (this.bulan && parseInt(this.bulan) < 1) {
                    alert('Jumlah bulan harus minimal 1!');
                    return false;
                }
                
                if (this.tahun && parseInt(this.tahun) < 1) {
                    alert('Jumlah tahun harus minimal 1!');
                    return false;
                }
            }

            // PERBAIKAN: Validasi berdasarkan room type yang lebih spesifik
            if (this.selectedRoomType === 'Meeting Room') {
                // Meeting Room: perlu jumlah orang, jam, dan booking date
                if (!this.jumlahOrang) {
                    alert('Jumlah orang wajib diisi untuk Meeting Room!');
                    return false;
                }
                
                if (parseInt(this.jumlahOrang) < 1) {
                    alert('Jumlah orang harus minimal 1!');
                    return false;
                }
                
                if (!this.jam) {
                    alert('Durasi jam wajib diisi untuk Meeting Room!');
                    return false;
                }
                
                if (parseInt(this.jam) < 1) {
                    alert('Durasi jam harus minimal 1!');
                    return false;
                }
                
                if (!this.bookingDate) {
                    alert('Tanggal booking wajib diisi untuk Meeting Room!');
                    return false;
                }
            }

            if (['Event Space', 'Coworking Space'].includes(this.selectedRoomType)) {
                // Event Space & Coworking Space: hanya perlu jumlah orang dan booking date (TANPA jam)
                if (!this.jumlahOrang) {
                    alert(`Jumlah orang wajib diisi untuk ${this.selectedRoomType}!`);
                    return false;
                }
                
                if (parseInt(this.jumlahOrang) < 1) {
                    alert('Jumlah orang harus minimal 1!');
                    return false;
                }
                
                if (!this.bookingDate) {
                    alert(`Tanggal booking wajib diisi untuk ${this.selectedRoomType}!`);
                    return false;
                }

                // PERBAIKAN: JAM TIDAK WAJIB untuk Event Space dan Coworking Space
                console.log(`${this.selectedRoomType} - Jam field is optional`);
            }

            console.log('Form validation passed');
            return true;
        },

        // Helper method untuk reset form jika diperlukan
        resetForm() {
            this.selectedRoomType = '';
            this.paket = '';
            this.bulan = '';
            this.tahun = '';
            this.jam = '';
            this.jumlahOrang = '';
            this.city = '';
            this.bookingDate = '';
            this.namaLengkap = '';
            this.email = '';
            this.phone = '';
            this.statusPkp = '';
            this.isSubmitting = false;
        }
    }
}
</script>

@endsection