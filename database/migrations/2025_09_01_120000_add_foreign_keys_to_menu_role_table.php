<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add FKs if the columns exist and FKs aren't already present
        Schema::table('menu_role', function (Blueprint $table) {
            // Guard: SQL Server needs explicit names and existence checks are manual; try/catch handled by migration runner
            if (! Schema::hasColumn('menu_role', 'menu_id') || ! Schema::hasColumn('menu_role', 'role_id')) {
                return;
            }
            try {
                $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
            } catch (Throwable $e) { /* ignore if already exists */
            }
            try {
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            } catch (Throwable $e) { /* ignore if already exists */
            }
        });
    }

    public function down(): void
    {
        Schema::table('menu_role', function (Blueprint $table) {
            try {
                $table->dropForeign(['menu_id']);
            } catch (Throwable $e) {
            }
            try {
                $table->dropForeign(['role_id']);
            } catch (Throwable $e) {
            }
        });
    }
};
