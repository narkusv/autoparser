<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserParseUrlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_parse_url', function (Blueprint $table) {
          $table->integer('user_id')->unsigned()->nullable();
          $table->foreign('user_id')->references('id')
           ->on('users')->onDelete('cascade');

         $table->integer('parse_url_id')->unsigned()->nullable();
         $table->foreign('parse_url_id')->references('id')
          ->on('parse_urls')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_parse_url');
    }
}
