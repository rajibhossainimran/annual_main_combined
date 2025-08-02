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
        Schema::create('purchase_types', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_id');
            $table->integer('demand_pvms_id');
            $table->integer('sub_org_id');
            $table->integer('demand_id');
            $table->integer('pvms_id');
            $table->integer('request_qty');
            $table->integer('received_qty')->nullable();
            $table->string('purchase_type');
            $table->string('status')->default('pending');
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
        Schema::dropIfExists('purchase_types');
    }
};
