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
        Schema::create('pamo_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('minor');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            // Index for faster queries
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pamo_categories');
    }
};
