<?php
// database/migrations/2024_01_01_000002_create_promos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            
            // Basic Info
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            
            // Classification
            $table->foreignId('promo_type_id')->constrained();
            $table->foreignId('promo_category_id')->constrained();
            
            // Status & Timing
            $table->enum('status', ['draft', 'active', 'inactive', 'ended', 'upcoming'])->default('draft');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('priority')->default(1);
            
            // Targeting
            $table->json('locations')->nullable(); // ['jakarta-pusat', 'surabaya', 'bandung']
            $table->json('service_types')->nullable(); // ['private-office', 'virtual-office']
            
            // Visual & Content
            $table->string('image_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->json('images')->nullable(); // Multiple images for carousel
            
            // Promotion Settings
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable();
            $table->decimal('min_transaction', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_per_user')->default(1);
            
            // Tracking
            $table->integer('view_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->integer('usage_count')->default(0);
            
            // Admin Controls
            $table->boolean('is_approved')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            
            // Audit
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['status', 'start_date', 'end_date']);
            $table->index(['code', 'status']);
            $table->index(['promo_type_id', 'promo_category_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('promos');
    }
};