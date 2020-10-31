<?php

namespace AJ\RestBundle\DTO;

use AJ\RestBundle\Http\ResponseDTOInterface;

class BadRequestResponseDTO implements ResponseDTOInterface
{
    private string $error;
    private string $message;
    private array $details;

    public function __construct(string $error, string $message, array $details)
    {
        $this->error = $error;
        $this->message = $message;
        $this->details = $details;
    }
}
