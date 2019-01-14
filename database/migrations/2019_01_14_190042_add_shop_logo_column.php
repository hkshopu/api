<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShopLogoColumn extends Migration
{
    const TABLE_NAME = 'shop';
    const COLUMN_NAME = 'logo_url';
    const COLUMN_NAME_AFTER = 'description_sc';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE_NAME, function($table) {
            $table->text(self::COLUMN_NAME)->nullable()->after(self::COLUMN_NAME_AFTER);
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
