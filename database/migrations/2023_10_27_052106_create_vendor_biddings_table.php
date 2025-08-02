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
        Schema::create('vendor_biddings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('csr_id');
            $table->unsignedBigInteger('vendor_id');
            $table->longText('details')->nullable();
            $table->decimal('offered_unit_price', 10, 2);
            $table->boolean('is_vendor_approved')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_biddings');
    }
};
