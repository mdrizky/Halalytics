<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user'); // Matches $primaryKey = 'id_user'
            $table->string('username')->unique();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('blood_type')->nullable();
            $table->text('allergy')->nullable();
            $table->text('medical_history')->nullable();
            $table->string('role')->default('user');
            $table->boolean('active')->default(true);
            $table->timestamp('last_login')->nullable();
            $table->string('image')->nullable();
            
            // Preferences & Stats
            $table->string('goal')->nullable();
            $table->string('diet_preference')->nullable();
            $table->string('activity_level')->nullable();
            $table->string('address')->nullable();
            $table->string('language')->default('id');
            $table->integer('age')->nullable();
            $table->float('height')->nullable();
            $table->float('weight')->nullable();
            $table->float('bmi')->nullable();
            $table->boolean('notif_enabled')->default(true);
            $table->boolean('dark_mode')->default(false);
            
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
