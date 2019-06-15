
<?php

class ColorTest extends TestCase
{
    // $router->get('color',  ['uses' => 'ColorController@colorList']);
    public function testColorList() {
        $this->call(
            "GET",
            "/api/color",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }
}

