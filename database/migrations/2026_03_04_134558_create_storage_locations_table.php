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
        Schema::create('storage_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);                    // Display name: "Primary Storage", "Archive Drive", etc.
            $table->string('disk', 50);                     // Laravel disk name: "public", "documents_archive", etc.
            $table->string('path_prefix', 255)->nullable(); // Optional subfolder within disk: "documents", "archive/2025"
            $table->string('driver', 50)->default('local'); // local, s3, ftp, etc.
            $table->string('root_path', 500)->nullable();   // Physical path for local disks: "D:\DocumentStorage"
            $table->bigInteger('max_size_bytes')->nullable(); // Maximum storage size (null = unlimited)
            $table->bigInteger('used_size_bytes')->default(0); // Current used space
            $table->boolean('is_default')->default(false);  // Is this the default for new uploads?
            $table->boolean('is_active')->default(true);    // Can files be uploaded here?
            $table->boolean('is_readonly')->default(false); // Is this location readonly (for archived storage)?
            $table->text('description')->nullable();        // Admin notes
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('is_default');
            $table->index('is_active');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        // Add storage_location_id to documents table
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('storage_location_id')->nullable()->after('folder_id');
            $table->foreign('storage_location_id')->references('id')->on('storage_locations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['storage_location_id']);
            $table->dropColumn('storage_location_id');
        });

        Schema::dropIfExists('storage_locations');
    }
};
