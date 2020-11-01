<?php

namespace AJ\RestBundle\DTOResolver;

use AJ\RestBundle\Http\RequestDTOInterface;
use JMS\Serializer\SerializerInterface;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class RequestBodyDTOResolver implements DTOResolverInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function supports(ArgumentMetadata $argument): bool
    {
        $reflection = new ReflectionClass($argument->getType());

        return $reflection->implementsInterface(RequestDTOInterface::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): object
    {
        $dtoClass = $argument->getType();

        if (null === $request->getContent()) {
            return new $dtoClass();
        }

        return $this
            ->serializer
            ->deserialize($request->getContent(), $dtoClass, 'json');
    }
}
