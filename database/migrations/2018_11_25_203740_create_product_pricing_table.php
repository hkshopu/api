<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Product;

class CreateProductPricingTable extends Migration
{
    const TABLE_NAME = 'product_pricing';

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
            $table->double('price', 15, 2);

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('price');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        $productIPHONE8 = Product::where('sku', 'IPHONE8')->whereNull('deleted_at')->first();
        $productIPHONEX = Product::where('sku', 'IPHONEX')->whereNull('deleted_at')->first();
        $productACERVX15 = Product::where('sku', 'ACERVX15')->whereNull('deleted_at')->first();
        $productGIBSONLESPAUL = Product::where('sku', 'GIBSONLESPAUL')->whereNull('deleted_at')->first();
        $productBOSTONOD200 = Product::where('sku', 'BOSTONOD200')->whereNull('deleted_at')->first();
        $productZOOMG1X = Product::where('sku', 'ZOOMG1X')->whereNull('deleted_at')->first();
        $productZOOMB1Xv2 = Product::where('sku', 'ZOOMB1Xv2')->whereNull('deleted_at')->first();
        $productASUSZENFONE3 = Product::where('sku', 'ASUSZENFONE3')->whereNull('deleted_at')->first();
        $productASUSZENFONE4 = Product::where('sku', 'ASUSZENFONE4')->whereNull('deleted_at')->first();
        $productSKU001 = Product::where('sku', 'SKU001')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'product_id' => $productIPHONE8->id,
                'price' => 100.00,
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['product_id' => $productIPHONE8->id, 'price' => 12500, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $productIPHONEX->id, 'price' => 13888.83, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $productACERVX15->id, 'price' => 9333.5, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $productGIBSONLESPAUL->id, 'price' => 20000, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $productBOSTONOD200->id, 'price' => 1000, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $productZOOMG1X->id, 'price' => 1000, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $productZOOMB1Xv2->id, 'price' => 1500, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $productASUSZENFONE3->id, 'price' => 3000, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $productASUSZENFONE4->id, 'price' => 3000, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $productSKU001->id, 'price' => 1000, 'created_by' => 13, 'updated_by' => 13],
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
