<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            if (Schema::hasColumn('locations', 'mitra_id')) {
                // pastikan tidak ada FK aktif sebelum drop kolom
                try {
                    DB::statement('ALTER TABLE locations DROP FOREIGN KEY locations_mitra_id_foreign');
                } catch (\Exception $e) {
                    // abaikan jika FK belum ada
                }

                $table->dropColumn('mitra_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->unsignedBigInteger('mitra_id')->nullable()->after('city_id');
        });
    }
};
