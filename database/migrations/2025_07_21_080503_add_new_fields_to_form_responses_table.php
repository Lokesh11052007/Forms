<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('form_responses', function (Blueprint $table) {
            $table->tinyInteger('rating')->nullable()->after('birth_date');  // 1–5 star rating
            $table->integer('nps')->nullable()->after('rating');             // Net Promoter Score (0–10)
            $table->json('likert')->nullable()->after('nps');                // Likert answers store as JSON
            $table->string('section_title')->nullable()->after('likert');    // Section label/title
            $table->text('submission_notes')->nullable()->after('section_title'); // Extra comments
        });
    }

    public function down(): void
    {
        Schema::table('form_responses', function (Blueprint $table) {
            $table->dropColumn([
                'rating',
                'nps',
                'likert',
                'section_title',
                'submission_notes'
            ]);
        });
    }
};
