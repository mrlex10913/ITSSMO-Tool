<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('type', ['incident', 'request'])->default('incident')->after('description');
            $table->enum('impact', ['low', 'medium', 'high'])->nullable()->after('priority');
            $table->enum('urgency', ['low', 'medium', 'high'])->nullable()->after('impact');
            $table->foreignId('sla_policy_id')->nullable()->after('urgency')->constrained('sla_policies');
            $table->timestamp('sla_due_at')->nullable()->after('sla_policy_id');
            $table->timestamp('acknowledged_at')->nullable()->after('due_at');
            $table->timestamp('responded_at')->nullable()->after('acknowledged_at');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sla_policy_id');
            $table->dropColumn(['type', 'impact', 'urgency', 'sla_due_at', 'acknowledged_at', 'responded_at']);
        });
    }
};
