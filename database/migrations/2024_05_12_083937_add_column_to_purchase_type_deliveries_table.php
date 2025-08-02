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
        Schema::table('purchase_type_deliveries', function (Blueprint $table) {
            $table->unsignedBigInteger('pvms_store_id')->after('delivered_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_type_deliveries', function (Blueprint $table) {
            //
        });
    }
};
