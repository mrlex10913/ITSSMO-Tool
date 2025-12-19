<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the FK first to alter the column in SQL Server
        Schema::table('ticket_attachments', function (Blueprint $table) {
            try {
                $table->dropForeign(['user_id']);
            } catch (\Throwable $e) { /* ignore if not exists */
            }
        });

        // Alter column to be nullable (SQL Server syntax)
        try {
            DB::statement('ALTER TABLE ticket_attachments ALTER COLUMN user_id bigint NULL');
        } catch (\Throwable $e) { /* may already be nullable */
        }

        // Recreate FK without cascading; NULL values are allowed
        Schema::table('ticket_attachments', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        // Revert to NOT NULL cautiously (may fail if null values exist)
        Schema::table('ticket_attachments', function (Blueprint $table) {
            try {
                $table->dropForeign(['user_id']);
            } catch (\Throwable $e) { /* ignore */
            }
        });
        // Attempt to set NOT NULL back
        try {
            DB::statement('UPDATE ticket_attachments SET user_id = 0 WHERE user_id IS NULL');
        } catch (\Throwable $e) {
        }
        try {
            DB::statement('ALTER TABLE ticket_attachments ALTER COLUMN user_id bigint NOT NULL');
        } catch (\Throwable $e) {
        }
        Schema::table('ticket_attachments', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
};
