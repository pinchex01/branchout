<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendees', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');
            $table->unsignedInteger('event_id');
            $table->foreign('event_id')
                ->references('id')
                ->on('events')
                ->onDelete('cascade');
            $table->unsignedInteger('ticket_id');
            $table->foreign('ticket_id')
                ->references('id')
                ->on('tickets')
                ->onDelete('cascade');
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->string('ref_no')->unique();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('status')->default('pending');
            $table->dateTime('check_in_time')->nullable();
            $table->boolean('is_refunded')->default(0);
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
        Schema::dropIfExists('attendees');
    }
}
