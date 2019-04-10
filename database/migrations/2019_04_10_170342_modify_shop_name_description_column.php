
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyShopNameDescriptionColumn extends Migration
{
    const TABLE_NAME = 'shop';
    const COLUMN_NAME_NAME_EN = 'name_en';
    const COLUMN_NAME_DESCRIPTION_EN = 'description_en';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE_NAME, function ($table) {
            $table->string(self::COLUMN_NAME_NAME_EN, 512)->nullable()->change();
            $table->text(self::COLUMN_NAME_DESCRIPTION_EN)->nullable()->change();
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
            $table->string(self::COLUMN_NAME_NAME_EN, 512)->change();
            $table->text(self::COLUMN_NAME_DESCRIPTION_EN)->change();
        });
    }
}
