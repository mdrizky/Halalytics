<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHalalCriticalIngredientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('halal_critical_ingredients', function (Blueprint $table) {
            $table->id('id_ingredient');
            $table->string('name');
            $table->enum('status', ['halal', 'haram', 'syubhat'])->default('syubhat');
            $table->text('description')->nullable();
            $table->text('critical_reason')->nullable(); // Alasan kenapa kritis
            $table->string('common_sources')->nullable(); // Sumber umum: "Babi, Sapi, Nabati"
            $table->string('alternatives')->nullable(); // Alternatif halal
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('name');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('halal_critical_ingredients');
    }
}
