<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Language;

class AddLanguageIdColumnUserTable extends Migration
{
    const TABLE_NAME = 'user';
    const COLUMN_NAME = 'language_id';
    const COLUMN_NAME_AFTER = 'activation_key';
    const ROW_VALUE_DEFAULT = 'en'; // English

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE_NAME, function($table) {
            $language = Language::where('code', self::ROW_VALUE_DEFAULT)->whereNull('deleted_at')->first();
            $table->bigInteger(self::COLUMN_NAME)->unsigned()->default($language->id)->after(self::COLUMN_NAME_AFTER);
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
