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
        Schema::create('rate_running_pvms', function (Blueprint $table) {
            $table->id();
            $table->integer('supplier_id');
            $table->integer('tender_ser_no')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('pvms_id');
            $table->double('price');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_running_pvms');
    }
};
