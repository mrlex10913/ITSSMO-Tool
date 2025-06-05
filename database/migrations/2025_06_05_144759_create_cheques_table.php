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
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->string('cheque_number')->unique();
            $table->string('payee_name');
            $table->decimal('amount', 15, 2);
            $table->string('amount_in_words');
            $table->date('cheque_date');
            $table->enum('status', ['draft', 'printed', 'issued', 'cleared', 'cancelled'])->default('draft');
            $table->timestamp('printed_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->json('field_positions')->nullable(); // Store field positions
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheques');
    }
};
