<?php

namespace AJ\RestBundle\Http;

use AJ\RestBundle\Build\BadRequestResponseDTOBuilder;
use AJ\RestBundle\DTOResolver\DTOResolverInterface;
use AJ\RestBundle\Exception\ApiException;
use Exception;
use Generator;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestDTOResolver implements ArgumentValueResolverInterface
{
    private ValidatorInterface $validator;
    private BadRequestResponseDTOBuilder $badRequestResponseDTOBuilder;

    /**
     * @var DTOResolverInterface[]
     */
    private iterable $dtoResolvers;

    public function __construct(
        ValidatorInterface $validator,
        BadRequestResponseDTOBuilder $badRequestResponseDTOBuilder,
        iterable $dtoResolvers
    ) {
        $this->validator = $validator;
        $this->badRequestResponseDTOBuilder = $badRequestResponseDTOBuilder;
        $this->dtoResolvers = $dtoResolvers;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $reflection = new ReflectionClass($argument->getType());

        return $reflection->implementsInterface(RequestDTOInterface::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        $dto = $this->fillDto($request, $argument);
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            $responseDto = $this->badRequestResponseDTOBuilder->build($errors);

            throw new ApiException($responseDto, 400);
        }

        yield $dto;
    }

    private function fillDto(Request $request, ArgumentMetadata $argument): object
    {
        foreach ($this->dtoResolvers as $dtoResolver) {
            if ($dtoResolver->supports($argument)) {
                return $dtoResolver->resolve($request, $argument);
            }
        }

        throw new Exception('Can not fill DTO (use more specific interface)');
    }
}
