{{-- MIDTRANS PAYMENT MODAL --}}
<div x-data="midtransPayment()" x-show="showModal" x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     x-transition.opacity
     style="display: none;">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Overlay --}}
        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50" 
             @click="closeModal"></div>
        
        {{-- Modal Content --}}
        <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
            
            {{-- Header --}}
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900" x-text="modalTitle"></h3>
                <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            {{-- Body --}}
            <div class="mb-4">
                {{-- Loading State --}}
                <div x-show="isLoading" class="text-center py-8">
                    <svg class="w-10 h-10 mx-auto animate-spin text-orange-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-3 text-gray-500">Memuat metode pembayaran...</p>
                </div>
                
                {{-- Error State --}}
                <div x-show="hasError" class="text-center py-6">
                    <div class="w-12 h-12 mx-auto bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="mt-3 text-red-600" x-text="errorMessage"></p>
                    <button @click="retryPayment" 
                            class="mt-4 px-4 py-2 bg-orange-500 text-white text-sm rounded-lg hover:bg-orange-600">
                        Coba Lagi
                    </button>
                </div>
                
                {{-- Payment Info (sebelum Snap muncul) --}}
                <div x-show="!isLoading && !hasError && !snapShown" class="space-y-3">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Total Pembayaran</p>
                        <p class="text-2xl font-bold text-gray-800" x-text="formatRupiah(totalAmount)"></p>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <p class="text-xs text-blue-700">
                            <span class="font-medium">Order ID:</span> 
                            <span x-text="orderId"></span>
                        </p>
                    </div>
                    <p class="text-xs text-gray-500 text-center">
                        Klik "Bayar Sekarang" untuk melanjutkan ke halaman pembayaran Midtrans
                    </p>
                </div>
                
                {{-- Midtrans Snap Container --}}
                <div id="snap-container" class="min-h-[300px]"></div>
            </div>
            
            {{-- Footer --}}
            <div class="flex justify-end gap-3">
                <button @click="closeModal" 
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                    Batal
                </button>
                <button @click="startPayment" 
                        x-show="!isLoading && !hasError && !snapShown"
                        class="px-6 py-2 bg-orange-500 text-white text-sm rounded-lg hover:bg-orange-600 transition">
                    Bayar Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('midtransPayment', () => ({
            showModal: false,
            isLoading: false,
            hasError: false,
            snapShown: false,
            errorMessage: '',
            modalTitle: 'Pembayaran Perpanjangan',
            orderId: '',
            totalAmount: 0,
            snapToken: '',
            finishRedirectUrl: '{{ route("customer.contracts.index") }}',
            
            formatRupiah(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount);
            },

            init() {
                window.addEventListener('open-midtrans-modal', (event) => {
                    console.log('Event received:', event.detail);
                    this.openModal(event.detail);
                });
            },
            
            openModal(data) {
                console.log('openModal received:', data);
                
                // ✅ Set semua properti secara eksplisit
                this.orderId = data.order_id || '';
                this.totalAmount = data.total_amount || 0;
                this.snapToken = data.snap_token || '';
                
                console.log('After setting:', {
                    orderId: this.orderId,
                    totalAmount: this.totalAmount,
                    snapToken: this.snapToken
                });
                
                this.showModal = true;
                this.isLoading = false;
                this.hasError = false;
                this.snapShown = false;
                
                // Bersihkan container
                const container = document.getElementById('snap-container');
                if (container) {
                    container.innerHTML = '';
                }
            },
            
            closeModal() {
                this.showModal = false;
                this.snapShown = false;
                document.getElementById('snap-container').innerHTML = '';
            },
            
            startPayment() {
                if (!this.snapToken) {
                    this.hasError = true;
                    this.errorMessage = 'Token pembayaran tidak tersedia';
                    return;
                }
                
                this.isLoading = true;
                
                try {
                    window.snap.embed(this.snapToken, {
                        embedId: 'snap-container',
                        onSuccess: (result) => {
                            console.log('Payment success:', result);
                            this.snapShown = true;
                            this.isLoading = false;
                            
                            // Redirect setelah 2 detik
                            setTimeout(() => {
                                window.location.href = this.finishRedirectUrl;
                            }, 2000);
                        },
                        onPending: (result) => {
                            console.log('Payment pending:', result);
                            this.snapShown = true;
                            this.isLoading = false;
                            
                            setTimeout(() => {
                                window.location.href = this.finishRedirectUrl;
                            }, 2000);
                        },
                        onError: (result) => {
                            console.error('Payment error:', result);
                            this.hasError = true;
                            this.isLoading = false;
                            this.errorMessage = 'Pembayaran gagal. Silakan coba lagi.';
                            document.getElementById('snap-container').innerHTML = '';
                        },
                        onClose: () => {
                            console.log('Snap popup closed');
                            if (!this.snapShown) {
                                this.closeModal();
                            }
                        }
                    });
                    
                    this.snapShown = true;
                    this.isLoading = false;
                    
                } catch (error) {
                    console.error('Snap embed error:', error);
                    this.hasError = true;
                    this.isLoading = false;
                    this.errorMessage = 'Gagal memuat metode pembayaran.';
                }
            },
            
            retryPayment() {
                this.hasError = false;
                this.startPayment();
            }
        }));
    });

    window.MidtransModal = {
        open: function(data) {
            console.log('MidtransModal.open called with:', data);
            
            // ✅ Dispatch custom event — sudah di-listen di init() modal
            window.dispatchEvent(new CustomEvent('open-midtrans-modal', {
                detail: {
                    order_id: data.order_id,
                    total_amount: data.total_amount,
                    snap_token: data.snap_token
                }
            }));
        }
    };
</script>

<style>
    [x-cloak] { display: none !important; }
</style>