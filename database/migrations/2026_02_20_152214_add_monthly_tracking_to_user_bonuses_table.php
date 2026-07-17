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
        Schema::table('user_bonuses', function (Blueprint $table) {
            // Tambahkan field untuk monthly tracking
            $table->integer('last_claim_month')->nullable()->after('bonus_hours_used');
            $table->integer('last_claim_year')->nullable()->after('last_claim_month');
            $table->integer('months_activated')->default(0)->after('last_claim_year');
            
            // Optional: Tambahkan index untuk performance
            $table->index(['last_claim_year', 'last_claim_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_bonuses', function (Blueprint $table) {
            // Hapus field yang ditambahkan
            $table->dropColumn([
                'last_claim_month',
                'last_claim_year',
                'months_activated'
            ]);
        });
    }
};