<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Shop;
use App\Shipment;

class CreateShopShipmentMapTable extends Migration
{
    const TABLE_NAME = 'shop_shipment_map';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->bigInteger('id', 1)->unsigned();
            $table->bigInteger('shop_id')->unsigned();
            $table->bigInteger('shipment_id')->unsigned();
            $table->double('amount', 15, 2)->nullable();

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('amount');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        $shop1 = Shop::where('name_en', 'Variety Shop')->whereNull('deleted_at')->first();
        $shop2 = Shop::where('name_en', 'Weinstein Guitars')->whereNull('deleted_at')->first();
        $shop3 = Shop::where('name_en', 'The King Slayer')->whereNull('deleted_at')->first();
        $shop4 = Shop::where('name_en', 'The Dirty Deeds Bike Shop')->whereNull('deleted_at')->first();
        $shop5 = Shop::where('name_en', 'The Roman Empire')->whereNull('deleted_at')->first();
        $shop6 = Shop::where('name_en', 'Gadget Planet')->whereNull('deleted_at')->first();

        $shipment1 = Shipment::where('name', 'normal')->whereNull('deleted_at')->first();
        $shipment2 = Shipment::where('name', 'all')->whereNull('deleted_at')->first();
        $shipment3 = Shipment::where('name', 'over')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'shop_id' => $shop1->id,
                'shipment_id' => $shipment1->id,
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['shop_id' => $shop1->id, 'shipment_id' => $shipment1->id, 'amount' => null,    'created_by' => 13, 'updated_by' => 13],
            ['shop_id' => $shop2->id, 'shipment_id' => $shipment1->id, 'amount' => null,    'created_by' => 13, 'updated_by' => 13],
            ['shop_id' => $shop3->id, 'shipment_id' => $shipment1->id, 'amount' => null,    'created_by' => 13, 'updated_by' => 13],
            ['shop_id' => $shop4->id, 'shipment_id' => $shipment2->id, 'amount' => null,    'created_by' => 13, 'updated_by' => 13],
            ['shop_id' => $shop5->id, 'shipment_id' => $shipment3->id, 'amount' => 2000,    'created_by' => 13, 'updated_by' => 13],
            ['shop_id' => $shop6->id, 'shipment_id' => $shipment3->id, 'amount' => 1234.56, 'created_by' => 13, 'updated_by' => 13],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
}
