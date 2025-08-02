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
        Schema::create('purchase_type_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_type_id');
            $table->unsignedBigInteger('delivered_by')->nullable();
            $table->unsignedBigInteger('recieved_by')->nullable();
            $table->dateTime('delivered_at');
            $table->dateTime('recieved_at')->nullable();
            $table->integer('delivered_qty')->default(0);
            $table->integer('received_qty')->nullable();
            $table->integer('waste_qty')->nullable();
            $table->text('received_remarks')->nullable();
            $table->boolean('is_received')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_type_deliveries');
    }
};
