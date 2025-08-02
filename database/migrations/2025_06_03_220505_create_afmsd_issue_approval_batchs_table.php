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
        Schema::create('afmsd_issue_approval_batchs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('afmsd_issue_approval_id');
            $table->unsignedBigInteger('batchPvms_id'); 
            $table->integer('qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('afmsd_issue_approval_batchs');
    }
};
