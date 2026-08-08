<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeDataColumnToJsonbOnNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jsonb_on_notifications', function (Blueprint $table) {
            // $table->jsonb('data')->change();

            DB::statement('
                ALTER TABLE notifications
                ALTER COLUMN data TYPE jsonb
                USING data::jsonb
            ');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jsonb_on_notifications', function (Blueprint $table) {
            // $table->text('data')->change();

            DB::statement('
                ALTER TABLE notifications
                ALTER COLUMN data TYPE text
                USING data::text
            ');
        });
    }
}
