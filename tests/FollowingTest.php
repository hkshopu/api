<?php

use App\Image;

class FollowingTest extends TestCase
{
    // $router->post('productfollowing', ['uses' => 'FollowingController@productFollowingAdd']);
    // $router->get('productfollowing/{product_id}',  ['uses' => 'FollowingController@productFollowingGet']);
    public function testProductFollowingGet() {
        $this->call(
            "GET",
            "/api/productfollowing/123434",
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
            "/api/productfollowing/{$product->id}",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->delete('productfollowing/{product_id}', ['uses' => 'FollowingController@productFollowingDelete']);
    // $router->post('imagefollowing', ['uses' => 'FollowingController@imageFollowingAdd']);
    // $router->get('imagefollowing/{image_id}',  ['uses' => 'FollowingController@imageFollowingGet']);
    public function testImageFollowingGet() {
        $this->call(
            "GET",
            "/api/imagefollowing/123434",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(400);

        $image = Image::whereNull('deleted_at')->inRandomOrder()->first();

        $this->call(
            "GET",
            "/api/imagefollowing/{$image->id}",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->delete('imagefollowing/{image_id}', ['uses' => 'FollowingController@imageFollowingDelete']);
    // $router->post('shopfollowing', ['uses' => 'FollowingController@shopFollowingAdd']);
    // $router->get('shopfollowing/{shop_id}',  ['uses' => 'FollowingController@shopFollowingGet']);
    public function testShopFollowingGet() {
        $this->call(
            "GET",
            "/api/shopfollowing/123434",
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
            "/api/shopfollowing/{$shop->id}",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->delete('shopfollowing/{shop_id}', ['uses' => 'FollowingController@shopFollowingDelete']);
}

