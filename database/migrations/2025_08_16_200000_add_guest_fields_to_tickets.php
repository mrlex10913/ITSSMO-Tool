<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make requester_id nullable and add guest fields
        Schema::table('tickets', function (Blueprint $table) {
            // Drop FK to alter nullability in SQL Server
            try {
                $table->dropForeign(['requester_id']);
            } catch (\Throwable $e) { /* ignore if not exists */
            }
        });

        // SQL Server: alter column to nullable
        try {
            DB::statement('ALTER TABLE tickets ALTER COLUMN requester_id bigint NULL');
        } catch (\Throwable $e) { /* ignore if already nullable */
        }

        Schema::table('tickets', function (Blueprint $table) {
            // Recreate FK without cascading
            $table->foreign('requester_id')->references('id')->on('users');

            // Guest identity fields
            $table->string('requester_name')->nullable();
            $table->string('requester_email')->nullable()->index();
            $table->string('requester_idno')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'requester_name')) {
                $table->dropColumn('requester_name');
            }
            if (Schema::hasColumn('tickets', 'requester_email')) {
                $table->dropColumn('requester_email');
            }
            if (Schema::hasColumn('tickets', 'requester_idno')) {
                $table->dropColumn('requester_idno');
            }
        });
        // Note: We intentionally do not revert requester_id to NOT NULL to avoid data loss
    }
};
