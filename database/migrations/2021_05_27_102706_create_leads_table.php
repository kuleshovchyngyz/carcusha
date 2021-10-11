<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable();
            $table->string('vendor');
            $table->string('vendor_model');
            $table->string('vendor_year');
            $table->integer('is_active')->nullable();
            $table->string('phonenumber');
            $table->string('folder');
            $table->integer('user_id');
            $table->integer('bitrix_user_id')->nullable();
            $table->integer('status_id');
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
        Schema::dropIfExists('leads');
    }
}
