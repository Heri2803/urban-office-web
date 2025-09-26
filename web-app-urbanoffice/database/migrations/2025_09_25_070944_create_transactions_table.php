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
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->nullable(); // user login (sementara bisa null)

        // Booking form
        $table->string('city');
        $table->string('room_type');
        $table->integer('jumlah_orang')->nullable(); 
        $table->date('booking_date')->nullable(); 

        // Virtual Office fields
        $table->enum('paket', ['monthly', 'yearly'])->nullable();
        $table->integer('bulan')->nullable();
        $table->integer('tahun')->nullable();

        // Meeting Room field
        $table->integer('jam')->nullable();

        // Contact & Info
        $table->enum('status_pkp', ['Non PKP', 'PKP'])->nullable();
        $table->string('phone')->nullable();
        $table->string('nama_lengkap');
        $table->string('email');

        // Transaksi Midtrans
        $table->string('order_id')->unique();
        $table->integer('gross_amount');
        $table->string('payment_type')->nullable();
        $table->string('status')->default('pending'); 
        $table->string('snap_token')->nullable();
        $table->timestamp('transaction_time')->nullable();

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
