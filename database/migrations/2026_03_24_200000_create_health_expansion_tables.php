<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Medical Profiles
        if (!Schema::hasTable('medical_profiles')) {
            Schema::create('medical_profiles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_user');
                $table->decimal('weight_kg', 5, 1)->nullable();
                $table->decimal('height_cm', 5, 1)->nullable();
                $table->json('drug_allergies')->nullable();
                $table->text('chronic_diseases')->nullable();
                $table->boolean('has_gerd')->nullable();
                $table->string('blood_type', 5)->nullable();
                $table->text('additional_notes')->nullable();
                $table->timestamps();

                $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
                $table->unique('id_user');
            });
        }

        // 2. Mental Health Quiz Results
        if (!Schema::hasTable('mental_health_quiz_results')) {
            Schema::create('mental_health_quiz_results', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_user');
                $table->enum('quiz_type', ['gad7', 'phq9', 'dass21']);
                $table->integer('total_score');
                $table->string('severity_level');
                $table->json('answers')->nullable();
                $table->text('ai_recommendation')->nullable();
                $table->timestamps();

                $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
                $table->index(['id_user', 'quiz_type']);
            });
        }

        // 3. Help Center Requests
        if (!Schema::hasTable('help_requests')) {
            Schema::create('help_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_user')->nullable();
                $table->string('category');
                $table->string('subject');
                $table->text('message');
                $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
                $table->text('admin_reply')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();

                $table->foreign('id_user')->references('id_user')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('help_requests');
        Schema::dropIfExists('mental_health_quiz_results');
        Schema::dropIfExists('medical_profiles');
    }
};
