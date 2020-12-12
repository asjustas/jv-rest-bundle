<?php

namespace AJ\RestBundle\DTOResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

interface DTOResolverInterface
{
    public const SIMPLE_TYPES = ['int', 'string'];

    public function supports(ArgumentMetadata $argument): bool;
    public function resolve(Request $request, ArgumentMetadata $argument): object;
}
