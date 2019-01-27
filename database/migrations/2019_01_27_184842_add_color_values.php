<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColorValues extends Migration
{
    const TABLE_NAME = 'color';
    const COLOR_LIST = [
        ['name' => 'INDIANRED', 'code' => '#CD5C5C', 'created_by' => 13, 'updated_by' => 13],
        ['name' => 'LIGHTCORAL', 'code' => '#F08080', 'created_by' => 13, 'updated_by' => 13],
        ['name' => 'SALMON', 'code' => '#FA8072', 'created_by' => 13, 'updated_by' => 13],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table(self::TABLE_NAME)->insert(self::COLOR_LIST);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (self::COLOR_LIST as $color) {
            DB::table(self::TABLE_NAME)->where('name', $color['name'])->where('code', $color['code'])->delete();
        }
    }
}
