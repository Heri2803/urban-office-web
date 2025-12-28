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
            // Tambahkan kolom untuk tracking price requests
            $table->enum('request_status', ['active', 'pending', 'rejected', 'inactive'])
                ->default('active')
                ->after('deposit')
                ->comment('Status: active=current price, pending=requested change, rejected=request rejected, inactive=old price');
            
            $table->decimal('previous_price', 12, 2)
                ->nullable()
                ->after('request_status')
                ->comment('Previous price before change (for pending/rejected requests)');
            
            $table->text('request_reason')
                ->nullable()
                ->after('previous_price')
                ->comment('Reason for price change request');
            
            $table->string('requested_by')
                ->nullable()
                ->after('request_reason')
                ->comment('User who submitted the request');
            
            $table->timestamp('requested_at')
                ->nullable()
                ->after('requested_by')
                ->comment('When the request was submitted');
            
            $table->string('reviewed_by')
                ->nullable()
                ->after('requested_at')
                ->comment('User who reviewed the request');
            
            $table->timestamp('reviewed_at')
                ->nullable()
                ->after('reviewed_by')
                ->comment('When the request was reviewed');
            
            // Tambahkan index untuk performa query
            $table->index(['request_status', 'service_category_id'], 'idx_status_category');
            $table->index(['request_status', 'requested_at'], 'idx_status_requested');
            
            // Optional: Untuk link ke parent/active record
            $table->foreignId('parent_id')
                ->nullable()
                ->after('id')
                ->constrained('service_prices')
                ->nullOnDelete()
                ->comment('Link to active/parent price record');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_prices', function (Blueprint $table) {
            // Hapus index terlebih dahulu
            $table->dropIndex('idx_status_category');
            $table->dropIndex('idx_status_requested');
            
            // Hapus foreign key constraint
            $table->dropForeign(['parent_id']);
            
            // Hapus kolom yang ditambahkan
            $table->dropColumn([
                'request_status',
                'previous_price',
                'request_reason',
                'requested_by',
                'requested_at',
                'reviewed_by',
                'reviewed_at',
                'parent_id'
            ]);
        });
    }
};