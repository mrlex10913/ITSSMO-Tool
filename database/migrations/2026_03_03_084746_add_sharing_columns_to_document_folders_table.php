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
        Schema::table('document_folders', function (Blueprint $table) {
            // Make folders private by default (only visible to creator)
            $table->boolean('is_private')->default(true)->after('is_active');

            // Share with specific users (array of user IDs)
            $table->json('shared_with_users')->nullable()->after('is_private');

            // Share with specific departments
            $table->json('shared_with_departments')->nullable()->after('shared_with_users');

            // Share with department head only (not entire department)
            $table->boolean('share_with_department_head')->default(false)->after('shared_with_departments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_folders', function (Blueprint $table) {
            $table->dropColumn([
                'is_private',
                'shared_with_users',
                'shared_with_departments',
                'share_with_department_head',
            ]);
        });
    }
};
