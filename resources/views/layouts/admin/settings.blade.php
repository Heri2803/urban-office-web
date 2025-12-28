@extends('layouts.admin')

@section('title', $title)

@section('content')
<div x-data="settings" class="container-fluid px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $title }}</h1>
        <p class="text-gray-600">Manage your profile and account settings</p>
    </div>

    <!-- Notification -->
    <div x-show="ui.notification.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg flex items-center gap-3"
         :class="{
             'bg-green-500 text-white': ui.notification.type === 'success',
             'bg-red-500 text-white': ui.notification.type === 'error',
             'bg-blue-500 text-white': ui.notification.type === 'info'
         }">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path x-show="ui.notification.type === 'success'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            <path x-show="ui.notification.type === 'error'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
        </svg>
        <span x-text="ui.notification.message"></span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column - Profile Photo -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Profile Photo</h2>
                
                <div class="flex flex-col items-center">
                    <div class="relative">
                        <img :src="photo.preview" alt="Profile" class="w-32 h-32 rounded-full object-cover border-4 border-gray-200">
                        <label for="photoUpload" class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </label>
                        <input type="file" id="photoUpload" accept="image/*" class="hidden" @change="uploadPhoto($event)">
                    </div>
                    
                    <p class="text-sm text-gray-600 mt-4 text-center">JPG or PNG. Max size 2MB</p>
                    <p class="text-xs text-gray-500 mt-1">Recommended: 400x400px</p>
                    
                    <button @click="removePhoto()" 
                            :disabled="photo.loading"
                            class="mt-4 text-sm text-red-600 hover:text-red-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!photo.loading">Remove Photo</span>
                        <span x-show="photo.loading" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Removing...
                        </span>
                    </button>
                </div>
            </div>

            <!-- Account Information -->
            <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Account Information</h2>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Account Status</p>
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Active</span>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Account Created</p>
                        <p class="text-sm text-gray-900">{{ $accountInfo['created_at'] }}</p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Last Login</p>
                        <p class="text-sm text-gray-900">{{ $accountInfo['last_login'] }}</p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Branch Assigned</p>
                        <p class="text-sm text-gray-900">{{ $accountInfo['branch'] }}</p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Work Schedule</p>
                        <p class="text-sm text-gray-900">{{ $accountInfo['schedule'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Forms -->
        <div class="lg:col-span-2">
            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h2>
                
                <form @submit.prevent="updateProfile()" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" x-model="profile.full_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                            <input type="tel" x-model="profile.phone_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" x-model="profile.email" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed" disabled>
                        <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <input type="text" value="Admin Receptionist" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed" disabled>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee ID</label>
                        <input type="text" value="EMP-2025-001" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed" disabled>
                    </div>
                    
                    <div class="flex gap-3 pt-2">
                        <button type="submit" 
                                :disabled="profile.loading"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!profile.loading">Save Changes</span>
                            <span x-show="profile.loading" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Saving...
                            </span>
                        </button>
                        <button type="button" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Change Password</h2>
                
                <form @submit.prevent="changePassword()" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Password *</label>
                        <div class="relative">
                            <input :type="getPasswordType('current')" 
                                   x-model="password.current_password" 
                                   placeholder="Enter current password" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10">
                            <button type="button" 
                                    @click="togglePassword('current')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path x-show="!password.showCurrent" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path x-show="!password.showCurrent" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    <path x-show="password.showCurrent" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password *</label>
                        <div class="relative">
                            <input :type="getPasswordType('new')" 
                                   x-model="password.new_password" 
                                   @input="password.strength = calculateStrength($event.target.value)"
                                   placeholder="Enter new password" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10">
                            <button type="button" 
                                    @click="togglePassword('new')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path x-show="!password.showNew" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path x-show="!password.showNew" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    <path x-show="password.showNew" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <!-- Password Strength Indicator -->
                        <div class="mt-2">
                            <div class="flex gap-1">
                                <template x-for="i in 4" :key="i">
                                    <div class="h-1 flex-1 rounded transition-colors duration-300"
                                         :class="getStrengthColor(i)"></div>
                                </template>
                            </div>
                            <p class="text-xs mt-1" x-text="getStrengthText()"></p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password *</label>
                        <div class="relative">
                            <input :type="getPasswordType('confirm')" 
                                   x-model="password.new_password_confirmation" 
                                   placeholder="Confirm new password" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10">
                            <button type="button" 
                                    @click="togglePassword('confirm')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path x-show="!password.showConfirm" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path x-show="!password.showConfirm" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    <path x-show="password.showConfirm" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Password Requirements -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-sm font-medium text-blue-900 mb-2">Password Requirements:</p>
                        <ul class="text-xs text-blue-800 space-y-1">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Minimum 8 characters
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                At least 1 uppercase letter
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                At least 1 number
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                At least 1 special character
                            </li>
                        </ul>
                    </div>
                    
                    <div class="flex gap-3 pt-2">
                        <button type="submit" 
                                :disabled="password.loading"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!password.loading">Change Password</span>
                            <span x-show="password.loading" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Changing...
                            </span>
                        </button>
                        <button type="button" 
                                @click="resetPasswordForm()"
                                class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Logout Confirmation Modal -->
<div x-show="ui.logoutModal" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Logout Confirmation</h3>
            <p class="text-sm text-gray-600 text-center mb-6">Are you sure you want to logout? You will need to login again to access the system.</p>
            
            <div class="flex gap-3">
                <button @click="closeLogoutModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Cancel
                </button>
                <button @click="confirmLogout()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    Yes, Logout
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('settings', () => ({
        // Profile State
        profile: {
            loading: false,
            full_name: '{{ $user->name }}',
            phone_number: '{{ $user->telephone }}',
            email: '{{ $user->email }}'
        },

        // Password State
        password: {
            loading: false,
            current_password: '',
            new_password: '',
            new_password_confirmation: '',
            showCurrent: false,
            showNew: false,
            showConfirm: false,
            strength: 0
        },

        // Photo State
        photo: {
            preview: '{{ $user->profile_photo ? Storage::url($user->profile_photo) : "https://ui-avatars.com/api/?name=" . urlencode($user->name) . "&size=200&background=3B82F6&color=fff" }}',
            loading: false
        },

        // UI State
        ui: {
            logoutModal: false,
            notification: {
                show: false,
                message: '',
                type: 'success'
            }
        },

        // Password Strength Calculator
        calculateStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            return strength;
        },

        // Get Strength Color
        getStrengthColor(index) {
            const colors = {
                1: 'bg-red-500',
                2: 'bg-yellow-500', 
                3: 'bg-blue-500',
                4: 'bg-green-500'
            };
            return index <= this.password.strength ? colors[this.password.strength] : 'bg-gray-200';
        },

        // Get Strength Text
        getStrengthText() {
            const texts = {
                0: 'Password strength',
                1: 'Weak',
                2: 'Fair', 
                3: 'Good',
                4: 'Strong'
            };
            return texts[this.password.strength];
        },

        // Toggle Password Visibility
        togglePassword(field) {
            if (field === 'current') this.password.showCurrent = !this.password.showCurrent;
            if (field === 'new') this.password.showNew = !this.password.showNew;
            if (field === 'confirm') this.password.showConfirm = !this.password.showConfirm;
        },

        // Get Password Field Type
        getPasswordType(field) {
            if (field === 'current') return this.password.showCurrent ? 'text' : 'password';
            if (field === 'new') return this.password.showNew ? 'text' : 'password';
            if (field === 'confirm') return this.password.showConfirm ? 'text' : 'password';
            return 'password';
        },

        // Handle Photo Upload
        async uploadPhoto(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validation
            if (file.size > 2 * 1024 * 1024) {
                this.showNotification('File size must be less than 2MB', 'error');
                return;
            }

            if (!file.type.match('image.*')) {
                this.showNotification('Please select a valid image file', 'error');
                return;
            }

            this.photo.loading = true;

            try {
                const formData = new FormData();
                formData.append('avatar', file);

                const response = await fetch('{{ route("admin.settings.avatar.update") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    this.photo.preview = result.avatar_url;
                    this.showNotification(result.message);
                } else {
                    throw new Error(result.message || 'Failed to upload photo');
                }
            } catch (error) {
                console.error('Photo upload error:', error);
                this.showNotification(error.message || 'Failed to upload photo', 'error');
            } finally {
                this.photo.loading = false;
                event.target.value = ''; // Reset file input
            }
        },

        // Remove Photo
        async removePhoto() {
            this.photo.loading = true;

            try {
                const response = await fetch('{{ route("admin.settings.avatar.remove") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    this.photo.preview = result.avatar_url;
                    this.showNotification(result.message);
                } else {
                    throw new Error(result.message || 'Failed to remove photo');
                }
            } catch (error) {
                console.error('Photo removal error:', error);
                this.showNotification(error.message || 'Failed to remove photo', 'error');
            } finally {
                this.photo.loading = false;
            }
        },

        // Update Profile
        async updateProfile() {
            this.profile.loading = true;

            try {
                const response = await fetch('{{ route("admin.settings.profile.update") }}', {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        full_name: this.profile.full_name,
                        phone_number: this.profile.phone_number
                    })
                });

                const result = await response.json();

                if (result.success) {
                    this.showNotification(result.message);
                    // Update local data if needed
                    if (result.user) {
                        this.profile.full_name = result.user.name;
                        this.profile.phone_number = result.user.telephone;
                    }
                } else {
                    if (result.errors) {
                        this.showNotification('Validation failed: ' + Object.values(result.errors).join(', '), 'error');
                    } else {
                        throw new Error(result.message || 'Failed to update profile');
                    }
                }
            } catch (error) {
                console.error('Profile update error:', error);
                this.showNotification(error.message || 'Failed to update profile', 'error');
            } finally {
                this.profile.loading = false;
            }
        },

        // Change Password
        async changePassword() {
            // Client-side validation
            if (this.password.new_password !== this.password.new_password_confirmation) {
                this.showNotification('New password and confirm password do not match', 'error');
                return;
            }

            this.password.loading = true;

            try {
                const response = await fetch('{{ route("admin.settings.password.update") }}', {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        current_password: this.password.current_password,
                        new_password: this.password.new_password,
                        new_password_confirmation: this.password.new_password_confirmation
                    })
                });

                const result = await response.json();

                if (result.success) {
                    this.showNotification(result.message);
                    this.resetPasswordForm();
                } else {
                    if (result.errors) {
                        const errorMessages = Object.values(result.errors).flat();
                        this.showNotification('Password change failed: ' + errorMessages.join(', '), 'error');
                    } else {
                        throw new Error(result.message || 'Failed to change password');
                    }
                }
            } catch (error) {
                console.error('Password change error:', error);
                this.showNotification(error.message || 'Failed to change password', 'error');
            } finally {
                this.password.loading = false;
            }
        },

        // Reset Password Form
        resetPasswordForm() {
            this.password.current_password = '';
            this.password.new_password = '';
            this.password.new_password_confirmation = '';
            this.password.strength = 0;
            this.password.showCurrent = false;
            this.password.showNew = false;
            this.password.showConfirm = false;
        },

        // Show Notification
        showNotification(message, type = 'success') {
            this.ui.notification = {
                show: true,
                message: message,
                type: type
            };

            setTimeout(() => {
                this.ui.notification.show = false;
            }, 4000);
        },

        // Logout Functions
        openLogoutModal() {
            this.ui.logoutModal = true;
            document.body.style.overflow = 'hidden';
        },

        closeLogoutModal() {
            this.ui.logoutModal = false;
            document.body.style.overflow = 'auto';
        },

        confirmLogout() {
            window.location.href = '{{ route("logout") }}';
        }
    }));
});
</script>
@endpush