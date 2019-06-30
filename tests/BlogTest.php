
<?php

use Illuminate\Http\Request;

class BlogTest extends TestCase
{
    // $router->get('blog',  ['uses' => 'BlogController@blogList']);
    public function testBlogList() {
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
        $this->assertResponseStatus(200);

        $this->seeJsonStructure([
            [
                'id',
                'title',
                'title_en',
                'title_tc',
                'title_sc',
                'content',
                'content_en',
                'content_tc',
                'content_sc',
                'is_top',
                'shop_id',
                'date_publish_start',
                'date_publish_end',
                'created_at',
                'category' => [
                    'id',
                    'name',
                ],
                'status',
                'image',
                'views',
                'likes',
                'comments',
                'total_records',
            ]
        ]);
    }

    // $router->post('blog', ['uses' => 'BlogController@blogCreate']);
    // $router->get('blog/{id}', ['uses' => 'BlogController@blogGet']);
    public function testBlogGet() {
        $this->call(
            "GET",
            "/api/blog/123434",
            [],
            [],
            [],
            [
                'HTTP_token' => null,
            ],
            ""
        );
        $this->assertResponseStatus(200);

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
            "/api/blog/{$blog->id}",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }

    // $router->delete('blog/{id}', ['uses' => 'BlogController@blogDelete']);
    // $router->patch('blog/{id}', ['uses' => 'BlogController@blogModify']);

    // /*

    // blogList

    // token -> shop_id -> category_id

    // token
    // - null
    // - valid
    // - invalid
    // shop_id
    // - null
    // - valid
    // - invalid
    // category_id
    // - null
    // - valid
    // - invalid
    // title_en
    // - null
    // - any
    // page_number
    // - null
    // - any
    // page_size
    // - null
    // - any

    // token     shop_id   category_id   title_en   page_number   page_size   expected
    // invalid                                                                400
    // null      invalid                                                      400
    // valid     invalid                                                      400
    // null      null      invalid                                            400
    // valid     null      invalid                                            400
    // null      valid     invalid                                            400
    // valid     valid     invalid                                            400

    // */
}

