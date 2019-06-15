<?php

class RatingTest extends TestCase
{
    // $router->post('shoprating', ['uses' => 'RatingController@shopRatingAdd']);
    // $router->get('shoprating/{shop_id}',  ['uses' => 'RatingController@shopRatingGet']);
    public function testShopRatingGet() {
        $this->call(
            "GET",
            "/api/shoprating/123434",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(400);

        $this->call(
            "GET",
            "/api/shop",
            [
                'name_en' => null,
                'page_number' => null,
                'page_size' => null,
                'product_id' => null,
                'access_token_user_id' => null,
            ],
            [],
            [],
            [
                'HTTP_token' => null,
            ],
            ""
        );

        $shopList = json_decode($this->response->content());
        $shop = $shopList[array_rand($shopList)];

        $this->call(
            "GET",
            "/api/shoprating/{$shop->id}",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->delete('shoprating/{shop_id}', ['uses' => 'RatingController@shopRatingDelete']);
    // $router->post('productrating', ['uses' => 'RatingController@productRatingAdd']);
    // $router->get('productrating/{product_id}',  ['uses' => 'RatingController@productRatingGet']);
    public function testProductRatingGet() {
        $this->call(
            "GET",
            "/api/productrating/123434",
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
            "/api/productrating/{$product->id}",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->delete('productrating/{product_id}', ['uses' => 'RatingController@productRatingDelete']);
}

