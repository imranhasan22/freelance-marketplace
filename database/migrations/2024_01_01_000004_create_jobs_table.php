<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->enum('budget_type', ['hourly', 'fixed']);
            $table->decimal('budget_min', 10, 2)->nullable();
            $table->decimal('budget_max', 10, 2)->nullable();
            $table->decimal('fixed_budget', 10, 2)->nullable();
            $table->datetime('deadline')->nullable();
            $table->json('skills_required')->nullable();
            $table->enum('experience_level', ['beginner', 'intermediate', 'expert']);
            $table->enum('status', ['open', 'in_progress', 'completed', 'cancelled'])->default('open');
            $table->boolean('featured')->default(false);
            $table->timestamps();

            $table->index(['status', 'featured', 'created_at']);
            $table->index(['category_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs');
    }
};
