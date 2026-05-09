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
        Schema::dropIfExists('user_points');
        Schema::dropIfExists('community_user_points');
        Schema::dropIfExists('daily_missions');
        Schema::dropIfExists('user_missions');
        
        if (Schema::hasColumn('users', 'onboarding_points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('onboarding_points');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse for this decommissioning
    }
};
