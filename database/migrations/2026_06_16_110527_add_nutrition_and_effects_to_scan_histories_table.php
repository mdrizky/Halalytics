<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scan_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('scan_histories', 'calories')) {
                $table->decimal('calories', 10, 2)->nullable()->after('health_score');
                $table->decimal('protein', 10, 2)->nullable()->after('calories');
                $table->decimal('carbs', 10, 2)->nullable()->after('protein');
                $table->decimal('fat', 10, 2)->nullable()->after('carbs');
                $table->decimal('fiber', 10, 2)->nullable()->after('fat');
                $table->decimal('sugar', 10, 2)->nullable()->after('fiber');
                $table->decimal('sodium', 10, 2)->nullable()->after('sugar');
                $table->string('nova_group')->nullable()->after('sodium');
                $table->string('nutri_score')->nullable()->after('nova_group');
                $table->text('ai_recommendation')->nullable()->after('nutri_score');
                $table->json('short_term_effects')->nullable()->after('ai_recommendation');
                $table->json('long_term_effects')->nullable()->after('short_term_effects');
                $table->date('scan_date')->nullable()->after('long_term_effects');
            }
        });
    }

    public function down(): void
    {
        Schema::table('scan_histories', function (Blueprint $table) {
            $columns = [
                'calories', 'protein', 'carbs', 'fat', 'fiber', 'sugar', 'sodium',
                'nova_group', 'nutri_score', 'ai_recommendation',
                'short_term_effects', 'long_term_effects', 'scan_date'
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('scan_histories', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
