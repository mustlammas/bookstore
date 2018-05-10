<?php

namespace App\Service;

use App\Dto\Book;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;

class JsonSerializer
{
    private $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer(
            array(new DateTimeNormalizer(), new ObjectNormalizer(), new ArrayDenormalizer()),
            array(new JsonEncoder())
        );
    }

    public function serialize($object)
    {
        return $this->serializer->serialize($object, 'json');
    }

    public function deserialize($json, $class)
    {
         return $this->serializer->deserialize($json, $class, 'json');
    }
}

?>