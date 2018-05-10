<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use \Datetime;
use App\Dto\Book;
use App\InMemoryBookStore;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ApiController extends Controller
{
    private $serializer;
    private $books;

    public function __construct() {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new DateTimeNormalizer(), new ObjectNormalizer());
        $this->serializer = new Serializer($normalizers, $encoders);
        $this->books = new InMemoryBookStore();

        $this->books->add(new Book("978-1-56619-909-4", "I Was Told There'd Be Cake", new DateTime()));
        $this->books->add(new Book("1-86092-022-5", "A Boy at Seven", new DateTime()));
        $this->books->add(new Book("1-86092-010-1", "The Higgler", new DateTime()));
    }

    public function list()
    {
         $json = $this->serializer->serialize($this->books->getAll(), 'json');
         return new Response($json);
    }
}

?>