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
        Schema::table('notesheet_demand_pvms', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('demand_repair_pvms_id')->nullable()->after('demand_pvms_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notesheet_demand_pvms', function (Blueprint $table) {
            //
        });
    }
};
