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
        Schema::create('master_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('document_code')->unique(); // e.g., ITSS-MAN-2024-001
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('file_size');
            $table->string('mime_type');
            $table->string('version', 20)->default('1.0');
            $table->unsignedBigInteger('parent_file_id')->nullable(); // For versioning
            $table->enum('status', ['draft', 'active', 'superseded', 'archived', 'pending_approval'])->default('active');
            $table->date('effective_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('review_date')->nullable();
            $table->json('tags')->nullable();
            $table->text('revision_notes')->nullable();
            $table->string('department'); // Uploading department
            $table->json('visible_to_departments')->nullable(); // Which departments can see
            $table->unsignedBigInteger('uploaded_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->boolean('is_confidential')->default(false);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('master_file_categories');
            $table->foreign('parent_file_id')->references('id')->on('master_files');
            $table->foreign('uploaded_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');

            $table->index(['status', 'category_id', 'department']);
            $table->index(['document_code', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_files');
    }
};
