<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingAmountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_amount', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(true);
            $table->bigInteger('payment_id')->unsigned();
            // $table->foreign('payment_id')->references('id')->on('payment');
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
        Schema::dropIfExists('pending_amounts');
    }
}
