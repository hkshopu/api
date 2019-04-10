<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTitleContentColumnBlogTable extends Migration
{
    const TABLE_NAME = 'blog';
    const COLUMN_NAME_TITLE = 'title';
    const COLUMN_NAME_CONTENT = 'content';
    const COLUMN_NAME_TITLE_AFTER = 'id';
    const COLUMN_NAME_CONTENT_AFTER = 'title_sc';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE_NAME, function($table) {
            $table->string(self::COLUMN_NAME_TITLE, 512)->nullable()->after(self::COLUMN_NAME_TITLE_AFTER);
            $table->text(self::COLUMN_NAME_CONTENT, 512)->nullable()->after(self::COLUMN_NAME_CONTENT_AFTER);
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
            $table->dropColumn(self::COLUMN_NAME_TITLE);
            $table->dropColumn(self::COLUMN_NAME_CONTENT);
        });
    }
}
