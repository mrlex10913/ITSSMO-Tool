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
        Schema::table('borrowed_item', function (Blueprint $table) {
            $table->string('return_remarks')->nullable()->after('remarks');
            $table->dateTime('date_of_return_remarks')->nullable()->after('return_remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowed_item', function (Blueprint $table) {
            $table->dropColumn(['return_remarks', 'date_of_return_remarks']);
        });
    }
};
