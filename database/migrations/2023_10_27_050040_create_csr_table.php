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
        Schema::create('csr', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tender_id');
            $table->unsignedBigInteger('pvms_id');
            $table->integer('pvms_quantity');
            $table->string('last_approval')->nullable();
            $table->string('last_approval_rank')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('last_approved_by')->nullable();
            $table->unsignedBigInteger('approved_vendor')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csr');
    }
};
