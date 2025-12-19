<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('csat_responses')) {
            return; // base table not present yet
        }
        Schema::table('csat_responses', function (Blueprint $table): void {
            if (! Schema::hasColumn('csat_responses', 'dismissed_until')) {
                $table->timestamp('dismissed_until')->nullable()->after('submitted_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('csat_responses')) {
            return;
        }
        Schema::table('csat_responses', function (Blueprint $table): void {
            if (Schema::hasColumn('csat_responses', 'dismissed_until')) {
                $table->dropColumn('dismissed_until');
            }
        });
    }
};
