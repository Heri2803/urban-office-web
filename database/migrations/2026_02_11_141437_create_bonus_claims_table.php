<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonus_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_bonus_id')->constrained('user_bonuses')->onDelete('cascade');
            $table->foreignId('meeting_transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users');
            $table->integer('hours_used');
            $table->date('claim_date');
            $table->time('claim_start_time');
            $table->enum('status', ['pending', 'confirmed', 'canceled'])->default('confirmed');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('user_bonus_id');
            $table->index('meeting_transaction_id');
            $table->index('admin_id');
            $table->index('claim_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonus_claims');
    }
};