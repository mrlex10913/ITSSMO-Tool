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
        Schema::create('ticket_time_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('duration_mins'); // Time spent in minutes
            $table->text('description')->nullable(); // What was done
            $table->date('work_date'); // Date the work was performed
            $table->boolean('is_billable')->default(false);
            $table->timestamps();

            $table->index('ticket_id');
            $table->index('user_id');
            $table->index('work_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_time_entries');
    }
};
