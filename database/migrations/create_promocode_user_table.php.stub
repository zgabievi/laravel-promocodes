<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreatePromocodeUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $models = config('promocodes.models');

        Schema::create($models['pivot']['table_name'], function (Blueprint $table) use ($models) {
            $table->id();
            $table->foreignId($models['promocodes']['foreign_id'])->constrained()->cascadeOnDelete();
            $table->foreignId($models['users']['foreign_id'])->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 40)->nullable();
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
        $models = config('promocodes.models');

        Schema::drop($models['pivot']['table_name']);
    }
}
