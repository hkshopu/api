<?php

class FollowingTest extends TestCase
{
    // $router->post('productfollowing', ['uses' => 'FollowingController@productFollowingAdd']);
    // $router->get('productfollowing/{product_id}',  ['uses' => 'FollowingController@productFollowingGet']);
    // $router->delete('productfollowing/{product_id}', ['uses' => 'FollowingController@productFollowingDelete']);
    // $router->post('imagefollowing', ['uses' => 'FollowingController@imageFollowingAdd']);
    // $router->get('imagefollowing/{image_id}',  ['uses' => 'FollowingController@imageFollowingGet']);
    // $router->delete('imagefollowing/{image_id}', ['uses' => 'FollowingController@imageFollowingDelete']);
    // $router->post('shopfollowing', ['uses' => 'FollowingController@shopFollowingAdd']);
    // $router->get('shopfollowing/{shop_id}',  ['uses' => 'FollowingController@shopFollowingGet']);
    // $router->delete('shopfollowing/{shop_id}', ['uses' => 'FollowingController@shopFollowingDelete']);

    public function testShouldGetProductFollowing() {
        $this->get("/api/productfollowing/10000001", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }

    public function testShouldGetProductFollowingIfProductIdInvalid() {
        $this->get("/api/productfollowing/434", []);
        $this->seeStatusCode(400);
        $this->seeJsonStructure([

        ]);
    }

    public function testShouldGetImageFollowing() {
        $this->get("/api/imagefollowing/10000001", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }

    public function testShouldGetImageFollowingIfImageIdInvalid() {
        $this->get("/api/imagefollowing/434", []);
        $this->seeStatusCode(400);
        $this->seeJsonStructure([

        ]);
    }

    public function testShouldGetShopFollowing() {
        $this->get("/api/shopfollowing/10000001", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }

    public function testShouldGetShopFollowingIfShopIdInvalid() {
        $this->get("/api/shopfollowing/434", []);
        $this->seeStatusCode(400);
        $this->seeJsonStructure([

        ]);
    }
}

