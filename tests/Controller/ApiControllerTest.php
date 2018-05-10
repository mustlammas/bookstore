<?php

namespace App\Tests\Controller;

use App\Controller\ApiController;
use App\Service\JsonSerializer;

use PHPUnit\Framework\TestCase;

class ApiControllerTest extends TestCase
{
    public function testAddBook()
    {
        $isbn = "978-1-56619-666-5";
        $serializer = new JsonSerializer();
        $controller = new ApiController($serializer);
        $json = "{
            \"isbn\":\"{$isbn}\",
            \"title\":\"I Was Told There'd Be Cake 2\",
            \"addedOn\":\"2000-01-10T08:41:33+00:00\"}";

        $controller->add($json);
        $booksJson = $controller->list()->getContent();
        $books = $serializer->deserialize($booksJson, 'App\Dto\Book[]');
        $book = $books[count($books) - 1];

        $this->assertEquals($isbn, $book->getIsbn());
    }
}

?>