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
        Schema::create('annual_demands', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('financial_year_id');
            $table->boolean('is_list_approved')->default(false);
            $table->unsignedInteger('last_list_approved_role')->nullable();
            $table->unsignedInteger('last_unit_approved_role')->nullable();
            $table->boolean('is_unit_approved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_demands');
    }
};
