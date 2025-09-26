{{-- resources/views/layouts/dashboard/invoicebooking.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-50" x-data="invoiceBooking()">

    {{-- Content --}}
    <div class="flex-1 ml-0 md:ml-52 lg:ml-64 xl:ml-64 mb-16">
        
        {{-- Header with Background --}}
        <div 
                class="relative h-36 sm:h-40 md:h-44 lg:h-48 overflow-hidden rounded-b-lg shadow"
                style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); background-size: cover; background-position: center;"
            >
                {{-- Orange Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-orange-500/90 via-orange-500/70 to-orange-400/60"></div>

                {{-- Arrow Button --}}
                <a href="{{ route('dashboard.home') }}" 
                class="absolute top-3 left-3 z-20 bg-white/20 hover:bg-white/30 rounded-full p-2 transition">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>

                {{-- Logo (Top Right) --}}
                <div class="absolute top-3 right-3 z-20 flex items-center space-x-2 bg-white/20 backdrop-blur-sm rounded-lg px-3 py-1">
                    <img src="/assets/LOGO_URBAN_OFFICE.png" alt="Urban Office Logo" class="w-12 h-12 object-contain"/>
                </div>

                {{-- Title Center --}}
                <div class="absolute inset-x-0 top-12 sm:top-14 md:top-16 text-center z-20">
                    <h1 class="text-white text-lg sm:text-xl md:text-2xl font-bold tracking-wide">BOOKING</h1>
                </div>

                {{-- Bottom Section --}}
                <div class="absolute bottom-3 left-4 right-4 z-20">
                    <p class="text-white/90 text-sm sm:text-base leading-snug">
                        Semua transaksi yang sudah aktif dan menunggu pembayaran
                    </p>
                </div>
            </div>


        {{-- Main Content --}}
        <div class="p-4 md:p-6">
            
            {{-- First Invoice Card --}}
            <div class="bg-white rounded-xl shadow-lg mb-6 overflow-hidden transition-all duration-700 ease-out"
                :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                style="transition-delay:200ms;">
                
                {{-- Success Header --}}
                <div class="bg-green-50 border-l-4 border-green-500 p-4 flex items-center">
                    <div class="flex items-center space-x-2">
                        <div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-green-800">Berhasil</span>
                    </div>
                    <div class="ml-auto">
                        <span class="text-2xl md:text-3xl font-bold text-gray-800">Rp. 5.280.000</span>
                        <p class="text-sm text-gray-600">PT URBAN KREASI BERSAMA</p>
                    </div>
                </div>

                {{-- Invoice Details --}}
                <div class="p-4 md:p-6">
                    {{-- Payment Details --}}
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Detail Pembayaran</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nominal Pesanan</span>
                                <span class="font-medium text-gray-600">Rp. 350.000</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nominal Diskon</span>
                                <span class="font-medium text-red-500">Rp. -10.000</span>
                            </div>
                            <hr class="my-2"/>
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-800">Nominal Pembayaran</span>
                                <span class="font-semibold text-gray-600">Rp. 340.000</span>
                            </div>
                        </div>
                    </div>

                    {{-- Booking Info --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="flex items-center space-x-3 mb-4">
                                <img src="/assets/LOGO_URBAN_OFFICE.png" alt="Urban Office Logo" class="w-14 h-14 object-contain ml-7"/>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-600">Kode Pemesanan</p>
                                <p class="font-semibold text-gray-600">549/VO/VIII/25</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600">Tanggal Transaksi</p>
                                <p class="font-semibold text-gray-600">25/12/2025</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Jenis Layanan</p>
                                <p class="font-semibold text-gray-600">Virtual Office</p>
                            </div>
                        </div>
                    </div>

                    {{-- Timeline --}}
                    <div class="mt-8">
                        <h3 class="font-semibold text-gray-800 mb-4">Timeline Layanan</h3>
                        <div class="relative">
                            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-orange-200"></div>
                            <div class="space-y-4">
                                <div class="relative flex items-start space-x-4">
                                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                                        <div class="w-3 h-3 bg-white rounded-full"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">13:40 25 Juni 2025</p>
                                        <p class="font-medium text-gray-600">Amaris Hotel</p>
                                    </div>
                                </div>
                                <div class="relative flex items-start space-x-4">
                                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                                        <div class="w-3 h-3 bg-white rounded-full"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">13:40 24 Juni 2025</p>
                                        <p class="font-medium text-gray-600">Amaris Hotel</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Button --}}
                    <div class="mt-6 pt-4 border-t">
                        <button 
                            @click="showPaymentModal = true"
                            class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Bayar Sekarang
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Second Invoice Card --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-700 ease-out"
                :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                style="transition-delay:400ms;">
                
                <div class="bg-green-50 border-l-4 border-green-500 p-4 flex items-center">
                    <div class="flex items-center space-x-2">
                        <div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-green-800">Berhasil</span>
                    </div>
                    <div class="ml-auto">
                        <span class="text-2xl md:text-3xl font-bold text-gray-800">Rp. 8.000.000</span>
                        <p class="text-sm text-gray-600">PT URBAN KREASI BERSAMA</p>
                    </div>
                </div>
                <div class="p-4 md:p-6 pb-2">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-1000 ease-out" style="width:100%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Modal --}}
        <div x-show="showPaymentModal" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-xl font-bold text-gray-800">Pilih Metode Pembayaran</h3>
                    <button @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Content --}}
                <div class="p-6 space-y-6">
                    {{-- Transfer Bank --}}
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">Transfer Bank</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <template x-for="bank in banks" :key="bank.name">
                                <button @click="selectedPayment = bank.name"
                                    class="p-4 border-2 rounded-lg transition-all duration-200 hover:shadow-md"
                                    :class="selectedPayment === bank.name ? 'border-orange-500 bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                    <div class="flex flex-col items-center space-y-2">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white text-lg"
                                            :class="bank.color">
                                            <span x-text="bank.logo"></span>
                                        </div>
                                        <span class="font-medium text-gray-800" x-text="bank.name"></span>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- E-Wallet --}}
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">E-Wallet</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <template x-for="wallet in wallets" :key="wallet.name">
                                <button @click="selectedPayment = wallet.name"
                                    class="p-4 border-2 rounded-lg transition-all duration-200 hover:shadow-md"
                                    :class="selectedPayment === wallet.name ? 'border-orange-500 bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                                    <div class="flex flex-col items-center space-y-2">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white text-lg"
                                            :class="wallet.color">
                                            <span x-text="wallet.logo"></span>
                                        </div>
                                        <span class="font-medium text-gray-800" x-text="wallet.name"></span>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- QRIS --}}
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">QRIS</h4>
                        <button @click="selectedPayment = 'QRIS'"
                            class="w-full p-4 border-2 rounded-lg transition-all duration-200 hover:shadow-md"
                            :class="selectedPayment === 'QRIS' ? 'border-orange-500 bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                            <div class="flex items-center justify-center space-x-3">
                                <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center text-white text-lg">📱</div>
                                <span class="font-medium text-gray-800">QRIS - Scan & Pay</span>
                            </div>
                        </button>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="p-6 border-t bg-gray-50 rounded-b-xl">
                    <div class="flex space-x-3">
                        <button @click="showPaymentModal = false"
                            class="flex-1 py-3 px-4 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100">
                            Batal
                        </button>
                        <button @click="confirmPayment"
                            :disabled="!selectedPayment"
                            class="flex-1 py-3 px-4 rounded-lg font-medium transition-all duration-200"
                            :class="selectedPayment ? 'bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white shadow-lg' : 'bg-gray-300 text-gray-500 cursor-not-allowed'">
                            Bayar Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Alpine.js Component --}}
<script>
function invoiceBooking() {
    return {
        isVisible: false,
        showPaymentModal: false,
        selectedPayment: '',
        banks: [
            { name: 'BCA', color: 'bg-blue-600', logo: '🏦' },
            { name: 'BNI', color: 'bg-orange-500', logo: '🏦' },
            { name: 'BRI', color: 'bg-blue-800', logo: '🏦' },
            { name: 'Mandiri', color: 'bg-yellow-500', logo: '🏦' },
        ],
        wallets: [
            { name: 'OVO', color: 'bg-purple-600', logo: '💜' },
            { name: 'DANA', color: 'bg-blue-500', logo: '💙' },
            { name: 'ShopeePay', color: 'bg-orange-500', logo: '🛒' },
            { name: 'GoPay', color: 'bg-green-500', logo: '🚗' },
        ],
        init() {
            this.isVisible = true;
        },
        confirmPayment() {
            if (this.selectedPayment) {
                alert(`Pembayaran dipilih: ${this.selectedPayment}`);
                this.showPaymentModal = false;
                this.selectedPayment = '';
            }
        }
    }
}
</script>
@endsection
