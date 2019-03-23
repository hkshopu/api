
<?php

class ColorTest extends TestCase
{
    // $router->get('color',  ['uses' => 'ColorController@colorList']);

    public function testShouldListColor() {
        $this->get("/api/color", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }
}

