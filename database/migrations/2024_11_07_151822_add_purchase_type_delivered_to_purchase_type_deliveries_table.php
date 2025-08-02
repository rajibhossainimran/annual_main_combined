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
            $table->boolean('purchase_type_delivered')->default(false)->after('is_received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_type_deliveries', function (Blueprint $table) {
            $table->dropColumn('purchase_type_delivered');
        });
    }
};
