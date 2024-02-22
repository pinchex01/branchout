<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('application_no')->unique()->nullable();
            $table->integer('applicable_id');
            $table->string('applicable_type');
            $table->string('name');
            $table->string('type');
            $table->longText('payload');
            $table->string('status')->index();
            $table->string('notes');
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->unsignedInteger('assigned_id')->nullable();
            $table->foreign('assigned_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->integer('organiser_id')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('application_id');
            $table->foreign('application_id')
                ->references('id')
                ->on('applications')
                ->onDelete('cascade');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->string('stage');
            $table->text('notes')->nullable()->default(null);
            $table->dateTime('completed_at')->nullable();
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
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('applications');
    }
}
