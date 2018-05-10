<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use \Datetime;
use App\Dto\Book;

class ApiController extends Controller
{
    public function list(LoggerInterface $logger)
    {
         $logger->info('We are logging!');

         $book = new Book("978-1-56619-909-4 ", "I Was Told There'd Be Cake", new DateTime());

         return new Response('<html><body>Hello, world!</body></html>');
    }
}

?>