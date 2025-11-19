<?php
// database/seeders/VirtualOfficePackagesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VirtualOfficePackagesSeeder extends Seeder
{
    public function run()
    {
        try {
            $this->command->info('🔧 Disabling foreign key checks...');
            
            // Nonaktifkan foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Data Virtual Office
            $virtualOfficePackages = [
                [
                    'room_id' => null,
                    'room_type_id' => 5, // Virtual Office
                    'service_category_id' => 3, // Office
                    'duration_type' => 'month',
                    'duration' => 1,
                    'base_price' => 440000.00,
                    'coffee_break_option' => null,
                    'coffee_break_price' => null,
                    'deposit' => 440000.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'room_id' => null,
                    'room_type_id' => 5, // Virtual Office
                    'service_category_id' => 4, // Executive
                    'duration_type' => 'month',
                    'duration' => 1,
                    'base_price' => 770000.00,
                    'coffee_break_option' => null,
                    'coffee_break_price' => null,
                    'deposit' => 440000.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'room_id' => null,
                    'room_type_id' => 5, // Virtual Office
                    'service_category_id' => 5, // Premiere
                    'duration_type' => 'month',
                    'duration' => 1,
                    'base_price' => 1430000.00,
                    'coffee_break_option' => null,
                    'coffee_break_price' => null,
                    'deposit' => 440000.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'room_id' => null,
                    'room_type_id' => 5, // Virtual Office
                    'service_category_id' => 6, // Empire
                    'duration_type' => 'month',
                    'duration' => 1,
                    'base_price' => 1545000.00,
                    'coffee_break_option' => null,
                    'coffee_break_price' => null,
                    'deposit' => 440000.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            $this->command->info('📦 Inserting virtual office packages...');
            
            // Insert data
            foreach ($virtualOfficePackages as $package) {
                DB::table('service_prices')->insert($package);
            }

            $this->command->info('🔧 Re-enabling foreign key checks...');
            
            // Aktifkan kembali foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->command->info('✅ Virtual Office packages seeded successfully!');
            $this->command->info('📦 Packages: Office (Rp 440.000), Executive (Rp 770.000), Premiere (Rp 1.430.000), Empire (Rp 1.545.000)');
            
        } catch (\Exception $e) {
            // Pastikan foreign key checks diaktifkan kembali jika error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->command->error('❌ Error: ' . $e->getMessage());
            throw $e;
        }
    }
}