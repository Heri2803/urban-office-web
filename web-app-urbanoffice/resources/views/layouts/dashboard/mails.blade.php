@extends('layouts.app') {{-- layout utama --}}

@section('content')
<div class="flex min-h-screen bg-gray-50">

    {{-- Main Content --}}
    <div class="flex-1 ml-0 md:ml-60 lg:ml-64 xl:ml-64 flex flex-col">

        {{-- Mails Header Bar --}}
        <div class="transition-all duration-700 ease-out opacity-100 translate-y-0">
            @include('layouts.components.mailsbar')
        </div>

        {{-- Mail Content --}}
        <div class="flex-1 p-4 md:p-6">
            {{-- Mail Header --}}
            <div class="mb-4 md:mb-6 transition-all duration-500 ease-out opacity-100 translate-y-0 delay-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-2">
                    <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-2 sm:mb-0">
                        Kontak Pesan Masuk
                    </h2>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 w-fit">
                        Terjawab
                    </span>
                </div>
                <p class="text-sm text-gray-600">549/VO/VII/25</p>
            </div>

            {{-- Message Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 transition-all duration-500 ease-out opacity-100 translate-y-0 delay-300">
                {{-- Message Header --}}
                <div class="p-4 md:p-6 border-b border-gray-200">
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <div class="w-5 h-5 bg-white rounded"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-800 text-sm md:text-base">
                                Dari Customer - Wicak
                            </h3>
                            <p class="text-xs md:text-sm text-gray-600 mt-1">
                                No Telp. 081243592921
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Message Content --}}
                <div class="p-4 md:p-6">
                    <div class="space-y-4">
                        {{-- Customer Message --}}
                        <div class="bg-gray-50 rounded-lg p-3 md:p-4">
                            <p class="text-gray-700 text-sm md:text-base">
                                Bisa dibantu siapkan dokumennya
                            </p>
                        </div>

                        {{-- Payment Information --}}
                        <div class="border-l-4 border-blue-400 bg-blue-50 pl-4 py-3 rounded-r-lg">
                            <p class="text-blue-700 text-sm md:text-base font-medium">
                                Untuk pelunasan berikut nanti 350.000
                            </p>
                        </div>

                        {{-- Payment Instructions --}}
                        <div class="border-l-4 border-yellow-400 bg-yellow-50 pl-4 py-3 rounded-r-lg">
                            <p class="text-yellow-700 text-sm md:text-base">
                                <span class="font-medium">Instruksi:</span> Jika ingin tf bisa ke rekening BCA 8023666667 a.n. Master Penerjemah Indonesia
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reply Input Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 transition-all duration-500 ease-out mb-16 opacity-100 translate-y-0 delay-400">
                {{-- Input Header --}}
                <div class="p-4 border-b border-gray-200">
                    <h3 class="font-medium text-gray-800 text-sm md:text-base">Balas Pesan</h3>
                </div>

                {{-- Message Input --}}
                <div class="p-4">
                    <form action="#" method="POST" enctype="multipart/form-data">
                        @csrf
                        <textarea
                            name="reply"
                            placeholder="Ketik pesan balasan..."
                            class="w-full min-h-[120px] p-3 border border-gray-300 rounded-lg focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all duration-200 text-gray-800 resize-none text-sm md:text-base"
                        ></textarea>

                        {{-- Input Actions --}}
                        <div class="flex items-center justify-between mt-4">
                            <div class="flex items-center space-x-3">
                                {{-- File Upload Button --}}
                                <input
                                    type="file"
                                    id="fileUpload"
                                    name="file"
                                    class="hidden"
                                    onchange="previewFileName(this)"
                                />

                                <button
                                    type="button"
                                    onclick="document.getElementById('fileUpload').click()"
                                    class="flex items-center space-x-2 px-3 py-2 text-gray-600 hover:text-orange-500 hover:bg-orange-50 rounded-lg transition-all duration-200"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    <span class="text-sm hidden sm:inline">File</span>
                                </button>
                            </div>

                            {{-- Send Button --}}
                            <button type="submit" class="flex items-center space-x-2 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                <span class="text-sm">Kirim</span>
                            </button>
                        </div>
                        {{-- Preview filename --}}
                        <p id="fileNamePreview" class="text-xs text-gray-500 mt-2"></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JS untuk preview nama file --}}
<script>
    function previewFileName(input) {
        const file = input.files[0];
        if (file) {
            document.getElementById('fileNamePreview').textContent = "File dipilih: " + file.name;
        }
    }
</script>
@endsection
