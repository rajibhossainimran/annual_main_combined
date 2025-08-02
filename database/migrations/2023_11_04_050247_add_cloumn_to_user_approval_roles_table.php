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
        Schema::table('user_approval_roles', function (Blueprint $table) {
            $table->string('can_change_action')->default('[]')->after('pvms_category_permission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_approval_roles', function (Blueprint $table) {
            //
        });
    }
};
