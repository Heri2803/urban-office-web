<?php
// app/Http/Controllers/API/LunchOptionController.php

namespace App\Http\Controllers\Booking;

use App\Models\LunchOption;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LunchOptionController extends Controller
{
    public function getByLocation($locationId = null)
    {
        try {
            $lunchOptions = LunchOption::available()
                ->byLocation($locationId)
                ->get()
                ->map(function($option) {
                    return [
                        'id' => $option->id,
                        'name' => $option->name,
                        'description' => $option->description,
                        'price' => (float) $option->price,
                        'location_id' => $option->location_id
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $lunchOptions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load lunch options'
            ], 500);
        }
    }
}