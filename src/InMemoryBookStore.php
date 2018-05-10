<?php

namespace App;

use App\Dto\Book;

class InMemoryBookStore {
    private $books = [];

    public function add(Book $book)
    {
        $this->books[$book->getIsbn()] = $book;
    }

    public function getAll()
    {
        return $this->books;
    }
}

?>