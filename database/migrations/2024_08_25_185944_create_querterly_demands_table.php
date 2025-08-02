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
        Schema::create('querterly_demands', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('annual_demand_id');
            $table->unsignedBigInteger('sub_org_id');
            $table->string('demand_no');
            $table->string('demand_type');
            $table->string('financial_year');
            $table->string('last_approval')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->dateTime('demand_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('querterly_demands');
    }
};
