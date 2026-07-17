<?php
// app/Http/Requests/DocumentUploadRequest.php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Check if user owns the transaction
        $transaction = \App\Models\Transaction::find($this->transaction_id);
        return $transaction && $transaction->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'transaction_id' => [
                'required',
                'integer',
                Rule::exists('transactions', 'id')->where(function ($query) {
                    $query->where('user_id', auth()->id());
                })
            ],
            'document_type' => [
                'required',
                Rule::in(['ktp', 'npwp', 'akta_perusahaan', 'siup_nib', 'other'])
            ],
            'document' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048', // 2MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_id.required' => 'ID transaksi wajib diisi',
            'transaction_id.exists' => 'Transaksi tidak ditemukan',
            'document_type.required' => 'Pilih jenis dokumen',
            'document_type.in' => 'Jenis dokumen tidak valid',
            'document.required' => 'File dokumen wajib diupload',
            'document.mimes' => 'Format file harus PDF, JPG, atau PNG',
            'document.max' => 'Ukuran file maksimal 2MB',
        ];
    }
}