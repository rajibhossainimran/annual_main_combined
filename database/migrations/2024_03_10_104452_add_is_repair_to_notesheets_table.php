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
        Schema::table('notesheets', function (Blueprint $table) {
            //
            $table->boolean('is_repair')->default(false)->after('is_dental');
            $table->boolean('is_rate_running')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notesheets', function (Blueprint $table) {
            //
        });
    }
};
