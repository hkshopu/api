
<?php

use App\UserType;
use App\User;
use App\AccessToken;

class UserTest extends TestCase
{
    const USER_TYPE_SYSTEM_ADMINISTRATOR = 'system administrator';
    const USER_TYPE_SYSTEM_OPERATOR      = 'system operator';
    const USER_TYPE_RETAILER             = 'retailer';
    const USER_TYPE_CONSUMER             = 'consumer';

    // $router->get('user',  ['uses' => 'UserController@userList']);
    public function testUserList() {
        $this->call(
            "GET",
            "/api/user",
            [
                'username' => null,
                'email' => null,
                'page_number' => null,
                'page_size' => null,
                'product_id' => null,
                'access_token_user_id' => null,
            ],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->post('user', ['uses' => 'UserController@userCreate']);
    // $router->get('user/{id}', ['uses' => 'UserController@userGet']);
    public function testUserGet() {
        $this->call(
            "GET",
            "/api/user/123434",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);

        $this->call(
            "GET",
            "/api/user",
            [
                'username' => null,
                'email' => null,
                'page_number' => null,
                'page_size' => null,
                'product_id' => null,
                'access_token_user_id' => null,
            ],
            [],
            [],
            [],
            ""
        );

        $userList = json_decode($this->response->content());
        $user = $userList[array_rand($userList)];

        $this->call(
            "GET",
            "/api/user/{$user->id}",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->delete('user/{id}', ['uses' => 'UserController@userDelete']);
    // $router->patch('user/{id}', ['uses' => 'UserController@userModify']);
    // $router->post('register', ['uses' => 'UserController@userRegister']);
    // $router->post('signup', ['uses' => 'UserController@userSignup']);
    // $router->post('login',  ['uses' => 'UserController@userLogin']);
    // $router->get('logout',  ['uses' => 'UserController@userLogout']);
    public function testUserLogout() {
        $this->call(
            "GET",
            "/api/logout",
            [],
            [],
            [],
            [
                'HTTP_token' => null,
            ],
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
            "/api/logout",
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
            "/api/logout",
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
            "/api/logout",
            [],
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
            "/api/logout",
            [],
            [],
            [],
            [
                'HTTP_token' => $accessTokenConsumer->token,
            ],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->patch('updatepassword/{user_id}',  ['uses' => 'UserController@passwordUpdate']);
    // $router->patch('changelanguage',  ['uses' => 'UserController@languageChange']);
}

