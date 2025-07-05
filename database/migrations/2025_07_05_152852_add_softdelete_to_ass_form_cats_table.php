<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftdeleteToAssFormCatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ass_form_cats', function (Blueprint $table) {
            $table->integer('delete_by')->nullable();
            $table->softDeletes();
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
            $table->dropColumn("delete_by");
            $table->dropSoftDeletes();
        });
    }
}
