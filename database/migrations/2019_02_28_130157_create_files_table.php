<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('commentable_id')->nullable();
            $table->string('commentable_type')->nullable();
            $table->string('hash')->nullable();
            $table->text('name')->nullable();
            $table->integer('size')->nullable();
            $table->text('discription')->nullable();
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
        Schema::dropIfExists('files');
    }
}
