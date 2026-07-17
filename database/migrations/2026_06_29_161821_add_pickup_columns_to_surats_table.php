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
        Schema::table('surats', function (Blueprint $table) {
            $table->date('tanggal_datang')->nullable()->after('tanggal_surat');
            $table->string('status_pengambilan')->default('belum_diambil')->after('status'); // belum_diambil, sudah_diambil
            $table->date('tanggal_diambil')->nullable()->after('status_pengambilan');
            $table->string('metode_pengambilan')->nullable()->after('tanggal_diambil'); // offline, delivery
            $table->string('kurir_pengiriman')->nullable()->after('metode_pengambilan'); // Nama ekspedisi
            $table->string('resi_pengiriman')->nullable()->after('kurir_pengiriman'); // Nomor resi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_datang',
                'status_pengambilan',
                'tanggal_diambil',
                'metode_pengambilan',
                'kurir_pengiriman',
                'resi_pengiriman'
            ]);
        });
    }
};
