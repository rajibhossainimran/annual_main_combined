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
        Schema::table('p_v_m_s', function (Blueprint $table) {
            $table->integer('category_id')->nullable()->after('item_sections_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('p_v_m_s', function (Blueprint $table) {
            //
        });
    }
};
