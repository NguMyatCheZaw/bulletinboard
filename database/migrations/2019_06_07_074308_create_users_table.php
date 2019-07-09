<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable(false);
            $table->string('email')->nullable(false)->unique();
            $table->text('password')->nullable(false);
            $table->string('profile', 255)->nullable(false);
            $table->string('type', 1)->default(1)->nullable(false);
            $table->string('phone', 20)->nullable(false);
            $table->string('address', 255)->nullable();
            $table->date('dob')->nullable();
            $table->integer('create_user_id')->unsigned()->nullable(false);
            $table->integer('updated_user_id')->unsigned()->nullable(false);
            $table->foreign('create_user_id')->references('id')->on('users');
            $table->foreign('updated_user_id')->references('id')->on('users');
            $table->integer('deleted_user_id')->nullable();
            $table->string('remember_token');
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
        Schema::dropIfExists('users');
    }
}
