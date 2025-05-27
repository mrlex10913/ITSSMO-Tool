<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pamo_assets', function (Blueprint $table) {
            try {
                $table->dropForeign(['assigned_to']);
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
        });

        // Step 2: Clean up orphaned records
        DB::statement("
            UPDATE pamo_assets
            SET assigned_to = NULL
            WHERE assigned_to IS NOT NULL
            AND assigned_to NOT IN (SELECT id FROM master_lists)
        ");

        // Step 3: Add new columns and constraints
        Schema::table('pamo_assets', function (Blueprint $table) {
            // Add assigned_to_type column if it doesn't exist
            if (!Schema::hasColumn('pamo_assets', 'assigned_to_type')) {
                $table->string('assigned_to_type')->nullable()->default('employee')->after('assigned_to');
            }

            // Add foreign key constraint to master_lists table
            $table->foreign('assigned_to')->references('id')->on('master_lists')->onDelete('set null');

            // Add index for better performance
            $table->index(['assigned_to', 'assigned_to_type']);
        });

        // Step 4: Update existing records to set assigned_to_type
        DB::statement("
            UPDATE pamo_assets
            SET assigned_to_type = 'employee'
            WHERE assigned_to IS NOT NULL
            AND assigned_to_type IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pamo_assets', function (Blueprint $table) {
            // Drop master_lists foreign key
            $table->dropForeign(['assigned_to']);

            // Drop index
            $table->dropIndex(['assigned_to', 'assigned_to_type']);
        });
        // Clean up data before re-adding users foreign key
        DB::statement("
            UPDATE pamo_assets
            SET assigned_to = NULL
            WHERE assigned_to IS NOT NULL
            AND assigned_to NOT IN (SELECT id FROM users)
        ");
        Schema::table('pamo_assets', function (Blueprint $table) {
            // Re-add users foreign key
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');

            // Remove assigned_to_type column
            $table->dropColumn('assigned_to_type');
        });
    }
};
