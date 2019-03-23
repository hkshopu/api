
<?php

class CategoryTest extends TestCase
{
    // $router->get('productcategory',  ['uses' => 'CategoryController@productCategoryList']);
    // $router->post('productcategory',  ['uses' => 'CategoryController@productCategoryAdd']);
    // $router->get('productcategory/{id}',  ['uses' => 'CategoryController@productCategoryGet']);
    // $router->patch('productcategory/{id}',  ['uses' => 'CategoryController@productCategoryModify']);
    // $router->delete('productcategory/{id}',  ['uses' => 'CategoryController@productCategoryDelete']);
    // $router->get('productcategoryparent/{id}',  ['uses' => 'CategoryController@productCategoryParentGet']);
    // $router->get('shopcategory',  ['uses' => 'CategoryController@shopCategoryList']);
    // $router->post('shopcategory',  ['uses' => 'CategoryController@shopCategoryAdd']);
    // $router->get('shopcategory/{id}',  ['uses' => 'CategoryController@shopCategoryGet']);
    // $router->patch('shopcategory/{id}',  ['uses' => 'CategoryController@shopCategoryModify']);
    // $router->delete('shopcategory/{id}',  ['uses' => 'CategoryController@shopCategoryDelete']);
    // $router->get('blogcategory',  ['uses' => 'CategoryController@blogCategoryList']);
    // $router->post('blogcategory',  ['uses' => 'CategoryController@blogCategoryAdd']);
    // $router->get('blogcategory/{id}',  ['uses' => 'CategoryController@blogCategoryGet']);
    // $router->patch('blogcategory/{id}',  ['uses' => 'CategoryController@blogCategoryModify']);
    // $router->delete('blogcategory/{id}',  ['uses' => 'CategoryController@blogCategoryDelete']);

    public function testShouldListProductCategory() {
        $this->get("/api/productcategory", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }

    public function testShouldGetProductCategory() {
        $this->get("/api/productcategory/10000001", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }

    public function testShouldGetProductCategoryParent() {
        $this->get("/api/productcategoryparent/10000001", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }

    public function testShouldListShopCategory() {
        $this->get("/api/shopcategory", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }

    public function testShouldGetShopCategory() {
        $this->get("/api/shopcategory/10000014", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }

    public function testShouldListBlogCategory() {
        $this->get("/api/blogcategory", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }

    public function testShouldGetBlogCategory() {
        $this->get("/api/blogcategory/10000021", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }
}

