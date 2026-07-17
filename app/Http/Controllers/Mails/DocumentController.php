<?php

namespace App\Http\Controllers\Mails;

use App\Models\Document;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\DocumentUploadRequest;
use App\Http\Requests\Customer\DocumentUpdateRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    /**
     * Display a listing of documents for a transaction.
     */
    public function index(Transaction $transaction)
    {
        try {
            // Authorize - user can only view their own transaction documents
            if ($transaction->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $documents = $transaction->documents()
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'type' => $doc->document_type,
                        'type_label' => $doc->type_label,
                        'filename' => $doc->original_filename,
                        'file_url' => $doc->file_url,
                        'file_size' => $doc->file_size,
                        'formatted_size' => $doc->formatted_size,
                        'status' => $doc->status,
                        'status_label' => $doc->status_label,
                        'notes' => $doc->notes,
                        'created_at' => $doc->created_at->toDateTimeString(),
                        'verified_at' => $doc->verified_at?->toDateTimeString(),
                    ];
                });

            return response()->json([
                'success' => true,
                'transaction_id' => $transaction->id,
                'documents' => $documents
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading documents: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly uploaded document.
     */
    public function store(DocumentUploadRequest $request)
    {
        try {
            $validated = $request->validated();
            $file = $request->file('document');
            $transaction = Transaction::findOrFail($validated['transaction_id']);

            // Check if same document type already exists and is pending/verified
            $existingDoc = Document::where('transaction_id', $transaction->id)
                ->where('document_type', $validated['document_type'])
                ->whereIn('status', ['pending', 'verified'])
                ->first();

            if ($existingDoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen dengan tipe ini sudah diupload dan sedang dalam proses verifikasi'
                ], 422);
            }

            // Generate unique filename
            $timestamp = time();
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filename = "{$timestamp}_{$transaction->id}_{$validated['document_type']}.{$extension}";
            
            // Store file
            $path = $file->storeAs(
                "documents/transaction_{$transaction->id}", 
                $filename, 
                'public'
            );

            if (!$path) {
                throw new \Exception('Gagal menyimpan file');
            }

            // Create document record
            $document = Document::create([
                'transaction_id' => $transaction->id,
                'user_id' => auth()->id(),
                'document_type' => $validated['document_type'],
                'filename' => $filename,
                'original_filename' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'status' => 'pending'
            ]);

            // Log success
            Log::info('Document uploaded successfully', [
                'document_id' => $document->id,
                'transaction_id' => $transaction->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diupload',
                'document' => [
                    'id' => $document->id,
                    'type' => $document->document_type,
                    'type_label' => $document->type_label,
                    'filename' => $document->original_filename,
                    'file_url' => $document->file_url,
                    'file_size' => $document->file_size,
                    'formatted_size' => $document->formatted_size,
                    'status' => $document->status,
                    'status_label' => $document->status_label,
                    'created_at' => $document->created_at->toDateTimeString()
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Document upload failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified document.
     */
    public function destroy(Document $document)
    {
        try {
            // Authorize - user can only delete their own pending documents
            if ($document->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            if ($document->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya dokumen dengan status pending yang dapat dihapus'
                ], 422);
            }

            // Delete file from storage
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Delete record
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Document delete failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update document (pakai DocumentUpdateRequest)
     */
    public function update(DocumentUpdateRequest $request, Document $document)
    {
        try {
            // 🔍 DEBUG: Lihat semua data yang masuk
            \Log::info('UPDATE REQUEST DATA:', [
                'all' => $request->all(),
                'has_document_type' => $request->has('document_type'),
                'document_type' => $request->input('document_type'),
                'has_file' => $request->hasFile('document'),
                'has_notes' => $request->has('notes'),
                'notes' => $request->input('notes'),
                'method' => $request->method(),
                'content_type' => $request->header('Content-Type')
            ]);

            $updatedData = [];

            // Update document type if provided
            if ($request->has('document_type')) {
                \Log::info('Updating document_type to: ' . $request->document_type);
                $document->document_type = $request->document_type;
                $updatedData[] = 'document_type';
            }

            // Update file if provided
            if ($request->hasFile('document')) {
                \Log::info('Updating file: ' . $request->file('document')->getClientOriginalName());
                
                // Delete old file
                if (Storage::disk('public')->exists($document->file_path)) {
                    Storage::disk('public')->delete($document->file_path);
                }

                // Upload new file
                $file = $request->file('document');
                $timestamp = time();
                $extension = $file->getClientOriginalExtension();
                $filename = "{$timestamp}_{$document->transaction_id}_{$document->document_type}.{$extension}";
                
                $path = $file->storeAs(
                    "documents/transaction_{$document->transaction_id}", 
                    $filename, 
                    'public'
                );

                $document->filename = $filename;
                $document->original_filename = $file->getClientOriginalName();
                $document->file_path = $path;
                $document->file_size = $file->getSize();
                $document->mime_type = $file->getMimeType();
                
                $updatedData[] = 'file';
            }

            // Update notes if provided
            if ($request->has('notes')) {
                \Log::info('Updating notes to: ' . $request->notes);
                $document->notes = $request->notes;
                $updatedData[] = 'notes';
            }

            \Log::info('Updated fields: ', $updatedData);

            // Save changes
            if (!empty($updatedData)) {
                $document->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Dokumen berhasil diupdate',
                    'updated_fields' => $updatedData,
                    'document' => [
                        'id' => $document->id,
                        'type' => $document->document_type,
                        'type_label' => $document->type_label,
                        'filename' => $document->original_filename,
                        'file_url' => $document->file_url,
                        'status' => $document->status,
                        'notes' => $document->notes
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang diupdate. Data yang diterima: ' . json_encode($request->all())
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Document update failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal update dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Replace document file (khusus ganti file)
     */
    public function replaceFile(Request $request, Document $document)
    {
        try {
            if ($document->user_id !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            if ($document->status !== 'pending') {
                return response()->json([
                    'success' => false, 
                    'message' => 'Hanya dokumen pending yang bisa diganti file-nya'
                ], 422);
            }

            $request->validate([
                'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
            ]);

            // Delete old file
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Upload new file
            $file = $request->file('document');
            $timestamp = time();
            $extension = $file->getClientOriginalExtension();
            $filename = "{$timestamp}_{$document->transaction_id}_{$document->document_type}.{$extension}";
            
            $path = $file->storeAs(
                "documents/transaction_{$document->transaction_id}", 
                $filename, 
                'public'
            );

            $document->filename = $filename;
            $document->original_filename = $file->getClientOriginalName();
            $document->file_path = $path;
            $document->file_size = $file->getSize();
            $document->mime_type = $file->getMimeType();
            $document->save();

            return response()->json([
                'success' => true,
                'message' => 'File dokumen berhasil diganti',
                'document' => [
                    'id' => $document->id,
                    'filename' => $document->original_filename,
                    'file_url' => $document->file_url,
                    'file_size' => $document->file_size,
                    'formatted_size' => $document->formatted_size
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal ganti file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify document (Admin only)
     */
    public function verify(Request $request, Document $document)
    {
        try {
            // Authorize - only admin can verify
            if (!auth()->user()->isAdmin()) { // Sesuaikan dengan role admin Anda
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - Admin only'
                ], 403);
            }

            $request->validate([
                'status' => 'required|in:verified,rejected',
                'notes' => 'required_if:status,rejected|nullable|string|max:500'
            ]);

            $document->update([
                'status' => $request->status,
                'notes' => $request->notes,
                'verified_at' => now(),
                'verified_by' => auth()->id()
            ]);

            // Log verification
            Log::info('Document verified', [
                'document_id' => $document->id,
                'status' => $request->status,
                'verified_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diverifikasi',
                'document' => [
                    'id' => $document->id,
                    'status' => $document->status,
                    'status_label' => $document->status_label,
                    'verified_at' => $document->verified_at?->toDateTimeString(),
                    'notes' => $document->notes
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Document verification failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal verifikasi dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check document status for a transaction
     */
    public function status(Transaction $transaction)
    {
        try {
            if ($transaction->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $requiredTypes = ['ktp', 'npwp', 'akta_perusahaan', 'siup_nib'];
            $documents = $transaction->documents()->get()->keyBy('document_type');
            
            $status = [];
            foreach ($requiredTypes as $type) {
                $doc = $documents->get($type);
                $status[$type] = [
                    'exists' => !is_null($doc),
                    'status' => $doc?->status ?? 'missing',
                    'status_label' => $doc?->status_label ?? 'Belum Diupload',
                    'filename' => $doc?->original_filename,
                    'file_url' => $doc ? Storage::disk('public')->url($doc->file_path) : null,
                    'uploaded_at' => $doc?->created_at?->toDateTimeString()
                ];
            }

            $allUploaded = collect($status)->every(fn($item) => $item['exists']);
            $allVerified = collect($status)->every(fn($item) => $item['status'] === 'verified');

            return response()->json([
                'success' => true,
                'transaction_id' => $transaction->id,
                'documents' => $status,
                'summary' => [
                    'total_required' => count($requiredTypes),
                    'uploaded' => collect($status)->filter(fn($item) => $item['exists'])->count(),
                    'verified' => collect($status)->filter(fn($item) => $item['status'] === 'verified')->count(),
                    'all_uploaded' => $allUploaded,
                    'all_verified' => $allVerified
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking document status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal cek status dokumen: ' . $e->getMessage()
            ], 500);
        }
    }
}