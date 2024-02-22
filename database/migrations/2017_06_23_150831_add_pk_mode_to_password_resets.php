<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPkModeToPasswordResets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('password_resets', function (Blueprint $table) {
            $table->string('pk');
            $table->string('mode')->nullable()->default(null);
            $table->string('username');
            $table->timestamp('updated_at');

            $table->dropColumn('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('password_resets', function (Blueprint $table) {
            $table->dropColumn('mode');
            $table->dropColumn('pk');
            $table->dropColumn('username');
            $table->dropColumn('updated_at');

            $table->string('email');
        });
    }
}
