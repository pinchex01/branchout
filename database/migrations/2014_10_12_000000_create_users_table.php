<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('other_name')->nullable();
            $table->string('full_name')->nullable();
            $table->string('id_number')->nullable()->default(null)->unique();
            $table->date('dob')->nullable();
            $table->string('phone')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->enum('gender',['Female','Male']);
            $table->string('password')->nullable();
            $table->string('avatar')->nullable();
            $table->string('citizenship')->nullable();
            $table->string('confirmation_code')->nullable();
            $table->boolean('phone_confirmed')->default(0);
            $table->boolean('email_confirmed')->default(0);
            $table->string('api_token')->nullable();
            $table->boolean('confirmed')->default(0);
            $table->boolean('active')->default(1);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
