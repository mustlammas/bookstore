<?php

namespace App\Command;

use App\Dto\Book;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Unirest\Request\Body;
use Unirest\Request;
use Unirest\Response;
use \DateTime;
use \RuntimeException;

/**
  Tests for the REST API.
*/
class PostTestDataCommand extends Command {

    const BOOKS_URL = 'http://localhost:8000/api/v1/books';

    protected function configure() {
        $this->setName('app:post-data')
             ->setDescription('Post test data to REST API');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $headers = array('Content-Type' => 'application/json');

        $books = array(
          "978-1-56619-909-4" => new Book("978-1-56619-909-4", "I Was Told There'd Be Cake", new DateTime(), ["Fiction"]),
          "1-86092-022-5" => new Book("1-86092-022-5", "A Boy at Seven", new DateTime(), ["Science Fiction", "Fiction"]),
          "1-86092-010-1" => new Book("1-86092-010-1", "The Higgler", new DateTime(), ["Biography", "Bestseller"])
        );

        foreach ($books as $book) {
            $isbn = $book->getIsbn();
            echo "POST book: {$isbn}\n";
            $data = array(
                "isbn" => $isbn,
                "title" => $book->getTitle(),
                "addedOn" => $book->getAddedOn(),
                "labels" =>$book->getLabels()
            );
            $body = Body::json($data);
            $url = PostTestDataCommand::BOOKS_URL . "/" . $isbn;
            $response = Request::post($url, $headers, $body);
            if (\Symfony\Component\HttpFoundation\Response::HTTP_CREATED != $response->code) {
                throw new RuntimeException("POST request failed (HTTP {$response->code}): {$url}");
            }
        }
    }
}

?>
