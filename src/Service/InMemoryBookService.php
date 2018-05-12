<?php

namespace App\Service;

use App\Dto\Book;
use App\Dto\Label;

use \Datetime;
use Psr\Log\LoggerInterface;

class InMemoryBookService implements BookService {

    private $logger;
    private $books;

    public function __construct(LoggerInterface $logger) {
      $this->logger = $logger;
      $this->books = array(
        "978-1-56619-909-4" => new Book("978-1-56619-909-4", "I Was Told There'd Be Cake", new DateTime(), [new Label(1, "Fiction")]),
        "1-86092-022-5" => new Book("1-86092-022-5", "A Boy at Seven", new DateTime(), [new Label(2, "Science Fiction")]),
        "1-86092-010-1" => new Book("1-86092-010-1", "The Higgler", new DateTime(), [new Label(3, "Biography")])
      );
    }

    public function getByIsbn($isbn) {
        foreach($this->books as $bookIsbn => $book) {
            if ($isbn === $bookIsbn) {
                return $book;
            }
        }

        return;
    }

    public function search($title, $isbn) {
        $result = [];
        $this->logger->info("TESTING::::::::::");
        $this->logger->info(gettype($this->books));


        foreach($this->books as $bookIsbn => $book) {
            $titleMatch = $this->partialMatch($title, $book->getTitle());
            $isbnMatch = $this->partialMatch($isbn, $bookIsbn);
            if ($titleMatch && $isbnMatch) {
                array_push($result, $book);
            }
        }
        return $result;
    }

    public function add(Book $book) {
        $this->books[$book->getIsbn()] = $book;
    }

    public function addLabel($isbn, $label) {
        $book = $this->getByIsbn($isbn);
        $book->addLabel($label);
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
