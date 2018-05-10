<?php

namespace App\Service;

use App\Dto\Book;
use Psr\Log\LoggerInterface;

class BookService {

    private $logger;
    private $books = [];

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function getAll() {
        return array_values($this->books);
    }

    public function getByTitle($title) {
        $result = [];
        foreach(array_values($this->books) as &$book) {
            if (strpos(strtoupper($book->getTitle()), strtoupper($title)) !== false) {
                array_push($result, $book);
            }
        }
        return $result;
    }

    public function add(Book $book) {
        $this->books[$book->getIsbn()] = $book;
    }
}

?>