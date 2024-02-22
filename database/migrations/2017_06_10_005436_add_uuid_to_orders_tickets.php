<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUuidToOrdersTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('pk')->nullable()->index();
        });

        Schema::table('attendees', function (Blueprint $table) {
            $table->string('pk')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('pk');
        });

        Schema::table('attendees', function (Blueprint $table) {
            $table->dropColumn('pk');
        });
    }
}
