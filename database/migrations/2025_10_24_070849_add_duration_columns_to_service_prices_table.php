<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::table('service_prices', function (Blueprint $table) {
            $table->foreignId('service_category_id')
                ->nullable()
                ->constrained('service_categories')
                ->onDelete('set null')
                ->after('room_type_id');
        });
    }

    /**
     * Batalkan migration.
     */
    public function down(): void
    {
        Schema::table('service_prices', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }
};
