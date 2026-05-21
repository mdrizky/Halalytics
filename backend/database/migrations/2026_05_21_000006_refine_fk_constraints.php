<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ai_logs', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('ai_feedbacks', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('ai_log_id')->references('id')->on('ai_logs')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ai_feedbacks', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['ai_log_id']);
        });

        Schema::table('ai_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
