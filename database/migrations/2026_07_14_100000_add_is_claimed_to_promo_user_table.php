<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promo_user', function (Blueprint $table) {
            $table->boolean('is_claimed')->default(false)->after('user_id');
            $table->timestamp('claimed_at')->nullable()->after('is_claimed');
        });

        // Sengaja TIDAK di-backfill true: baris lama yang sebenarnya hasil air-drop admin
        // (belum pernah benar-benar diklaim user) tidak boleh ikut ditandai claimed.
        // Baris hasil self-claim lama akan otomatis ter-set claimed lagi saat user
        // menekan "Claim Offer" berikutnya (lihat CustomerPromoController::claim()).
    }

    public function down(): void
    {
        Schema::table('promo_user', function (Blueprint $table) {
            $table->dropColumn(['is_claimed', 'claimed_at']);
        });
    }
};
