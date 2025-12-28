{{-- resources/views/layouts/superadmin/components/request.blade.php --}}

{{-- Request Modal --}}
<div id="requestModal"
     x-show="state.isRequestModalOpen"
     x-cloak
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">
    
    <div class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        {{-- Backdrop --}}
        <div x-show="state.isRequestModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-on:click="closeRequestModal()"
             class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
             aria-hidden="true">
        </div>
        
        {{-- Modal Content --}}
        <div x-show="state.isRequestModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6 sm:p-8 my-8 max-h-[90vh] overflow-y-auto transform transition-all">
            
            {{-- Header --}}
            <div class="flex items-center justify-between border-b pb-3 mb-6">
                <div>
                    <h3 id="modal-title" class="text-xl font-bold text-gray-800">Request Price Change</h3>
                    <p class="text-sm text-gray-500 mt-1" x-text="currentServiceLabel"></p>
                </div>
                <button x-on:click="closeRequestModal()" 
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                        aria-label="Close modal">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Branch Info --}}
            <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <strong>Branch:</strong> 
                        <span x-text="currentBranchName"></span>
                        <template x-if="state.selectedPackage">
                            <div class="mt-1">
                                <strong>Package:</strong> 
                                <span x-text="state.selectedPackage.name"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Loading State --}}
            <div x-show="state.isLoading" class="mb-6">
                <div class="flex items-center justify-center p-4">
                    <div class="animate-spin rounded-full h-6 w-6 border-t-2 border-b-2 border-blue-600 mr-3"></div>
                    <span class="text-gray-600">Loading form data...</span>
                </div>
            </div>

            {{-- Error State --}}
            <div x-show="state.formError" 
                 x-cloak
                 class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-red-800" x-text="state.formErrorMessage"></span>
                </div>
            </div>

            {{-- Form --}}
            <div x-show="!state.isLoading">
                <form id="requestForm" @submit.prevent="submitRequest()">
                    <div class="space-y-4">
                        {{-- Room Type --}}
                        <div>
                            <label for="reqRoomType" class="block text-sm font-medium text-gray-700 mb-2">
                                Room Type <span class="text-red-500">*</span>
                            </label>
                            <select id="reqRoomType" 
                                    x-model="state.formData.room_type_id"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    :class="{'border-red-300': state.formErrors.room_type_id}">
                                <option value="">Select Room Type</option>
                                <template x-for="roomType in state.roomTypes" :key="roomType.id">
                                    <option x-bind:value="roomType.id" x-text="roomType.name"></option>
                                </template>
                            </select>
                            <template x-if="state.formErrors.room_type_id">
                                <p class="mt-1 text-xs text-red-600" x-text="state.formErrors.room_type_id"></p>
                            </template>
                        </div>

                        {{-- Duration --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="reqDurationType" class="block text-sm font-medium text-gray-700 mb-2">
                                    Duration Type
                                </label>
                                <select id="reqDurationType" 
                                        x-model="state.formData.duration_type"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <template x-for="durationType in state.durationTypes" :key="durationType.value">
                                        <option x-bind:value="durationType.value" x-text="durationType.label"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label for="reqDuration" class="block text-sm font-medium text-gray-700 mb-2">
                                    Duration
                                </label>
                                <input type="number" 
                                       id="reqDuration"
                                       x-model="state.formData.duration"
                                       min="1"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                       :class="{'border-red-300': state.formErrors.duration}">
                                <template x-if="state.formErrors.duration">
                                    <p class="mt-1 text-xs text-red-600" x-text="state.formErrors.duration"></p>
                                </template>
                            </div>
                        </div>

                        {{-- Price --}}
                        <div>
                            <label for="reqPrice" class="block text-sm font-medium text-gray-700 mb-2">
                                Price (Rp) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">Rp</span>
                                </div>
                                <input type="number" 
                                       id="reqPrice"
                                       x-model="state.formData.total_price"
                                       required
                                       step="1000"
                                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                       :class="{'border-red-300': state.formErrors.total_price}"
                                       placeholder="300000">
                            </div>
                            <template x-if="state.formErrors.total_price">
                                <p class="mt-1 text-xs text-red-600" x-text="state.formErrors.total_price"></p>
                            </template>
                            <div class="mt-1 text-xs text-gray-500">
                                <template x-if="state.selectedPackage">
                                    <div class="flex justify-between">
                                        <span>Master Price: Rp <span x-text="formatCurrency(state.selectedPackage.price)"></span></span>
                                        <template x-if="state.selectedPackage.overridePrice">
                                            <span>Current Override: Rp <span x-text="formatCurrency(state.selectedPackage.overridePrice)"></span></span>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Di modal form --}}
                        <div class="mb-4">
                            <label for="coffeeBreakOption" class="block text-sm font-medium text-gray-700 mb-1">
                                Coffee Break Option *
                            </label>
                            <select id="coffeeBreakOption"
                                    x-model="state.formData.coffee_break_option"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    :class="{'border-red-300': state.formErrors.coffee_break_option}">
                                {{-- ⭐️ GUNAKAN INTEGER VALUES --}}
                                <option value="0">No Coffee Break</option>
                                <option value="1">Standard Coffee Break</option>
                                <option value="2">Premium Coffee Break</option>
                            </select>
                            <div x-show="state.formErrors.coffee_break_option" class="text-sm text-red-600 mt-1">
                                <span x-text="state.formErrors.coffee_break_option"></span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="coffeeBreakPrice" class="block text-sm font-medium text-gray-700 mb-1">
                                Coffee Break Price *
                                <span class="text-xs text-gray-500">(per person, required even if 0)</span>
                            </label>
                            <input type="number" id="coffeeBreakPrice"
                                x-model="state.formData.coffee_break_price"
                                required
                                min="0"
                                step="1000"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                :class="{'border-red-300': state.formErrors.coffee_break_price}"
                                placeholder="0">
                            <div x-show="state.formErrors.coffee_break_price" class="text-sm text-red-600 mt-1">
                                <span x-text="state.formErrors.coffee_break_price"></span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="deposit" class="block text-sm font-medium text-gray-700 mb-1">
                                Deposit Amount *
                                <span class="text-xs text-gray-500">(required, 0 if no deposit)</span>
                            </label>
                            <input type="number" id="deposit"
                                x-model="state.formData.deposit"
                                required
                                min="0"
                                step="1000"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                :class="{'border-red-300': state.formErrors.deposit}"
                                placeholder="0">
                            <div x-show="state.formErrors.deposit" class="text-sm text-red-600 mt-1">
                                <span x-text="state.formErrors.deposit"></span>
                            </div>
                        </div>

                        {{-- Reason --}}
                        <div class="mb-4">
                            <label for="reqReason" class="block text-sm font-medium text-gray-700 mb-1">
                                Reason for Change *
                                <span class="text-xs text-gray-500">(min. 20 characters)</span>
                            </label>
                            <textarea id="reqReason" 
                                    x-model="state.formData.reason" 
                                    required rows="3" 
                                    minlength="20" 
                                    maxlength="500"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"
                                    :class="{'border-red-300': state.formErrors.reason}"
                                    placeholder="Explain why this price change is needed..."
                                    @input="updateReasonCount($event)">
                            </textarea>
                            
                            {{-- Tambahkan counter --}}
                            <div class="flex justify-between mt-1">
                                <div x-show="state.formErrors.reason" class="text-sm text-red-600">
                                    <span x-text="state.formErrors.reason"></span>
                                </div>
                                <div class="text-xs text-gray-500 ml-auto">
                                    <span id="reasonCounter">0/500</span>
                                </div>
                            </div>
                        </div>

                        {{-- Effective Date --}}
                        <div>
                            <label for="reqEffectiveDate" class="block text-sm font-medium text-gray-700 mb-2">
                                Effective Date
                            </label>
                            <input type="date" 
                                   id="reqEffectiveDate"
                                   x-model="state.formData.effective_date"
                                   :min="getTodayDate()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <p class="mt-1 text-xs text-gray-500">
                                Leave empty for immediate effect
                            </p>
                        </div>

                        {{-- Hidden Fields --}}
                        <input type="hidden" 
                               x-model="state.formData.location_ids"
                               :value="state.currentLocationId">

                        {{-- Actions --}}
                        <div class="flex gap-3 pt-6 border-t">
                            <button type="button" 
                                    x-on:click="closeRequestModal()"
                                    :disabled="state.isSubmitting"
                                    class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                Cancel
                            </button>
                            <button type="submit" 
                                    :disabled="state.isSubmitting"
                                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                                <template x-if="state.isSubmitting">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </template>
                                <span x-text="state.isSubmitting ? 'Submitting...' : 'Submit Request'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- History Modal --}}
<div id="historyModal"
     x-show="state.isHistoryModalOpen"
     x-cloak
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="history-modal-title"
     role="dialog"
     aria-modal="true">
    
    <div class="flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        {{-- Backdrop --}}
        <div x-show="state.isHistoryModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-on:click="closeHistoryModal()"
             class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
             aria-hidden="true">
        </div>
        
        {{-- Modal Content --}}
        <div x-show="state.isHistoryModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative bg-white rounded-xl shadow-2xl w-full max-w-4xl p-6 sm:p-8 my-8 max-h-[90vh] overflow-y-auto transform transition-all">
            
            {{-- Header --}}
            <div class="flex items-center justify-between border-b pb-3 mb-6">
                <div>
                    <h3 id="history-modal-title" class="text-xl font-bold text-gray-800">
                        Price Override History
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Branch: <span x-text="currentBranchName" class="font-medium"></span>
                    </p>
                </div>
                <button x-on:click="closeHistoryModal()" 
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                        aria-label="Close history modal">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Loading State --}}
            <div x-show="state.isLoading" class="py-12">
                <div class="flex flex-col items-center justify-center">
                    <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-blue-600 mb-4"></div>
                    <p class="text-gray-600">Loading history data...</p>
                </div>
            </div>

            {{-- Error State --}}
            <div x-show="state.hasError" x-cloak class="py-12">
                <div class="flex flex-col items-center justify-center">
                    <svg class="w-12 h-12 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-red-600 font-medium mb-2" x-text="state.errorMessage"></p>
                    <button x-on:click="loadHistoryData()" 
                            class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-medium hover:bg-red-200 transition-colors">
                        Retry Loading
                    </button>
                </div>
            </div>

            {{-- History Content --}}
            <div x-show="!state.isLoading && !state.hasError">
                <template x-if="historyData && historyData.length > 0">
                    <div class="space-y-4">
                        <template x-for="item in historyData" :key="item.id">
                            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-3">
                                    <div>
                                        <h4 class="font-medium text-gray-900" x-text="item.package"></h4>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs text-gray-500" x-text="item.date"></span>
                                            <span class="text-xs px-2 py-0.5 rounded-full" 
                                                  :class="{
                                                    'bg-green-100 text-green-800': item.status === 'active',
                                                    'bg-yellow-100 text-yellow-800': item.status === 'pending',
                                                    'bg-red-100 text-red-800': item.status === 'rejected',
                                                    'bg-gray-100 text-gray-800': !['active','pending','rejected'].includes(item.status)
                                                  }"
                                                  x-text="item.status.charAt(0).toUpperCase() + item.status.slice(1)">
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-sm font-semibold" 
                                         :class="{
                                            'text-green-600': item.status === 'active',
                                            'text-yellow-600': item.status === 'pending',
                                            'text-red-600': item.status === 'rejected',
                                            'text-gray-600': !['active','pending','rejected'].includes(item.status)
                                         }"
                                         x-text="item.change">
                                    </div>
                                </div>
                                
                                <div class="text-sm text-gray-600 mb-2" x-text="item.reason"></div>
                                
                                <div class="flex flex-wrap gap-3 text-xs text-gray-500">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Effective: <span x-text="item.effective" class="font-medium"></span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Reviewed by: <span x-text="item.reviewedBy" class="font-medium"></span>
                                    </div>
                                    <template x-if="item.comment">
                                        <div class="w-full mt-2 pt-2 border-t border-gray-100">
                                            <div class="flex items-start gap-1">
                                                <svg class="w-3 h-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                </svg>
                                                <span class="font-medium">Comment:</span> 
                                                <span x-text="item.comment" class="ml-1"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                
                <template x-if="!historyData || historyData.length === 0">
                    <div class="py-12 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500 font-medium mb-2">No history found</p>
                        <p class="text-gray-400 text-sm">There are no price override requests for this branch yet.</p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>