<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('questions');                               // Stores array of field definitions
            $table->json('header')->nullable();                      // Branding, logo, company, etc.
            $table->json('footer')->nullable();                      // Footer/contact UI pieces
            $table->timestamp('expires_at')->nullable();             // When this form should expire (auto-close)
            $table->boolean('is_active')->default(true);             // Soft enabled/disabled
            // Optional for SEO-friendly sharing (add to model if you want)
            // $table->string('slug')->unique()->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
