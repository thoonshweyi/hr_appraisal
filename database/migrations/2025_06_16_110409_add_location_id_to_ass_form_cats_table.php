<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationIdToAssFormCatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ass_form_cats', function (Blueprint $table) {
            $table->unsignedBigInteger("location_id")->default('7');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ass_form_cats', function (Blueprint $table) {
             $table->dropColumn("location_id");
        });
    }
}
