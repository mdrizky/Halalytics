<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id_user')->onDelete('cascade');
            $table->string('fcm_token', 500);
            $table->string('device_type'); // 'android', 'ios'
            $table->string('device_id')->nullable();
            $table->timestamp('last_used_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['user_id', 'fcm_token']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_fcm_tokens');
    }
};
