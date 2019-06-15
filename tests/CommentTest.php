
<?php

class CommentTest extends TestCase
{
    // $router->post('shopcomment', ['uses' => 'CommentController@shopCommentAdd']);
    // $router->get('shopcomment/{shop_id}',  ['uses' => 'CommentController@shopCommentGet']);
    public function testShopCommentGet() {
        $this->call(
            "GET",
            "/api/shopcomment/123434",
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
            "/api/shopcomment/{$shop->id}",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->delete('shopcomment/{id}', ['uses' => 'CommentController@shopCommentDelete']);
    // $router->patch('shopcommentenable/{id}', ['uses' => 'CommentController@shopCommentEnable']);
    // $router->patch('shopcommentdisable/{id}', ['uses' => 'CommentController@shopCommentDisable']);
    // $router->post('blogcomment', ['uses' => 'CommentController@blogCommentAdd']);
    // $router->get('blogcomment/{blog_id}',  ['uses' => 'CommentController@blogCommentGet']);
    public function testBlogCommentGet() {
        $this->call(
            "GET",
            "/api/blogcomment/123434",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(400);

        $this->call(
            "GET",
            "/api/blog",
            [
                'shop_id' => null,
                'category_id' => null,
                'title_en' => null,
                'page_number' => null,
                'page_size' => null,
            ],
            [],
            [],
            [
                'HTTP_token' => null,
            ],
            ""
        );

        $blogList = json_decode($this->response->content());
        $blog = $blogList[array_rand($blogList)];

        $this->call(
            "GET",
            "/api/blogcomment/{$blog->id}",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->delete('blogcomment/{id}', ['uses' => 'CommentController@blogCommentDelete']);
    // $router->patch('blogcommentenable/{id}', ['uses' => 'CommentController@blogCommentEnable']);
    // $router->patch('blogcommentdisable/{id}', ['uses' => 'CommentController@blogCommentDisable']);
}


