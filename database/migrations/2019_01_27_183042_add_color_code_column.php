<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColorCodeColumn extends Migration
{
    const TABLE_NAME = 'color';
    const COLUMN_NAME = 'code';
    const COLUMN_NAME_AFTER = 'name';
    const COLUMN_NAME_WHERE = 'name';
    const COLOR_LIST = [
        [
            'name' => 'white',
            'code' => '#FFFFFF',
        ],
        [
            'name' => 'gray',
            'code' => '#808080',
        ],
        [
            'name' => 'silver',
            'code' => '#C0C0C0',
        ],
        [
            'name' => 'gold',
            'code' => '#FFD700',
        ],
        [
            'name' => 'black',
            'code' => '#000000',
        ],
        [
            'name' => 'red',
            'code' => '#FF0000',
        ],
        [
            'name' => 'light gray',
            'code' => '#D3D3D3',
        ],
    ];

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

        foreach (self::COLOR_LIST as $color){
            DB::table(self::TABLE_NAME)
                ->where([
                    self::COLUMN_NAME_WHERE => $color['name'],
                ])
                ->update([
                    self::COLUMN_NAME => $color['code'],
                ]);
        }
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
