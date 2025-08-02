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
            $table->unsignedBigInteger('workorder_receive_id')->nullable()->after('workorder_pvms_id');
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
