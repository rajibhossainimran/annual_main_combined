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
        Schema::create('querterly_demand_pvms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('querterly_demand_id');
            $table->unsignedBigInteger('annual_demand_pvms_unit_demand_id');
            $table->unsignedBigInteger('pvms_id');
            $table->integer('req_qty');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('querterly_demand_pvms');
    }
};
