<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Renames master_file* tables to document* tables while preserving data.
     */
    public function up(): void
    {
        // Step 1: Drop foreign key constraints on master_files table
        Schema::table('master_files', function ($table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['parent_file_id']);
            $table->dropForeign(['uploaded_by']);
            $table->dropForeign(['approved_by']);
        });

        // Step 2: Drop foreign key constraints on master_file_access_logs table
        Schema::table('master_file_access_logs', function ($table) {
            $table->dropForeign(['file_id']);
            $table->dropForeign(['user_id']);
        });

        // Step 3: Drop foreign key constraints on master_file_categories table
        Schema::table('master_file_categories', function ($table) {
            $table->dropForeign(['created_by']);
        });

        // Step 4: Rename tables using SQL Server syntax
        DB::statement('EXEC sp_rename \'master_file_categories\', \'document_categories\'');
        DB::statement('EXEC sp_rename \'master_file_access_logs\', \'document_access_logs\'');
        DB::statement('EXEC sp_rename \'master_files\', \'documents\'');

        // Step 5: Re-add foreign key constraints with new table names
        Schema::table('document_categories', function ($table) {
            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::table('documents', function ($table) {
            $table->foreign('category_id')->references('id')->on('document_categories');
            $table->foreign('parent_file_id')->references('id')->on('documents');
            $table->foreign('uploaded_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
        });

        Schema::table('document_access_logs', function ($table) {
            $table->foreign('file_id')->references('id')->on('documents');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Drop foreign key constraints
        Schema::table('documents', function ($table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['parent_file_id']);
            $table->dropForeign(['uploaded_by']);
            $table->dropForeign(['approved_by']);
        });

        Schema::table('document_access_logs', function ($table) {
            $table->dropForeign(['file_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('document_categories', function ($table) {
            $table->dropForeign(['created_by']);
        });

        // Step 2: Rename tables back
        DB::statement('EXEC sp_rename \'documents\', \'master_files\'');
        DB::statement('EXEC sp_rename \'document_access_logs\', \'master_file_access_logs\'');
        DB::statement('EXEC sp_rename \'document_categories\', \'master_file_categories\'');

        // Step 3: Re-add original foreign key constraints
        Schema::table('master_file_categories', function ($table) {
            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::table('master_files', function ($table) {
            $table->foreign('category_id')->references('id')->on('master_file_categories');
            $table->foreign('parent_file_id')->references('id')->on('master_files');
            $table->foreign('uploaded_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
        });

        Schema::table('master_file_access_logs', function ($table) {
            $table->foreign('file_id')->references('id')->on('master_files');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
};
