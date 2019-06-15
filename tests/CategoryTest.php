
<?php

use App\Entity;
use App\Category;
use App\Product;
use App\Shop;
use App\Blog;

class CategoryTest extends TestCase
{
    // $router->get('productcategory',  ['uses' => 'CategoryController@productCategoryList']);
    public function testProductCategoryList() {
        $this->call(
            "GET",
            "/api/productcategory",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->post('productcategory',  ['uses' => 'CategoryController@productCategoryAdd']);
    // $router->get('productcategory/{id}',  ['uses' => 'CategoryController@productCategoryGet']);
    public function testProductCategoryGet() {
        $this->call(
            "GET",
            "/api/productcategory/123434",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(400);

        $product = new Product();

        $productEntity = Entity::where('name', $product->getTable())->first();
        $productCategory = Category::where('entity', $productEntity->id)->whereNull('deleted_at')->inRandomOrder()->first();

        $this->call(
            "GET",
            "/api/productcategory/{$productCategory->id}",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->patch('productcategory/{id}',  ['uses' => 'CategoryController@productCategoryModify']);
    // $router->delete('productcategory/{id}',  ['uses' => 'CategoryController@productCategoryDelete']);
    // $router->get('productcategoryparent/{id}',  ['uses' => 'CategoryController@productCategoryParentGet']);
    public function testProductCategoryParentGet() {
        $this->call(
            "GET",
            "/api/productcategoryparent/123434",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(400);

        $product = new Product();

        $productEntity = Entity::where('name', $product->getTable())->first();
        $productCategory = Category::where('entity', $productEntity->id)->whereNull('deleted_at')->inRandomOrder()->first();

        $this->call(
            "GET",
            "/api/productcategoryparent/{$productCategory->id}",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->get('shopcategory',  ['uses' => 'CategoryController@shopCategoryList']);
    public function testShopCategoryList() {
        $this->call(
            "GET",
            "/api/shopcategory",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->post('shopcategory',  ['uses' => 'CategoryController@shopCategoryAdd']);
    // $router->get('shopcategory/{id}',  ['uses' => 'CategoryController@shopCategoryGet']);
    public function testShopCategoryGet() {
        $this->call(
            "GET",
            "/api/shopcategory/123434",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(400);

        $shop = new Shop();

        $shopEntity = Entity::where('name', $shop->getTable())->first();
        $shopCategory = Category::where('entity', $shopEntity->id)->whereNull('deleted_at')->inRandomOrder()->first();

        $this->call(
            "GET",
            "/api/shopcategory/{$shopCategory->id}",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->patch('shopcategory/{id}',  ['uses' => 'CategoryController@shopCategoryModify']);
    // $router->delete('shopcategory/{id}',  ['uses' => 'CategoryController@shopCategoryDelete']);
    // $router->get('blogcategory',  ['uses' => 'CategoryController@blogCategoryList']);
    public function testBlogCategoryList() {
        $this->call(
            "GET",
            "/api/blogcategory",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->post('blogcategory',  ['uses' => 'CategoryController@blogCategoryAdd']);
    // $router->get('blogcategory/{id}',  ['uses' => 'CategoryController@blogCategoryGet']);
    public function testBlogCategoryGet() {
        $this->call(
            "GET",
            "/api/blogcategory/123434",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(400);

        $blog = new Blog();

        $blogEntity = Entity::where('name', $blog->getTable())->first();
        $blogCategory = Category::where('entity', $blogEntity->id)->whereNull('deleted_at')->inRandomOrder()->first();

        $this->call(
            "GET",
            "/api/blogcategory/{$blogCategory->id}",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->patch('blogcategory/{id}',  ['uses' => 'CategoryController@blogCategoryModify']);
    // $router->delete('blogcategory/{id}',  ['uses' => 'CategoryController@blogCategoryDelete']);
}

