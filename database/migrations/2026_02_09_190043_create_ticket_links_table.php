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
        Schema::create('ticket_links', function (Blueprint $table) {
            $table->id();
            // SQL Server doesn't allow multiple cascade paths, use noActionOnDelete for linked_ticket_id
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('linked_ticket_id')->constrained('tickets')->noActionOnDelete();
            $table->string('link_type', 20); // related, parent, child, duplicate, blocks, blocked_by
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Prevent duplicate links between same tickets
            $table->unique(['ticket_id', 'linked_ticket_id', 'link_type']);
            $table->index('ticket_id');
            $table->index('linked_ticket_id');
            $table->index('link_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_links');
    }
};
