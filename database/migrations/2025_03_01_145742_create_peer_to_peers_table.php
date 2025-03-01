<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeerToPeersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peer_to_peers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessor_user_id');
            $table->unsignedBigInteger('assessee_user_id');
            $table->unsignedBigInteger('ass_form_cat_id');
            $table->unsignedBigInteger('appraisal_cycle_id');
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
        Schema::dropIfExists('peer_to_peers');
    }
}
