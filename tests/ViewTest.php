<?php

use App\Product;
use App\Blog;

class ViewTest extends TestCase
{
    // $router->post('productview', ['uses' => 'ViewController@productViewAdd']);
    // $router->get('productview/{product_id}',  ['uses' => 'ViewController@productViewGet']);
    // $router->post('blogview', ['uses' => 'ViewController@blogViewAdd']);
    // $router->get('blogview/{blog_id}',  ['uses' => 'ViewController@blogViewGet']);

    // public function testShouldAddProductView() {
    //     $parameters = [
    //         'product_id' => 10000001,
    //     ];
    //     $this->post("/api/productview", $parameters, []);
    //     $this->seeStatusCode(201);
    //     $this->seeJsonStructure([
            
    //     ]);
    // }

    // public function testShouldNotAddProductViewIfProductIdInvalid() {
    //     $this->post("/api/productview/", [
    //         'product_id' => 434,
    //     ], []);
    //     $this->seeStatusCode(400);
    //     $this->seeJsonStructure([

    //     ]);
    // }

    public function testShouldGetProductView() {
        $product = Product::whereNull('deleted_at')->inRandomOrder()->first();
        $this->get("/api/productview/{$product->id}", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'count',
        ]);
    }

    public function testShouldNotGetProductViewIfInvalidProductId() {
        $invalidId = null;
        $isIdInvalid = false;
        while ($isIdInvalid == false) {
            $invalidId = rand(10000000, 99999999);
            $product = Product::where('id', $invalidId)->whereNull('deleted_at')->inRandomOrder()->first();
            if (empty($product)) {
                $isIdInvalid = true;
            }
        }
        $this->get("/api/productview/{$invalidId}", []);
        $this->seeStatusCode(400);
        $this->seeJsonStructure([
            'success',
            'message',
        ]);
    }

    public function testShouldGetBlogView() {
        $blog = Blog::whereNull('deleted_at')->inRandomOrder()->first();
        $this->get("/api/blogview/{$blog->id}", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'count',
        ]);
    }

    public function testShouldNotGetBlogViewIfInvalidId() {
        $invalidId = null;
        $isIdInvalid = false;
        while ($isIdInvalid == false) {
            $invalidId = rand(10000000, 99999999);
            $blog = Blog::where('id', $invalidId)->whereNull('deleted_at')->inRandomOrder()->first();
            if (empty($blog)) {
                $isIdInvalid = true;
            }
        }
        $this->get("/api/blogview/{$invalidId}", []);
        $this->seeStatusCode(400);
        $this->seeJsonStructure([
            'success',
            'message',
        ]);
    }
}

