
<?php

class ShopTest extends TestCase
{
    // $router->get('shop',  ['uses' => 'ShopController@shopList']);
    // $router->post('shop', ['uses' => 'ShopController@shopCreate']);
    // $router->get('shop/{id}', ['uses' => 'ShopController@shopGet']);
    // $router->delete('shop/{id}', ['uses' => 'ShopController@shopDelete']);
    // $router->patch('shop/{id}', ['uses' => 'ShopController@shopModify']);
    // $router->get('shoppaymentmethod', ['uses' => 'ShopController@shopPaymentMethodList']);
    // $router->post('shoppaymentmethod', ['uses' => 'ShopController@shopPaymentMethodCreate']);
    // $router->delete('shoppaymentmethod', ['uses' => 'ShopController@shopPaymentMethodDelete']);
    // $router->patch('shoppaymentmethod', ['uses' => 'ShopController@shopPaymentMethodModify']);
    // $router->get('shopshipment', ['uses' => 'ShopController@shopShipmentList']);
    // $router->patch('shopshipment', ['uses' => 'ShopController@shopShipmentModify']);

    // public function testShouldListShop() {
    //     $this->get("/api/shop", []);
    //     $this->seeStatusCode(200);
    //     $this->seeJsonStructure([

    //     ]);
    // }

    public function testShouldListShopPaymentMethod() {
        $this->get("/api/shoppaymentmethod", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }

    public function testShouldListShopShipment() {
        $this->get("/api/shopshipment", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }
}

