<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('owner_id');
            $table->string('owner_type');
            $table->string('name');
            $table->string('account_no');
            $table->string('currency')->default('KES');
            $table->string('type')->default('bank');
            $table->unsignedInteger('bank_id')->nullable();
            $table->foreign('bank_id')
                ->references('id')
                ->on('banks')
                ->onDelete('cascade');
            $table->boolean('is_default')->default(0);
            $table->boolean('realtime')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['owner_id','owner_type','is_default']);
            $table->unique(['owner_id','owner_type','account_no','bank_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_accounts');
    }
}
