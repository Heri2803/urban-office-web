@extends('layouts.mitrapanel')

@section('title', 'Settings')

@section('page-title', 'Pengaturan')
@section('page-subtitle', 'Kelola informasi profil mitra Anda')

@section('content')
<div class="space-y-6" x-data="settingsData()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Pengaturan Profil</h2>
            <p class="text-sm text-gray-600 mt-1">Update informasi mitra dan preferensi akun</p>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- Sidebar Menu (Desktop) --}}
        <div class="lg:w-1/5">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-6">
                <div class="p-4 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800">Menu Pengaturan</h3>
                </div>
                <nav class="p-2">
                    <button @click="activeTab = 'profile'" 
                            type="button"
                            class="w-full flex items-center px-4 py-3 rounded-lg text-sm font-medium transition"
                            :class="activeTab === 'profile' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50'">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Informasi Profil
                    </button>
                    <button @click="activeTab = 'security'" 
                            type="button"
                            class="w-full flex items-center px-4 py-3 rounded-lg text-sm font-medium transition"
                            :class="activeTab === 'security' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50'">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Keamanan
                    </button>
                    <button @click="activeTab = 'notifications'" 
                            type="button"
                            class="w-full flex items-center px-4 py-3 rounded-lg text-sm font-medium transition"
                            :class="activeTab === 'notifications' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50'">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Notifikasi
                    </button>
                </nav>
            </div>
        </div>

        {{-- Main Content Area --}}
        <div class="flex-1">  {{-- Tab: Informasi Profil --}}
            <div x-show="activeTab === 'profile'" x-transition class="space-y-6">

                {{-- Profile Photo Section --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Foto Profil</h3>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        {{-- Current Photo --}}
                        <div class="relative">
                            <template x-if="formData.photoPreview">
                                <img :src="formData.photoPreview" 
                                    alt="Profile" 
                                    class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 shadow-lg">
                            </template>
                            <template x-if="!formData.photoPreview">
                                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-4xl font-bold border-4 border-gray-200 shadow-lg">
                                    <span x-text="formData.name.charAt(0).toUpperCase()"></span>
                                </div>
                            </template>
                            
                            {{-- Online Indicator --}}
                            <span class="absolute bottom-2 right-2 w-6 h-6 bg-green-500 border-4 border-white rounded-full"></span>
                        </div>

                        {{-- Upload Section --}}
                        <div class="flex-1 text-center sm:text-left">
                            <p class="text-sm font-medium text-gray-700 mb-2">Upload foto profil baru</p>
                            <p class="text-xs text-gray-500 mb-4">Format: JPG, PNG, atau WebP (Maks. 2MB)</p>
                            
                            <div class="flex flex-col sm:flex-row gap-2">
                                <label class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition cursor-pointer">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"/>
                                        <path d="M9 13h2v5a1 1 0 11-2 0v-5z"/>
                                    </svg>
                                    Upload Foto
                                    <input type="file" 
                                            @change="handlePhotoUpload" 
                                            accept="image/jpeg,image/png,image/webp"
                                            class="hidden">
                                </label>
                                
                                <button @click="removePhoto" 
                                        x-show="formData.photoPreview"
                                        type="button"
                                        class="inline-flex items-center px-4 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Hapus Foto
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Personal Information Form --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">Informasi Personal</h3>
                    
                    <form @submit.prevent="saveProfile" class="space-y-5">
                        
                        {{-- Nama Lengkap --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                    x-model="formData.name"
                                    required
                                    placeholder="Masukkan nama lengkap"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <p class="text-xs text-gray-500 mt-1">Nama akan ditampilkan di profil dan dokumen</p>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                </div>
                                <input type="email" 
                                        x-model="formData.email"
                                        required
                                        placeholder="email@example.com"
                                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            </div>
                        </div>

                        {{-- Nomor Telepon --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                    </svg>
                                </div>
                                <input type="tel" 
                                        x-model="formData.phone"
                                        required
                                        placeholder="08xx-xxxx-xxxx"
                                        pattern="[0-9]{10,13}"
                                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Format: 10-13 digit angka</p>
                        </div>

                        {{-- Alamat --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat Lengkap <span class="text-red-500">*</span>
                            </label>
                            <textarea x-model="formData.address"
                                        required
                                        rows="4"
                                        placeholder="Masukkan alamat lengkap"
                                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Alamat untuk korespondensi dan dokumen</p>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                            <button type="submit" 
                                    class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Simpan Perubahan
                            </button>
                            <button type="button" 
                                    @click="resetForm"
                                    class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                                </svg>
                                Reset
                            </button>
                        </div>

                    </form>
                </div>

            </div>

            {{-- Tab: Keamanan --}}
            <div x-show="activeTab === 'security'" x-transition class="space-y-6">

                {{-- Change Password --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">Ubah Password</h3>
                    
                    <form @submit.prevent="changePassword" class="space-y-5">
                        
                        {{-- Current Password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Password Saat Ini <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input :type="showCurrentPassword ? 'text' : 'password'" 
                                        x-model="passwordData.currentPassword"
                                        required
                                        placeholder="Masukkan password saat ini"
                                        class="w-full px-4 py-2.5 pr-10 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <button type="button" 
                                        @click="showCurrentPassword = !showCurrentPassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg x-show="!showCurrentPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showCurrentPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- New Password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Password Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input :type="showNewPassword ? 'text' : 'password'" 
                                        x-model="passwordData.newPassword"
                                        required
                                        minlength="8"
                                        placeholder="Minimal 8 karakter"
                                        class="w-full px-4 py-2.5 pr-10 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <button type="button" 
                                        @click="showNewPassword = !showNewPassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg x-show="!showNewPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showNewPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            {{-- Password Strength Indicator --}}
                            <div class="mt-2">
                                <div class="flex items-center space-x-2">
                                    <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full transition-all duration-300"
                                                :class="{
                                                    'w-0 bg-gray-300': passwordStrength === 0,
                                                    'w-1/3 bg-red-500': passwordStrength === 1,
                                                    'w-2/3 bg-yellow-500': passwordStrength === 2,
                                                    'w-full bg-green-500': passwordStrength === 3
                                                }">
                                        </div>
                                    </div>
                                    <span class="text-xs font-medium"
                                            :class="{
                                                'text-gray-400': passwordStrength === 0,
                                                'text-red-600': passwordStrength === 1,
                                                'text-yellow-600': passwordStrength === 2,
                                                'text-green-600': passwordStrength === 3
                                            }"
                                            x-text="['', 'Lemah', 'Sedang', 'Kuat'][passwordStrength]">
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Password Baru <span class="text-red-500">*</span>
                            </label>
                            <input :type="showNewPassword ? 'text' : 'password'" 
                                    x-model="passwordData.confirmPassword"
                                    required
                                    placeholder="Ketik ulang password baru"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <p x-show="passwordData.confirmPassword && passwordData.newPassword !== passwordData.confirmPassword" 
                                class="text-xs text-red-600 mt-1">
                                Password tidak cocok
                            </p>
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-4 border-t border-gray-200">
                            <button type="submit" 
                                    @click="console.log('🖱️ Button clicked')" 
                                    :disabled="!isPasswordFormValid || passwordLoading"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                
                                {{-- Loading Spinner --}}
                                <svg x-show="passwordLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                
                                {{-- Lock Icon --}}
                                <svg x-show="!passwordLoading" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                                
                                <span x-text="passwordLoading ? 'Mengubah Password...' : 'Update Password'"></span>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Security Info --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-yellow-900 mb-1">Tips Keamanan Password</h4>
                            <ul class="text-sm text-yellow-700 space-y-1 list-disc list-inside">
                                <li>Gunakan minimal 8 karakter</li>
                                <li>Kombinasikan huruf besar, huruf kecil, angka dan simbol</li>
                                <li>Jangan gunakan informasi personal yang mudah ditebak</li>
                                <li>Ubah password secara berkala</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Tab: Notifikasi --}}
            <div x-show="activeTab === 'notifications'" x-transition class="space-y-6">

                {{-- Email Notifications --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Notifikasi Email</h3>
                    <p class="text-sm text-gray-600 mb-6">Pilih notifikasi yang ingin Anda terima via email</p>
                    
                    <div class="space-y-4">
                        <template x-for="(item, key) in notificationSettings.email" :key="key">
                            <label class="flex items-start p-4 rounded-lg hover:bg-gray-50 transition cursor-pointer">
                                <input type="checkbox" 
                                        x-model="notificationSettings.email[key].enabled"
                                        class="mt-1 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900" x-text="item.label"></p>
                                    <p class="text-xs text-gray-500 mt-0.5" x-text="item.description"></p>
                                </div>
                            </label>
                        </template>
                    </div>

                    <div class="pt-4 border-t border-gray-200 mt-6">
                        <button @click="saveNotifications" 
                                type="button"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Simpan Preferensi
                        </button>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
function settingsData() {
    return {
        activeTab: 'profile',
        isLoading: true,
        
        // Form Data
        formData: {
            name: '',
            email: '',
            phone: '',
            address: '',
            photo: null,
            photoPreview: null,
            company_name: '',
            business_type: '',
            npwp: ''
        },
        
        // Original data for reset
        originalData: {},
        
        // Password Data
        passwordData: {
            currentPassword: '',
            newPassword: '',
            confirmPassword: ''
        },
        
        // ✅ TAMBAHKAN PROPERTY YANG DIPERLUKAN
        passwordLoading: false,
        showCurrentPassword: false,
        showNewPassword: false,
        
        // Notification Settings
        notificationSettings: {
            email: {
                newTransaction: {
                    enabled: true,
                    label: 'Transaksi Baru',
                    description: 'Notifikasi saat ada transaksi baru di lokasi Anda'
                },
                monthlyReport: {
                    enabled: true,
                    label: 'Laporan Bulanan',
                    description: 'Terima laporan bulanan transaksi dan pendapatan'
                },
                taxReminder: {
                    enabled: true,
                    label: 'Pengingat Pajak',
                    description: 'Reminder untuk melaporkan pajak sebelum deadline'
                },
                promoUpdate: {
                    enabled: false,
                    label: 'Update Promo',
                    description: 'Info tentang promo baru dan performa promo yang berjalan'
                },
                systemUpdate: {
                    enabled: true,
                    label: 'Update Sistem',
                    description: 'Pemberitahuan tentang maintenance dan update sistem'
                }
            }
        },

        // ✅ TAMBAHKAN STATE UNTUK MESSAGES
        successMessage: '',
        errorMessage: '',
        
        // ✅ COMPUTED PROPERTIES YANG DIPERLUKAN
        get passwordStrength() {
            const password = this.passwordData.newPassword;
            if (!password) return 0;
            
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength++;
            
            // Complexity checks
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) strength++;
            
            return strength;
        },

        // ✅ COMPUTED UNTUK VALIDASI FORM PASSWORD
        get isPasswordFormValid() {
            return this.passwordData.currentPassword && 
                   this.passwordData.newPassword && 
                   this.passwordData.confirmPassword &&
                   this.passwordData.newPassword === this.passwordData.confirmPassword &&
                   this.passwordData.newPassword.length >= 8;
        },

        // ✅ COMPUTED UNTUK PASSWORD MATCH (OPTIONAL)
        get passwordsMatch() {
            return this.passwordData.newPassword === this.passwordData.confirmPassword;
        },
        
        // Methods
        async init() {
            await this.loadProfileData();
            this.isLoading = false;
        },

        async loadProfileData() {
            try {
                console.log('🔄 Loading profile data...');
                
                const response = await fetch('/settings/profile-data');
                const data = await response.json();
                
                if (data.success) {
                    this.formData = {
                        ...this.formData,
                        ...data.profile
                    };
                    
                    if (data.profile.photo) {
                        this.formData.photoPreview = data.profile.photo;
                    }
                    
                    this.originalData = JSON.parse(JSON.stringify(this.formData));
                    console.log('✅ Profile data loaded:', this.formData);
                } else {
                    console.error('❌ Failed to load profile data:', data.message);
                }
            } catch (error) {
                console.error('❌ Error loading profile data:', error);
            }
        },
        
        // ✅ METHOD UNTUK MESSAGES
        showSuccess(message) {
            this.successMessage = message;
            setTimeout(() => {
                this.successMessage = '';
            }, 5000);
        },

        showError(message) {
            this.errorMessage = message;
            setTimeout(() => {
                this.errorMessage = '';
            }, 5000);
        },

        clearMessages() {
            this.successMessage = '';
            this.errorMessage = '';
        },

        handlePhotoUpload(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('Format file tidak valid. Gunakan JPG, PNG, atau WebP.');
                return;
            }
            
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = (e) => {
                this.formData.photoPreview = e.target.result;
                this.formData.photo = file;
            };
            reader.readAsDataURL(file);
        },
        
        removePhoto() {
            if (confirm('Yakin ingin menghapus foto profil?')) {
                this.formData.photo = null;
                this.formData.photoPreview = null;
            }
        },
        
        async saveProfile() {
            // Validate
            if (!this.formData.name || !this.formData.email || !this.formData.phone || !this.formData.address) {
                alert('Mohon lengkapi semua field yang wajib diisi.');
                return;
            }

            // Phone validation
            const phonePattern = /^[0-9]{10,13}$/;
            if (!phonePattern.test(this.formData.phone.replace(/[-\s]/g, ''))) {
                alert('Format nomor telepon tidak valid. Harus 10-13 digit angka.');
                return;
            }

            try {
                this.isLoading = true;
                
                const formDataToSend = new FormData();
                formDataToSend.append('name', this.formData.name);
                formDataToSend.append('email', this.formData.email);
                formDataToSend.append('phone', this.formData.phone);
                formDataToSend.append('address', this.formData.address);
                formDataToSend.append('company_name', this.formData.company_name);
                formDataToSend.append('business_type', this.formData.business_type);
                formDataToSend.append('npwp', this.formData.npwp);
                
                if (this.formData.photo instanceof File) {
                    formDataToSend.append('photo', this.formData.photo);
                }

                const response = await fetch('/settings/profile-update', {
                    method: 'POST',
                    body: formDataToSend,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const result = await response.json();
                
                if (!response.ok) {
                    throw new Error(result.message || `HTTP error! status: ${response.status}`);
                }
                
                if (result.success) {
                    this.showSuccess('Profil berhasil diperbarui!');
                    
                    // Update photo preview jika ada URL baru
                    if (result.photo_url) {
                        this.formData.photoPreview = result.photo_url;
                        this.formData.photo = null;
                    }
                    
                    // Update original data
                    this.originalData = JSON.parse(JSON.stringify(this.formData));
                    
                } else {
                    this.showError('Gagal memperbarui profil: ' + (result.message || 'Terjadi kesalahan'));
                }
                
            } catch (error) {
                console.error('❌ Error saving profile:', error);
                this.showError('Terjadi kesalahan saat menyimpan profil: ' + error.message);
            } finally {
                this.isLoading = false;
            }
        },
        
        resetForm() {
            if (confirm('Yakin ingin mereset perubahan?')) {
                this.formData = JSON.parse(JSON.stringify(this.originalData));
            }
        },
        
        // ✅ METHOD changePassword YANG DIPERBAIKI
        async changePassword() {
            console.log('🔑 changePassword method dipanggil');
            
            this.clearMessages();
            
            // Validasi
            if (!this.passwordData.currentPassword) {
                this.showError('Mohon masukkan password saat ini');
                return;
            }
            
            if (!this.passwordData.newPassword) {
                this.showError('Mohon masukkan password baru');
                return;
            }
            
            if (!this.passwordData.confirmPassword) {
                this.showError('Mohon konfirmasi password baru');
                return;
            }
            
            if (this.passwordData.newPassword !== this.passwordData.confirmPassword) {
                this.showError('Password baru dan konfirmasi tidak cocok');
                return;
            }
            
            if (this.passwordData.newPassword.length < 8) {
                this.showError('Password baru minimal 8 karakter');
                return;
            }

            try {
                this.passwordLoading = true;
                console.log('🔄 Mengirim request update password...');

                const response = await fetch('/settings/password-update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        current_password: this.passwordData.currentPassword,
                        new_password: this.passwordData.newPassword,
                        new_password_confirmation: this.passwordData.confirmPassword
                    })
                });

                console.log('📨 Response status:', response.status);

                const result = await response.json();
                console.log('📨 Response data:', result);

                if (!response.ok) {
                    throw new Error(result.message || `HTTP error! status: ${response.status}`);
                }

                if (result.success) {
                    this.showSuccess(result.message || 'Password berhasil diubah!');
                    console.log('✅ Password berhasil diubah di backend');
                    
                    // Reset form
                    this.passwordData = {
                        currentPassword: '',
                        newPassword: '',
                        confirmPassword: ''
                    };
                    
                } else {
                    this.showError(result.message || 'Gagal mengubah password');
                    console.log('❌ Gagal di backend:', result.message);
                }

            } catch (error) {
                console.error('❌ Error changing password:', error);
                this.showError('Terjadi kesalahan: ' + error.message);
            } finally {
                this.passwordLoading = false;
                console.log('🏁 changePassword selesai');
            }
        },
        
        async saveNotifications() {
            try {
                this.isLoading = true;
                this.clearMessages();

                const response = await fetch('/settings/notifications-update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        notifications: this.notificationSettings
                    })
                });

                const result = await response.json();

                if (result.success) {
                    this.showSuccess('Preferensi notifikasi berhasil disimpan!');
                } else {
                    this.showError('Gagal menyimpan preferensi notifikasi.');
                }

            } catch (error) {
                console.error('❌ Error saving notifications:', error);
                this.showError('Terjadi kesalahan saat menyimpan notifikasi.');
            } finally {
                this.isLoading = false;
            }
        }
    }
}
</script>
@endpush