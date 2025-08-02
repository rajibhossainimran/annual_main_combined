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
        Schema::create('afmsd_issue_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('purchase_type_id');
            $table->integer('asign_qty')->nullable();
            $table->integer('transit_qty')->nullable();
            $table->integer('total_due')->nullable();
            $table->unsignedBigInteger('afmsd_clerk')->nullable();
            $table->unsignedBigInteger('afmsd_stockControlOfficer')->nullable();
            $table->unsignedBigInteger('afmsd_groupIncharge')->nullable();
            $table->date('delivery_at');
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('afmsd_issue_approvals');
    }
};
