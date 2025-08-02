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
        Schema::create('notesheet_demand_pvms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notesheet_id');
            $table->unsignedBigInteger('demand_pvms_id')->nullable();
            $table->unsignedBigInteger('demand_id');
            $table->unsignedBigInteger('pvms_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notesheet_demand_pvms');
    }
};
