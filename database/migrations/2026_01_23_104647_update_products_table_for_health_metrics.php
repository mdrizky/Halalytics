<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Health Metrics
            if (!Schema::hasColumn('products', 'caffeine_mg')) {
                $table->integer('caffeine_mg')->default(0);
            }
            if (!Schema::hasColumn('products', 'sugar_g')) {
                $table->integer('sugar_g')->default(0);
            }
            if (!Schema::hasColumn('products', 'volume_ml')) {
                $table->integer('volume_ml')->nullable();
            }
            if (!Schema::hasColumn('products', 'calories')) {
                $table->integer('calories')->default(0);
            }
            if (!Schema::hasColumn('products', 'protein_g')) {
                $table->decimal('protein_g', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'fat_g')) {
                $table->decimal('fat_g', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'halal_certificate')) {
                 $table->string('halal_certificate')->nullable();
            }

            // Approval Workflow
            if (!Schema::hasColumn('products', 'approval_status')) {
                $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            }
            if (!Schema::hasColumn('products', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users', 'id_user');
            }
            if (!Schema::hasColumn('products', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
            if (!Schema::hasColumn('products', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'caffeine_mg', 'sugar_g', 'volume_ml', 'calories', 'protein_g', 'fat_g',
                'halal_certificate', 'approval_status', 'approved_by', 'approved_at', 'rejection_reason'
            ]);
        });
    }
};
