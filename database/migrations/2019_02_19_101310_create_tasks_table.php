<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->foreignId('partition_id')->unsigned();
            $table->foreign('partition_id')->references('id')->on('partitions');
            $table->string('number')->nullable();
            $table->date('data_ispoln')->nullable();
            $table->date('data_perenosa')->nullable();
            $table->text('text');
            $table->integer('autor_id');
            $table->string('executor')->nullable();
            $table->foreignId('protocol_id')->unsigned();
            $table->foreign('protocol_id')->references('id')->on('protocols');
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
