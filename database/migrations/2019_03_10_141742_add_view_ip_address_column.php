<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddViewIpAddressColumn extends Migration
{
    const TABLE_NAME = 'view';
    const COLUMN_NAME = 'ip_address';
    const COLUMN_NAME_AFTER = 'entity_id';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE_NAME, function($table) {
            $table->string(self::COLUMN_NAME, 512)->nullable()->after(self::COLUMN_NAME_AFTER);
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
