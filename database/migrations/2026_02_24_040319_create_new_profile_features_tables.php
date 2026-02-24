<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. User Achievements
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('badge_name');
            $table->string('badge_icon_url')->nullable();
            $table->timestamp('unlocked_at')->useCurrent();
            $table->boolean('is_notified')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
        });

        // 2. Monthly Reports (PDFs)
        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('month'); // e.g., "2026-02"
            $table->string('file_url')->nullable();
            $table->json('scan_stats')->nullable(); // Pre-calculated stats
            $table->text('ai_summary')->nullable(); // Generated paragraph
            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
        });

        // 3. Watchlist Triggers
        Schema::create('watchlist_triggers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('ingredient_name');
            $table->string('product_name')->nullable();
            $table->timestamp('triggered_at')->useCurrent();
            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
        });

        // 4. Update scan_histories
        if (Schema::hasTable('scan_histories')) {
            Schema::table('scan_histories', function (Blueprint $table) {
                if (!Schema::hasColumn('scan_histories', 'profile_name')) {
                    $table->string('profile_name')->nullable()->after('user_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('scan_histories')) {
            Schema::table('scan_histories', function (Blueprint $table) {
                if (Schema::hasColumn('scan_histories', 'profile_name')) {
                    $table->dropColumn('profile_name');
                }
            });
        }
        
        Schema::dropIfExists('watchlist_triggers');
        Schema::dropIfExists('monthly_reports');
        Schema::dropIfExists('user_achievements');
    }
};
