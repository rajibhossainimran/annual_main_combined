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
        Schema::create('p_v_m_s', function (Blueprint $table) {
            $table->id();
            $table->string('pvms_id')->unique();
            $table->string('pvms_name');
            $table->string('pvms_old_name')->nullable();
            $table->string('nomenclature');
            $table->unsignedBigInteger('account_units_id');
            $table->unsignedBigInteger('specifications_id');
            $table->unsignedBigInteger('item_groups_id');
            $table->unsignedBigInteger('item_sections_id');
            $table->unsignedBigInteger('item_types_id');
            $table->unsignedBigInteger('control_types_id')->nullable();
            $table->unsignedBigInteger('item_departments_id')->nullable();
            $table->string('page_no')->nullable();
            $table->string('remarks')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('p_v_m_s', function (Blueprint $table) {
            $table->foreign('account_units_id')->references('id')->on('account_units');
            $table->foreign('specifications_id')->references('id')->on('specifications');
            $table->foreign('item_groups_id')->references('id')->on('item_groups');
            $table->foreign('item_sections_id')->references('id')->on('item_sections');
            $table->foreign('item_types_id')->references('id')->on('item_types');
            $table->foreign('control_types_id')->references('id')->on('control_types');
            $table->foreign('item_departments_id')->references('id')->on('item_departments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_v_m_s');
    }
};
