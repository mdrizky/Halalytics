<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daily_intakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id_user')->onDelete('cascade');
            $table->date('intake_date');
            $table->integer('total_water_ml')->default(0);
            $table->integer('total_caffeine_mg')->default(0);
            $table->integer('total_sugar_g')->default(0);
            $table->integer('total_calories')->default(0);
            $table->timestamps();
            
            // One record per user per day
            $table->unique(['user_id', 'intake_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_intakes');
    }
};
