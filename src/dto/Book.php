<?php

namespace App

class Book
{
    private $isbn;
    private $title;
    private $addedOn;

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