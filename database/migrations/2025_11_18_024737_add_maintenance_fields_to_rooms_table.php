<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaintenanceFieldsToRoomsTable extends Migration
{
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->date('maintenance_start')->nullable()->after('status');
            $table->date('maintenance_end')->nullable()->after('maintenance_start');
            $table->text('maintenance_reason')->nullable()->after('maintenance_end');
        });
    }

    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['maintenance_start', 'maintenance_end', 'maintenance_reason']);
        });
    }
}