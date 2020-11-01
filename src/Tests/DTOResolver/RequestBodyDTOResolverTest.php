<?php

namespace AJ\RestBundle\Tests\DTOResolver;

use AJ\RestBundle\DTOResolver\RequestBodyDTOResolver;
use AJ\RestBundle\Tests\DTOResolver\Mock\RequestBodyDTOMock;
use AJ\RestBundle\Tests\DTOResolver\Mock\RequestBodyDTOWithoutInterfaceMock;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class RequestBodyDTOResolverTest extends TestCase
{
    public function testSupports(): void
    {
        $argument = new ArgumentMetadata(
            'dto',
            RequestBodyDTOMock::class,
            false,
            false,
            null
        );

        $serializerMock = $this->createMock(SerializerInterface::class);
        $resolver = new RequestBodyDTOResolver($serializerMock);

        $this->assertTrue($resolver->supports($argument));
    }

    public function testNotSupportsWithUsingCorrectInterface(): void
    {
        $argument = new ArgumentMetadata(
            'dto',
            RequestBodyDTOWithoutInterfaceMock::class,
            false,
            false,
            null
        );

        $serializerMock = self::createMock(SerializerInterface::class);
        $resolver = new RequestBodyDTOResolver($serializerMock);

        $this->assertFalse($resolver->supports($argument));
    }

    public function testResolveWithEmptyBody(): void
    {
        $serializerMock = self::createMock(SerializerInterface::class);
        $resolver = new RequestBodyDTOResolver($serializerMock);

        $argument = new ArgumentMetadata(
            'dto',
            RequestBodyDTOMock::class,
            false,
            false,
            null
        );

        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->once())
            ->method('getContent')
            ->willReturn(null)
        ;

        $this
            ->assertEquals(
                new RequestBodyDTOMock(),
                $resolver->resolve($requestMock, $argument)
            );
    }

    public function testResolveWithJsonBody(): void
    {
        $serializerMock = self::createMock(SerializerInterface::class);
        $resolver = new RequestBodyDTOResolver($serializerMock);

        $argument = new ArgumentMetadata(
            'dto',
            RequestBodyDTOMock::class,
            false,
            false,
            null
        );

        $requestMock = $this->createMock(Request::class);

        $requestMock
            ->expects($this->exactly(2))
            ->method('getContent')
            ->willReturn('{}')
        ;

        $serializerMock
            ->expects($this->once())
            ->method('deserialize')
            ->with(
                '{}',
                RequestBodyDTOMock::class,
                'json'
            )
            ->willReturn(new RequestBodyDTOMock())
        ;

        $resolver->resolve($requestMock, $argument);
    }
}
