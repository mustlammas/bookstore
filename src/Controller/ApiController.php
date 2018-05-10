<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Datetime;
use App\Dto\Book;
use App\InMemoryBookStore;
use App\Service\JsonSerializer;

class ApiController extends Controller
{
    private $serializer;
    private $books;

    public function __construct(JsonSerializer $serializer) {
        $this->serializer = $serializer;
        $this->books = new InMemoryBookStore();

        $this->books->add(new Book("978-1-56619-909-4", "I Was Told There'd Be Cake", new DateTime()));
        $this->books->add(new Book("1-86092-022-5", "A Boy at Seven", new DateTime()));
        $this->books->add(new Book("1-86092-010-1", "The Higgler", new DateTime()));
    }

    public function list()
    {
         $json = $this->serializer->serialize($this->books->getAll());
         return JsonResponse::fromJsonString($json);
    }

    public function add($json)
    {
        $book = $this->serializer->deserialize($json, Book::class);
        $this->books->add($book);
    }
}

?>