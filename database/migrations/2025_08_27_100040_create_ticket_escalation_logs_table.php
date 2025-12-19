<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_escalation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('sla_escalation_id')->constrained('sla_escalations')->cascadeOnDelete();
            $table->timestamp('notified_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_escalation_logs');
    }
};
