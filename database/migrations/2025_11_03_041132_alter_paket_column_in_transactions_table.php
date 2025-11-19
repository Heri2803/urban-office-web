<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Ubah enum menjadi VARCHAR(50)
            $table->string('paket', 50)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Kembalikan ke ENUM jika rollback
            $table->enum('paket', ['monthly', 'yearly'])->nullable()->change();
        });
    }

};
