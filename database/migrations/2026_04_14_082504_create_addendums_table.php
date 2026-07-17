<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addendums', function (Blueprint $table) {
            $table->id();

            // Relasi utama
            $table->foreignId('contract_id')
                  ->constrained('contracts')
                  ->cascadeOnDelete();

            $table->foreignId('transaction_id')
                  ->constrained('transactions')
                  ->cascadeOnDelete();

            $table->foreignId('invoice_id')
                  ->nullable()
                  ->constrained('invoices')
                  ->nullOnDelete();

            // Nomor & urutan
            $table->string('addendum_number')->nullable(); // ORDER-XXX/179/Urban Office/IV/2026
            $table->unsignedInteger('addendum_order');     // 1, 2, 3 per contract_id (untuk label I, II, III)
            $table->unsignedInteger('sequence_number');    // global sequence (179, 180, dst)

            // Tanggal
            $table->date('addendum_date')->nullable();     // input customer sebelum bayar
            $table->date('start_date')->nullable();        // sama dengan addendum_date (auto-fill)
            $table->date('end_date')->nullable();          // start_date + duration

            // Durasi
            $table->unsignedInteger('duration');           // angka durasi (misal 14)
            $table->enum('duration_type', ['month', 'year'])->default('month');

            // Keuangan
            $table->decimal('gross_amount', 15, 2)->default(0);

            // File & verifikasi
            $table->string('file_path')->nullable();       // path PDF addendum
            $table->string('public_token')->nullable()->unique(); // untuk QR code
            $table->timestamp('token_expires_at')->nullable();

            // Status
            $table->enum('status', ['draft', 'active', 'expired', 'terminated'])
                  ->default('draft');

            // Audit
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addendums');
    }
};