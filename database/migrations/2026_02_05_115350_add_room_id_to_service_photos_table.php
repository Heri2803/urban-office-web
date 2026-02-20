<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_photos', function (Blueprint $table) {
            // Tambah kolom room_id
            $table->foreignId('room_id')
                ->nullable()
                ->after('room_type_id')
                ->constrained('rooms')
                ->onDelete('cascade');
            
            // Tambah index untuk performa query
            $table->index(['location_id', 'room_type_id', 'room_id']);
            $table->index(['room_id', 'is_primary']);
            $table->index(['location_id', 'room_id']);
        });
        
        // Data migration: Update existing photos dengan room_id yang sesuai
        $this->migrateExistingPhotos();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_photos', function (Blueprint $table) {
            // Hapus foreign key constraint
            $table->dropForeign(['room_id']);
            
            // Hapus indexes
            $table->dropIndex(['location_id_room_type_id_room_id']);
            $table->dropIndex(['room_id_is_primary']);
            $table->dropIndex(['location_id_room_id']);
            
            // Hapus kolom
            $table->dropColumn('room_id');
        });
    }
    
    /**
     * Migrate existing photos to have room_id
     */
    private function migrateExistingPhotos(): void
    {
        // 1. Update photos yang sudah ada dengan room_id dari rooms yang match
        DB::statement("
            UPDATE service_photos sp
            INNER JOIN rooms r ON sp.room_type_id = r.room_type_id 
                AND sp.location_id = r.location_id
            SET sp.room_id = r.id
            WHERE sp.room_id IS NULL
            AND EXISTS (
                SELECT 1 FROM rooms r2 
                WHERE r2.room_type_id = sp.room_type_id 
                AND r2.location_id = sp.location_id
                LIMIT 1
            )
        ");
        
        // 2. Log hasil migration
        $updatedCount = DB::table('service_photos')->whereNotNull('room_id')->count();
        $nullCount = DB::table('service_photos')->whereNull('room_id')->count();
        
        \Log::info("Room ID Migration: {$updatedCount} photos updated, {$nullCount} photos without room");
    }
};