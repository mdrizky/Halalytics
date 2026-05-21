<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('prompt_type', 100)->nullable();
            $table->longText('input_data')->nullable();
            $table->longText('ai_response')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->boolean('is_accurate')->nullable();
            $table->text('feedback_text')->nullable();
            $table->timestamps();
            $table->index(['prompt_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_logs');
    }
};
