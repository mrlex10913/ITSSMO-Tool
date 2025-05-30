<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
        $table->string('name');
        $table->string('slug')->unique();
        $table->string('description')->nullable();
        $table->boolean('is_default')->default(false);
        $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
        $table->foreignId('role_id')->nullable()->constrained();
    });

        // Seed default roles
        $roles = [
            ['name' => 'Administrator', 'slug' => 'administrator', 'description' => 'Full system access', 'is_default' => false],
            ['name' => 'PAMO', 'slug' => 'pamo', 'description' => 'Property and Asset Management Officer', 'is_default' => false],
            ['name' => 'BFO', 'slug' => 'bfo', 'description' => 'Budget and Finance Officer', 'is_default' => false],
            ['name' => 'User', 'slug' => 'user', 'description' => 'Regular system user', 'is_default' => true],
        ];
        foreach ($roles as $role) {
        DB::table('roles')->insert($role);
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
