<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertLanguageEntity extends Migration
{
    const TABLE_NAME = 'entity';
    const ROW_VALUE = 'language';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table(self::TABLE_NAME)->insert([
            [
                'name' => self::ROW_VALUE,
                'created_by' => 13,
                'updated_by' => 13,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table(self::TABLE_NAME)->delete([
            [
                'name' => self::ROW_VALUE,
            ],
        ]);
    }
}

