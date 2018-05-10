<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\TestCase;
use Unirest\Request\Body;
use Unirest\Request;

class ApiClientTest extends TestCase
{
    public function testAddBook()
    {
        $headers = array('Content-Type' => 'application/json');
        $isbn = "978-1-56619-666-5";
        $data = array(
            "isbn" => $isbn,
            "title" => "I Was Told There'd Be Cake 2",
            "addedOn" => "2000-01-10T08:41:33+00:00"
        );
        $body = Body::json($data);
        $response = Request::post('http://localhost:8000/api/v1/books/' . $isbn, $headers, $body);
        $this->assertEquals(201, $response->code);
    }

    public function testAddBookInvalidContentType()
    {
        $headers = array('Content-Type' => 'application/xml');
        $isbn = "978-1-56619-666-5";
        $data = array(
            "isbn" => $isbn,
            "title" => "I Was Told There'd Be Cake 2",
            "addedOn" => "2000-01-10T08:41:33+00:00"
        );
        $body = Body::json($data);
        $response = Request::post('http://localhost:8000/api/v1/books/' . $isbn, $headers, $body);
        $this->assertEquals(Response::HTTP_UNSUPPORTED_MEDIA_TYPE, $response->code);
    }

    public function testAddBookInvalidContent()
    {
        $headers = array('Content-Type' => 'application/json');
        $isbn = "978-1-56619-666-5";
        $data = array(
            "a" => $isbn,
            "b" => "I Was Told There'd Be Cake 2",
            "c" => "2000-01-10T08:41:33+00:00"
        );
        $body = Body::json($data);
        $response = Request::post('http://localhost:8000/api/v1/books/' . $isbn, $headers, $body);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->code);
    }
}

?>