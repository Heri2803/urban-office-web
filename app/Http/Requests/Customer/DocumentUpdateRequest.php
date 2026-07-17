<?php
// app/Http/Requests/Customer/DocumentUpdateRequest.php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Get document from route binding
        $document = $this->route('document');
        
        // User can only update their own documents
        return $document && $document->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'document_type' => [
                'sometimes',  // Optional - hanya jika diisi
                'required',
                Rule::in(['ktp', 'npwp', 'akta_perusahaan', 'siup_nib', 'other'])
            ],
            'document' => [
                'sometimes',  // Optional - hanya jika upload file baru
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048', // 2MB
            ],
            'notes' => [
                'sometimes',
                'nullable',
                'string',
                'max:500'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'document_type.required' => 'Pilih jenis dokumen',
            'document_type.in' => 'Jenis dokumen tidak valid',
            'document.required' => 'File dokumen wajib diupload',
            'document.mimes' => 'Format file harus PDF, JPG, atau PNG',
            'document.max' => 'Ukuran file maksimal 2MB',
            'notes.max' => 'Catatan maksimal 500 karakter'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $document = $this->route('document');
            
            // Check if document is pending (only pending can be updated)
            if ($document && $document->status !== 'pending') {
                $validator->errors()->add(
                    'document', 
                    'Hanya dokumen dengan status pending yang dapat diupdate'
                );
            }

            // If updating document_type, check if new type already exists
            if ($this->has('document_type') && $document) {
                $existingDoc = \App\Models\Document::where('transaction_id', $document->transaction_id)
                    ->where('document_type', $this->document_type)
                    ->where('id', '!=', $document->id)
                    ->whereIn('status', ['pending', 'verified'])
                    ->first();

                if ($existingDoc) {
                    $validator->errors()->add(
                        'document_type', 
                        'Dokumen dengan tipe ini sudah ada'
                    );
                }
            }
        });
    }
}