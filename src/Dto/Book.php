<?php

namespace App\Dto;

use App\Dto\Label;

class Book {
    private $isbn;
    private $title;
    private $addedOn;
    private $labels;

    public function __construct($isbn, $title, $addedOn, $labels = []) {
        $this->isbn = $isbn;
        $this->title = $title;
        $this->addedOn = $addedOn;
        $this->labels = $labels;
    }

    public function getIsbn() {
        return $this->isbn;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getAddedOn() {
        return $this->addedOn;
    }

    public function getLabels() {
        return $this->labels;
    }
}

?>