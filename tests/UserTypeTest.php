
<?php

class UserTypeTest extends TestCase
{
    // $router->get('usertype',  ['uses' => 'UserTypeController@userTypeList']);
    public function testUserTypeList() {
        $this->call(
            "GET",
            "/api/usertype",
            [],
            [],
            [],
            [],
            ""
        );
        $this->assertResponseStatus(200);
    }
}

