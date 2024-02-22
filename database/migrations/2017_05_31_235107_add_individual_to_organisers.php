<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndividualToOrganisers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organisers', function (Blueprint $table) {
            $table->boolean('is_individual')->default(0);
        });

        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->string('bank_account_leaf')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organisers', function (Blueprint $table) {
            $table->dropColumn('is_individual');
        });

        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropColumn('bank_account_leaf');
        });
    }
}
