<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreSizeValues extends Migration
{
    const TABLE_NAME = 'size';
    const SIZE_LIST = [
        ['code' => 'xxxs',  'name' => 'triple extra small'  , 'created_by' => 13, 'updated_by' => 13],
        ['code' => 'xxs',   'name' => 'double extra small', 'created_by' => 13, 'updated_by' => 13],
        ['code' => 'xxl',   'name' => 'double extra large', 'created_by' => 13, 'updated_by' => 13],
        ['code' => 'xxxl',  'name' => 'triple extra large', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '1gb',   'name' => '1GB', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '2gb',   'name' => '2GB', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '4gb',   'name' => '4GB', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '8gb',   'name' => '8GB', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '16gb',  'name' => '16GB', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '32gb',  'name' => '32GB', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '64gb',  'name' => '64GB', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '128gb', 'name' => '128GB' , 'created_by' => 13, 'updated_by' => 13],
        ['code' => '1', 'name' => '1"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '2', 'name' => '2"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '3', 'name' => '3"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '4', 'name' => '4"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '5', 'name' => '5"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '6', 'name' => '6"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '7', 'name' => '7"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '8', 'name' => '8"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '9', 'name' => '9"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '10', 'name' => '10"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '11', 'name' => '11"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '12', 'name' => '12"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '13', 'name' => '13"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '14', 'name' => '14"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '15', 'name' => '15"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '16', 'name' => '16"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '17', 'name' => '17"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '18', 'name' => '18"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '19', 'name' => '19"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '20', 'name' => '20"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '21', 'name' => '21"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '22', 'name' => '22"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '23', 'name' => '23"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '24', 'name' => '24"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '25', 'name' => '25"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '26', 'name' => '26"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '27', 'name' => '27"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '28', 'name' => '28"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '29', 'name' => '29"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '30', 'name' => '30"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '31', 'name' => '31"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '32', 'name' => '32"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '33', 'name' => '33"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '34', 'name' => '34"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '35', 'name' => '35"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '36', 'name' => '36"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '37', 'name' => '37"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '38', 'name' => '38"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '39', 'name' => '39"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '40', 'name' => '40"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '41', 'name' => '41"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '42', 'name' => '42"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '43', 'name' => '43"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '44', 'name' => '44"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '45', 'name' => '45"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '46', 'name' => '46"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '47', 'name' => '47"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '48', 'name' => '48"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '49', 'name' => '49"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '50', 'name' => '50"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '51', 'name' => '51"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '52', 'name' => '52"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '53', 'name' => '53"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '54', 'name' => '54"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '55', 'name' => '55"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '56', 'name' => '56"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '57', 'name' => '57"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '58', 'name' => '58"', 'created_by' => 13, 'updated_by' => 13],
        ['code' => '59', 'name' => '59"', 'created_by' => 13, 'updated_by' => 13],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table(self::TABLE_NAME)->insert(self::SIZE_LIST);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (self::SIZE_LIST as $size) {
            DB::table(self::TABLE_NAME)->where('name', $size['name'])->where('code', $size['code'])->delete();
        }
    }
}
