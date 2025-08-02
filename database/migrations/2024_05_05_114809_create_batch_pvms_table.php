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
        Schema::create('batch_pvms', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no');
            $table->unsignedBigInteger('pvms_id');
            $table->unsignedBigInteger('lp_pvms_id')->nullable();
            $table->unsignedBigInteger('workorder_pvms_id')->nullable();
            $table->date('expire_date');
            $table->integer('qty');
            $table->boolean('is_afmsd_distributed')->default(0);
            $table->boolean('is_unit_distributed')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_pvms');
    }
};
