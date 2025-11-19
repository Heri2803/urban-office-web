<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'promo_type_id' => 'required|integer|exists:promo_types,id',
            'category_id' => 'required|integer|exists:promo_categories,id',
            'locations' => 'required|array|min:1',
            'locations.*' => 'integer|exists:locations,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'priority' => 'nullable|integer|min:1|max:10',
            'status' => 'required|in:active,inactive,draft,upcoming,ended',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            // Optional fields for all types
            'service_types' => 'sometimes|array',
            'service_types.*' => 'string',
            'discount_type' => 'sometimes|in:percentage,fixed',
            'discount_amount' => 'sometimes|numeric|min:0',
            'min_transaction' => 'sometimes|numeric|min:0',
            'usage_limit' => 'sometimes|integer|min:0',
            'usage_per_user' => 'sometimes|integer|min:1',
        ];

        // Conditional validation untuk discount type - PASTIKAN DI DALAM METHOD
        $promoType = $this->input('promo_type_id');
        if ($promoType == 2) { // Discount type
            $rules['service_types'] = 'required|array|min:1';
            $rules['discount_type'] = 'required|in:percentage,fixed';
            $rules['discount_amount'] = 'required|numeric|min:0';
            
            // Validasi percentage discount
            if ($this->input('discount_type') === 'percentage') {
                $rules['discount_amount'] .= '|max:100';
            }
        }

        return $rules; // PASTIKAN RETURN DI AKHIR METHOD
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Banner name is required',
            'name.max' => 'Banner name cannot exceed 255 characters',
            'promo_type_id.required' => 'Promo type is required',
            'promo_type_id.exists' => 'Selected promo type is invalid',
            'category_id.required' => 'Category is required',
            'category_id.exists' => 'Selected category is invalid',
            'locations.required' => 'Please select at least one location',
            'locations.min' => 'Please select at least one location',
            'locations.*.exists' => 'Selected location is invalid',
            'start_date.required' => 'Start date is required',
            'end_date.required' => 'End date is required',
            'end_date.after' => 'End date must be after start date',
            'status.required' => 'Status is required',
            'status.in' => 'Selected status is invalid',
            
            'image.image' => 'The file must be an image',
            'image.mimes' => 'Image must be JPEG, PNG, JPG, or GIF format',
            'image.max' => 'Image size must not exceed 2MB',

            // Conditional messages untuk discount
            'service_types.required' => 'Service types are required for discount promotions',
            'discount_type.required' => 'Discount type is required for discount promotions',
            'discount_amount.required' => 'Discount amount is required for discount promotions',
            'discount_amount.max' => 'Percentage discount cannot exceed 100%',
        ];
    }
}