@extends('layouts.admin')

@section('title', 'Edit Surat - ' . $surat->nomor_surat)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.surats.index') }}" class="hover:text-blue-600">Surat Masuk</a>
        <span>/</span>
        <a href="{{ route('admin.surats.show', $surat) }}" class="hover:text-blue-600">Detail</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Edit</span>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h1 class="text-xl font-bold text-gray-900 mb-6">✏️ Edit Surat Masuk</h1>

        @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.surats.update', $surat) }}" enctype="multipart/form-data"
              x-data="editForm()" class="space-y-5">
            @csrf @method('PUT')

            {{-- Nomor Surat --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nomor Surat <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nomor_surat" value="{{ old('nomor_surat', $surat->nomor_surat) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
            </div>

            {{-- Tanggal, Tanggal Datang & Pengirim --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_surat" value="{{ old('tanggal_surat', $surat->tanggal_surat->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Datang <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_datang" value="{{ old('tanggal_datang', $surat->tanggal_datang ? $surat->tanggal_datang->format('Y-m-d') : date('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pengirim <span class="text-red-500">*</span></label>
                    <input type="text" name="pengirim" value="{{ old('pengirim', $surat->pengirim) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
                </div>
            </div>

            {{-- Perihal --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Perihal <span class="text-red-500">*</span></label>
                <input type="text" name="perihal" value="{{ old('perihal', $surat->perihal) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
            </div>

            {{-- Isi Ringkasan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Isi / Ringkasan</label>
                <textarea name="isi_ringkasan" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm resize-none">{{ old('isi_ringkasan', $surat->isi_ringkasan) }}</textarea>
            </div>

            {{-- File --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lampiran File</label>
                @if($surat->file_path)
                <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg mb-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    <span class="text-sm text-blue-700 flex-1">{{ $surat->file_name }}</span>
                    <label class="flex items-center gap-1 text-xs text-red-600 cursor-pointer">
                        <input type="checkbox" name="remove_file" value="1"> Hapus file
                    </label>
                </div>
                @endif
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors cursor-pointer"
                     @click="$refs.fileInput.click()">
                    <input type="file" name="file_surat" accept=".pdf,.jpg,.jpeg,.png"
                           class="hidden" x-ref="fileInput"
                           @change="newFileName = $event.target.files[0]?.name || null">
                    <template x-if="!newFileName">
                        <div>
                            <p class="text-sm text-gray-500">Klik untuk upload file baru</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (maks 10MB)</p>
                        </div>
                    </template>
                    <template x-if="newFileName">
                        <div class="flex items-center justify-center gap-2 text-green-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium" x-text="newFileName"></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Informasi Pengambilan Surat --}}
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 space-y-4 shadow-sm" x-data="{ statusPengambilan: '{{ old('status_pengambilan', $surat->status_pengambilan ?? 'belum_diambil') }}', metodePengambilan: '{{ old('metode_pengambilan', $surat->metode_pengambilan ?? 'offline') }}' }">
                <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-1.5">
                    📦 Status Pengambilan Fisik Surat
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status Pengambilan <span class="text-red-500">*</span>
                        </label>
                        <select name="status_pengambilan" x-model="statusPengambilan"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm bg-white">
                            <option value="belum_diambil">Belum Diambil</option>
                            <option value="sudah_diambil">Sudah Diambil / Dikirim</option>
                        </select>
                    </div>
                </div>

                {{-- Fields jika sudah diambil --}}
                <div x-show="statusPengambilan === 'sudah_diambil'" x-transition class="space-y-4 pt-2 border-t border-gray-200/60">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Diambil <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_diambil" value="{{ old('tanggal_diambil', $surat->tanggal_diambil ? $surat->tanggal_diambil->format('Y-m-d') : date('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Metode Pengambilan <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-4 mt-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="metode_pengambilan" value="offline" x-model="metodePengambilan"
                                           class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">Offline (Diambil di Kantor)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="metode_pengambilan" value="delivery" x-model="metodePengambilan"
                                           class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">Delivery (Dikirim)</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Fields jika metode = delivery --}}
                    <div x-show="metodePengambilan === 'delivery'" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Ekspedisi / Kurir <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="kurir_pengiriman" value="{{ old('kurir_pengiriman', $surat->kurir_pengiriman) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm"
                                   placeholder="Cth: JNE, GoSend, J&T">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Resi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="resi_pengiriman" value="{{ old('resi_pengiriman', $surat->resi_pengiriman) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm"
                                   placeholder="Masukkan nomor resi pengiriman">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status + Recipients --}}
            <div x-data="{ status: '{{ old('status', $surat->status) }}' }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                <div class="flex gap-4 mb-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="draft" x-model="status" class="text-blue-600">
                        <span class="text-sm text-gray-700">Draft</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="published" x-model="status" class="text-blue-600">
                        <span class="text-sm text-gray-700">Published</span>
                    </label>
                </div>

                <div x-show="status === 'published'" x-transition>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Penerima <span class="text-red-500">*</span>
                    </label>
                    @if($customers->isEmpty())
                    <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-700">
                        Tidak ada penerima (customer / mitra) yang tersedia.
                    </div>
                    @else
                    <div class="border border-gray-300 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-3 py-2 flex items-center justify-between">
                            <span class="text-xs text-gray-600">{{ $customers->count() }} penerima tersedia (customer & mitra)</span>
                            <button type="button" class="text-xs text-blue-600 hover:underline" onclick="selectAllEdit()">Pilih Semua</button>
                        </div>
                        <div class="max-h-48 overflow-y-auto p-2 space-y-1">
                            @foreach($customers as $customer)
                            <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                <input type="checkbox" name="recipients[]" value="{{ $customer->id }}"
                                       {{ in_array($customer->id, old('recipients', $selectedRecipients)) ? 'checked' : '' }}
                                       class="customer-checkbox-edit rounded text-blue-600">
                                <div>
                                    <div class="text-sm font-medium text-gray-800">{{ $customer->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $customer->email }}</div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.surats.index') }}"
                   class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function editForm() { return { newFileName: null }; }
function selectAllEdit() {
    document.querySelectorAll('.customer-checkbox-edit').forEach(cb => cb.checked = true);
}
</script>
@endpush
@endsection
