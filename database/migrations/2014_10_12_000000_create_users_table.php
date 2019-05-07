<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('login')->unique();
            $table->string('name');
            $table->string('full_name');
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->integer('moderator')->nullable();
            $table->dateTime('last_connect')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('login_by_sms')->default(true);
            $table->integer('sms')->nullable();
            $table->string('color')->nullable();
            $table->integer('hide')->nullable();
            $table->integer('telegram')->nullable();
            $table->json('aliases')->nullable();
            $table->string('avatar')->nullable();
            $table->rememberToken();
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
