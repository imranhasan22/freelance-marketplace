<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->integer('delivery_time'); // in days
            $table->integer('revisions')->default(0);
            $table->json('features')->nullable();
            $table->json('images')->nullable();
            $table->enum('status', ['active', 'paused', 'draft'])->default('draft');
            $table->boolean('featured')->default(false);
            $table->timestamps();

            $table->index(['status', 'featured', 'created_at']);
            $table->index(['category_id', 'status']);
            $table->index(['price', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
};
