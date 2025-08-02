<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up()
    {
        Schema::table('p_v_m_s', function (Blueprint $table) {
            $table->string('item_source')->nullable()->after('remarks');
            // Replace 'column_name' with the name of the column you want to place it after
        });
    }

    public function down()
    {
        Schema::table('p_v_m_s', function (Blueprint $table) {
            $table->dropColumn('item_source');
        });
    }
};
