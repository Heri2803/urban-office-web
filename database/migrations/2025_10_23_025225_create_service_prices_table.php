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
        Schema::create('service_prices', function (Blueprint $table) {
            $table->id(); // auto-increment BIGINT sebagai primary key

            // Kolom relasi ke tabel rooms dan room_types
            $table->unsignedBigInteger('room_id');       // kolom room_id
            $table->unsignedBigInteger('room_type_id');  // kolom room_type_id

            // Jenis layanan dan durasi
            $table->string('service_type');  // contoh: 'Private Office', 'Meeting Room', dll
            $table->string('duration_type'); // contoh: 'hour', 'day', 'week', 'month', 'year'

            // Harga dasar dan opsi tambahan
            $table->decimal('base_price', 12, 2);
            $table->string('coffee_break_option')->nullable(); // contoh: 'none', '1x', '2x'
            $table->decimal('coffee_break_price', 12, 2)->default(0);
            $table->decimal('deposit', 12, 2)->default(0);

            $table->timestamps();

            // Relasi foreign key
            $table->foreign('room_id')
                  ->references('id')->on('rooms')
                  ->onDelete('cascade');

            $table->foreign('room_type_id')
                  ->references('id')->on('room_types')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_prices');
    }
};
