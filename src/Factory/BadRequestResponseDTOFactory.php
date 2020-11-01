<?php

namespace AJ\RestBundle\Factory;

use AJ\RestBundle\DTO\BadRequestResponseDTO;
use AJ\RestBundle\Http\ResponseDTOInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class BadRequestResponseDTOFactory
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
