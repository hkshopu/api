
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

    // $router->get('order', ['uses' => 'OrderController@orderList']);
    public function testOrderList() {
        $this->call(
            "GET",
            "/api/order",
            [],
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
            "/api/order",
            [
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

    // $router->post('order', ['uses' => 'OrderController@orderAdd']);
    // $router->get('order/{id}', ['uses' => 'OrderController@orderGet']);
    public function testOrderGet() {
        $this->call(
            "GET",
            "/api/order/123434",
            [],
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
            [],
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
            [],
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
            [],
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
            ->whereNull('shop.deleted_at')
            ->inRandomOrder()
            ->first()
        ;

        if (!empty($order)) {
            $this->call(
                "GET",
                "/api/order/{$order->id}",
                [
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
            [],
            [],
            [],
            [
                'HTTP_token' => $accessTokenConsumer->token,
            ],
            ""
        );
        $this->assertResponseStatus(200);

        $order = \DB::table('order')
            ->leftJoin('cart', 'cart.id', '=', 'order.cart_id')
            ->leftJoin('user', 'user.id', '=', 'cart.user_id')
            ->select('order.*')
            ->where('user.id', $userConsumer->id)
            ->whereNull('order.deleted_at')
            ->whereNull('cart.deleted_at')
            ->inRandomOrder()
            ->first()
        ;

        if (!empty($order)) {
            $this->call(
                "GET",
                "/api/order/{$order->id}",
                [
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

