<?php

namespace AJ\RestBundle\Exception;

use Throwable;

interface ApiExceptionInterface extends Throwable
{
    public function getBody(): object;
    public function getCode();
}
