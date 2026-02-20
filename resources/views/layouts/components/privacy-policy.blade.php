{{-- Privacy Policy Modal --}}
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
                        <h2 class="text-2xl font-bold text-gray-900">Privacy Policy</h2>
                        <p class="text-gray-600 mt-1">Last updated: {{ now()->format('F d, Y') }}</p>
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
                    <!-- Your Privacy Policy Content Here -->
                    <h3>1. Information We Collect</h3>
                    <p>We collect information you provide directly to us, such as when you create an account, use our services, or communicate with us.</p>
                    
                    <h3>2. How We Use Your Information</h3>
                    <p>We use the information we collect to provide, maintain, and improve our services, to communicate with you, and to protect WorkSpace and our users.</p>
                    
                    <h3>3. Information Sharing</h3>
                    <p>We do not share your personal information with companies, organizations, or individuals outside of WorkSpace except in the following cases:</p>
                    <ul>
                        <li>With your consent</li>
                        <li>For external processing</li>
                        <li>For legal reasons</li>
                    </ul>
                    
                    <h3>4. Data Security</h3>
                    <p>We work hard to protect WorkSpace and our users from unauthorized access to or unauthorized alteration, disclosure, or destruction of information we hold.</p>
                    
                    <h3>5. Changes to This Policy</h3>
                    <p>We may change this privacy policy from time to time. We will post any privacy policy changes on this page.</p>
                    
                    <h3>6. Contact Us</h3>
                    <p>If you have any questions about this Privacy Policy, please contact us at: privacy@workspace.com</p>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="sticky bottom-0 bg-white border-t px-8 py-4">
                <div class="flex justify-end">
                    <button @click="isOpen = false" 
                            class="px-6 py-2 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors">
                        I Understand
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>