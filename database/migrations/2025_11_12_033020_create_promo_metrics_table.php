<?php
// database/migrations/2024_01_01_000003_create_promo_metrics_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('promo_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')->constrained()->onDelete('cascade');
            $table->string('location'); // jakarta-pusat, surabaya, bandung
            $table->date('metric_date');
            
            // Engagement Metrics
            $table->integer('views')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('unique_visitors')->default(0);
            
            // Conversion Metrics
            $table->integer('transactions')->default(0);
            $table->decimal('revenue', 12, 2)->default(0);
            $table->integer('promo_usage')->default(0);
            $table->decimal('discount_amount_used', 12, 2)->default(0);
            
            // Customer Metrics
            $table->integer('new_customers')->default(0);
            $table->integer('returning_customers')->default(0);
            
            // Performance Calculations (cached for quick access)
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->decimal('revenue_per_click', 8, 2)->default(0);
            $table->decimal('avg_order_value', 8, 2)->default(0);
            
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['promo_id', 'location', 'metric_date']);
            
            // Indexes for reporting
            $table->index(['metric_date', 'location']);
            $table->index(['promo_id', 'metric_date']);
        });

        // Promo Usage Tracking
        Schema::create('promo_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('transaction_id')->constrained(); // Link to actual transaction
            $table->string('location');
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('transaction_amount', 12, 2);
            $table->json('metadata')->nullable(); // Additional usage data
            $table->timestamps();
            
            // Indexes
            $table->index(['promo_id', 'user_id']);
            $table->index(['location', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('promo_usages');
        Schema::dropIfExists('promo_metrics');
    }
};