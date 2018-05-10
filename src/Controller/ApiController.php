<?php

namespace App\Controller;

use \Datetime;
use App\Dto\Book;
use App\Dto\Label;
use App\Service\BookService;
use App\Service\JsonSerializer;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiController extends Controller {
    private $serializer;
    private $bookService;
    private $logger;

    public function __construct(BookService $bookService, JsonSerializer $serializer, LoggerInterface $logger) {
        $this->bookService = $bookService;
        $this->serializer = $serializer;
        $this->logger = $logger;

        $this->bookService->add(new Book(
            "978-1-56619-909-4",
            "I Was Told There'd Be Cake",
            new DateTime(),
            [new Label(1, "Fiction")]));

        $this->bookService->add(new Book(
            "1-86092-022-5",
            "A Boy at Seven",
             new DateTime(),
             [new Label(2, "Science Fiction")]));

        $this->bookService->add(new Book(
            "1-86092-010-1",
            "The Higgler",
            new DateTime(),
            [new Label(3, "Biography")]));
    }

    /**
     * @Route("/api/v1/books")
     * @Method("GET")
     */
    public function list(Request $request) {
         $title = $request->query->get('title');
         $isbn = $request->query->get('isbn');
         $json = $this->serializer->serialize($this->bookService->search($title, $isbn));
         return JsonResponse::fromJsonString($json);
    }

    /**
     * @Route("/api/v1/books/{isbn}")
     * @Method("GET")
     */
    public function getByIsbn($isbn) {
        $book = $this->bookService->getByIsbn($isbn);
        if ($book) {
            $json = $this->serializer->serialize($book);
            return JsonResponse::fromJsonString($json);
        } else {
            $response =  new Response();
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        }
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
            $this->bookService->add($book);
            $response->setStatusCode(Response::HTTP_CREATED);
        } else {
            $response->setStatusCode(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        return $response;
    }
}

?>