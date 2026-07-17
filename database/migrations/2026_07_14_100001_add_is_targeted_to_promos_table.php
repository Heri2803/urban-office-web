<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->boolean('is_targeted')->default(false)->after('priority');
        });

        // Sengaja TIDAK di-backfill: tidak ada cara akurat membedakan baris promo_user lama
        // yang berasal dari air-drop admin vs self-claim publik. Voucher yang sebelumnya
        // sudah di-setup targeted harus dibuka & disimpan ulang sekali lewat form admin
        // (Edit Banner/Voucher) agar is_targeted ke-set dengan benar oleh kode terbaru.
    }

    public function down(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->dropColumn('is_targeted');
        });
    }
};
