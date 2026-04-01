<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bpom_data', function (Blueprint $table) {
            if (!Schema::hasColumn('bpom_data', 'is_verified_manually')) {
                $table->boolean('is_verified_manually')->default(false)->after('verification_status');
            }

            if (!Schema::hasColumn('bpom_data', 'last_synced_at')) {
                $table->timestamp('last_synced_at')->nullable()->after('verified_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bpom_data', function (Blueprint $table) {
            $dropColumns = [];

            foreach (['is_verified_manually', 'last_synced_at'] as $column) {
                if (Schema::hasColumn('bpom_data', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
