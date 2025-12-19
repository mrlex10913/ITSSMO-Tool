<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('verification_status')->default('pending')->index(); // pending, verified, rejected
            $table->string('verification_method')->nullable(); // id_card, cor, email_code, in_person
            $table->foreignId('verified_by')->nullable()->constrained('users'); // NO ACTION on delete to avoid multiple cascade paths on SQL Server
            $table->timestamp('verified_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['verification_status', 'verification_method', 'verified_by', 'verified_at']);
        });
    }
};
