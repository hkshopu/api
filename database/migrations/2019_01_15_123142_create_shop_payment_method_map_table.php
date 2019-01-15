<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Shop;
use App\PaymentMethod;

class CreateShopPaymentMethodMapTable extends Migration
{
    const TABLE_NAME = 'shop_payment_method_map';

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
            $table->bigInteger('payment_method_id')->unsigned();
            $table->string('account_info', 512);
            $table->string('remarks', 512)->nullable();

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('remarks');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        $shop1 = Shop::where('name_en', 'Variety Shop')->whereNull('deleted_at')->first();
        $shop2 = Shop::where('name_en', 'Weinstein Guitars')->whereNull('deleted_at')->first();
        $shop3 = Shop::where('name_en', 'The King Slayer')->whereNull('deleted_at')->first();
        $shop4 = Shop::where('name_en', 'The Dirty Deeds Bike Shop')->whereNull('deleted_at')->first();
        $shop5 = Shop::where('name_en', 'The Roman Empire')->whereNull('deleted_at')->first();
        $shop6 = Shop::where('name_en', 'Gadget Planet')->whereNull('deleted_at')->first();

        $paymentMethod1 = PaymentMethod::where('code', 'paypal')->whereNull('deleted_at')->first();
        $paymentMethod2 = PaymentMethod::where('code', 'spf')->whereNull('deleted_at')->first();
        $paymentMethod3 = PaymentMethod::where('code', 'bank')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'shop_id' => $shop1->id,
                'payment_method_id' => $paymentMethod1->id,
                'account_info' => '000000000000',
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['shop_id' => $shop1->id, 'payment_method_id' => $paymentMethod1->id, 'account_info' => 'paypal.me/VarietyShop',      'remarks' => null,                   'created_by' => 13, 'updated_by' => 13],
            ['shop_id' => $shop1->id, 'payment_method_id' => $paymentMethod2->id, 'account_info' => 'SPF000111222333',            'remarks' => null,                   'created_by' => 13, 'updated_by' => 13],
            ['shop_id' => $shop1->id, 'payment_method_id' => $paymentMethod3->id, 'account_info' => '6-54624252-316',             'remarks' => 'HKD Checking Account', 'created_by' => 13, 'updated_by' => 13],
            ['shop_id' => $shop2->id, 'payment_method_id' => $paymentMethod1->id, 'account_info' => 'paypal.me/WeinsteinGuitars', 'remarks' => null,                   'created_by' => 13, 'updated_by' => 13],
            ['shop_id' => $shop2->id, 'payment_method_id' => $paymentMethod3->id, 'account_info' => '314-511-32414-5',            'remarks' => 'HKD Checking Account', 'created_by' => 13, 'updated_by' => 13],
            ['shop_id' => $shop3->id, 'payment_method_id' => $paymentMethod1->id, 'account_info' => 'paypal.me/SethRollins',      'remarks' => null,                   'created_by' => 13, 'updated_by' => 13],
            ['shop_id' => $shop4->id, 'payment_method_id' => $paymentMethod2->id, 'account_info' => 'SPF111222333444',            'remarks' => null,                   'created_by' => 13, 'updated_by' => 13],
            ['shop_id' => $shop5->id, 'payment_method_id' => $paymentMethod3->id, 'account_info' => '475272-475724',              'remarks' => 'HKD Savings',          'created_by' => 13, 'updated_by' => 13],
            ['shop_id' => $shop6->id, 'payment_method_id' => $paymentMethod2->id, 'account_info' => 'SPF222333444555',            'remarks' => null,                   'created_by' => 13, 'updated_by' => 13],
            ['shop_id' => $shop6->id, 'payment_method_id' => $paymentMethod3->id, 'account_info' => '24565-65652-46',             'remarks' => 'HKD Checking Account', 'created_by' => 13, 'updated_by' => 13],
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
