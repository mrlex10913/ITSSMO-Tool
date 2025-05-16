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
        Schema::create('asset_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('pamo_assets');
            $table->foreignId('from_location_id')->nullable()->constrained('pamo_locations');
            $table->foreignId('to_location_id')->nullable()->constrained('pamo_locations');
            $table->foreignId('assigned_by')->constrained('users');
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->date('movement_date');
            $table->text('notes')->nullable();
            $table->string('movement_type');
            $table->timestamps();

            $table->index('asset_id');
            $table->index('from_location_id');
            $table->index('to_location_id');
            $table->index('movement_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_movements');
    }
};
