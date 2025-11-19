<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan kolom sudah ada sebelum diubah posisinya
        if (Schema::hasColumn('transactions', 'location_id') && Schema::hasColumn('transactions', 'detail_location')) {

            // Ubah posisi kolom menggunakan SQL manual
            DB::statement("ALTER TABLE transactions MODIFY COLUMN location_id BIGINT UNSIGNED NULL AFTER user_id");
            DB::statement("ALTER TABLE transactions MODIFY COLUMN detail_location VARCHAR(255) NULL AFTER location_id");

            // Tambahkan relasi foreign key ke tabel locations
            Schema::table('transactions', function (Blueprint $table) {
                // Hapus FK lama jika ada (biar tidak bentrok)
                // Tambahkan ulang FK baru
                $table->foreign('location_id')
                    ->references('id')
                    ->on('locations')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            });
        }
    }

    public function down(): void
    {
        // Rollback ke posisi sebelumnya (opsional)
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
        });

        DB::statement("ALTER TABLE transactions MODIFY COLUMN location_id BIGINT UNSIGNED NULL AFTER order_id");
        DB::statement("ALTER TABLE transactions MODIFY COLUMN detail_location VARCHAR(255) NULL AFTER location_id");
    }
};
