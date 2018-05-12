<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Unirest\Request\Body;
use Unirest\Request;
use Unirest\Response;

/**
  Tests for the REST API.
*/
class ApiClientTest extends TestCase {

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

    public function testListBooks() {
        $response = Request::get(ApiClientTest::BOOKS_URL);
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_OK, $response->code);
    }

    public function testSearchForBooksByTitle() {
        $response = Request::get(ApiClientTest::BOOKS_URL . '?title=cake');
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_OK, $response->code);
        $this->assertEquals("I Was Told There'd Be Cake", $response->body[0]->title);
    }

    public function testSearchForBooksByIsbn() {
        $response = Request::get(ApiClientTest::BOOKS_URL . '?isbn=86092');
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_OK, $response->code);
        $this->assertEquals("1-86092-022-5", $response->body[0]->isbn);
        $this->assertEquals("1-86092-010-1", $response->body[1]->isbn);
    }

    public function testSearchForBooksByIsbnAndTitle() {
        $response = Request::get(ApiClientTest::BOOKS_URL . '?isbn=56619&title=cake');
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_OK, $response->code);
        $this->assertEquals("978-1-56619-909-4", $response->body[0]->isbn);
        $this->assertEquals("I Was Told There'd Be Cake", $response->body[0]->title);
    }

    public function testGetBookByIsbn() {
        $response = Request::get(ApiClientTest::BOOKS_URL . '/978-1-56619-909-4');
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_OK, $response->code);
        $this->assertEquals("978-1-56619-909-4", $response->body->isbn);
        $this->assertEquals("I Was Told There'd Be Cake", $response->body->title);
    }

    public function testGetBookByNonExistentIsbn() {
        $response = Request::get(ApiClientTest::BOOKS_URL . '/978-1-00000-000-4');
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND, $response->code);
    }

    public function testAddBookInvalidContentType() {
        $headers = array('Content-Type' => 'application/xml');
        $isbn = "978-1-56619-666-5";
        $data = array(
            "isbn" => $isbn,
            "title" => "I Was Told There'd Be Cake 2",
            "addedOn" => "2000-01-10T08:41:33+00:00"
        );
        $body = Body::json($data);
        $response = Request::post(ApiClientTest::BOOKS_URL . "/" . $isbn, $headers, $body);
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_UNSUPPORTED_MEDIA_TYPE, $response->code);
    }

    public function testAddBookInvalidContent() {
        $headers = array('Content-Type' => 'application/json');
        $isbn = "978-1-56619-666-5";
        $data = array(
            "a" => $isbn,
            "b" => "I Was Told There'd Be Cake 2",
            "c" => "2000-01-10T08:41:33+00:00"
        );
        $body = Body::json($data);
        $response = Request::post(ApiClientTest::BOOKS_URL . "/" . $isbn, $headers, $body);
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $response->code);
    }

    public function testAddLabelToBook() {
        $response = Request::post(ApiClientTest::BOOKS_URL . '/978-1-56619-909-4/labels/new-label');
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_CREATED, $response->code);
    }
}

?>
