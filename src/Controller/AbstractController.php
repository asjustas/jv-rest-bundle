<?php

namespace AJ\RestBundle\Controller;

use AJ\RestBundle\Http\ResponseDTOInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AbstractController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    protected function createResponse(
        ?ResponseDTOInterface $content,
        int $status = Response::HTTP_OK
    ): Response {
        return new JsonResponse(
            $this->serializer->serialize($content, 'json'),
            $status,
            [],
            true
        );
    }
}
