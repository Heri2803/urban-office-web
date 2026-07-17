{{-- resources/views/layouts/dashboard/deals.blade.php --}}
@extends('layouts.app')

@section('content')
<div x-data="dealsApp()" class="flex min-h-screen bg-gray-50">
    <div class="flex-1 w-full md:ml-52 lg:ml-64">
        
        {{-- Header Section --}}
        <div class="relative min-h-[16rem] sm:min-h-[18rem] lg:min-h-[20rem] bg-cover bg-center transition-all duration-1000"
            :class="isLoaded ? 'opacity-100' : 'opacity-0'"
            style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?w=1200&h=400&fit=crop')">

            <div class="absolute inset-0 bg-gradient-to-t from-orange-500/90 via-orange-400/70 to-transparent"></div>

            <div class="relative z-10 p-6 sm:p-10 py-10 sm:py-14 flex flex-col justify-center max-w-4xl">
                @auth
                <a href="{{ route('dashboard.home') }}" class="inline-flex items-center text-white/80 hover:text-white mb-6 group transition-all">
                    <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Dashboard
                </a>
                @endauth

                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-bold text-white mb-3 sm:mb-4 tracking-tight translate-y-4 opacity-0 transition-all duration-700" :class="{'translate-y-0 opacity-100': isLoaded}">
                    Exclusive Deals & Promos
                </h1>
                <p class="text-blue-100 text-sm sm:text-lg max-w-2xl mb-6 sm:mb-8 translate-y-4 opacity-0 transition-all duration-700 delay-100" :class="{'translate-y-0 opacity-100': isLoaded}">
                    Discover our latest offers to elevate your workspace experience. Claim them now before they're gone!
                </p>

                @auth
                <div class="translate-y-4 opacity-0 transition-all duration-700 delay-200" :class="{'translate-y-0 opacity-100': isLoaded}">
                    <a href="{{ route('dashboard.my-vouchers') }}" class="inline-flex items-center justify-center px-4 py-2 sm:px-6 sm:py-3 border border-transparent text-sm sm:text-base font-medium rounded-full text-orange-900 bg-white hover:bg-orange-50 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg>
                        Lihat Dompet Kupon Saya
                    </a>
                </div>
                @endauth
            </div>
        </div>

        {{-- Content Section --}}
        <div class="p-3 sm:p-10 mb-12 max-w-7xl mx-auto mt-4 relative z-20">
            
            <div x-show="loading" class="flex justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>

            <div x-show="!loading && promos.length === 0" class="bg-white rounded-2xl shadow-xl py-12 px-10 text-center" style="display: none;">
                <svg class="w-24 h-24 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">No Active Deals</h3>
                <p class="text-gray-500">Check back later for exciting new promotions!</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-8" x-show="!loading" style="display: none;">
                <template x-for="(promo, index) in promos" :key="promo.id">
                    <div class="bg-white rounded-xl sm:rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden flex flex-col group opacity-0 translate-y-8"
                         :class="{'opacity-100 translate-y-0': isLoaded, 'grayscale opacity-60 hover:!translate-y-0 hover:shadow-lg': isLoaded && !promo.can_claim && promo.promo_type_id != 1}"
                         :style="`transition-delay: ${index * 150 + 200}ms`">

                        {{-- Card Header / Image --}}
                        <div class="h-24 sm:h-48 relative overflow-hidden bg-gradient-to-br from-blue-100 to-indigo-50 flex-shrink-0">
                            <template x-if="promo.image_url">
                                <img :src="promo.image_url" alt="" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                            </template>
                            <template x-if="!promo.image_url">
                                <div class="w-full h-full flex items-center justify-center text-blue-300">
                                    <svg class="w-10 h-10 sm:w-24 sm:h-24" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"></path></svg>
                                </div>
                            </template>

                            {{-- Badge --}}
                            <div class="absolute top-1.5 right-1.5 sm:top-4 sm:right-4 bg-white/90 backdrop-blur text-blue-800 text-[10px] sm:text-xs font-bold px-1.5 py-0.5 sm:px-3 sm:py-1 rounded-full shadow-sm"
                                 x-text="promo.promo_type_id == 2 ? 'Discount' : (promo.promo_type_id == 3 ? 'Voucher' : 'Banner')">
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-3 sm:p-6 flex flex-col flex-1">
                            <div class="text-[10px] sm:text-xs font-semibold text-blue-600 tracking-wider uppercase mb-1 sm:mb-2" x-text="promo.category?.name"></div>
                            <h3 class="text-sm sm:text-xl font-bold text-gray-900 mb-1 sm:mb-2 leading-tight line-clamp-2" x-text="promo.name"></h3>
                            <p class="hidden sm:block text-gray-600 text-sm mb-6 flex-1 line-clamp-3" x-text="promo.description || 'No description available.'"></p>

                            <div class="flex items-center justify-between text-[10px] sm:text-sm text-gray-500 bg-gray-50 p-1.5 sm:p-3 rounded-lg sm:rounded-xl border border-gray-100 mb-2 sm:mb-6 gap-1">
                                <div class="flex items-center min-w-0">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="truncate" x-text="formatDate(promo.end_date)"></span>
                                </div>
                                <div class="font-mono bg-white px-1 sm:px-2 py-0.5 sm:py-1 rounded border border-gray-200 text-gray-700 truncate" x-text="promo.code"></div>
                            </div>

                            <div class="hidden sm:flex items-center text-xs text-gray-500 mb-6">
                                <svg class="w-4 h-4 mr-1 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                                <span>Berlaku untuk: <span class="font-semibold text-gray-700" x-text="formatServiceTypes(promo.service_types)"></span></span>
                            </div>

                            {{-- Action Button --}}
                            <button x-show="promo.promo_type_id == 2 || promo.promo_type_id == 3"
                                    type="button"
                                    @click="claimPromo(promo.id)"
                                    class="w-full py-1.5 sm:py-3 rounded-lg sm:rounded-xl font-bold text-white text-xs sm:text-base shadow-lg transition-all transform active:scale-95 relative z-50 block"
                                    :class="promo.can_claim ? 'bg-blue-600 hover:bg-blue-700 shadow-blue-600/30 cursor-pointer' : 'bg-gray-300 shadow-none cursor-not-allowed'"
                                    :disabled="claiming === promo.id || !promo.can_claim">
                                <span x-show="claiming === promo.id" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Claiming...
                                </span>
                                <span x-show="claiming !== promo.id" x-text="promo.can_claim ? 'Claim Offer' : (promo.is_expired ? 'Expired' : (promo.is_quota_full ? 'Kuota Habis' : 'Tidak Tersedia'))"></span>
                            </button>

                            <a x-show="promo.promo_type_id == 1"
                               href="{{ route('dashboard.booking') }}"
                               class="w-full block text-center py-1.5 sm:py-3 rounded-lg sm:rounded-xl font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors text-xs sm:text-base">
                                Book Now
                            </a>
                        </div>
                    </div>
                </template>
            </div>

        </div>
    </div>

    {{-- Claim Result Modal --}}
    <div x-show="modal.open"
         x-cloak
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] flex items-center justify-center px-4 py-6"
         @click.self="closeModal()">
        <div x-show="modal.open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden">
            <div class="px-6 pt-8 pb-6 text-center">
                <div class="mx-auto mb-4 flex items-center justify-center w-16 h-16 rounded-full"
                     :class="modal.success ? 'bg-green-100' : 'bg-red-100'">
                    <svg x-show="modal.success" class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <svg x-show="!modal.success" class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2" x-text="modal.success ? 'Berhasil!' : 'Gagal'"></h3>
                <p class="text-gray-600 text-sm" x-text="modal.message"></p>
            </div>
            <div class="px-6 pb-6">
                <button type="button" @click="closeModal()"
                        class="w-full py-3 rounded-xl font-bold text-white transition-colors"
                        :class="modal.success ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function dealsApp() {
        return {
            isLoaded: false,
            loading: true,
            promos: @json($promos),
            claiming: null,
            modal: { open: false, success: false, message: '' },

            init() {
                setTimeout(() => {
                    this.isLoaded = true;
                    this.loading = false;
                }, 100);
            },

            formatDate(dateStr) {
                if (!dateStr) return '';
                const date = new Date(dateStr);
                return date.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
            },

            formatServiceTypes(serviceTypes) {
                if (!serviceTypes || serviceTypes.length === 0 || serviceTypes.includes('all-services')) {
                    return 'Semua Layanan';
                }
                return serviceTypes
                    .map(slug => slug.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' '))
                    .join(', ');
            },

            showModal(success, message) {
                this.modal = { open: true, success, message };
            },

            closeModal() {
                this.modal.open = false;
            },

            claimPromo(promoId) {
                @guest
                    window.location.href = "{{ route('login') }}";
                    return;
                @endguest

                const promo = this.promos.find(p => p.id === promoId);
                if (this.claiming === promoId || (promo && !promo.can_claim)) {
                    return;
                }

                this.claiming = promoId;

                fetch("{{ route('dashboard.promo.claim') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ promo_id: promoId })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        // Promo yang sudah diklaim pindah ke My Vouchers, jadi hilangkan dari daftar deals.
                        this.promos = this.promos.filter(p => p.id !== promoId);
                        this.showModal(true, result.message);
                    } else {
                        this.showModal(false, result.message || 'Failed to claim promo.');
                    }
                })
                .catch(e => {
                    console.error("Fetch error:", e);
                    this.showModal(false, 'An error occurred while claiming.');
                })
                .finally(() => {
                    this.claiming = null;
                });
            }
        }
    }
</script>
@endsection
