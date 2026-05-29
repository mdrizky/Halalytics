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
        Schema::table('health_encyclopedias', function (Blueprint $table) {
            $table->text('causes')->nullable()->after('content');
            $table->text('symptoms')->nullable()->after('causes');
            $table->text('treatments')->nullable()->after('symptoms');
            $table->text('halal_notes')->nullable()->after('treatments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_encyclopedias', function (Blueprint $table) {
            $table->dropColumn(['causes', 'symptoms', 'treatments', 'halal_notes']);
        });
    }
};
