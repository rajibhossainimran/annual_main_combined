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
        Schema::create('annual_demand_pvms_unit_demands', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('annual_demand_pvms_id');
            $table->unsignedInteger('annual_demand_unit_id');
            $table->unsignedInteger('department_id');
            $table->integer('estimated_qty')->nullable();
            $table->integer('afmsd_qty')->nullable();
            $table->integer('dg_qty')->nullable();
            $table->text('unit_remarks')->nullable();
            $table->text('afmsd_remarks')->nullable();
            $table->text('dg_remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_demand_pvms_unit_demands');
    }
};
