
<?php

use App\UserType;
use App\User;
use App\AccessToken;
use App\Order;

class OrderTest extends TestCase
{
    const USER_TYPE_SYSTEM_ADMINISTRATOR = 'system administrator';
    const USER_TYPE_SYSTEM_OPERATOR      = 'system operator';
    const USER_TYPE_RETAILER             = 'retailer';
    const USER_TYPE_CONSUMER             = 'consumer';

    private $jsonStructureFull = [
        [
            'id',
            'cart_id',
            'shop_id',
            'shop_payment_method_id',
            'shipment_receiver',
            'shipment_address',
            'shop_cart_gross',
            'shipping_fee_original',
            'shipment_fee_override',
            'shop_order_total',
            'created_at',
            'created_by',
            'user' => [
                'id',
                'username',
                'email',
                'first_name',
                'middle_name',
                'last_name',
                'gender',
                'birth_date',
                'mobile_phone',
                'address',
                'user_type_id',
                'activation_key',
                'language_id',
                'created_at',
                'image_url',
            ],
            'shop' => [
                'id',
                'name',
                'name_en',
                'name_tc',
                'name_sc',
                'description',
                'description_en',
                'description_tc',
                'description_sc',
                'logo_url',
                'user_id',
                'created_at',
                'image_url',
            ],
            'is_new',
            'order_status',
            'payment_status',
            'order_date', // hanggang dito muna
            'shop_order' => [
                'shop_id',
                'logo_url',
                'name',
                'name_en',
                'name_tc',
                'name_sc',
                'payment_method' => [
                    [
                        'id',
                        'name',
                        'code',
                        'account_info',
                        'remarks',
                    ],
                ],
                'cart_date',
                'shop_date',
                'total_quantity',
                'total_amount',
                'total_amount_discounted',
                'shipment_id',
                'shipment_type',
                'shipment_label',
                'shipment_quota',
                'shipment_fee_computed',
                'shop_cart_total',
                'product' => [
                    [
                        'cart_item_id',
                        'product_id',
                        'attribute_id',
                        'attribute' => [
                            'id',
                            'size_id',
                            'color_id',
                            'other',
                            'color',
                            'size',
                        ],
                        'image_url',
                        'name',
                        'name_en',
                        'name_tc',
                        'name_sc',
                        'shop_name',
                        'shop_name_en',
                        'shop_name_tc',
                        'shop_name_sc',
                        'description',
                        'description_en',
                        'description_tc',
                        'description_sc',
                        'cart_date',
                        'product_date',
                        'price',
                        'price_discounted',
                        'quantity',
                        'total_price',
                        'total_price_discounted',
                        'shipping_price',
                        'shipping_price_total',
                        'order_item_status',
                    ],
                ],
            ],
        ],
    ];

    private $jsonStructureSimple = [
        [
            'order_id',
            'order_total_quantity',
            'order_total_price',
            'product_list' => [
                [
                    'product_id',
                    'product_image',
                    'product_name',
                    'product_name_en',
                    'product_name_tc',
                    'product_name_sc',
                    'product_description',
                    'product_description_en',
                    'product_description_tc',
                    'product_description_sc',
                    'shop_name',
                    'shop_name_en',
                    'shop_name_tc',
                    'shop_name_sc',
                    'cart_item_id',
                    'order_date',
                    'quantity',
                    'price',
                    'total_price',
                    'total_price_discounted',
                    'payment_status',
                    'order_item_status',
                ],
            ],
        ],
    ];

    // $router->get('order', ['uses' => 'OrderController@orderList']);
    public function testOrderList() {
        $this->call(
            "GET",
            "/api/order",
            [
                'shop_id' => null,
                'product_id' => null,
            ],
            [],
            [],
            [
                'HTTP_token' => null,
            ],
            ""
        );
        $this->assertResponseStatus(401);

        $userTypeSystemAdministrator = UserType::where('name', self::USER_TYPE_SYSTEM_ADMINISTRATOR)->whereNull('deleted_at')->first();
        $userSystemAdministrator = User::where('user_type_id', $userTypeSystemAdministrator->id)->whereNull('deleted_at')->inRandomOrder()->first();

        $accessTokenSystemAdministrator = AccessToken::where('token', "unit_test_{$userSystemAdministrator->username}")->where('user_id', $userSystemAdministrator->id)->whereNull('deleted_at')->first();
        if (empty($accessTokenSystemAdministrator)) {
            $accessTokenSystemAdministrator = AccessToken::create([
                'user_id' => $userSystemAdministrator->id,
                'token' => "unit_test_{$userSystemAdministrator->username}",
                'expires_at' => '2042-12-31',
                'created_by' => 13,
                'updated_by' => 13,
            ]);
        }

        $this->call(
            "GET",
            "/api/order",
            [
                'shop_id' => null,
                'product_id' => null,
            ],
            [],
            [],
            [
                'HTTP_token' => $accessTokenSystemAdministrator->token,
            ],
            ""
        );
        $this->assertResponseStatus(200);

        $this->seeJsonStructure($this->jsonStructureFull);

        $userTypeSystemOperator = UserType::where('name', self::USER_TYPE_SYSTEM_OPERATOR)->whereNull('deleted_at')->first();
        $userSystemOperator = User::where('user_type_id', $userTypeSystemOperator->id)->whereNull('deleted_at')->inRandomOrder()->first();

        $accessTokenSystemOperator = AccessToken::where('token', "unit_test_{$userSystemOperator->username}")->where('user_id', $userSystemOperator->id)->whereNull('deleted_at')->first();
        if (empty($accessTokenSystemOperator)) {
            $accessTokenSystemOperator = AccessToken::create([
                'user_id' => $userSystemOperator->id,
                'token' => "unit_test_{$userSystemOperator->username}",
                'expires_at' => '2042-12-31',
                'created_by' => 13,
                'updated_by' => 13,
            ]);
        }

        $this->call(
            "GET",
            "/api/order",
            [
                'shop_id' => null,
                'product_id' => null,
            ],
            [],
            [],
            [
                'HTTP_token' => $accessTokenSystemOperator->token,
            ],
            ""
        );
        $this->assertResponseStatus(200);

        $this->seeJsonStructure($this->jsonStructureFull);

        $userTypeRetailer = UserType::where('name', self::USER_TYPE_RETAILER)->whereNull('deleted_at')->first();
        $userRetailer = User::where('user_type_id', $userTypeRetailer->id)->whereNull('deleted_at')->inRandomOrder()->first();

        $accessTokenRetailer = AccessToken::where('token', "unit_test_{$userRetailer->username}")->where('user_id', $userRetailer->id)->whereNull('deleted_at')->first();
        if (empty($accessTokenRetailer)) {
            $accessTokenRetailer = AccessToken::create([
                'user_id' => $userRetailer->id,
                'token' => "unit_test_{$userRetailer->username}",
                'expires_at' => '2042-12-31',
                'created_by' => 13,
                'updated_by' => 13,
            ]);
        }

        $this->call(
            "GET",
            "/api/order",
            [
                'shop_id' => null,
                'product_id' => null,
            ],
            [],
            [],
            [
                'HTTP_token' => $accessTokenRetailer->token,
            ],
            ""
        );
        $this->assertResponseStatus(200);

        if (!empty(json_decode($this->response->content(), true))) {
            $this->seeJsonStructure($this->jsonStructureFull);
        }

        $userTypeConsumer = UserType::where('name', self::USER_TYPE_CONSUMER)->whereNull('deleted_at')->first();
        $userConsumer = User::where('user_type_id', $userTypeConsumer->id)->whereNull('deleted_at')->inRandomOrder()->first();

        $accessTokenConsumer = AccessToken::where('token', "unit_test_{$userConsumer->username}")->where('user_id', $userConsumer->id)->whereNull('deleted_at')->first();
        if (empty($accessTokenConsumer)) {
            $accessTokenConsumer = AccessToken::create([
                'user_id' => $userConsumer->id,
                'token' => "unit_test_{$userConsumer->username}",
                'expires_at' => '2042-12-31',
                'created_by' => 13,
                'updated_by' => 13,
            ]);
        }

        $this->call(
            "GET",
            "/api/order",
            [
                'shop_id' => null,
                'product_id' => null,
            ],
            [],
            [],
            [
                'HTTP_token' => $accessTokenConsumer->token,
            ],
            ""
        );
        $this->assertResponseStatus(200);

        if (!empty(json_decode($this->response->content(), true))) {
            $this->seeJsonStructure($this->jsonStructureSimple);
        }
    }

    // $router->post('order', ['uses' => 'OrderController@orderAdd']);
    // $router->get('order/{id}', ['uses' => 'OrderController@orderGet']);
    public function testOrderGet() {
        $this->call(
            "GET",
            "/api/order/123434",
            [
                'shop_id' => null,
                'product_id' => null,
            ],
            [],
            [],
            [
                'HTTP_token' => null,
            ],
            ""
        );
        $this->assertResponseStatus(401);

        $userTypeSystemAdministrator = UserType::where('name', self::USER_TYPE_SYSTEM_ADMINISTRATOR)->whereNull('deleted_at')->first();
        $userSystemAdministrator = User::where('user_type_id', $userTypeSystemAdministrator->id)->whereNull('deleted_at')->inRandomOrder()->first();

        $accessTokenSystemAdministrator = AccessToken::where('token', "unit_test_{$userSystemAdministrator->username}")->where('user_id', $userSystemAdministrator->id)->whereNull('deleted_at')->first();
        if (empty($accessTokenSystemAdministrator)) {
            $accessTokenSystemAdministrator = AccessToken::create([
                'user_id' => $userSystemAdministrator->id,
                'token' => "unit_test_{$userSystemAdministrator->username}",
                'expires_at' => '2042-12-31',
                'created_by' => 13,
                'updated_by' => 13,
            ]);
        }

        $this->call(
            "GET",
            "/api/order/123434",
            [
                'shop_id' => null,
                'product_id' => null,
            ],
            [],
            [],
            [
                'HTTP_token' => $accessTokenSystemAdministrator->token,
            ],
            ""
        );
        $this->assertResponseStatus(200);

        $order = Order::whereNull('deleted_at')->inRandomOrder()->first();

        $this->call(
            "GET",
            "/api/order/{$order->id}",
            [
                'shop_id' => null,
                'product_id' => null,
            ],
            [],
            [],
            [
                'HTTP_token' => $accessTokenSystemAdministrator->token,
            ],
            ""
        );
        $this->assertResponseStatus(200);

        $userTypeSystemOperator = UserType::where('name', self::USER_TYPE_SYSTEM_OPERATOR)->whereNull('deleted_at')->first();
        $userSystemOperator = User::where('user_type_id', $userTypeSystemOperator->id)->whereNull('deleted_at')->inRandomOrder()->first();

        $accessTokenSystemOperator = AccessToken::where('token', "unit_test_{$userSystemOperator->username}")->where('user_id', $userSystemOperator->id)->whereNull('deleted_at')->first();
        if (empty($accessTokenSystemOperator)) {
            $accessTokenSystemOperator = AccessToken::create([
                'user_id' => $userSystemOperator->id,
                'token' => "unit_test_{$userSystemOperator->username}",
                'expires_at' => '2042-12-31',
                'created_by' => 13,
                'updated_by' => 13,
            ]);
        }

        $this->call(
            "GET",
            "/api/order/123434",
            [
                'shop_id' => null,
                'product_id' => null,
            ],
            [],
            [],
            [
                'HTTP_token' => $accessTokenSystemOperator->token,
            ],
            ""
        );
        $this->assertResponseStatus(200);

        $order = Order::whereNull('deleted_at')->inRandomOrder()->first();

        $this->call(
            "GET",
            "/api/order/{$order->id}",
            [
                'shop_id' => null,
                'product_id' => null,
            ],
            [],
            [],
            [
                'HTTP_token' => $accessTokenSystemAdministrator->token,
            ],
            ""
        );
        $this->assertResponseStatus(200);

        $userTypeRetailer = UserType::where('name', self::USER_TYPE_RETAILER)->whereNull('deleted_at')->first();
        $userRetailer = User::where('user_type_id', $userTypeRetailer->id)->whereNull('deleted_at')->inRandomOrder()->first();

        $accessTokenRetailer = AccessToken::where('token', "unit_test_{$userRetailer->username}")->where('user_id', $userRetailer->id)->whereNull('deleted_at')->first();
        if (empty($accessTokenRetailer)) {
            $accessTokenRetailer = AccessToken::create([
                'user_id' => $userRetailer->id,
                'token' => "unit_test_{$userRetailer->username}",
                'expires_at' => '2042-12-31',
                'created_by' => 13,
                'updated_by' => 13,
            ]);
        }

        $this->call(
            "GET",
            "/api/order/123434",
            [
                'shop_id' => null,
                'product_id' => null,
            ],
            [],
            [],
            [
                'HTTP_token' => $accessTokenRetailer->token,
            ],
            ""
        );
        $this->assertResponseStatus(200);

        $order = \DB::table('order')
            ->leftJoin('shop', 'shop.id', '=', 'order.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('order.*')
            ->where('user.id', $userRetailer->id)
            ->whereNull('order.deleted_at')
            ->inRandomOrder()
            ->first()
        ;

        if (!empty($order)) {
            $this->call(
                "GET",
                "/api/order/{$order->id}",
                [
                    'shop_id' => null,
                    'product_id' => null,
                ],
                [],
                [],
                [
                    'HTTP_token' => $accessTokenRetailer->token,
                ],
                ""
            );
            $this->assertResponseStatus(200);
        }

        $userTypeConsumer = UserType::where('name', self::USER_TYPE_CONSUMER)->whereNull('deleted_at')->first();
        $userConsumer = User::where('user_type_id', $userTypeConsumer->id)->whereNull('deleted_at')->inRandomOrder()->first();

        $accessTokenConsumer = AccessToken::where('token', "unit_test_{$userConsumer->username}")->where('user_id', $userConsumer->id)->whereNull('deleted_at')->first();
        if (empty($accessTokenConsumer)) {
            $accessTokenConsumer = AccessToken::create([
                'user_id' => $userConsumer->id,
                'token' => "unit_test_{$userConsumer->username}",
                'expires_at' => '2042-12-31',
                'created_by' => 13,
                'updated_by' => 13,
            ]);
        }

        $this->call(
            "GET",
            "/api/order/123434",
            [
                'shop_id' => null,
                'product_id' => null,
            ],
            [],
            [],
            [
                'HTTP_token' => $accessTokenConsumer->token,
            ],
            ""
        );
        $this->assertResponseStatus(200);

        $order = \DB::table('order')
            ->leftJoin('cart_item', 'cart_item.order_id', '=', 'order.id')
            ->leftJoin('cart', 'cart.id', '=', 'cart_item.cart_id')
            ->leftJoin('user', 'user.id', '=', 'cart.user_id')
            ->select('order.*')
            ->where('user.id', $userConsumer->id)
            ->whereNull('order.deleted_at')
            ->whereNull('cart_item.deleted_at')
            ->whereNull('cart.deleted_at')
            ->inRandomOrder()
            ->first()
        ;

        if (!empty($order)) {
            $this->call(
                "GET",
                "/api/order/{$order->id}",
                [
                    'shop_id' => null,
                    'product_id' => null,
                ],
                [],
                [],
                [
                    'HTTP_token' => $accessTokenConsumer->token,
                ],
                ""
            );
            $this->assertResponseStatus(200);
        }
    }

    // $router->delete('order/{id}', ['uses' => 'OrderController@orderDelete']);
    // $router->patch('order/{id}',  ['uses' => 'OrderController@orderModify']);
}

