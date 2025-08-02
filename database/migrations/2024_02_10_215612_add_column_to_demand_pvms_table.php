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
            $table->string('authorized_machine')->nullable()->after('disease');
            $table->string('existing_machine')->nullable()->after('authorized_machine');
            $table->string('running_machine')->nullable()->after('existing_machine');
            $table->string('disabled_machine')->nullable()->after('running_machine');
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
