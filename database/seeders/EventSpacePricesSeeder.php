<?php
// database/seeders/EventSpacePricesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSpacePricesSeeder extends Seeder
{
    public function run()
    {
        try {
            $this->command->info('🔧 Disabling foreign key checks...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Data Event Space Prices (sementara - adjust sesuai data server nanti)
            $eventSpacePrices = [
                // Event Space 4 Jam - Tanpa Coffee Break
                [
                    'room_id' => null,
                    'room_type_id' => 4, // Event Space
                    'service_category_id' => 6, // Empire (atau sesuaikan dengan kategori event space di server)
                    'duration_type' => 'hour',
                    'duration' => 4,
                    'base_price' => 150000.00, // Harga per orang
                    'coffee_break_option' => null,
                    'coffee_break_price' => null,
                    'deposit' => 300000.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // Event Space 4 Jam - Dengan Coffee Break 1x
                [
                    'room_id' => null,
                    'room_type_id' => 4,
                    'service_category_id' => 6,
                    'duration_type' => 'hour',
                    'duration' => 4,
                    'base_price' => 150000.00,
                    'coffee_break_option' => '1x',
                    'coffee_break_price' => 200000.00, // Harga dengan coffee break
                    'deposit' => 300000.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // Event Space 8 Jam - Tanpa Coffee Break
                [
                    'room_id' => null,
                    'room_type_id' => 4,
                    'service_category_id' => 6,
                    'duration_type' => 'hour',
                    'duration' => 8,
                    'base_price' => 250000.00,
                    'coffee_break_option' => null,
                    'coffee_break_price' => null,
                    'deposit' => 500000.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // Event Space 8 Jam - Dengan Coffee Break 2x
                [
                    'room_id' => null,
                    'room_type_id' => 4,
                    'service_category_id' => 6,
                    'duration_type' => 'hour',
                    'duration' => 8,
                    'base_price' => 250000.00,
                    'coffee_break_option' => '2x',
                    'coffee_break_price' => 350000.00,
                    'deposit' => 500000.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // Event Space Daily (Full Day)
                [
                    'room_id' => null,
                    'room_type_id' => 4,
                    'service_category_id' => 6,
                    'duration_type' => 'day',
                    'duration' => 1,
                    'base_price' => 2000000.00, // Harga flat untuk sewa harian
                    'coffee_break_option' => null,
                    'coffee_break_price' => null,
                    'deposit' => 1000000.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // Event Space Weekly
                [
                    'room_id' => null,
                    'room_type_id' => 4,
                    'service_category_id' => 6,
                    'duration_type' => 'week',
                    'duration' => 1,
                    'base_price' => 10000000.00, // Harga flat untuk sewa mingguan
                    'coffee_break_option' => null,
                    'coffee_break_price' => null,
                    'deposit' => 2000000.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                // Event Space Monthly
                [
                    'room_id' => null,
                    'room_type_id' => 4,
                    'service_category_id' => 6,
                    'duration_type' => 'month',
                    'duration' => 1,
                    'base_price' => 35000000.00, // Harga flat untuk sewa bulanan
                    'coffee_break_option' => null,
                    'coffee_break_price' => null,
                    'deposit' => 5000000.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            $this->command->info('📦 Inserting event space prices...');
            
            // Insert data
            foreach ($eventSpacePrices as $price) {
                DB::table('service_prices')->insert($price);
            }

            $this->command->info('🔧 Re-enabling foreign key checks...');
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->command->info('✅ Event Space Prices seeded successfully!');
            $this->command->info('🎪 Pricing: 4 Jam (150k), 4 Jam+CB (200k), 8 Jam (250k), 8 Jam+CB (350k), Daily (2jt), Weekly (10jt), Monthly (35jt)');
            
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->command->error('❌ Error: ' . $e->getMessage());
            throw $e;
        }
    }
}