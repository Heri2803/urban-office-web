@verbatim
<div x-data="documentUploader()" 
     x-init="
        $watch('$store.documentModal.isOpen', value => {
            if (value && $store.documentModal.transactionId) {
                open($store.documentModal.transactionId);
            } else {
                close();
            }
            document.body.style.overflow = value ? 'hidden' : '';
        });
     "
     x-show="$store.documentModal.isOpen"
     x-cloak
     @keydown.escape.window="$store.documentModal.close()"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-title"
     class="fixed inset-0 z-50"
     style="display: none;">

    <!-- Modal Wrapper -->
    <div class="flex items-center justify-center min-h-screen px-4 py-4">

        <!-- Backdrop -->
        <div x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm"
             @click="$store.documentModal.close()"
             aria-hidden="true">
        </div>

        <!-- Modal Panel -->
        <div x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="scale-95 opacity-0"
             x-transition:enter-end="scale-100 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="scale-100 opacity-100"
             x-transition:leave-end="scale-95 opacity-0"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col">
            <div class="bg-gradient-to-r from-orange-600 to-orange-700 px-4 sm:px-6 py-4 flex-shrink-0 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="flex-shrink-0">
                            <svg class="h-7 w-7 sm:h-8 sm:w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base sm:text-lg font-semibold text-white truncate" id="modal-title">
                                Upload Dokumen Virtual Office
                            </h3>
                        </div>
                    </div>
                    <button @click="$store.documentModal.close()" 
                            aria-label="Tutup modal"
                            class="text-white hover:text-orange-200 flex-shrink-0 ml-3 p-1 rounded-lg 
                                   focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-orange-600
                                   transition-colors duration-150">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="bg-gray-50 px-4 sm:px-6 flex-1 overflow-y-auto modal-scroll rounded-b-2xl">

                <!-- Loading State -->
                <div x-show="isLoading" class="text-center py-12">
                    <svg class="animate-spin h-10 w-10 text-orange-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-gray-500 mt-2 text-sm">Memuat status dokumen...</p>
                </div>
                <template x-if="!isLoading">
                    <div class="space-y-4 sm:space-y-5 py-4 pb-8 sm:pb-10">

                        <!-- ─── Progress Summary ─── -->
                        <div class="bg-white rounded-xl p-3 sm:p-4 border border-gray-200 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Progress Dokumen</span>
                                <span class="text-sm font-semibold text-orange-600" 
                                      x-text="`${uploadedCount}/${totalRequired} Terupload`"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-orange-600 h-2 rounded-full transition-all duration-500"
                                     :style="`width: ${totalRequired > 0 ? (uploadedCount / totalRequired) * 100 : 0}%`"
                                     role="progressbar"
                                     :aria-valuenow="totalRequired > 0 ? Math.round((uploadedCount / totalRequired) * 100) : 0"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>

                        <!-- ─── Document List ─── -->
                        <div class="space-y-3">
                            <h4 class="text-sm font-semibold text-gray-700 px-1">Dokumen yang Diperlukan:</h4>

                            <template x-for="doc in requiredDocuments" :key="doc.type">
                                <div class="bg-white rounded-xl border transition-all duration-200"
                                     :class="getDocumentCardClass(doc.type)">

                                    <div class="p-3 sm:p-4">

                                        <!-- Row 1: Icon + Label + Status Badge -->
                                        <div class="flex items-start gap-3">

                                            <!-- Icon -->
                                            <div class="flex-shrink-0">
                                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center"
                                                     :class="getDocumentIconBgClass(doc.type)">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5"
                                                         :class="getDocumentIconColorClass(doc.type)"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                            </div>

                                            <!-- Info -->
                                            <div class="flex-1 min-w-0">

                                                <!-- Label + Status Badge -->
                                                <div class="flex items-center justify-between gap-2">
                                                    <h5 class="text-sm font-medium text-gray-900 truncate" 
                                                        x-text="doc.label"></h5>
                                                    <span class="text-xs px-2 py-1 rounded-full font-medium flex-shrink-0"
                                                          :class="getStatusBadgeClass(doc.type)"
                                                          x-text="getStatusLabel(doc.type)">
                                                    </span>
                                                </div>

                                                <!-- File Info (jika sudah ada file) -->
                                                <template x-if="getDocument(doc.type)">
                                                    <div class="mt-2">

                                                        <!-- File Row -->
                                                        <div class="flex items-center justify-between bg-gray-50 p-2 rounded-lg border border-gray-100 gap-2">
                                                            <div class="flex items-center gap-2 min-w-0 flex-1">
                                                                <svg class="w-4 h-4 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                                </svg>
                                                                <span class="text-xs text-gray-700 truncate" 
                                                                      x-text="getDocument(doc.type).filename"></span>
                                                            </div>

                                                            <!-- Tombol Lihat / Link -->
                                                            <div class="flex-shrink-0">
                                                                <button
                                                                    x-show="isImageFile(getDocument(doc.type).filename)"
                                                                    @click="togglePreview(doc.type)"
                                                                    :aria-expanded="previewDocType === doc.type"
                                                                    :aria-label="previewDocType === doc.type ? 'Tutup preview ' + doc.label : 'Lihat preview ' + doc.label"
                                                                    class="text-xs text-orange-600 hover:text-orange-800 focus:outline-none 
                                                                           px-2 py-1 rounded-md bg-orange-50 hover:bg-orange-100 
                                                                           whitespace-nowrap transition-colors duration-150">
                                                                    <span x-text="previewDocType === doc.type ? 'Tutup' : 'Lihat'"></span>
                                                                </button>
                                                                <a x-show="!isImageFile(getDocument(doc.type).filename)"
                                                                   :href="getDocument(doc.type).file_url"
                                                                   target="_blank"
                                                                   rel="noopener noreferrer"
                                                                   :aria-label="'Buka file ' + doc.label + ' di tab baru'"
                                                                   class="text-xs text-orange-600 hover:text-orange-800 
                                                                          px-2 py-1 rounded-md bg-orange-50 hover:bg-orange-100 
                                                                          whitespace-nowrap transition-colors duration-150">
                                                                    Lihat
                                                                </a>
                                                            </div>
                                                        </div>

                                                        <!-- Slide Down Image Preview -->
                                                        <div x-show="previewDocType === doc.type"
                                                             x-transition:enter="transition ease-out duration-200"
                                                             x-transition:enter-start="opacity-0 -translate-y-1"
                                                             x-transition:enter-end="opacity-100 translate-y-0"
                                                             x-transition:leave="transition ease-in duration-150"
                                                             x-transition:leave-start="opacity-100 translate-y-0"
                                                             x-transition:leave-end="opacity-0 -translate-y-1"
                                                             class="mt-1.5 rounded-lg border border-gray-200 overflow-hidden bg-gray-50">

                                                            <!-- Preview Header -->
                                                            <div class="flex items-center justify-between px-3 py-1.5 bg-gray-100 border-b border-gray-200">
                                                                <span class="text-xs text-gray-500 truncate" 
                                                                      x-text="getDocument(doc.type).filename"></span>
                                                                <button @click="togglePreview(doc.type)"
                                                                        aria-label="Tutup preview"
                                                                        class="text-gray-400 hover:text-gray-600 focus:outline-none flex-shrink-0 ml-2 p-1 rounded transition-colors duration-150">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                            <!-- Gambar Preview -->
                                                            <div class="p-2 flex justify-center bg-gray-50">
                                                                <img :src="getDocument(doc.type).file_url"
                                                                     :alt="'Preview dokumen ' + doc.label"
                                                                     class="max-h-28 max-w-[220px] w-auto object-contain rounded-md border border-gray-200 shadow-sm"
                                                                     loading="lazy"
                                                                     @error="handleImageError(doc.type)" />
                                                            </div>
                                                        </div>

                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Row 2: Action Button -->
                                        <div class="mt-3">
                                            <template x-if="getDocumentStatus(doc.type) === 'missing'">
                                                <button @click="openUploadForm(doc.type)"
                                                        class="w-full text-sm bg-orange-600 text-white px-3 py-2 rounded-lg 
                                                               hover:bg-orange-700 text-center font-medium
                                                               focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2
                                                               transition-colors duration-150">
                                                    Upload Dokumen
                                                </button>
                                            </template>
                                            <template x-if="getDocumentStatus(doc.type) === 'pending'">
                                                <button @click="openUploadForm(doc.type, true)"
                                                        class="w-full text-sm bg-amber-500 text-white px-3 py-2 rounded-lg 
                                                               hover:bg-amber-600 text-center font-medium
                                                               focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2
                                                               transition-colors duration-150">
                                                    Ganti File
                                                </button>
                                            </template>
                                            <template x-if="getDocumentStatus(doc.type) === 'verified'">
                                                <div class="w-full flex items-center justify-center gap-1.5 py-2">
                                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span class="text-sm text-green-600 font-medium">Terverifikasi</span>
                                                </div>
                                            </template>
                                        </div>

                                    </div>
                                </div>
                            </template>
                        </div>
                        <div x-show="showUploadForm"
                             x-ref="uploadFormSection"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-2"
                             class="bg-white rounded-xl border-2 border-orange-200 shadow-sm overflow-hidden">

                            <!-- Form Header -->
                            <div class="bg-orange-50 px-4 py-3 border-b border-orange-100 flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-orange-800">
                                    <span x-text="isEditing ? '✏️ Ganti File' : '📎 Upload Baru'"></span>
                                    <span class="text-orange-500 font-normal"> — </span>
                                    <span x-text="selectedDocLabel" class="text-orange-700"></span>
                                </h4>
                                <button type="button"
                                        @click="cancelUpload"
                                        aria-label="Batal upload"
                                        class="text-orange-400 hover:text-orange-600 p-1 rounded-lg 
                                               focus:outline-none focus:ring-2 focus:ring-orange-400
                                               transition-colors duration-150">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Form Body -->
                            <div class="p-4 sm:p-5">
                                <form id="upload-form" @submit.prevent="submitUpload" class="space-y-4" novalidate>

                                    <!-- File Input -->
                                    <div>
                                        <label for="doc-file-input" class="block text-sm font-medium text-gray-700 mb-1.5">
                                            Pilih File <span class="text-red-500" aria-hidden="true">*</span>
                                        </label>
                                        <input id="doc-file-input"
                                               type="file" 
                                               x-ref="fileInput"
                                               @change="handleFileSelect"
                                               accept=".pdf,.jpg,.jpeg,.png"
                                               class="block w-full text-sm text-gray-500 
                                                      file:mr-3 file:py-2 file:px-3 sm:file:px-4
                                                      file:rounded-lg file:border-0 
                                                      file:text-sm file:font-semibold
                                                      file:bg-orange-50 file:text-orange-700 
                                                      hover:file:bg-orange-100
                                                      file:transition-colors file:duration-150
                                                      focus:outline-none">
                                        <p class="text-xs text-gray-500 mt-1.5">
                                            Format: PDF, JPG, PNG · Maks. 2MB
                                        </p>
                                    </div>

                                    <!-- File Preview (setelah file dipilih) -->
                                    <div x-show="form.selectedFile"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="bg-orange-50 px-3 py-2.5 rounded-lg border border-orange-100 flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2 min-w-0 flex-1">
                                            <svg class="w-4 h-4 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="text-xs text-orange-700 truncate font-medium" 
                                                  x-text="form.selectedFile?.name ?? ''"></span>
                                        </div>
                                        <button type="button" 
                                                @click="clearSelectedFile"
                                                aria-label="Hapus file yang dipilih"
                                                class="text-red-400 hover:text-red-600 flex-shrink-0 p-1 rounded
                                                       focus:outline-none focus:ring-2 focus:ring-red-400
                                                       transition-colors duration-150">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Notes -->
                                    <div>
                                        <label for="doc-notes" class="block text-sm font-medium text-gray-700 mb-1.5">
                                            Catatan 
                                            <span class="text-gray-400 font-normal">(Opsional)</span>
                                        </label>
                                        <textarea id="doc-notes"
                                                  x-model="form.notes"
                                                  rows="2"
                                                  class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2
                                                         focus:ring-2 focus:ring-orange-500 focus:border-orange-500 
                                                         placeholder-gray-400 resize-none
                                                         transition-colors duration-150"
                                                  placeholder="Tambahkan catatan jika perlu..."></textarea>
                                    </div>

                                    <!-- Error Message -->
                                    <div x-show="form.error"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         role="alert"
                                         class="flex items-start gap-2 text-sm text-red-700 bg-red-50 border border-red-100 p-3 rounded-lg">
                                        <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span x-text="form.error"></span>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-1">
                                        <button type="button"
                                                @click="cancelUpload"
                                                class="w-full sm:w-auto px-4 py-2.5 text-sm font-medium text-gray-700 
                                                       border border-gray-300 rounded-lg hover:bg-gray-50 
                                                       focus:outline-none focus:ring-2 focus:ring-gray-400
                                                       transition-colors duration-150 text-center">
                                            Batal
                                        </button>
                                        <button type="submit"
                                                :disabled="form.isUploading || !form.selectedFile"
                                                class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium 
                                                       bg-orange-600 text-white rounded-lg 
                                                       hover:bg-orange-700 
                                                       disabled:opacity-50 disabled:cursor-not-allowed 
                                                       focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2
                                                       flex items-center justify-center gap-2
                                                       transition-colors duration-150">
                                            <svg x-show="form.isUploading"
                                                 class="animate-spin h-4 w-4 text-white"
                                                 fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span x-text="form.isUploading ? 'Mengupload...' : (isEditing ? 'Ganti File' : 'Upload')"></span>
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>

                    </div>
                </template>

            </div>
        </div>
    </div>
</div>
@endverbatim

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('documentUploader', () => ({

        // State
        isOpen: false,
        transactionId: null,
        isLoading: true,
        documents: [],
        requiredDocuments: [
            { type: 'ktp', label: 'KTP / Paspor' },
            { type: 'npwp', label: 'NPWP' },
            { type: 'akta_perusahaan', label: 'Akta Perusahaan' },
            { type: 'siup_nib', label: 'SIUP / NIB' }
        ],

        // UI State
        showUploadForm: false,
        isEditing: false,
        selectedDocType: null,
        selectedDocLabel: '',
        previewDocType: null,

        // Form State
        form: {
            document_type: '',
            notes: '',
            selectedFile: null,
            isUploading: false,
            error: ''
        },

        previewLoading: false,
        previewError: false,

        // Computed
        get totalRequired() {
            return this.requiredDocuments.length;
        },

        get uploadedCount() {
            return this.documents.filter(function(d) { 
                return d.status !== 'missing'; 
            }).length;
        },

        // Methods
        async open(transactionId) {
            console.log('Modal open untuk:', transactionId);
            this.transactionId = transactionId;
            this.isOpen = true;
            await this.loadDocuments();
        },

        close() {
            this.isOpen = false;
            this.resetForm();
        },

        async loadDocuments() {
            this.isLoading = true;
            try {
                const url = '/dashboard/mails/documents/status/' + this.transactionId;
                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error('HTTP error, status: ' + response.status);
                }

                const data = await response.json();

                if (data.success) {
                    this.documents = Object.entries(data.documents || {}).map(function([type, doc]) {
                        return {
                            type: type,
                            id: (doc && doc.id) ? doc.id : null,
                            filename: (doc && doc.filename) ? doc.filename : null,
                            file_url: (doc && doc.file_url) ? doc.file_url : null,
                            status: (doc && doc.status) ? doc.status : 'missing',
                            status_label: (doc && doc.status_label) ? doc.status_label : 'Belum Diupload',
                            exists: !!(doc && doc.id && doc.filename)
                        };
                    });
                } else {
                    console.error('Gagal memuat dokumen:', data.message);
                }
            } catch (error) {
                console.error('Error loading documents:', error);
            } finally {
                this.isLoading = false;
            }
        },

        isImageFile(filename) {
            if (!filename) return false;
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            const extension = filename.split('.').pop().toLowerCase();
            return imageExtensions.includes(extension);
        },

        getDocumentStatus(type) {
            const doc = this.documents.find(function(d) { return d.type === type; });
            return doc ? doc.status : 'missing';
        },

        getStatusLabel(type) {
            const doc = this.documents.find(function(d) { return d.type === type; });
            return doc ? doc.status_label : 'Belum Diupload';
        },

        getDocumentFile(type) {
            const doc = this.documents.find(function(d) { return d.type === type; });
            return doc || null;
        },

        togglePreview(type) {
            this.previewDocType = this.previewDocType === type ? null : type;
        },

        openUploadForm(type, isEdit) {
            isEdit = isEdit || false;
            const doc = this.requiredDocuments.find(function(d) { return d.type === type; });
            if (!doc) return;

            this.selectedDocType = type;
            this.selectedDocLabel = doc.label;
            this.isEditing = isEdit;
            this.showUploadForm = true;
            this.form.document_type = type;
            this.form.error = '';
        },

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                this.form.error = 'Format file tidak didukung. Gunakan PDF, JPG, atau PNG';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                this.form.error = 'Ukuran file maksimal 2MB';
                return;
            }

            this.form.selectedFile = file;
            this.form.error = '';
        },

        clearSelectedFile() {
            this.form.selectedFile = null;
            if (this.$refs && this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },

        getDocumentId(type) {
            const doc = this.documents.find(function(d) { return d.type === type; });
            return (doc && doc.id) ? doc.id : null;
        },

        async submitUpload() {
            if (!this.form.selectedFile) {
                this.form.error = 'Pilih file terlebih dahulu';
                return;
            }

            this.form.isUploading = true;
            this.form.error = '';

            const formData = new FormData();
            formData.append('document_type', this.form.document_type);
            formData.append('notes', this.form.notes || '');
            formData.append('document', this.form.selectedFile);
            formData.append('transaction_id', this.transactionId);

            try {
                const documentId = this.getDocumentId(this.form.document_type);
                const isUpdate = !!documentId;

                let url, method;

                if (isUpdate) {
                    url = '/dashboard/mails/documents/' + documentId;
                    method = 'POST';
                    formData.append('_method', 'PUT');
                } else {
                    url = '/dashboard/mails/documents/upload';
                    method = 'POST';
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    await this.loadDocuments();
                    this.cancelUpload();
                    this.$dispatch('document-updated', {
                        transactionId: this.transactionId,
                        type: this.form.document_type
                    });
                    this.showSuccess('Dokumen berhasil diupload');
                } else {
                    this.form.error = data.message || 'Gagal upload dokumen';
                }
            } catch (error) {
                console.error('Upload error:', error);
                this.form.error = 'Gagal terhubung ke server';
            } finally {
                this.form.isUploading = false;
            }
        },

        cancelUpload() {
            this.showUploadForm = false;
            this.isEditing = false;
            this.selectedDocType = null;
            this.previewDocType = null;
            this.form.document_type = '';
            this.form.notes = '';
            this.form.selectedFile = null;
            this.form.error = '';

            if (this.$refs && this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }

            this.resetPreviewState();
        },

        resetForm() {
            this.cancelUpload();
            this.documents = [];
            this.isLoading = false;
        },

        resetPreviewState() {
            this.previewLoading = false;
            this.previewError = false;
        },

        handleImageError() {
            this.previewError = true;
            console.error('Gagal memuat preview gambar');
        },

        showSuccess(message) {
            this.$dispatch('show-notification', {
                type: 'success',
                message: message
            });
        },

        showError(message) {
            this.$dispatch('show-notification', {
                type: 'error',
                message: message
            });
        }

    }));
});
</script>

<style>
    .modal-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .modal-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .modal-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .modal-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>