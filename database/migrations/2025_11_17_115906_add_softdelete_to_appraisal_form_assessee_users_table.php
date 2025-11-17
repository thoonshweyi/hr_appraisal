<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftdeleteToAppraisalFormAssesseeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appraisal_form_assessee_users', function (Blueprint $table) {
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
        Schema::table('appraisal_form_assessee_users', function (Blueprint $table) {
            $table->dropColumn("delete_by");
            $table->dropSoftDeletes();
        });
    }
}
