<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModifyUserIdToAppraisalFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appraisal_forms', function (Blueprint $table) {
            $table->unsignedBigInteger("modify_user_id")->default(1);
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
            $table->dropColumn("modify_user_id")->default(1);
        });
    }
}
