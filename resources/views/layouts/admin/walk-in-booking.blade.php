@extends('layouts.admin')

@section('title', 'Walk-in Booking')

@section('content')
<div x-data="walkInBookingData()" x-init="init()" class="space-y-4 md:space-y-6">
    
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">Walk-in Booking</h1>
            <p class="text-sm text-gray-600 mt-1">Quick booking for customers at reception</p>
        </div>
        <div class="flex items-center gap-2 sm:gap-3">
            <a href="{{ route('admin.booking.all') }}" class="px-3 sm:px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span class="hidden sm:inline">Back to All Bookings</span>
                <span class="sm:hidden">Back</span>
            </a>
        </div>
    </div>

    {{-- Progress Steps --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
        <div class="flex items-center justify-between">
            <template x-for="(step, index) in steps" :key="index">
                <div class="flex items-center" :class="index < steps.length - 1 ? 'flex-1' : ''">
                    <div class="flex flex-col items-center relative">
                        <div :class="{
                            'bg-blue-600 text-white': currentStep >= index + 1,
                            'bg-gray-200 text-gray-600': currentStep < index + 1
                        }" class="w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center font-semibold text-sm md:text-base transition">
                            <span x-text="index + 1"></span>
                        </div>
                        <p :class="currentStep >= index + 1 ? 'text-blue-600' : 'text-gray-500'" class="text-xs md:text-sm font-medium mt-2 whitespace-nowrap" x-text="step"></p>
                    </div>
                    <div x-show="index < steps.length - 1" :class="currentStep > index + 1 ? 'bg-blue-600' : 'bg-gray-200'" class="flex-1 h-1 mx-2 md:mx-4 transition"></div>
                </div>
            </template>
        </div>
    </div>

    {{-- Main Form --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        
        {{-- STEP 1: Customer Information --}}
        <div x-show="currentStep === 1" class="p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">Customer Information</h2>

            {{-- Search Existing Customer --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search Existing Customer (Optional)
                </h3>
                <div class="flex gap-2">
                    <input type="text" x-model="searchCustomer" @keyup.enter="searchExistingCustomer()" placeholder="Search by phone or email..." class="flex-1 px-3 py-2 border border-blue-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button @click="searchExistingCustomer()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        Search
                    </button>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-sm text-gray-600 mb-4">Or enter new customer information:</p>
            </div>

            {{-- Customer Form --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="formData.customerName" placeholder="Enter customer full name" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" x-model="formData.customerPhone" placeholder="08123456789" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <input type="email" x-model="formData.customerEmail" placeholder="customer@email.com" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Company <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <input type="text" x-model="formData.customerCompany" placeholder="Company name" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Navigation --}}
            <div class="flex justify-end mt-6 pt-6 border-t border-gray-200">
                <button @click="nextStep()" :disabled="!formData.customerName || !formData.customerPhone" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed font-medium">
                    Next: Select Service
                </button>
            </div>
        </div>

        {{-- STEP 2: Service Selection --}}
        <div x-show="currentStep === 2" class="p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">Select Service & Package</h2>

            {{-- Service Type --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Service Type <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <template x-for="service in services" :key="service.id">
                        <button @click="selectService(service)" :class="{
                            'border-blue-500 bg-blue-50': formData.serviceType === service.id,
                            'border-gray-300 hover:border-blue-300': formData.serviceType !== service.id
                        }" class="p-4 border-2 rounded-lg transition text-left">
                            <div class="flex items-center gap-3 mb-2">
                                <div :class="formData.serviceType === service.id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'" class="w-10 h-10 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x-html="service.icon"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 text-sm" x-text="service.name"></h3>
                                    <p class="text-xs text-gray-500" x-text="service.description"></p>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Package Selection (Meeting Room) --}}
            <div x-show="formData.serviceType === 'meeting' || formData.serviceType === 'event'" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Package Duration <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <template x-for="pkg in currentPackages" :key="pkg.id">
                        <button @click="formData.package = pkg.id; formData.duration = pkg.duration" :class="{
                            'border-blue-500 bg-blue-50': formData.package === pkg.id,
                            'border-gray-300 hover:border-blue-300': formData.package !== pkg.id
                        }" class="p-3 border-2 rounded-lg transition text-center">
                            <p class="font-semibold text-gray-800 text-sm" x-text="pkg.name"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="formatCurrency(pkg.price)"></p>
                            <p x-show="pkg.discount > 0" class="text-xs text-green-600 mt-1" x-text="'Save ' + pkg.discount + '%'"></p>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Date & Time Selection --}}
            <div x-show="formData.serviceType" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date <span class="text-red-500">*</span></label>
                    <input type="date" x-model="formData.bookingDate" :min="today" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div x-show="formData.serviceType === 'meeting' || formData.serviceType === 'event'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Time <span class="text-red-500">*</span></label>
                    <select x-model="formData.startTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select time</option>
                        <template x-for="time in timeSlots" :key="time">
                            <option :value="time" x-text="time"></option>
                        </template>
                    </select>
                </div>
            </div>

            {{-- Additional Info (Meeting Room / Event Space) --}}
            <div x-show="formData.serviceType === 'meeting' || formData.serviceType === 'event'" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Number of Participants</label>
                        <input type="number" x-model="formData.participants" min="1" placeholder="e.g., 10" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Facilities Needed</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        <template x-for="facility in availableFacilities" :key="facility">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" :value="facility" x-model="formData.facilities" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="text-sm text-gray-700" x-text="facility"></span>
                            </label>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="flex justify-between mt-6 pt-6 border-t border-gray-200">
                <button @click="prevStep()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    Back
                </button>
                <button @click="nextStep()" :disabled="!canProceedToStep3" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed font-medium">
                    Next: Select Room
                </button>
            </div>
        </div>

        {{-- STEP 3: Room Selection --}}
        <div x-show="currentStep === 3" class="p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">Select Available Room</h2>

            {{-- For Services without Room Selection --}}
            <div x-show="!needsRoomSelection" class="text-center py-8">
                <svg class="w-16 h-16 mx-auto text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-600 mb-6">This service doesn't require room selection</p>
                <button @click="nextStep()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Continue to Payment
                </button>
            </div>

            {{-- Available Rooms --}}
            <div x-show="needsRoomSelection" class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold">Date:</span> <span x-text="formData.bookingDate"></span> | 
                        <span class="font-semibold">Time:</span> <span x-text="formData.startTime"></span> | 
                        <span class="font-semibold">Participants:</span> <span x-text="formData.participants"></span> people
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    <template x-for="room in availableRooms" :key="room.number">
                        <button @click="selectRoom(room)" :disabled="!room.available" :class="{
                            'border-blue-500 bg-blue-50': formData.selectedRoom === room.number,
                            'border-gray-300 hover:border-blue-300': formData.selectedRoom !== room.number && room.available,
                            'border-gray-200 bg-gray-50 opacity-50 cursor-not-allowed': !room.available
                        }" class="p-4 border-2 rounded-lg transition text-center relative">
                            <div class="font-semibold text-gray-800 mb-1" x-text="room.number"></div>
                            <div class="text-xs text-gray-600 mb-2" x-text="room.capacity + ' pax'"></div>
                            <div x-show="room.available" class="text-xs text-green-600 font-medium">Available</div>
                            <div x-show="!room.available" class="text-xs text-red-600 font-medium">Occupied</div>
                            <svg x-show="formData.selectedRoom === room.number" class="absolute -top-1 -right-1 w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </template>
                </div>

                {{-- Room Info --}}
                <div x-show="selectedRoomData" class="bg-gray-50 border border-gray-200 rounded-lg p-4 mt-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Selected Room Details:</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-gray-600">Room Number:</p>
                            <p class="font-medium text-gray-800" x-text="selectedRoomData?.number"></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Capacity:</p>
                            <p class="font-medium text-gray-800" x-text="selectedRoomData?.capacity + ' people'"></p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <p class="text-gray-600 text-sm mb-2">Available Facilities:</p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="facility in selectedRoomData?.facilities" :key="facility">
                                <span class="inline-flex items-center px-2 py-1 bg-white text-blue-700 rounded text-xs font-medium border border-blue-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span x-text="facility"></span>
                                </span>
                            </template>
                        </div>
                    </div>

                    {{-- Match Warning --}}
                    <div x-show="formData.participants > selectedRoomData?.capacity" class="mt-3 flex items-start gap-2 bg-orange-50 border border-orange-200 rounded p-3">
                        <svg class="w-5 h-5 text-orange-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-xs text-orange-800">
                            <span class="font-semibold">Warning:</span> Room capacity (<span x-text="selectedRoomData?.capacity"></span> pax) is less than participants (<span x-text="formData.participants"></span> pax)
                        </p>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <div x-show="needsRoomSelection" class="flex justify-between mt-6 pt-6 border-t border-gray-200">
                <button @click="prevStep()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    Back
                </button>
                <button @click="nextStep()" :disabled="!formData.selectedRoom" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed font-medium">
                    Next: Payment
                </button>
            </div>
        </div>

        {{-- STEP 4: Payment & Confirmation --}}
        <div x-show="currentStep === 4" class="p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-6">Payment & Confirmation</h2>

            {{-- Booking Summary --}}
            <div class="bg-gray-50 rounded-lg p-4 md:p-6 mb-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Booking Summary</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Customer:</span>
                        <span class="font-medium text-gray-800" x-text="formData.customerName"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Phone:</span>
                        <span class="font-medium text-gray-800" x-text="formData.customerPhone"></span>
                    </div>
                    <div x-show="formData.customerEmail" class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span class="font-medium text-gray-800" x-text="formData.customerEmail"></span>
                    </div>
                    <div class="border-t border-gray-300 pt-3"></div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Service:</span>
                        <span class="font-medium text-gray-800" x-text="getServiceName()"></span>
                    </div>
                    <div x-show="formData.package" class="flex justify-between">
                        <span class="text-gray-600">Package:</span>
                        <span class="font-medium text-gray-800" x-text="getPackageName()"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date:</span>
                        <span class="font-medium text-gray-800" x-text="formData.bookingDate"></span>
                    </div>
                    <div x-show="formData.startTime" class="flex justify-between">
                        <span class="text-gray-600">Time:</span>
                        <span class="font-medium text-gray-800" x-text="formData.startTime + ' (' + formData.duration + ')'"></span>
                    </div>
                    <div x-show="formData.selectedRoom" class="flex justify-between">
                        <span class="text-gray-600">Room:</span>
                        <span class="font-medium text-gray-800" x-text="formData.selectedRoom"></span>
                    </div>
                    <div x-show="formData.participants" class="flex justify-between">
                        <span class="text-gray-600">Participants:</span>
                        <span class="font-medium text-gray-800" x-text="formData.participants + ' people'"></span>
                    </div>
                </div>
            </div>

            {{-- Pricing --}}
            <div class="bg-white border border-gray-200 rounded-lg p-4 md:p-6 mb-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Pricing Details</h3>
                
                <div class="space-y-3 text-sm mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Base Price:</span>
                        <span class="font-medium text-gray-800" x-text="formatCurrency(pricing.basePrice)"></span>
                    </div>
                    <div x-show="pricing.packageDiscount > 0" class="flex justify-between text-green-600">
                        <span>Package Discount:</span>
                        <span x-text="'- ' + formatCurrency(pricing.packageDiscount)"></span>
                    </div>
                </div>

                {{-- Apply Voucher --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Apply Voucher Code (Optional)</label>
                    <div class="flex gap-2">
                        <input type="text" x-model="formData.voucherCode" placeholder="Enter voucher code" class="flex-1 px-3 py-2 border border-blue-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button @click="applyVoucher()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium whitespace-nowrap">
                            Apply
                        </button>
                    </div>
                    <p x-show="formData.voucherApplied" class="text-xs text-green-600 mt-2 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Voucher applied successfully!
                    </p>
                </div>

                {{-- Manual Discount --}}
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Manual Discount (Optional)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="flex items-center gap-2 mb-2">
                                <input type="radio" x-model="formData.discountType" value="percentage" class="w-4 h-4 text-blue-600">
                                <span class="text-sm">Percentage</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="number" x-model="formData.discountValue" :disabled="formData.discountType !== 'percentage'" min="0" max="30" placeholder="Max 30%" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100">
                                <span class="flex items-center text-gray-600">%</span>
                            </div>
                        </div>
                        <div>
                            <label class="flex items-center gap-2 mb-2">
                                <input type="radio" x-model="formData.discountType" value="fixed" class="w-4 h-4 text-blue-600">
                                <span class="text-sm">Fixed Amount</span>
                            </label>
                            <input type="number" x-model="formData.discountValue" :disabled="formData.discountType !== 'fixed'" min="0" placeholder="Rp" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100">
                        </div>
                    </div>
                    <div x-show="formData.discountType" class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Discount Reason</label>
                        <select x-model="formData.discountReason" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select reason</option>
                            <option value="first-time">First-time Customer</option>
                            <option value="loyal">Loyal Customer</option>
                            <option value="corporate">Corporate Deal</option>
                            <option value="special">Special Request</option>
                        </select>
                    </div>
                </div>

                {{-- Total --}}
                <div class="border-t border-gray-300 pt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-base font-semibold text-gray-800">Total Payment:</span>
                        <span class="text-2xl font-bold text-blue-600" x-text="formatCurrency(calculateTotal())"></span>
                    </div>
                </div>
            </div>

            {{-- Special Notes --}}
            <div class="bg-white border border-gray-200 rounded-lg p-4 md:p-6 mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Special Requests / Notes (Optional)</label>
                <textarea x-model="formData.notes" rows="3" placeholder="Any special requests or notes..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            {{-- Confirmation Options --}}
            <div class="bg-white border border-gray-200 rounded-lg p-4 md:p-6 mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Send Confirmation</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" x-model="formData.sendEmail" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Send email confirmation</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" x-model="formData.sendSMS" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Send SMS reminder</span>
                    </label>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="flex flex-col sm:flex-row justify-between gap-3 pt-6 border-t border-gray-200">
                <button @click="prevStep()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium order-2 sm:order-1">
                    Back
                </button>
                <button @click="confirmBooking()"  class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed font-semibold order-1 sm:order-2">
                    Confirm & Process Payment
                </button>
            </div>
        </div>

    </div>

    {{-- Success Modal --}}
    <div x-show="showSuccessModal" x-cloak @click.away="showSuccessModal = false" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 transform transition-all">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Booking Created Successfully!</h3>
                    <p class="text-sm text-gray-600 mb-6">
                        Booking ID: <span class="font-semibold text-blue-600" x-text="generatedBookingId"></span>
                    </p>
                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                        <p class="text-sm text-gray-600 mb-2">Payment Status:</p>
                        <p class="text-base font-semibold text-green-600">✓ Paid - <span x-text="formatCurrency(calculateTotal())"></span></p>
                    </div>

                    <div class="flex flex-col gap-3">
                        <button @click="printReceipt()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Print Receipt
                        </button>
                        <button @click="sendToEmail()" x-show="formData.customerEmail" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                            Send to Email
                        </button>
                        <button @click="createNewBooking()" class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                            Create New Booking
                        </button>
                        <a href="{{ route('admin.booking.all') }}" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-medium text-center">
                            Go to All Bookings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Alpine.js Data & Logic --}}
@push('scripts')
<script>
function walkInBookingData() {
    return {
        // Steps
        steps: ['Customer', 'Service', 'Room', 'Payment'],
        currentStep: 1,

        // Today's date
        today: new Date().toISOString().split('T')[0],

        // Search
        searchCustomer: '',

        // Form Data
        formData: {
            customerName: '',
            customerPhone: '',
            customerEmail: '',
            customerCompany: '',
            serviceType: '',
            package: '',
            duration: '',
            bookingDate: '',
            startTime: '',
            participants: '',
            facilities: [],
            selectedRoom: '',
            voucherCode: '',
            voucherApplied: false,
            discountType: '',
            discountValue: 0,
            discountReason: '',
            notes: '',
            sendEmail: true,
            sendSMS: true
        },

        // Pricing
        pricing: {
            basePrice: 0,
            packageDiscount: 0
        },

        // Services
        services: [
            { id: 'meeting', name: 'Meeting Room', description: 'Per hour booking', icon: '' },
            { id: 'private', name: 'Private Office', description: 'Monthly/Yearly', icon: '' },
            { id: 'sharing', name: 'Sharing Room', description: 'Monthly/Yearly', icon: '' },
            { id: 'coworking', name: 'Coworking Space', description: 'Day pass', icon: '' },
            { id: 'virtual', name: 'Virtual Office', description: 'Monthly/Yearly', icon: '' },
            { id: 'event', name: 'Event Space', description: 'Per hour/day', icon: '' }
        ],

        // Packages
        packages: {
            meeting: [
                { id: '1h', name: '1 Hour', duration: '1 Hour', price: 100000, discount: 0 },
                { id: '2h', name: '2 Hours', duration: '2 Hours', price: 180000, discount: 10 },
                { id: '4h', name: '4 Hours', duration: '4 Hours', price: 320000, discount: 20 },
                { id: 'fullday', name: 'Full Day', duration: '8 Hours', price: 500000, discount: 37.5 }
            ],
            event: [
                { id: '4h', name: '4 Hours', duration: '4 Hours', price: 2000000, discount: 0 },
                { id: 'halfday', name: 'Half Day', duration: '5 Hours', price: 2500000, discount: 0 },
                { id: 'fullday', name: 'Full Day', duration: '10 Hours', price: 4500000, discount: 10 }
            ]
        },

        // Time Slots
        timeSlots: ['08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00'],

        // Facilities
        availableFacilities: ['Proyektor', 'Whiteboard', 'Flipchart', 'AC', 'Sound System', 'Teleconference'],

        // Available Rooms
        availableRooms: [
            { number: 'Room 201', capacity: 10, available: true, facilities: ['Proyektor', 'Whiteboard', 'AC'] },
            { number: 'Room 202', capacity: 6, available: true, facilities: ['Proyektor', 'Flipchart', 'AC'] },
            { number: 'Room 203', capacity: 20, available: false, facilities: ['LED TV', 'Sound System', 'Whiteboard'] },
            { number: 'Room 204', capacity: 8, available: true, facilities: ['Proyektor', 'Whiteboard', 'AC'] },
            { number: 'Room 205', capacity: 12, available: false, facilities: ['Proyektor', 'AC'] }
        ],

        // Modal
        showSuccessModal: false,
        generatedBookingId: '',

        // Initialize
        init() {
            this.formData.bookingDate = this.today;
        },

        // Computed
        get currentPackages() {
            if (this.formData.serviceType === 'meeting') {
                return this.packages.meeting;
            } else if (this.formData.serviceType === 'event') {
                return this.packages.event;
            }
            return [];
        },

        get canProceedToStep3() {
            if (!this.formData.serviceType || !this.formData.bookingDate) return false;
            if (this.formData.serviceType === 'meeting' || this.formData.serviceType === 'event') {
                return this.formData.package && this.formData.startTime;
            }
            return true;
        },

        get needsRoomSelection() {
            return ['meeting', 'private', 'sharing', 'event'].includes(this.formData.serviceType);
        },

        get selectedRoomData() {
            return this.availableRooms.find(r => r.number === this.formData.selectedRoom);
        },

        // Methods
        formatCurrency(value) {
            return 'Rp ' + parseInt(value).toLocaleString('id-ID');
        },

        searchExistingCustomer() {
            alert('Searching for customer: ' + this.searchCustomer);
            // Implementation for searching existing customer
        },

        selectService(service) {
            this.formData.serviceType = service.id;
            this.formData.package = '';
            this.formData.duration = '';
            this.calculatePricing();
        },

        selectRoom(room) {
            if (room.available) {
                this.formData.selectedRoom = room.number;
            }
        },

        calculatePricing() {
            if (this.formData.serviceType === 'meeting' || this.formData.serviceType === 'event') {
                const pkg = this.currentPackages.find(p => p.id === this.formData.package);
                if (pkg) {
                    this.pricing.basePrice = pkg.price;
                    this.pricing.packageDiscount = (pkg.price * pkg.discount) / 100;
                }
            } else {
                this.pricing.basePrice = 0;
                this.pricing.packageDiscount = 0;
            }
        },

        calculateTotal() {
            let total = this.pricing.basePrice - this.pricing.packageDiscount;

            // Apply voucher (example: 100% free)
            if (this.formData.voucherApplied) {
                total = 0;
            }

            // Apply manual discount
            if (this.formData.discountType === 'percentage' && this.formData.discountValue > 0) {
                total -= (total * this.formData.discountValue) / 100;
            } else if (this.formData.discountType === 'fixed' && this.formData.discountValue > 0) {
                total -= this.formData.discountValue;
            }

            return Math.max(0, total);
        },

        applyVoucher() {
            if (this.formData.voucherCode) {
                // Simulate voucher validation
                this.formData.voucherApplied = true;
                alert('Voucher applied: FREE booking!');
            }
        },

        getServiceName() {
            const service = this.services.find(s => s.id === this.formData.serviceType);
            return service ? service.name : '';
        },

        getPackageName() {
            const pkg = this.currentPackages.find(p => p.id === this.formData.package);
            return pkg ? pkg.name : '';
        },

        nextStep() {
            if (this.currentStep === 2) {
                this.calculatePricing();
            }
            if (this.currentStep === 3 && !this.needsRoomSelection) {
                this.currentStep = 4;
            } else {
                this.currentStep++;
            }
        },

        prevStep() {
            if (this.currentStep === 4 && !this.needsRoomSelection) {
                this.currentStep = 2;
            } else {
                this.currentStep--;
            }
        },

        confirmBooking() {
            // Generate booking ID
            this.generatedBookingId = '#WI-' + Math.floor(Math.random() * 10000);
            
            // Show success modal
            this.showSuccessModal = true;

            // Implementation: Send data to backend
            console.log('Booking Data:', this.formData);
        },

        printReceipt() {
            alert('Printing receipt for ' + this.generatedBookingId);
            window.print();
        },

        sendToEmail() {
            alert('Sending confirmation to ' + this.formData.customerEmail);
        },

        createNewBooking() {
            this.showSuccessModal = false;
            this.currentStep = 1;
            this.formData = {
                customerName: '',
                customerPhone: '',
                customerEmail: '',
                customerCompany: '',
                serviceType: '',
                package: '',
                duration: '',
                bookingDate: this.today,
                startTime: '',
                participants: '',
                facilities: [],
                selectedRoom: '',
                voucherCode: '',
                voucherApplied: false,
                discountType: '',
                discountValue: 0,
                discountReason: '',
                notes: '',
                sendEmail: true,
                sendSMS: true
            };
            this.pricing = {
                basePrice: 0,
                packageDiscount: 0
            };
        }
    }
}
</script>
@endpush

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
@endsection