@extends('layouts.app')

@section('head')
<script type="text/javascript" 
src="https://app.sandbox.midtrans.com/snap/snap.js" 
data-client-key="{{ config('midtrans.client_key') }}"></script> 

<script> 
function previewFileName(input) {
    const file = input.files[0];
    if (file) {
        document.getElementById('fileNamePreview').textContent = "File dipilih: " + file.name;
    }
}

function openSnap(token) 
{ 
    document.body.style.overflow = 'hidden'; 
    if (typeof snap === 'undefined') {
        document.body.style.overflow = '';
        alert('Midtrans Snap SDK belum siap. Silakan coba refresh halaman.');
        return;
    }
    snap.pay(token, 
    { 
        onSuccess: function(result) { 
            console.log("Success:", result); 
            document.body.style.overflow = ''; 
            location.reload(); 
        }, 
        onPending: function(result) { 
            console.log("Pending:", result); 
            document.body.style.overflow = '';
        }, 
        onError: function(result) { 
            console.error("Error:", result); 
            document.body.style.overflow = '';
        }, 
        onClose: function() { 
            document.body.style.overflow = '';
            alert('Anda menutup popup tanpa menyelesaikan pembayaran.'); 
        } 
    }); 
} 

document.addEventListener('alpine:init', () => {
    Alpine.data('mailsData', () => ({
        isFilterOpen: false,
        allTransactions: @json($transactions), 
        filterStatus: '', 
        filterDate: '',
        
        currentPage: 1,
        itemsPerPage: 5,

        get filteredTransactions() {
            return this.allTransactions.filter(transaction => {
                const statusMatch = this.filterStatus === '' || transaction.status === this.filterStatus;
                
                let dateMatch = true;
                if (this.filterDate) {
                    const bookingDate = transaction.booking_date ? new Date(transaction.booking_date).toISOString().split('T')[0] : '';
                    dateMatch = bookingDate === this.filterDate;
                }

                return statusMatch && dateMatch;
            });
        },

        get paginatedTransactions() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredTransactions.slice(start, end);
        },

        get totalPages() {
            return Math.ceil(this.filteredTransactions.length / this.itemsPerPage);
        },

        get pageNumbers() {
            let pages = [];
            const total = this.totalPages;
            const current = this.currentPage;
        
            if (total <= 5) {
                // 1,2,3,4,5 tanpa duplikat
                pages = Array.from({ length: total }, (_, i) => i + 1);
            } else {
                if (current <= 2) {
                    pages = [1, 2, 3, '...', total];
                } else if (current >= total - 1) {
                    pages = [1, '...', total - 2, total - 1, total];
                } else {
                    pages = [1, '...', current, '...', total];
                }
            }
        
            // ✅ Hilangkan duplikat
            return pages.filter((value, index, self) => self.indexOf(value) === index);
        },

        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
                this.scrollToTop();
            }
        },

        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.scrollToTop();
            }
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.scrollToTop();
            }
        },

        scrollToTop() {
            const wrapper = document.getElementById('mail-content-wrapper');
            if (wrapper) {
                wrapper.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }
        },

        resetFilter() {
            this.filterStatus = '';
            this.filterDate = '';
            this.currentPage = 1;
        },

        init() {
            const mainContent = document.getElementById('mail-content-wrapper');
            if (mainContent) {
                mainContent.classList.remove('opacity-0', 'translate-y-6');
                mainContent.classList.add('opacity-100', 'translate-y-0');
            }

            this.$watch('filterStatus', () => { this.currentPage = 1; });
            this.$watch('filterDate', () => { this.currentPage = 1; });
        }
    }));
});
</script>
@endsection

@section('content')
<div class="flex min-h-screen bg-gray-50">

    <div class="flex-1 ml-0 md:ml-60 lg:ml-64 xl:ml-64 flex flex-col mb-10">

        <div>
            @include('layouts.components.mailsbar')
        </div>

        <div x-data="mailsData" id="mail-content-wrapper" 
            class="flex-1 p-4 md:p-6 opacity-0 translate-y-6 transition-all duration-500 ease-out">
            
            {{-- Mail Header & Filter --}}
            <div class="mb-4 md:mb-6 flex justify-between items-center relative">
                <h2 class="text-lg md:text-xl font-semibold text-gray-800">
                    Kontak Pesan Masuk
                </h2>
                
                <div class="relative" @click.outside="isFilterOpen = false">
                    <button @click="isFilterOpen = !isFilterOpen" 
                            class="p-2 rounded-full text-gray-600 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1h-1a1 1 0 00-1 1v8a1 1 0 01-1 1H7a1 1 0 01-1-1v-8a1 1 0 00-1-1H4a1 1 0 01-1-1V4z"/>
                        </svg>
                    </button>

                    <div x-show="isFilterOpen" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" 
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-64 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 p-4 z-10">
                        
                        <div class="space-y-3">
                            <h4 class="text-sm font-semibold text-gray-700">Filter Transaksi</h4>
                            
                            <select x-model="filterStatus" class="form-select block w-full pl-3 pr-10 py-2 text-sm border-gray-300 focus:ring-orange-500 focus:border-orange-500 rounded-md shadow-sm">
                                <option value="">Semua Status</option>
                                <option value="settlement">Settlement (Lunas)</option>
                                <option value="pending">Pending (Menunggu)</option>
                                <option value="failure">Failure (Gagal)</option>
                                <option value="expire">Expire (Kadaluarsa)</option>
                                <option value="cancel">Cancel (Dibatalkan)</option>
                            </select>
                            
                            <input x-model="filterDate" type="date" placeholder="Filter Tanggal Booking" class="form-input block w-full px-3 py-2 text-sm border-gray-300 focus:ring-orange-500 focus:border-orange-500 rounded-md shadow-sm">
                            
                            <button @click="resetFilter(); isFilterOpen = false" class="w-full px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md shadow-sm transition-colors">
                                Reset Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Loop Transaksi (Paginated) --}}
            <template x-for="(transaction, index) in paginatedTransactions" :key="transaction.order_id">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            
                    <!-- ✅ HEADER CARD — klik untuk buka halaman detail -->
                    <div 
                        class="p-4 md:p-6 border-b border-gray-200 cursor-pointer hover:bg-gray-50 transition"
                        @click="window.location.href = `{{ route('dashboard.transaction.show', '') }}/${transaction.id}`"
                    >
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                <div class="w-5 h-5 bg-white rounded"></div>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-800 text-sm md:text-base">
                                    Dari Customer - <span x-text="transaction.nama_lengkap"></span>
                                </h3>
                                <p class="text-xs md:text-sm text-gray-600 mt-1">
                                    No Telp. <span x-text="transaction.phone"></span>
                                </p>
            
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-green-100 text-green-800': transaction.status === 'settlement', 
                                            'bg-yellow-100 text-yellow-800': transaction.status === 'pending', 
                                            'bg-red-100 text-red-800': transaction.status !== 'settlement' && transaction.status !== 'pending'
                                        }">
                                        <span x-text="transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1)"></span>
                                    </span>
            
                                    <span class="text-xs text-gray-600">
                                        Order ID: <span x-text="transaction.order_id ?? '-'"></span>
                                    </span>
            
                                    <span class="text-xs text-gray-600">
                                        Tanggal Booking: 
                                        <span 
                                            x-text="transaction.booking_date 
                                            ? new Date(transaction.booking_date).toLocaleDateString('id-ID', 
                                                {day: '2-digit', month: '2-digit', year: 'numeric'}
                                              ) 
                                            : '-'">
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
            
                    <!-- ✅ BODY CARD – tombol Snap -->
                    <div class="p-4 md:p-6">
                        <div class="space-y-4">
            
                            <div class="bg-gray-50 rounded-lg p-3 md:p-4">
                                <p class="text-gray-700 text-sm md:text-base">
                                    Terima kasih atas transaksi Anda.
                                </p>
                            </div>
            
                            <div class="border-l-4 border-blue-400 bg-blue-50 pl-4 py-3 rounded-r-lg">
                                <p class="text-blue-700 text-sm md:text-base font-medium"> 
                                    Untuk pelunasan berikut nanti Rp. 
                                    <span x-text="Number(transaction.gross_amount).toLocaleString('id-ID')"></span>
                                </p> 
            
                                <!-- ✅ FIX: Tombol Snap sepenuhnya bebas, tidak dibungkus <a> -->
                                <template x-if="transaction.status === 'pending' && transaction.snap_token">
                                    <button type="button"
                                        @click.stop="openSnap(transaction.snap_token)"
                                        class="mt-2 px-3 py-1.5 text-sm bg-orange-500 hover:bg-orange-600 
                                               text-white rounded-lg shadow transition">
                                        Lanjutkan Pembayaran
                                    </button>
                                </template>
                            </div>
            
                        </div>
                    </div>
            
                </div>
            </template>

            
            {{-- Message Kosong --}}
            <template x-if="filteredTransactions.length === 0">
                <div class="text-center py-10 text-gray-500">
                    Tidak ada transaksi yang cocok dengan filter.
                </div>
            </template>

            {{-- UNIFIED PAGINATION CONTROLS (ALL SCREEN SIZES) --}}
            <div x-show="totalPages > 1" class="mt-8 mb-6">
                <div class="flex items-center justify-between gap-2">
                    {{-- Previous Button --}}
                    <button @click="prevPage()" 
                            :disabled="currentPage === 1"
                            :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 active:bg-gray-200'"
                            class="flex items-center justify-center px-3 md:px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span class="hidden sm:inline ml-2">Previous</span>
                    </button>

                    {{-- Page Numbers --}}
                    <div class="flex items-center gap-1">
                        <template x-for="page in pageNumbers" :key="page">
                            <div>
                                <button x-show="page !== '...'"
                                        @click="goToPage(page)"
                                        :class="page === currentPage ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-300'"
                                        class="px-3 md:px-4 py-2 text-sm font-medium border rounded-lg transition-colors"
                                        x-text="page">
                                </button>
                                <span x-show="page === '...'" class="px-1 md:px-2 text-gray-500 text-sm">...</span>
                            </div>
                        </template>
                    </div>

                    {{-- Next Button --}}
                    <button @click="nextPage()" 
                            :disabled="currentPage === totalPages"
                            :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 active:bg-gray-200'"
                            class="flex items-center justify-center px-3 md:px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg transition-colors">
                        <span class="hidden sm:inline mr-2">Next</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- POP-UP NOTIFICATION --}}
@if(session('success') || session('error') || session('warning'))
<div id="paymentNotification" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0" 
         id="notificationModal">
        
        <div class="p-8 text-center">
            @if(session('success'))
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Berhasil!</h3>
                <p class="text-gray-600 text-base">{{ session('success') }}</p>
                
            @elseif(session('error'))
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Gagal</h3>
                <p class="text-gray-600 text-base">{{ session('error') }}</p>
                
            @elseif(session('warning'))
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
                    <svg class="h-10 w-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Perhatian</h3>
                <p class="text-gray-600 text-base">{{ session('warning') }}</p>
            @endif
        </div>

        <div class="px-8 pb-8">
            <button onclick="closeNotification()" 
                class="w-full px-6 py-3 text-white font-semibold rounded-xl transition-all duration-200 transform hover:scale-105 active:scale-95
                @if(session('success')) bg-green-600 hover:bg-green-700 shadow-lg shadow-green-500/50
                @elseif(session('error')) bg-red-600 hover:bg-red-700 shadow-lg shadow-red-500/50
                @else bg-yellow-600 hover:bg-yellow-700 shadow-lg shadow-yellow-500/50 @endif">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notification = document.getElementById('paymentNotification');
    const modal = document.getElementById('notificationModal');
    
    if (notification && modal) {
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            modal.classList.remove('scale-95', 'opacity-0');
            modal.classList.add('scale-100', 'opacity-100');
        }, 100);
        setTimeout(() => {
            closeNotification();
        }, 5000);
    }
});

function closeNotification() {
    const notification = document.getElementById('paymentNotification');
    const modal = document.getElementById('notificationModal');
    
    if (notification && modal) {
        document.body.style.overflow = '';
        modal.classList.remove('scale-100', 'opacity-100');
        modal.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeNotification();
    }
});

document.getElementById('paymentNotification')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeNotification();
    }
});
</script>
@endif

@endsection