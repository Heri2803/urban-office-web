<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_service_photos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->string('original_name');
            $table->string('file_path'); // Path di storage
            $table->string('file_url'); // Full URL untuk akses
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type');
            $table->text('caption')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->unsignedBigInteger('uploaded_by');
            $table->timestamps();

            // Index untuk performa
            $table->index(['room_type_id', 'is_primary']);
            $table->index('room_type_id');
            
            // Foreign keys
            $table->foreign('uploaded_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_photos');
    }
};