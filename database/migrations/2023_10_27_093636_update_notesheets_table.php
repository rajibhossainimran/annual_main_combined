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
            $table->bigInteger('notesheet_budget')->after('notesheet_id');
            $table->longText('notesheet_details');
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
