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
        Schema::create('querterly_demand_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('demand_id');
            $table->unsignedBigInteger('approved_by');
            $table->tinyInteger('step_number');
            $table->string('role_name');
            $table->text('note');
            $table->boolean('need_reapproval')->default(false);
            $table->enum('action', ['APPROVE', 'BACK']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('querterly_demand_approvals');
    }
};
