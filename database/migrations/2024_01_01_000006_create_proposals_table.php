<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('cover_letter');
            $table->decimal('proposed_budget', 10, 2);
            $table->integer('delivery_time'); // in days
            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])->default('pending');
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->unique(['job_id', 'user_id']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('proposals');
    }
};
