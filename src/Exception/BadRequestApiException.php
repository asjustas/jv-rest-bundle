<?php

namespace AJ\RestBundle\Exception;

use AJ\RestBundle\DTO\BadRequestResponseDTO;

class BadRequestApiException extends ApiException
{
    public function __construct(string $error, string $message, array $details)
    {
        parent::__construct(
            new BadRequestResponseDTO($error, $message, $details),
            400
        );
    }
}
