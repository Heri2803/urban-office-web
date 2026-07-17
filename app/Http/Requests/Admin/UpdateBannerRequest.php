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
        $promoId = $this->route('banner')->id ?? $this->route('promo')->id ?? null;
        
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:promos,code,' . $promoId,
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:promo_categories,id',
            'status' => 'required|in:draft,active,inactive,upcoming,ended',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'priority' => 'required|integer|min:1|max:100',
            'locations' => 'nullable|array',
            'locations.*' => 'nullable|integer|exists:locations,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            
            // Optional fields
            'service_types' => 'nullable|array',
            'service_types.*' => 'string',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_amount' => 'nullable|numeric|min:0',
            'min_transaction' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'usage_per_user' => 'nullable|integer|min:1',
            'target_users' => 'nullable|array',
            'target_users.*' => 'integer|exists:users,id',
        ];

        // Conditional validation untuk discount type (asumsi promo_type_id dikirim, atau diambil dari instance)
        $banner = $this->route('banner');
        
        if ($banner && $banner->promo_type_id == 1) { // Banner type
            $rules['category_id'] = 'required|exists:promo_categories,id';
        } else {
            // Locations wajib jika type != 1
            $rules['locations'] = 'required|array|min:1';
            $rules['locations.*'] = 'integer|exists:locations,id';
        }

        if ($banner && $banner->promo_type_id == 2) { // Discount type
            $rules['service_types'] = 'required|array|min:1';
            $rules['discount_type'] = 'required|in:percentage,fixed';
            $rules['discount_amount'] = 'required|numeric|min:0';
            
            if ($this->input('discount_type') === 'percentage') {
                $rules['discount_amount'] .= '|max:100';
            }
        }

        return $rules;
    }
}