<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppraisalCyclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appraisal_cycles', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->text("description");
            $table->date('start_date');
            $table->date('end_date');
            $table->date('action_start_date');
            $table->date('action_end_date');
            $table->time("action_start_time")->nullable();
            $table->time("action_end_time")->nullable();
            $table->unsignedBigInteger("status_id");
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appraisal_cycles');
    }
}
