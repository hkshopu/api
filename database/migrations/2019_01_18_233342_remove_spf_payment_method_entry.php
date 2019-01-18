<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\User;
use App\PaymentMethod;
use Carbon\Carbon;

class RemoveSpfPaymentMethodEntry extends Migration
{
    const DATA_DELETE_DATE = '2019-01-19 00:04:42';
    const TABLE_NAME_PAYMENT_METHOD = 'payment_method';
    const COLUMN_NAME_PAYMENT_METHOD = 'code';
    const COLUMN_VALUE_PAYMENT_METHOD = 'spf';
    const TABLE_NAME_SHOP_PAYMENT_METHOD_MAP = 'shop_payment_method_map';
    const COLUMN_NAME_SHOP_PAYMENT_METHOD_MAP = 'payment_method_id';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $user = User::where('username', 'jtaylo')->whereNull('deleted_at')->first();
        $paymentMethod = PaymentMethod::where(self::COLUMN_NAME_PAYMENT_METHOD, self::COLUMN_VALUE_PAYMENT_METHOD)->whereNull('deleted_at')->first();

        DB::table(self::TABLE_NAME_PAYMENT_METHOD)
            ->where(self::COLUMN_NAME_PAYMENT_METHOD, self::COLUMN_VALUE_PAYMENT_METHOD)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => self::DATA_DELETE_DATE,
                'deleted_by' => $user->id,
            ]);

        DB::table(self::TABLE_NAME_SHOP_PAYMENT_METHOD_MAP)
            ->where(self::COLUMN_NAME_SHOP_PAYMENT_METHOD_MAP, $paymentMethod->id)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => self::DATA_DELETE_DATE,
                'deleted_by' => $user->id,
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $paymentMethod = PaymentMethod::where(self::COLUMN_NAME_PAYMENT_METHOD, self::COLUMN_VALUE_PAYMENT_METHOD)->where('deleted_at', self::DATA_DELETE_DATE)->first();

        DB::table(self::TABLE_NAME_PAYMENT_METHOD)
            ->where(self::COLUMN_NAME_PAYMENT_METHOD, self::COLUMN_VALUE_PAYMENT_METHOD)
            ->where('deleted_at', self::DATA_DELETE_DATE)
            ->update([
                'deleted_at' => null,
                'deleted_by' => null,
            ]);

        DB::table(self::TABLE_NAME_SHOP_PAYMENT_METHOD_MAP)
            ->where(self::COLUMN_NAME_SHOP_PAYMENT_METHOD_MAP, $paymentMethod->id)
            ->where('deleted_at', self::DATA_DELETE_DATE)
            ->update([
                'deleted_at' => null,
                'deleted_by' => null,
            ]);
    }
}
