<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nutritionist_id');
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('meals');
            $table->text('notes')->nullable();
            $table->integer('duration_days')->default(7);
            $table->enum('status', ['draft', 'active', 'completed', 'archived'])->default('draft');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('target_calories')->nullable();
            $table->json('nutritional_targets')->nullable();
            $table->timestamps();

            $table->foreign('nutritionist_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'status']);
            $table->index(['nutritionist_id', 'status']);
            $table->index('start_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_plans');
    }
};
