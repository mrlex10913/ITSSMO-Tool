<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sla_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['incident', 'request']);
            $table->enum('priority', ['low', 'medium', 'high', 'critical']);
            $table->unsignedInteger('respond_mins')->nullable();
            $table->unsignedInteger('resolve_mins')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('sla_policies')->insert([
            ['name' => 'Incident - Low', 'type' => 'incident', 'priority' => 'low', 'respond_mins' => 240, 'resolve_mins' => 2880, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Incident - Medium', 'type' => 'incident', 'priority' => 'medium', 'respond_mins' => 120, 'resolve_mins' => 1440, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Incident - High', 'type' => 'incident', 'priority' => 'high', 'respond_mins' => 60, 'resolve_mins' => 720, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Incident - Critical', 'type' => 'incident', 'priority' => 'critical', 'respond_mins' => 30, 'resolve_mins' => 240, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Request - Low', 'type' => 'request', 'priority' => 'low', 'respond_mins' => 240, 'resolve_mins' => 2880, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Request - Medium', 'type' => 'request', 'priority' => 'medium', 'respond_mins' => 240, 'resolve_mins' => 2880, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Request - High', 'type' => 'request', 'priority' => 'high', 'respond_mins' => 120, 'resolve_mins' => 1440, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Request - Critical', 'type' => 'request', 'priority' => 'critical', 'respond_mins' => 60, 'resolve_mins' => 720, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('sla_policies');
    }
};
