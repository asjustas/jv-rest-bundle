<?php

namespace AJ\RestBundle\Tests\DTOResolver;

use AJ\RestBundle\DTOResolver\QueryDTOResolver;
use AJ\RestBundle\Tests\DTOResolver\Mock\QueryDTOMock;
use AJ\RestBundle\Tests\DTOResolver\Mock\RequestBodyDTOMock;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class QueryDTOResolverTest extends TestCase
{
    public function testSupports(): void
    {
        $argument = new ArgumentMetadata(
            'dto',
            QueryDTOMock::class,
            false,
            false,
            null
        );

        $serializerMock = $this->createMock(SerializerInterface::class);
        $resolver = new QueryDTOResolver($serializerMock);

        $this->assertTrue($resolver->supports($argument));
    }

    public function testSupportsWithUsingCorrectInterface(): void
    {
        $argument = new ArgumentMetadata(
            'dto',
            RequestBodyDTOMock::class,
            false,
            false,
            null
        );

        $serializerMock = $this->createMock(SerializerInterface::class);
        $resolver = new QueryDTOResolver($serializerMock);

        $this->assertFalse($resolver->supports($argument));
    }

    public function testResolve(): void
    {
        $argument = new ArgumentMetadata(
            'dto',
            RequestBodyDTOMock::class,
            false,
            false,
            null
        );

        $serializerMock = $this->createMock(SerializerInterface::class);
        $resolver = new QueryDTOResolver($serializerMock);
        $request = new Request(['test' => 1]);

        $serializerMock
            ->expects($this->once())
            ->method('deserialize')
            ->with(
        '{"test":1}',
                RequestBodyDTOMock::class,
                'json'
            )
            ->willReturn(new RequestBodyDTOMock());

        $resolver->resolve($request, $argument);
    }
}
