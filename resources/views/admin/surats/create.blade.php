@extends('layouts.admin')

@section('title', 'Tambah Surat Masuk')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.surats.index') }}" class="hover:text-blue-600">Surat Masuk</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Tambah Surat</span>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h1 class="text-xl font-bold text-gray-900 mb-6">📝 Tambah Surat Masuk Baru</h1>

        @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.surats.store') }}" enctype="multipart/form-data"
              x-data="suratForm()" class="space-y-5">
            @csrf

            {{-- Nomor Surat (auto-generated, read-only) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nomor Surat
                    <span class="ml-1 text-[10px] font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">Otomatis</span>
                </label>
                <div class="relative">
                    <input type="text" name="nomor_surat" value="{{ old('nomor_surat', $suggestedNumber) }}"
                           readonly
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-500 text-sm cursor-not-allowed select-none font-mono"
                           tabindex="-1">
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-1">Nomor surat digenerate otomatis berdasarkan urutan terbaru</p>
            </div>

            {{-- Tanggal, Tanggal Datang & Pengirim --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tanggal Surat <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_surat" value="{{ old('tanggal_surat', date('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tanggal Datang <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_datang" value="{{ old('tanggal_datang', date('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Pengirim <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="pengirim" value="{{ old('pengirim') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm"
                           placeholder="Nama instansi/orang pengirim" required>
                </div>
            </div>

            {{-- Perihal --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Perihal / Subjek <span class="text-red-500">*</span>
                </label>
                <input type="text" name="perihal" value="{{ old('perihal') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm"
                       placeholder="Cth: Undangan Rapat Bulanan Urban Office" required>
            </div>

            {{-- Isi Ringkasan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Isi / Ringkasan</label>
                <textarea name="isi_ringkasan" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm resize-none"
                          placeholder="Tulis ringkasan isi surat...">{{ old('isi_ringkasan') }}</textarea>
            </div>

            {{-- Upload File --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lampiran File <span class="text-gray-400 font-normal">(Opsional, maks 10MB)</span></label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors cursor-pointer"
                     @click="$refs.fileInput.click()">
                    <input type="file" name="file_surat" accept=".pdf,.jpg,.jpeg,.png"
                           class="hidden" x-ref="fileInput"
                           @change="fileName = $event.target.files[0]?.name || null">
                    <template x-if="!fileName">
                        <div>
                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-sm text-gray-500">Klik untuk upload atau drag & drop</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (maks 10MB)</p>
                        </div>
                    </template>
                    <template x-if="fileName">
                        <div class="flex items-center justify-center gap-2 text-green-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium" x-text="fileName"></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Informasi Pengambilan Surat --}}
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 space-y-4 shadow-sm" x-data="{ statusPengambilan: '{{ old('status_pengambilan', 'belum_diambil') }}', metodePengambilan: '{{ old('metode_pengambilan', 'offline') }}' }">
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
                            <input type="date" name="tanggal_diambil" value="{{ old('tanggal_diambil', date('Y-m-d')) }}"
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
                            <input type="text" name="kurir_pengiriman" value="{{ old('kurir_pengiriman') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm"
                                   placeholder="Cth: JNE, GoSend, J&T">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Resi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="resi_pengiriman" value="{{ old('resi_pengiriman') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm"
                                   placeholder="Masukkan nomor resi pengiriman">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status --}}
            <div x-data="{ status: '{{ old('status', 'draft') }}' }">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="draft" x-model="status"
                               class="text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Draft <span class="text-gray-400">(belum dikirim)</span></span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="published" x-model="status"
                               class="text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Published <span class="text-gray-400">(kirim notifikasi)</span></span>
                    </label>
                </div>

                {{-- Recipients (hanya tampil jika published) --}}
                <div x-show="status === 'published'" x-transition class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Penerima <span class="text-red-500">*</span>
                        <span class="text-gray-400 font-normal">(wajib jika Published)</span>
                    </label>
                    @if($customers->isEmpty())
                    <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-700">
                        Tidak ada penerima (customer / mitra) yang tersedia.
                    </div>
                    @else
                    <div class="border border-gray-300 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-3 py-2 flex items-center justify-between">
                            <span class="text-xs text-gray-600">{{ $customers->count() }} penerima tersedia (customer & mitra)</span>
                            <button type="button" class="text-xs text-orange-600 hover:underline font-semibold" onclick="selectAll()">Pilih Semua</button>
                        </div>
                        <div class="p-2 border-b border-gray-200">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input type="text" id="searchRecipient" onkeyup="filterRecipients()" placeholder="Cari nama atau email..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>
                        <div class="max-h-48 overflow-y-auto p-2 space-y-1">
                            @foreach($customers as $customer)
                            <label class="recipient-item flex items-center gap-3 p-2 hover:bg-orange-50 rounded-lg cursor-pointer transition-colors">
                                <input type="checkbox" name="recipients[]" value="{{ $customer->id }}"
                                       {{ in_array($customer->id, old('recipients', [])) ? 'checked' : '' }}
                                       class="customer-checkbox rounded text-blue-600 focus:ring-blue-500">
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

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.surats.index') }}"
                   class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    Simpan Surat
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function suratForm() {
    return {
        fileName: null,
    };
}

function selectAll() {
    // Hanya pilih checkbox yang sedang tampil (tidak di-hidden oleh fitur search)
    document.querySelectorAll('.recipient-item:not([style*="display: none"]) .customer-checkbox')
        .forEach(cb => cb.checked = true);
}

function filterRecipients() {
    let filter = document.getElementById('searchRecipient').value.toLowerCase();
    let items = document.querySelectorAll('.recipient-item');

    items.forEach(item => {
        let text = item.textContent || item.innerText;
        if (text.toLowerCase().indexOf(filter) > -1) {
            item.style.display = "";
        } else {
            item.style.display = "none";
        }
    });
}
</script>
@endpush
@endsection
