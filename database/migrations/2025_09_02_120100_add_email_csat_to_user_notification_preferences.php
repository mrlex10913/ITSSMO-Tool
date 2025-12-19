<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_notification_preferences') && ! Schema::hasColumn('user_notification_preferences', 'email_csat')) {
            Schema::table('user_notification_preferences', function (Blueprint $table): void {
                $table->boolean('email_csat')->default(true)->after('email_escalation');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_notification_preferences') && Schema::hasColumn('user_notification_preferences', 'email_csat')) {
            Schema::table('user_notification_preferences', function (Blueprint $table): void {
                $table->dropColumn('email_csat');
            });
        }
    }
};
