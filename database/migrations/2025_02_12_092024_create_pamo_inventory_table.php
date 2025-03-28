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
        Schema::create('pamo_inventory', function (Blueprint $table) {
            $table->id();
            $table->string('po_number');
            $table->string('property_tag');
            $table->string('item_description');
            $table->string('brand');
            $table->string('model');
            $table->string('serial');
            $table->decimal('unit_cost', 10, 2);
            $table->string('location');
            $table->string('item_condition');
            $table->string('accountable_person');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pamo_inventory');
    }
};
