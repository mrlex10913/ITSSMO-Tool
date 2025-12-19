<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('csat_responses', function (Blueprint $table): void {
            $table->timestamp('dismissed_until')->nullable()->after('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('csat_responses', function (Blueprint $table): void {
            $table->dropColumn('dismissed_until');
        });
    }
};
