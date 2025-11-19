<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_prices', function (Blueprint $table) {
            $table->unsignedBigInteger('room_id')->nullable()->change();
            $table->unsignedBigInteger('room_type_id')->nullable()->change();
            $table->decimal('deposit', 12, 2)->nullable()->change();
            $table->decimal('coffee_break_price', 12, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('service_prices', function (Blueprint $table) {
            $table->unsignedBigInteger('room_id')->nullable(false)->change();
            $table->unsignedBigInteger('room_type_id')->nullable(false)->change();
            $table->decimal('deposit', 12, 2)->nullable(false)->default(0.00)->change();
            $table->decimal('coffee_break_price', 12, 2)->nullable(false)->default(0.00)->change();
        });
    }
};
