<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePromocodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('promocodes.table', 'promocodes'), function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned()->nullable();

            $table->string('code', 32)->unique();
            $table->double('reward', 10, 2)->nullable();

            $table->json('data')->nullable();

            $table->boolean('is_used')->default(false);

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(config('promocodes.table', 'promocodes'));
    }
}
