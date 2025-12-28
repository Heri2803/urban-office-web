<!-- Confirmation Modal -->
<div id="confirmModal" x-show="showConfirmModal" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full" @click.outside="closeConfirmModal">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Confirm Booking</h3>
            <p class="text-sm text-gray-600 text-center mb-6">Are you sure you want to confirm this booking?</p>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-6" x-show="selectedBooking">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Booking ID:</span>
                        <span class="font-medium text-gray-900" x-text="selectedBooking.booking_code"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Customer:</span>
                        <span class="font-medium text-gray-900" x-text="selectedBooking.customer_name"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Service:</span>
                        <span class="font-medium text-gray-900" x-text="selectedBooking.room_type === 'coworking_space' ? 'Coworking Space' : 'Event Space'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date:</span>
                        <span class="font-medium text-gray-900" x-text="formatDate(selectedBooking.booking_date)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Time:</span>
                        <span class="font-medium text-gray-900" x-text="selectedBooking.start_time"></span>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-3">
                <button @click="closeConfirmModal" 
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Cancel
                </button>
                <button @click="confirmBookingAction" 
                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                    Confirm Booking
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Confirmation Modal -->
<div id="cancelModal" x-show="showCancelModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full" @click.outside="closeCancelModal">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Cancel Confirmation</h3>
            <p class="text-sm text-gray-600 text-center mb-6">Are you sure you want to cancel this confirmation? The booking will return to waiting confirmation list.</p>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-6" x-show="selectedBooking">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Booking ID:</span>
                        <span class="font-medium text-gray-900" x-text="selectedBooking.booking_code"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Customer:</span>
                        <span class="font-medium text-gray-900" x-text="selectedBooking.customer_name"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Service:</span>
                        <span class="font-medium text-gray-900" x-text="selectedBooking.room_type === 'coworking_space' ? 'Coworking Space' : 'Event Space'"></span>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-3">
                <button @click="closeCancelModal" 
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    No, Keep It
                </button>
                <button @click="cancelConfirmationAction" 
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    Yes, Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Notification -->
<div x-show="showSuccessNotification" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform translate-y-2"
     class="fixed top-4 right-4 z-50">
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg max-w-sm">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-green-800" x-text="successMessage"></p>
                <p class="text-xs text-green-600 mt-0.5">Action completed successfully</p>
            </div>
            <button @click="showSuccessNotification = false" class="ml-4 text-green-400 hover:text-green-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Error Notification -->
<div x-show="showErrorNotification" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform translate-y-2"
     class="fixed top-4 right-4 z-50">
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg max-w-sm">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-red-800" x-text="errorMessage"></p>
                <p class="text-xs text-red-600 mt-0.5">Please try again</p>
            </div>
            <button @click="showErrorNotification = false" class="ml-4 text-red-400 hover:text-red-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
</div>