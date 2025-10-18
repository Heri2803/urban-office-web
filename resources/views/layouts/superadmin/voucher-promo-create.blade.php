@extends('layouts.superadmin')

@section('content')
<main x-data="createVoucher()" x-init="init()" class="p-4 sm:p-6 lg:p-10 space-y-6 bg-gray-50 min-h-screen">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
        <div>
            <div class="flex items-center space-x-2 mb-2">
                <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Active Vouchers
                </a>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800">🎟️ Create New Voucher</h1>
            <p class="text-sm text-gray-500 mt-1">Generate promotional vouchers for customers</p>
        </div>
    </div>

    {{-- Step Progress Indicator --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 sm:p-6">
        <div class="flex items-center justify-between relative">
            {{-- Step 1 --}}
            <div class="flex flex-col items-center flex-1 relative z-10">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all"
                     :class="currentStep >= 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400'">
                    <span x-show="currentStep > 1">✓</span>
                    <span x-show="currentStep <= 1">1</span>
                </div>
                <p class="text-xs mt-2 text-center hidden sm:block" :class="currentStep >= 1 ? 'text-indigo-600 font-medium' : 'text-gray-500'">Basic Info</p>
            </div>
            
            {{-- Progress Line 1 --}}
            <div class="flex-1 h-1 -mx-2 relative" style="top: -20px;">
                <div class="h-full bg-gray-200"></div>
                <div class="h-full bg-indigo-600 transition-all" :style="'width: ' + (currentStep > 1 ? '100%' : '0%')"></div>
            </div>

            {{-- Step 2 --}}
            <div class="flex flex-col items-center flex-1 relative z-10">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all"
                     :class="currentStep >= 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400'">
                    <span x-show="currentStep > 2">✓</span>
                    <span x-show="currentStep <= 2">2</span>
                </div>
                <p class="text-xs mt-2 text-center hidden sm:block" :class="currentStep >= 2 ? 'text-indigo-600 font-medium' : 'text-gray-500'">Discount</p>
            </div>

            {{-- Progress Line 2 --}}
            <div class="flex-1 h-1 -mx-2 relative" style="top: -20px;">
                <div class="h-full bg-gray-200"></div>
                <div class="h-full bg-indigo-600 transition-all" :style="'width: ' + (currentStep > 2 ? '100%' : '0%')"></div>
            </div>

            {{-- Step 3 --}}
            <div class="flex flex-col items-center flex-1 relative z-10">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all"
                     :class="currentStep >= 3 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400'">
                    <span x-show="currentStep > 3">✓</span>
                    <span x-show="currentStep <= 3">3</span>
                </div>
                <p class="text-xs mt-2 text-center hidden sm:block" :class="currentStep >= 3 ? 'text-indigo-600 font-medium' : 'text-gray-500'">Usage Rules</p>
            </div>

            {{-- Progress Line 3 --}}
            <div class="flex-1 h-1 -mx-2 relative" style="top: -20px;">
                <div class="h-full bg-gray-200"></div>
                <div class="h-full bg-indigo-600 transition-all" :style="'width: ' + (currentStep > 3 ? '100%' : '0%')"></div>
            </div>

            {{-- Step 4 --}}
            <div class="flex flex-col items-center flex-1 relative z-10">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all"
                     :class="currentStep >= 4 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400'">
                    4
                </div>
                <p class="text-xs mt-2 text-center hidden sm:block" :class="currentStep >= 4 ? 'text-indigo-600 font-medium' : 'text-gray-500'">Preview</p>
            </div>
        </div>
    </div>

    {{-- Form Container --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-4 sm:p-6 lg:p-8">
        
        {{-- STEP 1: Basic Information --}}
        <div x-show="currentStep === 1" x-transition class="space-y-6">
            <h2 class="text-xl font-bold text-gray-800 border-b pb-3">Step 1: Basic Information</h2>

            {{-- Voucher Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Voucher Type *</label>
                <div class="space-y-3">
                    <label class="flex items-start p-4 border rounded-lg cursor-pointer transition-colors hover:bg-gray-50"
                           :class="formData.voucherType === 'single' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300'">
                        <input type="radio" x-model="formData.voucherType" value="single" class="mt-1">
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">Single Code</p>
                            <p class="text-sm text-gray-500">One code that can be used by multiple customers</p>
                        </div>
                    </label>
                    <label class="flex items-start p-4 border rounded-lg cursor-pointer transition-colors hover:bg-gray-50"
                           :class="formData.voucherType === 'unique' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300'">
                        <input type="radio" x-model="formData.voucherType" value="unique" class="mt-1">
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">Unique Codes</p>
                            <p class="text-sm text-gray-500">Generate multiple unique codes for distribution</p>
                        </div>
                    </label>
                    <label class="flex items-start p-4 border rounded-lg cursor-pointer transition-colors hover:bg-gray-50"
                           :class="formData.voucherType === 'auto' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300'">
                        <input type="radio" x-model="formData.voucherType" value="auto" class="mt-1">
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">Auto-Generate</p>
                            <p class="text-sm text-gray-500">System automatically creates codes on demand</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Voucher Code (Single) --}}
            <div x-show="formData.voucherType === 'single'">
                <label class="block text-sm font-medium text-gray-700 mb-2">Voucher Code *</label>
                <div class="flex space-x-2">
                    <input type="text" x-model="formData.voucherCode" 
                           @input="formData.voucherCode = formData.voucherCode.toUpperCase()"
                           placeholder="WELCOME2025" 
                           class="flex-1 rounded-lg border-gray-300 uppercase text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <button @click="generateRandomCode()" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 whitespace-nowrap">
                        🎲 Generate
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">⚠️ Code must be uppercase, alphanumeric only (no spaces or special characters)</p>
            </div>

            {{-- Number of Codes (Unique) --}}
            <div x-show="formData.voucherType === 'unique'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Number of Codes *</label>
                    <input type="number" x-model="formData.numberOfCodes" min="1" max="10000"
                           placeholder="100" 
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Max: 10,000 codes</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Code Pattern</label>
                    <select x-model="formData.codePattern" class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="random">Random (XK7P9M2Q)</option>
                        <option value="prefix">Prefix-based (PROMO-001)</option>
                        <option value="suffix">Suffix-based (001-DISC)</option>
                    </select>
                </div>
            </div>

            {{-- Voucher Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Voucher Name/Title * <span class="text-gray-400 text-xs">(Internal reference)</span></label>
                <input type="text" x-model="formData.voucherName" 
                       placeholder="Welcome Promo" 
                       class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea x-model="formData.description" rows="3"
                          placeholder="New customer welcome discount for first booking..." 
                          class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>

            {{-- Navigation --}}
            <div class="flex justify-end pt-4 border-t">
                <button @click="nextStep()" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 flex items-center">
                    Next: Discount Settings
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- STEP 2: Discount Settings --}}
        <div x-show="currentStep === 2" x-transition class="space-y-6">
            <h2 class="text-xl font-bold text-gray-800 border-b pb-3">Step 2: Discount Settings</h2>

            {{-- Discount Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Discount Type *</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="flex flex-col items-center p-4 border rounded-lg cursor-pointer transition-colors hover:bg-gray-50"
                           :class="formData.discountType === 'percentage' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300'">
                        <input type="radio" x-model="formData.discountType" value="percentage" class="mb-2">
                        <span class="text-2xl mb-1">📊</span>
                        <span class="font-medium text-sm">Percentage</span>
                        <span class="text-xs text-gray-500 text-center mt-1">% discount</span>
                    </label>
                    <label class="flex flex-col items-center p-4 border rounded-lg cursor-pointer transition-colors hover:bg-gray-50"
                           :class="formData.discountType === 'fixed' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300'">
                        <input type="radio" x-model="formData.discountType" value="fixed" class="mb-2">
                        <span class="text-2xl mb-1">💰</span>
                        <span class="font-medium text-sm">Fixed Amount</span>
                        <span class="text-xs text-gray-500 text-center mt-1">Rp discount</span>
                    </label>
                    <label class="flex flex-col items-center p-4 border rounded-lg cursor-pointer transition-colors hover:bg-gray-50"
                           :class="formData.discountType === 'free' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300'">
                        <input type="radio" x-model="formData.discountType" value="free" class="mb-2">
                        <span class="text-2xl mb-1">🎁</span>
                        <span class="font-medium text-sm">Free Service</span>
                        <span class="text-xs text-gray-500 text-center mt-1">100% off</span>
                    </label>
                </div>
            </div>

            {{-- Discount Value --}}
            <div x-show="formData.discountType !== 'free'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Discount Value *</label>
                    <div class="relative">
                        <input type="number" x-model="formData.discountValue" min="1"
                               :max="formData.discountType === 'percentage' ? 100 : 999999999"
                               :placeholder="formData.discountType === 'percentage' ? '20' : '50000'" 
                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 pr-12">
                        <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm font-medium"
                              x-text="formData.discountType === 'percentage' ? '%' : 'Rp'">
                        </span>
                    </div>
                </div>

                {{-- Max Discount (Percentage only) --}}
                <div x-show="formData.discountType === 'percentage'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Discount Amount <span class="text-gray-400 text-xs">(Optional)</span></label>
                    <input type="number" x-model="formData.maxDiscountAmount" min="0"
                           placeholder="100000" 
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Leave blank for no limit</p>
                </div>
            </div>

            {{-- Minimum Transaction --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Transaction Amount <span class="text-gray-400 text-xs">(Optional)</span></label>
                <input type="number" x-model="formData.minTransaction" min="0"
                       placeholder="200000" 
                       class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <p class="text-xs text-gray-500 mt-1">Minimum booking amount required to use this voucher</p>
            </div>

            {{-- Applicable Services --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Applicable Services *</label>
                <div class="space-y-2">
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" x-model="formData.allServices" @change="toggleAllServices()" class="rounded">
                        <span class="ml-3 font-medium text-gray-900">All Services</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 pl-4">
                        <template x-for="service in serviceTypes" :key="service">
                            <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50"
                                   :class="formData.selectedServices.includes(service) ? 'bg-indigo-50 border-indigo-300' : ''">
                                <input type="checkbox" :value="service" x-model="formData.selectedServices" 
                                       :disabled="formData.allServices" class="rounded">
                                <span class="ml-2 text-sm" x-text="service"></span>
                            </label>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Special: Free Meeting Room 1 Hour --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <label class="flex items-start">
                    <input type="checkbox" x-model="formData.freeMeetingRoom1Hour" class="mt-1 rounded">
                    <div class="ml-3">
                        <p class="font-medium text-gray-900">🎁 Special: Free Meeting Room 1 Hour</p>
                        <p class="text-sm text-gray-600 mt-1">100% discount for Meeting Room bookings, strictly limited to 1 hour duration only. This setting overrides discount value above for Meeting Room service.</p>
                    </div>
                </label>
            </div>

            {{-- Navigation --}}
            <div class="flex justify-between pt-4 border-t">
                <button @click="prevStep()" 
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Previous
                </button>
                <button @click="nextStep()" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 flex items-center">
                    Next: Usage Rules
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- STEP 3: Usage Rules --}}
        <div x-show="currentStep === 3" x-transition class="space-y-6">
            <h2 class="text-xl font-bold text-gray-800 border-b pb-3">Step 3: Usage Rules & Restrictions</h2>

            {{-- Validity Period --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Validity Period *</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Start Date & Time</label>
                        <div class="flex space-x-2">
                            <input type="date" x-model="formData.startDate" 
                                   class="flex-1 rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <input type="time" x-model="formData.startTime" 
                                   class="w-28 rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">End Date & Time</label>
                        <div class="flex space-x-2">
                            <input type="date" x-model="formData.endDate" 
                                   class="flex-1 rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <input type="time" x-model="formData.endTime" 
                                   class="w-28 rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Usage Limit --}}
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="font-medium text-gray-900 mb-4">Usage Limit</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Usage Limit</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" x-model="formData.unlimitedUsage" value="true" class="mr-2">
                                <span class="text-sm">Unlimited redemptions</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" x-model="formData.unlimitedUsage" value="false" class="mr-2">
                                <span class="text-sm">Limited to</span>
                                <input type="number" x-model="formData.totalUsageLimit" min="1"
                                       :disabled="formData.unlimitedUsage === 'true'"
                                       placeholder="1000"
                                       class="ml-2 w-32 rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="ml-2 text-sm">total redemptions</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Per User Limit</label>
                        <div class="flex items-center">
                            <input type="number" x-model="formData.perUserLimit" min="1" max="100"
                                   placeholder="1"
                                   class="w-32 rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="ml-2 text-sm text-gray-600">time(s) per customer</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Branch & Mitra Availability --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                {{-- Branch --}}
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 mb-3">Branch Availability</h3>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" x-model="formData.allBranches" @change="toggleAllBranches()" class="rounded">
                            <span class="ml-2 font-medium text-sm">All Branches (Global)</span>
                        </label>
                        <div class="pl-6 space-y-1">
                            <template x-for="branch in branches" :key="branch">
                                <label class="flex items-center text-sm">
                                    <input type="checkbox" :value="branch" x-model="formData.selectedBranches"
                                           :disabled="formData.allBranches" class="rounded">
                                    <span class="ml-2" x-text="branch"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Mitra --}}
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 mb-3">Mitra Availability</h3>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" x-model="formData.allMitra" @change="toggleAllMitra()" class="rounded">
                            <span class="ml-2 font-medium text-sm">All Mitra Partners</span>
                        </label>
                        <div class="pl-6 space-y-1">
                            <template x-for="mitra in mitras" :key="mitra">
                                <label class="flex items-center text-sm">
                                    <input type="checkbox" :value="mitra" x-model="formData.selectedMitra"
                                           :disabled="formData.allMitra" class="rounded">
                                    <span class="ml-2" x-text="mitra"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Additional Settings --}}
            <div class="space-y-3">
                <label class="flex items-start p-3 border rounded-lg">
                    <input type="checkbox" x-model="formData.combinable" class="mt-1 rounded">
                    <div class="ml-3">
                        <span class="font-medium text-sm">Combinable with Other Discounts</span>
                        <p class="text-xs text-gray-500">Allow this voucher to be used together with other promotions</p>
                    </div>
                </label>
            </div>

            {{-- Customer Eligibility --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Customer Eligibility *</label>
                <div class="space-y-2">
                    <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                           :class="formData.customerEligibility === 'all' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300'">
                        <input type="radio" x-model="formData.customerEligibility" value="all" class="mt-1">
                        <div class="ml-3">
                            <span class="font-medium text-sm">All Customers</span>
                            <p class="text-xs text-gray-500">Available for everyone</p>
                        </div>
                    </label>
                    <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                           :class="formData.customerEligibility === 'new' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300'">
                        <input type="radio" x-model="formData.customerEligibility" value="new" class="mt-1">
                        <div class="ml-3">
                            <span class="font-medium text-sm">New Customers Only</span>
                            <p class="text-xs text-gray-500">First-time bookers only</p>
                        </div>
                    </label>
                    <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                           :class="formData.customerEligibility === 'returning' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300'">
                        <input type="radio" x-model="formData.customerEligibility" value="returning" class="mt-1">
                        <div class="ml-3">
                            <span class="font-medium text-sm">Returning Customers Only</span>
                            <p class="text-xs text-gray-500">Customers with previous bookings</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Booking Source --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Booking Source Restriction</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <label class="flex items-center p-3 border rounded-lg">
                        <input type="checkbox" x-model="formData.allowOnline" class="rounded">
                        <span class="ml-2 text-sm">Online Booking</span>
                    </label>
                    <label class="flex items-center p-3 border rounded-lg">
                        <input type="checkbox" x-model="formData.allowManual" class="rounded">
                        <span class="ml-2 text-sm">Manual (Admin)</span>
                    </label>
                    <label class="flex items-center p-3 border rounded-lg">
                        <input type="checkbox" x-model="formData.allowWalkin" class="rounded">
                        <span class="ml-2 text-sm">Walk-in Only</span>
                    </label>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="flex justify-between pt-4 border-t">
                <button @click="prevStep()" 
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Previous
                </button>
                <button @click="nextStep()" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 flex items-center">
                    Next: Preview
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- STEP 4: Preview & Generate --}}
        <div x-show="currentStep === 4" x-transition class="space-y-6">
            <h2 class="text-xl font-bold text-gray-800 border-b pb-3">Step 4: Preview & Generate</h2>

            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-6 border-2 border-indigo-200">
                <h3 class="text-lg font-bold text-indigo-900 mb-4">📋 Voucher Summary Preview</h3>
                
                <div class="bg-white rounded-lg p-4 space-y-4">
                    {{-- Voucher Code Display --}}
                    <div class="text-center py-4 border-b">
                        <p class="text-sm text-gray-500 mb-2">Voucher Code</p>
                        <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg">
                            <span class="text-2xl sm:text-3xl font-bold text-white tracking-wider" 
                                  x-text="formData.voucherCode || 'AUTO-GENERATED'">
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2" x-show="formData.voucherType === 'unique'">
                            + <span x-text="formData.numberOfCodes"></span> unique codes will be generated
                        </p>
                    </div>

                    {{-- Basic Info --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Voucher Name</p>
                            <p class="font-medium" x-text="formData.voucherName || '-'"></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Type</p>
                            <p class="font-medium capitalize" x-text="formData.voucherType + ' Code'"></p>
                        </div>
                    </div>

                    {{-- Discount Info --}}
                    <div class="border-t pt-4">
                        <p class="font-semibold text-gray-700 mb-2">💰 Discount Details</p>
                        <div class="bg-green-50 rounded-lg p-3 space-y-1 text-sm">
                            <p x-show="formData.discountType === 'percentage'">
                                <span class="font-bold text-green-700 text-xl" x-text="formData.discountValue + '%'"></span>
                                <span class="text-gray-600"> discount</span>
                                <span x-show="formData.maxDiscountAmount" class="text-gray-600">
                                    (Max Rp <span x-text="parseInt(formData.maxDiscountAmount).toLocaleString('id-ID')"></span>)
                                </span>
                            </p>
                            <p x-show="formData.discountType === 'fixed'">
                                <span class="font-bold text-green-700 text-xl">Rp <span x-text="parseInt(formData.discountValue).toLocaleString('id-ID')"></span></span>
                                <span class="text-gray-600"> discount</span>
                            </p>
                            <p x-show="formData.discountType === 'free'">
                                <span class="font-bold text-green-700 text-xl">100% OFF</span>
                                <span class="text-gray-600"> - Free Service</span>
                            </p>
                            <p x-show="formData.freeMeetingRoom1Hour" class="text-orange-600 font-medium">
                                🎁 Special: Free Meeting Room 1 Hour
                            </p>
                            <p x-show="formData.minTransaction" class="text-gray-600">
                                Min. Transaction: Rp <span x-text="parseInt(formData.minTransaction).toLocaleString('id-ID')"></span>
                            </p>
                        </div>
                    </div>

                    {{-- Valid Period --}}
                    <div class="border-t pt-4">
                        <p class="font-semibold text-gray-700 mb-2">📅 Valid Period</p>
                        <p class="text-sm">
                            <span x-text="formData.startDate"></span> <span x-text="formData.startTime"></span>
                            <span class="text-gray-500 mx-2">to</span>
                            <span x-text="formData.endDate"></span> <span x-text="formData.endTime"></span>
                        </p>
                    </div>

                    {{-- Usage Limit --}}
                    <div class="border-t pt-4">
                        <p class="font-semibold text-gray-700 mb-2">🎟️ Usage Limit</p>
                        <ul class="text-sm space-y-1">
                            <li x-show="formData.unlimitedUsage === 'true'">
                                • Total: <span class="font-medium">Unlimited redemptions</span>
                            </li>
                            <li x-show="formData.unlimitedUsage === 'false'">
                                • Total: <span class="font-medium" x-text="formData.totalUsageLimit"></span> redemptions
                            </li>
                            <li>
                                • Per User: <span class="font-medium" x-text="formData.perUserLimit"></span> time(s)
                            </li>
                        </ul>
                    </div>

                    {{-- Applicable To --}}
                    <div class="border-t pt-4">
                        <p class="font-semibold text-gray-700 mb-2">✅ Applicable To</p>
                        <ul class="text-sm space-y-1">
                            <li>• Services: 
                                <span class="font-medium" x-show="formData.allServices">All Services</span>
                                <span class="font-medium" x-show="!formData.allServices" x-text="formData.selectedServices.join(', ')"></span>
                            </li>
                            <li>• Branches: 
                                <span class="font-medium" x-show="formData.allBranches">All Branches (Global)</span>
                                <span class="font-medium" x-show="!formData.allBranches" x-text="formData.selectedBranches.join(', ')"></span>
                            </li>
                            <li>• Mitra: 
                                <span class="font-medium" x-show="formData.allMitra">All Mitra Partners</span>
                                <span class="font-medium" x-show="!formData.allMitra" x-text="formData.selectedMitra.join(', ')"></span>
                            </li>
                            <li x-show="formData.combinable">• <span class="text-green-600 font-medium">Can be combined with other discounts</span></li>
                            <li x-show="!formData.combinable">• <span class="text-orange-600 font-medium">Exclusive discount only</span></li>
                        </ul>
                    </div>

                    {{-- Customer Eligibility --}}
                    <div class="border-t pt-4">
                        <p class="font-semibold text-gray-700 mb-2">👥 Customer Eligibility</p>
                        <p class="text-sm">
                            <span x-show="formData.customerEligibility === 'all'">All Customers</span>
                            <span x-show="formData.customerEligibility === 'new'">New Customers Only (First booking)</span>
                            <span x-show="formData.customerEligibility === 'returning'">Returning Customers Only</span>
                        </p>
                    </div>

                    {{-- Booking Source --}}
                    <div class="border-t pt-4">
                        <p class="font-semibold text-gray-700 mb-2">📱 Booking Source</p>
                        <div class="flex flex-wrap gap-2 text-sm">
                            <span x-show="formData.allowOnline" class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full">Online</span>
                            <span x-show="formData.allowManual" class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full">Manual (Admin)</span>
                            <span x-show="formData.allowWalkin" class="px-3 py-1 bg-green-100 text-green-700 rounded-full">Walk-in</span>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="border-t pt-4" x-show="formData.description">
                        <p class="font-semibold text-gray-700 mb-2">📝 Description</p>
                        <p class="text-sm text-gray-600" x-text="formData.description"></p>
                    </div>
                </div>
            </div>

            {{-- Status After Creation --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">Status After Creation</label>
                <div class="space-y-2">
                    <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                           :class="formData.activateNow ? 'border-green-600 bg-green-50' : 'border-gray-300'">
                        <input type="radio" x-model="formData.activateNow" :value="true" class="mt-1">
                        <div class="ml-3">
                            <span class="font-medium text-sm">🟢 Active Immediately</span>
                            <p class="text-xs text-gray-500">Voucher will be usable right after creation</p>
                        </div>
                    </label>
                    <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                           :class="!formData.activateNow ? 'border-blue-600 bg-blue-50' : 'border-gray-300'">
                        <input type="radio" x-model="formData.activateNow" :value="false" class="mt-1">
                        <div class="ml-3">
                            <span class="font-medium text-sm">🔵 Draft (Activate Later)</span>
                            <p class="text-xs text-gray-500">Save as draft and activate manually later</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="flex flex-col sm:flex-row justify-between gap-3 pt-4 border-t">
                <button @click="prevStep()" 
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Previous
                </button>
                <div class="flex gap-3">
                    <button @click="saveDraft()" 
                            class="flex-1 sm:flex-none px-6 py-2 border border-indigo-300 bg-indigo-50 text-indigo-700 rounded-lg text-sm font-medium hover:bg-indigo-100 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Save as Draft
                    </button>
                    <button @click="createVoucher()" 
                            class="flex-1 sm:flex-none px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg text-sm font-medium hover:from-indigo-700 hover:to-purple-700 flex items-center justify-center shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Create & Activate
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- Success Modal --}}
    <div x-show="showSuccessModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showSuccessModal = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
            
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md p-8 text-center transform transition-all">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Voucher Created Successfully! 🎉</h3>
                <p class="text-gray-600 mb-6">Your voucher has been created and is now <span x-text="formData.activateNow ? 'active' : 'saved as draft'"></span>.</p>
                
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-500 mb-1">Voucher Code</p>
                    <p class="text-2xl font-bold text-indigo-600" x-text="formData.voucherCode || 'GENERATED-CODE'"></p>
                </div>

                <div class="space-y-2">
                    <button @click="viewVoucherList()" 
                            class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                        View Active Vouchers
                    </button>
                    <button @click="createAnother()" 
                            class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                        Create Another Voucher
                    </button>
                </div>
            </div>
        </div>
    </div>

</main>

<script>
    function createVoucher() {
        return {
            currentStep: 1,
            showSuccessModal: false,
            
            // Master Data
            serviceTypes: ['Meeting Room', 'Private Office', 'Sharing Room', 'Coworking Space', 'Virtual Office', 'Event Space'],
            branches: ['Surabaya Center', 'Surabaya Timur', 'Jakarta Selatan', 'Jakarta Barat'],
            mitras: ['PT SBY Office', 'PT JKT Workspace', 'PT Bandung Space'],
            
            // Form Data
            formData: {
                // Step 1
                voucherType: 'single',
                voucherCode: '',
                numberOfCodes: 100,
                codePattern: 'random',
                voucherName: '',
                description: '',
                
                // Step 2
                discountType: 'percentage',
                discountValue: '',
                maxDiscountAmount: '',
                minTransaction: '',
                allServices: true,
                selectedServices: [],
                freeMeetingRoom1Hour: false,
                
                // Step 3
                startDate: '',
                startTime: '00:00',
                endDate: '',
                endTime: '23:59',
                unlimitedUsage: 'false',
                totalUsageLimit: 1000,
                perUserLimit: 1,
                allBranches: true,
                selectedBranches: [],
                allMitra: true,
                selectedMitra: [],
                combinable: true,
                customerEligibility: 'all',
                allowOnline: true,
                allowManual: true,
                allowWalkin: false,
                
                // Step 4
                activateNow: true
            },

            init() {
                // Set default dates
                const today = new Date();
                this.formData.startDate = today.toISOString().split('T')[0];
                
                const nextMonth = new Date();
                nextMonth.setMonth(nextMonth.getMonth() + 1);
                this.formData.endDate = nextMonth.toISOString().split('T')[0];
            },

            // Navigation
            nextStep() {
                if (this.validateCurrentStep()) {
                    if (this.currentStep < 4) {
                        this.currentStep++;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                }
            },

            prevStep() {
                if (this.currentStep > 1) {
                    this.currentStep--;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            validateCurrentStep() {
                switch(this.currentStep) {
                    case 1:
                        if (!this.formData.voucherName) {
                            alert('Please enter voucher name');
                            return false;
                        }
                        if (this.formData.voucherType === 'single' && !this.formData.voucherCode) {
                            alert('Please enter voucher code');
                            return false;
                        }
                        if (this.formData.voucherType === 'unique' && !this.formData.numberOfCodes) {
                            alert('Please enter number of codes to generate');
                            return false;
                        }
                        break;
                    case 2:
                        if (this.formData.discountType !== 'free' && !this.formData.discountValue) {
                            alert('Please enter discount value');
                            return false;
                        }
                        if (!this.formData.allServices && this.formData.selectedServices.length === 0) {
                            alert('Please select at least one service');
                            return false;
                        }
                        break;
                    case 3:
                        if (!this.formData.startDate || !this.formData.endDate) {
                            alert('Please set validity period');
                            return false;
                        }
                        if (!this.formData.allowOnline && !this.formData.allowManual && !this.formData.allowWalkin) {
                            alert('Please select at least one booking source');
                            return false;
                        }
                        break;
                }
                return true;
            },

            // Utility Functions
            generateRandomCode() {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                let code = '';
                for (let i = 0; i < 8; i++) {
                    code += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                this.formData.voucherCode = code;
            },

            toggleAllServices() {
                if (this.formData.allServices) {
                    this.formData.selectedServices = [];
                } else {
                    this.formData.selectedServices = [...this.serviceTypes];
                }
            },

            toggleAllBranches() {
                if (this.formData.allBranches) {
                    this.formData.selectedBranches = [];
                } else {
                    this.formData.selectedBranches = [...this.branches];
                }
            },

            toggleAllMitra() {
                if (this.formData.allMitra) {
                    this.formData.selectedMitra = [];
                } else {
                    this.formData.selectedMitra = [...this.mitras];
                }
            },

            // Actions
            saveDraft() {
                if (confirm('Save voucher as draft?')) {
                    console.log('Saving as draft...', this.formData);
                    this.showSuccessModal = true;
                }
            },

            createVoucher() {
                if (confirm('Create and activate voucher now?')) {
                    console.log('Creating voucher...', this.formData);
                    
                    // If code is empty and type is single, generate one
                    if (this.formData.voucherType === 'single' && !this.formData.voucherCode) {
                        this.generateRandomCode();
                    }
                    
                    // Simulate API call
                    setTimeout(() => {
                        this.showSuccessModal = true;
                    }, 500);
                }
            },

            viewVoucherList() {
                alert('Redirecting to Active Vouchers page...');
                this.showSuccessModal = false;
                // window.location.href = '/superadmin/vouchers/active';
            },

            createAnother() {
                this.showSuccessModal = false;
                this.currentStep = 1;
                
                // Reset form
                this.formData = {
                    voucherType: 'single',
                    voucherCode: '',
                    numberOfCodes: 100,
                    codePattern: 'random',
                    voucherName: '',
                    description: '',
                    discountType: 'percentage',
                    discountValue: '',
                    maxDiscountAmount: '',
                    minTransaction: '',
                    allServices: true,
                    selectedServices: [],
                    freeMeetingRoom1Hour: false,
                    startDate: this.formData.startDate,
                    startTime: '00:00',
                    endDate: this.formData.endDate,
                    endTime: '23:59',
                    unlimitedUsage: 'false',
                    totalUsageLimit: 1000,
                    perUserLimit: 1,
                    allBranches: true,
                    selectedBranches: [],
                    allMitra: true,
                    selectedMitra: [],
                    combinable: true,
                    customerEligibility: 'all',
                    allowOnline: true,
                    allowManual: true,
                    allowWalkin: false,
                    activateNow: true
                };
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection