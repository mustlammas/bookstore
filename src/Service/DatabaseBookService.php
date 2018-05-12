<?php

namespace App\Service;

use App\Dto\Book;
use Psr\Log\LoggerInterface;

use \DateTime;
use \PDO;
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
    }

    //TODO: use mysql_real_escape_string()
    public function search($title, $isbn) {
        $this->logger->info("Searching for books: isbn = '{$isbn}', title = '{$title}'" );

        $queryBuilder = $this->connection->createQueryBuilder();
        $result = $queryBuilder
            ->select('isbn', 'title', 'added_on')
            ->from('books')
            ->where(
              $queryBuilder->expr()->andX(
                $queryBuilder->expr()->like('isbn', '?'),
                $queryBuilder->expr()->like('title', '?')
              )
            )
            ->setParameter(0, '%' . $isbn . '%')
            ->setParameter(1, '%' . $title . '%')
            ->execute();

        return $result;
    }

    public function add(Book $book) {
      $this->connection->insert('books', [
        'isbn' => $book->getIsbn(),
        'title' => $book->getTitle(),
        'added_on' => new DateTime()
      ], [
          PDO::PARAM_STR,
          PDO::PARAM_STR,
          'datetime',
      ]);
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
