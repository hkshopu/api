<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Shop;

class CreateProductTable extends Migration
{
    const TABLE_NAME = 'product';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->bigInteger('id', 1)->unsigned();
            $table->string('sku', 512)->unique();
            $table->string('name_en', 512);
            $table->string('name_tc', 512)->nullable();
            $table->string('name_sc', 512)->nullable();
            $table->text('description_en');
            $table->text('description_tc')->nullable();
            $table->text('description_sc')->nullable();
            $table->bigInteger('shop_id')->unsigned();

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('shop_id');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        $shop1 = Shop::where('name_en', 'Variety Shop')->whereNull('deleted_at')->first();
        $shop2 = Shop::where('name_en', 'Weinstein Guitars')->whereNull('deleted_at')->first();
        $shop3 = Shop::where('name_en', 'Gadget Planet')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'sku' => 'TEST',
                'name_en' => 'Test',
                'description_en' => 'Test',
                'shop_id' => $shop1->id,
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['sku' => 'IPHONE8',       'name_en' => 'iPhone 8',                            'name_tc' => null, 'name_sc' => null, 'description_en' => 'Latest iPhone release',                                                                                                                                                                                                                                'description_tc' => null, 'description_sc' => null, 'shop_id' => $shop3->id, 'created_by' => 13, 'updated_by' => 13],
            ['sku' => 'IPHONEX',       'name_en' => 'iPhone X',                            'name_tc' => null, 'name_sc' => null, 'description_en' => 'Not so latest iPhone release',                                                                                                                                                                                                                         'description_tc' => null, 'description_sc' => null, 'shop_id' => $shop3->id, 'created_by' => 13, 'updated_by' => 13],
            ['sku' => 'ACERVX15',      'name_en' => 'Acer VX15',                           'name_tc' => null, 'name_sc' => null, 'description_en' => 'Not bad for a gaming laptop, though',                                                                                                                                                                                                                  'description_tc' => null, 'description_sc' => null, 'shop_id' => $shop2->id, 'created_by' => 13, 'updated_by' => 13],
            ['sku' => 'GIBSONLESPAUL', 'name_en' => 'Gibson - Les Paul',                   'name_tc' => null, 'name_sc' => null, 'description_en' => 'The guitar that made legends. Enough said.',                                                                                                                                                                                                           'description_tc' => null, 'description_sc' => null, 'shop_id' => $shop2->id, 'created_by' => 13, 'updated_by' => 13],
            ['sku' => 'BOSTONOD200',   'name_en' => 'Boston Engineering OD-200 Overdrive', 'name_tc' => null, 'name_sc' => null, 'description_en' => 'Equipped with dual gain circuitry, the OD-100 is easy to create the tight and fat over drive sound by adjusting the two bands EQ and the GAIN knobs. It\'s so sensitive to capture details of the performance, and remain the warm distortion sound.', 'description_tc' => null, 'description_sc' => null, 'shop_id' => $shop2->id, 'created_by' => 13, 'updated_by' => 13],
            ['sku' => 'ZOOMG1X',       'name_en' => 'Zoom G1X Guitar Effects Pedal',       'name_tc' => null, 'name_sc' => null, 'description_en' => 'AMAZINGLY AFFORDABLE PACKAGE WITH KNOCKOUT PERFORMANCE.',                                                                                                                                                                                              'description_tc' => null, 'description_sc' => null, 'shop_id' => $shop2->id, 'created_by' => 13, 'updated_by' => 13],
            ['sku' => 'ZOOMB1Xv2',     'name_en' => 'Zoom B1X BASS EFFECTS PEDAL (V2)',    'name_tc' => null, 'name_sc' => null, 'description_en' => 'VERSION 2: Amazingly affordable package with knockout performance.',                                                                                                                                                                                   'description_tc' => null, 'description_sc' => null, 'shop_id' => $shop2->id, 'created_by' => 13, 'updated_by' => 13],
            ['sku' => 'ASUSZENFONE3',  'name_en' => 'Asus Zenfone 3',                      'name_tc' => null, 'name_sc' => null, 'description_en' => 'Asus Zenfone 3 Description',                                                                                                                                                                                                                           'description_tc' => null, 'description_sc' => null, 'shop_id' => $shop3->id, 'created_by' => 13, 'updated_by' => 13],
            ['sku' => 'ASUSZENFONE4',  'name_en' => 'Asus Zenfone 4',                      'name_tc' => null, 'name_sc' => null, 'description_en' => 'Asus Zenfone 4 Description',                                                                                                                                                                                                                           'description_tc' => null, 'description_sc' => null, 'shop_id' => $shop3->id, 'created_by' => 13, 'updated_by' => 13],
            ['sku' => 'SKU001',        'name_en' => 'Name English Product',                'name_tc' => null, 'name_sc' => null, 'description_en' => 'Description in English',                                                                                                                                                                                                                               'description_tc' => null, 'description_sc' => null, 'shop_id' => $shop1->id, 'created_by' => 13, 'updated_by' => 13],
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
