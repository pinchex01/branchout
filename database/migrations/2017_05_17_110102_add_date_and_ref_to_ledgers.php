<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateAndRefToLedgers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ledgers', function (Blueprint $table) {
            $table->dateTime('txn_date')->nullable();
            $table->string('ref')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ledgers', function (Blueprint $table) {
            $table->dropColumn('ref');
            $table->dropColumn('txn_date');
        });
    }
}
