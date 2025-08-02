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
        Schema::table('workorder_receive_pvms', function (Blueprint $table) {
            $table->unsignedBigInteger('pvms_store_id')->nullable()->after('on_loan_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workorder_receive_pvms', function (Blueprint $table) {
            //
        });
    }
};
