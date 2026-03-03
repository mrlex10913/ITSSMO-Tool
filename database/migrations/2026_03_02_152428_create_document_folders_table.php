<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('color', 7)->default('#3B82F6');
            $table->string('icon')->default('folder');
            $table->string('department')->nullable();
            $table->json('visible_to_departments')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('document_folders')->onDelete('no action');
            $table->foreign('created_by')->references('id')->on('users');
            $table->index(['parent_id', 'department']);
            $table->unique(['slug', 'parent_id', 'department'], 'document_folders_slug_parent_dept_unique');
        });

        // Add folder_id to documents table
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('folder_id')->nullable()->after('category_id');
            $table->foreign('folder_id')->references('id')->on('document_folders')->onDelete('set null');
            $table->index('folder_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
            $table->dropIndex(['folder_id']);
            $table->dropColumn('folder_id');
        });

        Schema::dropIfExists('document_folders');
    }
};
