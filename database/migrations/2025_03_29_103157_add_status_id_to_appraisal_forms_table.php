<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusIdToAppraisalFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appraisal_forms', function (Blueprint $table) {
            $table->unsignedBigInteger("status_id")->default(21);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appraisal_forms', function (Blueprint $table) {
            $table->unsignedBigInteger("status_id")->default(21);
        });
    }
}
