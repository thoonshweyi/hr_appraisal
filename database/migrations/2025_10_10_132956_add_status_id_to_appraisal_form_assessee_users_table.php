<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusIdToAppraisalFormAssesseeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appraisal_form_assessee_users', function (Blueprint $table) {
            $table->unsignedBigInteger("status_id")->default(2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appraisal_form_assessee_users', function (Blueprint $table) {
            $table->dropColumn("status_id");
        });
    }
}
