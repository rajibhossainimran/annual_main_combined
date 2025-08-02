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
        Schema::create('notesheet_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notesheet_id');
            $table->unsignedBigInteger('approved_by');
            $table->tinyInteger('step_number');
            $table->string('role_name');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notesheet_approvals');
    }
};
