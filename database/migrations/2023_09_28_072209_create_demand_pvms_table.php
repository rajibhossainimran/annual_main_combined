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
        Schema::create('demand_pvms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demand_id');
            $table->unsignedBigInteger('p_v_m_s_id');
            $table->text('patient_name')->nullable();
            $table->string('disease')->nullable();
            $table->integer('qty');
            $table->integer('reviewd_qty')->nullable();
            // $table->decimal('avg_expense', 10, 2);
            // $table->decimal('unit_pre_stock', 10, 2);
            $table->text('remarks')->nullable();
            $table->string('purchase_type')->nullable();
            $table->string('co_note')->nullable();
            $table->string('co_selected')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demand_pvms');
    }
};
