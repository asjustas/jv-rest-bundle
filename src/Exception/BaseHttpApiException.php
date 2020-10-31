<?php

namespace AJ\RestBundle\Exception;

use AJ\RestBundle\DTO\ErrorResponseDTO;

class BaseHttpApiException extends ApiException
{
    public function __construct(string $error, string $message, int $code)
    {
        parent::__construct(
            new ErrorResponseDTO($error, $message),
            $code
        );
    }
}
