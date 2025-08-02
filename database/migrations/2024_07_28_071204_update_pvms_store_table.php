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
        Schema::table('pvms_store', function (Blueprint $table) {
            $table->boolean('is_on_loan')->default(false)->after('is_received');
            $table->unsignedBigInteger('on_loan_item_id')->nullable()->after('is_on_loan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pvms_store', function (Blueprint $table) {
            //
        });
    }
};
