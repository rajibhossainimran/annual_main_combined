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
        Schema::table('vendor_biddings', function (Blueprint $table) {
            //
            $table->string('brand_name')->nullable()->after('details');
            $table->string('pack_size')->nullable();
            $table->string('mfg')->nullable();
            $table->string('origin')->nullable();
            $table->string('dar_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_biddings', function (Blueprint $table) {
            //
        });
    }
};
