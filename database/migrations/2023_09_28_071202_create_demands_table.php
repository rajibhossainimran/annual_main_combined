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
        Schema::create('demands', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->unsignedBigInteger('sub_org_id');
            // $table->integer('control_type_id');
            // $table->integer('financial_years_id');
            $table->integer('demand_type_id');
            // $table->date('demand_date');
            // $table->string('signal_no')->nullable();
            // $table->string('pradhikar_no')->nullable();
            // $table->string('indent_no')->nullable();
            $table->string('status')->default('pending');
            $table->integer('total_pvmc_selected_for_notesheet')->nullable();
            $table->string('last_approved_role')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demands');
    }
};
