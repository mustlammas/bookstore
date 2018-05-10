<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use \Datetime;
use App\Dto\Book;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ApiController extends Controller
{
    private $serializer;

    public function __construct() {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function list(LoggerInterface $logger)
    {
         $book = new Book("978-1-56619-909-4 ", "I Was Told There'd Be Cake", new DateTime());
         $json = $this->serializer->serialize($book, 'json');
         # $logger->info($json);

         return new Response($json);
    }
}

?>