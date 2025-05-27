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
        Schema::table('asset_movements', function (Blueprint $table) {
            try {
                $table->dropForeign(['assigned_to']);
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
        });

        DB::statement("
            UPDATE asset_movements
            SET assigned_to = NULL
            WHERE assigned_to IS NOT NULL
            AND assigned_to NOT IN (SELECT id FROM master_lists)
        ");

        Schema::table('asset_movements', function (Blueprint $table) {
            $table->foreign('assigned_to')->references('id')->on('master_lists')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_movements', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
        });

        DB::statement("
            UPDATE asset_movements
            SET assigned_to = NULL
            WHERE assigned_to IS NOT NULL
            AND assigned_to NOT IN (SELECT id FROM users)
        ");

        Schema::table('asset_movements', function (Blueprint $table) {
            // Re-add users foreign key
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }
};
