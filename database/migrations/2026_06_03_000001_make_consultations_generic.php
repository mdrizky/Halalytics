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
        Schema::table('nutrition_consultations', function (Blueprint $table) {
            if (!Schema::hasColumn('nutrition_consultations', 'type')) {
                $table->string('type', 32)->default('expert')->after('id'); // expert, admin, support
            }
            if (!Schema::hasColumn('nutrition_consultations', 'admin_id')) {
                $table->unsignedBigInteger('admin_id')->nullable()->after('nutritionist_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nutrition_consultations', function (Blueprint $table) {
            $table->dropColumn(['type', 'admin_id']);
        });
    }
};
