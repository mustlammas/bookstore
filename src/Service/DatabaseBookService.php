<?php

namespace App\Service;

use App\Dto\Book;
use Psr\Log\LoggerInterface;

use \DateTime;
use \PDO;
use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

// TODO: move database access to a separate service class
class DatabaseBookService implements BookService {

    private $logger;
    private $connection;
    private $books = [];

    public function __construct(LoggerInterface $logger, Connection $connection) {
        $this->logger = $logger;
        $this->connection = $connection;
    }

    public function getByIsbn($isbn) {
      $this->logger->info("Get by ISBN: isbn = '{$isbn}'" );

      $queryBuilder = $this->connection->createQueryBuilder();
      $result = $queryBuilder
          ->select('isbn', 'title', 'added_on')
          ->from('books')
          ->where($queryBuilder->expr()->eq('isbn', '?'))
          ->setParameter(0, $isbn)
          ->execute();

      return $result->fetch();
    }

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

        return $result->fetchAll();
    }

    public function add(Book $book) {
      $isbn = $book->getIsbn();
      $this->logger->info("Adding book: isbn = {$isbn}");

      $existing = $this->getByIsbn($isbn);
      if ($existing) {
        throw new ConflictHttpException();
      } else {
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
