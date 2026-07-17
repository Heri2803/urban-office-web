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
        Schema::table('transactions', function (Blueprint $table) {
            // Tambah kolom npwp setelah kolom nik
            $table->string('npwp', 50)
                  ->nullable()
                  ->after('nik');
            
            // Tambah index untuk pencarian (optional)
            $table->index('npwp', 'transactions_npwp_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Hapus index terlebih dahulu
            $table->dropIndex('transactions_npwp_index');
            
            // Hapus kolom npwp
            $table->dropColumn('npwp');
        });
    }
};