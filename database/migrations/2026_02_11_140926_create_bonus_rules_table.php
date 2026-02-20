<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonus_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Paket VO Premium - 5 Jam Meeting"
            $table->string('room_type_trigger'); // 'Virtual Office'
            $table->integer('min_gross_amount')->default(0);
            $table->integer('bonus_hours')->default(0);
            $table->integer('valid_days')->default(30); // bonus berlaku berapa hari
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonus_rules');
    }
};