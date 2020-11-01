<?php

namespace AJ\RestBundle\Http;

use AJ\RestBundle\DTOResolver\DTOResolverInterface;
use AJ\RestBundle\Exception\ApiException;
use AJ\RestBundle\Factory\BadRequestResponseDTOFactory;
use Exception;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestDTOResolver implements ArgumentValueResolverInterface
{
    private ValidatorInterface $validator;
    private BadRequestResponseDTOFactory $badRequestDTOFactory;

    /**
     * @var DTOResolverInterface[]
     */
    private iterable $dtoResolvers;

    public function __construct(
        ValidatorInterface $validator,
        BadRequestResponseDTOFactory $badRequestDTOFactory,
        iterable $dtoResolvers
    ) {
        $this->validator = $validator;
        $this->badRequestDTOFactory = $badRequestDTOFactory;
        $this->dtoResolvers = $dtoResolvers;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        $reflection = new ReflectionClass($argument->getType());

        return $reflection->implementsInterface(RequestDTOInterface::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $dto = $this->fillDto($request, $argument);
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            $responseDto = $this->badRequestDTOFactory->build($errors);

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
