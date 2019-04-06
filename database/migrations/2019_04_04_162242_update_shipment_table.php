<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShipmentTable extends Migration
{
    const TABLE_NAME = 'shipment';
    const ROW_NAME_ALL = 'all';
    const ROW_NAME_OVER = 'over';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table(self::TABLE_NAME)
                ->where('name', self::ROW_NAME_ALL)
                ->update(['label' => 'All order free shipment']);
        DB::table(self::TABLE_NAME)
                ->where('name', self::ROW_NAME_OVER)
                ->update(['label' => 'Free shipment. If Order total price over:']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table(self::TABLE_NAME)
                ->where('name', self::ROW_NAME_ALL)
                ->update(['label' => 'All order fee shipment']);
        DB::table(self::TABLE_NAME)
                ->where('name', self::ROW_NAME_OVER)
                ->update(['label' => 'Fee shipment, If Order total price over:']);
    }
}
