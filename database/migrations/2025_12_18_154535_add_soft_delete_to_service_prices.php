<?php

// database/migrations/[timestamp]_add_soft_delete_to_service_prices.php
// Contoh: 2024_01_23_150000_add_soft_delete_to_service_prices.php

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
            // 1. Soft delete column (Laravel standard)
            $table->softDeletes(); // Ini akan menambah kolom `deleted_at` (timestamp, nullable)
            
            // 2. User yang menghapus (tracking)
            $table->unsignedBigInteger('deleted_by_user_id')
                ->nullable()
                ->after('deleted_at');
            
            // 3. Alasan penghapusan
            $table->string('deletion_reason', 500)
                ->nullable()
                ->after('deleted_by_user_id');
            
            // 4. Untuk tracking restore (optional)
            $table->unsignedBigInteger('restored_by_user_id')
                ->nullable()
                ->after('deletion_reason');
            
            $table->timestamp('restored_at')
                ->nullable()
                ->after('restored_by_user_id');
            
            $table->string('restore_reason', 500)
                ->nullable()
                ->after('restored_at');
            
            // 5. Foreign key constraints
            $table->foreign('deleted_by_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->onUpdate('cascade');
            
            $table->foreign('restored_by_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->onUpdate('cascade');
            
            // 6. Index untuk performance
            $table->index('deleted_at');
            $table->index(['deleted_at', 'request_status']);
            $table->index('deleted_by_user_id');
            $table->index('restored_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_prices', function (Blueprint $table) {
            // Hapus foreign keys
            $table->dropForeign(['deleted_by_user_id']);
            $table->dropForeign(['restored_by_user_id']);
            
            // Hapus indexes
            $table->dropIndex(['deleted_at']);
            $table->dropIndex(['deleted_at', 'request_status']);
            $table->dropIndex(['deleted_by_user_id']);
            $table->dropIndex(['restored_by_user_id']);
            
            // Hapus kolom (harus sesuai urutan yang benar)
            $table->dropColumn([
                'restore_reason',
                'restored_at',
                'restored_by_user_id',
                'deletion_reason',
                'deleted_by_user_id',
                'deleted_at', // dari softDeletes()
            ]);
        });
    }
};