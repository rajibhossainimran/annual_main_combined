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
        Schema::table('demand_approvals', function (Blueprint $table) {
            $table->boolean('need_reapproval')->default(false)->after('note');
            $table->enum('action', ['APPROVE', 'BACK'])->after('need_reapproval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demand_approvals', function (Blueprint $table) {
            //
        });
    }
};
