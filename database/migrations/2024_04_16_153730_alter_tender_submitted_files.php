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
        Schema::table('tender_submitted_files', function (Blueprint $table) {
            //
            $table->boolean('is_valid')->nullable();
            $table->unsignedBigInteger('file_checked_by')->nullable();
            $table->dateTime('file_checked_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tender_submitted_files', function (Blueprint $table) {
            //
        });
    }
};
