<?php

use App\Models\RatingScale;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCriteriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('criterias', function (Blueprint $table) {
            $table->id();
            $table->text("name");
            $table->unsignedBigInteger("status_id");
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('ass_form_cat_id');
            $ratingscales = RatingScale::orderBy('id','asc')->paginate(10);
            foreach($ratingscales as $ratingscale){
                $table->bigInteger(Str::snake($ratingscale['name']));
            }


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
        Schema::dropIfExists('criterias');
    }
}
