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
        Schema::create('issue_order_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('purchase_type_id');
            $table->string('approval_status')->default('Pending');
            $table->unsignedBigInteger('approved_by');
            $table->timestamp('approved_at');
            $table->unsignedBigInteger('send_to');
            $table->integer('step_number');
            $table->text('note')->nullable();
            $table->string('action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_order_approvals');
    }
};
