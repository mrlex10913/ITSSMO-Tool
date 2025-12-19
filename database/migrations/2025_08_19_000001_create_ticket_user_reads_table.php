<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_user_reads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('last_seen_comment_id')->nullable();
            $table->unsignedInteger('last_seen_comment_count')->default(0);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
            $table->unique(['ticket_id', 'user_id']);
            $table->index(['user_id', 'ticket_id']);
            // FK intentionally omitted to avoid SQL Server cascade issues
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_user_reads');
    }
};
