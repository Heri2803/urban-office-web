<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Improvement untuk support:
     * - File upload (PDF, Word, Excel) selain image
     * - is_edited flag yang proper (bukan string manipulation)
     * - Soft delete yang proper (bukan string manipulation)
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {

            // ── 1. ATTACHMENT TYPE ──────────────────────────────────────────
            // Membedakan tipe file tanpa perlu parsing extension di frontend
            // Ditambahkan setelah kolom attachment_mime yang sudah ada
            $table->enum('attachment_type', ['image', 'pdf', 'word', 'excel'])
                  ->nullable()
                  ->after('attachment_mime');

            // ── 2. IS_EDITED FLAG ───────────────────────────────────────────
            // Menggantikan cara lama yang append string "(edited)" ke message
            // Ditambahkan setelah kolom read_at yang sudah ada
            $table->boolean('is_edited')
                  ->default(false)
                  ->after('read_at');

            $table->timestamp('edited_at')
                  ->nullable()
                  ->after('is_edited');

            // ── 3. SOFT DELETE ──────────────────────────────────────────────
            // Menggantikan cara lama yang manipulasi string [PESAN DIHAPUS]
            // Laravel SoftDeletes standard menggunakan kolom deleted_at
            $table->softDeletes();
        });

        // ── 4. BACKFILL: Bersihkan data lama yang pakai string manipulation ──
        // Update is_edited = true untuk pesan yang masih ada "(edited)" di message
        DB::table('messages')
            ->where('message', 'like', '%(edited)%')
            ->update(['is_edited' => true]);

        // Bersihkan tanda "(edited)" dari message yang sudah ada
        DB::statement("
            UPDATE messages 
            SET message = TRIM(REPLACE(message, '(edited)', ''))
            WHERE message LIKE '%(edited)%'
        ");

        // Soft delete pesan yang berisi "[PESAN DIHAPUS]"
        DB::statement("
            UPDATE messages 
            SET deleted_at = NOW()
            WHERE message LIKE '%[PESAN DIHAPUS]%'
        ");

        // ── 5. BACKFILL: Set attachment_type untuk data attachment yang sudah ada ──
        // Image
        DB::statement("
            UPDATE messages 
            SET attachment_type = 'image'
            WHERE attachment_mime LIKE 'image/%'
              AND attachment IS NOT NULL
              AND attachment_type IS NULL
        ");

        // PDF
        DB::statement("
            UPDATE messages 
            SET attachment_type = 'pdf'
            WHERE attachment_mime = 'application/pdf'
              AND attachment IS NOT NULL
              AND attachment_type IS NULL
        ");

        // Word
        DB::statement("
            UPDATE messages 
            SET attachment_type = 'word'
            WHERE attachment_mime IN (
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            )
            AND attachment IS NOT NULL
            AND attachment_type IS NULL
        ");

        // Excel
        DB::statement("
            UPDATE messages 
            SET attachment_type = 'excel'
            WHERE attachment_mime IN (
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            )
            AND attachment IS NOT NULL
            AND attachment_type IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {

            // Hapus soft delete
            $table->dropSoftDeletes();

            // Hapus kolom baru
            $table->dropColumn([
                'attachment_type',
                'is_edited',
                'edited_at',
            ]);
        });
    }
};