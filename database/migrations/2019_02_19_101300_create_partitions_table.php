<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partitions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('protokol_id')->unsigned();
            $table->foreign('protokol_id')->references('id')->on('protokols');
            $table->integer('number');
            $table->text('text');
            $table->string('speaker')->nullable();
//            $table->string('file_hash')->nullable();
//            $table->string('file_name')->nullable();
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
        Schema::dropIfExists('partitions');
    }
}
