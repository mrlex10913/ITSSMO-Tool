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
        Schema::create('qn_a_q_uestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qn_a_subjects_id')->constrained()->onDelete('cascade');
            $table->string('questions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qn_a_q_uestions');
    }
};
