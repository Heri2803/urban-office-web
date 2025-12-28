<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddSuperadminRoleToUsersTable extends Migration
{
    /**
     * Run the migrations - TAMBAHKAN 'superadmin' ke ENUM
     */
    public function up()
    {
        // SANGAT SIMPLE: Hanya 1 line SQL
        DB::statement("ALTER TABLE `users` 
            MODIFY COLUMN `role` 
            ENUM('customer', 'mitra', 'admin', 'superadmin') 
            NOT NULL DEFAULT 'customer'");
    }

    /**
     * Reverse - KEMBALIKAN ke 3 values saja
     */
    public function down()
    {
        DB::statement("ALTER TABLE `users` 
            MODIFY COLUMN `role` 
            ENUM('customer', 'mitra', 'admin') 
            NOT NULL DEFAULT 'customer'");
    }
}