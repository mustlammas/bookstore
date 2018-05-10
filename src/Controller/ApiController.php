<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    public function list(LoggerInterface $logger)
    {
         $logger->info('We are logging!');
         return new Response('<html><body>Hello, world!</body></html>');
    }
}

?>