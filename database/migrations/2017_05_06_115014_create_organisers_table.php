<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganisersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organisers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->index();
            $table->string('slug')->unique()->nullable();
            $table->string('type')->nullable();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('avatar')->nullable();
            $table->text('about')->nullable();
            $table->string('confirmation_code')->nullable();
            $table->boolean('active')->default(1);
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
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
        Schema::dropIfExists('organisers');
    }
}
