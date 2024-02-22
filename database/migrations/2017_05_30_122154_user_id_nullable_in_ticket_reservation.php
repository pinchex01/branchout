<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserIdNullableInTicketReservation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reserved_tickets', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('reserved_tickets', function (Blueprint $table) {
            $table->integer('user_id')->nullable()->default(null);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->integer('user_id')->nullable()->default(null);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('user_id')->index()->nullable()->default(null);
            $table->string('first_name')->nullable()->default(null);
            $table->string('last_name')->nullable()->default(null);
            $table->string('email')->index()->nullable()->default(null);
            $table->string('phone')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reserved_tickets', function (Blueprint $table) {
            //
        });
    }
}
