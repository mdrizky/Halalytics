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
        Schema::table('street_foods', function (Blueprint $table) {
            if (!Schema::hasColumn('street_foods', 'ai_analysis_data')) {
                $table->json('ai_analysis_data')->nullable()->after('common_ingredients');
            }
            $table->string('halal_status')->default('halal_umum')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('street_foods', function (Blueprint $table) {
            $table->dropColumn('ai_analysis_data');
        });
    }
};
