<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreStatusValues extends Migration
{
    const TABLE_NAME = 'status';
    const ROW_LIST = [
        ['name' => 'wait for payment', 'created_by' => 13, 'updated_by' => 13],
        ['name' => 'paid',             'created_by' => 13, 'updated_by' => 13],
        ['name' => 'on hold',          'created_by' => 13, 'updated_by' => 13],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table(self::TABLE_NAME)->insert(self::ROW_LIST);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (self::ROW_LIST as $row) {
            DB::table(self::TABLE_NAME)->where('name', $row['name'])->delete();
        }
    }
}
