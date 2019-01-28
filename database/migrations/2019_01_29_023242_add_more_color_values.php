<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreColorValues extends Migration
{
    const TABLE_NAME = 'color';
    const COLOR_LIST = [
        ['name' => 'pink'  , 'code' => '#FFC0CB', 'created_by' => 13, 'updated_by' => 13],
        ['name' => 'orange', 'code' => '#FFA500', 'created_by' => 13, 'updated_by' => 13],
        ['name' => 'yellow', 'code' => '#FFFF00', 'created_by' => 13, 'updated_by' => 13],
        ['name' => 'violet', 'code' => '#EE82EE', 'created_by' => 13, 'updated_by' => 13],
        ['name' => 'purple', 'code' => '#800080', 'created_by' => 13, 'updated_by' => 13],
        ['name' => 'indigo', 'code' => '#4B0082', 'created_by' => 13, 'updated_by' => 13],
        ['name' => 'green' , 'code' => '#008000', 'created_by' => 13, 'updated_by' => 13],
        ['name' => 'blue'  , 'code' => '#0000FF', 'created_by' => 13, 'updated_by' => 13],
        ['name' => 'brown' , 'code' => '#A52A2A', 'created_by' => 13, 'updated_by' => 13],
        ['name' => 'maroon', 'code' => '#800000', 'created_by' => 13, 'updated_by' => 13],
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
