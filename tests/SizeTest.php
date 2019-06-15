
<?php

class SizeTest extends TestCase
{
    // $router->get('size',  ['uses' => 'SizeController@sizeList']);
    public function testSizeList() {
        $this->call(
            "GET",
            "/api/size",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }
}

