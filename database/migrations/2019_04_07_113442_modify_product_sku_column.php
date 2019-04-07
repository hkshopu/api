<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyProductSkuColumn extends Migration
{
    const TABLE_NAME = 'product';
    const COLUMN_NAME = 'sku';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE_NAME, function ($table) {
            $table->string(self::COLUMN_NAME, 512)->nullable()->change();
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
            $table->string(self::COLUMN_NAME, 512)->unique()->change();
        });
    }
}
