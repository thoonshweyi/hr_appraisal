<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssessmentReportIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appraisal_forms', function (Blueprint $table) {
            $table->index(
                ['appraisal_cycle_id', 'deleted_at'],
                'idx_appraisal_forms_cycle_deleted'
            );
        });

        Schema::table('appraisal_form_assessee_users', function (Blueprint $table) {
            $table->index(
                ['assessee_user_id', 'deleted_at', 'appraisal_form_id'],
                'idx_form_assessees_user_deleted_form'
            );
        });

        Schema::table('form_results', function (Blueprint $table) {
            $table->unique(
                ['appraisal_form_id', 'assessee_user_id', 'criteria_id'],
                'uq_form_results_form_user_criteria'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_results', function (Blueprint $table) {
            $table->dropUnique('uq_form_results_form_user_criteria');
        });

        Schema::table('appraisal_form_assessee_users', function (Blueprint $table) {
            $table->dropIndex('idx_form_assessees_user_deleted_form');
        });

        Schema::table('appraisal_forms', function (Blueprint $table) {
            $table->dropIndex('idx_appraisal_forms_cycle_deleted');
        });
    }
}
