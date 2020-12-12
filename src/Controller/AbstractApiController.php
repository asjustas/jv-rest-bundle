<?php

namespace AJ\RestBundle\Controller;

use AJ\RestBundle\Http\ResponseDTOInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiController extends AbstractController
{
    protected function createResponse(
        ?ResponseDTOInterface $content,
        int $status = Response::HTTP_OK
    ): Response {
        return new JsonResponse(
            $this->get('jms_serializer')->serialize($content, 'json'),
            $status,
            [],
            true
        );
    }

    public static function getSubscribedServices(): array
    {
        $subscribedServices = [
            'jms_serializer' => '?'.SerializerInterface::class,
        ];

        return array_merge($subscribedServices, parent::getSubscribedServices());
    }
}
