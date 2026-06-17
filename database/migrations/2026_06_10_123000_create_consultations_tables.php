<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id('id_consultation');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('expert_id')->nullable();
            $table->string('status')->default('pending');
            $table->string('subject');
            $table->text('description')->nullable();
            $table->string('consultation_type')->default('chat');
            $table->date('preferred_date')->nullable();
            $table->time('preferred_time')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('expert_id')->references('id_user')->on('users')->onDelete('set null');
            $table->foreign('cancelled_by')->references('id_user')->on('users')->onDelete('set null');
            $table->index(['user_id', 'status']);
            $table->index(['expert_id', 'status']);
        });

        Schema::create('consultation_messages', function (Blueprint $table) {
            $table->id('id_message');
            $table->unsignedBigInteger('consultation_id');
            $table->unsignedBigInteger('sender_id');
            $table->text('message');
            $table->string('message_type')->default('text');
            $table->string('attachment_path')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('consultation_id')->references('id_consultation')->on('consultations')->onDelete('cascade');
            $table->foreign('sender_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->index(['consultation_id', 'created_at']);
        });

        Schema::create('consultation_sessions', function (Blueprint $table) {
            $table->id('id_session');
            $table->unsignedBigInteger('consultation_id');
            $table->unsignedBigInteger('expert_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('scheduled_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->string('status')->default('scheduled');
            $table->string('meeting_link')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->foreign('consultation_id')->references('id_consultation')->on('consultations')->onDelete('cascade');
            $table->foreign('expert_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('cancelled_by')->references('id_user')->on('users')->onDelete('set null');
            $table->index(['expert_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['scheduled_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_sessions');
        Schema::dropIfExists('consultation_messages');
        Schema::dropIfExists('consultations');
    }
};