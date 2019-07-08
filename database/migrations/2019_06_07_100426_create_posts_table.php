<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255)->unique()->nullable(false);
            $table->string('description')->nullable(false);
            $table->integer('status')->default(1)->nullable(false);
            $table->integer('create_user_id')->unsigned()->nullable(false);
            $table->integer('updated_user_id')->unsigned()->nullable(false);
            $table->foreign('create_user_id')->references('id')->on('users');
            $table->foreign('updated_user_id')->references('id')->on('users');
            $table->integer('deleted_user_id')->nullable();
            $table->dateTime('created_at')->nullable(false);
            $table->dateTime('updated_at')->nullable(false);
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
