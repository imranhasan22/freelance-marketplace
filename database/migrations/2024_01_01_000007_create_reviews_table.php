<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // Foreign keys for users (reviewer and reviewee)
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewee_id')->constrained('users')->onDelete('cascade');
            
            // Foreign keys for job, service, and order (make sure these tables exist)
            $table->foreignId('job_id')->nullable()->constrained('jobs')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
            
            // Rating and comment
            $table->tinyInteger('rating')->unsigned(); // 1-5 stars
            $table->text('comment')->nullable();
            
            // Review type (job or service)
            $table->enum('type', ['job', 'service']);
            
            $table->timestamps();

            // Indexes for performance optimization
            $table->index(['reviewee_id', 'rating']);
            $table->index(['type', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};
