<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyCartItemQuantityColumn extends Migration
{
    const TABLE_NAME = 'cart_item';
    const COLUMN_NAME = 'quantity';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE_NAME, function ($table) {
            $table->bigInteger(self::COLUMN_NAME)->signed()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(self::TABLE_NAME, function ($table) {
            $table->bigInteger(self::COLUMN_NAME)->unsigned()->change();
        });
    }
}
