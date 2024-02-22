<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesPeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table){
            $table->float('commission')->default(0);
        });

        Schema::create('sales_people', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->unsignedInteger('organiser_id');
            $table->foreign('organiser_id')->references('id')->on('organisers')->onDelete('cascade');
            $table->string('code')->index();
            $table->integer('tickets_sold')->default(0);
            $table->double('fees')->default(0);
            $table->double('total')->default(0);
            $table->boolean('settled')->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('orders', function (Blueprint $table){
            $table->integer('sales_person_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table){
            $table->dropColumn('sales_person_id');
        });

        Schema::table('events', function (Blueprint $table){
            $table->dropColumn('commission');
        });

        Schema::dropIfExists('sales_people');
    }
}
