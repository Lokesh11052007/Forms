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
        Schema::create('form_responses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('form_id')->constrained()->onDelete('cascade');

    $table->string('name')->nullable();
    $table->string('mobile_number')->nullable();
    $table->string('email')->nullable();
    $table->string('short_answer')->nullable();
    $table->text('paragraph')->nullable();
    $table->json('multiple_choice')->nullable();
    $table->string('single_choice')->nullable();
    $table->json('location')->nullable();
    $table->string('file_upload')->nullable();
    $table->string('age')->nullable();
    $table->date('birth_date')->nullable();

    $table->json('response_data')->nullable(); // for JSON-based form builder

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_responses');
    }
};
