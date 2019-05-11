<?php

use App\Product;
use App\Blog;
use Illuminate\Http\Request;

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

    // public function testShouldGetProductView(Request $request) {
    //     $isProductActive = false;

    //     // $request = new Request();
    //     // $request->request->add([
    //     //     'filter_inactive' => true,
    //     //     'language' => 'en',
    //     // ]);

    //     while ($isProductActive == false) {
    //         $product = Product::whereNull('deleted_at')->inRandomOrder()->first();
    //         $shop = app('App\Http\Controllers\ShopController')->shopGet($product->shop_id, $request)->getData();
    //         if (!empty($shop->id)) {
    //             $isProductActive = true;
    //         }
    //     }

    //     var_dump($request);exit;

    //     $this->get("/api/productview/{$product->id}", []);
    //     $this->seeStatusCode(200);
    //     $this->seeJsonStructure([
    //         'count',
    //     ]);
    // }

    public function testShouldNotGetProductViewIfInvalidProductId() {
        $invalidId = null;
        $isIdInvalid = false;
        while ($isIdInvalid == false) {
            $invalidId = rand(10000000, 99999999);
            $productQuery = \DB::table('product')
                ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
                ->leftJoin('user', 'user.id', '=', 'shop.user_id')
                ->select('product.*')
                ->where('product.id', $invalidId)
                ->whereNull('product.deleted_at');

            // if ($request->filter_inactive == true) {
                $productQuery
                    ->whereNull('shop.deleted_at')
                    ->whereNull('user.deleted_at');
            // }

            $product = $productQuery->inRandomOrder()->first();

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
        $blogQuery = \DB::table('blog')
            ->leftJoin('shop', 'shop.id', '=', 'blog.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('blog.*')
            ->whereNull('blog.deleted_at');

        // if ($request->filter_inactive == true) {
            $blogQuery
                ->whereNull('shop.deleted_at')
                ->whereNull('user.deleted_at');
        // }

        $blog = $blogQuery->inRandomOrder()->first();

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
            $blogQuery = \DB::table('blog')
                ->leftJoin('shop', 'shop.id', '=', 'blog.shop_id')
                ->leftJoin('user', 'user.id', '=', 'shop.user_id')
                ->select('blog.*')
                ->where('blog.id', $invalidId)
                ->whereNull('blog.deleted_at');

            // if ($request->filter_inactive == true) {
                $blogQuery
                    ->whereNull('shop.deleted_at')
                    ->whereNull('user.deleted_at');
            // }

            $blog = $blogQuery->inRandomOrder()->first();

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

