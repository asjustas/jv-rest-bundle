<?php

namespace AJ\RestBundle\Http;

use AJ\RestBundle\Exception\BadRequestApiException;
use Exception;
use JMS\Serializer\SerializerInterface;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestDTOResolver implements ArgumentValueResolverInterface
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return $this
            ->implementsInterface($argument, RequestDTOInterface::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $dto = $this->fillDto($request, $argument);
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            $messages = [];

            foreach ($errors as $validation) {
                $messages[$validation->getPropertyPath()] = $validation->getMessage();
            }

            throw new BadRequestApiException(
                'bad_request',
                'Bad request received',
                $messages
            );
        }

        yield $dto;
    }

    private function fillDto(Request $request, ArgumentMetadata $argument): object
    {
        if ($this->implementsInterface($argument, RequestBodyAwareDTOInterface::class)) {
            $dtoClass = $argument->getType();

            if (null === $request->getContent()) {
                return new $dtoClass();
            }

            return $this
                ->serializer
                ->deserialize($request->getContent(), $dtoClass, 'json');
        }

        if ($this->implementsInterface($argument, QueryAwareDTOInterface::class)) {
            $dtoClass = $argument->getType();
            $json = json_encode($request->query->all(), JSON_THROW_ON_ERROR);

            return $this
                ->serializer
                ->deserialize($json, $dtoClass, 'json');
        }

        throw new Exception('Can not fill DTO (use more specific interface)');
    }

    private function implementsInterface(
        ArgumentMetadata $argument,
        string $interface
    ): bool {
        $reflection = new ReflectionClass($argument->getType());

        return $reflection->implementsInterface($interface);
    }
}
