<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNameDescriptionColumnShopTable extends Migration
{
    const TABLE_NAME = 'shop';
    const COLUMN_NAME_NAME = 'name';
    const COLUMN_NAME_DESCRIPTION = 'description';
    const COLUMN_NAME_NAME_AFTER = 'id';
    const COLUMN_NAME_DESCRIPTION_AFTER = 'name_sc';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE_NAME, function($table) {
            $table->string(self::COLUMN_NAME_NAME, 512)->nullable()->after(self::COLUMN_NAME_NAME_AFTER);
            $table->text(self::COLUMN_NAME_DESCRIPTION, 512)->nullable()->after(self::COLUMN_NAME_DESCRIPTION_AFTER);
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
            $table->dropColumn(self::COLUMN_NAME_NAME);
            $table->dropColumn(self::COLUMN_NAME_DESCRIPTION);
        });
    }
}

