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
        Schema::create('csr_demands', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('csr_id');
            $table->unsignedBigInteger('notesheet_id');
            $table->unsignedBigInteger('notesheet_demand_pvms_id');
            $table->unsignedBigInteger('demand_id');
            $table->unsignedBigInteger('demand_pvms_id');
            $table->unsignedBigInteger('pvms_id');
            $table->integer('qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csr_demands');
    }
};
