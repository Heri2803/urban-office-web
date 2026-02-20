<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAffiliateInfluencerColumnsToTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('affiliate_code_used', 50)->nullable()->after('status');
            $table->decimal('commission_earned', 12, 2)->default(0.00)->after('affiliate_code_used');
            
            // Index untuk pencarian dan join
            $table->index('affiliate_code_used');
            $table->index(['affiliate_code_used', 'status']);
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['affiliate_code_used']);
            $table->dropIndex(['affiliate_code_used', 'status']);
            $table->dropColumn(['affiliate_code_used', 'commission_earned']);
        });
    }
}