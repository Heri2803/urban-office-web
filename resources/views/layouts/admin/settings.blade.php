@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Settings</h1>
        <p class="text-gray-600">Manage your profile and account settings</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column - Profile Photo -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Profile Photo</h2>
                
                <!-- Current Photo -->
                <div class="flex flex-col items-center">
                    <div class="relative">
                        <img id="profileImage" src="https://ui-avatars.com/api/?name=Admin+User&size=200&background=3B82F6&color=fff" alt="Profile" class="w-32 h-32 rounded-full object-cover border-4 border-gray-200">
                        <label for="photoUpload" class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </label>
                        <input type="file" id="photoUpload" accept="image/*" class="hidden">
                    </div>
                    
                    <p class="text-sm text-gray-600 mt-4 text-center">JPG or PNG. Max size 2MB</p>
                    <p class="text-xs text-gray-500 mt-1">Recommended: 400x400px</p>
                    
                    <button id="removePhoto" class="mt-4 text-sm text-red-600 hover:text-red-700 font-medium">
                        Remove Photo
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
                        <p class="text-sm text-gray-900">January 15, 2025</p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Last Login</p>
                        <p class="text-sm text-gray-900">October 14, 2025 - 09:30 AM</p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Branch Assigned</p>
                        <p class="text-sm text-gray-900">Jakarta Central - JKT001</p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Work Schedule</p>
                        <p class="text-sm text-gray-900">Mon-Fri, 08:00 - 17:00</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Forms -->
        <div class="lg:col-span-2">
            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h2>
                
                <form id="profileForm" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" value="Admin User" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                            <input type="tel" value="+62 812 3456 7890" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" value="admin@example.com" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed" disabled>
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
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Save Changes
                        </button>
                        <button type="reset" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Change Password</h2>
                
                <form id="passwordForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Password *</label>
                        <div class="relative">
                            <input type="password" id="currentPassword" placeholder="Enter current password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10">
                            <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 togglePassword" data-target="currentPassword">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password *</label>
                        <div class="relative">
                            <input type="password" id="newPassword" placeholder="Enter new password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10">
                            <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 togglePassword" data-target="newPassword">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <!-- Password Strength Indicator -->
                        <div class="mt-2">
                            <div class="flex gap-1">
                                <div class="h-1 flex-1 bg-gray-200 rounded" id="strength1"></div>
                                <div class="h-1 flex-1 bg-gray-200 rounded" id="strength2"></div>
                                <div class="h-1 flex-1 bg-gray-200 rounded" id="strength3"></div>
                                <div class="h-1 flex-1 bg-gray-200 rounded" id="strength4"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1" id="strengthText">Password strength</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password *</label>
                        <div class="relative">
                            <input type="password" id="confirmPassword" placeholder="Confirm new password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10">
                            <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 togglePassword" data-target="confirmPassword">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
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
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Change Password
                        </button>
                        <button type="reset" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
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
                <button id="cancelLogout" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Cancel
                </button>
                <button id="confirmLogout" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    Yes, Logout
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Notification -->
<div id="successNotif" class="hidden fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
    </svg>
    <span id="successMessage">Changes saved successfully!</span>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Profile Photo Upload
        const photoUpload = document.getElementById('photoUpload');
        const profileImage = document.getElementById('profileImage');
        const removePhoto = document.getElementById('removePhoto');

        if (photoUpload) {
            photoUpload.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        profileImage.src = e.target.result;
                        showNotification('Profile photo updated!');
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        if (removePhoto) {
            removePhoto.addEventListener('click', function() {
                profileImage.src = 'https://ui-avatars.com/api/?name=Admin+User&size=200&background=3B82F6&color=fff';
                photoUpload.value = '';
                showNotification('Profile photo removed!');
            });
        }

        // Toggle Password Visibility
        const togglePasswordBtns = document.querySelectorAll('.togglePassword');
        
        togglePasswordBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                
                if (input.type === 'password') {
                    input.type = 'text';
                } else {
                    input.type = 'password';
                }
            });
        });

        // Password Strength Indicator
        const newPassword = document.getElementById('newPassword');
        
        if (newPassword) {
            newPassword.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                
                if (password.length >= 8) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                
                // Reset all
                for (let i = 1; i <= 4; i++) {
                    document.getElementById('strength' + i).className = 'h-1 flex-1 bg-gray-200 rounded';
                }
                
                // Apply strength colors
                const colors = ['', 'bg-red-500', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'];
                const texts = ['', 'Weak', 'Fair', 'Good', 'Strong'];
                
                for (let i = 1; i <= strength; i++) {
                    document.getElementById('strength' + i).className = 'h-1 flex-1 rounded ' + colors[strength];
                }
                
                document.getElementById('strengthText').textContent = strength > 0 ? texts[strength] : 'Password strength';
            });
        }

        // Profile Form Submit
        const profileForm = document.getElementById('profileForm');
        
        if (profileForm) {
            profileForm.addEventListener('submit', function(e) {
                e.preventDefault();
                showNotification('Profile updated successfully!');
            });
        }

        // Password Form Submit
        const passwordForm = document.getElementById('passwordForm');
        
        if (passwordForm) {
            passwordForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const newPass = document.getElementById('newPassword').value;
                const confirmPass = document.getElementById('confirmPassword').value;
                
                if (newPass !== confirmPass) {
                    alert('New password and confirm password do not match!');
                    return;
                }
                
                showNotification('Password changed successfully!');
                passwordForm.reset();
                
                // Reset strength indicator
                for (let i = 1; i <= 4; i++) {
                    document.getElementById('strength' + i).className = 'h-1 flex-1 bg-gray-200 rounded';
                }
                document.getElementById('strengthText').textContent = 'Password strength';
            });
        }

        // Logout Modal
        const logoutBtn = document.getElementById('logoutBtn');
        const logoutModal = document.getElementById('logoutModal');
        const cancelLogout = document.getElementById('cancelLogout');
        const confirmLogout = document.getElementById('confirmLogout');

        if (logoutBtn) {
            logoutBtn.addEventListener('click', function() {
                logoutModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
        }

        if (cancelLogout) {
            cancelLogout.addEventListener('click', function() {
                logoutModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            });
        }

        if (confirmLogout) {
            confirmLogout.addEventListener('click', function() {
                // Redirect to logout route
                window.location.href = '/logout';
            });
        }

        // Close modal on backdrop click
        if (logoutModal) {
            logoutModal.addEventListener('click', function(e) {
                if (e.target === logoutModal) {
                    logoutModal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            });
        }

        // Success Notification Function
        function showNotification(message) {
            const notif = document.getElementById('successNotif');
            const messageEl = document.getElementById('successMessage');
            
            messageEl.textContent = message;
            notif.classList.remove('hidden');
            
            setTimeout(function() {
                notif.classList.add('hidden');
            }, 3000);
        }
    });
</script>
@endpush