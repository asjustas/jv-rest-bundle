<?php

namespace AJ\RestBundle\Tests\Factory;

use AJ\RestBundle\DTO\BadRequestResponseDTO;
use AJ\RestBundle\Factory\BadRequestResponseDTOFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class BadRequestResponseDTOFactoryTest extends TestCase
{
    public function testBuild(): void
    {
        $expectedDto = new BadRequestResponseDTO(
            'bad_request',
            'Bad request received',
            [
                [
                    'field' => 'price',
                    'message' => 'Invalid value',
                ],
            ]
        );

        $errors = new ConstraintViolationList(
            [
                new ConstraintViolation(
                    'Invalid value',
                    null,
                    [],
                    '',
                    'price',
                    5
                )
            ]
        );

        $this
            ->assertEquals(
                $expectedDto,
                (new BadRequestResponseDTOFactory())->build($errors)
            );
    }
}
