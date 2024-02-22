<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_users', function (Blueprint $table) {
            $table->unsignedInteger('merchant_id');
            $table->foreign('merchant_id')->references('id')->on(config('laratrust.merchant_table'))
                ->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->integer('role_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_users');
    }
}
