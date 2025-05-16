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
        Schema::create('pamo_assets', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->nullable();
            $table->string('property_tag_number');
            $table->string('barcode')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->foreignId('category_id')->constrained('pamo_categories');
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_value', 10, 2)->nullable();
            $table->string('status')->default('available'); // available, in-use, maintenance, disposed
            $table->foreignId('location_id')->nullable()->constrained('pamo_locations'); // Optional location
            $table->foreignId('assigned_to')->nullable()->constrained('users'); // Optional user assignment
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('property_tag_number');
            $table->index('barcode');
            $table->index('serial_number');
            $table->index('category_id');
            $table->index('status');
            $table->index('location_id');
            $table->index('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pamo_assets');
    }
};
