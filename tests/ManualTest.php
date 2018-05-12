<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Unirest\Request\Body;
use Unirest\Request;
use Unirest\Response;

/**
  Tests for the REST API.
*/
class ManualTest extends TestCase {

    const BOOKS_URL = 'http://localhost:8000/api/v1/books';

    public function testAddBook() {
        $headers = array('Content-Type' => 'application/json');
        $isbn = "978-1-56619-666-5";
        $data = array(
            "isbn" => $isbn,
            "title" => "I Was Told There'd Be Cake 2",
            "addedOn" => "2000-01-10T08:41:33+00:00"
        );
        $body = Body::json($data);
        $response = Request::post(ApiClientTest::BOOKS_URL . "/" . $isbn, $headers, $body);
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_CREATED, $response->code);
    }
}

?>
