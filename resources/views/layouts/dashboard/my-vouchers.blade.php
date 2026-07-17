{{-- resources/views/layouts/dashboard/my-vouchers.blade.php --}}
@extends('layouts.app')

@section('content')
<div x-data="vouchersApp()" class="flex min-h-screen bg-gray-50">
    <div class="flex-1 w-full md:ml-52 lg:ml-64">
        
        {{-- Header Section --}}
        <div class="relative min-h-[16rem] sm:min-h-[18rem] lg:min-h-[20rem] bg-cover bg-center transition-all duration-1000"
            :class="isLoaded ? 'opacity-100' : 'opacity-0'"
            style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?w=1200&h=400&fit=crop')">

            <div class="absolute inset-0 bg-gradient-to-t from-orange-500/90 via-orange-400/70 to-transparent"></div>

            <div class="relative z-10 p-6 sm:p-10 py-10 sm:py-14 flex flex-col justify-center max-w-4xl">
                <a href="{{ route('dashboard.home') }}" class="inline-flex items-center text-white/80 hover:text-white mb-6 group transition-all">
                    <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Dashboard
                </a>

                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-bold text-white mb-3 sm:mb-4 tracking-tight translate-y-4 opacity-0 transition-all duration-700" :class="{'translate-y-0 opacity-100': isLoaded}">
                    My Vouchers & Promos
                </h1>
                <p class="text-orange-50 text-sm sm:text-lg max-w-2xl translate-y-4 opacity-0 transition-all duration-700 delay-100" :class="{'translate-y-0 opacity-100': isLoaded}">
                    Your personal collection of exclusive discounts and air-dropped vouchers. Ready to be used for your next booking!
                </p>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="p-3 sm:p-10 mb-12 max-w-7xl mx-auto mt-4 relative z-20">

            <div x-show="loading" class="flex justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
            </div>

            <div x-show="!loading && vouchers.length === 0" class="bg-white rounded-2xl shadow-xl py-8 px-4 sm:py-12 sm:px-10 text-center" style="display: none;">
                <div class="w-16 h-16 sm:w-24 sm:h-24 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                    <svg class="w-8 h-8 sm:w-12 sm:h-12 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                </div>
                <h3 class="text-lg sm:text-2xl font-bold text-gray-900 mb-2 sm:mb-4">Wallet is Empty</h3>
                <p class="text-sm sm:text-base text-gray-500 mb-6 sm:mb-8 max-w-md mx-auto">You don't have any vouchers or claimed deals yet.</p>
                <a href="{{ route('deals') }}" class="inline-flex items-center justify-center px-5 py-2 sm:px-8 sm:py-3 border border-transparent text-sm sm:text-base font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-lg hover:shadow-xl">
                    Browse Deals
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-2 gap-3 sm:gap-6" x-show="!loading" style="display: none;">
                <template x-for="(voucher, index) in vouchers" :key="voucher.id">
                    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 flex flex-col sm:flex-row overflow-hidden opacity-0 translate-y-4"
                         :class="{'opacity-100 translate-y-0': isLoaded}"
                         :style="`transition-delay: ${index * 100 + 200}ms`">

                        {{-- Left side: Style based on voucher/discount --}}
                        <div class="w-full sm:w-1/3 p-2 sm:p-6 flex flex-col justify-center items-center text-center relative overflow-hidden text-white"
                             :class="voucher.promo_type_id == 3 ? 'bg-gradient-to-br from-purple-500 to-indigo-600' : 'bg-gradient-to-br from-blue-500 to-cyan-500'">

                            {{-- Decorative Circles --}}
                            <div class="hidden sm:block absolute top-0 right-0 -mt-4 -mr-4 w-20 h-20 bg-white opacity-10 rounded-full blur-xl"></div>
                            <div class="hidden sm:block absolute bottom-0 left-0 -mb-4 -ml-4 w-20 h-20 bg-white opacity-10 rounded-full blur-xl"></div>

                            {{-- Icon --}}
                            <svg x-show="voucher.promo_type_id == 3" class="w-6 h-6 sm:w-10 sm:h-10 mb-1 sm:mb-2 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg>
                            <svg x-show="voucher.promo_type_id == 2" class="w-6 h-6 sm:w-10 sm:h-10 mb-1 sm:mb-2 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>

                            <div class="text-lg sm:text-3xl font-bold mb-0.5 sm:mb-1" x-text="formatDiscount(voucher)"></div>
                            <div class="text-[9px] sm:text-xs font-medium uppercase tracking-wider opacity-90" x-text="voucher.promo_type_id == 3 ? 'Voucher' : 'Discount'"></div>
                        </div>

                        {{-- Right side: Details --}}
                        <div class="w-full sm:w-2/3 p-3 sm:p-6 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start gap-1 mb-1 sm:mb-2">
                                    <h3 class="text-sm sm:text-xl font-bold text-gray-900 leading-tight line-clamp-2" x-text="voucher.name"></h3>
                                    {{-- Status Badge --}}
                                    <span class="flex-shrink-0 px-1.5 py-0.5 sm:px-2 sm:py-1 text-[9px] sm:text-xs font-semibold rounded-full"
                                          :class="!voucher.pivot.is_claimed ? 'bg-amber-100 text-amber-700' : (voucher.pivot.is_used ? 'bg-gray-100 text-gray-500' : 'bg-green-100 text-green-700')"
                                          x-text="!voucher.pivot.is_claimed ? 'Belum Diklaim' : (voucher.pivot.is_used ? 'Used' : 'Available')"></span>
                                </div>

                                <p class="hidden sm:block text-sm text-gray-600 mb-4 line-clamp-2" x-text="voucher.description"></p>

                                <div class="space-y-0.5 sm:space-y-1 mb-2 sm:mb-4">
                                    <div class="flex items-center text-[9px] sm:text-xs text-gray-500">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="truncate">s/d <span class="font-semibold text-gray-700" x-text="formatDate(voucher.end_date)"></span></span>
                                    </div>
                                    <div x-show="voucher.min_transaction > 0" class="hidden sm:flex items-center text-xs text-gray-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Min. spend <span class="ml-1 font-semibold text-gray-700" x-text="'Rp ' + Number(voucher.min_transaction).toLocaleString('id-ID')"></span>
                                    </div>
                                    <div class="hidden sm:flex items-center text-xs text-gray-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                                        Berlaku untuk <span class="ml-1 font-semibold text-gray-700" x-text="formatServiceTypes(voucher.service_types)"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-2 sm:pt-4 mt-1 sm:mt-2">
                                <template x-if="!voucher.pivot.is_claimed">
                                    <button @click="claimVoucher(voucher.id)"
                                            type="button"
                                            class="w-full py-1.5 sm:py-2.5 rounded-lg sm:rounded-xl font-bold text-white text-xs sm:text-base bg-amber-500 hover:bg-amber-600 shadow-lg shadow-amber-500/30 transition-all active:scale-95"
                                            :disabled="claiming === voucher.id">
                                        <span x-show="claiming === voucher.id">Claiming...</span>
                                        <span x-show="claiming !== voucher.id" class="hidden sm:inline">Claim Voucher Ini</span>
                                        <span x-show="claiming !== voucher.id" class="sm:hidden">Claim</span>
                                    </button>
                                </template>
                                <div x-show="voucher.pivot.is_claimed" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-0">
                                    <div class="font-mono text-[10px] sm:text-sm bg-gray-100 px-1.5 sm:px-3 py-0.5 sm:py-1 rounded text-gray-700 font-semibold tracking-widest border border-gray-200 truncate text-center sm:text-left">
                                        <span x-text="voucher.code"></span>
                                    </div>
                                    <button @click="copyCode(voucher.code)" class="text-[10px] sm:text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center justify-center sm:justify-start transition-colors">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        Copy Code
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            
        </div>
    </div>
</div>

<script>
    function vouchersApp() {
        return {
            isLoaded: false,
            loading: true,
            vouchers: @json($vouchers),
            claiming: null,

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

            formatDiscount(voucher) {
                if (voucher.discount_type === 'percentage') {
                    return Number(voucher.discount_amount) + '%';
                }
                // Jika jutaan, persingkat jadi Jt (contoh: 100000 -> 100K)
                let amount = Number(voucher.discount_amount);
                if (amount >= 1000000) {
                    return (amount / 1000000) + ' Jt';
                } else if (amount >= 1000) {
                    return (amount / 1000) + 'K';
                }
                return 'Rp ' + amount;
            },

            copyCode(code) {
                navigator.clipboard.writeText(code).then(() => {
                    alert('Promo code copied to clipboard!');
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                });
            },

            claimVoucher(promoId) {
                if (this.claiming === promoId) {
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
                        const voucher = this.vouchers.find(v => v.id === promoId);
                        if (voucher) {
                            voucher.pivot.is_claimed = true;
                        }
                    } else {
                        alert(result.message || 'Failed to claim voucher.');
                    }
                })
                .catch(e => {
                    console.error('Fetch error:', e);
                    alert('An error occurred while claiming.');
                })
                .finally(() => {
                    this.claiming = null;
                });
            }
        }
    }
</script>
@endsection
