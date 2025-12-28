<?php
// database/seeders/LunchOptionsSeeder.php

namespace Database\Seeders;

use App\Models\LunchOption;
use Illuminate\Database\Seeder;

class LunchOptionsSeeder extends Seeder
{
    public function run()
    {
        $lunchOptions = [
            [
                'name' => 'Chicken Blackpepper',
                'description' => 'Nasi putih, ayam saos blackpepper, sayur , free Esteh manis',
                'price' => 30000,
                'is_available' => true,
                'location_id' => null
            ],
            [
                'name' => 'Chicken Teriyaki', 
                'description' => 'Nasi putih, ayam saos teriyaki, sayur, free Esteh manis',
                'price' => 30000,
                'is_available' => true,
                'location_id' => null
            ],
            [
                'name' => 'Ayam Kalio',
                'description' => 'Nasi merah, ayam saos kalio, sayur, free Esteh manis',
                'price' => 30000,
                'is_available' => true,
                'location_id' => null
            ],
            [
                'name' => 'Nasi Goreng Hongkong',
                'description' => 'Nasi Goreng dengan bumbu khas Hongkong, free Esteh manis',
                'price' => 30000,
                'is_available' => true,
                'location_id' => null
            ],
            [
                'name' => 'Ayam Rendang',
                'description' => 'Nasi putih, ayam saos rendang, sayur, free Esteh manis',
                'price' => 30000,
                'is_available' => true,
                'location_id' => null
            ],
        ];

        foreach ($lunchOptions as $option) {
            LunchOption::create($option);
        }
    }
}