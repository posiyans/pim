<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProtokolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('protocols', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->string('title')->nullable();
//            $table->string('file_hash')->nullable();
//            $table->string('file_name')->nullable();
            $table->json('descriptions')->nullable();
            $table->string('type')->default('psd');
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
