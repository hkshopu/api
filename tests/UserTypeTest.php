
<?php

class UserTypeTest extends TestCase
{
    // $router->get('usertype',  ['uses' => 'UserTypeController@userTypeList']);

    public function testShouldListUserType() {
        $this->get("/api/usertype", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([

        ]);
    }
}

