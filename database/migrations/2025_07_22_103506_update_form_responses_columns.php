<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_responses', function (Blueprint $table) {
            // Remove old JSON column (if it exists)
            if (Schema::hasColumn('form_responses', 'response_data')) {
                $table->dropColumn('response_data');
            }

            $table->string('name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('email')->nullable();
            $table->text('short_answer')->nullable();
            $table->longText('paragraph')->nullable();
            $table->json('multiple_choice')->nullable();
            $table->string('single_choice')->nullable();
            $table->json('location')->nullable();
            $table->string('file_upload')->nullable();
            $table->integer('age')->nullable();
            $table->string('choice')->nullable();
            $table->text('text_field')->nullable();
            $table->integer('rating')->nullable();
            $table->date('date_answer')->nullable();
            $table->json('ranking')->nullable();
            $table->json('likert')->nullable();
            $table->integer('nps')->nullable();
            $table->string('section')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('form_responses', function (Blueprint $table) {
            $table->dropColumn([
                'name', 'mobile_number', 'email', 'short_answer', 'paragraph',
                'multiple_choice', 'single_choice', 'location', 'file_upload',
                'age', 'choice', 'text_field', 'rating', 'date_answer',
                'ranking', 'likert', 'nps', 'section'
            ]);

            $table->json('response_data')->nullable(); // rollback if needed
        });
    }
};
