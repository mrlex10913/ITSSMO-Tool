<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('ticket_categories')->insert([
            ['name' => 'Hardware', 'description' => 'Devices, peripherals, and hardware-related issues', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Software', 'description' => 'Applications, OS, and software-related issues', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Network', 'description' => 'Connectivity, internet, VPN, and network issues', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Account Access', 'description' => 'Login, password, and account access issues', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Other', 'description' => 'General inquiries and uncategorized', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_categories');
    }
};
