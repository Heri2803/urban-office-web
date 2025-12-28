@extends('layouts.admin')

@php
    // Gunakan isset() untuk mengecek apakah variabel ada
    $isExistingCustomer = isset($isExistingCustomer) ? $isExistingCustomer : false;
    $booking_type = isset($booking_type) ? $booking_type : 'walk_in';
    
    $pageTitle = $isExistingCustomer ? 'Existing Customer Booking' : 'Walk-in Booking';
    $pageDescription = $isExistingCustomer 
        ? 'Booking for existing customers with booking history' 
        : 'Quick booking for customers at reception';
@endphp

@section('title', $pageTitle)
@section('content')
<div x-data="walkInBookingData()" x-init="initForm()" class="space-y-4 md:space-y-6">
    
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">Walk-in & Existing Customer Booking</h1>
            <p class="text-sm text-gray-600 mt-1">Quick booking for all customers</p>
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
        {{-- STEP 1: CUSTOMER INFORMATION (DUAL MODE) --}}
        <div x-show="currentStep === 1" class="p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">
                <span x-text="bookingType === 'existing' ? 'Select Existing Customer' : 'Customer Information'"></span>
            </h2>

            {{-- EXISTING CUSTOMER MODE --}}
            <template x-if="bookingType === 'existing'">
                <div>
                    {{-- SEARCH BOX (HANYA DI MODE EXISTING) --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Search Existing Customer <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                x-model="customerSearch" 
                                @input.debounce.500ms="searchCustomers()"
                                placeholder="Search by name, phone, or email..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                :disabled="selectedCustomer">
                            <div class="absolute right-3 top-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Search for customers who have booked before</p>
                    </div>

                    {{-- SEARCH RESULTS --}}
                    <div x-show="customerResults.length > 0 && !selectedCustomer" class="mb-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Select Customer:</h3>
                        <div class="space-y-3 max-h-80 overflow-y-auto pr-2">
                            <template x-for="customer in customerResults" :key="customer.id">
                                <button @click="selectCustomer(customer)" 
                                        class="w-full p-4 border-2 border-gray-200 rounded-lg text-left transition hover:border-blue-300 hover:bg-blue-50">
                                    {{-- Customer info dengan booking history --}}
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-800" x-text="customer.name"></h4>
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 mt-2 text-sm text-gray-600">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                                    </svg>
                                                    <span x-text="customer.phone"></span>
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                                    </svg>
                                                    <span x-text="customer.email" class="truncate"></span>
                                                </span>
                                            </div>
                                            {{-- Booking History Info --}}
                                            <div class="mt-2 text-xs text-gray-500">
                                                <span x-text="customer.booking_count"></span> previous bookings
                                                <template x-if="customer.last_booking">
                                                    | Last: <span x-text="customer.last_booking.date"></span> 
                                                    (<span x-text="customer.last_booking.service"></span>)
                                                </template>
                                            </div>
                                        </div>
                                        <div class="text-right ml-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <span x-text="customer.booking_count"></span> bookings
                                            </span>
                                        </div>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                    {{-- SELECTED CUSTOMER INFO --}}
                    <div x-show="selectedCustomer" class="mb-6">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-medium text-green-800 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Customer Selected
                                </h3>
                                <button @click="resetExistingCustomer()" 
                                        class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    Change
                                </button>
                            </div>
                            
                            {{-- AUTO-FILLED FORM (READ-ONLY) --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Full Name</label>
                                    <div class="px-3 py-2 bg-white border border-gray-200 rounded text-gray-800">
                                        <span x-text="selectedCustomer ? selectedCustomer.name : ''"></span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Phone Number</label>
                                    <div class="px-3 py-2 bg-white border border-gray-200 rounded text-gray-800">
                                        <span x-text="selectedCustomer ? selectedCustomer.phone : ''"></span>
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm text-gray-600 mb-1">Email</label>
                                    <div class="px-3 py-2 bg-white border border-gray-200 rounded text-gray-800">
                                        <span x-text="selectedCustomer ? selectedCustomer.email : ''"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- BOOKING HISTORY SUMMARY --}}
                            <template x-if="selectedCustomer">
                                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded">
                                    <h4 class="text-sm font-medium text-blue-800 mb-2">Booking History:</h4>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-gray-600">Total Bookings:</span>
                                            <span class="font-medium ml-2" x-text="selectedCustomer.booking_count"></span>
                                        </div>
                                        <div x-show="selectedCustomer.last_booking">
                                            <span class="text-gray-600">Last Booking:</span>
                                            <span class="font-medium ml-2" x-text="selectedCustomer.last_booking?.date || '-'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            {{-- LOYALTY DISCOUNT --}}
                            <div x-show="loyaltyDiscount > 0" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-yellow-800">
                                            Loyalty Discount: <span x-text="loyaltyDiscount"></span>% Applied!
                                        </p>
                                        <p class="text-xs text-yellow-700 mt-1">
                                            Based on <span x-text="selectedCustomer ? selectedCustomer.booking_count : 0"></span> previous bookings
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- NO RESULTS --}}
                    <div x-show="customerSearch.length >= 2 && customerResults.length === 0 && !isSearching && !selectedCustomer" class="mb-6">
                        <div class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-gray-600">No existing customers found for "<span x-text="customerSearch"></span>"</p>
                            <p class="text-sm text-gray-500 mt-1">This customer may be new or has different contact details</p>
                            <button @click="bookingType = 'walk_in'" 
                                    class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                                Switch to Walk-in Booking
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            {{-- WALK-IN MODE --}}
            <template x-if="bookingType === 'walk_in'">
                <div>
                    <div class="mb-6">
                        <p class="text-sm text-gray-600 mb-4">Enter customer information:</p>
                    </div>

                    {{-- CUSTOMER FORM (MANUAL INPUT) - GANTI BINDING --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   x-model="namaLengkap" 
                                   @input="formData.customerName = $event.target.value"
                                   placeholder="Enter customer full name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" 
                                   x-model="phone" 
                                   @input="formData.customerPhone = $event.target.value"
                                   placeholder="08123456789" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <input type="email" 
                                   x-model="email" 
                                   @input="formData.customerEmail = $event.target.value"
                                   placeholder="customer@email.com" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Company <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <input type="text" 
                                   x-model="formData.customerCompany" 
                                   placeholder="Company name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    {{-- QUICK SWITCH TO EXISTING CUSTOMER --}}
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-sm text-gray-600 mb-3">Is this customer returning?</p>
                        <button @click="switchToExistingMode()" 
                                class="px-4 py-2 border border-green-300 text-green-700 rounded-lg hover:bg-green-50 transition text-sm font-medium flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Search Existing Customer
                        </button>
                    </div>
                </div>
            </template>

            {{-- NAVIGATION --}}
            <div class="flex justify-between mt-6 pt-6 border-t border-gray-200">
                <div>
                    <button @click="switchBookingType()" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <span x-text="bookingType === 'existing' ? 'Switch to Walk-in' : 'Switch to Existing Customer'"></span>
                    </button>
                </div>
                <button @click="nextStep()" 
                        :disabled="bookingType === 'existing' ? !selectedCustomer : (!namaLengkap || !phone)"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed font-medium">
                    Next: Select Service
                </button>
            </div>
        </div>

        {{-- STEP 2: SERVICE SELECTION - GANTI TOTAL STRUKTUR --}}
        <div x-show="currentStep === 2" class="p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">Select Service & Package</h2>

            {{-- Service Type - GANTI DENGAN CUSTOMER FORMAT --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-800 mb-3">
                    Pilih Jenis Layanan <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 lg:grid-cols-5 gap-2 md:gap-3">
                    <template x-for="roomType in roomTypes" :key="roomType">
                        <button type="button"
                                @click="selectRoomType(roomType)"
                                class="px-3 md:px-4 py-3 rounded-lg font-semibold text-sm md:text-base transition-all duration-300 transform hover:scale-105"
                                :class="selectedRoomType === roomType 
                                    ? 'bg-orange-500 hover:bg-orange-600 text-white shadow-lg' 
                                    : 'bg-gray-100 hover:bg-gray-200 text-gray-800 hover:shadow-md'">
                            <span x-text="roomType"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Date & Time Selection - GANTI BINDING --}}
            <div x-show="selectedRoomType" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                        x-model="bookingDate" 
                        @change="formData.booking_date = $event.target.value"
                        :min="today" 
                        class="form-input border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 hover:border-gray-400">
                </div>

                {{-- Show time selection for services that need it --}}
                <div x-show="needsStartTime()">
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        Waktu Mulai Akses <span class="text-red-500">*</span>
                    </label>
                    <input type="time"
                        x-model="startTime"
                        @change="formData.start_time = $event.target.value"
                        class="form-input border-2 border-gray-300 focus:border-orange-500 focus:ring 
                                focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium
                                placeholder-gray-500 transition-all duration-200 hover:border-gray-400">
                </div>
            </div>

            {{-- SERVICE-SPECIFIC FIELDS --}}
            
            {{-- PRIVATE OFFICE Durasi --}}
            <div x-show="selectedRoomType === 'Private Office'" x-transition>
                <label class="block text-sm font-semibold text-gray-800 mb-2">
                    Durasi Sewa <span class="text-red-500">*</span>
                </label>
                <select x-model="privateOfficeDuration"
                        @change="formData.private_office_duration = $event.target.value; fetchServicePrice()"
                        class="form-select border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 bg-white">
                    <option value="">-- Pilih Durasi --</option>
                    <option value="hourly">Per Jam</option>
                    <option value="daily">Per Hari</option>
                    <option value="weekly">Per Minggu</option>
                    <option value="monthly">Per Bulan</option>
                    <option value="yearly">Per Tahun</option>
                </select>
            </div>

            {{-- VIRTUAL OFFICE: Paket --}}
            <div x-show="selectedRoomType === 'Virtual Office'" x-transition>
                <label class="block text-sm font-semibold text-gray-800 mb-2">
                    Pilih Paket <span class="text-red-500">*</span>
                </label>
                <select x-model="virtualOfficePackage"
                        @change="formData.virtual_office_package = $event.target.value; calculatePrice()"
                        class="form-select border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 bg-white">
                    <option value="">-- Pilih Paket --</option>
                    <template x-for="service in servicePrices" :key="service.id">
                        <option :value="service.value"
                                x-text="`${service.name} - Rp ${formatPrice(service.base_price)}`">
                        </option>
                    </template>
                </select>
            
                {{-- Dropdown Durasi --}}
                <div class="mt-3">
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        Durasi Sewa <span class="text-red-500">*</span>
                    </label>
                    <select x-model="virtualOfficeDuration"
                            @change="formData.virtual_office_duration = $event.target.value; calculatePrice()"
                            class="form-select border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium bg-white">
                        <option value="">-- Pilih Durasi --</option>
                        <option value="monthly">Per Bulan</option>
                        <option value="yearly">Per Tahun</option>
                    </select>
                </div>
            </div>

            {{-- COWORKING: Opsi Pass --}}
            <div x-show="selectedRoomType === 'Coworking Space'" x-transition>
                <label class="block text-sm font-semibold text-gray-800 mb-2">
                    Pilih Pass <span class="text-red-500">*</span>
                </label>
            
                <div class="space-y-2">
                    <template x-for="pass in coworkingPasses" :key="pass.id">
                        <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                            :class="coworkingPass === pass.value ? 'border-orange-500 bg-orange-50' : 'border-gray-300 hover:border-gray-400'">
                            <input type="radio" 
                                   x-model="coworkingPass" 
                                   @change="formData.coworking_pass = pass.id"
                                   :value="pass.value" 
                                   class="mr-3 text-orange-500 focus:ring-orange-500">
                            <div class="flex-1">
                                <span class="font-semibold" x-text="pass.name"></span>
                                <span class="text-gray-600 text-sm ml-2" x-text="formatPassPrice(pass)"></span>
                            </div>
                        </label>
                    </template>
                </div>
            </div>

            {{-- EVENT SPACE: Durasi & Coffee Break --}}
            <div x-show="selectedRoomType === 'Event Space'" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        Durasi <span class="text-red-500">*</span>
                    </label>
                    <select x-model="eventDuration"
                        @change="formData.event_duration = $event.target.value; calculatePrice()"
                        class="form-select border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 bg-white">
                        <option value="">-- Pilih Durasi --</option>
                        <option value="4h">4 Jam</option>
                        <option value="8h">8 Jam</option>
                        <option value="daily">Per Hari</option>
                        <option value="weekly">Per Minggu</option>
                        <option value="monthly">Per Bulan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        Coffee Break <span class="text-red-500">*</span>
                    </label>
                    <select x-model="eventCoffeeBreak"
                        @change="formData.coffee_break_option = $event.target.value; calculatePrice()"
                        class="form-select border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 bg-white">
                        <option value="">-- Pilih Opsi --</option>
                        <option value="none">Tanpa Coffee Break</option>
                        <option value="1x">1x Coffee Break</option>
                        <option value="2x">2x Coffee Break</option>
                    </select>
                </div>
            </div>

            {{-- MEETING ROOM: Durasi & Coffee Break --}}
            <div x-show="selectedRoomType === 'Meeting Room'" 
                 x-transition 
                 class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
                <!-- Pilih Durasi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        Durasi <span class="text-red-500">*</span>
                    </label>
            
                    <select x-model="meetingDuration"
                        @change="formData.meeting_duration = $event.target.value; calculatePrice()"
                        class="form-select border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 bg-white">
                        <option value="">-- Pilih Durasi --</option>
                        <option value="1h">1 Jam</option>
                        <option value="4h">4 Jam</option>
                        <option value="8h">8 Jam</option>
                        <option value="custom">Custom (Jam Manual)</option>
                        <option value="daily">Per Hari</option>
                        <option value="weekly">Per Minggu</option>
                        <option value="monthly">Per Bulan</option>
                    </select>
            
                    <!-- Inputan Manual jika pilih Custom -->
                    <template x-if="meetingDuration === 'custom'">
                        <div class="mt-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Masukkan Jumlah Jam</label>
                            <input type="number" min="1" x-model="customMeetingHours"
                                   @input="formData.custom_hours = $event.target.value; calculatePrice()"
                                   placeholder="Masukkan jumlah jam"
                                   class="form-input border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 placeholder-gray-500 transition-all duration-200 hover:border-gray-400">
                        </div>
                    </template>
                </div>
            
                <!-- Pilih Coffee Break -->
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        Coffee Break <span class="text-red-500">*</span>
                    </label>
                    <select x-model="meetingCoffeeBreak"
                        @change="formData.coffee_break_option = $event.target.value; calculatePrice()"
                        class="form-select border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 bg-white">
                        <option value="">-- Pilih Opsi --</option>
                        <option value="none">Tanpa Coffee Break</option>
                        <option value="1x">1x Coffee Break</option>
                        <template x-if="meetingDuration === '8h' || (meetingDuration === 'custom' && parseInt(customMeetingHours) >= 8)">
                            <option value="2x">2x Coffee Break</option>
                        </template>
                    </select>
                </div>
            </div>

            {{-- SHARING ROOM: Durasi --}}
            <div x-show="selectedRoomType === 'Sharing Room'" x-transition>
                <label class="block text-sm font-semibold text-gray-800 mb-2">
                    Durasi Sewa <span class="text-red-500">*</span>
                </label>
            
                <select x-model="sharingRoomDuration"
                        @change="formData.sharing_room_duration = $event.target.value; fetchServicePrice()"
                        class="form-select border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium bg-white">
                    <option value="">-- Pilih Durasi --</option>
                    <option value="monthly">Per Bulan</option>
                    <option value="yearly">Per Tahun</option>
                </select>
            </div>

            {{-- Input Jumlah Orang & Tanggal Booking --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                {{-- Field Jumlah Orang --}}
                <div x-show="shouldShowPeopleInput()">
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        Jumlah Orang <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           x-model="numPeople"
                           @input="formData.jumlah_orang = $event.target.value; validateCapacity()"
                           min="1"
                           placeholder="Masukkan jumlah orang"
                           class="form-input border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium placeholder-gray-500 transition-all duration-200 hover:border-gray-400">
                    
                    <p x-show="capacityWarning"
                       x-text="capacityWarning"
                       class="text-xs text-red-600 mt-1"></p>
                </div>
                
                {{-- Input Jumlah Hari / Minggu / Bulan / Tahun --}}
                <div x-show="shouldShowQuantityInput()">
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        <span x-text="getQuantityLabel()"></span> <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           x-model="quantity"
                           @input="formData.quantity = $event.target.value; calculatePrice()"
                           min="1"
                           :placeholder="getQuantityPlaceholder()"
                           class="form-input border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium placeholder-gray-500 transition-all duration-200 hover:border-gray-400">
                </div>
            </div>

            {{-- Price Preview --}}
            <div x-show="calculatedPrice > 0 || summary?.total > 0" class="mb-6 p-4 bg-gray-50 rounded-lg mt-4">
                <h3 class="font-medium text-gray-800 mb-3">Price Summary</h3>
                
                {{-- Total --}}
                <div class="border-t border-gray-300 pt-3">
                    <div class="flex justify-between items-center">
                        <span class="text-base font-semibold text-gray-800">Total:</span>
                        <span class="text-2xl font-bold text-blue-600" 
                              x-text="formatCurrency(summary?.total || calculatedPrice)"></span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1" x-text="summary?.details || priceDetails"></p>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="flex justify-between mt-6 pt-6 border-t border-gray-200">
                <button @click="prevStep()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    Back
                </button>
                <button @click="nextStep()" 
                        :disabled="!selectedRoomType || !bookingDate || (needsStartTime() && !startTime)"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed font-medium">
                    Next: <span x-text="needsRoomSelection() ? 'Select Room' : 'Review & Payment'"></span>
                </button>
            </div>
        </div>

        {{-- STEP 3: Room Selection - UPDATE BINDING --}}
        <div x-show="currentStep === 3" class="p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">Select Available Room</h2>

            {{-- For Services without Room Selection --}}
            <div x-show="!needsRoomSelection()" class="text-center py-8">
                <svg class="w-16 h-16 mx-auto text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-600 mb-6">This service doesn't require room selection</p>
                <button @click="nextStep()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Continue to Payment
                </button>
            </div>

            {{-- Available Rooms --}}
            <div x-show="needsRoomSelection()" class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold">Date:</span> <span x-text="bookingDate"></span> | 
                        <span class="font-semibold">Time:</span> <span x-text="startTime"></span> | 
                        <span class="font-semibold">Participants:</span> <span x-text="numPeople || formData.jumlah_orang"></span> people
                    </p>
                </div>

                {{-- Rooms Grid - GANTI BINDING --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    <template x-for="room in availableRooms" :key="room.id">
                        <button @click="selectedRoom = room.id; formData.room_id = room.id" 
                                :disabled="!room.is_selectable" 
                                :class="{
                                    'border-blue-500 bg-blue-50': selectedRoom === room.id,
                                    'border-gray-300 hover:border-blue-300': selectedRoom !== room.id && room.is_selectable,
                                    'border-gray-200 bg-gray-50 opacity-50 cursor-not-allowed': !room.is_selectable
                                }" 
                                class="p-4 border-2 rounded-lg transition text-center relative">
                            <div class="font-semibold text-gray-800 mb-1" x-text="room.room_number"></div>
                            <div class="text-xs text-gray-600 mb-2" x-text="room.capacity + ' pax'"></div>
                            <div x-show="room.is_selectable" class="text-xs text-green-600 font-medium">Available</div>
                            <div x-show="!room.is_selectable" class="text-xs text-red-600 font-medium">Occupied</div>
                            <svg x-show="selectedRoom === room.id" class="absolute -top-1 -right-1 w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </template>
                </div>

                {{-- Room Info --}}
                <div x-show="selectedRoom" class="bg-gray-50 border border-gray-200 rounded-lg p-4 mt-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Selected Room Details:</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-gray-600">Room Number:</p>
                            <p class="font-medium text-gray-800" x-text="roomDetails?.room_number || 'N/A'"></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Capacity:</p>
                            <p class="font-medium text-gray-800" x-text="(roomDetails?.capacity || 0) + ' people'"></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Size:</p>
                            <p class="font-medium text-gray-800" x-text="(roomDetails?.size_m2 || 0) + ' m²'"></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Floor:</p>
                            <p class="font-medium text-gray-800" x-text="roomDetails?.floor || 'N/A'"></p>
                        </div>
                    </div>

                    {{-- Match Warning --}}
                    <div x-show="roomDetails && numPeople > roomDetails.capacity" class="mt-3 flex items-start gap-2 bg-orange-50 border border-orange-200 rounded p-3">
                        <svg class="w-5 h-5 text-orange-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-xs text-orange-800">
                            <span class="font-semibold">Warning:</span> Room capacity (<span x-text="roomDetails?.capacity || 0"></span> pax) is less than participants (<span x-text="numPeople"></span> pax)
                        </p>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <div x-show="needsRoomSelection()" class="flex justify-between mt-6 pt-6 border-t border-gray-200">
                <button @click="prevStep()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    Back
                </button>
                <button @click="nextStep()" 
                        :disabled="!selectedRoom"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed font-medium">
                    Next: Payment
                </button>
            </div>
        </div>

        {{-- STEP 4: Payment & Confirmation - UPDATE DISPLAY FIELDS --}}
        <div x-show="currentStep === 4" class="p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-6">Payment & Confirmation</h2>

            {{-- Booking Summary --}}
            <div class="bg-gray-50 rounded-lg p-4 md:p-6 mb-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Booking Summary</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Customer:</span>
                        <span class="font-medium text-gray-800" x-text="namaLengkap || formData.customerName"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Phone:</span>
                        <span class="font-medium text-gray-800" x-text="phone || formData.customerPhone"></span>
                    </div>
                    <div x-show="email || formData.customerEmail" class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span class="font-medium text-gray-800" x-text="email || formData.customerEmail"></span>
                    </div>
                    
                    @if($isExistingCustomer)
                    <div x-show="selectedCustomer" class="flex justify-between items-center">
                        <span class="text-gray-600">Customer Type:</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Existing Customer
                        </span>
                    </div>
                    <div x-show="selectedCustomer" class="flex justify-between">
                        <span class="text-gray-600">Previous Bookings:</span>
                        <span class="font-medium text-gray-800" x-text="selectedCustomer.booking_count"></span>
                    </div>
                    @endif
                    
                    <div class="border-t border-gray-300 pt-3"></div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Service:</span>
                        <span class="font-medium text-gray-800" x-text="selectedRoomType"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date:</span>
                        <span class="font-medium text-gray-800" x-text="bookingDate"></span>
                    </div>
                    <div x-show="startTime" class="flex justify-between">
                        <span class="text-gray-600">Time:</span>
                        <span class="font-medium text-gray-800" x-text="startTime"></span>
                    </div>
                    <div x-show="selectedRoom" class="flex justify-between">
                        <span class="text-gray-600">Room:</span>
                        <span class="font-medium text-gray-800" x-text="roomDetails?.room_number || 'N/A'"></span>
                    </div>
                    <div x-show="numPeople" class="flex justify-between">
                        <span class="text-gray-600">Participants:</span>
                        <span class="font-medium text-gray-800" x-text="numPeople + ' people'"></span>
                    </div>
                </div>
            </div>

            {{-- Pricing --}}
            <div class="bg-white border border-gray-200 rounded-lg p-4 md:p-6 mb-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Pricing Details</h3>
                
                <div class="space-y-3 text-sm mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Base Price:</span>
                        <span class="font-medium text-gray-800" x-text="formatCurrency(summary?.roomSubtotal || pricing.basePrice)"></span>
                    </div>
                    
                    {{-- Loyalty Discount --}}
                    <div x-show="loyaltyDiscount > 0" class="flex justify-between text-yellow-600">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            Loyalty Discount (<span x-text="loyaltyDiscount"></span>%):
                        </span>
                        <span x-text="'- ' + formatCurrency((summary?.roomSubtotal || pricing.basePrice) * loyaltyDiscount / 100)"></span>
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
                            <option value="promotion">Promotion</option>
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
                <button @click="confirmBooking()"  
                        :disabled="isSubmitting"
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed font-semibold order-1 sm:order-2 flex items-center justify-center gap-2">
                    <svg x-show="isSubmitting" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isSubmitting ? 'Processing...' : 'Confirm & Process Payment'"></span>
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
                        
                        @if($isExistingCustomer)
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <p class="text-sm text-gray-600">Customer Type:</p>
                            <p class="font-medium text-green-700">Existing Customer (Loyalty discount applied)</p>
                        </div>
                        @endif
                    </div>

                    <div class="flex flex-col gap-3">
                        <button @click="printReceipt()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Print Receipt
                        </button>
                        <button @click="sendToEmail()" x-show="email || formData.customerEmail" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                            Send to Email
                        </button>
                        @if($isExistingCustomer)
                        <a href="{{ route('admin.booking.existing-customer') }}" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium text-center">
                            New Existing Customer Booking
                        </a>
                        @else
                        <a href="{{ route('admin.booking.walk-in-booking') }}" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium text-center">
                            New Walk-in Booking
                        </a>
                        @endif
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
    // ======================================================
    // 1. GET CUSTOMER LOGIC FIRST
    // ======================================================
    const customerLogic = enhancedBookingForm();
    
    // ======================================================
    // 2. SERVICE TYPE MAPPING (Admin ID ↔ Customer Name)
    // ======================================================
    const serviceTypeMap = {
        // Admin ID → Customer Display Name
        'meeting': 'Meeting Room',
        'private_office': 'Private Office', 
        'event': 'Event Space',
        'coworking': 'Coworking Space',
        'virtual_office': 'Virtual Office',
        'sharing_room': 'Sharing Room'
    };
    
    const reverseServiceMap = {
        // Customer Name → Admin ID
        'Meeting Room': 'meeting',
        'Private Office': 'private_office',
        'Event Space': 'event',
        'Coworking Space': 'coworking',
        'Virtual Office': 'virtual_office',
        'Sharing Room': 'sharing_room'
    };
    
    // ======================================================
    // 3. MAIN FUNCTION RETURN
    // ======================================================
    return {
        // ======================================================
        // A. INHERIT ALL FROM CUSTOMER (Pricing, Calculations, etc)
        // ======================================================
        ...customerLogic,
        
        // ======================================================
        // B. ADMIN-SPECIFIC STATE (Override/Add)
        // ======================================================
        
        // Multi-step wizard
        steps: ['Customer', 'Service', 'Room', 'Payment'],
        currentStep: 1,
        bookingType: '{{ $booking_type ?? "walk_in" }}',
        
        // Location from admin user
        adminLocation: {
            city_id: {{ auth()->user()->city_id ?? 1 }},
            location_id: {{ auth()->user()->location_id ?? 1 }},
            location_name: '{{ auth()->user()->location->name ?? "Default Location" }}'
        },
        
        // Customer search (existing mode)
        customerSearch: '',
        customerResults: [],
        selectedCustomer: null,
        isSearching: false,
        loyaltyDiscount: 0,
        
        // Admin UI state
        showSuccessModal: false,
        generatedBookingId: '',
        errorMessage: '',
        successMessage: '',
        
        // Admin-specific filters
        roomFilter: {
            minCapacity: '',
            floor: '',
            sortBy: 'capacity_asc'
        },
        
        // ======================================================
        // C. OVERRIDE INITIALIZATION
        // ======================================================
        async initForm() {
            console.log('🔄 Admin Booking Initialization');
            
            try {
                // 1. Set location dari admin user
                this.setAdminLocation();
                
                // 2. Panggil customer init
                if (typeof customerLogic.initForm === 'function') {
                    await customerLogic.initForm.call(this);
                }
                
                // 3. Setup watchers khusus admin
                this.setupAdminWatchers();
                
                // 4. Load data khusus admin
                await this.loadAdminData();
                
                console.log('✅ Admin booking ready:', {
                    city: this.city,
                    location: this.location,
                    adminLocation: this.adminLocation
                });
                
            } catch (error) {
                console.error('❌ Admin init error:', error);
            }
        },
        
        // ======================================================
        // D. ADMIN LOCATION MANAGEMENT
        // ======================================================
        setAdminLocation() {
            // Override customer's city/location dengan admin location
            this.city = this.adminLocation.city_id;
            this.location = this.adminLocation.location_id;
            
            console.log('📍 Admin location set:', {
                city: this.city,
                location: this.location,
                name: this.adminLocation.location_name
            });
        },
        
        async loadAdminData() {
            try {
                // Load services untuk admin
                await this.loadAvailableServices();
                
                // Load facilities
                await this.loadAvailableFacilities();
                
                // Load additional data
                await Promise.all([
                    this.loadCoworkingPasses(),
                    this.loadVirtualOfficePackages(),
                    this.loadEventPackages()
                ]);
                
                // Set today's date
                this.today = new Date().toISOString().split('T')[0];
                this.bookingDate = this.today;
                this.formData.booking_date = this.today;
                
            } catch (error) {
                console.error('Error loading admin data:', error);
            }
        },
        
        // ======================================================
        // E. CUSTOMER MANAGEMENT (Existing Customer Mode)
        // ======================================================
        async searchCustomers() {
            if (this.customerSearch.length < 2) {
                this.customerResults = [];
                return;
            }
            
            this.isSearching = true;
            
            try {
                const url = `/booking/existing-customer/api/search?q=${encodeURIComponent(this.customerSearch)}`;
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.success && data.customers) {
                    this.customerResults = data.customers;
                } else {
                    this.showError(data.message || 'Search failed');
                    this.customerResults = [];
                }
            } catch (error) {
                console.error('Search error:', error);
                this.showError('Failed to search customers');
                this.customerResults = [];
            } finally {
                this.isSearching = false;
            }
        },
        
        async selectCustomer(customer) {
            this.selectedCustomer = customer;
            this.customerResults = [];
            
            // Auto-fill menggunakan customer field names
            this.namaLengkap = customer.name;
            this.phone = customer.phone;
            this.email = customer.email;
            
            // Juga sync ke formData untuk compatibility
            this.formData.userId = customer.id;
            this.formData.customerName = customer.name;
            this.formData.customerPhone = customer.phone;
            this.formData.customerEmail = customer.email;
            
            // Get loyalty discount
            await this.getLoyaltyDiscount(customer.id);
            
            this.showSuccess(`Customer selected: ${customer.name}`);
        },
        
        async getLoyaltyDiscount(userId) {
            try {
                const response = await fetch(`/booking/existing-customer/api/${userId}/loyalty-discount`);
                const data = await response.json();
                
                if (data.success && data.discount_percentage > 0) {
                    this.loyaltyDiscount = data.discount_percentage;
                    console.log('🎯 Loyalty discount:', this.loyaltyDiscount + '%');
                }
            } catch (error) {
                console.error('Loyalty discount error:', error);
            }
        },
        
        resetExistingCustomer() {
            this.selectedCustomer = null;
            this.customerSearch = '';
            this.customerResults = [];
            this.loyaltyDiscount = 0;
            
            // Clear customer fields
            this.namaLengkap = '';
            this.phone = '';
            this.email = '';
            this.formData.userId = null;
            this.formData.customerName = '';
            this.formData.customerPhone = '';
            this.formData.customerEmail = '';
        },
        
        switchToExistingMode() {
            this.bookingType = 'existing';
            if (this.phone || this.formData.customerPhone) {
                this.customerSearch = this.phone || this.formData.customerPhone;
                this.searchCustomers();
            }
        },
        
        switchBookingType() {
            this.bookingType = this.bookingType === 'existing' ? 'walk_in' : 'existing';
            
            if (this.bookingType === 'walk_in' && this.selectedCustomer) {
                // Keep data saat switch ke walk_in
                this.namaLengkap = this.selectedCustomer.name;
                this.phone = this.selectedCustomer.phone;
                this.email = this.selectedCustomer.email;
            }
            
            if (this.bookingType === 'existing') {
                this.resetExistingCustomer();
                if (this.phone || this.formData.customerPhone) {
                    this.customerSearch = this.phone || this.formData.customerPhone;
                    this.searchCustomers();
                }
            }
        },
        
        // ======================================================
        // F. SERVICE SELECTION & MAPPING
        // ======================================================
        async loadAvailableServices() {
            try {
                const response = await fetch('/booking/existing-customer/api/services/available');
                const data = await response.json();
                
                if (data.success) {
                    this.availableServices = data.services || [];
                    console.log('📦 Loaded services:', this.availableServices.length);
                }
            } catch (error) {
                console.error('Error loading services:', error);
                this.availableServices = [];
            }
        },
        
        async selectService(service) {
            console.log('🔧 Select service:', service);
            
            // 1. Map admin service ID to customer room type
            const customerRoomType = serviceTypeMap[service.id] || service.name;
            
            // 2. Call customer's selectRoomType method
            if (typeof customerLogic.selectRoomType === 'function') {
                customerLogic.selectRoomType.call(this, customerRoomType);
            }
            
            // 3. Store admin service ID
            this.formData.room_type = service.id;
            
            // 4. Reset related fields
            this.formData.paket = '';
            this.formData.room_id = '';
            this.formData.start_time = '';
            this.formData.jumlah_orang = '';
            
            // 5. Load packages untuk service ini
            await this.loadPackagesForService(service.id);
            
            // 6. Set defaults based on service type
            this.setServiceDefaults(service.id);
        },
        
        setServiceDefaults(serviceId) {
            switch(serviceId) {
                case 'meeting':
                    this.formData.jumlah_orang = 1;
                    this.formData.service_category_id = this.determineServiceCategoryId();
                    this.formData.meeting_duration = '1h';
                    this.meetingDuration = '1h';
                    break;
                    
                case 'private_office':
                    this.formData.private_office_duration = 'monthly';
                    this.privateOfficeDuration = 'monthly';
                    break;
                    
                case 'event':
                    this.formData.jumlah_orang = 10;
                    this.formData.event_duration = '4h';
                    this.eventDuration = '4h';
                    break;
                    
                case 'coworking':
                    this.formData.jumlah_orang = 1;
                    if (this.availableCoworkingPasses.length > 0) {
                        this.formData.coworking_pass = this.availableCoworkingPasses[0].id;
                        this.coworkingPass = this.availableCoworkingPasses[0].value;
                    }
                    break;
                    
                case 'virtual_office':
                    if (this.availableVirtualOfficePackages.length > 0) {
                        this.formData.virtual_office_package = this.availableVirtualOfficePackages[0].id;
                        this.virtualOfficePackage = this.availableVirtualOfficePackages[0].value;
                    }
                    this.formData.virtual_office_duration = 'monthly';
                    this.virtualOfficeDuration = 'monthly';
                    break;
                    
                case 'sharing_room':
                    this.formData.jumlah_orang = 1;
                    this.formData.sharing_room_duration = 'monthly';
                    this.sharingRoomDuration = 'monthly';
                    break;
            }
        },
        
        // ======================================================
        // G. PACKAGE & ROOM MANAGEMENT
        // ======================================================
        async loadPackagesForService(serviceType) {
            try {
                const response = await fetch(`/booking/existing-customer/api/services/${serviceType}/packages`);
                const data = await response.json();
                
                if (data.success) {
                    this.availablePackages = data.packages;
                    console.log('📦 Packages for', serviceType, ':', this.availablePackages.length);
                }
            } catch (error) {
                console.error('Error loading packages:', error);
                this.availablePackages = [];
            }
        },
        
        async loadAvailableTimeSlots() {
            if (!this.formData.room_type || !this.bookingDate) return;
            
            try {
                const params = new URLSearchParams({
                    service_type: this.formData.room_type,
                    date: this.bookingDate,
                    duration: this.formData.duration || 1
                });
                
                const response = await fetch(`/booking/existing-customer/api/available-times?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    this.availableTimeSlots = data.available_times;
                }
            } catch (error) {
                console.error('Error loading time slots:', error);
                this.availableTimeSlots = [];
            }
        },
        
        async loadAvailableRooms() {
            if (!this.selectedRoomType || !this.bookingDate) {
                this.availableRooms = [];
                return;
            }
            
            try {
                const params = new URLSearchParams({
                    location_id: this.location,
                    room_type: this.selectedRoomType,
                    booking_date: this.bookingDate,
                    start_time: this.startTime || ''
                });
                
                const response = await fetch(`/rooms?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    this.availableRooms = data.data || [];
                    console.log('📦 Available rooms:', this.availableRooms.length);
                }
            } catch (error) {
                console.error('Error loading rooms:', error);
                this.availableRooms = [];
            }
        },
        
        // ======================================================
        // H. PRICE CALCULATION (Extend Customer Logic)
        // ======================================================
        async calculatePrice() {
            console.log('💰 Admin calculate price called');
            
            try {
                // 1. First, call customer's calculatePrice
                if (typeof customerLogic.calculatePrice === 'function') {
                    const customerPrice = await customerLogic.calculatePrice.call(this);
                    
                    // 2. Store base price
                    this.calculatedPrice = customerPrice;
                    this.pricing.basePrice = customerPrice;
                    
                    // 3. Apply admin discounts
                    this.applyAdminDiscounts();
                    
                    // 4. Update summary
                    this.updateAdminSummary();
                    
                    console.log('💰 Final admin price:', {
                        base: customerPrice,
                        loyaltyDiscount: this.loyaltyDiscount,
                        manualDiscount: this.formData.discountValue,
                        final: this.calculatedPrice
                    });
                    
                    return this.calculatedPrice;
                }
            } catch (error) {
                console.error('Price calculation error:', error);
                this.calculatedPrice = 0;
                return 0;
            }
        },
        
        applyAdminDiscounts() {
            let finalPrice = this.calculatedPrice;
            
            // 1. Apply loyalty discount
            if (this.loyaltyDiscount > 0) {
                const discountAmount = (finalPrice * this.loyaltyDiscount) / 100;
                finalPrice -= discountAmount;
                console.log('🎯 Applied loyalty discount:', discountAmount);
            }
            
            // 2. Apply manual discount
            if (this.formData.discountType === 'percentage' && this.formData.discountValue > 0) {
                const discountAmount = (finalPrice * this.formData.discountValue) / 100;
                finalPrice -= discountAmount;
                console.log('🎯 Applied percentage discount:', discountAmount);
            } else if (this.formData.discountType === 'fixed' && this.formData.discountValue > 0) {
                finalPrice -= this.formData.discountValue;
                console.log('🎯 Applied fixed discount:', this.formData.discountValue);
            }
            
            // Ensure price doesn't go negative
            this.calculatedPrice = Math.max(0, Math.round(finalPrice));
        },
        
        updateAdminSummary() {
            // Update summary object for display
            if (!this.summary) {
                this.summary = {
                    subtotal: 0,
                    adminFee: 0,
                    deposit: 0,
                    total: 0,
                    details: ''
                };
            }
            
            this.summary.total = this.calculatedPrice;
            
            // Get price details based on service type
            this.priceDetails = this.getAdminPriceDetails();
        },
        
        getAdminPriceDetails() {
            if (!this.selectedRoomType) return '';
            
            switch(this.selectedRoomType) {
                case 'Meeting Room':
                    const people = this.numPeople || this.formData.jumlah_orang || 0;
                    const type = people > 6 ? 'Big Meeting' : 'Small Meeting';
                    return `${type} | ${people} people`;
                    
                case 'Private Office':
                    return `Private Office | ${this.privateOfficeDuration || 'Monthly'}`;
                    
                case 'Event Space':
                    return `Event Space | ${this.eventDuration || '4h'}`;
                    
                case 'Coworking Space':
                    return `Coworking | ${this.coworkingPass || 'Standard Pass'}`;
                    
                case 'Virtual Office':
                    return `Virtual Office | ${this.virtualOfficePackage || 'Basic Package'}`;
                    
                case 'Sharing Room':
                    return `Sharing Room | ${this.sharingRoomDuration || 'Monthly'}`;
                    
                default:
                    return this.selectedRoomType;
            }
        },
        
        calculateTotal() {
            // For admin, calculatedPrice already includes discounts
            return this.calculatedPrice || 0;
        },
        
        // ======================================================
        // I. FORM VALIDATION & SUBMISSION
        // ======================================================
        async confirmBooking() {
            console.log('📤 Admin confirm booking');
            
            // Validate
            if (!this.validateAdminBooking()) {
                return;
            }
            
            this.isSubmitting = true;
            
            try {
                // Prepare transaction data
                const transactionData = this.prepareTransactionData();
                
                console.log('📤 Sending booking data:', transactionData);
                
                const response = await fetch('/booking/existing-customer/api/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(transactionData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.generatedBookingId = data.booking_id || `ADMIN-${Date.now()}`;
                    this.showSuccessModal = true;
                    console.log('✅ Booking created:', this.generatedBookingId);
                } else {
                    throw new Error(data.message || 'Booking failed');
                }
                
            } catch (error) {
                console.error('❌ Booking error:', error);
                this.showError(error.message || 'Failed to create booking');
            } finally {
                this.isSubmitting = false;
            }
        },
        
        validateAdminBooking() {
            // Step 1 validation
            if (this.currentStep === 1) {
                if (this.bookingType === 'existing' && !this.selectedCustomer) {
                    this.showError('Please select a customer first');
                    return false;
                }
                
                if (this.bookingType === 'walk_in') {
                    if (!this.namaLengkap?.trim()) {
                        this.showError('Customer name is required');
                        return false;
                    }
                    if (!this.phone?.trim()) {
                        this.showError('Phone number is required');
                        return false;
                    }
                }
            }
            
            // Step 2 validation
            if (this.currentStep === 2 && !this.selectedRoomType) {
                this.showError('Please select a service type');
                return false;
            }
            
            // Additional validations based on service type
            if (this.selectedRoomType === 'Meeting Room' && (!this.numPeople || this.numPeople < 1)) {
                this.showError('Number of participants is required');
                return false;
            }
            
            return true;
        },
        
        prepareTransactionData() {
            // Map data to Transaction model format
            return {
                // Customer
                user_id: this.selectedCustomer?.id || null,
                nama_lengkap: this.namaLengkap,
                email: this.email,
                phone: this.phone,
                
                // Location
                city_id: this.city,
                location_id: this.location,
                
                // Service
                room_type: this.selectedRoomType,
                room_id: this.selectedRoom || this.formData.room_id,
                jumlah_orang: this.numPeople || this.formData.jumlah_orang,
                
                // Time
                booking_date: this.bookingDate,
                start_time: this.startTime || this.formData.start_time,
                
                // Package & Duration
                paket: this.getPaketValue(),
                bulan: this.getBulanValue(),
                tahun: this.getTahunValue(),
                minggu: this.getMingguValue(),
                jam: this.getJamValue(),
                hari: this.getHariValue(),
                
                // Pricing
                gross_amount: this.calculateTotal(),
                deposit: this.summary?.deposit || 0,
                lunch_total: this.lunchTotal || 0,
                
                // Payment
                payment_type: this.formData.payment_type || 'cash',
                status: 'settlement', // Admin bookings langsung settled
                
                // Additional
                notes: this.formData.notes || '',
                discount_reason: this.formData.discountReason || null,
                loyalty_discount_percentage: this.loyaltyDiscount || 0,
                
                // Admin specific
                created_by: {{ auth()->id() }},
                is_admin_booking: true
            };
        },
        
        // Helper methods for duration fields
        getPaketValue() {
            const mapping = {
                'Virtual Office': 'virtual_office',
                'Private Office': 'private_office',
                'Meeting Room': 'meeting',
                'Event Space': 'event',
                'Coworking Space': 'coworking',
                'Sharing Room': 'sharing'
            };
            return mapping[this.selectedRoomType] || this.selectedRoomType.toLowerCase();
        },
        
        getBulanValue() {
            if (this.selectedRoomType === 'Virtual Office' && this.virtualOfficeDuration === 'monthly') {
                return this.virtualOfficeMonths || 1;
            }
            if (this.selectedRoomType === 'Private Office' && this.privateOfficeDuration === 'monthly') {
                return this.quantity || 1;
            }
            return null;
        },
        
        getJamValue() {
            if (['Meeting Room', 'Coworking Space'].includes(this.selectedRoomType)) {
                return this.meetingDuration === 'custom' ? this.customMeetingHours : 
                       parseInt(this.meetingDuration) || 1;
            }
            return null;
        },
        
        // ======================================================
        // J. NAVIGATION & UI HELPERS
        // ======================================================
        nextStep() {
            if (this.currentStep === 1 && !this.validateStep1()) {
                return;
            }
            
            if (this.currentStep === 2 && !this.validateStep2()) {
                return;
            }
            
            // Skip room selection if not needed
            if (this.currentStep === 3 && !this.needsRoomSelection()) {
                this.currentStep = 4;
            } else {
                this.currentStep++;
            }
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        
        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
        
        validateStep1() {
            if (this.bookingType === 'existing' && !this.selectedCustomer) {
                this.showError('Please select a customer first');
                return false;
            }
            
            if (this.bookingType === 'walk_in') {
                const errors = this.validateCustomerInfo();
                if (errors.length > 0) {
                    this.showError(errors[0]);
                    return false;
                }
            }
            
            return true;
        },
        
        validateStep2() {
            if (!this.selectedRoomType) {
                this.showError('Please select a service type');
                return false;
            }
            
            // Additional validations
            if (this.needsBookingDate() && !this.bookingDate) {
                this.showError('Booking date is required');
                return false;
            }
            
            if (this.needsStartTime() && !this.startTime) {
                this.showError('Start time is required');
                return false;
            }
            
            return true;
        },
        
        validateCustomerInfo() {
            const errors = [];
            
            if (!this.namaLengkap?.trim()) {
                errors.push('Customer name is required');
            }
            
            if (!this.phone?.trim()) {
                errors.push('Phone number is required');
            } else if (!/^[0-9]{10,13}$/.test(this.phone)) {
                errors.push('Phone number must be 10-13 digits');
            }
            
            if (this.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) {
                errors.push('Invalid email format');
            }
            
            return errors;
        },
        
        // ======================================================
        // K. COMPUTED PROPERTIES & HELPERS
        // ======================================================
        get filteredRooms() {
            let rooms = [...(this.availableRooms || [])];
            
            // Filter by capacity
            if (this.roomFilter.minCapacity) {
                rooms = rooms.filter(room => room.capacity >= parseInt(this.roomFilter.minCapacity));
            }
            
            // Filter by floor
            if (this.roomFilter.floor) {
                rooms = rooms.filter(room => room.floor == this.roomFilter.floor);
            }
            
            // Sort
            switch(this.roomFilter.sortBy) {
                case 'capacity_asc':
                    rooms.sort((a, b) => a.capacity - b.capacity);
                    break;
                case 'capacity_desc':
                    rooms.sort((a, b) => b.capacity - a.capacity);
                    break;
                case 'room_number':
                    rooms.sort((a, b) => a.room_number?.localeCompare(b.room_number));
                    break;
            }
            
            return rooms;
        },
        
        get selectedRoomData() {
            if (!this.selectedRoom || !this.availableRooms) return null;
            
            const room = this.availableRooms.find(r => r.id === this.selectedRoom);
            return room || null;
        },
        
        needsRoomSelection() {
            return ['Private Office', 'Meeting Room', 'Event Space', 'Sharing Room'].includes(this.selectedRoomType);
        },
        
        needsStartTime() {
            return ['Meeting Room', 'Event Space', 'Private Office'].includes(this.selectedRoomType);
        },
        
        needsPackage() {
            return ['Meeting Room', 'Event Space', 'Virtual Office', 'Coworking Space'].includes(this.selectedRoomType);
        },
        
        needsNumberOfPeople() {
            return ['Meeting Room', 'Event Space', 'Coworking Space', 'Sharing Room'].includes(this.selectedRoomType);
        },
        
        showAdditionalInfo() {
            return this.selectedRoomType && 
                (this.needsNumberOfPeople() || 
                 ['Meeting Room', 'Event Space', 'Private Office'].includes(this.selectedRoomType));
        },
        
        showFacilities() {
            return ['Meeting Room', 'Event Space'].includes(this.selectedRoomType);
        },
        
        // ======================================================
        // L. EVENT HANDLERS
        // ======================================================
        onDateChange() {
            this.formData.start_time = '';
            this.availableTimeSlots = [];
            this.availableRooms = [];
            
            if (this.selectedRoomType) {
                this.loadAvailableTimeSlots();
                this.loadAvailableRooms();
            }
        },
        
        onTimeChange() {
            if (this.selectedRoomType && this.bookingDate) {
                this.loadAvailableRooms();
            }
        },
        
        onPeopleChange() {
            // Update service_category_id for meeting
            if (this.selectedRoomType === 'Meeting Room') {
                this.formData.service_category_id = this.determineServiceCategoryId();
            }
            
            // Recalculate price
            this.calculatePrice();
        },
        
        determineServiceCategoryId() {
            if (this.selectedRoomType !== 'Meeting Room') return null;
            const people = parseInt(this.numPeople) || 0;
            return people <= 6 ? 1 : 2;
        },
        
        // ======================================================
        // M. SETUP WATCHERS
        // ======================================================
        setupAdminWatchers() {
            // Watch for customer search
            this.$watch('customerSearch', (value) => {
                if (value.length >= 2 && this.bookingType === 'existing') {
                    this.searchCustomers();
                }
            });
            
            // Watch for discount changes
            this.$watch('formData.discountValue', () => {
                this.calculatePrice();
            });
            
            this.$watch('formData.discountType', () => {
                this.calculatePrice();
            });
            
            // Watch for date/time changes
            this.$watch('bookingDate', () => {
                this.onDateChange();
            });
            
            this.$watch('startTime', () => {
                this.onTimeChange();
            });
        },
        
        // ======================================================
        // N. UTILITY METHODS
        // ======================================================
        formatCurrency(value) {
            if (!value) return 'Rp 0';
            return 'Rp ' + parseInt(value).toLocaleString('id-ID');
        },
        
        showError(message) {
            this.errorMessage = message;
            setTimeout(() => this.errorMessage = '', 5000);
        },
        
        showSuccess(message) {
            this.successMessage = message;
            setTimeout(() => this.successMessage = '', 3000);
        },
        
        // ======================================================
        // O. RESET & CLEANUP
        // ======================================================
        resetForm() {
            // Reset customer logic
            if (typeof customerLogic.resetForm === 'function') {
                customerLogic.resetForm.call(this);
            }
            
            // Reset admin-specific
            this.currentStep = 1;
            this.selectedCustomer = null;
            this.customerSearch = '';
            this.customerResults = [];
            this.loyaltyDiscount = 0;
            
            // Reset formData
            this.formData = {
                userId: null,
                customerName: '',
                customerPhone: '',
                customerEmail: '',
                customerCompany: '',
                room_type: '',
                room_id: '',
                jumlah_orang: '',
                booking_date: this.today,
                start_time: '',
                paket: '',
                duration: '',
                coffee_break_option: '0',
                private_office_duration: '',
                facilities: [],
                voucherCode: '',
                voucherApplied: false,
                discountType: '',
                discountValue: 0,
                discountReason: '',
                notes: '',
                sendEmail: true,
                sendSMS: true,
                payment_type: 'cash',
                status: 'pending'
            };
        },
        
        // ======================================================
        // P. SUCCESS MODAL METHODS
        // ======================================================
        printReceipt() {
            const receiptContent = `
                <!DOCTYPE html>
                <html>
                <head><title>Booking Receipt</title></head>
                <body>
                    <h2>Admin Booking Receipt</h2>
                    <p>ID: ${this.generatedBookingId}</p>
                    <p>Customer: ${this.namaLengkap}</p>
                    <p>Service: ${this.selectedRoomType}</p>
                    <p>Amount: ${this.formatCurrency(this.calculateTotal())}</p>
                    <p>Location: ${this.adminLocation.location_name}</p>
                    <p>Date: ${new Date().toLocaleDateString('id-ID')}</p>
                </body>
                </html>
            `;
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(receiptContent);
            printWindow.document.close();
            printWindow.print();
        },
        
        createNewBooking() {
            this.showSuccessModal = false;
            this.currentStep = 1;
            this.resetForm();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };
}
</script>
@endpush

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
@endsection