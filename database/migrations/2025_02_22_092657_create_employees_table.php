<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->dateTime('beginning_date');
            $table->string("employee_code")->unique();
            $table->unsignedBigInteger("branch_id");
            $table->string("employee_name");
            $table->integer('age');
            $table->unsignedBigInteger('gender_id');
            $table->string("nickname")->nullable();
            $table->unsignedBigInteger("division_id");
            $table->unsignedBigInteger("department_id");
            $table->unsignedBigInteger("sub_department_id");
            $table->unsignedBigInteger("section_id");
            $table->unsignedBigInteger("position_id");
            $table->integer('longevity_year');
            $table->integer('longevity_month');
            $table->integer('longevity_day');
            $table->string('longevity_total');
            $table->string('education_level')->nullable();
            $table->string('institution')->nullable();
            $table->string('faculty')->nullable();
            $table->string('major_graduated')->nullable();
            $table->string('position_level_id')->nullable();
            $table->string("nrc")->unique();
            $table->string("father_name");
            $table->enum("job_status",['p'])->unique();
            $table->string("phone")->unique();
            $table->string("address")->unique();
            $table->date("dob")->unique();
            $table->string('image')->nullable();
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
        Schema::dropIfExists('employees');
    }
}
