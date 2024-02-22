<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservedTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reserved_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ticket_id')->index();
            $table->integer('event_id')->index();
            $table->integer('quantity');
            $table->dateTime('expires_at');
            $table->integer('user_id')->index();
            $table->string('session_id')->index();
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
        Schema::dropIfExists('reserved_tickets');
    }
}
