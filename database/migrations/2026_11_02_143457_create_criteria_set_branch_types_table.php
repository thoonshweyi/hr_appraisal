<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCriteriaSetBranchTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('criteria_set_branch_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("criteria_set_id");
            $table->unsignedBigInteger("branch_type_id");
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
        Schema::dropIfExists('criteria_set_branch_types');
    }
}
