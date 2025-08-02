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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('org_id')->nullable();
            $table->unsignedBigInteger('sub_org_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('org_id')->references('id')->on('organizations');
            $table->foreign('sub_org_id')->references('id')->on('sub_organizations');
            $table->foreign('branch_id')->references('id')->on('branches');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
