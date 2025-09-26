{{-- resources/views/form-mitra.blade.php --}}
@extends('layouts.app') {{-- sesuaikan dengan layout utama kamu --}}

@section('content')
<div x-data="{ isVisible: false, fotoProperti: null }" x-init="setTimeout(() => isVisible = true, 100)" class="flex min-h-screen bg-gray-50">

    {{-- Content --}}
    <div class="flex-1 ml-0 md:ml-52 lg:ml-64 xl:ml-64 p-4 md:p-6 mb-10">
        
        {{-- User Status Header --}}
        <div class="mb-6 transition-all duration-700 ease-out"
             :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-4'">
            @include('layouts.components.userstatus', ['name' => 'Georgius Mario', 'status' => 'Mitra'])
        </div>

        {{-- Form Card --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 max-w-4xl mx-auto transition-all duration-700 ease-out"
             :class="isVisible ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 translate-y-8 scale-95'"
             style="transition-delay: 200ms">

            {{-- Form Header --}}
            <div class="mb-6 md:mb-8 transition-all duration-500 ease-out"
                 :class="isVisible ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4'"
                 style="transition-delay: 400ms">
                <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">
                    Isi Formulir dibagian bawah ini untuk melengkapi data-data mitra property
                </h1>
            </div>

            {{-- Form --}}
            <form action="{{ route('dashboard.mitra') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Nama Lengkap --}}
                <div class="transition-all duration-500 ease-out"
                     :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                     style="transition-delay: 500ms">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap KTP</label>
                    <input type="text" name="namaLengkap" value="{{ old('namaLengkap') }}"
                           placeholder="Masukkan nama lengkap sesuai KTP"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all duration-200 text-gray-800 font-medium placeholder-gray-500 hover:border-gray-400"/>
                </div>

                {{-- Email & Alamat Properti --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 transition-all duration-500 ease-out"
                     :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                     style="transition-delay: 600ms">
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
                        <input type="email" name="alamatEmail" value="{{ old('alamatEmail') }}"
                               placeholder="contoh@email.com"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all duration-200 text-gray-800 font-medium placeholder-gray-500 hover:border-gray-400"/>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Properti</label>
                        <input type="text" name="alamatProperti" value="{{ old('alamatProperti') }}"
                               placeholder="Alamat lengkap properti"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all duration-200 text-gray-800 font-medium placeholder-gray-500 hover:border-gray-400"/>
                    </div>
                </div>

                {{-- NIK --}}
                <div class="transition-all duration-500 ease-out"
                     :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                     style="transition-delay: 700ms">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">NIK</label>
                    <input type="text" name="nik" value="{{ old('nik') }}"
                           placeholder="Nomor Induk Kependudukan" maxlength="16"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all duration-200 text-gray-800 font-medium placeholder-gray-500 hover:border-gray-400"/>
                </div>

                {{-- Upload Foto Properti --}}
                <div class="transition-all duration-500 ease-out"
                     :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                     style="transition-delay: 800ms">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Foto Properti</label>
                    
                    <div class="relative">
                        <input type="file" name="fotoProperti" accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               @change="fotoProperti = $event.target.files[0]"/>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 md:p-12 text-center bg-gray-50 hover:bg-gray-100 hover:border-gray-400 transition-all duration-200">
                            <div class="flex flex-col items-center space-y-3">
                                <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-600 font-medium" x-text="fotoProperti ? fotoProperti.name : 'Klik atau drag file ke sini'"></p>
                                    <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG, GIF (Max: 5MB)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="pt-4 transition-all duration-500 ease-out"
                     :class="isVisible ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 translate-y-4 scale-95'"
                     style="transition-delay: 900ms">
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold py-4 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-1">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Mulai Sewa
                        </span>
                    </button>

                    <p class="text-center text-gray-600 text-sm mt-4 leading-relaxed">
                        Anda bisa memesan ruang ini untuk penggunaan per jam, hari, maupun jangka waktu yang panjang
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
