<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan role 'finance' ke enum role di tabel 'users'
        DB::statement("ALTER TABLE `users` 
            MODIFY COLUMN `role` 
            ENUM('customer', 'mitra', 'admin', 'superadmin', 'finance') 
            NOT NULL DEFAULT 'customer'");

        // 2. Tambahkan kolom approval ke tabel 'invoices'
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('settlement_request_status', ['pending', 'approved', 'rejected'])->nullable();
            $table->unsignedBigInteger('settlement_requested_by')->nullable();
            $table->unsignedBigInteger('settlement_processed_by')->nullable();
            $table->text('settlement_request_notes')->nullable();
            $table->string('settlement_payment_proof')->nullable();
            $table->text('settlement_rejection_reason')->nullable();
            
            $table->foreign('settlement_requested_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            $table->foreign('settlement_processed_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Hapus foreign keys dan kolom dari tabel 'invoices'
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['settlement_requested_by']);
            $table->dropForeign(['settlement_processed_by']);
            
            $table->dropColumn([
                'settlement_request_status',
                'settlement_requested_by',
                'settlement_processed_by',
                'settlement_request_notes',
                'settlement_payment_proof',
                'settlement_rejection_reason',
            ]);
        });

        // 2. Kembalikan role ke enum sebelumnya di tabel 'users'
        DB::statement("ALTER TABLE `users` 
            MODIFY COLUMN `role` 
            ENUM('customer', 'mitra', 'admin', 'superadmin') 
            NOT NULL DEFAULT 'customer'");
    }
};
