<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_conflicts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('resource_type');
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->json('mobile_data');
            $table->json('server_data');
            $table->dateTime('mobile_timestamp');
            $table->dateTime('server_timestamp');
            $table->string('resolution_strategy')->default('last-write-wins');
            $table->json('resolution_result')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'created_at']);
            $table->index(['resource_type', 'resolved_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_conflicts');
    }
};
