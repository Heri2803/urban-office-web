<?php
// database/migrations/2024_03_15_000002_create_invoices_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->unsignedBigInteger('transaction_id');
            $table->enum('status', ['pending', 'settlement', 'expired'])->default('pending');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('transaction_id')
                  ->references('id')
                  ->on('transactions')
                  ->onDelete('cascade');
                  
            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');
            
            // Indexes untuk performa
            $table->index('invoice_number');
            $table->index('transaction_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};