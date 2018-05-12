<?php

namespace App\Service;

use App\Dto\Book;
use Psr\Log\LoggerInterface;

use \DateTime;
use \PDO;
use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

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
      $rows = $queryBuilder
          ->select('b.isbn', 'b.title', 'b.added_on', 'l.value AS label')
          ->from('books', 'b')
          ->leftJoin('b', 'book_to_label', 'bl', 'bl.isbn = b.isbn')
          ->leftJoin('bl', 'labels', 'l', 'l.id = bl.label_id')
          ->where($queryBuilder->expr()->eq('b.isbn', '?'))
          ->setParameter(0, $isbn)
          ->execute()
          ->fetchAll();

      $book = NULL;
      foreach($rows as $row) {
        if ($book === NULL) {
          $book = new Book(
            $row['isbn'],
            $row['title'],
            $row['added_on'],
            [$row['label']]
          );
        } else {
          $book->addLabel($row['label']);
        }
      }

      return $book;
    }

    public function search($title, $isbn) {
        $this->logger->info("Searching for books: isbn = '{$isbn}', title = '{$title}'" );

        $queryBuilder = $this->connection->createQueryBuilder();
        $rows = $queryBuilder
            ->select('b.isbn', 'b.title', 'b.added_on', 'l.value AS label')
            ->from('books', 'b')
            ->leftJoin('b', 'book_to_label', 'bl', 'bl.isbn = b.isbn')
            ->leftJoin('bl', 'labels', 'l', 'l.id = bl.label_id')
            ->orderBy('b.isbn')
            ->where($queryBuilder->expr()->like('b.isbn', '?'))
            ->setParameter(0, '%' . $isbn . '%')
            ->execute()
            ->fetchAll();

        $books = [];
        foreach($rows as $row) {
          $book;
          if (isset($books[$row['isbn']])) {
            $book = $books[$row['isbn']];
          } else {
            $book = new Book(
              $row['isbn'],
              $row['title'],
              $row['added_on'],
              [$row['label']]
            );
            $books[$row['isbn']] = $book;
          }

          $book->addLabel($row['label']);
        }

        return $books;
    }

    public function add(Book $book) {
      $isbn = $book->getIsbn();
      $this->logger->info("Adding book: isbn = {$isbn}");

      $existing = $this->getByIsbn($isbn);
      if ($existing) {
        throw new ConflictHttpException();
      } else {
        $this->connection->beginTransaction();
        try{
          $this->connection->insert('books', [
            'isbn' => $isbn,
            'title' => $book->getTitle(),
            'added_on' => new DateTime()
          ], [
              PDO::PARAM_STR,
              PDO::PARAM_STR,
              'datetime',
          ]);

          foreach($book->getLabels() as $label) {
            $this->addLabel($isbn, $label);
          }

          $this->connection->commit();
        } catch (\Exception $e) {
          $this->connection->rollBack();
          throw $e;
        }
      }
    }

    public function addLabel($isbn, $label) {
      $this->connection->beginTransaction();
      try{
          $labelId = $this->getLabelId($label);
          if (!$labelId) {
            $this->connection->insert('labels', [
              'value' => $label
            ], [
                PDO::PARAM_STR
            ]);

            $labelId = $this->connection->lastInsertId();
          }

          $bookToLabel = $this->getBookToLabel($isbn, $labelId);

          if (!$bookToLabel) {
            $this->connection->insert('book_to_label', [
              'isbn' => $isbn,
              'label_id' => $labelId
            ], [
                PDO::PARAM_STR,
                PDO::PARAM_INT
            ]);
          }

          $this->connection->commit();
      } catch (\Exception $e) {
          $this->connection->rollBack();
          throw $e;
      }
    }

    function getLabelId($label) {
      $queryBuilder = $this->connection->createQueryBuilder();
      return $queryBuilder
          ->select('id')
          ->from('labels')
          ->where($queryBuilder->expr()->eq('value', '?'))
          ->setParameter(0, $label)
          ->execute()
          ->fetch()['id'];
    }

    function getBookToLabel($isbn, $labelId) {
      $queryBuilder = $this->connection->createQueryBuilder();
      return $queryBuilder
          ->select('isbn')
          ->from('book_to_label')
          ->where(
            $queryBuilder->expr()->andX(
              $queryBuilder->expr()->eq('isbn', '?'),
              $queryBuilder->expr()->eq('label_id', '?')
            )
          )
          ->setParameter(0, $isbn)
          ->setParameter(1, $labelId)
          ->execute()
          ->fetch()['isbn'];
    }
}

?>
