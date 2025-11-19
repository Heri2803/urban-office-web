<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set room_type_id menjadi NULL untuk multi-purpose rooms
        DB::table('rooms')
            ->whereIn('id', [19, 20, 21, 22]) // ✅ Sesuaikan dengan ID di localhost Anda
            ->update(['room_type_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: kembalikan ke room_type_id = 2 (Meeting Room)
        // Sesuaikan dengan data original Anda
        DB::table('rooms')
            ->whereIn('id', [19, 20, 21, 22])
            ->update(['room_type_id' => 2]);
    }
};