<?php

namespace AJ\RestBundle\DTO;

use AJ\RestBundle\Http\ResponseDTOInterface;

class ErrorResponseDTO implements ResponseDTOInterface
{
    private string $error;
    private string $message;

    public function __construct(string $error, string $message)
    {
        $this->error = $error;
        $this->message = $message;
    }
}
