<?php

class LikeTest extends TestCase
{
    // $router->post('bloglike', ['uses' => 'LikeController@blogLikeAdd']);
    // $router->get('bloglike/{blog_id}',  ['uses' => 'LikeController@blogLikeGet']);
    public function testBlogLikeGet() {
        $this->call(
            "GET",
            "/api/bloglike/123434",
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
            "/api/bloglike/{$blog->id}",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->delete('bloglike/{blog_id}', ['uses' => 'LikeController@blogLikeDelete']);
}

