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
        Schema::table('demand_repair_pvms', function (Blueprint $table) {
            //
            $table->integer('approved_qty')->default(0)->after('disabled_machine');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demand_repair_pvms', function (Blueprint $table) {
            //
        });
    }
};
