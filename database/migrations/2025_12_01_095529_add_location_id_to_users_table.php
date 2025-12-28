<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('location_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null');
            
            // Index untuk performa
            $table->index('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key constraint dulu
            $table->dropForeign(['location_id']);
            
            // Hapus index
            $table->dropIndex(['location_id']);
            
            // Hapus kolom
            $table->dropColumn('location_id');
        });
    }
};