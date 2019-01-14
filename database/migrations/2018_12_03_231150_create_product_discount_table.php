<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Product;

class CreateProductDiscountTable extends Migration
{
    const TABLE_NAME = 'product_discount';

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
            $table->enum('type', ['percentage', 'fixed']);
            $table->double('amount', 15, 2);

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

        $productIPHONE8 = Product::where('sku', 'IPHONE8')->whereNull('deleted_at')->first();
        $productIPHONEX = Product::where('sku', 'IPHONEX')->whereNull('deleted_at')->first();
        $productZOOMB1Xv2 = Product::where('sku', 'ZOOMB1Xv2')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'product_id' => $productIPHONE8->id,
                'type' => 'fixed',
                'amount' => 0.00,
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['product_id' => $productIPHONE8->id,   'type' => 'percentage', 'amount' => 0.10,   'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $productIPHONEX->id,   'type' => 'fixed',      'amount' => 166.66, 'created_by' => 13, 'updated_by' => 13],
            ['product_id' => $productZOOMB1Xv2->id, 'type' => 'percentage', 'amount' => 0.33,   'created_by' => 13, 'updated_by' => 13],
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
