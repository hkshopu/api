
<?php

class LanguageTest extends TestCase
{
    // $router->get('language',  ['uses' => 'LanguageController@languageList']);
    public function testLanguageList() {
        $this->call(
            "GET",
            "/api/language",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }
}

