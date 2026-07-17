{{-- resources/views/layouts/admin/components/modal-contract-generate.blade.php --}}

<div x-show="showContractModal" 
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/50 backdrop-blur-sm"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
     
    <div class="relative w-full max-w-md p-4 mx-auto"
         @click.away="closeContractModal()"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
         
        {{-- Modal content --}}
        <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100">
            
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50/50 rounded-t-2xl">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-file-signature text-purple-600"></i> Buat Kontrak (VO)
                </h3>
                <button type="button" @click="closeContractModal()"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 flex justify-center items-center transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            {{-- Body --}}
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600 mb-4">
                    Silakan tentukan <strong>Tanggal Kontrak</strong> yang akan dicetak di dalam dokumen PDF Perjanjian. Secara default, tanggal hari ini akan digunakan.
                </p>

                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">
                        Tanggal Kontrak <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           x-model="contractDate"
                           class="w-full form-input border-2 border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 rounded-lg p-3 text-gray-800 font-medium transition-all duration-200 hover:border-gray-400">
                </div>
            </div>
            
            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                <button @click="closeContractModal()" type="button"
                        class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors shadow-sm">
                    Batal
                </button>
                <button @click="generateContract()" type="button"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 border border-transparent rounded-lg transition-colors shadow-sm flex items-center gap-2">
                    <i class="fas fa-print"></i> Generate PDF
                </button>
            </div>
            
        </div>
    </div>
</div>
