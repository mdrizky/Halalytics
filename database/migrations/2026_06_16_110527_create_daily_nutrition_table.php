<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_nutrition', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id_user')->onDelete('cascade');
            $table->date('scan_date');
            $table->decimal('total_calories', 10, 2)->default(0);
            $table->decimal('total_protein', 10, 2)->default(0);
            $table->decimal('total_carbs', 10, 2)->default(0);
            $table->decimal('total_fat', 10, 2)->default(0);
            $table->decimal('total_fiber', 10, 2)->default(0);
            $table->decimal('total_sugar', 10, 2)->default(0);
            $table->decimal('total_sodium', 10, 2)->default(0);
            $table->integer('total_scans')->default(0);
            $table->integer('total_halal')->default(0);
            $table->integer('total_syubhat')->default(0);
            $table->integer('total_haram')->default(0);
            $table->decimal('health_score_avg', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'scan_date']);
            $table->index('user_id');
            $table->index('scan_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_nutrition');
    }
};
