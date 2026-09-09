<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddSlugToAssFormCatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ass_form_cats', function (Blueprint $table) {
            $table->string("slug")->nullable();
        });

        DB::table('ass_form_cats')
        ->select('id', 'name')
        ->orderBy('id')
        ->chunkById(100, function ($rows) {
            foreach ($rows as $row) {
                DB::table('ass_form_cats')
                    ->where('id', $row->id)
                    ->update([
                        'slug' => Str::slug($row->name),
                    ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ass_form_cats', function (Blueprint $table) {
            $table->dropColumn("slug")->nullable();
        });
    }
}
