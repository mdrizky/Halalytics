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
        Schema::table('products', function (Blueprint $table) {
            $table->string('brand')->nullable()->after('nama_product');
            $table->string('quantity')->nullable()->after('brand');
            $table->text('packaging')->nullable()->after('quantity');
            $table->text('labels')->nullable()->after('packaging');
            $table->string('nutriscore_grade', 10)->nullable()->after('labels');
            $table->integer('nova_group')->nullable()->after('nutriscore_grade');
            $table->text('stores')->nullable()->after('nova_group');
            $table->text('countries')->nullable()->after('stores');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'brand', 'quantity', 'packaging', 'labels', 
                'nutriscore_grade', 'nova_group', 'stores', 'countries'
            ]);
        });
    }
};
