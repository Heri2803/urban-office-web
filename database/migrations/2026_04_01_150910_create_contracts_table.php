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
        Schema::create('contracts', function (Blueprint $table) {
            // =============================================
            // PRIMARY KEY
            // =============================================
            $table->id();

            // =============================================
            // FOREIGN KEYS
            // =============================================
            $table->foreignId('transaction_id')
                  ->constrained('transactions')
                  ->cascadeOnDelete();

            $table->foreignId('invoice_id')
                  ->constrained('invoices')
                  ->cascadeOnDelete();

            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // =============================================
            // IDENTITAS KONTRAK
            // =============================================
            $table->string('contract_number')->unique()->nullable();

            $table->enum('type', [
                'Virtual Office',
                'Private Office',
            ]);

            // =============================================
            // STATUS KONTRAK
            // =============================================
            $table->enum('status', [
                'draft',        // Auto-created saat settlement, belum di-publish
                'active',       // PDF sudah digenerate, kontrak berjalan
                'expired',      // Habis masa sewa secara natural (end_date terlewati)
                'terminated',   // Diputus paksa sebelum habis
                'renewed',      // Diperpanjang (record lama tetap tersimpan sebagai history)
            ])->default('draft');

            // =============================================
            // PERIODE KONTRAK
            // =============================================
            $table->date('contract_date')->nullable();

            $table->date('start_date')->nullable();

            $table->date('end_date')->nullable();
                  

            // =============================================
            // SKEMA PEMBAYARAN
            // =============================================
            $table->enum('payment_scheme', [
                'full',     // Bayar penuh di awal
                'monthly',  // Bayar per bulan meski kontrak tahunan
            ])->default('full');

            $table->date('next_payment_date')->nullable();
                  

            $table->date('last_paid_date')->nullable();
                  
            // =============================================
            // FILE PDF
            // =============================================
            $table->string('file_path')->nullable();
               

            // =============================================
            // TERMINASI
            // =============================================
            $table->text('termination_reason')->nullable();
            $table->timestamp('terminated_at')->nullable();

            // =============================================
            // TIMESTAMPS
            // =============================================
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};