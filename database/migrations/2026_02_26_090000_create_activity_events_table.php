<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 64);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('username')->nullable();
            $table->string('entity_ref')->nullable();
            $table->string('summary', 255)->nullable();
            $table->string('status', 32)->default('success');
            $table->json('payload_json')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id_user')->on('users')->nullOnDelete();
            $table->index(['event_type', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_events');
    }
};
