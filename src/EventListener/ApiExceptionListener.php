<?php

namespace AJ\RestBundle\EventListener;

use AJ\RestBundle\Exception\ApiExceptionInterface;
use AJ\RestBundle\Http\ResponseDTOInterface;
use Exception;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ApiExceptionListener
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!$exception instanceof ApiExceptionInterface) {
            return;
        }

        if (!$exception->getBody() instanceof ResponseDTOInterface) {
            throw new Exception('Body does not implement ResponseDTOInterface');
        }

        $response = $this->buildResponse($exception);

        $event->setResponse($response);
    }

    private function buildResponse(ApiExceptionInterface $exception): Response
    {
        $serializedResponse = $this
            ->serializer
            ->serialize($exception->getBody(), 'json');

        $response = new JsonResponse(
            $serializedResponse,
            $exception->getCode(),
            [],
            true
        );

        $response->setStatusCode($exception->getCode());

        return $response;
    }
}
