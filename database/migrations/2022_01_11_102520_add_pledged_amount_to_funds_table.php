<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPledgedAmountToFundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('funds', function (Blueprint $table) {
            $table->double('pledge_amount',8,2)->default(0)->after('rise');
            $table->tinyInteger('is_transfer')->default('1')->comment('1- not transfer | 2 -transfer')->after('pledge_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('funds', function (Blueprint $table) {
            $table->dropColumn('pledge_amount');
            $table->dropColumn('is_transfer');
        });
    }
}
