<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderIdColumnCartItemTable extends Migration
{
    const TABLE_NAME = 'cart_item';
    const COLUMN_NAME = 'order_id';
    const COLUMN_NAME_AFTER = 'quantity';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE_NAME, function($table) {
            $table->bigInteger(self::COLUMN_NAME)->nullable()->unsigned()->after(self::COLUMN_NAME_AFTER);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(self::TABLE_NAME, function($table) {
            $table->dropColumn(self::COLUMN_NAME);
        });
    }
}
