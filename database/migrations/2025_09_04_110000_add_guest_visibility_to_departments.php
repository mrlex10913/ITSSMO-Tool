<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('departments', 'is_guest_visible')) {
            Schema::table('departments', function (Blueprint $table): void {
                $table->boolean('is_guest_visible')->default(true)->after('is_active')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('departments', 'is_guest_visible')) {
            Schema::table('departments', function (Blueprint $table): void {
                $table->dropColumn('is_guest_visible');
            });
        }
    }
};
