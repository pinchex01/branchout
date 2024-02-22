<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->double('price');
            $table->integer('min_per_person')->default(1);
            $table->integer('max_per_person')->nullable();
            $table->integer('quantity_available')->nullable();
            $table->integer('quantity_sold')->default(0);
            $table->dateTime('on_sale_date')->nullable();
            $table->dateTime('end_sale_date')->nullable();
            $table->double('sales_volume')->default(0);
            $table->unsignedInteger('event_id');
            $table->foreign('event_id')
                ->references('id')
                ->on('events')
                ->onDelete('cascade');
            $table->string('status')->default('on-sale');
            $table->string('public_id')->unique();
            $table->timestamps();

            $table->unique(['name','event_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
