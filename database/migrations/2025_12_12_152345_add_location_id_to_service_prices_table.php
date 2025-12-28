<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_prices', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->nullable()->after('parent_id');
            
            $table->foreign('location_id')
                  ->references('id')
                  ->on('locations')
                  ->onDelete('set null');
                  
            $table->index(['location_id', 'request_status'], 'service_prices_location_status_index');
            $table->index(['location_id', 'room_type_id'], 'service_prices_location_roomtype_index');
        });
    }

    public function down(): void
    {
        Schema::table('service_prices', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropIndex('service_prices_location_status_index');
            $table->dropIndex('service_prices_location_roomtype_index');
            $table->dropColumn('location_id');
        });
    }
};