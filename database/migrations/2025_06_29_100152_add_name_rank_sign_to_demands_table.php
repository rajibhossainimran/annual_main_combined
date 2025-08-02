<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameRankSignToDemandsTable extends Migration
{
    public function up()
    {
        Schema::table('demands', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('rank')->nullable();
            $table->string('sign')->nullable();
        });
    }

    public function down()
    {
        Schema::table('demands', function (Blueprint $table) {
            $table->dropColumn(['name', 'rank', 'sign']);
        });
    }
};


