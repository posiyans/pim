<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partition_id')->unsigned();
            $table->foreign('partition_id')->references('id')->on('partitions');
            $table->string('number')->nullable();
            $table->date('data_ispoln')->nullable();
            $table->date('data_perenosa')->nullable();
            $table->text('text');
            $table->integer('autor_id');
            $table->string('executor')->nullable();
            $table->integer('protokol_id')->unsigned();
            $table->foreign('protokol_id')->references('id')->on('protokols');
            $table->text('history')->nullable();
            $table->text('full_history')->nullable();
            $table->string('arxiv')->nullable();
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
        Schema::dropIfExists('tasks');
    }
}
