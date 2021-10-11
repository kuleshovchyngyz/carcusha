<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeingkeysOtherToTablesUserStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Lead::where('id','>',0)->update(['status_id'=>$s[DB::raw('status_id')]]);
        Schema::table('user_statuses', function (Blueprint $table) {
            $table->bigInteger('status_id')->unsigned()->change();
            $table->foreign('status_id')->references('id')->on('statuses')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tables_user_statuses', function (Blueprint $table) {
            //
        });
    }
}
