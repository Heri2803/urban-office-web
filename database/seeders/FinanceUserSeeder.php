<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FinanceUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'finance@urbanoffice.com'],
            [
                'name' => 'Finance Admin',
                'password' => Hash::make('password123'),
                'role' => 'finance',
                'telephone' => '081234567890',
                'alamat' => 'Urban Office HQ',
            ]
        );
    }
}
