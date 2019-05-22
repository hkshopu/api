<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Entity;
use App\Status;

class AddStatusOptionRows extends Migration
{
    const TABLE_NAME = 'status_option';
    const STATUS_OPTION_LIST = [
        ['entity' => 'order',      'status' => 'process',          'created_by' => 13, 'updated_by' => 13],
        ['entity' => 'order',      'status' => 'shipment',         'created_by' => 13, 'updated_by' => 13],
        ['entity' => 'order',      'status' => 'finish',           'created_by' => 13, 'updated_by' => 13],
        ['entity' => 'order',      'status' => 'on hold',          'created_by' => 13, 'updated_by' => 13],
        ['entity' => 'payment',    'status' => 'wait for payment', 'created_by' => 13, 'updated_by' => 13],
        ['entity' => 'payment',    'status' => 'paid',             'created_by' => 13, 'updated_by' => 13],
        ['entity' => 'order_item', 'status' => 'process',          'created_by' => 13, 'updated_by' => 13],
        ['entity' => 'order_item', 'status' => 'shipment',         'created_by' => 13, 'updated_by' => 13],
        ['entity' => 'order_item', 'status' => 'finish',           'created_by' => 13, 'updated_by' => 13],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::STATUS_OPTION_LIST as $statusOptionItem) {
            $entity = Entity::where('name', $statusOptionItem['entity'])->whereNull('deleted_at')->first();
            $status = Status::where('name', $statusOptionItem['status'])->whereNull('deleted_at')->first();
            $statusOptionItem['entity'] = $entity->id;
            $statusOptionItem['status_id'] = $status->id;
            unset($statusOptionItem['status']);

            DB::table(self::TABLE_NAME)->insert($statusOptionItem);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (self::STATUS_OPTION_LIST as $statusOptionItem) {
            $entity = Entity::where('name', $statusOptionItem['entity'])->whereNull('deleted_at')->first();
            $status = Status::where('name', $statusOptionItem['status'])->whereNull('deleted_at')->first();
            $statusOptionItem['entity'] = $entity->id;
            $statusOptionItem['status_id'] = $status->id;

            DB::table(self::TABLE_NAME)->where('entity', $statusOptionItem['entity'])->where('status_id', $statusOptionItem['status_id'])->delete();
        }
    }
}

