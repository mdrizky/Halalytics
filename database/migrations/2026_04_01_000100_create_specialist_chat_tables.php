<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('specialists')) {
            Schema::create('specialists', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('name');
                $table->string('specialty');
                $table->string('avatar_url')->nullable();
                $table->text('bio')->nullable();
                $table->boolean('is_online')->default(false);
                $table->boolean('is_available')->default(true);
                $table->decimal('rating', 3, 2)->default(5.00);
                $table->unsignedInteger('total_consultations')->default(0);
                $table->timestamps();

                $table->foreign('user_id')->references('id_user')->on('users')->nullOnDelete();
                $table->index(['is_available', 'is_online']);
            });
        }

        if (!Schema::hasTable('consultation_sessions')) {
            Schema::create('consultation_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('specialist_id');
                $table->enum('status', ['waiting', 'active', 'ended', 'cancelled'])->default('waiting');
                $table->string('topic', 200)->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
                $table->foreign('specialist_id')->references('id')->on('specialists')->cascadeOnDelete();
                $table->index(['user_id', 'status']);
                $table->index(['specialist_id', 'status']);
            });
        }

        if (!Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('session_id');
                $table->unsignedBigInteger('sender_id');
                $table->enum('sender_type', ['user', 'specialist']);
                $table->text('message')->nullable();
                $table->string('message_type', 20)->default('text');
                $table->string('file_url')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamps();

                $table->foreign('session_id')->references('id')->on('consultation_sessions')->cascadeOnDelete();
                $table->foreign('sender_id')->references('id_user')->on('users')->cascadeOnDelete();
                $table->index(['session_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('consultation_sessions');
        Schema::dropIfExists('specialists');
    }
};
