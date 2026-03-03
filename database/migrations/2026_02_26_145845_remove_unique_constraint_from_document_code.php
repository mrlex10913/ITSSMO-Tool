<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove unique constraint from document_code to allow multiple versions
     * to share the same document code.
     */
    public function up(): void
    {
        // Drop the unique index on document_code (SQL Server syntax)
        // The index name is from the old table name before rename
        DB::statement('DROP INDEX IF EXISTS master_files_document_code_unique ON documents');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add unique constraint if needed (not recommended for versioning)
        DB::statement('CREATE UNIQUE INDEX master_files_document_code_unique ON documents (document_code) WHERE document_code IS NOT NULL');
    }
};
