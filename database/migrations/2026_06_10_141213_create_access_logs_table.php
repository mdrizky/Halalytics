<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('resource_type');
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->string('action');
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->string('result')->default('success');
            $table->text('details')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('set null');
            $table->index(['user_id', 'created_at']);
            $table->index(['resource_type', 'resource_id']);
            $table->index(['action', 'result']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
};
