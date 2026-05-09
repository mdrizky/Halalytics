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
            if (!Schema::hasColumn('products', 'manufacture_date')) {
                $table->date('manufacture_date')->nullable();
            }
            if (!Schema::hasColumn('products', 'expiry_date')) {
                $table->date('expiry_date')->nullable();
            }
        });

        Schema::table('ingredients', function (Blueprint $table) {
            if (!Schema::hasColumn('ingredients', 'category')) {
                $table->string('category')->nullable();
            }
            if (!Schema::hasColumn('ingredients', 'image')) {
                $table->string('image')->nullable();
            }
            if (!Schema::hasColumn('ingredients', 'origin')) {
                $table->string('origin')->nullable();
            }
            if (!Schema::hasColumn('ingredients', 'common_uses')) {
                $table->text('common_uses')->nullable();
            }
            if (!Schema::hasColumn('ingredients', 'nutritional_info')) {
                $table->text('nutritional_info')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['manufacture_date', 'expiry_date']);
        });

        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn(['category', 'image', 'origin', 'common_uses', 'nutritional_info']);
        });
    }
};
