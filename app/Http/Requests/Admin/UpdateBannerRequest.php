<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:promo_categories,id',
            'status' => 'required|in:draft,active,inactive,upcoming,ended',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'priority' => 'required|integer|min:1|max:100',
            'locations' => 'required|array',
            'locations.*' => 'required|string', // atau integer tergantung kebutuhan
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            
            // TAMBAHKAN RULES UNTUK FIELD BARU
            'service_types' => 'nullable|array',
            'service_types.*' => 'string',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_amount' => 'nullable|numeric|min:0',
            'min_transaction' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'usage_per_user' => 'nullable|integer|min:1',
        ];
    }
}