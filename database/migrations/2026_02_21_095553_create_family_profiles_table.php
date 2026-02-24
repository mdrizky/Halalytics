<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('family_profiles', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->unsignedBigInteger('user_id');
            $blueprint->string('name');
            $blueprint->string('relationship')->nullable(); // e.g., anak, pasangan, orang_tua
            $blueprint->integer('age')->nullable();
            $blueprint->enum('gender', ['male', 'female', 'other'])->nullable();
            $blueprint->text('allergies')->nullable();
            $blueprint->text('medical_history')->nullable();
            $blueprint->string('image_path')->nullable();
            $blueprint->timestamps();

            $blueprint->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_profiles');
    }
};
