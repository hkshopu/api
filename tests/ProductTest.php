
<?php

use App\UserType;
use App\User;
use App\AccessToken;

class ProductTest extends TestCase
{
    const USER_TYPE_SYSTEM_ADMINISTRATOR = 'system administrator';
    const USER_TYPE_SYSTEM_OPERATOR      = 'system operator';
    const USER_TYPE_RETAILER             = 'retailer';
    const USER_TYPE_CONSUMER             = 'consumer';

    // $router->get('product',  ['uses' => 'ProductController@productList']);
    public function testProductList() {
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
            [],
            ""
        );
        $this->assertResponseStatus(200);

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
                'HTTP_token' => $accessTokenConsumer->token,
            ],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->post('product', ['uses' => 'ProductController@productCreate']);
    // $router->get('product/{id}', ['uses' => 'ProductController@productGet']);
    public function testProductGet() {
        $this->call(
            "GET",
            "/api/product/123434",
            [
                'shop_id' => null,
                'access_token_user_id' => null,
            ],
            [],
            [],
            [
                'HTTP_token' => null,
            ],
            ""
        );
        $this->assertResponseStatus(200);

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
            "/api/product/{$product->id}",
            [
                'shop_id' => null,
                'access_token_user_id' => null,
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
            "/api/product/{$product->id}",
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
            "/api/product/{$product->id}",
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
            "/api/product/{$product->id}",
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
                'HTTP_token' => $accessTokenConsumer->token,
            ],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->delete('product/{id}', ['uses' => 'ProductController@productDelete']);
    // $router->patch('product/{id}', ['uses' => 'ProductController@productModify']);
    // $router->put('productstock/{product_id}', ['uses' => 'ProductController@productStockPut']);
    // $router->post('productstock/{product_id}', ['uses' => 'ProductController@productStockPost']);
    // $router->delete('productstock/{product_id}', ['uses' => 'ProductController@productStockDelete']);
}

