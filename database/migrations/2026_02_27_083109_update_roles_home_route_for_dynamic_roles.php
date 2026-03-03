<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set home_route to 'generic.dashboard' for all roles that don't have a specific dashboard
        // (roles without dedicated dashboards should use the generic one)
        $excludedSlugs = ['administrator', 'developer', 'pamo', 'bfo', 'itss'];

        DB::table('roles')
            ->whereNull('home_route')
            ->whereNotIn('slug', $excludedSlugs)
            ->update(['home_route' => 'generic.dashboard']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset home_route to null for roles that were updated
        $excludedSlugs = ['administrator', 'developer', 'pamo', 'bfo', 'itss'];

        DB::table('roles')
            ->where('home_route', 'generic.dashboard')
            ->whereNotIn('slug', $excludedSlugs)
            ->update(['home_route' => null]);
    }
};
