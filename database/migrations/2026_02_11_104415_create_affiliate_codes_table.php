<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliateCodesTable extends Migration
{
    public function up()
    {
        Schema::create('affiliate_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('commission_percentage', 5, 2)->default(10.00);
            $table->date('start_date');
            $table->date('expiry_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index untuk performa query
            $table->index(['user_id', 'is_active']);
            $table->index(['expiry_date', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('affiliate_codes');
    }
}