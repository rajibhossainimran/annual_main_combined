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
        Schema::create('sub_org_stock', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_org_id');
            $table->unsignedBigInteger('pvms_id');
            $table->bigInteger('qty')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('sub_org_stock', function (Blueprint $table) {
            $table->foreign('sub_org_id')->references('id')->on('sub_organizations');
            $table->foreign('pvms_id')->references('id')->on('p_v_m_s');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_org_stock');
    }
};
