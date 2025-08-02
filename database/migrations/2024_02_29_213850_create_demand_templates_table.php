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
        Schema::create('demand_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_org_id');
            $table->integer('demand_type_id')->nullable();
            $table->unsignedBigInteger('demand_item_type_id')->nullable();
            $table->string('template_name');
            $table->string('document_file')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_dental_type')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demand_templates');
    }
};
