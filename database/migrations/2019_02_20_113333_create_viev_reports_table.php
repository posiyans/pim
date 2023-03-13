<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVievReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('view_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->unsigned();
            $table->foreign('task_id')->references('id')->on('tasks');
            $table->foreignId('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('executor')->nullable();
            $table->dateTime('done')->nullable();
            $table->integer('show')->default(0);
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
        Schema::dropIfExists('view_reports');
    }
}
