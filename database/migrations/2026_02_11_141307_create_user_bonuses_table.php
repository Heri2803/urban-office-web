<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bonus_rule_id')->nullable()->constrained('bonus_rules')->onDelete('set null');
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->integer('bonus_hours_total');
            $table->integer('bonus_hours_used')->default(0);
            $table->date('valid_until');
            $table->enum('status', ['active', 'expired', 'fully_used'])->default('active');
            $table->text('notes')->nullable(); // "Bonus dari pembayaran VO 15 Jan 2024"
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('valid_until');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_bonuses');
    }
};