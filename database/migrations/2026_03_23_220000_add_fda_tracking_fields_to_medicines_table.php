<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            if (!Schema::hasColumn('medicines', 'active_ingredient')) {
                $table->text('active_ingredient')->nullable()->after('ingredients');
            }

            if (!Schema::hasColumn('medicines', 'warnings')) {
                $table->text('warnings')->nullable()->after('side_effects');
            }

            if (!Schema::hasColumn('medicines', 'is_imported_from_fda')) {
                $table->boolean('is_imported_from_fda')->default(false)->after('source');
            }

            if (!Schema::hasColumn('medicines', 'external_reference')) {
                $table->string('external_reference')->nullable()->after('is_imported_from_fda');
            }

            if (!Schema::hasColumn('medicines', 'external_payload')) {
                $table->json('external_payload')->nullable()->after('external_reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $dropColumns = [];

            foreach (['active_ingredient', 'warnings', 'is_imported_from_fda', 'external_reference', 'external_payload'] as $column) {
                if (Schema::hasColumn('medicines', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
