
<?php

class SizeTest extends TestCase
{
    // $router->get('size',  ['uses' => 'SizeController@sizeList']);

    public function testShouldListSize() {
        $this->get("/api/size", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }
}

