<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_highlights', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 'Free Wi-Fi', 'Projector'
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0); // ✅ PENTING untuk urutan
            $table->boolean('is_active')->default(true);
            $table->boolean('show_in_all_services')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_highlights');
    }
};