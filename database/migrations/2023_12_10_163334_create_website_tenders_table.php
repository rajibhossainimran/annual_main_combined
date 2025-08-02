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
        Schema::create('website_tenders', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('details')->nullable();
            $table->text('tender_no')->nullable();
            $table->string('pdf_file');
            $table->date('last_submission_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_tenders');
    }
};
