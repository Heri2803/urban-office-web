<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Room;

class ServicePriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ✅ Ambil multi-purpose rooms (room_type_id = NULL)
        $multiPurposeRooms = Room::whereNull('room_type_id')->get();

        if ($multiPurposeRooms->isEmpty()) {
            $this->command->warn('⚠️ Tidak ada multi-purpose rooms (room_type_id = NULL)');
            $this->command->info('💡 Jalankan migration untuk set room_type_id = NULL terlebih dahulu');
            return;
        }

        $this->command->info("🔍 Ditemukan {$multiPurposeRooms->count()} multi-purpose rooms");

        foreach ($multiPurposeRooms as $room) {
            $this->command->info("📝 Processing Room {$room->room_number} (ID: {$room->id})");

            // Cek apakah sudah ada service_prices
            $existingCount = DB::table('service_prices')
                ->where('room_id', $room->id)
                ->count();

            if ($existingCount > 0) {
                $this->command->warn("   ⏭️  Skip - Sudah ada {$existingCount} service prices");
                continue;
            }

            // ✅ Ambil data service prices sesuai production
            $servicePrices = $this->getProductionServicePrices($room->id);

            // Insert ke database
            DB::table('service_prices')->insert($servicePrices);

            $this->command->info("   ✅ Berhasil insert " . count($servicePrices) . " service prices");
        }

        $this->command->info('✨ Seeder selesai!');
    }

    /**
     * Get service prices data matching production structure
     * Data diambil dari room 10, 11, 13, 15 di production
     */
    private function getProductionServicePrices($roomId): array
    {
        $now = now();

        return [
            // ========================================
            // PRIVATE OFFICE (room_type_id = 1)
            // Sesuai pattern production
            // ========================================
            [
                'room_id' => $roomId,
                'room_type_id' => 1,
                'service_category_id' => null,
                'duration_type' => 'hour',
                'duration' => 1,
                'base_price' => 115500.00,
                'coffee_break_option' => null,
                'coffee_break_price' => 0.00,
                'deposit' => 0.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'room_id' => $roomId,
                'room_type_id' => 1,
                'service_category_id' => null,
                'duration_type' => 'day',
                'duration' => 1,
                'base_price' => 693000.00,
                'coffee_break_option' => null,
                'coffee_break_price' => 0.00,
                'deposit' => 0.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'room_id' => $roomId,
                'room_type_id' => 1,
                'service_category_id' => null,
                'duration_type' => 'week',
                'duration' => 1,
                'base_price' => 3619000.00,
                'coffee_break_option' => null,
                'coffee_break_price' => 0.00,
                'deposit' => 0.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'room_id' => $roomId,
                'room_type_id' => 1,
                'service_category_id' => null,
                'duration_type' => 'month',
                'duration' => 1,
                'base_price' => 7000000.00,
                'coffee_break_option' => null,
                'coffee_break_price' => 0.00,
                'deposit' => 7000000.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'room_id' => $roomId,
                'room_type_id' => 1,
                'service_category_id' => null,
                'duration_type' => 'year',
                'duration' => 1,
                'base_price' => 63000000.00,
                'coffee_break_option' => null,
                'coffee_break_price' => 0.00,
                'deposit' => 7000000.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ========================================
            // MEETING ROOM - Small (service_category_id = 1)
            // Pattern: 1 jam, 4 jam, 8 jam
            // ========================================
            
            // 1 jam - tanpa coffee
            [
                'room_id' => $roomId,
                'room_type_id' => 2,
                'service_category_id' => 1,
                'duration_type' => 'hour',
                'duration' => 1,
                'base_price' => 125000.00,
                'coffee_break_option' => null,
                'coffee_break_price' => 0.00,
                'deposit' => 0.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // 4 jam - dengan coffee 1x
            [
                'room_id' => $roomId,
                'room_type_id' => 2,
                'service_category_id' => 1,
                'duration_type' => 'hour',
                'duration' => 4,
                'base_price' => 300000.00,
                'coffee_break_option' => 1,
                'coffee_break_price' => 350000.00,
                'deposit' => 0.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // 8 jam - dengan coffee 1x
            [
                'room_id' => $roomId,
                'room_type_id' => 2,
                'service_category_id' => 1,
                'duration_type' => 'hour',
                'duration' => 8,
                'base_price' => 550000.00,
                'coffee_break_option' => 1,
                'coffee_break_price' => 650000.00,
                'deposit' => 0.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ========================================
            // MEETING ROOM - Big (service_category_id = 2)
            // Pattern: 1 jam, 4 jam, 8 jam (dengan variasi coffee)
            // ========================================
            
            // 1 jam - tanpa coffee
            [
                'room_id' => $roomId,
                'room_type_id' => 2,
                'service_category_id' => 2,
                'duration_type' => 'hour',
                'duration' => 1,
                'base_price' => 21000.00,
                'coffee_break_option' => null,
                'coffee_break_price' => 0.00,
                'deposit' => 0.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // 4 jam - dengan coffee 1x
            [
                'room_id' => $roomId,
                'room_type_id' => 2,
                'service_category_id' => 2,
                'duration_type' => 'hour',
                'duration' => 4,
                'base_price' => 45000.00,
                'coffee_break_option' => 1,
                'coffee_break_price' => 55000.00,
                'deposit' => 0.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // 8 jam - dengan coffee 1x
            [
                'room_id' => $roomId,
                'room_type_id' => 2,
                'service_category_id' => 2,
                'duration_type' => 'hour',
                'duration' => 8,
                'base_price' => 90000.00,
                'coffee_break_option' => 1,
                'coffee_break_price' => 100000.00,
                'deposit' => 0.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // 8 jam - dengan coffee 2x
            [
                'room_id' => $roomId,
                'room_type_id' => 2,
                'service_category_id' => 2,
                'duration_type' => 'hour',
                'duration' => 8,
                'base_price' => 90000.00,
                'coffee_break_option' => 2,
                'coffee_break_price' => 110000.00,
                'deposit' => 0.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
    }

    /**
     * Optional: Method untuk custom harga per room jika diperlukan
     * Contoh jika room tertentu punya harga berbeda
     */
    private function getCustomPrices($roomId, $roomNumber): array
    {
        // Contoh: Room 204 (ID 21 di localhost, ID 13 di production) punya harga khusus
        $customPrices = [
            '204' => [
                'private_office_month' => 7500000.00,
                'private_office_year' => 67500000.00,
            ],
            '205' => [
                'private_office_hour' => 49500.00,
                'private_office_month' => 5000000.00,
                'private_office_year' => 45000000.00,
            ],
        ];

        return $customPrices[$roomNumber] ?? [];
    }
}