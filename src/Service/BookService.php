<?php

namespace App\Service;

use App\Dto\Book;
use Psr\Log\LoggerInterface;

interface BookService {
    public function getByIsbn($isbn);
    public function search($title, $isbn);
    public function add(Book $book);
    public function addLabel($isbn, $label);
}

?>
