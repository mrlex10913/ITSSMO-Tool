<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_attachments', function (Blueprint $table) {
            $table->unsignedBigInteger('ticket_comment_id')->nullable()->after('ticket_id');
        });

        Schema::table('ticket_attachments', function (Blueprint $table) {
            $table->foreign('ticket_comment_id')->references('id')->on('ticket_comments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_attachments', function (Blueprint $table) {
            try {
                $table->dropForeign(['ticket_comment_id']);
            } catch (\Throwable $e) {
            }
        });

        Schema::table('ticket_attachments', function (Blueprint $table) {
            $table->dropColumn('ticket_comment_id');
        });
    }
};
