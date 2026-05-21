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
        // Create refresh tokens table for token rotation
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('token', 255)->unique()->index();
            $table->integer('rotated_count')->default(0);
            $table->timestamp('last_rotated_at')->nullable();
            $table->timestamp('expires_at');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'expires_at']);
        });

        // Add expires_at to personal_access_tokens if not exists
        if (Schema::hasTable('personal_access_tokens') && !Schema::hasColumn('personal_access_tokens', 'expires_at')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->timestamp('expires_at')->nullable()->after('last_used_at');
                $table->index('expires_at');
            });
        }

        // Create password reset tokens table (enhanced version)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('token', 255)->unique();
            $table->integer('attempts')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['email', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop refresh tokens table
        if (Schema::hasTable('refresh_tokens')) {
            Schema::dropIfExists('refresh_tokens');
        }

        // Remove expires_at from personal_access_tokens
        if (Schema::hasTable('personal_access_tokens') && Schema::hasColumn('personal_access_tokens', 'expires_at')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->dropIndex(['expires_at']);
                $table->dropColumn('expires_at');
            });
        }

        // Drop password reset tokens table
        if (Schema::hasTable('password_reset_tokens')) {
            Schema::dropIfExists('password_reset_tokens');
        }
    }
};
