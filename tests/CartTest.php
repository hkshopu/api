
<?php

use Illuminate\Http\Request;

class CartTest extends TestCase
{
    // $router->post('cart', ['uses' => 'CartController@cartAdd']);
    // $router->post('carttest', ['uses' => 'CartController@cartAddTest']);
    // $router->get('cart/{cart_id}',  ['uses' => 'CartController@cartGet']);
    public function testCartGet() {
        $this->call(
            "GET",
            "/api/cart/123434",
            [
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
    }

    // $router->patch('cart',  ['uses' => 'CartController@cartModify']);
    // $router->delete('cart', ['uses' => 'CartController@cartDelete']);
    // $router->post('assigncart', ['uses' => 'CartController@cartAssign']);
}

