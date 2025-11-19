<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number'); // Nomor ruangan, misal 301
            $table->unsignedBigInteger('room_type_id'); // nanti bisa direlasikan
            $table->integer('floor')->nullable();
            $table->integer('capacity')->nullable();
            $table->decimal('size_m2', 5, 2)->nullable();
            $table->unsignedBigInteger('location_id'); // nanti bisa direlasikan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
