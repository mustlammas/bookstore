<?php

namespace App\Controller;

use \Datetime;
use App\Dto\Book;
use App\InMemoryBookStore;
use App\Service\JsonSerializer;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiController extends Controller
{
    private $serializer;
    private $books;
    private $logger;

    public function __construct(JsonSerializer $serializer, LoggerInterface $logger) {
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->books = new InMemoryBookStore();

        $this->books->add(new Book("978-1-56619-909-4", "I Was Told There'd Be Cake", new DateTime()));
        $this->books->add(new Book("1-86092-022-5", "A Boy at Seven", new DateTime()));
        $this->books->add(new Book("1-86092-010-1", "The Higgler", new DateTime()));
    }

    /**
     * @Route("/api/v1/books")
     * @Method("GET")
     */
    public function list() {
         $json = $this->serializer->serialize($this->books->getAll());
         return JsonResponse::fromJsonString($json);
    }

    /**
     * @Route("/api/v1/books/{isbn}")
     * @Method("POST")
     */
    public function add($isbn, Request $request) {
        $contentType = $request->headers->get('Content-Type');

        $response =  new Response();
        if ($contentType == 'application/json') {
            $content = $request->getContent();
            $this->logger->info("Adding book: " . $isbn);
            $book = $this->serializer->deserialize($content, Book::class);
            $this->books->add($book);
            $response->setStatusCode(Response::HTTP_CREATED);
        } else {
            $response->setStatusCode(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        return $response;
    }
}

?>