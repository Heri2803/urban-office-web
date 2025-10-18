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
        Schema::create('mitra', function (Blueprint $table) {
            $table->id();
            
            // Kolom dari input form
            $table->string('nama_lengkap_ktp', 255);
            $table->string('alamat_email')->unique();
            $table->text('alamat_properti');
            $table->string('nik', 16)->unique();
            $table->string('foto_properti_path'); // Path penyimpanan file foto
            
            // Kolom untuk status aplikasi
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitra');
    }
};
