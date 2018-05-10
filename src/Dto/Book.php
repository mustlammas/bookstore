<?php

namespace App\Dto;

class Book
{
    private $isbn;
    private $title;
    private $addedOn;

    public function __construct($isbn, $title, $addedOn)
    {
        $this->isbn = $isbn;
        $this->title = $title;
        $this->addedOn = $addedOn;
    }

    public function getIsbn()
    {
        return $this->isbn;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getAddedOn()
    {
        return $this->addedOn;
    }
}

?>