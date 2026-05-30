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
        // Use raw SQL to handle the transition safely
        // 1. Convert to string first to bypass enum validation
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE products MODIFY COLUMN source VARCHAR(255) DEFAULT 'local'");
        
        // 2. Clean up any invalid data that might be causing truncation (if any)
        // Just in case there are values not in our intended final list
        \Illuminate\Support\Facades\DB::table('products')
            ->whereNotIn('source', ['local', 'open_food_facts', 'umkm', 'user_ocr', 'open_beauty_facts', 'openfda'])
            ->update(['source' => 'local']);

        // 3. Convert back to the expanded enum
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE products MODIFY COLUMN source ENUM('local', 'open_food_facts', 'umkm', 'user_ocr', 'open_beauty_facts', 'openfda') DEFAULT 'local'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE products MODIFY COLUMN source ENUM('local', 'open_food_facts', 'umkm', 'user_ocr') DEFAULT 'local'");
        });
    }
};
