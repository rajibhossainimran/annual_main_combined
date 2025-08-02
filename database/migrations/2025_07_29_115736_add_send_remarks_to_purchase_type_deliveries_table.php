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
    Schema::table('purchase_type_deliveries', function (Blueprint $table) {
        $table->text('send_remarks')->nullable()->after('received_remarks');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('purchase_type_deliveries', function (Blueprint $table) {
        $table->dropColumn('send_remarks');
    });
}
};
