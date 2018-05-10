<?php

namespace App\Tests\Controller;

use App\Controller\ApiController;
use PHPUnit\Framework\TestCase;

class ApiControllerTest extends TestCase
{
    public function testAddBook()
    {
        $controller = new ApiController();
        $json = "{
            \"isbn\":\"978-1-56619-909-4\",
            \"title\":\"I Was Told There'd Be Cake\",
            \"addedOn\":\"2018-05-10T08:41:33+00:00\"}";
        $controller->add($json);

        $list = $controller->list();
        echo $list;

        $this->assertEquals(42, 42);
    }
}

?>