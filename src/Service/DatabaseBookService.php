<?php

namespace App\Service;

use App\Dto\Book;
use Psr\Log\LoggerInterface;

use Doctrine\DBAL\Driver\Connection;

class DatabaseBookService implements BookService {

    private $logger;
    private $connection;
    private $books = [];

    public function __construct(LoggerInterface $logger, Connection $connection) {
        $this->logger = $logger;
        $this->connection = $connection;
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
        $this->logger->info("Searching for books");
        $result = [];
        $books = $this->connection->fetchAll('SELECT isbn, title, added_on FROM books');

        foreach($books as $book) {
          $isbn = $book->isbn;
          $this->logger->info("ISBN: " . $isbn);
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
