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
        Schema::create('annual_demand_pvms', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('annual_demand_depatment_id');
            $table->unsignedInteger('pvms_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_demand_pvms');
    }
};
