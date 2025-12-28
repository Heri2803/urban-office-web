<?php
// database/migrations/2024_01_01_000002_create_transaction_lunches_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionLunchesTable extends Migration
{
    public function up()
    {
        Schema::create('transaction_lunches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('lunch_option_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
            
            // Composite index untuk performance
            $table->index(['transaction_id', 'lunch_option_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaction_lunches');
    }
}