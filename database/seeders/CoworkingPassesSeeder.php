<?php
// database/seeders/CoworkingPassesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoworkingPassesSeeder extends Seeder
{
    public function run()
    {
        try {
            $this->command->info('🔧 Disabling foreign key checks...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Data Coworking Passes (sementara - adjust sesuai data server nanti)
            $coworkingPasses = [
                // Daily Pass - Normal (service_category_id 7)
                [
                    'room_id' => null,
                    'room_type_id' => 3, // Coworking Space
                    'service_category_id' => 7, // Daily Pass
                    'duration_type' => 'hour',
                    'duration' => 1,
                    'base_price' => 25000.00,
                    'coffee_break_option' => null,
                    'coffee_break_price' => null,
                    'deposit' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'room_id' => null,
                    'room_type_id' => 3,
                    'service_category_id' => 7, // Daily Pass
                    'duration_type' => 'hour',
                    'duration' => 6,
                    'base_price' => 100000.00,
                    'coffee_break_option' => null,
                    'coffee_break_price' => null,
                    'deposit' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'room_id' => null,
                    'room_type_id' => 3,
                    'service_category_id' => 7, // Daily Pass
                    'duration_type' => 'hour',
                    'duration' => 8,
                    'base_price' => 120000.00,
                    'coffee_break_option' => null,
                    'coffee_break_price' => null,
                    'deposit' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

                // Student Pass (service_category_id 8)
                [
                    'room_id' => null,
                    'room_type_id' => 3,
                    'service_category_id' => 8, // Student Pass
                    'duration_type' => 'hour',
                    'duration' => 6,
                    'base_price' => 75000.00, // Harga khusus student
                    'coffee_break_option' => null,
                    'coffee_break_price' => null,
                    'deposit' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'room_id' => null,
                    'room_type_id' => 3,
                    'service_category_id' => 8, // Student Pass
                    'duration_type' => 'hour',
                    'duration' => 8,
                    'base_price' => 90000.00, // Harga khusus student
                    'coffee_break_option' => null,
                    'coffee_break_price' => null,
                    'deposit' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

                // Membership (service_category_id 9)
                [
                    'room_id' => null,
                    'room_type_id' => 3,
                    'service_category_id' => 9, // Membership
                    'duration_type' => 'month',
                    'duration' => 1,
                    'base_price' => 1500000.00,
                    'coffee_break_option' => null,
                    'coffee_break_price' => null,
                    'deposit' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            $this->command->info('📦 Inserting coworking passes...');
            
            // Insert data
            foreach ($coworkingPasses as $pass) {
                DB::table('service_prices')->insert($pass);
            }

            $this->command->info('🔧 Re-enabling foreign key checks...');
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->command->info('✅ Coworking Passes seeded successfully!');
            $this->command->info('🎫 Passes: Per Jam (25k), 6 Jam (100k), 8 Jam (120k), Student 6 Jam (75k), Student 8 Jam (90k), Membership (1.5jt)');
            
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->command->error('❌ Error: ' . $e->getMessage());
            throw $e;
        }
    }
}