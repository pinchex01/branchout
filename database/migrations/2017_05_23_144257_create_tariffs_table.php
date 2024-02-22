<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTariffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tariffs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->double('t_floor')->default(0);
            $table->double('t_ceiling')->default(0);
            $table->double('amount')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['t_floor','t_ceiling','amount']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tariffs');
    }
}
