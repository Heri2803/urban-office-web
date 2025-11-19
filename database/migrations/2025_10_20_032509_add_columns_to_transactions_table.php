<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Tambahkan kolom hari sebelum kolom bulan
            $table->string('hari')->nullable()->after('paket');

            // Tambahkan kolom deposit sebelum kolom gross_amout
            $table->decimal('deposit', 12, 2)->nullable()->after('order_id');

            $table->unsignedBigInteger('location_id')->nullable()->after('user_id');
            $table->string('detail_location')->nullable()->after('location_id');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn(['hari', 'deposit', 'location_id', 'detail_location']);
        });
    }
};
