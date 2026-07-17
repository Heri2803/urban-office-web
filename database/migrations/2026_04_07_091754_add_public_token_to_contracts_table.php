<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_add_public_token_to_contracts_table.php
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('public_token', 64)->unique()->nullable()->after('file_path');
            $table->timestamp('token_expires_at')->nullable()->after('public_token');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['public_token', 'token_expires_at']);
        });
    }
};
