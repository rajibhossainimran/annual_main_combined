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
        Schema::create('demand_template_pvms_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demand_template_id');
            $table->unsignedBigInteger('p_v_m_s_id');
            $table->text('patient_name')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->string('disease')->nullable();
            $table->integer('qty')->nullable();
            $table->text('remarks')->nullable();
            $table->string('ward')->nullable();
            $table->string('authorized_machine')->nullable();
            $table->string('existing_machine')->nullable();
            $table->string('running_machine')->nullable();
            $table->string('disabled_machine')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('installation_date')->nullable();
            $table->date('warranty_date')->nullable();
            $table->string('supplier')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demand_template_pvms_lists');
    }
};
