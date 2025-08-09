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
    Schema::table('purchase', function (Blueprint $table) {
        $table->boolean('afmsd_approval')->default(false)->after('send_to'); // Replace 'your_existing_column' if needed
    });
}

public function down()
{
    Schema::table('purchase', function (Blueprint $table) {
        $table->dropColumn('afmsd_approval');
    });
}
};
