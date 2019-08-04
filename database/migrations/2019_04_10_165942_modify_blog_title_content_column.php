<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyBlogTitleContentColumn extends Migration
{
    const TABLE_NAME = 'blog';
    const COLUMN_NAME_TITLE_EN = 'title_en';
    const COLUMN_NAME_CONTENT_EN = 'content_en';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE_NAME, function ($table) {
            $table->string(self::COLUMN_NAME_TITLE_EN, 512)->nullable()->change();
            $table->text(self::COLUMN_NAME_CONTENT_EN)->nullable()->change();
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
            $table->string(self::COLUMN_NAME_TITLE_EN, 512)->change();
            $table->text(self::COLUMN_NAME_CONTENT_EN)->change();
        });
    }
}

