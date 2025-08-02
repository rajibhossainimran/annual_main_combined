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
        Schema::create('csr_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('csr_id');
            $table->longText('remarks')->nullable();
            $table->unsignedBigInteger('selected_biddder_id')->nullable();
            $table->unsignedBigInteger('approved_by');
            $table->tinyInteger('step_number');
            $table->string('role_name');
            $table->string('last_approval_rank')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csr_approvals');
    }
};
