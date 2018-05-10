<?php

namespace App\Controller;

use \Datetime;
use App\Dto\Book;
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

class ApiController extends Controller
{
    private $serializer;
    private $books;
    private $logger;

    public function __construct(BookService $bookService, JsonSerializer $serializer, LoggerInterface $logger) {
        $this->books = $bookService;
        $this->serializer = $serializer;
        $this->logger = $logger;

        $this->books->add(new Book("978-1-56619-909-4", "I Was Told There'd Be Cake", new DateTime()));
        $this->books->add(new Book("1-86092-022-5", "A Boy at Seven", new DateTime()));
        $this->books->add(new Book("1-86092-010-1", "The Higgler", new DateTime()));
    }

    /**
     * @Route("/api/v1/books")
     * @Method("GET")
     */
    public function list(Request $request) {
         $title = $request->query->get('title');
         if (!empty($title)) {
             $this->logger->info("Searching for books by title: " . $title);
             $json = $this->serializer->serialize($this->books->getAll());
             return JsonResponse::fromJsonString($json);
         } else {
             $json = $this->serializer->serialize($this->books->getAll());
             return JsonResponse::fromJsonString($json);
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
            $this->books->add($book);
            $response->setStatusCode(Response::HTTP_CREATED);
        } else {
            $response->setStatusCode(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        return $response;
    }
}

?>