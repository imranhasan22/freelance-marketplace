<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('freelancer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->decimal('hourly_rate', 8, 2)->default(0);
            $table->json('skills')->nullable();
            $table->json('portfolio')->nullable();
            $table->enum('availability', ['available', 'busy', 'unavailable'])->default('available');
            $table->enum('experience_level', ['beginner', 'intermediate', 'expert'])->default('beginner');
            $table->json('languages')->nullable();
            $table->json('education')->nullable();
            $table->json('certifications')->nullable();
            $table->timestamps();

            $table->index(['availability', 'experience_level']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('freelancer_profiles');
    }
};
