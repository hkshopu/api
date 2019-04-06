<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Carbon\Carbon;
use App\Cart;
use App\Shop;
use App\ShopPaymentMethodMap;

class CreateOrderTable extends Migration
{
    const TABLE_NAME = 'order';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->bigInteger('id', 1)->unsigned();
            $table->bigInteger('cart_id')->unsigned();
            $table->bigInteger('shop_id')->unsigned();
            $table->bigInteger('shop_payment_method_id')->unsigned();
            $table->string('shipment_receiver', 512)->nullable();
            $table->string('shipment_address', 512)->nullable();
            $table->string('shipment_fee_override', 15, 2)->nullable();

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('shipment_fee_override');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        $cart = Cart::whereNull('deleted_at')->first();
        $shop = Shop::whereNull('deleted_at')->first();
        $shopPaymentMethodMap = ShopPaymentMethodMap::where('shop_id', $shop->id)->whereNull('deleted_at')->first();

        $tableIncrement = 67337000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'cart_id' => $cart->id,
                'shop_id' => $shop->id,
                'shop_payment_method_id' => $shopPaymentMethodMap->id,
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
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
