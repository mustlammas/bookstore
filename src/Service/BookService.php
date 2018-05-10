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

    public function getByIsbn($isbn) {
        foreach(array_values($this->books) as &$book) {
            if ($isbn === $book->getIsbn()) {
                return $book;
            }
        }

        return;
    }

    public function search($title, $isbn) {
        $result = [];
        foreach(array_values($this->books) as &$book) {
            $titleMatch = $this->partialMatch($title, $book->getTitle());
            $isbnMatch = $this->partialMatch($isbn, $book->getIsbn());
            if ($titleMatch && $isbnMatch) {
                array_push($result, $book);
            }
        }
        return $result;
    }

    public function add(Book $book) {
        $this->books[$book->getIsbn()] = $book;
    }

    function partialMatch($search, $candidate) {
        if (empty($search)) {
            return true;
        } else {
            return strpos(strtoupper($candidate), strtoupper($search));
        }
    }
}

?>