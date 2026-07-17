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
        Schema::table('addendums', function (Blueprint $table) {
            $table->foreignId('parent_addendum_id')
                ->nullable()
                ->after('contract_id')
                ->constrained('addendums')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addendums', function (Blueprint $table) {
            $table->dropForeign(['parent_addendum_id']);
            $table->dropColumn('parent_addendum_id');
        });
    }
};
