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
    Schema::table('users', function (Blueprint $table) {
        $table->string('telephone')->nullable()->after('email');
        $table->string('alamat')->nullable()->after('telephone');
        $table->string('paket')->nullable()->after('alamat'); // sementara string, nanti bisa relasi ke tabel transaction
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['telephone', 'alamat', 'paket']);
    });
}

};
