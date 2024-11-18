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
        Schema::create('asset_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_categories_id')->constrained()->onDelete('cascade');
            $table->string('item_barcode');
            $table->string('item_name');
            $table->string('item_model');
            $table->string('item_serial_itss');
            $table->string('item_serial_purch');
            $table->string('assigned_to');
            $table->string('location');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_lists');
    }
};
