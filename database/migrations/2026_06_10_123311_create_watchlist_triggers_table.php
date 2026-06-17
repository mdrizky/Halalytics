<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('watchlist_triggers')) {
            Schema::create('watchlist_triggers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('ingredient_name');
                $table->string('product_name')->nullable();
                $table->timestamp('triggered_at');
                $table->timestamps();

                $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
                $table->index(['user_id', 'triggered_at']);
                $table->index(['ingredient_name', 'triggered_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('watchlist_triggers');
    }
};
