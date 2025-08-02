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
        Schema::create('workorder_pvms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workorder_id');
            $table->unsignedBigInteger('csr_id')->nullable();
            $table->unsignedBigInteger('pvms_id');
            $table->integer('qty');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('delivery_mood');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workorder_csrs');
    }
};
