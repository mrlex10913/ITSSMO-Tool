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
        Schema::create('master_file_access_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('file_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('action', ['view', 'download', 'print', 'share']);
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->string('department')->nullable();
            $table->timestamps();

            $table->foreign('file_id')->references('id')->on('master_files');
            $table->foreign('user_id')->references('id')->on('users');

            $table->index(['file_id', 'created_at']);
            $table->index(['user_id', 'department', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_file_access_logs');
    }
};
