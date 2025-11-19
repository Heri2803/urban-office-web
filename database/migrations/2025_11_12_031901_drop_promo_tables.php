<?php
// database/migrations/2024_01_01_000000_drop_promo_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop dalam urutan yang benar (child dulu, parent kemudian)
        Schema::dropIfExists('promo_categories');
        Schema::dropIfExists('promo_types');
    }

    public function down()
    {
        // Optional: bisa recreate tables jika rollback needed
        // Tapi untuk case ini, kita tidak perlu
    }
};