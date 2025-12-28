<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_prices', function (Blueprint $table) {
            // Tambah kolom untuk user ID references
            $table->unsignedBigInteger('requested_by_user_id')
                ->nullable()
                ->after('requested_by');
                
            $table->unsignedBigInteger('reviewed_by_user_id')
                ->nullable()
                ->after('reviewed_by');
            
            // Tambah foreign key constraints
            $table->foreign('requested_by_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
                
            $table->foreign('reviewed_by_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            
            // Tambah index untuk performance
            $table->index(['requested_by_user_id']);
            $table->index(['reviewed_by_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_prices', function (Blueprint $table) {
            // Hapus foreign keys
            $table->dropForeign(['requested_by_user_id']);
            $table->dropForeign(['reviewed_by_user_id']);
            
            // Hapus indexes
            $table->dropIndex(['requested_by_user_id']);
            $table->dropIndex(['reviewed_by_user_id']);
            
            // Hapus kolom
            $table->dropColumn(['requested_by_user_id', 'reviewed_by_user_id']);
        });
    }
};