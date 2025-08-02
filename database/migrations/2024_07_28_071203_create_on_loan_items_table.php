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
        Schema::create('on_loan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('on_loan_id');
            $table->unsignedBigInteger('pvms_id');
            $table->integer('qty');
            $table->integer('receieved_qty')->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('on_loan_items');
    }
};
