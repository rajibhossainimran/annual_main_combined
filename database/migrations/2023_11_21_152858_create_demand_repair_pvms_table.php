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
        Schema::create('demand_repair_pvms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demand_id');
            $table->unsignedBigInteger('p_v_m_s_id');
            $table->date('issue_date')->nullable();
            $table->date('installation_date')->nullable();
            $table->date('warranty_date')->nullable();
            $table->integer('authorized_machine')->nullable();
            $table->integer('existing_machine')->nullable();
            $table->integer('running_machine')->nullable();
            $table->integer('disabled_machine')->nullable();
            $table->string('supplier')->nullable();
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
        Schema::dropIfExists('demand_repair_pvms');
    }
};
