<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Ubah nama kolom detail_location menjadi city_id
            if (Schema::hasColumn('transactions', 'detail_location')) {
                $table->renameColumn('detail_location', 'city_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Kembalikan ke nama semula jika di-rollback
            if (Schema::hasColumn('transactions', 'city_id')) {
                $table->renameColumn('city_id', 'detail_location');
            }
        });
    }
};
