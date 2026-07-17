{{-- resources/views/layouts/admin/components/modal-invoice-manual.blade.php --}}
<div x-show="showManualModal"
     class="fixed inset-0 z-[1050] overflow-y-auto"
     aria-labelledby="modal-title" role="dialog" aria-modal="true"
     x-cloak>
     
    {{-- Backdrop --}}
    <div x-show="showManualModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 transition-opacity"
         style="background-color: rgba(255, 255, 255, 0.5); backdrop-filter: blur(2px);"
         @click="showManualModal = false"></div>

    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        {{-- Modal Panel --}}
        <div x-show="showManualModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-gray-100">
             
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h5 class="text-lg font-bold text-gray-900 flex items-center gap-2" id="modal-title">
                    <i class="fas fa-hand-pointer text-blue-600"></i>
                    Input Invoice Manual
                </h5>
                <button type="button" @click="showManualModal = false" :disabled="loading" class="text-gray-400 text-lg hover:text-gray-500 hover:bg-gray-100 rounded-lg p-1.5 transition-colors focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Success Message --}}
            <div x-show="successMessage" class="mx-6 mt-4 p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 flex items-center" x-cloak>
                <i class="fas fa-check-circle mr-2 text-green-600"></i>
                <span x-text="successMessage"></span>
            </div>

            {{-- Error Message --}}
            <div x-show="error" class="mx-6 mt-4 p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 flex items-center" x-cloak>
                <i class="fas fa-exclamation-circle mr-2 text-red-600"></i>
                <span x-text="error"></span>
            </div>

            <form @submit.prevent="submitManualForm">
                <div class="px-6 py-4">
                    {{-- Loading Overlay --}}
                    <div x-show="loading" class="flex flex-col items-center justify-center py-12">
                        <svg class="animate-spin -ml-1 mr-3 h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-4 text-sm text-gray-500 font-medium">Menyimpan data invoice...</p>
                    </div>

                    <div x-show="!loading">
                        {{-- Customer Info --}}
                        <h6 class="text-sm font-semibold text-gray-900 mb-4 border-b border-gray-100 pb-2">Data Customer</h6>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       x-model="manualForm.nama_lengkap"
                                       class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       :class="errors.nama_lengkap ? 'border-red-300' : 'border-gray-300'"
                                       required>
                                <p class="mt-1 text-xs text-red-600" x-show="errors.nama_lengkap" x-text="errors.nama_lengkap?.[0]"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Perusahaan</label>
                                <input type="text"
                                       x-model="manualForm.company_name"
                                       class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       :class="errors.company_name ? 'border-red-300' : 'border-gray-300'">
                                <p class="mt-1 text-xs text-red-600" x-show="errors.company_name" x-text="errors.company_name?.[0]"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Telepon <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       x-model="manualForm.phone"
                                       class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       :class="errors.phone ? 'border-red-300' : 'border-gray-300'"
                                       required>
                                <p class="mt-1 text-xs text-red-600" x-show="errors.phone" x-text="errors.phone?.[0]"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email"
                                       x-model="manualForm.email"
                                       class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       :class="errors.email ? 'border-red-300' : 'border-gray-300'"
                                       required>
                                <p class="mt-1 text-xs text-red-600" x-show="errors.email" x-text="errors.email?.[0]"></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIK (Opsional)</label>
                                <input type="text"
                                       x-model="manualForm.nik"
                                       class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       :class="errors.nik ? 'border-red-300' : 'border-gray-300'">
                                <p class="mt-1 text-xs text-red-600" x-show="errors.nik" x-text="errors.nik?.[0]"></p>
                            </div>
                        </div>

                        {{-- Sewa Info --}}
                        <h6 class="text-sm font-semibold text-gray-900 mb-4 mt-8 border-b border-gray-100 pb-2">Detail Sewa</h6>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Kota <span class="text-red-500">*</span>
                                </label>
                                <select x-model="manualForm.city_id"
                                        class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        :class="errors.city_id ? 'border-red-300' : 'border-gray-300'"
                                        required>
                                    <option value="">Pilih Kota</option>
                                    <template x-for="city in cities" :key="city.id">
                                        <option :value="city.id" x-text="city.name"></option>
                                    </template>
                                </select>
                                <p class="mt-1 text-xs text-red-600" x-show="errors.city_id" x-text="errors.city_id?.[0]"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Lokasi <span class="text-red-500">*</span>
                                </label>
                                <select x-model="manualForm.location_id"
                                        class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                        :class="errors.location_id ? 'border-red-300' : 'border-gray-300'"
                                        :disabled="!manualForm.city_id"
                                        required>
                                    <option value="">Pilih Lokasi</option>
                                    <template x-for="loc in filteredLocations" :key="loc.id">
                                        <option :value="loc.id" x-text="loc.name"></option>
                                    </template>
                                </select>
                                <p class="mt-1 text-xs text-red-600" x-show="errors.location_id" x-text="errors.location_id?.[0]"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tipe Ruangan <span class="text-red-500">*</span>
                                </label>
                                <select x-model="manualForm.room_type"
                                        class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        :class="errors.room_type ? 'border-red-300' : 'border-gray-300'"
                                        required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="Virtual Office">Virtual Office</option>
                                    <option value="Private Office">Private Office</option>
                                    <option value="Meeting Room">Meeting Room</option>
                                    <option value="Coworking Space">Coworking Space</option>
                                    <option value="Sharing Room">Sharing Room</option>
                                    <option value="Event Space">Event Space</option>
                                </select>
                                <p class="mt-1 text-xs text-red-600" x-show="errors.room_type" x-text="errors.room_type?.[0]"></p>
                            </div>

                            <div x-show="['Private Office', 'Meeting Room', 'Sharing Room'].includes(manualForm.room_type)">
                                <label class="block text-sm font-medium text-black mb-1">
                                    Ruangan <span class="text-xs text-gray-500">(Spesifik)</span>
                                </label>
                                <select x-model="manualForm.room_id"
                                    class="w-full text-sm text-gray-900 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                    :class="errors.room_id ? 'border-red-300' : 'border-gray-300'"
                                    :disabled="!manualForm.location_id">
                                    <option value="">Pilih Ruangan</option>
                                    <template x-for="room in filteredRooms" :key="room.id">
                                        <option :value="room.id" x-text="room.name"></option>
                                    </template>
                                </select>
                                <p class="mt-1 text-xs text-red-600" x-show="errors.room_id" x-text="errors.room_id?.[0]"></p>
                            </div>

                            <div x-show="['Meeting Room', 'Coworking Space', 'Event Space', 'Private Office', 'Sharing Room'].includes(manualForm.room_type)">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Jumlah Pax/Orang <span class="text-red-500">*</span>
                                </label>
                                <input type="number" min="1"
                                       x-model="manualForm.jumlah_orang"
                                       class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       :class="errors.jumlah_orang ? 'border-red-300' : 'border-gray-300'">
                                <p class="mt-1 text-xs text-red-600" x-show="errors.jumlah_orang" x-text="errors.jumlah_orang?.[0]"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tanggal Mulai <span class="text-red-500">*</span>
                                </label>
                                <input type="date"
                                       x-model="manualForm.booking_date"
                                       class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       :class="errors.booking_date ? 'border-red-300' : 'border-gray-300'"
                                       :min="todayDate"
                                       required>
                                <p class="mt-1 text-xs text-red-600" x-show="errors.booking_date" x-text="errors.booking_date?.[0]"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                                <input type="time"
                                       x-model="manualForm.start_time"
                                       class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       :class="errors.start_time ? 'border-red-300' : 'border-gray-300'">
                                <p class="mt-1 text-xs text-red-600" x-show="errors.start_time" x-text="errors.start_time?.[0]"></p>
                            </div>
                        </div>

                        <!-- Durasi & Atribut Tambahan -->
                        <div x-show="manualForm.room_type" class="mt-4 p-4 bg-blue-50 border border-blue-100 rounded-lg">
                            <h6 class="text-sm font-semibold text-blue-900 mb-3 border-b border-blue-200 pb-2">Spesifikasi Layanan</h6>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                                <!-- Virtual Office Specific -->
                                <template x-if="manualForm.room_type === 'Virtual Office'">
                                    <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Paket VO</label>
                                            <select x-model="manualForm.service_category_id" class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Pilih Paket VO</option>
                                                <template x-for="cat in serviceCategories.filter(c => c.type === 'Virtual Office')" :key="cat.id">
                                                    <option :value="cat.id" x-text="cat.name"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Status PKP</label>
                                            <select x-model="manualForm.status_pkp" class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Pilih Status PKP</option>
                                                <option value="PKP">PKP</option>
                                                <option value="Non PKP">Non PKP</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Format Sewa VO</label>
                                            <select x-model="manualForm.paket" class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Pilih Format Durasi</option>
                                                <option value="monthly">Bulanan</option>
                                                <option value="yearly">Tahunan</option>
                                            </select>
                                        </div>
                                        <div x-show="manualForm.paket === 'monthly'">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Berapa Bulan?</label>
                                            <input type="number" min="1" x-model="manualForm.bulan" class="w-full text-sm rounded-lg shadow-sm">
                                        </div>
                                        <div x-show="manualForm.paket === 'yearly'">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Berapa Tahun?</label>
                                            <input type="number" min="1" x-model="manualForm.tahun" class="w-full text-sm rounded-lg shadow-sm">
                                        </div>
                                    </div>
                                </template>

                                <!-- Meeting Room & Event Space Specific -->
                                <template x-if="['Meeting Room', 'Event Space'].includes(manualForm.room_type)">
                                    <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Format Paket</label>
                                            <select x-model="manualForm.paket" class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Pilih Format Layanan</option>
                                                <option value="hourly">Per Jam</option>
                                                <option value="daily">Harian</option>
                                            </select>
                                        </div>
                                        <div x-show="manualForm.paket === 'hourly'">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Berapa Jam?</label>
                                            <input type="number" min="1" x-model="manualForm.jam" class="w-full text-sm rounded-lg shadow-sm">
                                        </div>
                                        <div x-show="manualForm.paket === 'daily'">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Berapa Hari?</label>
                                            <input type="number" min="1" x-model="manualForm.hari" class="w-full text-sm rounded-lg shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Coffee Break</label>
                                            <select x-model="manualForm.coffee_break" class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Tanpa Coffee Break</option>
                                                <option value="1x">1x Coffee Break</option>
                                                <option value="2x">2x Coffee Break</option>
                                            </select>
                                        </div>
                                    </div>
                                </template>

                                <!-- Private Office / Sharing Room Specific -->
                                <template x-if="['Private Office', 'Sharing Room'].includes(manualForm.room_type)">
                                    <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Format Sewa Office</label>
                                            <select x-model="manualForm.paket" class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Pilih Format</option>
                                                <option value="monthly">Bulanan</option>
                                                <option value="yearly">Tahunan</option>
                                                <option value="weekly" x-show="manualForm.room_type === 'Private Office'">Mingguan</option>
                                                <option value="daily" x-show="manualForm.room_type === 'Private Office'">Harian</option>
                                            </select>
                                        </div>
                                        <div x-show="manualForm.paket === 'daily'">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Berapa Hari?</label>
                                            <input type="number" min="1" x-model="manualForm.hari" class="w-full text-sm rounded-lg shadow-sm">
                                        </div>
                                        <div x-show="manualForm.paket === 'weekly'">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Berapa Minggu?</label>
                                            <input type="number" min="1" x-model="manualForm.minggu" class="w-full text-sm rounded-lg shadow-sm">
                                        </div>
                                        <div x-show="manualForm.paket === 'monthly'">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Berapa Bulan?</label>
                                            <input type="number" min="1" x-model="manualForm.bulan" class="w-full text-sm rounded-lg shadow-sm">
                                        </div>
                                        <div x-show="manualForm.paket === 'yearly'">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Berapa Tahun?</label>
                                            <input type="number" min="1" x-model="manualForm.tahun" class="w-full text-sm rounded-lg shadow-sm">
                                        </div>
                                    </div>
                                </template>

                                <!-- Coworking Space -->
                                <template x-if="manualForm.room_type === 'Coworking Space'">
                                    <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Pass</label>
                                            <select x-model="manualForm.service_category_id" class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Pilih Tipe Pass</option>
                                                <template x-for="cat in serviceCategories.filter(c => c.type === 'Coworking Space')" :key="cat.id">
                                                    <option :value="cat.id" x-text="cat.name"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Format Tarif</label>
                                            <select x-model="manualForm.paket" class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Pilih Format Tarif</option>
                                                <option value="hourly">Per Jam</option>
                                                <option value="daily">Harian</option>
                                                <option value="monthly">Bulanan</option>
                                            </select>
                                        </div>
                                        <div x-show="manualForm.paket === 'hourly'">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Berapa Jam?</label>
                                            <input type="number" min="1" x-model="manualForm.jam" class="w-full text-sm rounded-lg shadow-sm">
                                        </div>
                                    </div>
                                </template>

                                <!-- Makan Siang (Lunch) - Universal -->
                                <div class="col-span-1 md:col-span-2 mt-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Opsi Makan Siang (Lunch/Catering)</label>
                                    <div class="flex gap-2 items-center">
                                        <select x-model="manualForm.lunch_option_id" class="flex-1 text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Tanpa Makan Siang</option>
                                            <template x-for="lunch in lunchOptions" :key="lunch.id">
                                                <option :value="lunch.id" x-text="lunch.name + ' (Rp ' + (new Intl.NumberFormat('id-ID').format(lunch.price)) + '/pax)'"></option>
                                            </template>
                                        </select>
                                        <input type="number" x-model="manualForm.lunch_quantity" min="1" placeholder="Porsi" class="w-24 text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" :disabled="!manualForm.lunch_option_id">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Note: Harga lunch akan diakumulasi ke Total Dibayar di bawah tapi harus direkap manual di tabel input 'Total Sewa', ya.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Financial --}}
                        <h6 class="text-sm font-semibold text-gray-900 mb-4 mt-8 border-b border-gray-100 pb-2">Informasi Pembayaran</h6>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Total Sewa (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       x-model="manualForm.gross_amount"
                                       class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       :class="errors.gross_amount ? 'border-red-300' : 'border-gray-300'"
                                       min="0"
                                       required>
                                <p class="mt-1 text-xs text-red-600" x-show="errors.gross_amount" x-text="errors.gross_amount?.[0]"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deposit (Rp)</label>
                                <input type="number"
                                       x-model="manualForm.deposit"
                                       class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       :class="(errors.deposit || isDepositExceeded) ? 'border-red-300' : 'border-gray-300'"
                                       min="0">
                                <p class="mt-1 text-xs text-red-600" x-show="isDepositExceeded">Deposit tidak boleh melebihi total sewa</p>
                                <p class="mt-1 text-xs text-red-600" x-show="!isDepositExceeded && errors.deposit" x-text="errors.deposit?.[0]"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select x-model="manualForm.status"
                                        class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        :class="errors.status ? 'border-red-300' : 'border-gray-300'"
                                        required>
                                    <option value="pending">Pending</option>
                                    <option value="settlement">Settlement</option>
                                    <option value="expired">Expired</option>
                                </select>
                                <p class="mt-1 text-xs text-red-600" x-show="errors.status" x-text="errors.status?.[0]"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                                <textarea x-model="manualForm.notes"
                                          class="w-full text-sm rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                          rows="2"
                                          :class="errors.notes ? 'border-red-300' : 'border-gray-300'"></textarea>
                                <p class="mt-1 text-xs text-red-600" x-show="errors.notes" x-text="errors.notes?.[0]"></p>
                            </div>
                        </div>

                        {{-- Total Preview --}}
                        <div class="mt-6">
                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Total Sewa:</span>
                                    <strong class="text-gray-900" x-text="formatCurrency(manualForm.gross_amount)"></strong>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600 mt-2">
                                    <span>Deposit:</span>
                                    <span class="text-gray-900" x-text="formatCurrency(manualForm.deposit)"></span>
                                </div>
                                <hr class="my-3 border-gray-200">
                                <div class="flex justify-between text-base font-bold text-gray-900">
                                    <span>Grand Total:</span>
                                    <span x-text="formatCurrency((parseFloat(manualForm.gross_amount) || 0) + (parseFloat(manualForm.deposit) || 0))"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3 rounded-b-xl">
                    <button type="button"
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 shadow-sm rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                            @click="showManualModal = false"
                            :disabled="loading">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-medium text-white shadow-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-75 disabled:cursor-not-allowed"
                            :class="isDepositExceeded ? 'bg-blue-400' : 'bg-blue-600 hover:bg-blue-700'"
                            :disabled="loading || isDepositExceeded">
                        <span x-show="!loading" class="flex items-center gap-2">
                            <i class="fas fa-save"></i> Simpan Invoice
                        </span>
                        <span x-show="loading" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>