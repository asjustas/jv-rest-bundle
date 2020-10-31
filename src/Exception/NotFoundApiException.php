<?php

namespace AJ\RestBundle\Exception;

use AJ\RestBundle\DTO\ErrorResponseDTO;

class NotFoundApiException extends BaseHttpApiException
{
    public function __construct(string $error, string $message)
    {
        parent::__construct($error, $message, 404);
    }
}
