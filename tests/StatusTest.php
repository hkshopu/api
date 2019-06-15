<?php

class StatusTest extends TestCase
{
    // $router->get('categorystatus',  ['uses' => 'StatusController@categoryStatusList']);
    public function testCategoryStatusList() {
        $this->call(
            "GET",
            "/api/categorystatus",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->get('productstatus',  ['uses' => 'StatusController@productStatusList']);
    public function testProductStatusList() {
        $this->call(
            "GET",
            "/api/productstatus",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->get('shopstatus',  ['uses' => 'StatusController@shopStatusList']);
    public function testShopStatusList() {
        $this->call(
            "GET",
            "/api/shopstatus",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->get('commentstatus',  ['uses' => 'StatusController@commentStatusList']);
    public function testCommentStatusList() {
        $this->call(
            "GET",
            "/api/commentstatus",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->get('blogstatus',  ['uses' => 'StatusController@blogStatusList']);
    public function testBlogStatusList() {
        $this->call(
            "GET",
            "/api/blogstatus",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->get('userstatus',  ['uses' => 'StatusController@userStatusList']);
    public function testUserStatusList() {
        $this->call(
            "GET",
            "/api/userstatus",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->get('orderstatus',  ['uses' => 'StatusController@orderStatusList']);
    public function testOrderStatusList() {
        $this->call(
            "GET",
            "/api/orderstatus",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->get('paymentstatus',  ['uses' => 'StatusController@paymentStatusList']);
    public function testPaymentStatusList() {
        $this->call(
            "GET",
            "/api/paymentstatus",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->get('orderitemstatus',  ['uses' => 'StatusController@orderItemStatusList']);
    public function testOrderItemStatusList() {
        $this->call(
            "GET",
            "/api/orderitemstatus",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }
}

