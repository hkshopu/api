<?php

use App\UserType;
use App\User;
use App\AccessToken;

class ViewTest extends TestCase
{
    const USER_TYPE_SYSTEM_ADMINISTRATOR = 'system administrator';
    const USER_TYPE_SYSTEM_OPERATOR      = 'system operator';
    const USER_TYPE_RETAILER             = 'retailer';
    const USER_TYPE_CONSUMER             = 'consumer';

    // $router->post('productview', ['uses' => 'ViewController@productViewAdd']);
    // $router->get('productview/{product_id}',  ['uses' => 'ViewController@productViewGet']);
    public function testProductViewGet() {
        $this->call(
            "GET",
            "/api/productview/123434",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(400);

        $this->call(
            "GET",
            "/api/product",
            [
                'shop_id' => null,
                'category_id' => null,
                'name_en' => null,
                'sort' => null,
                'page_number' => null,
                'page_size' => null,
                'access_token_user_id' => null,
            ],
            [],
            [],
            [
                'HTTP_token' => null,
            ],
            ""
        );

        $productList = json_decode($this->response->content());
        $product = $productList[array_rand($productList)];

        $this->call(
            "GET",
            "/api/productview/{$product->id}",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->post('blogview', ['uses' => 'ViewController@blogViewAdd']);
    // $router->get('blogview/{blog_id}',  ['uses' => 'ViewController@blogViewGet']);
    public function testBlogViewGet() {
        $this->call(
            "GET",
            "/api/blogview/123434",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(400);

        $this->call(
            "GET",
            "/api/blog",
            [
                'shop_id' => null,
                'category_id' => null,
                'title_en' => null,
                'page_number' => null,
                'page_size' => null,
            ],
            [],
            [],
            [
                'HTTP_token' => null,
            ],
            ""
        );

        $blogList = json_decode($this->response->content());
        $blog = $blogList[array_rand($blogList)];

        $this->call(
            "GET",
            "/api/blogview/{$blog->id}",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->post('orderview', ['uses' => 'ViewController@orderViewAdd']);
    // $router->get('orderview/{order_id}',  ['uses' => 'ViewController@orderViewGet']);
    public function testOrderViewGet() {
        $this->call(
            "GET",
            "/api/orderview/123434",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(400);

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

        $orderList = json_decode($this->response->content());
        $order = $orderList[array_rand($orderList)];

        $this->call(
            "GET",
            "/api/orderview/{$order->id}",
            [],
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

        $orderList = json_decode($this->response->content());
        $order = $orderList[array_rand($orderList)];

        $this->call(
            "GET",
            "/api/orderview/{$order->id}",
            [],
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

        $orderList = json_decode($this->response->content());
        if (!empty($orderList)) {
            $order = $orderList[array_rand($orderList)];

            $this->call(
                "GET",
                "/api/orderview/{$order->id}",
                [],
                [],
                [],
                [
                    'HTTP_token' => $accessTokenRetailer->token,
                ],
                ""
            );
            $this->assertResponseStatus(200);
        }
    }
}

