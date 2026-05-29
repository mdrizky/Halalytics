<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFamilyIdToMedicineReminders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medicine_reminders', function (Blueprint $table) {
            $table->unsignedBigInteger('id_family_profile')->nullable()->after('id_user');
            $table->foreign('id_family_profile')->references('id')->on('family_profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medicine_reminders', function (Blueprint $table) {
            $table->dropForeign(['id_family_profile']);
            $table->dropColumn('id_family_profile');
        });
    }
}
