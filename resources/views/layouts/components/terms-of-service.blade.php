{{-- Terms of Service Modal --}}
<div x-data="{ isOpen: false }" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     x-show="isOpen"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape="isOpen = false">
    
    <!-- Overlay -->
    <div class="fixed inset-0 bg-black/50" @click="isOpen = false"></div>
    
    <!-- Modal Container -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <!-- Modal Content -->
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden"
             @click.stop
             x-show="isOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95">
            
            <!-- Header -->
            <div class="sticky top-0 bg-white border-b px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Terms of Service</h2>
                        <p class="text-gray-600 mt-1">Effective date: {{ now()->format('F d, Y') }}</p>
                    </div>
                    <button @click="isOpen = false" 
                            class="text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Content -->
            <div class="p-8 overflow-y-auto max-h-[calc(90vh-120px)]">
                <div class="prose prose-lg max-w-none">
                    <!-- Your Terms of Service Content Here -->
                    <h3>1. Acceptance of Terms</h3>
                    <p>By accessing and using WorkSpace services, you accept and agree to be bound by the terms and provision of this agreement.</p>
                    
                    <h3>2. Description of Service</h3>
                    <p>WorkSpace provides a virtual office platform that allows users to work remotely while maintaining professional standards and collaboration.</p>
                    
                    <h3>3. User Responsibilities</h3>
                    <p>Users are responsible for maintaining the confidentiality of their account and password and for restricting access to their computer.</p>
                    
                    <h3>4. Prohibited Uses</h3>
                    <p>You may not use the Service for any illegal or unauthorized purpose. You must not, in the use of the Service, violate any laws.</p>
                    
                    <h3>5. Modifications to Service</h3>
                    <p>WorkSpace reserves the right at any time to modify or discontinue, temporarily or permanently, the Service (or any part thereof) with or without notice.</p>
                    
                    <h3>6. Termination</h3>
                    <p>We may terminate or suspend access to our Service immediately, without prior notice or liability, for any reason whatsoever.</p>
                    
                    <h3>7. Limitation of Liability</h3>
                    <p>WorkSpace shall not be liable for any indirect, incidental, special, consequential or punitive damages resulting from your use of the Service.</p>
                    
                    <h3>8. Contact Information</h3>
                    <p>Questions about the Terms of Service should be sent to us at: legal@workspace.com</p>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="sticky bottom-0 bg-white border-t px-8 py-4">
                <div class="flex justify-end">
                    <button @click="isOpen = false" 
                            class="px-6 py-2 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors">
                        Accept Terms
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>