<?php

namespace App\Service;

use App\Dto\Book;

class BookService {

    private $books = [];

    public function getAll() {
        return array_values($this->books);
    }

    public function getBooksByTitle($title) {
         return array_values($this->books);
    }

    public function add(Book $book) {
        $this->books[$book->getIsbn()] = $book;
    }
}

?>