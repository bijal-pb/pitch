<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->index();
            $table->string('name')->nullable();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->integer('age')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('type')->comment('1- pledge user | 2 - business user')->nullable();
            $table->boolean('is_verified')->comment('1- verified | 2 - not verified ')->default(1);
            $table->boolean('is_notification')->comment('1 - enable | 2 - disable')->default(1);
            $table->boolean('status')->comment('1 - active | 2 - deactive')->default(1);
            $table->string('stripe_cust_id')->nullable();
            $table->text('profile_photo')->nullable();
            $table->string('startup_name')->nullable();
            $table->text('startup_location')->nullable();
            $table->text('bio')->nullable();
            $table->string('device_type')->nullable();
            $table->text('device_token')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
