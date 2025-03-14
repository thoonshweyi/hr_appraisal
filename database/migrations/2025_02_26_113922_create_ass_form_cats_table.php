<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssFormCatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ass_form_cats', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->unsignedBigInteger("status_id");
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('attach_form_type_id');
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
        Schema::dropIfExists('ass_form_cats');
    }
}
