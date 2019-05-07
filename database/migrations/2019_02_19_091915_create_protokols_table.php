<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProtokolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('protokols', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nomer');
            $table->string('title')->nullable();
//            $table->string('file_hash')->nullable();
//            $table->string('file_name')->nullable();
            $table->json('descriptions')->nullable();
            $table->string('type')->default('psd');
            $table->integer('year')->nullable();
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
        Schema::dropIfExists('protokols');
    }
}
