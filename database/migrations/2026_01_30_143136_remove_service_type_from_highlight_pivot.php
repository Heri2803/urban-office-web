<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_highlight_room_type', function (Blueprint $table) {
            // Hanya hapus service_type, pertahankan service_category_id
            $table->dropColumn('service_type');
        });
    }

    public function down(): void
    {
        Schema::table('service_highlight_room_type', function (Blueprint $table) {
            // Jika perlu rollback, tambah kembali service_type
            $table->enum('service_type', ['category', 'room_type'])
                  ->default('room_type');
        });
    }
};