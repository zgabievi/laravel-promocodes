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
        $table_name = config('promocodes.table', 'promocodes');
        $relation_table = config('promocodes.relation_table', 'promocode_user');
        $related_pivot_key = config('promocodes.related_pivot_key', 'user_id');
        $foreign_pivot_key = config('promocodes.foreign_pivot_key', 'promocode_id');

        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');

            $table->string('code', 32)->unique();
            $table->double('reward', 10, 2)->nullable();
            $table->integer('quantity')->nullable();

            $table->text('data')->nullable();

            $table->boolean('is_disposable')->default(false);
            $table->timestamp('expires_at')->nullable();
        });

        Schema::create($relation_table, function (Blueprint $table) use ($table_name, $related_pivot_key, $foreign_pivot_key) {
            $table->increments('id');

            $table->unsignedBigInteger($related_pivot_key);
            $table->unsignedBigInteger($foreign_pivot_key);

            $table->timestamp('used_at');

            $table->foreign($related_pivot_key)->references('id')->on('users')->onDelete('cascade');
            $table->foreign($foreign_pivot_key)->references('id')->on($table_name)->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(config('promocodes.relation_table', 'promocode_user'));
        Schema::drop(config('promocodes.table', 'promocodes'));
    }
}
