<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePledgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pledges', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('from_user')->index();
            $table->foreign('from_user')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('to_user')->index();
            $table->foreign('to_user')->references('id')->on('users')->onDelete('cascade');
            $table->double('amount',8,2);
            $table->tinyInteger('status')->comment('1-Processing | 2-Completed | 3-Declined | 4-Refund');
            $table->softDeletes();
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
        Schema::dropIfExists('pledges');
    }
}
