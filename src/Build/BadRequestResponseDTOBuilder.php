<?php

namespace AJ\RestBundle\Build;

use AJ\RestBundle\DTO\BadRequestResponseDTO;
use AJ\RestBundle\Http\ResponseDTOInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class BadRequestResponseDTOBuilder
{
    public function build(ConstraintViolationListInterface $errors): ResponseDTOInterface
    {
        $messages = [];

        foreach ($errors as $validation) {
            $messages[] = [
                'field' => $validation->getPropertyPath(),
                'message' => $validation->getMessage(),
            ];
        }

        return new BadRequestResponseDTO(
            'bad_request',
            'Bad request received',
            $messages
        );
    }
}
