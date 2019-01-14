<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Product;
use App\Size;
use App\Color;
use App\ProductAttribute;

class CreateProductInventoryTable extends Migration
{
    const TABLE_NAME = 'product_inventory';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->bigInteger('id', 1)->unsigned();
            $table->bigInteger('product_id')->unsigned();
            $table->bigInteger('attribute_id')->unsigned()->nullable();
            $table->bigInteger('stock');

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('stock');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        $product1 = Product::where('sku', 'IPHONE8')->whereNull('deleted_at')->first();
        $product2 = Product::where('sku', 'IPHONEX')->whereNull('deleted_at')->first();
        $product3 = Product::where('sku', 'ACERVX15')->whereNull('deleted_at')->first();
        $product4 = Product::where('sku', 'ZOOMG1X')->whereNull('deleted_at')->first();
        $product5 = Product::where('sku', 'ZOOMB1Xv2')->whereNull('deleted_at')->first();
        $product6 = Product::where('sku', 'ASUSZENFONE3')->whereNull('deleted_at')->first();
        $product7 = Product::where('sku', 'ASUSZENFONE4')->whereNull('deleted_at')->first();
        $product8 = Product::where('sku', 'SKU001')->whereNull('deleted_at')->first();

        $sizeSmall = Size::where('name', 'small')->whereNull('deleted_at')->first();
        $sizeMedium = Size::where('name', 'medium')->whereNull('deleted_at')->first();
        $sizeLarge = Size::where('name', 'large')->whereNull('deleted_at')->first();

        $colorWhite = Color::where('name', 'white')->whereNull('deleted_at')->first();
        $colorGray = Color::where('name', 'gray')->whereNull('deleted_at')->first();
        $colorSilver = Color::where('name', 'silver')->whereNull('deleted_at')->first();
        $colorBlack = Color::where('name', 'black')->whereNull('deleted_at')->first();
        $colorRed = Color::where('name', 'red')->whereNull('deleted_at')->first();

        $productAttribute1 = ProductAttribute::where('size_id', null)->where('color_id', $colorWhite->id)->where('other', null)->whereNull('deleted_at')->first();
        $productAttribute2 = ProductAttribute::where('size_id', null)->where('color_id', $colorGray->id)->where('other', null)->whereNull('deleted_at')->first();
        $productAttribute3 = ProductAttribute::where('size_id', null)->where('color_id', $colorSilver->id)->where('other', null)->whereNull('deleted_at')->first();
        $productAttribute4 = ProductAttribute::where('size_id', $sizeSmall->id)->where('color_id', null)->where('other', null)->whereNull('deleted_at')->first();
        $productAttribute5 = ProductAttribute::where('size_id', $sizeMedium->id)->where('color_id', null)->where('other', null)->whereNull('deleted_at')->first();
        $productAttribute6 = ProductAttribute::where('size_id', $sizeLarge->id)->where('color_id', null)->where('other', null)->whereNull('deleted_at')->first();
        $productAttribute7 = ProductAttribute::where('size_id', null)->where('color_id', $colorSilver->id)->where('other', 'FREE Case')->whereNull('deleted_at')->first();
        $productAttribute8 = ProductAttribute::where('size_id', null)->where('color_id', $colorBlack->id)->where('other', null)->whereNull('deleted_at')->first();
        $productAttribute9 = ProductAttribute::where('size_id', null)->where('color_id', $colorRed->id)->where('other', null)->whereNull('deleted_at')->first();
        $productAttribute10 = ProductAttribute::where('size_id', null)->where('color_id', $colorBlack->id)->where('other', 'FREE Case')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'product_id' => $product1->id,
                'stock' => 0,
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['product_id' => $product1->id,	'attribute_id' => $productAttribute1->id,  'stock' => 10, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $product1->id,	'attribute_id' => $productAttribute1->id,  'stock' => 15, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $product1->id,	'attribute_id' => $productAttribute2->id,  'stock' => 5 , 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $product1->id,	'attribute_id' => $productAttribute3->id,  'stock' => 15, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $product2->id,	'attribute_id' => $productAttribute3->id,  'stock' => 20, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $product6->id,	'attribute_id' => $productAttribute3->id,  'stock' => 20, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $product3->id,	'attribute_id' => $productAttribute4->id,  'stock' => 10, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $product3->id,	'attribute_id' => $productAttribute5->id,  'stock' => 10, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $product3->id,	'attribute_id' => $productAttribute6->id,  'stock' => 10, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $product2->id,	'attribute_id' => $productAttribute7->id,  'stock' => 13, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $product4->id,	'attribute_id' => $productAttribute8->id,  'stock' => -1, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $product4->id,	'attribute_id' => $productAttribute8->id,  'stock' => 10, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $product5->id,	'attribute_id' => $productAttribute9->id,  'stock' => -1, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $product5->id,	'attribute_id' => $productAttribute9->id,  'stock' => 8 , 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $product7->id,	'attribute_id' => $productAttribute10->id, 'stock' => -5, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $product7->id,	'attribute_id' => $productAttribute10->id, 'stock' => 33, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $product6->id,	'attribute_id' => null,                    'stock' => -2, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $product6->id,	'attribute_id' => null,                    'stock' => 10, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $product8->id,	'attribute_id' => null,                    'stock' => 10, 'created_by' => 13, 'updated_by' => 13],
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
