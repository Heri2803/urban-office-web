<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_highlight_room', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_highlight_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['service_highlight_id', 'room_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_highlight_room');
    }
};