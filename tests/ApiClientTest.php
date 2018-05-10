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
        $data = array(
            "isbn" => "978-1-56619-666-5",
            "title" => "I Was Told There'd Be Cake 2",
            "addedOn" => "2000-01-10T08:41:33+00:00"
        );
        $body = Body::json($data);
        $response = Request::post('http://localhost:8000/api/v1/books', $headers, $body);
        echo "\nHTTP Response code: " . $response->code;
    }
}

?>