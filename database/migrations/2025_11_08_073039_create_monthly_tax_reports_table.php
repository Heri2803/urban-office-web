<?php
// database/migrations/2024_01_01_000001_create_monthly_tax_reports_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyTaxReportsTable extends Migration
{
    public function up()
    {
        Schema::create('monthly_tax_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            
            // Period: tanggal 1 setiap bulan (e.g., 2024-09-01 untuk September 2024)
            $table->date('period');
            
            // Data summary
            $table->integer('total_transactions')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0); // 10% of revenue
            
            // Status flow: draft → generated → reported → paid
            $table->enum('status', ['draft', 'generated', 'reported', 'paid'])->default('draft');
            
            // Invoice info
            $table->string('invoice_number')->nullable();
            $table->text('notes')->nullable();
            
            // Reporting info
            $table->timestamp('reported_at')->nullable();
            $table->string('reported_by')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Unique constraint: satu lokasi hanya punya satu report per bulan
            $table->unique(['location_id', 'period']);
            
            // Index untuk performance
            $table->index('period');
            $table->index('status');
            $table->index('invoice_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('monthly_tax_reports');
    }
}