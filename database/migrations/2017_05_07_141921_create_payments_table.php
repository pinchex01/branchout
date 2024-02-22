<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('payment_key')->unique();
            $table->integer('order_id')->nullable();
            $table->string('payment_ref')->unique()->nullable();
            $table->string('currency')->default('KES');
            $table->double('fee')->default(0);
            $table->double('amount')->default(0);
            $table->double('total')->default(0);
            $table->string('status')->default('unpaid');
            $table->string('channel')->default('MPESA');
            $table->dateTime('date_paid')->nullable();
            $table->text('notes')->nullable();
            $table->text('payload')->nullable();
            $table->integer('user_id');
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
        Schema::dropIfExists('payments');
    }
}
