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
        Schema::create('workorders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedInteger('financial_year_id');
            $table->string('order_no');
            $table->decimal('total_amount', 10, 2);
            $table->string('contract_number');
            $table->date('contract_date');
            $table->date('last_submit_date');
            $table->boolean('is_delivered')->default(0);
            $table->boolean('is_dadgms_approved')->default(0);
            $table->boolean('is_adgms_approved')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workorders');
    }
};
