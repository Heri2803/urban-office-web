<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceCategoryIdToTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            
            // ✅ Tambah kolom baru untuk tipe paket
            $table->unsignedBigInteger('service_category_id')
                  ->nullable()
                  ->after('room_type');

            // ✅ Foreign key ke tabel service_categories
            $table->foreign('service_category_id')
                  ->references('id')
                  ->on('service_categories')
                  ->nullOnDelete(); 
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['service_category_id']);
            $table->dropColumn(['service_category_id', 'duration']);
        });
    }
}
