<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * SQL Server doesn't support ENUM - Laravel creates a VARCHAR with a CHECK constraint.
     * We need to drop the old constraint and add a new one with 'scheduled' included.
     */
    public function up(): void
    {
        // Find and drop the existing check constraint on status column
        $constraints = DB::select("
            SELECT cc.name
            FROM sys.check_constraints cc
            JOIN sys.columns c ON cc.parent_object_id = c.object_id AND cc.parent_column_id = c.column_id
            WHERE OBJECT_NAME(cc.parent_object_id) = 'tickets' AND c.name = 'status'
        ");

        foreach ($constraints as $constraint) {
            DB::statement("ALTER TABLE tickets DROP CONSTRAINT [{$constraint->name}]");
        }

        // Add new check constraint with 'scheduled' status included
        DB::statement("ALTER TABLE tickets ADD CONSTRAINT tickets_status_check CHECK (status IN ('scheduled', 'open', 'in_progress', 'resolved', 'closed'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new constraint
        DB::statement('ALTER TABLE tickets DROP CONSTRAINT tickets_status_check');

        // Re-add the original constraint without 'scheduled'
        DB::statement("ALTER TABLE tickets ADD CONSTRAINT tickets_status_check CHECK (status IN ('open', 'in_progress', 'resolved', 'closed'))");
    }
};
