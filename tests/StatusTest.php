<?php

class StatusTest extends TestCase
{
    // $router->get('categorystatus',  ['uses' => 'StatusController@categoryStatusList']);
    // $router->get('productstatus',  ['uses' => 'StatusController@productStatusList']);
    // $router->get('shopstatus',  ['uses' => 'StatusController@shopStatusList']);
    // $router->get('commentstatus',  ['uses' => 'StatusController@commentStatusList']);
    // $router->get('blogstatus',  ['uses' => 'StatusController@blogStatusList']);
    // $router->get('userstatus',  ['uses' => 'StatusController@userStatusList']);

    public function testShouldListCategoryStatus() {
        $this->get("/api/categorystatus", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }

    public function testShouldListProductStatus() {
        $this->get("/api/productstatus", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }

    public function testShouldListShopStatus() {
        $this->get("/api/shopstatus", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }

    public function testShouldListCommentStatus() {
        $this->get("/api/commentstatus", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }

    public function testShouldListBlogStatus() {
        $this->get("/api/blogstatus", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }

    public function testShouldListUserStatus() {
        $this->get("/api/userstatus", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }
}

