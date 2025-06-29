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
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewee_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('job_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('rating')->unsigned(); // 1-5 stars
            $table->text('comment')->nullable();
            $table->enum('type', ['job', 'service']);
            $table->timestamps();

            $table->index(['reviewee_id', 'rating']);
            $table->index(['type', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};
