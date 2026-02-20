<?php
// database/migrations/xxxx_add_location_id_to_service_photos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('service_photos', function (Blueprint $table) {
            // Tambah kolom location_id
            $table->foreignId('location_id')
                ->nullable()
                ->after('room_type_id')
                ->constrained('locations')
                ->onDelete('cascade');
            
            // Index untuk performa query
            $table->index(['location_id', 'room_type_id']);
            $table->index(['location_id', 'is_primary']);
            $table->index(['room_type_id', 'location_id']);
        });
        
        // ✅ DATA MIGRATION: Update existing records
        // Asumsi: Ambil location dari rooms yang menggunakan room_type tersebut
        $this->migrateExistingPhotos();
    }
    
    public function down()
    {
        Schema::table('service_photos', function (Blueprint $table) {
            // Drop foreign key dan index
            $table->dropForeign(['location_id']);
            $table->dropIndex(['location_id', 'room_type_id']);
            $table->dropIndex(['location_id', 'is_primary']);
            $table->dropIndex(['room_type_id', 'location_id']);
            
            // Drop kolom
            $table->dropColumn('location_id');
        });
    }
    
    private function migrateExistingPhotos()
    {
        // Update photos yang sudah ada dengan location_id
        // Ambil dari rooms yang menggunakan room_type tersebut
        DB::statement("
            UPDATE service_photos sp
            LEFT JOIN rooms r ON sp.room_type_id = r.room_type_id
            SET sp.location_id = r.location_id
            WHERE sp.location_id IS NULL
            AND r.location_id IS NOT NULL
        ");
        
        // Jika ada photos tanpa location (no matching rooms), set default location
        $defaultLocation = DB::table('locations')->first();
        if ($defaultLocation) {
            DB::table('service_photos')
                ->whereNull('location_id')
                ->update(['location_id' => $defaultLocation->id]);
        }
    }
};