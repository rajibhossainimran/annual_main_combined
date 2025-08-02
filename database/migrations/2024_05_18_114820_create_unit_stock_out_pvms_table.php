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
        Schema::create('unit_stock_out_pvms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_stock_out_id');
            $table->unsignedBigInteger('pvms_id');
            $table->unsignedBigInteger('btach_pvms_id');
            $table->unsignedBigInteger('store_id');
            $table->integer('qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_stock_out');
    }
};
