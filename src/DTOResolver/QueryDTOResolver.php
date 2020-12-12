<?php

namespace AJ\RestBundle\DTOResolver;

use AJ\RestBundle\Http\QueryAwareDTOInterface;
use JMS\Serializer\SerializerInterface;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class QueryDTOResolver implements DTOResolverInterface
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function supports(ArgumentMetadata $argument): bool
    {
        if (in_array($argument->getType(), DTOResolverInterface::SIMPLE_TYPES, true)) {
            return false;
        }

        $reflection = new ReflectionClass($argument->getType());

        return $reflection->implementsInterface(QueryAwareDTOInterface::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): object
    {
        $dtoClass = $argument->getType();
        $json = json_encode($request->query->all(), JSON_THROW_ON_ERROR);

        return $this
            ->serializer
            ->deserialize($json, $dtoClass, 'json');
    }
}
