<?php
// database/migrations/2024_01_01_000001_create_lunch_options_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLunchOptionsTable extends Migration
{
    public function up()
    {
        Schema::create('lunch_options', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->boolean('is_available')->default(true);
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['is_available', 'location_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('lunch_options');
    }
}