<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductsTableForDualSource extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Source management
            if (!Schema::hasColumn('products', 'source')) {
                $table->enum('source', [
                    'local',           // Manual input by admin
                    'open_food_facts', // Imported from OFF
                    'umkm',           // UMKM products
                    'user_ocr'        // User photo submission
                ])->default('local')->after('id_product');
            }

            // OpenFoodFacts specific fields
            if (!Schema::hasColumn('products', 'off_product_id')) {
                $table->string('off_product_id')->nullable()->unique()->after('barcode');
            }
            
            if (!Schema::hasColumn('products', 'off_last_synced')) {
                $table->timestamp('off_last_synced')->nullable();
            }

            // Import metadata
            if (!Schema::hasColumn('products', 'is_imported_from_off')) {
                $table->boolean('is_imported_from_off')->default(false);
            }
            
            if (!Schema::hasColumn('products', 'auto_imported_at')) {
                $table->timestamp('auto_imported_at')->nullable();
            }

            // Verification status
            if (!Schema::hasColumn('products', 'verification_status')) {
                $table->enum('verification_status', ['verified', 'needs_review', 'rejected'])
                      ->default('needs_review')
                      ->after('status');
            }

            // Data quality indicators
            if (!Schema::hasColumn('products', 'data_completeness_score')) {
                $table->integer('data_completeness_score')->default(0); // 0-100
            }
            
            if (!Schema::hasColumn('products', 'needs_manual_review')) {
                $table->boolean('needs_manual_review')->default(false);
            }

            // Index for performance
            $table->index('source');
            $table->index('verification_status');
            $table->index('off_product_id');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'source', 'off_product_id', 'off_last_synced',
                'is_imported_from_off', 'auto_imported_at',
                'verification_status', 'data_completeness_score', 'needs_manual_review'
            ]);
        });
    }
}
