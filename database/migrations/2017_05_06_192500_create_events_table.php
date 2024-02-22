<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('venue')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->string('location');
            $table->float('lat')->nullable();
            $table->float('lng')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->dateTime('on_sale_date')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedInteger('organiser_id');
            $table->foreign('organiser_id')
                ->references('id')
                ->on('organisers')
                ->onDelete('cascade');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->boolean('is_live')->default();
            $table->string('status')->default('draft');
            $table->string('avatar')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
