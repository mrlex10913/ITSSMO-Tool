<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Changes the unique constraint on slug from global uniqueness
     * to per-department uniqueness, allowing same category names
     * in different departments.
     */
    public function up(): void
    {
        Schema::table('document_categories', function (Blueprint $table) {
            // Drop the existing unique constraint on slug (uses old table name in index)
            $table->dropUnique('master_file_categories_slug_unique');

            // Add composite unique constraint on slug + department
            // This allows same slug in different departments
            $table->unique(['slug', 'department'], 'document_categories_slug_department_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_categories', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('document_categories_slug_department_unique');

            // Restore the original unique constraint on slug only
            $table->unique(['slug'], 'master_file_categories_slug_unique');
        });
    }
};
