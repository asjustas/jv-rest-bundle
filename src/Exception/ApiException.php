<?php

namespace AJ\RestBundle\Exception;

use AJ\RestBundle\Http\ResponseDTOInterface;
use Exception;

class ApiException extends Exception implements ApiExceptionInterface
{
    private ResponseDTOInterface $body;

    public function __construct(ResponseDTOInterface $body, int $code)
    {
        $this->body = $body;
        $this->code = $code;

        parent::__construct(null, $code);
    }

    public function getBody(): object
    {
        return $this->body;
    }
}
