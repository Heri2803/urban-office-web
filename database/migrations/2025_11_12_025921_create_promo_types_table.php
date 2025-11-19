<?php
// database/migrations/2024_01_01_000001_create_promo_types_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('promo_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Banner, Discount, Voucher
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('promo_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Hero, Section, Popup, Private Office, etc
            $table->string('slug')->unique();
            $table->foreignId('promo_type_id')->constrained();
            $table->json('settings')->nullable(); // Category-specific settings
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed initial data
        DB::table('promo_types')->insert([
            ['name' => 'Banner', 'slug' => 'banner', 'description' => 'Visual promotional banners', 'is_active' => true],
            ['name' => 'Discount', 'slug' => 'discount', 'description' => 'Price discount promotions', 'is_active' => true],
            ['name' => 'Voucher', 'slug' => 'voucher', 'description' => 'Voucher code promotions', 'is_active' => true],
        ]);

        DB::table('promo_categories')->insert([
            // Banner categories - WITH settings
            ['name' => 'Hero Banner', 'slug' => 'hero-banner', 'promo_type_id' => 1, 'settings' => '{"width":1920,"height":600}', 'is_active' => true],
            ['name' => 'Section Banner', 'slug' => 'section-banner', 'promo_type_id' => 1, 'settings' => '{"width":800,"height":400}', 'is_active' => true],
            ['name' => 'Popup Banner', 'slug' => 'popup-banner', 'promo_type_id' => 1, 'settings' => '{"width":600,"height":400}', 'is_active' => true],
            
            // Service categories - WITH NULL settings dan is_active
            ['name' => 'Private Office', 'slug' => 'private-office', 'promo_type_id' => 2, 'settings' => null, 'is_active' => true],
            ['name' => 'Virtual Office', 'slug' => 'virtual-office', 'promo_type_id' => 2, 'settings' => null, 'is_active' => true],
            ['name' => 'Coworking Space', 'slug' => 'coworking-space', 'promo_type_id' => 2, 'settings' => null, 'is_active' => true],
            ['name' => 'Meeting Room', 'slug' => 'meeting-room', 'promo_type_id' => 2, 'settings' => null, 'is_active' => true],
            ['name' => 'Event Space', 'slug' => 'event-space', 'promo_type_id' => 2, 'settings' => null, 'is_active' => true],
            ['name' => 'Sharing Room', 'slug' => 'sharing-room', 'promo_type_id' => 2, 'settings' => null, 'is_active' => true],
            ['name' => 'All Services', 'slug' => 'all-services', 'promo_type_id' => 2, 'settings' => null, 'is_active' => true],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('promo_categories');
        Schema::dropIfExists('promo_types');
    }
};