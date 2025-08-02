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
        Schema::table('workorder_receives', function (Blueprint $table) {
            $table->date('receiving_date')->nullable();
            $table->string('received_by')->nullable();
            $table->string('crv_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workorder_receives', function (Blueprint $table) {
            //
        });
    }
};
