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
        Schema::table('demand_pvms', function (Blueprint $table) {
            $table->string('prev_purchase')->nullable()->after('ward');
            $table->string('present_stock')->nullable();
            $table->string('proposed_reqr')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demand_pvms', function (Blueprint $table) {
            //
        });
    }
};
