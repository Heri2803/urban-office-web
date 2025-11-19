<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Pastikan mitra_id ada dan bertipe sama dengan id di tabel mitras
            $table->unsignedBigInteger('mitra_id')->nullable()->change();
            
            // Tambahkan foreign key constraint
            $table->foreign('mitra_id')
                  ->references('id')
                  ->on('mitra')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['mitra_id']);
            $table->integer('mitra_id')->nullable()->change(); // Kembalikan ke tipe semula jika perlu
        });
    }
};