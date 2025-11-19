<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // tambahkan kolom mitra_id setelah city_id
            $table->unsignedBigInteger('mitra_id')->nullable()->after('city_id');

            // tambahkan foreign key ke tabel mitra
            $table->foreign('mitra_id')
                  ->references('id')
                  ->on('mitra')
                  ->onDelete('set null'); // jika mitra dihapus, lokasi tetap ada tapi mitra_id jadi null
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign(['mitra_id']);
            $table->dropColumn('mitra_id');
        });
    }
};
