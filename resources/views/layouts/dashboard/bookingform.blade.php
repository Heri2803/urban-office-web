@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">

    {{-- Content --}}
    <div class="flex-1 ml-0 md:ml-60 lg:ml-64 xl:ml-64 p-4 md:p-6 mb-12">

        {{-- User Status --}}
        <div class="mb-6 transition-all duration-700 ease-out opacity-100 translate-y-0">
            @include('layouts.components.userstatus', [
                'name' => 'Georgius Mario',
                'status' => 'Virtual Office'
            ])
        </div>

        {{-- Main Content Container --}}
        <div class="flex flex-col lg:flex-row gap-6">
            
            {{-- Room Card --}}
            <div class="w-full lg:w-1/3 order-1 lg:order-1 mb-6 lg:mb-0">
                @include('layouts.components.roomcard')
            </div>

            {{-- Booking Form --}}
            <div class="w-full lg:w-2/3 order-2 lg:order-2" x-data="enhancedBookingForm()" x-init="initForm()">
                <div class="bg-white shadow-lg hover:shadow-xl rounded-xl p-4 md:p-6">
                    
                    {{-- Header --}}
                    <div class="mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Form Booking</h2>
                        <p class="text-gray-600">Isi form di bawah untuk melakukan pemesanan ruang kerja</p>
                    </div>

                    {{-- Form --}}
                    <div class="space-y-6">

                        {{-- Kota & Detail Location --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    Pilih Kota <span class="text-red-500">*</span>
                                </label>
                                <select x-model="city" @change="onCityChange()" 
                                    class="form-select border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 hover:border-gray-400 bg-white">
                                    <option value="">-- Pilih Kota --</option>
                                    <template x-for="c in cities" :key="c.id">
                                        <option :value="c.id" x-text="c.name"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    Detail Lokasi <span class="text-red-500">*</span>
                                </label>
                                <select x-model="location" @change="onLocationChange()"
                                    :disabled="!city"
                                    class="form-select border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 hover:border-gray-400 bg-white disabled:bg-gray-100 disabled:cursor-not-allowed">
                                    <option value="">-- Pilih Lokasi --</option>
                                    <template x-for="loc in locations" :key="loc.id">
                                        <option :value="loc.id" x-text="loc.name"></option>
                                    </template>
                                </select>
                                <p x-show="!city" class="text-xs text-gray-500 mt-1">Pilih kota terlebih dahulu</p>
                            </div>
                        </div>

                        {{-- Tabs Pilih Jenis Ruang --}}
                        <div>
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

                        {{-- Dropdown Ruangan (Private Office, Meeting Room) --}}
                        <div x-show="['Private Office', 'Meeting Room', 'Sharing Room'].includes(selectedRoomType) && location" 
                             x-transition
                             class="grid grid-cols-1">
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    Pilih Ruangan <span class="text-red-500">*</span>
                                </label>
                                <select x-model="selectedRoom" @change="onRoomChange()"
                                    class="form-select border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 hover:border-gray-400 bg-white">
                                    
                                    <option value="">-- Pilih Ruangan --</option>
                                    
                                    <template x-if="availableRooms && availableRooms.length > 0">
                                        <template x-for="room in availableRooms" :key="room.id">
                                            <option 
                                                :value="room.id"
                                                :disabled="!room.is_selectable"
                                                x-text="`${room.room_number || 'Tanpa Nomor'} - Kapasitas ${room.capacity ?? 'tidak diketahui'} orang (${room.size_m2 ?? 'ukuran belum tersedia'} m²) - Status: ${room.status}`">
                                            </option>
                                        </template>
                                    </template>
                        
                                    <template x-if="availableRooms && availableRooms.length === 0">
                                        <option disabled value="">Tidak ada ruangan tersedia untuk lokasi ini</option>
                                    </template>
                        
                                </select>
                        
                                <!-- Detail ruangan -->
                                <p x-show="selectedRoom" class="text-xs text-gray-500 mt-1">
                                    <span x-text="getRoomDetails()"></span>
                                </p>
                            </div>
                        </div>
                        {{-- PRIVATE OFFICE Durasi --}}
                        <div x-show="selectedRoomType === 'Private Office'" x-transition>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">
                                Durasi Sewa <span class="text-red-500">*</span>
                            </label>
                            <select x-model="privateOfficeDuration"
                                    @change="fetchServicePrice(selectedRoom, selectedRoomType)"
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
                                    @change="calculatePrice()"
                                    class="form-select border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 bg-white">
                                <option value="">-- Pilih Paket --</option>
                                
                                {{-- Dynamic options dari database --}}
                                <template x-for="service in servicePrices" :key="service.id">
                                    <option :value="service.value"
                                            x-text="`${service.name} - Rp ${formatPrice(service.base_price)}`">
                                    </option>
                                </template>
                            </select>
                        
                            {{-- ✅ Dropdown Durasi --}}
                            <div class="mt-3">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    Durasi Sewa <span class="text-red-500">*</span>
                                </label>
                                <select x-model="virtualOfficeDuration"
                                        @change="calculatePrice()"
                                        class="form-select border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium bg-white">
                                    <option value="">-- Pilih Durasi --</option>
                                    <option value="monthly">Per Bulan</option>
                                    <option value="yearly">Per Tahun</option>
                                </select>
                            </div>
                        
                            {{-- ✅ Input Jumlah Bulan (jika pilih monthly) --}}
                            <div x-show="virtualOfficeDuration === 'monthly'" 
                                 x-transition 
                                 class="mt-3">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    Jumlah Bulan <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       min="1" 
                                       x-model.number="virtualOfficeMonths"
                                       @input="calculatePrice()"
                                       class="form-input border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 bg-white"
                                       placeholder="Masukkan jumlah bulan (min. 1)" />
                                <p class="text-xs text-gray-600 mt-1">* Minimum 1 bulan</p>
                            </div>
                        
                            {{-- ✅ Input Jumlah Tahun (jika pilih yearly) --}}
                            <div x-show="virtualOfficeDuration === 'yearly'" 
                                 x-transition 
                                 class="mt-3">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    Jumlah Tahun <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       min="1" 
                                       x-model.number="virtualOfficeYears"
                                       @input="calculatePrice()"
                                       class="form-input border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 bg-white"
                                       placeholder="Masukkan jumlah tahun" />
                                <p class="text-xs text-orange-600 mt-1">* Otomatis dikalikan 12 bulan per tahun</p>
                            </div>
                        </div>

                        {{-- COWORKING: Opsi Pass --}}
                        <div x-show="selectedRoomType === 'Coworking Space'" x-transition>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">
                                Pilih Pass <span class="text-red-500">*</span>
                            </label>
                        
                            <div class="space-y-2">
                                {{-- 🆕 Dynamic dari database --}}
                                <template x-for="pass in coworkingPasses" :key="pass.id">
                                    <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all"
                                        :class="coworkingPass === pass.value ? 'border-orange-500 bg-orange-50' : 'border-gray-300 hover:border-gray-400'">
                                        <input type="radio" 
                                               x-model="coworkingPass" 
                                               :value="pass.value" 
                                               class="mr-3 text-orange-500 focus:ring-orange-500">
                                        <div class="flex-1">
                                            <span class="font-semibold" x-text="pass.name"></span>
                                            <span class="text-gray-600 text-sm ml-2" x-text="formatPassPrice(pass)"></span>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        
                            {{-- ✅ Input Jumlah Jam (muncul hanya jika pilih Per Jam) --}}
                            <div x-show="coworkingPass === 'per_jam'" x-transition class="mt-4">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    Jumlah Jam <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       x-model.number="coworkingHours" 
                                       min="1" 
                                       max="5"
                                       class="form-input border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 bg-white"
                                       placeholder="Masukkan jumlah jam (1–5)">
                                <p class="text-xs text-gray-500 mt-1">* Maksimal 5 jam.</p>
                            </div>
                        </div>
                        {{-- EVENT SPACE: Durasi & Coffee Break --}}
                        <div x-show="selectedRoomType === 'Event Space'" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    Durasi <span class="text-red-500">*</span>
                                </label>
                                <select x-model="eventDuration"
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
                                class="form-select border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 bg-white">
                                <option value="">-- Pilih Opsi --</option>
                                <option value="none">Tanpa Coffee Break</option>
                                <option value="1x">1x Coffee Break</option>
                                <!-- Jika 8 jam atau lebih (termasuk custom >= 8 jam) -->
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
                                @change="fetchServicePrice()"
                                class="form-select border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium bg-white">
                            <option value="">-- Pilih Durasi --</option>
                            <option value="monthly">Per Bulan</option>
                            <option value="yearly">Per Tahun</option>
                        </select>
                    </div>
                        {{-- Input Jumlah (untuk yang perlu) & Tanggal Booking --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- 🆕 Field Jumlah Orang (sekarang support semua room type) --}}
                            <div x-show="shouldShowPeopleInput()">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    Jumlah Orang <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       x-model="numPeople"
                                       @input="validateCapacity()"
                                       min="1"
                                       placeholder="Masukkan jumlah orang"
                                       class="form-input border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium placeholder-gray-500 transition-all duration-200 hover:border-gray-400">
                                
                                <p x-show="capacityWarning"
                                   x-text="capacityWarning"
                                   class="text-xs text-red-600 mt-1"></p>
                            </div>
                            
                            {{-- 🆕 Input Jumlah Hari / Minggu / Bulan / Tahun (sekarang support semua room type) --}}
                            <div x-show="shouldShowQuantityInput()">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    <span x-text="getQuantityLabel()"></span> <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       x-model="quantity"
                                       min="1"
                                       :placeholder="getQuantityPlaceholder()"
                                       class="form-input border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium placeholder-gray-500 transition-all duration-200 hover:border-gray-400">
                            </div>
                            
                            {{-- Tanggal Booking --}}
                            <div x-show="needsBookingDate()">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    Tanggal Mulai <span class="text-red-500">*</span>
                                </label>
                                <input type="date" x-model="bookingDate" :min="getTodayDate()"
                                    class="form-input border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 hover:border-gray-400">
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    Waktu Mulai Akses <span class="text-red-500">*</span>
                                </label>
                                <input type="time"
                                       x-model="startTime"
                                       class="form-input border-2 border-gray-300 focus:border-orange-500 focus:ring 
                                              focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium
                                              placeholder-gray-500 transition-all duration-200 hover:border-gray-400">
                            </div>
                        </div>

                        {{-- Status PKP (Virtual Office only) --}}
                        <div x-show="selectedRoomType === 'Virtual Office'" x-transition>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">
                                Status PKP <span class="text-red-500">*</span>
                            </label>
                            <select x-model="statusPkp"
                                class="form-select border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium transition-all duration-200 hover:border-gray-400 bg-white">
                                <option value="">-- Pilih Status --</option>
                                <option value="Non PKP">Non PKP</option>
                                <option value="PKP">PKP</option>
                            </select>
                        </div>

                        {{-- Nama, Email, Phone --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" x-model="namaLengkap" placeholder="Nama Lengkap"
                                    class="form-input border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium placeholder-gray-500 transition-all duration-200 hover:border-gray-400">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" x-model="email" placeholder="email@example.com"
                                    class="form-input border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium placeholder-gray-500 transition-all duration-200 hover:border-gray-400">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-2">
                                    Nomor Telepon <span class="text-red-500">*</span>
                                </label>
                                <input type="text" x-model="phone" placeholder="08xxxxxxxxxx"
                                    class="form-input border-2 border-gray-300 focus:border-orange-500 focus:ring focus:ring-orange-200 rounded-lg p-3 w-full text-gray-800 font-medium placeholder-gray-500 transition-all duration-200 hover:border-gray-400">
                            </div>
                        </div>

                        {{-- Checkout Summary (Hidden until checkout clicked) --}}
                        <div x-show="showSummary" x-transition class="bg-gradient-to-br from-orange-50 to-orange-100 border-2 border-orange-300 rounded-xl p-6 space-y-4">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Ringkasan Pemesanan
                            </h3>
                            
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Jenis Layanan:</span>
                                    <span class="font-semibold" x-text="selectedRoomType"></span>
                                </div>
                                <div x-show="summary.details" class="flex justify-between">
                                    <span class="text-gray-700">Detail:</span>
                                    <span class="font-semibold text-right" x-text="summary.details"></span>
                                </div>
                                <div class="border-t border-orange-300 pt-2 mt-2"></div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Subtotal:</span>
                                    <span class="font-semibold" x-text="formatCurrency(summary.subtotal)"></span>
                                </div>
                                <div class="flex justify-between text-orange-600">
                                    <span>Admin Fee (10%):</span>
                                    <span class="font-semibold" x-text="formatCurrency(summary.adminFee)"></span>
                                </div>
                                <div class="flex justify-between text-red-600">
                                    <span>Potongan (10%):</span>
                                    <span class="font-semibold" x-text="formatCurrency(summary.adminFee)"></span>
                                </div>
                                <div x-show="summary.deposit > 0" class="flex justify-between text-blue-600">
                                    <span>Deposit:</span>
                                    <span class="font-semibold" x-text="formatCurrency(summary.deposit)"></span>
                                </div>
                                <div class="border-t-2 border-orange-400 pt-2 mt-2"></div>
                                <div class="flex justify-between text-lg">
                                    <span class="font-bold text-gray-800">Total Bayar:</span>
                                    <span class="font-bold text-orange-600" x-text="formatCurrency(summary.total)"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="space-y-3">
                            {{-- Checkout Button (Always visible) --}}
                            <button @click="handleCheckout()" type="button"
                                    :disabled="isSubmitting"
                                    x-show="!showSummary"
                                    class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                <span class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span x-text="isSubmitting ? 'Memproses...' : 'Checkout'"></span>
                                </span>
                            </button>

                            {{-- Mulai Sewa Button (Only visible after checkout) --}}
                            <button @click="submitBooking()" type="button"
                                    :disabled="isSubmitting"
                                    x-show="showSummary"
                                    x-transition
                                    class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold py-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                <span class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span x-text="isSubmitting ? 'Memproses Pembayaran...' : 'Mulai Sewa'"></span>
                                </span>
                            </button>

                            {{-- Edit/Cancel Button --}}
                            <button @click="cancelCheckout()" type="button"
                                    x-show="showSummary"
                                    x-transition
                                    class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 rounded-lg transition-all duration-300">
                                Edit Pemesanan
                            </button>
                        </div>

                        {{-- Info Tambahan --}}
                        <div class="text-center mt-4">
                            <p class="text-gray-600 text-sm px-2 leading-relaxed">
                                Pastikan data yang Anda masukkan sudah benar sebelum melakukan checkout
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Alpine.js - Load once saja, cek dulu apakah sudah ada --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

{{-- Midtrans Snap JS --}}
<script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
function enhancedBookingForm() {
    return {
        // Form States
        city: '',
        location: '',
        selectedRoomType: '',
        selectedRoom: '',
        quantity: '',
        numPeople: '',
        bookingDate: '',
        startTime: '',
        namaLengkap: '',
        email: '',
        phone: '',
        statusPkp: '',
        
        // Room Type Specific
        privateOfficeDuration: '',
        virtualOfficePackage: '',
        virtualOfficeDuration: '', 
        virtualOfficeMonths: 1, 
        virtualOfficeYears: 1,
        coworkingHours: 1,
        coworkingPass: '',
        eventDuration: '',
        eventCoffeeBreak: '',
        meetingDuration: '',
        meetingCoffeeBreak: '',
        customMeetingHours: '',
        sharingRoomDuration: '',
        
        // Data from Backend
        cities: [],
        locations: [],
        availableRooms: [],
        roomDetails: null,
        servicePriceDetails: {},
        servicePrices: [], 
        eventSpacePrices: [],
        
        // Room Types
        roomTypes: ['Virtual Office', 'Private Office', 'Meeting Room', 'Event Space', 'Coworking Space', 'Sharing Room'],
        
        // Coworking Passes
        coworkingPasses: [],
        
        // UI States
        showSummary: false,
        isSubmitting: false,
        capacityWarning: '',
        
        // Summary
        summary: {
            subtotal: 0,
            adminFee: 0,
            deposit: 0,
            total: 0,
            details: ''
        },

        // Initialize
        async initForm() {
            await this.loadCities();
        },

        // Load cities from Backend
        async loadCities() {
            try {
                const response = await fetch('/cities');
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const result = await response.json();
                
                // Check API success flag
                if (!result.success) {
                    throw new Error(result.message || 'Failed to load cities');
                }
                
                // Extract data from response
                this.cities = result.data || [];
                
            } catch (error) {
                console.error('❌ Error loading cities:', error);
                
                // User-friendly fallback
                this.cities = [
                    { id: 1, name: 'Surabaya' },
                    { id: 2, name: 'Jakarta' },
                    { id: 3, name: 'Bandung' }
                ];
                
                // Optional: Show toast notification if you implement it
                // this.toast('Menggunakan data kota default', 'warning');
            }
        },

        // Load Locations based on City
        async onCityChange() {
            this.location = '';
            this.locations = [];
            this.availableRooms = [];
            this.selectedRoom = '';
            
            if (!this.city) return;
            
            try {
                const response = await fetch(`/locations?city_id=${this.city}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.message || 'Failed to load locations');
                }
                
                this.locations = result.data || [];
                
                // Check if no locations found
                if (result.count === 0) {
                    alert('Tidak ada lokasi tersedia untuk kota ini');
                }
                
            } catch (error) {
                console.error('❌ Error loading locations:', error);
                alert('Gagal memuat lokasi: ' + error.message);
                this.locations = [];
            }
        },

        // Load Available Rooms based on Location and Room Type
        async onLocationChange() {
            this.availableRooms = [];
            this.selectedRoom = '';
            
            if (!this.location || !this.selectedRoomType) return;
            
            // Only load rooms for Private Office and Meeting Room
            if (!['Private Office', 'Meeting Room', 'Sharing Room'].includes(this.selectedRoomType)) return;
            
            try {
                // Encode room type for URL
                const encodedRoomType = encodeURIComponent(this.selectedRoomType);
                const response = await fetch(
                    `/rooms?location_id=${this.location}&room_type=${encodedRoomType}`
                );
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.message || 'Failed to load rooms');
                }
                
                this.availableRooms = result.data || [];
                
                
                // Check if no rooms available
                if (result.count === 0) {
                    alert(`Tidak ada ruangan ${this.selectedRoomType} tersedia di lokasi ini`);
                }
                
            } catch (error) {
                console.error('❌ Error loading rooms:', error);
                alert('Gagal memuat daftar ruangan: ' + error.message);
                this.availableRooms = [];
            }
        },

        // Select Room Type
        selectRoomType(roomType) {
            this.selectedRoomType = roomType;
            this.resetRoomSpecificFields();
            this.showSummary = false;
            
            // Load rooms if applicable
            if (this.location && ['Private Office', 'Meeting Room', 'Sharing Room'].includes(roomType)) {
                this.onLocationChange();
            }
            
            // ✅ Pastikan ini ada
            console.log('📤 Dispatching room-type-selected:', roomType);
            this.$dispatch('room-type-selected', { roomType: roomType });
        },

        // Get Room Details
        async onRoomChange() {
            if (!this.selectedRoom) {
                this.roomDetails = null;
                this.servicePriceDetails = {};
                return;
            }
        
            try {
                const response = await fetch(`/rooms/${this.selectedRoom}`);
        
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
        
                const result = await response.json();
        
                if (!result.success) {
                    throw new Error(result.message || 'Failed to load room details');
                }
        
                this.roomDetails = result.data;
        
                // ✅ Dispatch event untuk update room card dengan gambar spesifik (jika ada)
                if (this.roomDetails.images && this.roomDetails.images.length > 0) {
                    this.$dispatch('room-images-loaded', { 
                        roomType: this.selectedRoomType,
                        images: this.roomDetails.images,
                        benefits: this.roomDetails.benefits || this.getRoomBenefits()
                    });
                }
        
                // Panggil fetchServicePrice jika layanan sudah dipilih
                if (this.selectedRoomType || this.selectedService) {
                    await this.fetchServicePrice();
                } else {
                    console.log('ℹ️ Menunggu user memilih jenis layanan...');
                }
        
            } catch (error) {
                console.error('❌ Error loading room details:', error);
                alert('Gagal memuat detail ruangan: ' + error.message);
                this.roomDetails = null;
            }
        },
        
        getRoomDetails() {
            if (!this.roomDetails) return '';
            
            return this.roomDetails.full_description || 
                   `Lantai ${this.roomDetails.floor} - ${this.roomDetails.size_m2}m² - Kapasitas ${this.roomDetails.capacity} orang`;
        },
        
        // ✅ Helper function untuk generate benefits dari room details
        getRoomBenefits() {
            if (!this.roomDetails) return [];
            
            const benefits = [];
            
            if (this.roomDetails.capacity) {
                benefits.push(`Kapasitas hingga ${this.roomDetails.capacity} orang`);
            }
            
            if (this.roomDetails.size_m2) {
                benefits.push(`Luas ruangan ${this.roomDetails.size_m2} m²`);
            }
            
            if (this.roomDetails.floor) {
                benefits.push(`Terletak di lantai ${this.roomDetails.floor}`);
            }
            
            // Tambahkan benefit default
            benefits.push('Fasilitas premium dengan standar internasional');
            
            return benefits;
        },

        // Validate Capacity
        validateCapacity() {
            this.capacityWarning = '';
        
            if (!this.numPeople || !this.roomDetails) return;
        
            const qty = parseInt(this.numPeople);
            const capacity = parseInt(this.roomDetails.capacity);
        
            if (qty > capacity) {
                this.capacityWarning = `⚠️ Jumlah orang melebihi kapasitas ruangan (max: ${capacity} orang)`;
            }
        },
        
        //Helper untuk cek kapan input quantity (hari/minggu/bulan) muncul
        shouldShowPeopleInput() {
            if (this.selectedRoomType === 'Private Office') {
                return ['hourly', 'daily', 'weekly', 'monthly', 'yearly'].includes(this.privateOfficeDuration);
            }
            if (this.selectedRoomType === 'Meeting Room') {
                return ['1h', '4h', '8h', 'custom', 'daily', 'weekly', 'monthly'].includes(this.meetingDuration);
            }
            if (this.selectedRoomType === 'Event Space') {
                return ['4h', '8h', 'daily', 'weekly', 'monthly'].includes(this.eventDuration);
            }
        
            // ✅ Tambahkan Coworking
            if (this.selectedRoomType === 'Coworking Space') {
                return true; // selalu tampil
            }
            
            if (this.selectedRoomType === 'Sharing Room') {
                return ['monthly', 'yearly'].includes(this.sharingRoomDuration);
            }

            return false;
        },
        
        //Helper untuk cek kapan input quantity (hari/minggu/bulan) muncul
        shouldShowQuantityInput() {
            if (this.selectedRoomType === 'Private Office') {
                return ['hourly','daily', 'weekly', 'monthly', 'yearly'].includes(this.privateOfficeDuration);
            }
            if (this.selectedRoomType === 'Meeting Room') {
                return ['daily', 'weekly', 'monthly'].includes(this.meetingDuration);
            }
            if (this.selectedRoomType === 'Event Space') {
                return ['daily', 'weekly', 'monthly'].includes(this.eventDuration);
            }
            if (this.selectedRoomType === 'Sharing Room') {
                return ['monthly', 'yearly'].includes(this.sharingRoomDuration);
            }
            return false;
        },

        // Helper: Get quantity label
        getQuantityLabel() {
            if (this.selectedRoomType === 'Private Office') {
                if (this.privateOfficeDuration === 'hourly') return 'Jumlah Jam';
                if (this.privateOfficeDuration === 'daily') return 'Jumlah Hari';
                if (this.privateOfficeDuration === 'weekly') return 'Jumlah Minggu';
                if (this.privateOfficeDuration === 'monthly') return 'Jumlah Bulan';
                if (this.privateOfficeDuration === 'yearly') return 'Jumlah Tahun';
            }
        
            if (this.selectedRoomType === 'Meeting Room') {
                const durationMap = {
                    daily: 'Jumlah Hari',
                    weekly: 'Jumlah Minggu',
                    monthly: 'Jumlah Bulan'
                };
        
                if (['daily', 'weekly', 'monthly'].includes(this.meetingDuration)) {
                    return durationMap[this.meetingDuration];
                }
                return '';
            }
        
            // 🆕 Tambahkan Event Space
            if (this.selectedRoomType === 'Event Space') {
                const durationMap = {
                    daily: 'Jumlah Hari',
                    weekly: 'Jumlah Minggu',
                    monthly: 'Jumlah Bulan'
                };
        
                if (['daily', 'weekly', 'monthly'].includes(this.eventDuration)) {
                    return durationMap[this.eventDuration];
                }
                return '';
            }
            
            if (this.selectedRoomType === 'Sharing Room') {
                const map = {
                    monthly: 'Jumlah Bulan',
                    yearly: 'Jumlah Tahun'
                };
                return map[this.sharingRoomDuration] || '';
            }
            return 'Jumlah Orang';
        },

        getQuantityPlaceholder() {
            if (this.selectedRoomType === 'Private Office') {
                if (this.privateOfficeDuration === 'hourly') return 'Masukkan jumlah jam';
                if (this.privateOfficeDuration === 'daily') return 'Masukkan jumlah hari';
                if (this.privateOfficeDuration === 'weekly') return 'Masukkan jumlah minggu';
                if (this.privateOfficeDuration === 'monthly') return 'Masukkan jumlah bulan';
                if (this.privateOfficeDuration === 'yearly') return 'Masukkan jumlah tahun';
            }
        
            if (this.selectedRoomType === 'Meeting Room') {
                if (this.meetingDuration === 'daily') return 'Masukkan jumlah hari';
                if (this.meetingDuration === 'weekly') return 'Masukkan jumlah minggu';
                if (this.meetingDuration === 'monthly') return 'Masukkan jumlah bulan';
            }
        
            // 🆕 Tambahkan Event Space
            if (this.selectedRoomType === 'Event Space') {
                if (this.eventDuration === 'daily') return 'Masukkan jumlah hari';
                if (this.eventDuration === 'weekly') return 'Masukkan jumlah minggu';
                if (this.eventDuration === 'monthly') return 'Masukkan jumlah bulan';
            }
            
            if (this.selectedRoomType === 'Sharing Room') {
                return this.sharingRoomDuration === 'monthly'
                    ? 'Masukkan jumlah bulan'
                    : 'Masukkan jumlah tahun';
            }
        
            return 'Masukkan jumlah orang';
        },

        // Helper: Check if needs booking date
        needsBookingDate() {
            return true;
        },

        // Helper: Get today's date
        getTodayDate() {
            return new Date().toISOString().split('T')[0];
        },

        // Reset room-specific fields
        resetRoomSpecificFields() {
            this.selectedRoom = '';
            this.quantity = '';
            this.privateOfficeDuration = '';
            this.virtualOfficePackage = '';
            this.virtualOfficeDuration = ''; 
            this.virtualOfficeMonths = 1;    
            this.virtualOfficeYears = 1;
            this.coworkingPass = '';
            this.eventDuration = '';
            this.eventCoffeeBreak = '';
            this.meetingDuration = '';
            this.meetingCoffeeBreak = '';
            this.capacityWarning = '';
        },

        // Calculate Price
        async calculatePrice() {
            let subtotal = 0;
            let details = '';
        
            try {
                switch (this.selectedRoomType) {
                    case 'Virtual Office':
                        subtotal = this.calculateVirtualOfficePrice();
                        details = this.getVirtualOfficeDetails();
                        break;
                    case 'Private Office':
                        subtotal = this.calculatePrivateOfficePrice();
                        details = this.getPrivateOfficeDetails();
                        break;
                    case 'Meeting Room':
                        subtotal = await this.calculateMeetingRoomPrice(); 
                        details = this.getMeetingRoomDetails();
                        break;
                    case 'Event Space':
                        subtotal = this.calculateEventSpacePrice();
                        details = this.getEventSpaceDetails();
                        break;
                    case 'Coworking Space':
                        subtotal = this.calculateCoworkingPrice();
                        details = this.getCoworkingDetails();
                        break;
                    case 'Sharing Room': // ✅ TAMBAHKAN INI
                        subtotal = this.calculateSharingRoomPrice();
                        details = this.getSharingRoomDetails();
                        break;
                }
        
                const adminFee = subtotal * 0.10;
                const deposit = this.calculateDeposit(subtotal);
                const total = subtotal + deposit;
        
                this.summary = {
                    subtotal,
                    adminFee,
                    deposit,
                    total,
                    details
                };
        
                console.log("💰 Ringkasan harga:", this.summary); // untuk debugging
        
                return true;
            } catch (error) {
                console.error('Error calculating price:', error);
                alert('Terjadi kesalahan dalam perhitungan harga');
                return false;
            }
        },

        // Init function
        async init() {
            await this.fetchServicePrices(); // Virtual Office
            await this.fetchCoworkingPasses(); // Coworking
            await this.fetchEventSpacePrices(); // Event Space
        },
        
        // 🆕 Fetch data dari API
        async fetchServicePrices() {
            try {
                const response = await fetch("{{ url('/virtual-office-packages') }}"); // Sesuaikan dengan route Anda
                if (!response.ok) throw new Error('Failed to fetch');
                this.servicePrices = await response.json();
            } catch (error) {
                console.error('Error fetching service prices:', error);
                // Optional: tampilkan error ke user
            }
        },
        
        // 🆕 Helper: Format harga ke Rupiah
        formatPrice(price) {
            return new Intl.NumberFormat('id-ID').format(price);
        },
        
        // ✅ Kalkulasi harga Virtual Office (updated)
        calculateVirtualOfficePrice() {
            if (!this.virtualOfficePackage) return 0;
        
            if (!Array.isArray(this.servicePrices) || this.servicePrices.length === 0) return 0;
        
            const selected = this.servicePrices.find(s =>
                s.value && s.value.toLowerCase() === this.virtualOfficePackage.toLowerCase()
            );
        
            if (!selected) return 0;
        
            const basePrice = Number(selected.base_price) || 0;
        
            // ✅ Hitung total bulan berdasarkan durasi
            let totalMonths = 0;
            
            if (this.virtualOfficeDuration === 'monthly') {
                // Per bulan: langsung ambil input bulan
                totalMonths = Number(this.virtualOfficeMonths) || 1;
            } else if (this.virtualOfficeDuration === 'yearly') {
                // Per tahun: tahun × 12
                const years = Number(this.virtualOfficeYears) || 1;
                totalMonths = years * 12;
            } else {
                // Default jika belum pilih durasi
                totalMonths = 1;
            }
        
            const subtotal = basePrice * totalMonths;
        
            console.log('💰 Virtual Office Calculation:');
            console.log('  - Base Price (per bulan):', basePrice);
            console.log('  - Duration Type:', this.virtualOfficeDuration);
            console.log('  - Total Months:', totalMonths);
            console.log('  - Subtotal:', subtotal);
        
            return subtotal;
        },
        // ✅ Get details Virtual Office (updated)
        getVirtualOfficeDetails() {
            if (!this.virtualOfficePackage) return '';
        
            const selected = this.servicePrices.find(s =>
                s.value && s.value.toLowerCase() === this.virtualOfficePackage.toLowerCase()
            );
        
            if (!selected) return '';
        
            // ✅ Tentukan detail durasi
            let durationText = '';
            
            if (this.virtualOfficeDuration === 'monthly') {
                const months = Number(this.virtualOfficeMonths) || 1;
                durationText = `${months} bulan`;
            } else if (this.virtualOfficeDuration === 'yearly') {
                const years = Number(this.virtualOfficeYears) || 1;
                const totalMonths = years * 12;
                durationText = `${years} tahun (${totalMonths} bulan)`;
            }
        
            return `Paket ${selected.name} - ${durationText}`;
        },
        // PRIVATE OFFICE PRICING (integrated with calculatePrice)
        calculatePrivateOfficePrice() {
            // ✅ Cek apakah data dari API sudah ada
            if (!this.servicePriceDetails || !this.servicePriceDetails.base_price) {
                console.warn('⚠️ Service price details belum ada dari API');
                return 0;
            }
        
            if (!this.roomDetails || !this.roomDetails.room_number) {
                console.warn('⚠️ Room details tidak ditemukan');
                return 0;
            }
        
            const qty = parseInt(this.quantity) || 1;
            
            // ✅ GANTI HARDCODE dengan data dari API
            // servicePriceDetails sudah berisi: base_price, coffee_break_price, deposit, dll
            let price = this.servicePriceDetails.base_price || 0;
        
            // ✅ Hitung total dengan quantity
            const totalPrice = price * qty;
        
            console.log('💰 Private Office Price Calculation:', {
                room_number: this.roomDetails.room_number,
                size_m2: this.roomDetails.size_m2,
                duration: this.privateOfficeDuration,
                duration_type: this.servicePriceDetails.duration_type,
                base_price: price,
                quantity: qty,
                total: totalPrice,
                deposit: this.servicePriceDetails.deposit || 0
            });
        
            // Pastikan tidak NaN
            if (isNaN(totalPrice) || totalPrice <= 0) {
                console.warn(`⚠️ Harga tidak valid untuk Room ${this.roomDetails.room_number} (${this.privateOfficeDuration})`);
                return 0;
            }
        
            return totalPrice;
        },

        getPrivateOfficeDetails() {
            // ✅ Mapping untuk display label
            const durationLabels = {
                'hourly': 'per Jam',
                'daily': 'per Hari',
                'weekly': 'per Minggu',
                'monthly': 'per Bulan',
                'yearly': 'per Tahun'
            };
            
            const label = durationLabels[this.privateOfficeDuration] || '';
            const roomNum = this.roomDetails?.room_number || '';
            const qty = parseInt(this.quantity) || 1;
            
            let details = `Ruang ${roomNum} - ${label}`;
            
            // Tambahkan quantity jika lebih dari 1
            if (qty > 1) {
                details = `${qty}x ${details}`;
            }
            
            // ✅ Tambahkan info coffee break jika ada
            if (this.privateOfficeCoffeeBreak && this.privateOfficeCoffeeBreak !== 'none') {
                details += ` + Coffee Break ${this.privateOfficeCoffeeBreak}`;
            }
            
            return details;
        },
        
        //wrapper function untuk memanggil function fetchServicePrice()
        async getServicePrice(serviceCategoryId = null, duration = null, coffeeBreakOption = null) {
            try {
                console.log('🔄 getServicePrice() called with:', {
                    serviceCategoryId,
                    duration,
                    coffeeBreakOption
                });
        
                // ✅ Set parameter ke state jika ada
                if (duration !== null) {
                    // Temporary override duration untuk fetch
                    const originalDuration = this.meetingDuration;
                    const originalCustomHours = this.customMeetingHours;
                    
                    if (duration === 1 || duration === 4 || duration === 8) {
                        this.meetingDuration = `${duration}h`;
                    } else {
                        this.meetingDuration = 'custom';
                        this.customMeetingHours = duration;
                    }
                    
                    // Set coffee break jika ada
                    if (coffeeBreakOption !== null) {
                        const originalCoffee = this.meetingCoffeeBreak;
                        
                        if (coffeeBreakOption === 0) {
                            this.meetingCoffeeBreak = 'none';
                        } else if (coffeeBreakOption === 1) {
                            this.meetingCoffeeBreak = '1x';
                        } else if (coffeeBreakOption === 2) {
                            this.meetingCoffeeBreak = '2x';
                        }
                        
                        // Fetch dengan parameter baru
                        await this.fetchServicePrice();
                        
                        // Restore original
                        this.meetingCoffeeBreak = originalCoffee;
                    } else {
                        await this.fetchServicePrice();
                    }
                    
                    // Restore original duration
                    this.meetingDuration = originalDuration;
                    this.customMeetingHours = originalCustomHours;
                } else {
                    // Fetch dengan parameter current
                    await this.fetchServicePrice();
                }
        
                // Return data dari state
                return this.servicePriceDetails;
        
            } catch (error) {
                console.error('❌ Error getServicePrice:', error);
                return null;
            }
        },

        // MEETING ROOM PRICING
        async calculateMeetingRoomPrice() {
          try {
            const people = Number(this.numPeople) || 0;
            const isBigMeeting = people > 6;
            const serviceCategoryId = isBigMeeting ? 2 : 1;
            const coffeeOption = this.meetingCoffeeBreak;
            const coffeeBreak = coffeeOption !== 'none' ? 10000 : 0;
            const hasCoffeeBreak = coffeeOption !== 'none';
            
            // 🔥 Konversi coffeeOption ke angka untuk API
            // 'none' = 0, '1' = 1, '2' = 2
            const coffeeBreakParam = coffeeOption === 'none' ? 0 : parseInt(coffeeOption) || 1;
        
            let totalHarga = 0;
            let duration = 0;
        
            // 🔥 Tentukan durasi berdasarkan pilihan
            if (this.meetingDuration === 'custom') {
              duration = parseInt(this.customMeetingHours) || 1;
            } else if (this.meetingDuration === 'daily' || this.meetingDuration === 'weekly' || this.meetingDuration === 'monthly') {
              duration = 8; // Pakai harga 8 jam sebagai base
            } else {
              // Format: "1h", "4h", "8h" → ambil angkanya
              duration = parseInt(this.meetingDuration) || 1;
            }
        
            // === CASE 1: CUSTOM DURATION ===
            if (this.meetingDuration === 'custom') {
              if (isBigMeeting) {
                // --- BIG MEETING ---
                if (duration < 4) {
                  console.log(`🔹 Big Meeting custom <4 jam (${duration} jam)`);
                  
                  if (duration === 1) {
                    // 🔥 KHUSUS: Big Meeting 1 jam - ambil base_price + tambah CB manual
                    const hourlyData = await this.getServicePrice(serviceCategoryId, 1, 0);
                    const hourlyBase = Number(hourlyData?.base_price) || 0;
                    
                    // Tambah coffee break manual per orang
                    // 1x CB = +10000, 2x CB = +20000
                    const cbPerOrang = coffeeBreakParam * 10000;
                    
                    totalHarga = (hourlyBase + cbPerOrang) * people;
                    console.log(`💡 Breakdown: (${hourlyBase} + ${cbPerOrang} CB) × ${people} orang = ${totalHarga}`);
                  } else {
                    // Durasi 2-3 jam
                    const hourlyData = await this.getServicePrice(serviceCategoryId, 1, 0);
                    const hourlyBase = Number(hourlyData?.base_price) || 0;
                    totalHarga = hourlyBase * duration * people;
                  }
                } else if (duration >= 4 && duration < 8) {
                  console.log(`🔹 Big Meeting custom 4–7 jam (${duration} jam)`);
                  const data4Jam = await this.getServicePrice(serviceCategoryId, 4, 0);
                  const data1Jam = await this.getServicePrice(serviceCategoryId, 1, 0);
                  const base4Jam = Number(data4Jam?.base_price) || 0;
                  const hourlyBase = Number(data1Jam?.base_price) || 0;
        
                  const sisaJam = duration - 4;
                  totalHarga = (base4Jam + (hourlyBase * sisaJam)) * people;
                } else if (duration >= 8) {
                  console.log(`🔹 Big Meeting >=8 jam (${duration} jam)`);
                  const data8Jam = await this.getServicePrice(serviceCategoryId, 8, 0);
                  const base8Jam = Number(data8Jam?.base_price) || 0;
                  totalHarga = base8Jam * people;
                }
              } else {
                // --- SMALL MEETING ---
                if (duration <= 4) {
                  console.log(`🔹 Small Meeting ≤4 jam (${duration} jam)`);
                  const dataDurasi = await this.getServicePrice(serviceCategoryId, duration, coffeeBreakParam);
                  const basePrice = hasCoffeeBreak 
                    ? Number(dataDurasi?.coffee_break_price) || 0 
                    : Number(dataDurasi?.base_price) || 0;
                  totalHarga = basePrice;
                } else {
                  console.log(`🔹 Small Meeting custom >4 jam (${duration} jam)`);
                  const data4Jam = await this.getServicePrice(serviceCategoryId, 4, coffeeBreakParam);
                  const data1Jam = await this.getServicePrice(serviceCategoryId, 1, 0);
                  
                  const base4Jam = hasCoffeeBreak 
                    ? Number(data4Jam?.coffee_break_price) || 0 
                    : Number(data4Jam?.base_price) || 0;
                  const hourlyBase = Number(data1Jam?.base_price) || 0;
        
                  const sisaJam = duration - 4;
                  totalHarga = base4Jam + (hourlyBase * sisaJam);
        
                  // Tambah coffee break sekali saja
                  totalHarga += coffeeBreak;
                }
              }
            } else {
              // === CASE 2: STANDARD DURATION (termasuk DAILY/WEEKLY/MONTHLY) ===
              
              if (this.meetingDuration === 'daily' || this.meetingDuration === 'weekly' || this.meetingDuration === 'monthly') {
                // 🔥 DURASI PER HARI/MINGGU/BULAN
                console.log(`🔹 ${this.meetingDuration.toUpperCase()} meeting`);
                
                // Ambil harga 8 jam sebagai base
                const data8Jam = await this.getServicePrice(serviceCategoryId, 8, coffeeBreakParam);
                const basePrice = Number(data8Jam?.base_price) || 0;
                const coffeePrice = Number(data8Jam?.coffee_break_price) || 0;
                
                // Tentukan harga per hari (8 jam)
                const hargaPerHari = hasCoffeeBreak ? coffeePrice : basePrice;
                
                // 🔥 Ambil quantity dan konversi ke jumlah hari
                const quantityInput = parseInt(this.quantity) || 1;
                let multiplier = quantityInput;
                
                // Konversi minggu/bulan ke hari
                if (this.meetingDuration === 'weekly') {
                  multiplier = quantityInput * 7; // minggu → hari
                } else if (this.meetingDuration === 'monthly') {
                  multiplier = quantityInput * 30; // bulan → hari
                }
                
                console.log('🔍 quantity input:', quantityInput);
                console.log('🔍 Multiplier (hari):', multiplier);
                
                if (isBigMeeting) {
                  totalHarga = hargaPerHari * multiplier * people;
                } else {
                  totalHarga = hargaPerHari * multiplier;
                }
                
                const unit = this.meetingDuration === 'daily' ? 'hari' : this.meetingDuration === 'weekly' ? 'minggu' : 'bulan';
                console.log(`💡 Breakdown: ${hargaPerHari} × ${multiplier} hari (${quantityInput} ${unit}) × ${isBigMeeting ? people + ' orang' : '1'} = ${totalHarga}`);
                
              } else {
                // STANDARD DURATION (1h, 4h, 8h)
                console.log(`🔹 Standard meeting (${duration} jam)`);
                
                if (isBigMeeting && duration === 1) {
                  // 🔥 KHUSUS: Big Meeting 1 jam standard - sama seperti custom
                  const data = await this.getServicePrice(serviceCategoryId, 1, 0);
                  const hourlyBase = Number(data?.base_price) || 0;
                  
                  // Tambah coffee break manual per orang
                  const cbPerOrang = coffeeBreakParam * 10000;
                  
                  totalHarga = (hourlyBase + cbPerOrang) * people;
                  console.log(`💡 Breakdown: (${hourlyBase} + ${cbPerOrang} CB) × ${people} orang = ${totalHarga}`);
                } else {
                  // Standard meeting untuk durasi lain atau Small Meeting
                  const data = await this.getServicePrice(serviceCategoryId, duration, coffeeBreakParam);
                  const basePrice = Number(data?.base_price) || 0;
                  const coffeePrice = Number(data?.coffee_break_price) || 0;
        
                  if (isBigMeeting) {
                    totalHarga = (hasCoffeeBreak ? coffeePrice : basePrice) * people;
                  } else {
                    totalHarga = hasCoffeeBreak ? coffeePrice : basePrice;
                  }
                }
              }
            }
        
            console.log("💰 Total harga akhir:", totalHarga);
            return totalHarga;
        
          } catch (error) {
            console.error('Error calculateMeetingRoomPrice:', error);
            return 0;
          }
        },

        getMeetingRoomDetails() {
          const people = parseInt(this.numPeople) || 0;
          const type = people <= 6 ? 'Small Meeting' : 'Big Meeting';
          const coffeeLabels = { 'none': 'Tanpa CB', '1x': '1x CB', '2x': '2x CB' };
        
          // Tentukan teks durasi berdasarkan meetingDuration
          let durationText = '';
          switch (this.meetingDuration) {
            case '1h':
              durationText = '1 Jam';
              break;
            case '4h':
              durationText = '4 Jam';
              break;
            case '8h':
              durationText = '8 Jam';
              break;
            case 'custom':
              durationText = `${this.customMeetingHours || 0} Jam`;
              break;
            case 'daily':
              durationText = `${this.quantity || 0} Hari`;
              break;
            case 'weekly':
              durationText = `${this.quantity || 0} Minggu`;
              break;
            case 'monthly':
              durationText = `${this.quantity || 0} Bulan`;
              break;
            default:
              durationText = '-';
          }
        
          return `${type} - ${people} orang - ${durationText} - ${coffeeLabels[this.meetingCoffeeBreak] || ''}`;
        },
        
        // Get price berdasarkan duration dan coffee break
        getEventSpacePrice(duration, coffeeBreak) {
            // Safety check
            if (!this.eventSpacePrices || this.eventSpacePrices.length === 0) {
                console.warn('Event space prices not loaded yet');
                return 0;
            }
            
            // Untuk daily/weekly/monthly, ambil harga dari 8h
            const lookupDuration = ['daily', 'weekly', 'monthly'].includes(duration) ? '8h' : duration;
            
            // Filter berdasarkan duration
            const items = this.eventSpacePrices.filter(p => p.value === lookupDuration);
            
            if (!items || items.length === 0) {
                console.warn(`No prices found for duration: ${lookupDuration}`);
                return 0;
            }
            
            // 🆕 Filter berdasarkan coffee_break_count
            if (coffeeBreak === 'none') {
                // Ambil yang coffee_break_count === null (tanpa coffee break)
                const item = items.find(p => p.coffee_break_count === null || p.coffee_break_count === 0);
                if (item) {
                    return parseFloat(item.base_price || 0);
                }
                // Fallback: ambil item pertama yang punya base_price
                const fallback = items.find(p => p.base_price);
                return fallback ? parseFloat(fallback.base_price) : 0;
            } else if (coffeeBreak === '1x') {
                // Ambil yang coffee_break_count = 1
                const item = items.find(p => p.coffee_break_count === 1);
                return item ? parseFloat(item.coffee_break_price || item.base_price) : 0;
            } else if (coffeeBreak === '2x') {
                // Ambil yang coffee_break_count = 2
                const item = items.find(p => p.coffee_break_count === 2);
                return item ? parseFloat(item.coffee_break_price || item.base_price) : 0;
            }
            
            return 0;
        },
        
        // ✅ Kalkulasi Event Space
        calculateEventSpacePrice() {
            // Safety check
            if (!this.eventSpacePrices || this.eventSpacePrices.length === 0) {
                console.warn('Event space prices not loaded');
                return 0;
            }
            
            const people = parseInt(this.numPeople) || 0;
            const duration = this.eventDuration;
            const coffee = this.eventCoffeeBreak;
            
            if (!duration || !coffee || people === 0) return 0;
            
            // Get price per person dari database
            const pricePerPerson = this.getEventSpacePrice(duration, coffee);
            
            if (pricePerPerson === 0) {
                console.warn('Price per person is 0 for:', { duration, coffee });
                return 0;
            }
            
            // Untuk daily/weekly/monthly, kalikan dengan days multiplier
            if (duration === 'daily') {
                const days = parseInt(this.quantity) || 1;
                return pricePerPerson * people * days;
            } else if (duration === 'weekly') {
                const weeks = parseInt(this.quantity) || 1;
                return pricePerPerson * people * weeks * 7;
            } else if (duration === 'monthly') {
                const months = parseInt(this.quantity) || 1;
                return pricePerPerson * people * months * 30;
            }
            
            // Untuk hourly (1h, 4h, 8h)
            return pricePerPerson * people;
        },
        
        // ✅ Get Event Space details
         getEventSpaceDetails() {
            const people = parseInt(this.numPeople) || 0;
            const duration = this.eventDuration;
            const coffee = this.eventCoffeeBreak;
            
            const durationLabels = {
                '1h': '1 Jam',
                '4h': '4 Jam',
                '8h': '8 Jam',
                'daily': 'Hari',
                'weekly': 'Minggu',
                'monthly': 'Bulan'
            };
            
            const coffeeLabels = { 
                'none': 'Tanpa CB', 
                '1x': '1x CB', 
                '2x': '2x CB' 
            };
            
            let durationText = durationLabels[duration] || duration;
            
            // Untuk daily/weekly/monthly, tambahkan quantity
            if (duration === 'daily') {
                const days = parseInt(this.quantity) || 1;
                durationText = `${days} ${durationText}`;
            } else if (duration === 'weekly') {
                const weeks = parseInt(this.quantity) || 1;
                const totalDays = weeks * 7;
                durationText = `${weeks} ${durationText} (${totalDays} hari)`;
            } else if (duration === 'monthly') {
                const months = parseInt(this.quantity) || 1;
                const totalDays = months * 30;
                durationText = `${months} ${durationText} (${totalDays} hari)`;
            }
            
            return `${people} orang - ${durationText} - ${coffeeLabels[coffee]}`;
        },
        
        // 🆕 Format harga untuk display di radio button
        formatPassPrice(pass) {
            const formattedPrice = this.formatPrice(pass.price);
            if (pass.duration_type === 'month') {
                return `Rp ${formattedPrice}/bulan`;
            }
            return `Rp ${formattedPrice}`;
        },

        // Kalkulasi harga Coworking (tidak hardcode)
        calculateCoworkingPrice() {
            if (!this.coworkingPass) return 0;
                    
            const selectedPass = this.coworkingPasses.find(
                pass => pass.value === this.coworkingPass
            );
            
            if (!selectedPass) return 0;
            
            const basePrice = parseFloat(selectedPass.price);
            
            // ✅ Per Jam
            if (selectedPass.value === 'per_jam') {
                const hours = Math.min(this.coworkingHours, 5);
                return basePrice * hours * this.numPeople;
            }
            
            // ✅ Pass lain
            return basePrice * this.numPeople;
        },


        // Get Coworking details
        getCoworkingDetails() {
            if (!this.coworkingPass) return '';
            
            const pass = this.coworkingPasses.find(p => p.value === this.coworkingPass);
            if (!pass) return '';
            
            if (pass.value === 'per_jam') {
                return `${pass.name} (${this.coworkingHours} jam)`;
            }
            return pass.name;
        },
        // Calculate Sharing Room Price
        calculateSharingRoomPrice() {
            if (!this.servicePriceDetails || !this.servicePriceDetails.base_price) {
                console.warn('⚠️ Service price details belum tersedia');
                return 0;
            }
        
            const basePrice = Number(this.servicePriceDetails.base_price) || 0;
            const numPeople = Number(this.numPeople) || 0;
            
            // ✅ Gunakan this.quantity untuk durasi (bulan/tahun)
            const duration = Number(this.quantity) || 1;
            
            // Formula: base_price × jumlah_orang × durasi
            const subtotal = basePrice * numPeople * duration;
        
            console.log('💰 Sharing Room Calculation:');
            console.log('  - Base Price:', basePrice);
            console.log('  - Jumlah Orang:', numPeople);
            console.log('  - Durasi (quantity):', duration, this.sharingRoomDuration === 'monthly' ? 'Bulan' : 'Tahun');
            console.log('  - Subtotal:', subtotal);
        
            return subtotal;
        },
        // Get Sharing Room Details for Summary
        getSharingRoomDetails() {
            const numPeople = Number(this.numPeople) || 0;
            const duration = Number(this.quantity) || 1;
            
            let durationText = '';
            if (this.sharingRoomDuration === 'monthly') {
                durationText = `${duration} Bulan`;
            } else if (this.sharingRoomDuration === 'yearly') {
                durationText = `${duration} Tahun`;
            }
        
            return `${numPeople} orang - ${durationText}`;
        },
         // Update fetch function dengan better error handling
        async fetchEventSpacePrices() {
            try {
                const response = await fetch('/event-space-prices');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                this.eventSpacePrices = data;
            } catch (error) {
                console.error('❌ Error fetching event space prices:', error);
                this.eventSpacePrices = []; // Set empty array sebagai fallback
            }
        },
        
        // Fetch Coworking Passes
        async fetchCoworkingPasses() {
            try {
                const response = await fetch('/coworking-passes');
                if (!response.ok) throw new Error('Failed to fetch');
                this.coworkingPasses = await response.json();
            } catch (error) {
                console.error('Error fetching coworking passes:', error);
            }
        },
        // Fungsi untuk ambil detail room
        async fetchServicePrice() {
            try {
                if (!this.roomDetails) {
                    console.warn('⚠️ Room details belum ada');
                    return;
                }
        
                const serviceType = this.selectedRoomType;
        
                if (!serviceType) {
                    console.warn('⚠️ Layanan belum dipilih, skip fetch service price');
                    return;
                }
        
                console.log('🔍 Service type:', serviceType);
                console.log('🔍 Room type_id:', this.roomDetails.room_type_id);
        
                // Tentukan room_type_id
                let roomTypeId;
                
                if (this.roomDetails.room_type_id === null || 
                    this.roomDetails.room_type_id === 'null' || 
                    !this.roomDetails.room_type_id) {
                    
                    if (serviceType === 'Private Office') {
                        roomTypeId = 1;
                    } else if (serviceType === 'Meeting Room') {
                        roomTypeId = 2;
                    } else if (serviceType === 'Sharing Room') { // ✅ TAMBAHKAN INI
                        roomTypeId = 6;
                    } else {
                        console.warn('⚠️ Unknown service type:', serviceType);
                        return;
                    }
                } else {
                    roomTypeId = this.roomDetails.room_type_id;
                }
        
                console.log('✅ Determined room_type_id:', roomTypeId);
        
                // ✅ Mapping duration type dari UI ke database format
                const durationTypeMap = {
                    'hourly': 'hour',
                    'daily': 'day',
                    'weekly': 'week',
                    'monthly': 'month',
                    'yearly': 'year'
                };
        
                // ✅ Hitung durasi DAN duration_type
                let duration = 1;
                let durationType = 'hour'; // Default
                
                if (roomTypeId === 2) { // Meeting Room
                    durationType = 'hour'; // Meeting Room selalu hour
                    if (this.meetingDuration === 'custom') {
                        duration = Number(this.customMeetingHours) || 1;
                    } else {
                        duration = parseInt(this.meetingDuration) || 1;
                    }
                } else if (roomTypeId === 1) { // Private Office
                    // ✅ Ambil dari dropdown dan convert ke format database
                    const selectedDuration = this.privateOfficeDuration;
                    durationType = durationTypeMap[selectedDuration] || 'month'; // Default month
                    
                    // ✅ Duration untuk Private Office fixed 1 (sesuai harga per unit)
                    duration = 1;
                    
                    console.log('🔍 Selected duration UI:', selectedDuration);
                    console.log('🔍 Mapped duration_type:', durationType);
                } else if (roomTypeId === 6) { // ✅ TAMBAHKAN INI - Sharing Room
                    const selectedDuration = this.sharingRoomDuration; // 'monthly' atau 'yearly'
                    durationType = durationTypeMap[selectedDuration] || 'month';
                     duration = Number(this.quantity) || 1;
                    // Duration adalah jumlah bulan/tahun yang diinput user
                    if (selectedDuration === 'monthly') {
                        duration = Number(this.sharingRoomMonths) || 1;
                    } else if (selectedDuration === 'yearly') {
                        duration = Number(this.sharingRoomYears) || 1;
                    }
                    
                    console.log('🔍 Sharing Room duration UI:', selectedDuration);
                    console.log('🔍 Duration value:', duration);
                    console.log('🔍 Mapped duration_type:', durationType);
                }
        
                // Coffee break option (hanya untuk meeting room)
                let coffeeOption = 0;
                if (roomTypeId === 2) {
                    if (this.meetingCoffeeBreak === '1x') coffeeOption = 1;
                    else if (this.meetingCoffeeBreak === '2x') coffeeOption = 2;
                }
        
                // Jumlah orang
                const people = Number(this.numPeople) || 0;
        
                // Buat params
                const params = {
                    room_type_id: roomTypeId,
                    room_id: this.selectedRoom,
                    duration: duration,
                    duration_type: durationType, // ✅ Sudah di-convert ke format database
                    coffee_break_option: coffeeOption,
                    people: people
                };
        
                // Tambahkan service_category_id HANYA jika Meeting Room
                if (roomTypeId === 2) {
                    const serviceCategoryId = people > 6 ? 2 : 1;
                    params.service_category_id = serviceCategoryId;
                }
        
                const url = `/get-service-price?` + new URLSearchParams(params);
        
                console.log("📡 Fetching harga dari:", url);
        
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
        
                if (!data) {
                    throw new Error('Data harga kosong');
                }
        
                this.servicePriceDetails = data;
        
                console.log("✅ Data harga diterima:", data);
        
            } catch (error) {
                console.error('❌ Error getServicePrice:', error);
                this.servicePriceDetails = {};
            }
        },

        // Fungsi untuk hitung deposit
        calculateDeposit(subtotal) {
            if (this.selectedRoomType === 'Virtual Office') {
                if (!this.virtualOfficePackage) return 0;
        
                const selectedService = this.servicePrices.find(service =>
                    service.value &&
                    service.value.toLowerCase() === this.virtualOfficePackage.toLowerCase()
                );
        
                return selectedService ? Number(selectedService.deposit) || 0 : 0;
            }
        
            if (this.selectedRoomType === 'Private Office') {
                return Number(this.servicePriceDetails?.deposit) || 0;
            }
        
            // ✅ TAMBAHKAN INI
            if (this.selectedRoomType === 'Sharing Room') {
                return Number(this.servicePriceDetails?.deposit) || 0;
            }
        
            return 0;
        },


        // Format Currency
        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },

        // Handle Checkout
        handleCheckout() {
            if (!this.validateForm()) {
                return;
            }
            
            if (!this.calculatePrice()) {
                return;
            }
            
            this.showSummary = true;
            
            // Scroll to summary
            setTimeout(() => {
                const summaryEl = document.querySelector('[x-show="showSummary"]');
                if (summaryEl) {
                    summaryEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 100);
        },

        // Cancel Checkout
        cancelCheckout() {
            this.showSummary = false;
        },

        // Submit Booking (Midtrans)
        async submitBooking() {
            if (this.isSubmitting) return;
            
            this.isSubmitting = true;
            
            try {
                const bookingData = this.prepareBookingData();
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    throw new Error('CSRF token not found');
                }
                
                const response = await fetch('/transactions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(bookingData)
                });
                
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    
                    console.error('=== ERROR RESPONSE ===');
                    console.error('Status:', response.status);
                    console.error('Error Data:', errorData);
                    
                    // Tampilkan validation errors dari Laravel
                    if (errorData.errors) {
                        const errorMessages = Object.entries(errorData.errors)
                            .map(([field, messages]) => `${field}: ${messages.join(', ')}`)
                            .join('\n');
                        
                        console.error('Validation Errors:\n', errorMessages);
                        throw new Error('Validation error:\n' + errorMessages);
                    }
                    
                    throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success && result.snap_token) {
                    if (typeof snap === 'undefined') {
                        throw new Error('Midtrans Snap tidak tersedia');
                    }
                    
                    snap.pay(result.snap_token, {
                        onSuccess: (response) => {
                            console.log('Payment success:', response);
                            alert('Pembayaran berhasil!');
                            window.location.href = '/dashboard/invoice/';
                        },
                        onPending: (response) => {
                            console.log('Payment pending:', response);
                            alert('Pembayaran pending! Silakan selesaikan pembayaran Anda.');
                            window.location.href = '/dashboard/invoice/';
                        },
                        onError: (response) => {
                            console.error('Payment error:', response);
                            alert('Terjadi kesalahan pembayaran! Silakan coba lagi.');
                        },
                        onClose: () => {
                            console.log('Payment popup closed');
                        }
                    });
                } else {
                    throw new Error(result.message || 'Gagal generate payment token');
                }
                
            } catch (error) {
                console.error('Booking error:', error);
                alert('Terjadi kesalahan: ' + error.message);
            } finally {
                this.isSubmitting = false;
            }
        },

        // Prepare Booking Data
        prepareBookingData() {
        console.log('=== DEBUG BOOKING DATA ===');
        console.log('City ID:', this.city);
        console.log('Location ID:', this.location);
        console.log('Room Type:', this.selectedRoomType);
        console.log('Virtual Office Package:', this.virtualOfficePackage);
        console.log('Summary:', this.summary);
        
        const data = {
            city_id: this.city || null,
            location_id: this.location || null,
            room_type: this.selectedRoomType || null,
            nama_lengkap: this.namaLengkap || '',
            email: this.email || '',
            phone: this.phone || '',
            booking_date: this.bookingDate || null,
            start_time: this.startTime || null,
            subtotal: this.summary?.subtotal || 0,
            admin_fee: this.summary?.adminFee || 0,
            deposit: this.summary?.deposit || 0,
            total_amount: this.summary?.total || 0,
        };
        
        // Add room_id if selected
        if (this.selectedRoom) {
            data.room_id = this.selectedRoom;
        }
        
        // Add quantity as jumlah_orang
        if (this.numPeople) {
            data.jumlah_orang = parseInt(this.numPeople);
        }
        
        // ✅ Room-specific data dengan MAPPING yang benar
        switch (this.selectedRoomType) {
            case 'Virtual Office':
            // ✅ Gunakan servicePrices karena data VO ada di sini
            const voList = Array.isArray(this.servicePrices) ? this.servicePrices : [];
        
            const pkg = voList.find(
                p => p.value && p.value.toLowerCase() === this.virtualOfficePackage.toLowerCase()
            );
            
            if (pkg) {
                data.service_category_id = pkg.service_category_id;
        
                // ✅ UBAH INI - Tentukan paket dan durasi berdasarkan pilihan user
                if (this.virtualOfficeDuration === 'monthly') {
                    data.paket = 'monthly';
                    data.bulan = parseInt(this.virtualOfficeMonths) || 1;
                    
                    console.log('📤 Sending monthly:', data.bulan, 'bulan');
                } else if (this.virtualOfficeDuration === 'yearly') {
                    data.paket = 'yearly';
                    data.tahun = parseInt(this.virtualOfficeYears) || 1;
                    
                    console.log('📤 Sending yearly:', data.tahun, 'tahun');
                } else {
                    // Fallback jika durasi belum dipilih
                    console.warn('⚠️ Virtual Office duration belum dipilih, default ke monthly');
                    data.paket = 'monthly';
                    data.bulan = 1;
                }
            } else {
                console.warn('Virtual Office package not found:', this.virtualOfficePackage);
            }
        
            data.status_pkp = this.statusPkp || null;
        
            break;

            case 'Private Office':

                const durationType = this.privateOfficeDuration; // daily / weekly / monthly / yearly
                const durationValue = parseInt(this.quantity) || 1; // ✅ jumlah hari/minggu/bulan/tahun
            
                console.log('📦 Private Office Duration:', { 
                    type: durationType, 
                    value: durationValue 
                });
            
                switch (durationType) {
                    case 'hourly':
                        data.paket = 'hourly';
                        data.jam = durationValue;
                        break;
            
                    case 'daily':
                        data.paket = 'daily';
                        data.hari = durationValue;
                        break;
            
                    case 'weekly':
                        data.paket = 'weekly';
                        data.minggu = durationValue;   // ✅ SIMPAN LANGSUNG MINGGU (kolom baru)
                        break;
            
                    case 'monthly':
                        data.paket = 'monthly';
                        data.bulan = durationValue;
                        break;
            
                    case 'yearly':
                        data.paket = 'yearly';
                        data.tahun = durationValue;
                        break;
            
                    default:
                        data.paket = 'monthly';
                        data.bulan = 1;
                }
            
            break;

            case 'Meeting Room':
                // ✅ Meeting Room duration
                let meetingDuration = 1;
                if (this.meetingDuration === 'custom') {
                    meetingDuration = Number(this.customMeetingHours) || 1;
                } else {
                    meetingDuration = parseInt(this.meetingDuration) || 1;
                }
                
                data.paket = 'hourly';
                data.jam = meetingDuration;
                break;
                
            case 'Event Space':
                data.jumlah_orang = parseInt(this.numPeople) || 0;
                
                // Mapping durasi Event Space berdasarkan nilai HTML
                switch (this.eventDuration) {
                    case '4h':
                        data.paket = 'hourly';
                        data.jam = 4;
                        break;
                        
                    case '8h':
                        data.paket = 'hourly';
                        data.jam = 8;
                        break;
                        
                    case 'daily':
                        data.paket = 'daily';
                        data.hari = parseInt(this.quantity) || 1;
                        break;
                        
                    case 'weekly':
                        data.paket = 'weekly';
                        data.minggu = parseInt(this.quantity) || 1;
                        break;
                        
                    case 'monthly':
                        data.paket = 'monthly';
                        data.bulan = parseInt(this.quantity) || 1;
                        break;
                        
                    default:
                        console.error('Unknown event duration:', this.eventDuration);
                        data.paket = 'hourly';
                        data.jam = null; // Akan trigger validation error
                        break;
                }
                
                // Coffee break option
                if (this.coffeeBreakOption) {
                    data.coffee_break_option = parseInt(this.coffeeBreakOption) || 0;
                }
                break;
                
            case 'Coworking Space':
                const pass = this.coworkingPasses.find(p => p.value === this.coworkingPass);
            
                if (pass) {
                    data.service_category_id = pass.service_category_id; 
                }
            
                data.jumlah_orang = parseInt(this.numPeople) || 1;
            
                switch (this.coworkingPass) {
                    case 'per_jam':
                        data.paket = 'hourly';
                        data.jam = parseInt(this.coworkingHours) || 1;
                        break;
            
                    case 'daily_pass_6_jam':
                    case 'daily_pass_8_jam':
                    case 'student_pass_6_jam':
                    case 'student_pass_8_jam':
                        data.paket = 'hourly';
                        data.jam = (this.coworkingPass.includes('6')) ? 6 : 8;
                        break;
            
                    case 'membership_bulanan':
                        data.paket = 'monthly';
                        data.bulan = 1;
                        break;
            
                    default:
                        console.warn("Unknown coworking pass:", this.coworkingPass);
                        data.paket = 'hourly'; // fallback default
                        data.jam = 1;
                        break;
                }
            break;
            
            case 'Sharing Room':
                data.jumlah_orang = parseInt(this.numPeople) || 1;
                const srDur = this.sharingRoomDuration;  // monthly / yearly
                const srQty = parseInt(this.quantity) || 1;
                data.paket = srDur;
                if (srDur === 'monthly') {
                    data.bulan = srQty;
                } else if (srDur === 'yearly') {
                    data.tahun = srQty;
                }
                break;
        }
        
        console.log('=== DATA YANG AKAN DIKIRIM ===');
        console.log(JSON.stringify(data, null, 2));
        
        return data;
    },

        // Validate Form
        validateForm() {
            // Basic validation
            if (!this.city) {
                alert('Pilih kota terlebih dahulu!');
                return false;
            }
            
            if (!this.location) {
                alert('Pilih detail lokasi terlebih dahulu!');
                return false;
            }
            
            if (!this.selectedRoomType) {
                alert('Pilih jenis layanan terlebih dahulu!');
                return false;
            }
            
            if (!this.namaLengkap?.trim()) {
                alert('Nama lengkap wajib diisi!');
                return false;
            }
            
            if (!this.email?.trim()) {
                alert('Email wajib diisi!');
                return false;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(this.email)) {
                alert('Format email tidak valid!');
                return false;
            }
            
            if (!this.phone?.trim()) {
                alert('Nomor telepon wajib diisi!');
                return false;
            }
            
            const phoneRegex = /^(\+62|62|0)[0-9]{9,12}$/;
            if (!phoneRegex.test(this.phone)) {
                alert('Format nomor telepon tidak valid! (Contoh: 08123456789)');
                return false;
            }
            
            // Room-specific validation
            if (['Private Office', 'Meeting Room'].includes(this.selectedRoomType)) {
                if (!this.selectedRoom) {
                    alert('Pilih ruangan terlebih dahulu!');
                    return false;
                }
            }
            
            if (this.needsBookingDate() && !this.bookingDate) {
                alert('Tanggal booking wajib diisi!');
                return false;
            }
            
            if (this.needsBookingDate()) {
                const bookingDateObj = new Date(this.bookingDate);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (bookingDateObj < today) {
                    alert('Tanggal booking tidak boleh di masa lalu!');
                    return false;
                }
            }
            
            // Validate based on room type
            switch (this.selectedRoomType) {
                case 'Virtual Office':
                    if (!this.virtualOfficePackage) {
                        alert('Pilih paket Virtual Office!');
                        return false;
                    }
                    if (!this.statusPkp) {
                        alert('Pilih status PKP!');
                        return false;
                    }
                    // Validasi untuk monthly
                    if (this.virtualOfficeDuration === 'monthly') {
                        if (!this.virtualOfficeMonths || this.virtualOfficeMonths < 1) {
                            alert('Jumlah bulan minimal 1!');
                            return false;
                        }
                    }
                    // Validasi untuk yearly
                    if (this.virtualOfficeDuration === 'yearly') {
                        if (!this.virtualOfficeYears || this.virtualOfficeYears < 1) {
                            alert('Jumlah tahun minimal 1!');
                            return false;
                        }
                    }
                    break;
                    
                case 'Private Office':
                    if (!this.privateOfficeDuration) {
                        alert('Pilih durasi sewa!');
                        return false;
                    }
                    if (this.shouldShowPeopleInput() && !this.numPeople) {
                        alert(`${this.getQuantityLabel()} wajib diisi!`);
                        return false;
                    }
                    if (this.quantity && parseInt(this.quantity) < 1) {
                        alert(`${this.getQuantityLabel()} harus minimal 1!`);
                        return false;
                    }
                    // Check capacity warning
                    if (this.capacityWarning) {
                        const confirm = window.confirm('Jumlah orang melebihi kapasitas ruangan. Lanjutkan?');
                        if (!confirm) return false;
                    }
                    break;
                    
                case 'Meeting Room':
                    if (!this.meetingDuration) {
                        alert('Pilih durasi meeting!');
                        return false;
                    }
                    if (!this.meetingCoffeeBreak) {
                        alert('Pilih opsi coffee break!');
                        return false;
                    }
                    
                    // Validasi numPeople (bukan quantity!)
                    if (!this.numPeople) {
                        alert('Jumlah orang wajib diisi!');
                        return false;
                    }
                    const meetingPeople = parseInt(this.numPeople);
                    if (meetingPeople < 1 || meetingPeople > 18) {
                        alert('Jumlah orang untuk Meeting Room: 1-18 orang!');
                        return false;
                    }
                    
                    // Validasi quantity hanya untuk daily, weekly, monthly
                    if (['daily', 'weekly', 'monthly'].includes(this.meetingDuration)) {
                        if (!this.quantity) {
                            const label = this.getQuantityLabel();
                            alert(`${label} wajib diisi!`);
                            return false;
                        }
                        if (parseInt(this.quantity) < 1) {
                            alert(`${this.getQuantityLabel()} minimal 1!`);
                            return false;
                        }
                    }
                    break;
                    
                case 'Event Space':
                    if (!this.eventDuration) {
                        alert('Pilih durasi event!');
                        return false;
                    }
                    if (!this.eventCoffeeBreak) {
                        alert('Pilih opsi coffee break!');
                        return false;
                    }
                    if (!this.numPeople) {
                        alert('Jumlah orang wajib diisi!');
                        return false;
                    }
                    if (parseInt(this.numPeople) < 1) {
                        alert('Jumlah orang harus minimal 1!');
                        return false;
                    }
                    // Validasi quantity (hanya untuk daily/weekly/monthly)
                    if (this.eventDuration === 'daily' || this.eventDuration === 'weekly' || this.eventDuration === 'monthly') {
                        if (!this.quantity) {
                            alert('Jumlah ' + this.getQuantityLabel() + ' wajib diisi!');
                            return false;
                        }
                        if (parseInt(this.quantity) < 1) {
                            alert('Jumlah ' + this.getQuantityLabel() + ' harus minimal 1!');
                            return false;
                        }
                    }
                    break;
                    
                case 'Coworking Space':
                    if (!this.coworkingPass) {
                        alert('Pilih jenis pass Coworking!');
                        return false;
                    }
                    break;
            }
            
            return true;
        }
    }
}
</script>

@endsection