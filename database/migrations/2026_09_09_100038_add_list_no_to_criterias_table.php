<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddListNoToCriteriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('criterias', function (Blueprint $table) {
            $table->unsignedBigInteger('list_no')->nullable();
        });

        DB::table('criterias')
        ->select('ass_form_cat_id')
        ->distinct()
        ->orderBy('ass_form_cat_id')
        ->pluck('ass_form_cat_id')
        ->each(function ($assFormCatId) {
            DB::table('criterias')
                ->where('ass_form_cat_id', $assFormCatId)
                ->orderBy('id')
                ->get(['id'])
                ->each(function ($criteria, $index) {
                    DB::table('criterias')
                        ->where('id', $criteria->id)
                        ->update([
                            'list_no' => $index + 1,
                        ]);
                });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('criterias', function (Blueprint $table) {
            $table->dropColumn("list_no");
        });
    }
}
