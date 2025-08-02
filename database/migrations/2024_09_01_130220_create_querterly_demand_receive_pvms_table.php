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
        Schema::create('querterly_demand_receive_pvms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('querterly_demand_receive_id');
            $table->unsignedBigInteger('querterly_demand_id');
            $table->unsignedBigInteger('querterly_demand_pvms_id');
            $table->unsignedBigInteger('batch_pvms_id');
            $table->unsignedBigInteger('pvms_store_id');
            $table->integer('issued_qty');
            $table->integer('received_qty')->default(0);
            $table->integer('wastage_qty')->default(0);
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('querterly_demand_receive_pvms');
    }
};
