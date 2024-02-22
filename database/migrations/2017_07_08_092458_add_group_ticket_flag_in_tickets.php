<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupTicketFlagInTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->boolean('is_group_ticket')->default(0);
        });

        Schema::table('reserved_tickets', function (Blueprint $table) {
            $table->integer('groups_of')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('is_group_ticket');
        });

        Schema::table('reserved_tickets', function (Blueprint $table) {
            $table->dropColumn('groups_of');
        });
    }
}
