<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cek dulu jika kolom belum ada
        $columns = DB::select("SHOW COLUMNS FROM service_highlight_room_type");
        $columnNames = array_column($columns, 'Field');
        
        if (!in_array('service_category_id', $columnNames)) {
            DB::statement("
                ALTER TABLE service_highlight_room_type
                ADD COLUMN service_category_id BIGINT UNSIGNED NULL AFTER room_type_id
            ");
        }
        
        
        // Tambah foreign key jika belum ada
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_NAME = 'service_highlight_room_type'
            AND COLUMN_NAME = 'service_category_id'
            AND CONSTRAINT_NAME LIKE '%foreign%'
        ");
        
        if (empty($constraints)) {
            DB::statement("
                ALTER TABLE service_highlight_room_type
                ADD CONSTRAINT fk_service_highlight_room_type_category
                FOREIGN KEY (service_category_id)
                REFERENCES service_categories(id)
                ON DELETE CASCADE
            ");
        }
    }

    public function down(): void
    {
        // Simple drop - tanpa conditional check
        DB::statement("
            ALTER TABLE service_highlight_room_type
            DROP FOREIGN KEY fk_service_highlight_room_type_category,
            DROP COLUMN service_category_id
        ");
    }
};