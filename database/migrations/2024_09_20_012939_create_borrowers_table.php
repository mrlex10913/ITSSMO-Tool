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
        Schema::create('borrowers', function (Blueprint $table) {
            $table->id();
            $table->string('id_number')->nullable();
            $table->string('doc_tracker');
            $table->string('name')->nullable();
            $table->string('contact')->nullable();
            $table->string('department')->nullable();
            $table->string('authorizedby')->nullable();
            $table->string('date_to_borrow')->nullable();
            $table->string('date_to_return')->nullable();
            $table->string('location')->nullable();
            $table->string('event')->nullable();
            $table->string('receivedby')->nullable();
            $table->string('returnby')->nullable();
            $table->string('status')->nullable();
            $table->string('released_checkedby')->nullable();
            $table->string('notedby')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowers');
    }
};
