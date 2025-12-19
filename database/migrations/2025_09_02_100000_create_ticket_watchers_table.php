<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_watchers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('user_id');
            // Role of the watcher relative to the ticket: requester, assignee, watcher
            $table->string('role', 20)->default('watcher');
            // Per-watcher toggles
            $table->boolean('notify_comment')->default(true);
            $table->boolean('notify_status')->default(true);
            $table->boolean('notify_assignment')->default(true);
            $table->boolean('notify_escalation')->default(true);
            $table->timestamps();

            $table->unique(['ticket_id', 'user_id']);
            $table->index(['ticket_id']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_watchers');
    }
};
