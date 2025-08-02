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
        Schema::create('pvms_store', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_org_id')->nullable();
            $table->unsignedBigInteger('from_sub_org_id')->nullable();
            $table->unsignedBigInteger('issue_voucher_id')->nullable();
            $table->unsignedBigInteger('workorder_receive_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('pvms_id');
            $table->unsignedBigInteger('batch_pvms_id');
            $table->integer('stock_in')->default(0);
            $table->integer('stock_out')->default(0);
            $table->boolean('is_received')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pvms_store');
    }
};
