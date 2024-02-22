<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettlementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settlements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->string('account_type');
            $table->string('conversation_id')->nullable();
            $table->double('amount')->default(0);
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->dateTime('date_settled')->nullable();
            $table->string('bank')->nullable();
            $table->string('account_no')->nullable();
            $table->string('account_name')->nullable();
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
        Schema::dropIfExists('settlements');
    }
}
