<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("slug");
            $table->unsignedBigInteger("division_id");
            $table->unsignedBigInteger("department_id");
            $table->unsignedBigInteger("sub_department_id");
            $table->unsignedBigInteger("section_id");
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
        Schema::dropIfExists('positions');
    }
}
