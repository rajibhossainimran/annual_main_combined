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
        Schema::create('on_loan_item_receives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('on_loan_item_id');
            $table->unsignedBigInteger('batch_pvms_id');
            $table->integer('receieved_qty');
            $table->date('date');
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
        Schema::table('on_loan_item_receives', function (Blueprint $table) {
            //
        });
    }
};
