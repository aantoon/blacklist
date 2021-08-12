<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignToBlackList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('black_lists', function(Blueprint $table) {
            $table->foreign('advertiser_id')->references('id')->on('advertisers');
            $table->foreign('publisher_id')->references('id')->on('publishers');
            $table->foreign('site_id')->references('id')->on('sites');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('black_lists', function (Blueprint $table) {
            $table->dropForeign(['advertiser_id']);
            $table->dropForeign(['publisher_id']);
            $table->dropForeign(['site_id']);
        });
    }
}
