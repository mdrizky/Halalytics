<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nutrition_consultations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('nutritionist_id')->nullable();
            $table->string('status', 32)->default('open');
            $table->string('subject')->nullable();
            $table->timestamps();

            $table->index(['nutritionist_id', 'status']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('nutrition_consultation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained('nutrition_consultations')->cascadeOnDelete();
            $table->string('sender_role', 24);
            $table->unsignedBigInteger('sender_user_id')->nullable();
            $table->text('body');
            $table->json('metadata')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['consultation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutrition_consultation_messages');
        Schema::dropIfExists('nutrition_consultations');
    }
};
