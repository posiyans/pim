<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            //$table->foreign('user_id')->references('id')->on('users');
            $table->integer('commentable_id')->nullable();
            $table->string('commentable_type')->nullable();
            $table->string('type')->nullable();
            $table->string('action')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('description')->nullable();
            $table->json('value')->nullable();
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
        Schema::dropIfExists('logs');
    }
}
