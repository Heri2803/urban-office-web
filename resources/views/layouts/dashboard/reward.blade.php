{{-- resources/views/layouts/dashboard/reward.blade.php --}}
@extends('layouts.app')

@section('content')
{{-- GUNAKAN HANYA 1 x-data --}}
<div x-data="rewardApp()" class="flex min-h-screen bg-gray-50">

    {{-- Main Content --}}
    <div class="flex-1 w-full md:ml-52 lg:ml-64">
        
        {{-- Header Section --}}
        <div 
            class="relative h-64 sm:h-72 md:h-80 lg:h-96 bg-cover bg-center transition-all duration-1000 overflow-hidden"
            :class="isLoaded ? 'opacity-100 scale-100' : 'opacity-0 scale-95'"
            style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2069&q=80')">
            
            <div class="absolute inset-0 bg-gradient-to-t from-orange-500 via-orange-400/90 to-orange-300/70"></div>

            <div class="relative z-10 p-4 sm:p-6 text-white h-full flex flex-col">
                <a href="{{ route('dashboard.home') }}"
                    class="inline-flex items-center mb-4 sm:mb-6 hover:bg-white/20 p-2 rounded-lg transition-all duration-700 delay-200 transform hover:scale-105 hover:shadow-lg"
                    :class="isLoaded ? 'translate-x-0 opacity-100' : '-translate-x-10 opacity-0'">
                        <span class="font-medium text-sm sm:text-base">REWARD</span>
                </a>
                
                <div class="flex-1 flex flex-col justify-center items-center text-center">
                    {{-- Main Earning Card --}}
                    <div class="inline-block bg-black/30 backdrop-blur-sm rounded-xl sm:rounded-2xl 
                                px-3 sm:px-4 lg:px-6 py-2 sm:py-3 lg:py-4 mb-8 sm:mb-4 lg:mb-12 
                                transition-all duration-1000 delay-300 transform 
                                hover:bg-black/40 hover:scale-105 
                                w-full max-w-[180px] sm:max-w-[240px] md:max-w-[280px]"
                        :class="isLoaded ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                        <div class="flex items-center justify-center space-x-2 mb-2">
                            <span class="text-sm sm:text-lg lg:text-xl animate-bounce">💰</span>
                            <span class="text-[10px] sm:text-sm font-medium opacity-90">PENDAPATAN</span>
                        </div>
                        <div class="text-sm sm:text-xl md:text-2xl lg:text-3xl font-bold break-words">
                            Rp. {{ number_format($totalGross, 0, ',', '.') }}
                        </div>
                    </div>

                    {{-- Service Cards --}}
                    <div class="flex flex-row flex-wrap justify-center gap-2 sm:gap-3 md:gap-4 px-2 sm:px-4 w-full max-w-6xl sm:mb-10 lg:mb-12">
                        @foreach(['Meeting Room', 'Virtual Office', 'Event Space', 'Coworking Space'] as $type)
                            <div class="bg-white/20 backdrop-blur-sm rounded-lg sm:rounded-xl md:rounded-2xl 
                                        px-2 sm:px-3 md:px-4 lg:px-6 py-2 sm:py-3 md:py-4 
                                        transition-all duration-1000 transform 
                                        hover:bg-white/30 hover:scale-105 hover:shadow-xl 
                                        w-[75px] sm:w-[90px] md:w-[110px] lg:w-[130px] xl:w-[150px]
                                        h-[75px] sm:h-[90px] md:h-[110px] lg:h-[130px] xl:h-[150px]
                                        flex flex-col justify-center">
                                <div class="flex flex-col items-center space-y-1 mb-1 sm:mb-2">
                                    <span class="text-xs sm:text-sm md:text-base lg:text-lg">💰</span>
                                    <span class="text-[8px] sm:text-[10px] md:text-xs lg:text-sm font-medium">{{ $type }}</span>
                                </div>
                                <div class="text-center text-[10px] sm:text-xs md:text-sm lg:text-base xl:text-lg font-bold">
                                    Rp. {{ number_format($grossByType[$type] ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- 🎁 BONUS SECTION --}}
        <div class="p-4 sm:p-6">
            
            {{-- Loading State --}}
            <template x-if="isLoading">
                <div class="text-center py-12">
                    <div class="inline-flex items-center gap-2 text-purple-600">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Memuat data bonus...</span>
                    </div>
                </div>
            </template>

            {{-- Bonus Summary Cards --}}
            <template x-if="!isLoading && bonusData">
                <div class="grid grid-cols-4 gap-1 sm:gap-2 md:gap-4 mb-8">
                    {{-- Total Bonus --}}
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg sm:rounded-xl md:rounded-2xl p-2 sm:p-3 md:p-4 lg:p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between mb-1 sm:mb-2">
                            <span class="text-lg sm:text-xl md:text-2xl lg:text-3xl">🎁</span>
                            <span class="text-[8px] sm:text-xs md:text-sm bg-white/20 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full whitespace-nowrap">Total</span>
                        </div>
                        <div class="text-xs sm:text-sm md:text-base lg:text-xl xl:text-2xl font-bold leading-tight" x-text="bonusData.total_hours + ' Jam'"></div>
                        <div class="text-[8px] sm:text-xs md:text-sm text-purple-100 mt-0.5 sm:mt-1 leading-tight">
                            <span class="block sm:inline">Nilai: Rp</span> 
                            <span x-text="formatRupiah(bonusData.total_hours * 50000)" class="block sm:inline"></span>
                        </div>
                    </div>

                    {{-- Sisa Bonus --}}
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg sm:rounded-xl md:rounded-2xl p-2 sm:p-3 md:p-4 lg:p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between mb-1 sm:mb-2">
                            <span class="text-lg sm:text-xl md:text-2xl lg:text-3xl">⏳</span>
                            <span class="text-[8px] sm:text-xs md:text-sm bg-white/20 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full whitespace-nowrap">Sisa</span>
                        </div>
                        <div class="text-xs sm:text-sm md:text-base lg:text-xl xl:text-2xl font-bold leading-tight" x-text="bonusData.total_remaining_hours + ' Jam'"></div>
                        <div class="text-[8px] sm:text-xs md:text-sm text-blue-100 mt-0.5 sm:mt-1 leading-tight" 
                            x-text="bonusData.total_used_hours + ' jam digunakan'"></div>
                    </div>

                    {{-- Bonus Aktif --}}
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg sm:rounded-xl md:rounded-2xl p-2 sm:p-3 md:p-4 lg:p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between mb-1 sm:mb-2">
                            <span class="text-lg sm:text-xl md:text-2xl lg:text-3xl">✅</span>
                            <span class="text-[8px] sm:text-xs md:text-sm bg-white/20 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full whitespace-nowrap">Aktif</span>
                        </div>
                        <div class="text-xs sm:text-sm md:text-base lg:text-xl xl:text-2xl font-bold leading-tight" x-text="bonusData.bonuses.length"></div>
                        <div class="text-[8px] sm:text-xs md:text-sm text-green-100 mt-0.5 sm:mt-1 leading-tight">Paket aktif</div>
                    </div>

                    {{-- Akan Segera Kadaluarsa --}}
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg sm:rounded-xl md:rounded-2xl p-2 sm:p-3 md:p-4 lg:p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between mb-1 sm:mb-2">
                            <span class="text-lg sm:text-xl md:text-2xl lg:text-3xl">⚠️</span>
                            <span class="text-[8px] sm:text-xs md:text-sm bg-white/20 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full whitespace-nowrap">Segera</span>
                        </div>
                        <div class="text-xs sm:text-sm md:text-base lg:text-xl xl:text-2xl font-bold leading-tight" x-text="bonusData.expiring_soon"></div>
                        <div class="text-[8px] sm:text-xs md:text-sm text-orange-100 mt-0.5 sm:mt-1 leading-tight">≤ 7 hari</div>
                    </div>
                </div>
            </template>

            {{-- ========== MONTHLY BONUS OVERVIEW SECTION ========== --}}
            <template x-if="!isLoading && monthlyOverview.length > 0">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                    <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-4">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <span>📅</span> Sisa Bonus Per Bulan
                        </h2>
                        <p class="text-purple-100 text-sm mt-1">
                            Lihat jadwal bonus Anda untuk 12 bulan ke depan
                        </p>
                    </div>
                    
                    <div class="p-4">
                        {{-- Summary Cards --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                            <div class="bg-purple-50 rounded-lg p-3">
                                <span class="text-xs text-purple-600">Total Bulan</span>
                                <p class="text-xl font-bold text-purple-700" x-text="monthlyOverview.length"></p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-3">
                                <span class="text-xs text-green-600">Sisa Jam</span>
                                <p class="text-xl font-bold text-green-700" x-text="monthlySummary.total_hours_remaining"></p>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-3">
                                <span class="text-xs text-blue-600">Bulan Aktif</span>
                                <p class="text-xl font-bold text-blue-700" x-text="monthlySummary.total_months_remaining"></p>
                            </div>
                            <div class="bg-orange-50 rounded-lg p-3">
                                <span class="text-xs text-orange-600">Akan Expired</span>
                                <p class="text-xl font-bold text-orange-700" x-text="monthlySummary.expiring_soon.length"></p>
                            </div>
                        </div>
                        
                        {{-- Loading State --}}
                        <template x-if="monthlyOverviewLoading">
                            <div class="text-center py-8">
                                <div class="inline-flex items-center gap-2 text-purple-600">
                                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Memuat data bulanan...</span>
                                </div>
                            </div>
                        </template>
                        
                        {{-- Monthly Timeline --}}
                        <template x-if="!monthlyOverviewLoading">
                            <div class="space-y-3">
                                <template x-for="(month, index) in monthlyOverview" :key="index">
                                    <div class="border rounded-xl overflow-hidden transition-all duration-300"
                                        :class="expandedMonth === index ? 'border-purple-300 shadow-md' : 'border-gray-200'">
                                        
                                        {{-- Month Header (Clickable) --}}
                                        <div @click="toggleMonth(index)" 
                                            class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50 transition">
                                            <div class="flex items-center gap-3 flex-1">
                                                <span class="text-2xl" x-text="month.is_current ? '🔵' : (month.total_remaining > 0 ? '🟢' : '⚪')"></span>
                                                <div>
                                                    <h3 class="font-semibold text-gray-800" x-text="month.month_name"></h3>
                                                    <p class="text-xs text-gray-500" x-text="`Kuota: ${month.total_quota} jam`"></p>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center gap-3">
                                                {{-- Status Badge --}}
                                                <span class="text-xs px-2 py-1 rounded-full whitespace-nowrap"
                                                    :class="getMonthStatusClass(month)"
                                                    x-text="getMonthStatusText(month)">
                                                </span>
                                                
                                                {{-- Progress Circle --}}
                                                <div class="relative w-10 h-10">
                                                    <svg class="w-10 h-10 transform -rotate-90">
                                                        <circle class="text-gray-200" stroke-width="3" stroke="currentColor" fill="transparent" r="16" cx="20" cy="20"/>
                                                        <circle class="text-purple-600" stroke-width="3" stroke="currentColor" fill="transparent" r="16" cx="20" cy="20"
                                                                :stroke-dasharray="2 * Math.PI * 16"
                                                                :stroke-dashoffset="2 * Math.PI * 16 * (1 - month.percentage_used / 100)"/>
                                                    </svg>
                                                    <span class="absolute inset-0 flex items-center justify-center text-xs font-bold"
                                                        x-text="month.percentage_used + '%'"></span>
                                                </div>
                                                
                                                {{-- Expand Icon --}}
                                                <span class="text-xl transition-transform duration-300"
                                                    :class="expandedMonth === index ? 'rotate-180' : ''">
                                                    ▼
                                                </span>
                                            </div>
                                        </div>
                                        
                                        {{-- Expanded Details --}}
                                        <div x-show="expandedMonth === index" 
                                            x-collapse
                                            class="border-t border-gray-100 bg-gray-50 p-4">
                                            
                                            {{-- Progress Bar --}}
                                            <div class="mb-4">
                                                <div class="flex justify-between text-sm mb-1">
                                                    <span class="text-gray-600">Penggunaan Bulan Ini</span>
                                                    <span class="font-semibold" 
                                                        :class="month.total_remaining <= 2 ? 'text-orange-600' : 'text-purple-600'"
                                                        x-text="`${month.total_used}/${month.total_quota} jam`">
                                                    </span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                    <div class="h-2.5 rounded-full transition-all duration-300"
                                                        :class="month.total_remaining <= 2 ? 'bg-orange-500' : 'bg-purple-600'"
                                                        :style="`width: ${month.percentage_used}%`">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            {{-- Bonus Details --}}
                                            <div class="space-y-2">
                                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Detail Bonus:</h4>
                                                <template x-for="bonus in month.bonus_details" :key="bonus.bonus_id">
                                                    <div class="bg-white rounded-lg p-3 flex justify-between items-center">
                                                        <div>
                                                            <p class="text-sm font-medium" x-text="bonus.source"></p>
                                                            <p class="text-xs text-gray-500">
                                                                Kuota: <span x-text="bonus.quota + ' jam'"></span>
                                                            </p>
                                                        </div>
                                                        <div class="text-right">
                                                            <p class="text-sm font-semibold" 
                                                            :class="bonus.remaining > 0 ? 'text-green-600' : 'text-gray-400'"
                                                            x-text="bonus.remaining + ' jam sisa'">
                                                            </p>
                                                            <p class="text-xs text-gray-500" x-text="`Terpakai: ${bonus.used} jam`"></p>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Empty State jika tidak ada data --}}
            <template x-if="!isLoading && !monthlyOverviewLoading && monthlyOverview.length === 0">
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 text-center">
                    <div class="text-5xl mb-4">📅</div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Belum Ada Data Bulanan</h3>
                    <p class="text-gray-600">Anda belum memiliki bonus aktif untuk 12 bulan ke depan</p>
                </div>
            </template>

            {{-- Active Bonuses List --}}
            <template x-if="!isLoading && bonusData?.bonuses?.length > 0">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-4">
                        <h2 class="text-xl font-bold text-orange flex items-center gap-2">
                            <span>🎁</span> Bonus Aktif Anda
                        </h2>
                    </div>
                    
                    <div class="p-4 space-y-4">
                        <template x-for="bonus in bonusData.bonuses" :key="bonus.id">
                            <div class="border border-purple-100 rounded-xl p-4 hover:shadow-md transition">
                                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-2xl">🎁</span>
                                            <h3 class="font-semibold text-lg" x-text="bonus.source"></h3>
                                            <span x-show="isExpiringSoon(bonus.valid_until)" 
                                                  class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full">
                                                ⚠️ Segera kadaluarsa
                                            </span>
                                        </div>
                                        
                                        {{-- Progress Bar dengan Inline Style --}}
                                        <div class="space-y-1">
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600" x-text="'Sisa ' + bonus.remaining_hours + ' dari ' + bonus.bonus_hours_total + ' jam'"></span>
                                                <span class="font-semibold" 
                                                    :style="getPercentageColor(bonus.remaining_hours / bonus.bonus_hours_total)" 
                                                    x-text="Math.round((bonus.remaining_hours / bonus.bonus_hours_total) * 100) + '%'">
                                                </span>
                                            </div>
                                            
                                            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                                {{-- Progress bar dengan inline style gradient --}}
                                                <div class="h-2.5 rounded-full transition-all duration-500 ease-out"
                                                    :style="{
                                                        'width': (bonus.remaining_hours / bonus.bonus_hours_total * 100) + '%',
                                                        'background': getProgressBarGradient(bonus.remaining_hours / bonus.bonus_hours_total)
                                                    }">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-2 text-xs text-gray-500">
                                            <span>Telah digunakan: </span>
                                            <span class="font-medium" x-text="bonus.bonus_hours_used + ' jam'"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="lg:text-right">
                                        <p class="text-sm text-gray-500">Berlaku hingga:</p>
                                        <p class="font-semibold" :class="isExpiringSoon(bonus.valid_until) ? 'text-red-600' : 'text-gray-800'" 
                                           x-text="bonus.valid_until"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Empty State Bonus --}}
            <template x-if="!isLoading && bonusData?.bonuses?.length === 0">
                <div class="bg-white rounded-2xl shadow-lg p-8 text-center mb-8">
                    <div class="text-7xl mb-4">🎁</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Bonus Aktif</h3>
                    <p class="text-gray-600 mb-6">Ayo booking lebih banyak untuk mendapatkan bonus spesial!</p>
                    <a href="{{ route('dashboard.booking') }}" 
                       class="inline-block bg-purple-500 text-white px-6 py-3 rounded-lg hover:bg-purple-600 transition">
                        Booking Sekarang
                    </a>
                </div>
            </template>

            {{-- CLAIM HISTORY SECTION --}}
            <div class="p-4 sm:p-6">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 transition-all duration-1000 delay-1100 transform"
                    :class="isLoaded ? 'translate-x-0 opacity-100' : '-translate-x-10 opacity-0'">
                    RIWAYAT CLAIM BONUS
                </h2>
                
                {{-- Loading State --}}
                <template x-if="isLoadingClaims && claims.length === 0">
                    <div class="text-center py-12">
                        <div class="inline-flex items-center gap-2 text-purple-600">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Memuat riwayat claim bonus...</span>
                        </div>
                    </div>
                </template>
                
                {{-- Grid 3 Kolom untuk Data Claims --}}
                <template x-if="!isLoadingClaims && claims.length > 0">
                    <div>
                        {{-- Grid dengan 3 kolom --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <template x-for="claim in claims" :key="claim.id">
                                <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-300 transform hover:shadow-xl hover:-translate-y-1 hover:scale-[1.02] h-full flex flex-col"
                                    :class="isLoaded ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'">
                                    
                                    <div class="p-4 sm:p-5 flex flex-col h-full">
                                        {{-- Header --}}
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="text-2xl">🎁</span>
                                            <span class="text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded-full">Claim Bonus</span>
                                            <span class="text-xs text-gray-500 ml-auto" x-text="formatDate(claim.created_at).substring(0, 10)"></span>
                                        </div>
                                        
                                        {{-- Location & Room --}}
                                        <div class="space-y-2 mb-3 flex-1">
                                            <div>
                                                <p class="text-xs text-gray-500">Lokasi</p>
                                                <p class="font-semibold text-sm line-clamp-1" x-text="claim.location_name || 'Tidak tersedia'"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Ruangan</p>
                                                <p class="font-semibold text-sm line-clamp-1" x-text="claim.room_name || 'Tidak tersedia'"></p>
                                            </div>
                                        </div>
                                        
                                        {{-- Durasi --}}
                                        <div class="bg-purple-50 p-3 rounded-lg flex items-center gap-2 mb-3">
                                            <span class="text-lg" x-text="getDurationIcon(claim.duration_type)"></span>
                                            <div>
                                                <p class="text-xs text-purple-600 mb-1">Durasi</p>
                                                <p class="text-base font-bold text-purple-700" x-text="claim.duration_display"></p>
                                            </div>
                                        </div>
                                        
                                        {{-- Status --}}
                                        <div class="flex justify-end mt-auto">
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                                :class="getStatusClass(claim.status)"
                                                x-text="claim.status">
                                            </span>
                                        </div>
                                    </div>
                                    
                                </div>
                            </template>
                        </div>
                        
                        {{-- PAGINATION LINKS dengan ukuran lebih besar --}}
                        <div class="mt-8 flex justify-center md:justify-end" x-show="lastPage > 1">
                            <nav class="flex items-center gap-2" aria-label="Pagination">
                                {{-- Previous button --}}
                                <button @click="goToPage(currentPage - 1)" 
                                        :disabled="currentPage === 1"
                                        class="w-10 h-10 flex items-center justify-center rounded-lg border-2 transition
                                            text-gray-700 border-gray-300 text-xl font-bold
                                            hover:bg-orange-500 hover:text-white hover:border-orange-500
                                            disabled:opacity-50 disabled:cursor-not-allowed
                                            disabled:hover:bg-transparent disabled:hover:text-gray-700 disabled:hover:border-gray-300">
                                    ‹
                                </button>
                                
                                {{-- Page numbers --}}
                                <template x-for="page in getPageNumbers()" :key="page">
                                    <template x-if="page === '...'">
                                        <span class="w-10 h-10 flex items-center justify-center text-gray-500 text-lg">...</span>
                                    </template>
                                    <template x-if="page !== '...'">
                                        <button @click="goToPage(page)"
                                                class="w-10 h-10 flex items-center justify-center rounded-lg border-2 transition font-semibold text-base"
                                                :class="currentPage === page 
                                                    ? 'bg-orange-500 text-white border-orange-500' 
                                                    : 'border-gray-300 text-gray-700 hover:bg-orange-100 hover:border-orange-300'">
                                            <span x-text="page"></span>
                                        </button>
                                    </template>
                                </template>
                                
                                {{-- Next button --}}
                                <button @click="goToPage(currentPage + 1)" 
                                        :disabled="currentPage === lastPage"
                                        class="w-10 h-10 flex items-center justify-center rounded-lg border-2 transition
                                            text-gray-700 border-gray-300 text-xl font-bold
                                            hover:bg-orange-500 hover:text-white hover:border-orange-500
                                            disabled:opacity-50 disabled:cursor-not-allowed
                                            disabled:hover:bg-transparent disabled:hover:text-gray-700 disabled:hover:border-gray-300">
                                    ›
                                </button>
                            </nav>
                        </div>
                    </div>
                </template>
                
                {{-- Empty State --}}
                <template x-if="!isLoadingClaims && claims.length === 0">
                    <div class="text-center py-12 bg-white rounded-2xl shadow-lg">
                        <div class="text-6xl mb-4">🎁</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Claim Bonus</h3>
                        <p class="text-gray-600">Anda belum melakukan claim bonus apapun</p>
                    </div>
                </template>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {

    // ============================================
    // REWARD APP COMPONENT (SEMUA DIGABUNG JADI SATU)
    // ============================================
    Alpine.data('rewardApp', () => ({
        // State untuk Bonus Aktif
        isLoaded: false,
        isLoading: true,
        bonusData: null,
        
        // State untuk Claim History
        claims: [],
        isLoadingClaims: true,
        claimsCurrentPage: 1,
        claimsLastPage: 1,
        claimsHasMorePages: false,
        monthlyOverview: [],
        monthlyOverviewLoading: false,
        monthlySummary: {
            total_hours_remaining: 0,
            total_months_remaining: 0,
            expiring_soon: []
        },
        expandedMonth: null,

        init() {
            console.log('✅ rewardApp initialized');

            setTimeout(() => {
                this.isLoaded = true;
                console.log('✅ isLoaded set to true');
            }, 200);

            // Load kedua data sekaligus
            this.loadBonusData();
            this.loadClaimHistory();
            this.loadMonthlyOverview();
        },

        // ✅ NEW: Method untuk load monthly overview
        async loadMonthlyOverview() {
            try {
                this.monthlyOverviewLoading = true;
                const response = await fetch('/api/customer/bonus/monthly/overview', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.monthlyOverview = result.data.overview;
                    this.monthlySummary = result.data.summary;
                    console.log('✅ Monthly overview loaded:', this.monthlyOverview);
                }
            } catch (error) {
                console.error('❌ Failed to load monthly overview:', error);
            } finally {
                this.monthlyOverviewLoading = false;
            }
        },

         // ✅ NEW: Toggle expand/collapse
        toggleMonth(index) {
            if (this.expandedMonth === index) {
                this.expandedMonth = null;
            } else {
                this.expandedMonth = index;
            }
        },

        // ✅ NEW: Get status badge class
        getMonthStatusClass(month) {
            if (month.is_current) return 'bg-blue-100 text-blue-600';
            if (month.total_remaining === 0) return 'bg-gray-100 text-gray-600';
            if (month.total_remaining <= 2) return 'bg-orange-100 text-orange-600';
            return 'bg-green-100 text-green-600';
        },

        // ✅ NEW: Get status text
        getMonthStatusText(month) {
            if (month.is_current) return 'Bulan Ini';
            if (month.total_remaining === 0) return 'Terpakai';
            return `${month.total_remaining} jam tersisa`;
        },

        // ========== METHOD UNTUK BONUS AKTIF ==========
        async loadBonusData() {
            try {
                console.log('📡 Loading bonus data...');
                this.isLoading = true;

                const response = await fetch('/api/customer/bonus', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                console.log('📡 Response status:', response.status);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                console.log('📡 Response data:', result);

                if (result.success) {
                    this.bonusData = result.data;

                    if (this.bonusData.bonuses && this.bonusData.bonuses.length > 0) {
                        this.bonusData.total_hours = this.bonusData.bonuses.reduce(
                            (sum, b) => sum + (b.bonus_hours_total || 0), 0
                        );
                        this.bonusData.total_used_hours = this.bonusData.bonuses.reduce(
                            (sum, b) => sum + (b.bonus_hours_used || 0), 0
                        );
                        this.bonusData.total_remaining_hours = this.bonusData.bonuses.reduce(
                            (sum, b) => sum + (b.remaining_hours || 0), 0
                        );
                        this.bonusData.expiring_soon = this.bonusData.bonuses.filter(b => {
                            return this.isExpiringSoon(b.valid_until);
                        }).length;
                    } else {
                        this.bonusData.total_hours = 0;
                        this.bonusData.total_used_hours = 0;
                        this.bonusData.total_remaining_hours = 0;
                        this.bonusData.expiring_soon = 0;
                    }

                    console.log('✅ Bonus data processed:', this.bonusData);
                }
            } catch (error) {
                console.error('❌ Failed to load bonus data:', error);
                // Set safe defaults saat error
                this.bonusData = {
                    bonuses: [],
                    total_hours: 0,
                    total_used_hours: 0,
                    total_remaining_hours: 0,
                    expiring_soon: 0
                };
            } finally {
                this.isLoading = false;
                console.log('✅ isLoading set to false');
            }
        },

        async goToPage(page) {
            if (page < 1 || page > this.lastPage || page === this.currentPage) return;
            
            this.isLoadingClaims = true;
            this.currentPage = page;
            
            try {
                const response = await fetch(`/api/customer/bonus-claims/history?page=${page}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) throw new Error('Failed to load');
                
                const result = await response.json();
                if (result.success) {
                    this.claims = result.data;
                    this.currentPage = result.meta.current_page;
                    this.lastPage = result.meta.last_page;
                }
            } catch (error) {
                console.error('Error loading page:', error);
            } finally {
                this.isLoadingClaims = false;
            }
        },

        // Helper untuk mendapatkan nomor halaman dengan ellipsis
        getPageNumbers() {
            const pages = [];
            const delta = 2; // Jumlah halaman di samping current page
            
            // Selalu tampilkan halaman pertama
            pages.push(1);
            
            // Hitung range halaman di sekitar current page
            let rangeStart = Math.max(2, this.currentPage - delta);
            let rangeEnd = Math.min(this.lastPage - 1, this.currentPage + delta);
            
            // Tambahkan ellipsis jika perlu
            if (rangeStart > 2) {
                pages.push('...');
            }
            
            // Tambahkan halaman dalam range
            for (let i = rangeStart; i <= rangeEnd; i++) {
                pages.push(i);
            }
            
            // Tambahkan ellipsis jika perlu
            if (rangeEnd < this.lastPage - 1) {
                pages.push('...');
            }
            
            // Selalu tampilkan halaman terakhir jika lebih dari 1
            if (this.lastPage > 1) {
                pages.push(this.lastPage);
            }
            
            return pages;
        },

        // ========== METHOD UNTUK CLAIM HISTORY ==========
        async loadClaimHistory(page = 1) {
            try {
                this.isLoadingClaims = true;
                console.log(`📡 Loading claim history page ${page}...`);
                
                const response = await fetch(`/api/customer/bonus-claims/history?page=${page}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('📡 Claim history response:', result);
                
                if (result.success) {
                    this.claims = result.data;
                    this.currentPage = result.meta.current_page;
                    this.lastPage = result.meta.last_page;
                }
            } catch (error) {
                console.error('❌ Failed to load claim history:', error);
                this.claims = [];
                this.currentPage = 1;
                this.lastPage = 1;
            } finally {
                this.isLoadingClaims = false;
            }
        },
        
        // Load more untuk infinite scroll
        loadMoreClaims() {
            if (this.claimsHasMorePages && !this.isLoadingClaims) {
                this.loadClaimHistory(this.claimsCurrentPage + 1);
            }
        },

        // ========== SHARED UTILITY METHODS ==========
        isExpiringSoon(validUntil) {
            if (!validUntil) return false;

            try {
                const months = {
                    'Jan': 0, 'Feb': 1, 'Mar': 2, 'Apr': 3,
                    'Mei': 4, 'Jun': 5, 'Jul': 6, 'Agu': 7,
                    'Sep': 8, 'Okt': 9, 'Nov': 10, 'Des': 11
                };

                const parts = validUntil.trim().split(/\s+/);
                if (parts.length < 3) return false;

                const day   = parseInt(parts[0]);
                const month = months[parts[1]];
                const year  = parseInt(parts[2]);

                if (isNaN(day) || month === undefined || isNaN(year)) return false;

                const expiryDate = new Date(year, month, day);
                const now        = new Date();
                now.setHours(0, 0, 0, 0);

                const diffTime = expiryDate - now;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                return diffDays <= 7 && diffDays >= 0;
            } catch (e) {
                console.error('❌ Error parsing date:', validUntil, e);
                return false;
            }
        },

        // ========== PROGRESS BAR METHODS ==========
        getPercentageColor(percentage) {
            if (percentage > 0.6) return 'color: #eab308';      // Kuning
            if (percentage > 0.3) return 'color: #f97316';      // Orange
            return 'color: #ef4444';                             // Merah
        },
        
        getProgressBarGradient(percentage) {
            if (percentage > 0.6) {
                return 'linear-gradient(90deg, #fbbf24, #eab308)';  // Kuning gradient
            } else if (percentage > 0.3) {
                return 'linear-gradient(90deg, #fb923c, #f97316)';  // Orange gradient
            } else {
                return 'linear-gradient(90deg, #f87171, #ef4444)';  // Merah gradient
            }
        },

        // ========== FORMATTING METHODS ==========
        formatRupiah(number) {
            if (!number && number !== 0) return '0';
            return new Intl.NumberFormat('id-ID').format(number);
        },
        
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        // ========== CLAIM HISTORY HELPER METHODS ==========
        getStatusClass(status) {
            switch(status) {
                case 'settlement':
                    return 'bg-green-100 text-green-700';
                case 'pending':
                    return 'bg-yellow-100 text-yellow-700';
                case 'failed':
                    return 'bg-red-100 text-red-700';
                default:
                    return 'bg-gray-100 text-gray-700';
            }
        },
        
        getDurationIcon(type) {
            switch(type) {
                case 'jam': return '⏱️';
                case 'hari': return '📅';
                case 'minggu': return '📆';
                case 'bulan': return '📅';
                case 'tahun': return '📅';
                default: return '⏳';
            }
        }
    }));

});
</script>
@endpush
@endsection