<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameLeadIdInFantomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fantoms', function (Blueprint $table) {
            $table->renameColumn('lead_id', 'bitrix_lead_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fantoms', function (Blueprint $table) {
            $table->renameColumn('bitrix_lead_id', 'lead_id');
        });
    }
}
