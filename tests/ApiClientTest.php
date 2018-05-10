<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Unirest\Request\Body;
use Unirest\Request;

class ApiControllerTest extends TestCase
{
    public function test()
    {
        $headers = array('Accept' => 'application/json');
        $isbn = "978-1-56619-666-5";
        $data = array(
            "isbn" => $isbn,
            "title" => "I Was Told There'd Be Cake 2",
            "addedOn" => "2000-01-10T08:41:33+00:00"
        );
        $body = Body::json($data);
        $response = Request::post('http://localhost:8000/api/v1/books/' . $isbn, $headers, $body);
        $this->assertEquals(200, $response->code);
    }
}

?>